<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentDetail;
use App\Models\Product;
use App\Models\ShippingCharge;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Stripe;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $product = Product::with('product_images')->find($request->id);

        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product record not Found',
            ]);
        }
        if (Cart::count() > 0) {
            $cardContent = Cart::content();
            $cartAlreadyExist = false;

            foreach ($cardContent as $item) {
                if ($item->id == $product->id) {
                    $cartAlreadyExist = true;
                }
            }

            if ($cartAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['ProductImage' => (!empty($product->product_images) ? $product->product_images->first() : '')]);
                $status = true;
                $message = '<strong>' . $product->title . '</strong> added sucessfully in your Cart';
                session()->flash('success', $message);
            } else {
                $status = false;
                $message = $product->title . ' already added in your Cart';
                session()->flash('error', $message);
            }
        } else {
            Cart::add($product->id, $product->title, 1, $product->price, ['ProductImage' => (!empty($product->product_images) ? $product->product_images->first() : '')]);
            $status = true;
            $message = '<strong>' . $product->title . '</strong> added sucessfully in Cart';
            session()->flash('success', $message);
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }

    public function cart()
    {
        $cartContent = Cart::content();
        $data['cartContent'] = $cartContent;
        return view('front.cart', $data);
    }

    public function updateCart(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;

        // Check stock for cart
        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);

        if ($product->track_qty == 'Yes') {
            if ($product->qty  >= $request->qty) {
                Cart::update($rowId, $qty);
                $message = "Cart Updated Successfully";
                $status = true;
                session()->flash('success', $message);
            } else {
                $message = 'Requested qty(' . $request->qty . ') not available in stock';
                $status = false;
                session()->flash('error', $message);
            }
        } else {
            Cart::update($rowId, $qty);
            $message = "Cart Updated Successfully";
            $status = true;
            session()->flash('success', $message);
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }

    public function deleteCartItem(Request $request)
    {
        $itemInfo = Cart::get($request->rowId);
        if ($itemInfo == null) {
            $message = "Item record not found in Cart";
            $status = false;
            session()->flash('error', $message);
            return response()->json([
                'status' => $status,
                'message' => $message,
            ]);
        }

        Cart::remove($request->rowId);
        $message = "Item record deleted successfully from Cart";
        $status = true;
        session()->flash('success', $message);
        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }

    public function checkout()
    {
        $discount = 0;

        // Redirect to Cart if Cart is empty
        if (Cart::count() == 0) {
            return redirect()->route('front.cart');
        }

        // Redirect to Login if User is not login
        if (Auth::check() == false) {

            if (!session()->has('url.temp')) {
                session(['url.temp' => url()->current()]);
            }

            return redirect()->route('account.login');
        }

        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();

        $countries = Country::orderBy('name', 'ASC')->get();

        $subTotal = Cart::subtotal(2, '.', '');
        // Apply Discount logic
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
        }

        // Handle Shipping here
        if ($customerAddress != '') {

            $countryId = $customerAddress->country_id;
            $shippingInfo = ShippingCharge::where('country_id', $countryId)->first();

            $totalQty = 0;
            $totalShipping = 0;
            $grandTotal = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            $totalShipping = $totalQty * $shippingInfo->amount;
            $grandTotal = ($subTotal - $discount) + $totalShipping;
        } else {
            $totalShipping = 0;
            $grandTotal = ($subTotal - $discount) + $totalShipping;
        }

        return view('front.checkout', [
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShipping' => $totalShipping,
            'discount' => $discount,
            'grandTotal' => $grandTotal,
        ]);
    }

    public function processCheckout(Request $request)
    {
        // Step - 1 : Apply Validation  
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Please fix these errors',
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        // Step - 2 : Save User Addresses Table

        $user = Auth::user();
        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
            ]
        );

        // Store data in Orders tale
        $shipping = 0;
        $discount = 0;
        $coupon_code_id = NULL;
        $coupon_code = "";
        $subTotal = Cart::subtotal(2, '.', '');
        $grandTotal = 0;
        $shippingInfo = ShippingCharge::where('country_id', $request->country)->first();

        $totalQty = 0;
        $grandTotal = 0;
        foreach (Cart::content() as $item) {
            $totalQty += $item->qty;
        }

        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
            $coupon_code_id = $code->id;
            $coupon_code = $code->code;
        }

        if ($shippingInfo != null) {
            $shipping = $totalQty * $shippingInfo->amount;
            $grandTotal = ($subTotal - $discount) + $shipping;
        } else {
            $shippingInfo = ShippingCharge::where('country_id', 'rest_of_world')->first();
            $shipping = $totalQty * $shippingInfo->amount;
            $grandTotal = ($subTotal - $discount) + $shipping;
        }

        $order = new Order();
        $order->sub_total = $subTotal;
        $order->shipping = $shipping;
        $order->grand_total = $grandTotal;
        $order->discount = $discount;
        $order->coupon_code_id = $coupon_code_id;
        $order->coupon_code = $coupon_code;
        $order->payment_status = 'not paid';
        $order->status = 'pending';
        $order->user_id = $user->id;
        $order->first_name = $request->first_name;
        $order->last_name = $request->last_name;
        $order->email = $request->email;
        $order->mobile = $request->mobile;
        $order->country_id = $request->country;
        $order->address = $request->address;
        $order->appartment = $request->apartment;
        $order->city = $request->city;
        $order->state = $request->state;
        $order->zip = $request->zip;
        $order->notes = $request->notes;
        $order->save();

        // Store order items in order item table
        foreach (Cart::content() as $item) {
            $orderItem = new OrderItem;
            $orderItem->product_id = $item->id;
            $orderItem->order_id = $order->id;
            $orderItem->name = $item->name;
            $orderItem->qty = $item->qty;
            $orderItem->price = $item->price;
            $orderItem->total = $item->qty * $item->price;
            $orderItem->save();
        }

        // Save Payment Details
        if ($request->payment_method == "stripe") {
            // Handle Stripe payment
            try {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                $totalAmount = $request->grandTotal * 100; // Total amount in cents
                $numRecipients = $request->no_of_people; // Number of recipients

                $currency = "pkr";

                $amountPaidFor = $request->no_of_people_paid_for; // Number of people paid for by one individual

                if ($numRecipients != 0) {
                    $individualAmount = round($totalAmount / $numRecipients); // Amount for each individual
                
                    // Ensure $individualAmount is not 0 to avoid division by zero error
                    if ($individualAmount != 0) {
                        $paidAmount = round($amountPaidFor * $individualAmount); // Total paid amount by one individual
                    } else {
                        $paidAmount = 0;
                    }
                } else {
                    $individualAmount = 0;
                    $paidAmount = 0;
                }

                $user_desc = "Payment for order #" . $order->id . " Total Receipts ". $numRecipients ." bill paid for " . $amountPaidFor . (($amountPaidFor > 1) ? ' Receipts' : ' Receipt') . " Total Amount: " . $totalAmount. " and Paid Amount: ". $paidAmount;
                // Create a PaymentIntent for the amount paid by one individual
                \Stripe\Charge::create([
                    "amount" => $paidAmount,
                    "currency" => $currency,
                    "source" => $request->stripeToken,
                    "description" => $user_desc,

                ]);

                // Calculate the remaining amount to be paid by other individuals
                if ($paidAmount != 0) {
                $remainingAmount = round($totalAmount - $paidAmount);
                }

                // Create a PaymentIntent for each remaining recipient, if any
                $remainingRecipients = $numRecipients - $amountPaidFor;
                if ($remainingRecipients > 0) {
                    $individualRemainingAmount = round($remainingAmount / $remainingRecipients);

                    $user_desc = "Payment for order #" . $order->id . " Total Receipts ". $numRecipients ." bill paid for " . $amountPaidFor . (($amountPaidFor > 1) ? ' Receipts' : ' Receipt') . " Total Amount: " . $totalAmount. " and Paid Amount: ". $paidAmount;

                    $other_user_desc = "Payment for order #" . $order->id. " Total Receipts ". $numRecipients ." Remaining " . (($remainingRecipients > 1) ? ' Receipts ' : ' Receipt ') . $remainingRecipients ." Total Amount: " . $totalAmount. " and Paid Amount: ". $individualRemainingAmount;

                    for ($i = 0; $i < $remainingRecipients; $i++) {
                        \Stripe\PaymentIntent::create([
                            "amount" => $individualRemainingAmount,
                            "currency" => $currency,
                            'payment_method_types' => ['card'],
                            "description" => $other_user_desc,
                        ]);
                    }
                }


                $stripe = new PaymentDetail();
                $stripe->user_id = Auth::user()->id;
                $stripe->order_id = $order->id;
                $stripe->stripe_token = $request->stripeToken;
                $stripe->name_on_card = $request->name_on_card;
                $stripe->card_number = $request->card_number;
                $stripe->cvc = $request->cvc;
                $stripe->exp_month = $request->exp_month;
                $stripe->exp_year = $request->exp_year;
                $stripe->currency = $currency;
                $stripe->total_receipts = $numRecipients;
                $stripe->total_receipts_paid_for = $amountPaidFor;
                $stripe->remaing_receipts = $numRecipients - $amountPaidFor;
                $stripe->total_amount = $totalAmount;
                $stripe->paid_amount = $paidAmount;
                $stripe->remaining_amount = $totalAmount - $paidAmount;
                $stripe->user_desc = $user_desc;
                $stripe->other_user_desc = $other_user_desc;
                $stripe->save();

                // Additional logic for successful Stripe payment
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage()]);
            }
        }

        // Update Stock
        $product = Product::find($item->id);
        if ($product->track_qty == "Yes") {
            $currentStock = $product->qty;
            $updatedStock = $currentStock - $item->qty;
            $product->qty = $updatedStock;
            $product->save();
        }

        // Send Order Email
        orderEmail($order->id, "customer");

        session()->flash('success', 'You have successfully placed your Order');

        Cart::destroy();

        session()->forget('code');

        return response()->json([
            'message' => 'Order placed Sucessfully',
            'orderId' => $order->id,
            'status' => true,
        ]);
    }

    public function thanks($id)
    {
        return view('front.thanks', [
            'id' => $id,
        ]);
    }

    public function getOrderSummary(Request $request)
    {
        $country = $request->country;
        $subTotal = Cart::subtotal(2, '.', '');
        $discount = 0;
        $discountString = "";


        // Apply Discount logic
        if (session()->has('code')) {
            $code = session()->get('code');
            if ($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
            $discountString =
                '<div class="input-group mt-4" id="removeDiscountDiv">
                <strong>' . session()->get('code')->code . '</strong>
                <a href="#" class="btn btn-danger btn-sm" id="removeDiscount"><i class="fa fa-times"></i></a>
            </div>';
        }

        if ($country > 0) {

            $shippingInfo = ShippingCharge::where('country_id', $country)->first();

            $totalQty = 0;
            $grand_total = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if ($shippingInfo != null) {
                $totalShipping = $totalQty * $shippingInfo->amount;
                $grand_total = ($subTotal - $discount) + $totalShipping;

                return response()->json([
                    'status' => true,
                    'grand_total' => round($grand_total, 2),
                    'discount' => round($discount, 2),
                    'discountString' => $discountString,
                    'totalShipping' => round($totalShipping, 2),
                ]);
            } else {
                $shippingInfo = ShippingCharge::where('country_id', 'rest_of_world')->first();
                $totalShipping = $totalQty * $shippingInfo->amount;
                $grand_total = ($subTotal - $discount) + $totalShipping;

                return response()->json([
                    'status' => true,
                    'grand_total' => round($grand_total, 2),
                    'discount' => round($discount, 2),
                    'discountString' => $discountString,
                    'totalShipping' => round($totalShipping, 2),
                ]);
            }
        } else {
            return response()->json([
                'status' => true,
                'discount' => round($discount, 2),
                'discountString' => $discountString,
                'grand_total' => round(($subTotal - $discount), 2),
                'totalShipping' => round(0, 2),
            ]);
        }
    }

    public function discountCoupon(Request $request)
    {

        // Invalid Coupon Condition
        $code = DiscountCoupon::where('code', $request->code)->first();
        if ($code == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Discount Coupon',
            ]);
        }

        // Start Date Condition
        $now = Carbon::now();
        if (!empty($code->starts_at)) {
            $start_date = Carbon::createFromFormat('Y-m-d H:i:s', $code->starts_at);
            if ($now->lt($start_date)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Discount Coupon yet to Start. Discount Coupon will start at ' . $start_date,
                ]);
            }
        }

        //Expiry Date Condition
        if (!empty($code->expires_at)) {
            $end_date = Carbon::createFromFormat('Y-m-d H:i:s', $code->expires_at);
            if ($now->gt($end_date)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Discount Coupon already expires at ' . $end_date,
                ]);
            }
        }

        // Max Uses Discount Conditin
        if ($code->max_uses > 0) {
            $couponUsed = Order::where('coupon_code_id', $code->id)->count();
            if ($couponUsed >= $code->max_uses) {
                return response()->json([
                    'status' => false,
                    'message' => 'Maximum number of coupons reached',
                ]);
            }
        }

        // Max number of users Discount condition
        if ($code->max_uses_users > 0) {
            $couponUsers = Order::where(['coupon_code_id' => $code->id, 'user_id' => Auth::user()->id])->count();
            if ($couponUsers >= $code->max_uses_users) {
                return response()->json([
                    'status' => false,
                    'message' => 'You already used this coupon',
                ]);
            }
        }

        // Minimum amount for coupon condition
        $subTotal = Cart::subtotal(2, '.', '');
        if ($code->min_amount > 0) {
            if ($subTotal < $code->min_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your minimum amount must be ' . $code->min_amount,
                ]);
            }
        }

        session()->put('code', $code);
        return $this->getOrderSummary($request);
    }

    public function removeDiscount(Request $request)
    {
        session()->forget('code');
        return $this->getOrderSummary($request);
    }
}
