<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderListController extends Controller
{
     //user order list
     public function orderList(){
        $orders = Order::where('user_id', Auth::id())->get();
        return view('user.orderlist.myOrder')->with(['orders' => $orders]);

    }

    //view order list
    public function viewOrder($id){
        $orders = Order::where('id', $id)->where('user_id', Auth::id())->first();
        return view('user.orderlist.viewOrderList')->with(['orders' => $orders]);
    }
}
