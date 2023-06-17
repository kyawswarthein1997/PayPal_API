<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    //view product
    public function product(){
        $productData = Product::select('products.*', 'categories.category_id','categories.category_name')
                        ->leftJoin('categories', 'categories.category_id', 'products.category_id')
                        ->paginate(3);
        return view('Admin.product.product')->with(['productData' => $productData]);
    }

    //add product
    public function addProduct(){
        $categoryData = Category::get();
        return view('Admin.product.addProduct')->with(['categoryData' => $categoryData]);
    }

    //creaate product
    public function createProduct(Request $request){

        $validator = Validator::make($request->all(), [
            'category' => 'required',
            'product_name' => 'required',
            'image' => 'required',
            'small_description' => 'required',
            'description' => 'required',
            'original_price' => 'required',
            'selling_price' => 'required',
            'qty' => 'required',
            'tax' => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasFile('image')){

            $imageFile = $request->file('image');
            $imageName = uniqid().'_'.$imageFile->getClientOriginalName();
            $imageFile->move(public_path().'./uploads/product', $imageName);


        }

        $createProduct = $this->requestProductData($request, $imageName);
        Product::create($createProduct);
        return redirect()->route('admin#product')->with(['productCreated' => 'Product created Sucessfully']);
    }

    //edit product page
    public function editProduct($id){

        $categoryData = Category::get();
        $editProduct = Product::select('products.*','categories.category_id','categories.category_name')
                                ->join('categories', 'categories.category_id','=','products.category_id')
                                -> where('id',$id)
                                ->first();

        return view('Admin.product.editProduct')->with(['editProduct' => $editProduct, 'editCategoryData' => $categoryData]);
    }

    //update product page
    public function updateProduct(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'category' => 'required',
            'product_name' => 'required',
            'image' => 'required',
            'small_description' => 'required',
            'description' => 'required',
            'original_price' => 'required',
            'selling_price' => 'required',
            'qty' => 'required',
            'tax' => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }


        $updateData = $this->requestUpdateProductData($request);


        if(isset($updateData['image'])){
            //get old image
            $updateImgData = Product::select('image')->where('id', $id)->first();
            $updateImage = $updateImgData['image'];

            //delete old image
            if(File::exists(public_path().'/uploads/product/'.$updateImage)){
                File::delete(public_path().'/uploads/product/'.$updateImage);
            }

            //get new image
            $newImageFile = $request->file('image');
            $newImageName = uniqid().'_'.$newImageFile->getClientOriginalName();
            $newImageFile->move(public_path().'./uploads/product/', $newImageName);

            $updateData['image'] = $newImageName;

            //update database image
            Product::where('id',$id)->update($updateData);

            return redirect()->route('admin#product')->with(['updateData' => 'Product data updated Sucessfully']);

        }

    }

    //product info
    public function productInfo($id){
        $infoData = Product::select('products.*', 'categories.category_id','categories.category_name')
                        ->leftJoin('categories', 'categories.category_id', 'products.category_id')
                        ->where('id', $id)
                        ->first();
        return view('Admin.product.productInfo')->with(['productData' => $infoData]);
    }

    // product search
    public function searchProduct(Request $request){
        $searchKey = $request->tabel_search;

        $searchData = Product::select('products.*', 'categories.category_id','categories.category_name')
                            ->leftJoin('categories', 'categories.category_id', 'products.category_id')
                            ->orwhere('product_name', 'like', '%' . $searchKey. '%')
                            ->orwhere('original_price', 'like', '%' . $searchKey. '%')
                            ->orwhere('selling_price', 'like', '%' . $searchKey. '%')
                            ->orwhere('category_name', 'like', '%' . $searchKey. '%')
                            ->paginate(3);

        $searchData->appends($request->all());

        return view('Admin.product.product')->with(['productData' => $searchData]);
    }

    //delete product item
    public function deleteProduct($id){
        $deleteData = Product::select('image')->where('id', $id)->first();
        $deleteImage = $deleteData['image'];

        Product::where('id', $id)->delete(); //db data delete

        //project image folder delete
        if(File::exists(public_path().'/uploads/product/'.$deleteImage)){
            File::delete(public_path().'/uploads/product/'.$deleteImage);
        }

        return back()->with(['productDeleted' => "Product Delete Successfully"]);
    }

    //request product data
    private function requestProductData($request, $imageName){
        return[
            'category_id' =>$request->category,
            'product_name' => $request->product_name,
            'small_description' => $request->small_description,
            'description' => $request->description,
            'original_price' => $request->original_price,
            'selling_price' => $request->selling_price,
            'image' => $imageName,
            'qty' => $request->qty,
            'tax' => $request->tax,
            'status' => $request->status == True ? '1' : '0',
            'trending' => $request->trending == True ? '1' : '0',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    //request update product data
    private function requestUpdateProductData($request){
        $arr = [
            'category_id' =>$request->category,
            'product_name' => $request->product_name,
            'small_description' => $request->small_description,
            'description' => $request->description,
            'original_price' => $request->original_price,
            'selling_price' => $request->selling_price,
            'qty' => $request->qty,
            'tax' => $request->tax,
            'status' => $request->status == True ? '1' : '0',
            'trending' => $request->trending == True ? '1' : '0',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        if(isset($request->image)){
            $arr['image'] = $request->image;
        }

        return $arr;
    }
}
