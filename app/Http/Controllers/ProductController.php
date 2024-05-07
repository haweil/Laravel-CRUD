<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    // this method will show product page
    public function index (){
        $products =Product::orderBy('created_at','DESC')->get();
        return view('products.list',[
            'products'=>$products
        ]);

    }

    // this method will show create product page
    public function create (){

        return view('products.create');

    }

    // this method will store a product in db
    public function store (Request $request) {
        $rules = [
        'name' => 'required|min:5',
        'sku' => 'required|min:3',
        'price' => 'required|numeric',
        ];

        if ($request->hasFile('image')){
            $rules['image']='image';
        }


      $validator=Validator::make($request->all(),$rules);
        if ($validator->fails()){
        return redirect()->route('products.create')->withInput()->withErrors($validator);
        }

        // here we will insert in db
        $product = new Product();
        $product->name=$request->name;
        $product->sku=$request->sku;
        $product->price=$request->price;
        $product->description=$request->description;


        if ($request->hasFile('image')){
            // here we will store image
            $image =$request->file('image');
            $ext=$image->getClientOriginalExtension();
            $imageName=time().'.'.$ext; //unique image name

            // Save image to product directory
            $image->move(public_path('uploads/products'),$imageName);

            // save image in database
            $product->image=$imageName;
         }
         $product->save();
        return redirect()->route('products.index')->with('success','Product added Successfully');
    }

     // this method will show edit product page
     public function edit ($id){
        $product=Product::findOrFail($id);
        return view('products.edit',[
            'product'=>$product,
        ]);

    }

    //this method will update a product
     public function update ($id,Request $request){

        $product=Product::findOrFail($id);

        $rules = [
            'name' => 'required|min:5',
            'sku' => 'required|min:3',
            'price' => 'required|numeric',
            ];

            if ($request->hasFile('image')){
                $rules['image']='image';
            }

          $validator=Validator::make($request->all(),$rules);
            if ($validator->fails()){
            return redirect()->route('products.edit',$product->id)->withInput()->withErrors($validator);
            }

            // here we will update product in db
            $product->name=$request->name;
            $product->sku=$request->sku;
            $product->price=$request->price;
            $product->description=$request->description;


            if ($request->hasFile('image')){

                //delete old image
                File::delete(public_path('uploads/products/',$product->image));

                // here we will store image
                $image =$request->file('image');
                $ext=$image->getClientOriginalExtension();
                $imageName=time().'.'.$ext; //unique image name

                // Save image to product directory
                $image->move(public_path('uploads/products'),$imageName);

                // save image in database
                $product->image=$imageName;
             }
             $product->save();
            return redirect()->route('products.index')->with('success','Product update Successfully');
     }

    public function destroy ($id)
    {

        $product=Product::findOrFail($id);

        //delete image
        File::delete(public_path('uploads/products/',$product->image));
        //delete product from database
        $product->delete();

        return redirect()->route('products.index')->with('success','Product deleted Successfully');

    }


}