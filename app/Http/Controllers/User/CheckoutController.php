<?php

namespace App\Http\Controllers\User;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    //checkout page
    public function checkout(){
        $old_cartItems = Cart::where('user_id', Auth::id())->get();
        foreach($old_cartItems as $items){
            if(!Product::where('id', $items->product_id)->where('qty', '>=' , $items->product_qty)->exists()){
                $removeItem = Cart::where('user_id', Auth::id())->where('product_id', $items->product_id)->first();
                $removeItem->delete();
            }
        }
        $cartItems = Cart::where('user_id', Auth::id())->get();
        return view('user.checkout.checkout')->with(['cartData' => $cartItems]);
    }

    //place the orders
    public function placeOrder(Request $request){

        $order = new Order();
        $order->user_id = Auth::id();
        $order->name = $request->input('name');
        $order->email = $request->input('email');
        $order->phone = $request->input('phone');
        $order->address = $request->input('address');
        $order->city = $request->input('city');
        $order->state = $request->input('state');
        $order->country = $request->input('country');
        // $order->pin_code = $request->input('pin_code');

        //payment process
        $order->payment_mode = $request->input('payment_mode');
        $order->payment_id = $request->input('payment_id');

        // to calculate the total price
        $total = 0;

        $cart_items_total = Cart::where('user_id', Auth::id())->get();

        foreach($cart_items_total as $order_total){
            $total += $order_total->products->selling_price * $order_total->product_qty;

        }
        $order->total_price = $total;

        $order->tracking_no = 'jumpstart'.rand(1111,9999);
        $order->save();


        $cartData = Cart::where('user_id', Auth::id())->get();
        foreach($cartData as $item){

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'order_qty' => $item->product_qty,
                'order_price' => $item->products->selling_price,
            ]);

            $product = Product::where('id', $item->product_id)->first();

            $product->qty = $product->qty - $item->product_qty;

            $product->update();
        }


        if(Auth::user()->city == NULL){
            $user = User::where('id', Auth::id())->first();

            $user->city = $request->input('city');
            $user->state = $request->input('state');
            $user->country = $request->input('country');
            // $user->pin_code = $request->input('pin_code');

            $user->update();
        }

        $cartData = Cart::where('user_id', Auth::id())->get();
        Cart::destroy($cartData);

        if($request->input('payment_mode') == "Paid by PayPal"){

            return response()->json(['status' => 'Order Place Successfully']);
        }

        return redirect('/user')->with(['orderSuccess' => 'Order Placed Successfully']);
    }


}
