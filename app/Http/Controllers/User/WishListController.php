<?php

namespace App\Http\Controllers\User;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WishListController extends Controller
{
    //view wishlist
    public function viewWishlist(){
        $wishlist = Wishlist::where('user_id', Auth::id())->get();
        return view('user.wishlist.viewWishlist')->with(['whishlist' => $wishlist]);
    }



    //add to wishlist
    public function addToWishlist(Request $request){

        if(Auth::check()){

            $product_id = $request->input('product_id');
            if(Product::find($product_id)){

                $wish = new Wishlist();
                $wish->product_id = $product_id;
                $wish->user_id = Auth::id();
                $wish->save();
                return response()->json(['status' => 'Product added to wishlist']);

            }else{
                return response()->json(['status' => 'Product does not exist']);
            }
        }
        else{
            return response()->json(['status' => 'Login to Continue']);
        }


        return view('user.wishlist.addToWishlist');
    }
}
