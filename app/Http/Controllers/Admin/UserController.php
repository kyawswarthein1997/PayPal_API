<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    //user list
    public function userList(){
        $userData = User::where('role', '=', 'user')->paginate(2);
        return view('Admin.user.userList')->with(['userList' => $userData]);
    }

    //admin list
    public function adminList(){
        $adminData = User::where('role', '=', 'admin')->paginate(2);
        return view('Admin.user.adminList')->with(['adminList' => $adminData]);
    }

    //user delete
    public function deleteUser($id){
        User::where('id', $id)->delete();
        return back()->with(['userDelete' => 'User Deleted Sucessfully..!']);
    }

    //admin delete
    public function deleteAdmin($id){
        User::where('id', $id)->delete();
        return back()->with(['adminDelete' => 'Admin Deleted Sucessfully']);
    }

    //userSearch
    public function userSearch(Request $request){
        $userResponse = $this->search($request->searchData, 'user', $request);
        return view('Admin.user.userList')->with(['userList' => $userResponse]);
    }

    //adminSearch
    public function adminSearch(Request $request){
        $adminResponse = $this->search($request->searchData, 'admin', $request);
        return view('admin.user.adminList')->with(['adminList' => $adminResponse]);
    }


    //search function this function use to group out the query of searching defining of data by role
    private function search($key, $role, $request){
        $searchData = User::where('role', $role);
        $searchData = $searchData->where(function ($query) use ($key) {
                            $query->orwhere('name', 'like', '%' . $key. '%')
                            ->orwhere('email', 'like', '%' . $key. '%')
                            ->orwhere('phone', 'like', '%' . $key. '%')
                            ->orwhere('address', 'like', '%' . $key. '%');
                        })
                        ->paginate(2);

        $searchData->appends($request->all());
        return $searchData;
    }
}
