<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function index(Request $request){
        $discountCoupon = DiscountCoupon::latest();
        if(!empty($request->get('keyword'))){
            $discountCoupon = $discountCoupon->where('name', 'like', '%'. $request->get('keyword') .'%');
        }
        $discountCoupon = $discountCoupon->paginate(10);

        return view('admin.coupon.list', compact('discountCoupon'));
    }

    public function create(){
        return view('admin.coupon.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
            'start_date'         => 'required|date_format:Y-m-d H:i:s|after:today',
            'end_date'        => 'required|date_format:Y-m-d H:i:s|after:start_date'
        ]);

        if($validator->passes()){

            // if(!empty($request->start_date)){
            //     $now = Carbon::now();
            //     $start_date = Carbon::createFromDate('Y-m-d H:i:s', $request->start_date);
            //     if($start_date->lt($now) == true){
            //         return response()->json([
            //             'status' => false,
            //             'errors' => ['start_date' => 'Start Date can not be less than current date time']
            //         ]);
            //     }
            // }

            // if(!empty($request->end_date)){
            //     $start_date = Carbon::createFromDate('Y-m-d H:i:s', $request->start_date);
            //     $end_date = Carbon::createFromDate('Y-m-d H:i:s', $request->end_date);
            //     if($end_date->gt($start_date) == false){
            //         return response()->json([
            //             'status' => false,
            //             'errors' => ['end_date' => 'End Date must be greater than Start Date']
            //         ]);
            //     }
            // }

            $coupon = new DiscountCoupon();
            $coupon->code = $request->code;
            $coupon->name = $request->name;
            $coupon->description = $request->description;
            $coupon->max_uses = $request->max_uses;
            $coupon->max_uses_users = $request->max_uses_users;
            $coupon->type = $request->type;
            $coupon->discount_amount = $request->discount_amount;
            $coupon->min_amount = $request->min_amount;
            $coupon->status = $request->status;
            $coupon->starts_at = $request->start_date;
            $coupon->expires_at = $request->end_date;
            $coupon->save();

            $message = 'Discount Coupon added Successfully';
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

    public function edit($couponId, Request $request){
        $coupon = DiscountCoupon::find($couponId);
        if(empty($coupon)){
            return redirect()->route('coupons.index');
        }
        return view('admin.coupon.edit', compact('coupon'));
    }

    public function update($couponId, Request $request){
        $coupon = DiscountCoupon::find($couponId);
        if(empty($coupon)){
            session()->flash('success', 'Discount Coupon Not Found');
            return response()->json([
                'success' => false,
                'notFound' => true,
                'message' => 'Discount Coupon Not Found',
            ]);
        }
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date_format:Y-m-d H:i:s|after:start_date'
        ]);

        if($validator->passes()){
            $coupon->code = $request->code;
            $coupon->name = $request->name;
            $coupon->description = $request->description;
            $coupon->max_uses = $request->max_uses;
            $coupon->max_uses_users = $request->max_uses_users;
            $coupon->type = $request->type;
            $coupon->discount_amount = $request->discount_amount;
            $coupon->min_amount = $request->min_amount;
            $coupon->status = $request->status;
            $coupon->starts_at = $request->start_date;
            $coupon->expires_at = $request->end_date;
            $coupon->save();

            $message = 'Discount Coupon Updated Successfully';
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

    public function delete($id){
        $coupon = DiscountCoupon::find($id);
        if (empty($coupon)) {
            session()->flash('error', 'Discount Coupon Not Found');
            return response([
                'status' => false,
                'notFound' => true,
            ]);
        }
        $coupon->delete();
        session()->flash('success', 'Discount Coupon Deleted Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Discount Coupon Deleted Successfully'
            ]);
    }
}
