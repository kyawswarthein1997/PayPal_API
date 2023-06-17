<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index(){
        $adminId = auth()->user()->id;
        $adminData = User::where('id', $adminId)->first();
        return view('Admin.home')->with(['admin' => $adminData]);
    }
}
