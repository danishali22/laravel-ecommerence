<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::latest('id');
        if(!empty($request->get('keyword'))){
            $brands = $brands->where('name', 'like', '%'.$request->get('keyword').'%');     
        }

        $brands = $brands->paginate(10);
        return view('admin.brands.list', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $brands = new Brand();
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();

            session()->flash('success', 'Brand Created Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand Created Sucessfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }
    }

    public function edit($id)
    {
        $brands = Brand::find($id);
        if (empty($brands)) {
            return redirect()->route('brands.index');
        }
        return view('admin.brands.edit', compact('brands'));
    }

    public function update($id, Request $request)
    {
        $brands = Brand::find($id);
        if (empty($brands)) {
            session()->flash('error', 'Brand record not Found');
            return response()->json([
                'success' => false,
                'notFound' => true,
                'message' => 'Brand record not Found',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $brands->id . ',id',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();

            session()->flash('success', 'Brand Updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand Updated Sucessfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }
    }

    public function destroy($id, Request $request){
        $brand = Brand::find($id);
        if(empty($brand)){
            session()->flash('error', 'Brand record not Found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Brand record not Found'
            ]);

        }
            $brand->delete();

            session()->flash('success', 'Brand Deleted Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand Deleted Successfully'
            ]);

    }
}
