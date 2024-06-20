<?php

namespace App\Http\Controllers;

use App\Mail\ContactEmail;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_featured', 'Yes')->orderBy('id', 'DESC')->where('status', 1)->take(8)->get();
        $data['featuredProducts'] = $featuredProducts;

        $latestProducts = Product::orderBy('id', 'DESC')->where('status', 1)->take(8)->get();
        $data['latestProducts'] = $latestProducts;
        return view('front.home', $data);
    }

    public function addToWishlist(Request $request)
    {
        if (Auth::check() == false) {

            session(['url.temp' => url()->previous()]);

            return response()->json([
                'status' => false,
            ]);
        } else {

            // Retrieve the product information
            $product = Product::find($request->id);

            // Check if the wishlist item exists
            $wishlistItem = Wishlist::where([
                'user_id' => Auth::user()->id,
                'product_id' => $request->id,
            ])->first();

            // If the wishlist item exists, return a JSON response indicating that the product is already in the wishlist
                if($wishlistItem){
                return response()->json([
                    'status' => true,
                    'title' => 'Oops!!!',
                    'message' => '<div class="alert alert-danger"><strong>' . $product->title . '</strong> already added in your Wishlist <br/> <a href="' . route('account.wishlist') . '"> Your Wishlists </a> </div>',
                ]);
            }

            // Attempt to update or create a wishlist item
            Wishlist::updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'product_id' => $request->id,
                ],
                [
                    'user_id' => Auth::user()->id,
                    'product_id' => $request->id,
                ]
            );

            // If the wishlist item does not exist, return a JSON response indicating that the product has been successfully added to the wishlist
            return response()->json([
                'status' => true,
                'title' => 'Success',
                'message' => '<div class="alert alert-success"><strong>' . $product->title . '</strong> added in your Wishlist <br/> <a href="' . route('account.wishlist') . '"> Your Wishllists </a> </div>',
            ]);
        }
    }

    public function pages($slug){
        $page = Page::where('slug',$slug)->first();

        if($page == null){
            abort(404);
        }

        return view('front.page', [
            'page' => $page
        ]);
    }

    public function sendContactEmail(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required',
        ]);

        
        if($validator->passes()){    
            $mailData = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'mail_subject' => 'You have received a contact email'
            ];
            $user = User::where('id', 1)->first();
            
            Mail::to($user->email)->send(new ContactEmail($mailData));
            
            $message = 'Thanks for contacting us, we will get back to you soon';

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
}
