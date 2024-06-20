<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null){
        $data = [];
        $categorySelected = '';
        $subCategorySelected = '';
        $brandsArray = [];
        
        // Retrieve all categories and brands
        $data['categories'] = Category::orderBy('name', 'ASC')->with('sub_categories')->where('status', 1)->where('showHome', 'yes')->get();
        $data['brands'] = Brand::orderBy('name', 'ASC')->where('status', 1)->get();
    
        // Filter products based on category and subcategory if provided
        $products = Product::where('status', 1);
    
        if($categorySlug){
            $category = Category::where('slug', $categorySlug)->first();
            if($category){
                $products->where('category_id', $category->id);
                $data['selectedCategory'] = $category;
                $categorySelected = $category->id;
            }
        }
    
        if($subCategorySlug){
            $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
            if($subCategory){
                $products->where('sub_category_id', $subCategory->id);
                $data['selectedSubCategory'] = $subCategory;
                $subCategorySelected = $subCategory->id;
            }
        }

        // Brand Filter
        if(!empty($request->get('brand'))){
            $brandsArray = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brandsArray);
        }

        // Price Filter
        if ($request->filled('price_min') && $request->get('price_max') != '') {
            if($request->get('price_max') == 10000){
                $products = $products->whereBetween('price', [intval($request->get('price_min')), 1000000]);
            }else{
                $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
            }
        }

        // Search Filter
        if($request->get('search') != ""){
            $products = $products->where('title', 'like', '%'. $request->get('search') .'%');
        }

        // Sorting Filter
        if($request->get('sort') != ""){
            if($request->get('sort') == "latest"){
                $products = $products->orderBy('id', 'DESC');
            }
            else if($request->get('sort') == "price_asc"){
                $products = $products->orderBy('price', 'ASC');
            }
            else {
                $products = $products->orderBy('price', 'DESC');
            }
        }
        else{
            $products = $products->orderBy('id', 'DESC');
        }
        
    
        // Retrieve filtered products
        $data['products'] = $products->paginate(6);
        $data['categorySelected'] = $categorySelected;
        $data['subCategorySelected'] = $subCategorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['priceMax'] = (intval($request->get('price_max')) == 0) ? 10000 : intval($request->get('price_max'));
        $data['priceMin'] = intval($request->get('price_min'));
        $data['sort'] = $request->get('sort');
    
        return view('front.shop', $data);
    }

    public function product($slug){
        $product = Product::where('slug', $slug)
                    ->withCount('product_ratings')
                    ->withSum('product_ratings', 'rating')
                    ->with(['product_images', 'product_ratings'])->first();
        //dd($product);

        if($product == null){
            abort(404);
        }

        //DB::enableQueryLog();
        $relatedProducts = [];
        if(!empty($product->related_products)){
            $productArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)->where('status', 1)->with('product_images')->get();
           // dd(DB::getQueryLog());
        }

        // Rating Avg
        /* "product_ratings_count" => 2
    "product_ratings_sum_rating" => 7.0 */

    $avgRating = '0.0';
    $avgRatingPer = '0';
    if($product->product_ratings_count > 0){
        $avgRating = number_format(($product->product_ratings_sum_rating / $product->product_ratings_count), 1);
        $avgRatingPer = ($avgRating * 100)/5;
    }

        $data['product'] = $product;
        $data['relatedProducts'] = $relatedProducts;
        $data['avgRating'] = $avgRating;
        $data['avgRatingPer'] = $avgRatingPer;
        return view('front.product', $data);
    }

    public function saveRating(Request $request, $productId){
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5',
            'email' => 'required|email',
            'rating' => 'required',
            'comment' => 'required|min:10',
        ]);

        if ($validator->passes()) {
            $count = ProductRating::where('email', $request->email)->where('product_id', $productId)->count();

            if($count > 0){

                session()->flash('error', 'You have already rated this product');

                return response()->json([
                    'status' => true,
                    'message' => 'You have already rated this product',
                ]);
            }

            $productRating = new ProductRating();
            $productRating->product_id = $productId;
            $productRating->name = $request->name;
            $productRating->email = $request->email;
            $productRating->rating = $request->rating;
            $productRating->comment = $request->comment;
            $productRating->save();

            session()->flash('success', 'Thanks for your valuable review');

            return response()->json([
                'status' => true,
                'message' => 'Thanks for your valuable review',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }
    }
    
}