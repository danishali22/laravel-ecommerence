<?php

use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\Page;
use App\Models\ProductImages;
use Illuminate\Support\Facades\Mail;

function getCategories(){
    return Category::orderBy('id', 'DESC')->with('sub_categories')->where('showHome', 'Yes')->where('status', 1)->take(8)->get();
}

function getProductImage($productId){
    return ProductImages::where('product_id', $productId)->first();
}

function orderEmail($orderId, $userType="customer"){
    $order = Order::where('id', $orderId)->with('items')->first();

    if($userType == "customer"){
        $subject = "Thanks for your Order";
        $email = $order->email;
    }
    else{
        $subject = "You have received an Order";
        $email = env('ADMIN_EMAIL');
    }

    $mailData = [
        'subject' => $subject,
        'order' => $order,
        'userType' => $userType
    ];

    Mail::to($email)->send(new OrderEmail($mailData));
    //dd($order);
}

function getCountryName($id){
    return Country::where('id', $id)->first();
}

function staticPages(){
    return Page::orderBy('name', 'ASC')->get();
}
?>