<?php

namespace App\Http\Controllers\User;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    //add to cart
    public function addToCart(Request $request){
        $product_id = $request->input('product_id');
        $product_qty = $request->input('product_qty');

        if(Auth::check()){

            $productCheck = Product::where('id', $product_id)->first();

            if($productCheck){

                if(Cart::where('product_id',$product_id)->where('user_id', Auth::id())->exists()){

                    return response()->json(['status' => $productCheck->product_name.'Already added to Cart']);
                }
                else{

                    $cartItem = new Cart();
                    $cartItem->product_id = $product_id;
                    $cartItem->user_id = Auth::id();
                    $cartItem->product_qty = $product_qty;
                    $cartItem->save();
                    return response()->json(['status' => $productCheck->product_name.'Added to Cart']);
                }
            }

        }else{
            return response()->json(['status' => 'Login to Continue']);
        }
    }

    //view cart page
    public function viewCart(){

        $cart_items = Cart::where('user_id', Auth::id())
                            ->get();
        return view('user.cart.viewCart')->with(['cartItems' => $cart_items]);
    }

    //update cart items
    public function updateCartItems(Request $request){

        $product_id = $request->input('product_id');
        $prod_qty = $request->input('product_qty');

        if(Auth::check()){

            if(Cart::where('product_id',$product_id)->where('user_id', Auth::id())->exists()){

                $cart = Cart::where('product_id',$product_id)->where('user_id', Auth::id())->first();
                $cart->product_qty = $prod_qty;
                $cart->update();

                // Cart::where('product_qty', $product_update_qty)->where('user_id', Auth::id())->update();

                return response()->json(['status' => 'Quantatiy Updated']);
            }

        }

    }

    //delete cart items
    public function deleteCartItems(Request $request){

        if(Auth::check()){
            $prod_id = $request->input('product_id');


            if(Cart::where('product_id',$prod_id)->where('user_id', Auth::id())->exists()){

                $cartData = Cart::where('product_id',$prod_id)->where('user_id', Auth::id())->delete();

                return response()->json(['status' => $cartData.'Item has been removed']);
            }


        }else{
            return response()->json(['status' => 'Login to Continue']);
        }

    }


    //
    public function cartCount(){
        $cartcount = Cart::where('user_id', Auth::id())->count();
        return response()->json(['count' => $cartcount]);
    }
}
