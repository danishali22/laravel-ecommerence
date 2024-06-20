<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Chat;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wishlist;
use App\Events\MessageEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(){
        return view('front.account.login');
    }

    public function authenticate(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->passes()){
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))){
                if(session()->has('url.temp')){
                    return redirect(session()->get('url.temp'));
                }
                // if (session()->has('url.temp')) {
                // $tempUrl = session()->get('url.temp');
                // session()->forget('url.temp'); // Clear the temp URL from session
                // return redirect($tempUrl);
                // }
                return redirect()->route('account.profile')->with('success', 'You are logged in successfully');
            }
            else{
                //session()->flash('error', 'Either Email or Password is Incorrect');  replace with ->with('error', 'Either Email or Password is Incorrect');
                return redirect()->route('account.login')->withInput($request->only('email'))->with('error', 'Either Email or Password is Incorrect');
            }
        }
        else{
            return redirect()->route('account.login')->withErrors($validator)->withInput($request->only('email'));
        }

    }

    public function register(){
        return view('front.account.register');
    }

    public function registerProcess(Request $request){
        $validator  = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed',
        ]);

        if($validator->passes()){
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            $message = 'You have been registered Successfully';
            session()->flash('success', $message);
            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function profile(){
        $user = User::find(Auth::user()->id)->first();
        $data['user'] = $user;
        
        $address = CustomerAddress::where('user_id', Auth::user()->id)->first();
        $data['address'] = $address;
        
        $countries = Country::orderBy('name', 'ASC')->get();
        $data['countries'] = $countries;
        return view('front.account.profile', $data);
    }

    public function udateProfile(Request $request){
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$userId.',id',
            'phone' => 'required',
        ]);

        if($validator->passes()){
            $user = User::find(Auth::user()->id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            $message = "User Record Update Successfully";

            session()->flash("success", $message);

            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }
        else{

            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

    }
    public function updateAddress(Request $request){
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'country' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if($validator->passes()){
        CustomerAddress::updateOrCreate(
            ['user_id' => $userId],
            [
                'user_id' => $userId,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,
                'address' => $request->address,
                'apartment' => $request->appartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
            ]
        );

            $message = "User Record Update Successfully";

            session()->flash("success", $message);

            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }
        else{

            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login')->with('success', 'You are logged out successfully');
    }

    public function orders(){
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();
        $data['orders'] = $orders;
        return view('front.account.orders', $data);
    }

    public function orderDetails($id){
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->where('id', $id)->first();
        $data['orders'] = $orders;

        $orderItems = OrderItem::where('order_id', $id)->get();
        $data['orderItems'] = $orderItems;

        $orderItemsCount = OrderItem::where('order_id', $id)->count();
        $data['orderItemsCount'] = $orderItemsCount;
        // dd($orderItems);
        return view('front.account.order-details', $data);
    }

    public function wishlist(){
        $wishlist = Wishlist::where('user_id', Auth::user()->id)->with('product')->get();
        $data = [];
        $data['wishlists'] = $wishlist;

        return view('front.account.wishlist', $data);
    }

    public function removeProductFromWishlist(Request $request){
        $wishlist = Wishlist::where('product_id', $request->id)->where('user_id', Auth::user()->id)->first();

        if($wishlist == null){
            $message = "Product already removed from Wishlist";

            session()->flash('error', $message);

            return response()->json([
                'status' => false,
                'message' => $message,
            ]);
        }
        else{
            Wishlist::where(['product_id' =>  $request->id,'user_id' => Auth::user()->id])->delete();

            $message = "Product removed Successfully from Wishlist";

            session()->flash('success', $message);

            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }
    }

    public function showChangePassword(){
        return view('front.account.change-password');
    }

    public function processChangePassword(Request $request){
        $validator = Validator::make($request->all(),[
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);

        if($validator->passes()){
            $user = User::select('id', 'password')->where('id', Auth::user()->id)->first();
            if(Hash::check($request->old_password, $user->password)){
                User::where('id', $user->id)->update([
                    'password' => Hash::make($request->new_password),
                ]);
                
                session()->flash('success', 'Your Password is Changeed Successfully.');
                return response()->json([
                    'status' => true,
                    'message' => 'Your Password is Changeed Successfully.',
                ]);
            }
            else{
                session()->flash('error', 'Your Old Password is incorrect. Please try again');
                return response()->json([
                    'status' => true,
                    'message' => 'Your Old Password is incorrect. Please try again',
                ]);
            }
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
        
    }

    public function forgotPassword(){
        return view('front.account.forgot-password');
    }

    public function processforgotPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if($validator->fails()){
            return redirect()->route('front.forgotPassword')->withInput()->withErrors($validator);
        }
        else{
            $token = Str::random(60);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now(),
            ]);

            //Send Reset Password Mail

            $user = User::where('email', $request->email)->first();
            
            $formData = [
                'token' => $token,
                'user' => $user,
                'mail_subject' => 'You have requested to change password',
            ];

            Mail::to($request->email)->send(new ResetPasswordMail($formData));

            return redirect()->route('front.forgotPassword')->with('success', 'Please check you email to reset your password');
        }
    }

    function resetPassword($token){

        $tokenExists = DB::table('password_reset_tokens')->where('token', $token)->first();

        if($tokenExists == null){
            return redirect()->route('front.forgotPassword')->with('error', 'Invalid Request');
        }
        return view('front.account.reset-password', ['token' => $token]);
    }

    function processresetPassword(Request $request){

        $token = $request->token;

        $tokenObj = DB::table('password_reset_tokens')->where('token', $token)->first();

        if($tokenObj == null){
            return redirect()->route('front.forgotPassword')->with('error', 'Invalid Request');
        }

        $user = User::where('email', $tokenObj->email)->first();

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);

        if($validator->fails()){
            return redirect()->route('front.resetPassword', $token)->withErrors($validator);
        }

        User::where('id', $user->id)->update([
            'password' => Hash::make($request->new_password),
        ]);

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        return redirect()->route('account.login')->with('success', 'You have successfully updated your password');
    }

    // Load Dashboard where all users shown for chat
    public function loadDashboard(){
        $users = User::whereNotIn('id', [Auth::user()->id])->get();
        return view('front.account.dashboard', compact('users'));
    }

    public function saveChat(Request $request){
        try {
            $chat = Chat::create([
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
            ]);

            // event(new MessageEvent($chat));
            MessageEvent::dispatch($chat);

            return response()->json([
                'status' => true,
                'data' => $chat,
                'message' => 'Chat saved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
