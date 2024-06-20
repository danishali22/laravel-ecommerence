<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Shipping;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{

    public function create(Request $request)
    {
        $shippingCharges = ShippingCharge::select('shipping_charges.*', 'countries.name as country')->leftJoin('countries', 'countries.id', 'shipping_charges.country_id')->latest('shipping_charges.id');
        if (!empty($request->get('keyword'))) {
            $shippingCharges = $shippingCharges->where('name', 'like', '%' . $request->get('keyword') . '%');
            $shippingCharges = $shippingCharges->orwhere('countries.name', 'like', '%' . $request->get('keyword') . '%');
        }

        $shippingCharges = $shippingCharges->paginate(10);

        // return view('admin.shipping.list', compact('shippingCharges'));
        $countries = Country::get();
        $data['countries'] = $countries;
        $data['shippingCharges'] = $shippingCharges;
        return view('admin.shipping.create', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric',
        ]);

        if ($validator->passes()) {
            $count = ShippingCharge::where('country_id', $request->country)->count();
            $countryName = Country::where('id', $request->country)->first();
            if($count > 0){
                if($request->country == 'rest_of_world'){
                    session()->flash('error', 'Shipping Charges already added for Rest of the world');
                }
                else{
                    session()->flash('error', 'Shipping Charges already added for '. $countryName->name);
                }
                return response()->json([
                    'status' => true,
                ]);
            }

            $shipping = new ShippingCharge();
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            if($request->country == 'rest_of_world'){
                session()->flash('success', 'Shipping Charges for Rest of the world added Successfully');
                return response()->json([
                    'status' => true,
                    'message' => 'Shipping Charges for Rest of the world added Successfully',
                ]);
            }
            else{
                session()->flash('success', 'Shipping Charges for '. $countryName->name .' added Successfully');
                return response()->json([
                    'status' => true,
                    'message' => 'Shipping Charges for '. $countryName->name .' added Successfully',
                ]);
            }

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($id, Request $request)
    {
        $shippingCharges = ShippingCharge::find($id);
        if (empty($shippingCharges)) {
            session()->flash('error', 'Record Not Found');
            return redirect()->route('shipping.create');
        }

        $countries = Country::orderBy('name', 'ASC')->get();
        $data['shipping'] = $shippingCharges;
        $data['countries'] = $countries;
        return view('admin.shipping.edit', $data);
    }

    public function update($id, Request $request)
    {
        $shippingCharges = ShippingCharge::find($id);
        if (empty($shippingCharges)) {
            session()->flash('error', 'Record Not Found');
            return response([
                'status' => false,
                'notFound' => true,
            ]);
        }

        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric',
        ]);

        if ($validator->passes()) {
            $shippingCharges->country_id = $request->country;
            $shippingCharges->amount = $request->amount;
            $shippingCharges->save();

            $countryName = Country::where('id', $request->country)->first();

            if($request->country == 'rest_of_world'){
                session()->flash('success', 'Shipping Charges for Rest of the world updated Successfully');
                return response()->json([
                    'status' => true,
                    'message' => 'Shipping Charges for Rest of the world updated Successfully',
                ]);
            }
            else{
                session()->flash('success', 'Shipping Charges for '. $countryName->name .' updated Successfully');
                return response()->json([
                    'status' => true,
                    'message' => 'Shipping Charges for '. $countryName->name .' updated Successfully',
                ]);
            }

        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }
    }

    public function delete($id)
    {
        $shippingCharges = ShippingCharge::find($id);
        if (empty($shippingCharges)) {
            session()->flash('error', 'Shipping Charges Not Found');
            return response([
                'status' => false,
                'notFound' => true,
            ]);
        }
        $shippingCharges->delete();

        $countryId = $shippingCharges->country_id;

        $countryName = Country::where('id', $countryId)->first();

        if(!$countryName){
            session()->flash('success', 'Shipping Charges for Rest of the world deleted Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Shipping Charges for Rest of the world deleted Successfully',
            ]);
        }
        else{
            session()->flash('success', 'Shipping Charges for '. $countryName->name .' deleted Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Shipping Charges for '. $countryName->name .' deleted Successfully',
            ]);
        }
    }
}
