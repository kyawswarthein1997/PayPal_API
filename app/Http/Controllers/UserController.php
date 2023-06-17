<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //user index page
    public function index(){
        $featureProduct = Product::where('trending', '1')->get();
        $categoryStatus = Category::where('status', '1')->get();
        return view('user.dashboard.home')->with(['featureProduct' => $featureProduct,'categoryStatus' => $categoryStatus]);
    }

    //user category page
    public function viewCategory($id){
        $cat = Category::where('category_id', $id)->first();
        $data = Category::select('categories.*', 'products.*')
                        ->join('products','categories.category_id','products.category_id')
                        ->orwhere('products.category_id', $id)
                        ->get();
        $featureProduct = Product::where('trending', '1')->get();
        return view('user.dashboard.viewCategory')->with(['viewCategory' => $data , 'categoryData' => $cat, 'featureProduct' => $featureProduct]);
    }

    //product details page
    public function productDetails($product_name){
        $productData = Product::where('product_name', $product_name)->first();
        return view('user.dashboard.productDetails')->with(['productDetails' => $productData]);
    }

}
