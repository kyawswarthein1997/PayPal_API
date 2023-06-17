<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    //view order list
    public function adminOrderList(){
        $orderList = Order::where('status', '0')->get();
        return view('Admin.order.adminOrderList')->with(['orderList' => $orderList ]);
    }

    //view order
    public function adminViewOrder($id){
        $orders = Order::where('id', $id)->first();
        return view('Admin.order.adminViewOrder')->with(['orders' => $orders]);
    }

    //update Order
    public function updateOrder(Request $request, $id){
        $orders = Order::find($id);
        $orders->status = $request->input('order_status');
        $orders->update();
        return redirect()->route('admin#orderList')->with(['orderUpdated' => 'Order Updated Successfully']);
    }

    //order history
    public function orderHistory(){
        $orderList = Order::where('status', '1')->get();
        return view('Admin.order.orderHistory')->with(['orderList' => $orderList ]);
    }
}
