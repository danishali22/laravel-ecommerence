<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\TempImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function index(){
        //$admin = Auth::guard('admin')->user();
        //echo 'Welcome ' . $admin->name . ' <a href="'.route('admin.logout').'">Logout</a>';

        $totalOrders = Order::where('status', '!=', 'cancelled')->count();
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('grand_total');

        // This Month
        $startOfMonth =  Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentDate =  Carbon::now()->format('Y-m-d');
        $totalRevenueThisMonth = Order::where('status', '!=', 'cancelled')->whereDate('created_at', '>=', $startOfMonth)->whereDate('created_at', '<=', $currentDate)->sum('grand_total');

        // Last Month
        $startOfLastMonth =  Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $endOfLastMonth =  Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        $totalRevenueLastMonth = Order::where('status', '!=', 'cancelled')->whereDate('created_at', '>=', $startOfLastMonth)->whereDate('created_at', '<=', $endOfLastMonth)->sum('grand_total');

        // Last 30 Days
        $last30days =  Carbon::now()->subDays(30)->format('Y-m-d');
        $currentDate =  Carbon::now()->format('Y-m-d');
        $totalRevenueLast30days = Order::where('status', '!=', 'cancelled')->whereDate('created_at', '>=', $last30days)->whereDate('created_at', '<=', $endOfLastMonth)->sum('grand_total');

        // Delete Temp Images Here
        $dayBeforeToday = Carbon::now()->subDay(1)->format('Y-m-d H:i:s');
        $tempImages = TempImage::where('created_at', '<=', $dayBeforeToday)->get();

        foreach ($tempImages as $tempImage) {
            $path = public_path('/temp/'. $tempImage->name);
            $thumbPath = public_path('/temp/thumb/'. $tempImage->name);

            if(File::exists($path)){
                File::delete($path);
            }
            if(File::exists($thumbPath)){
                File::delete($thumbPath);
            }

            TempImage::where('id', $tempImage->id)->delete();

        }

        
        return view('admin.dashboard',[
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
            'totalRevenueThisMonth' => $totalRevenueThisMonth,
            'totalRevenueLastMonth' => $totalRevenueLastMonth,
            'totalRevenueLast30days' => $totalRevenueLast30days,
        ]);
    }

    public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.logout');
    }
}
