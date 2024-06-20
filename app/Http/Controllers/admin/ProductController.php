<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\ProductRating;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $product = Product::latest('id')->with('product_images');
        if (!empty($request->get('keyword'))) {
            $product = $product->where('title', 'like', '%' . $request->get('keyword') . '%');
        }
        $product = $product->paginate();
        //dd($product);
        $data['products'] = $product;
        return view('admin.product.list', $data);
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view('admin.product.create', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $product = new Product();
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->related_products = (!empty($request->related_products)) ? implode(',', $request->related_products) : '';
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->save();

            if (!empty($request->imageArray)) {
                foreach ($request->imageArray as $temp_image_id) {
                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.', $tempImageInfo->name);
                    $ext = last($extArray);

                    $productImage = new ProductImages();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imgName = $product->id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                    $productImage->image = $imgName;
                    $productImage->save();

                    // Generate Product Thumbnails

                    // Large
                    $manager = new ImageManager(new Driver());
                    $sourcePath = public_path() . '/temp/' . $tempImageInfo->name;
                    $destPath = public_path() . '/uploads/product/large/' . $imgName;
                    $img = $manager->read($sourcePath);
                    $img->resize(1400, 1000);
                    $img->save($destPath);

                    // Small
                    $manager = new ImageManager(new Driver());
                    $destSmallPath = public_path() . '/uploads/product/small/' . $imgName;
                    $img = $manager->read($sourcePath);
                    $img->cover(300, 300);
                    $img->save($destSmallPath);
                }
            }

            session()->flash('success', 'Product Added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product Added Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($id, Request $request)
    {
        $products = Product::find($id);

        if (empty($products)) {
            return redirect()->route('products.index')->with('error', 'Product record not Found');
        }

        
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $subCategories = SubCategory::where('category_id', $products->category_id)->get();
        $productImage = ProductImages::where('product_id', $products->id)->get();

        
        //DB::enableQueryLog();    for echo db query
        $relatedProducts = [];
        if($products->related_products != ''){
            $productArray = explode(',', $products->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)->get();
        }
        //dd(DB::getQueryLog());
        

        $data = [];
        $data['productImage'] = $productImage;
        $data['products'] = $products;
        $data['categories'] = $categories;
        $data['subCategories'] = $subCategories;
        $data['brands'] = $brands;
        $data['relatedProducts'] = $relatedProducts;
        return view('admin.product.edit', $data);
    }

    public function update($id, Request $request)
    {
        $product = Product::find($id);
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,' . $product->id . ',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,' . $product->id . ',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->shipping_returns = $request->shipping_returns;
            $product->related_products = (!empty($request->related_products)) ? implode(',', $request->related_products) : '';
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->save();

            session()->flash('success', 'Product Updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function delete($id, Request $request)
    {
        $product = Product::find($id);
        if (empty($product)) {
            session()->flash('error', 'Product record not Found');
            return response()->json([
                'status' => false,
                'message' => 'Product record not Found',
            ]);
        }

        $productImages = ProductImages::where('product_id', $id)->get();

        if (!empty($productImages)) {
            foreach ($productImages as $productImage) {
                $smallImagePath = public_path('/uploads/product/small/' . $productImage->image);
                $largeImagePath = public_path('/uploads/product/large/' . $productImage->image);

                if (File::exists($smallImagePath)) {
                    File::delete($smallImagePath);
                }

                if (File::exists($largeImagePath)) {
                    File::delete($largeImagePath);
                }
            }
            ProductImages::where('product_id', $id)->delete();
        }
        $product->delete();

        session()->flash('success', 'Product Deleted Successfully');
        return response()->json([
            'status' => true,
            'message' => 'Product Deleted Successfully',
        ]);
    }


    // Get Sub Categories
    public function getSubCategories(Request $request)
    {
        if (!empty($request->category_id)) {
            $subCategories = SubCategory::where('category_id', $request->category_id)->orderBy('name', 'ASC')->get();
            return response()->json([
                'status' => true,
                'subCategories' => $subCategories,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'subCategories' => [],
            ]);
        }
    }

    // Update Product Image
    public function updateProductImage(Request $request)
    {
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productImage = new ProductImages();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'NULL';
        $productImage->save();

        $imgName = $request->product_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
        $productImage->image = $imgName;
        $productImage->save();

        // Generate Product Thumbnails

        // Large
        $manager = new ImageManager(new Driver());
        $destPath = public_path() . '/uploads/product/large/' . $imgName;
        $img = $manager->read($sourcePath);
        $img->resize(1400, 1000);
        $img->save($destPath);

        // Small
        $manager = new ImageManager(new Driver());
        $destSmallPath = public_path() . '/uploads/product/small/' . $imgName;
        $img = $manager->read($sourcePath);
        $img->cover(300, 300);
        $img->save($destSmallPath);

        return response()->json([
            'success' => 'true',
            'image_id' => $productImage->id,
            'image_path' => asset('/uploads/product/small/' . $productImage->image),
            'message' => 'Image Uploaded Successfully',
        ]);
    }

    // Delete Product Image on spot
    public function deleteProductImage(Request $request)
    {
        $productImage = ProductImages::find($request->id);
        if (empty($productImage)) {
            return response()->json([
                'status' => false,
                'message' => 'Image not Found',
            ]);
        }

        File::delete(public_path() . '/uploads/product/small/' . $productImage->image);
        File::delete(public_path() . '/uploads/product/large/' . $productImage->image);
        $productImage->delete();

        return response()->json([
            'status' => true,
            'message' => 'Image Deleted Successfully',
        ]);
    }

    // Select2 get Products
    public function getProducts(Request $request){
        $tempProduct = [];
        if($request->term != ""){
            $products = Product::where('title','like', '%'.$request->term.'%')->get();
            
            if($products != null){
                foreach ($products as $product) {
                    $tempProduct[] = array('id' => $product->id, 'text' => $product->title);
                }
            }
        }

        return response()->json([
            'tags' => $tempProduct,
            'status' => true,
        ]);

        $data = [];
        $data['related_products'] = $products;

    }

    public function productRatings(Request $request){
        $ratings = ProductRating::select('product_ratings.*', 'products.title as productTitle')->orderBy('product_ratings.created_at', 'DESC');
        $ratings = $ratings->leftJoin('products', 'products.id', 'product_ratings.product_id');
        if($request->get('keyword') != ""){
            $ratings = $ratings->orWhere('products.title', 'like', '%'.$request->get('keyword').'%');
            $ratings = $ratings->orWhere('product_ratings.name', 'like', '%'.$request->get('keyword').'%');
        }
        $ratings = $ratings->paginate(10);
        return view('admin.product.ratings',[
            'ratings' => $ratings,
        ]);
    }

    function changeRatingStatus(Request $request){
        $productRating = ProductRating::find($request->id);
        $productRating->status = $request->status;
        $productRating->save();

        $message = "Status Changed Successfully";

        session()->flash('success', $message);
        return response()->json([
            'status' => true,
            'message'=> $message,
        ]);

    }
}
