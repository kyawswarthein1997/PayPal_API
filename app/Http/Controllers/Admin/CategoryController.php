<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    //view category
    public function category(){
        $categoryData = Category::paginate(3);
        return view('Admin.category.adminViewCategory')->with(['categoryData' => $categoryData]);
    }

    //category search
    public function searchCategory(Request $request){
        $searchData = Category::where('category_name', 'like', '%' . $request->searchData. '%')->paginate(3);
        $searchData->appends($request->all());
        return view('Admin.category.adminViewCategory')->with(['categoryData' => $searchData]);
    }

    //add category
    public function addCategory(){
        return view('Admin.category.addCategory');
    }

    //create category
    public function createCategory(Request $request){

        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'image' => 'required',
            'descriptions' => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }


        $category = new Category();

        if($request->hasfile('image')){

            $imageFile = $request->file('image');
            $imageName = uniqid().'_'.$imageFile->getClientOriginalName();
            $imageFile->move(public_path().'./uploads/category', $imageName);

            $category->image = $imageName;
        }

        $category->category_name = $request->input('category_name');
        $category->descriptions = $request->input('descriptions');
        $category->status = $request->input('status') == True ? '1' : '0';
        $category->popular = $request->input('popular') == True ? '1' : '0';
        $category->save();
        return redirect()->route('admin#category')->with(['categoryCreated', 'Category Created Sucessfully']);
    }


    //edit category
    public function editCategory($id){
        $editCategory = Category::where('category_id', $id)->first();
        return view('Admin.category.editCategory')->with(['editCategory' => $editCategory]);
    }

    //update category
    public function updateCategory(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'image' => 'required',
            'descriptions' => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = $this->requestUpdateCategoryData($request);

       //get old image
       $updateImgData = Category::select('image')->where('category_id', $id)->first();
       $updateImage = $updateImgData['image'];

       //delete old image
       if(File::exists(public_path().'/uploads/category/'.$updateImage)){
           File::delete(public_path().'/uploads/category/'.$updateImage);
       }

       //get new image
       $newImageFile = $request->file('image');
       $newImageName = uniqid().'_'.$newImageFile->getClientOriginalName();
       $newImageFile->move(public_path().'./uploads/category/', $newImageName);

       $updateData['image'] = $newImageName;


       //update database image
       Category::where('category_id',$id)->update($updateData);
       return redirect()->route('admin#category')->with(['updateData' => 'Category data updated Sucessfully']);



    }

    //deletecategory
    public function deleteCategory($id){
        $deleteData = Category::select('image')->where('category_id', $id)->first();
        $deleteImage = $deleteData['image'];

        Category::where('category_id', $id)->delete(); //db data delete

        //project image folder delete
        if(File::exists(public_path().'/uploads/category/'.$deleteImage)){
            File::delete(public_path().'/uploads/category/'.$deleteImage);
        }

        return back()->with(['categoryDeleted' => "Category Delete Successfully"]);
    }

   //request update category data
    private function requestUpdateCategoryData($request){
        $arr = [
            'category_name' => $request->category_name,
            'descriptions'=> $request->descriptions,
            'status' => $request->status == True ? '1' : '0',
            'popular' => $request->popular == True ? '1' : '0',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        if(isset($request->image)){
            $arr['image'] = $request->image;
        }

        return $arr;
    }

}
