<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Models\Brand; 
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // Add product form
    public function create()
    {
        $categories = Category::all();
        $brand = Brand::all();
        return view('admin.product.add', compact('categories', 'brand'));
    }


     //show all data
     public function index()
     {
    $products = Product::with(['category', 'brand'])->orderBy('id', 'desc')->paginate(2);
    return view('admin.product.show', compact('products'));
    }


    public function dataShow($id)
{
    $product = Product::with(['creatorUser', 'editorUser', 'category', 'brand'])->findOrFail($id);
    return view('admin.product.index', compact('product'));
}




   // Insert product data
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|max:100',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'price' => 'required|numeric',
            'cost' => 'required|numeric',
            'code' => 'required|unique:products,code',
            'unit' => 'required|numeric|min:1',
            'details' => 'nullable',
            'img_url' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
            'status' => 'required|in:1,0',
        ]);

        $image_rename = '';
        if ($request->hasFile('img_url')) {
            $image = $request->file('img_url');
            $ext = $image->getClientOriginalExtension();
            $image_rename = time() . '_' . rand(100000, 10000000) . '.' . $ext;
            $image->move(public_path('productImage'), $image_rename);
        }

        $product = new Product();
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->price = $request->price;
        $product->cost = $request->cost;
        $product->code = $request->code;
        $product->unit = $request->unit;
        $product->details = $request->details;
        $product->img_url = $image_rename;
        $product->status = $request->status;
        $product->creator = auth()->id();
        $product->slug = Str::random(10);
        $product->save();


        return redirect()->back()->with('success', 'Product added successfully.');
    }



    // edit product form

    public function edit($id){
        $product = Product::find($id);
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.product.edit', compact('product', 'categories', 'brands'));
    }

    // Update product data

    public function update(Request $request, $id)
    {
    $request->validate([
        'name' => 'required|max:100',
        'category_id' => 'required|exists:categories,id',
        'brand_id' => 'required|exists:brands,id',
        'price' => 'required|numeric',
        'cost' => 'required|numeric',
        'code' => 'required',
        'unit' => 'required|numeric|min:1',
        'details' => 'nullable',
        'img_url' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
    ]);
    $product = Product::find($id);

    if (!$product) {
        return back()->with('fail', 'Product not found.');
    }
    $image_rename = $product->img_url;
    if ($request->hasFile('img_url')) {
        if ($product->img_url && file_exists(public_path('productImage/' . $product->img_url))) {
            unlink(public_path('productImage/' . $product->img_url));
        }

        $image = $request->file('img_url');
        $ext = $image->getClientOriginalExtension();
        $image_rename = time() . '_' . rand(100000, 10000000) . '.' . $ext;
        $image->move(public_path('productImage'), $image_rename);
    }
    $product->update([
        'name' => $request['name'],
        'category_id' => $request['category_id'],
        'brand_id' => $request['brand_id'],
        'price' => $request['price'],
        'cost' => $request['cost'],
        'code' => $request['code'],
        'unit' => $request['unit'],
        'details' => $request['details'],
        'img_url' => $image_rename,
        'status' => $request['status'],
    ]);

    if ($product) {
        return redirect()->back()->with('success', 'Product updated successfully.');
    } else {
        return back()->with('fail', 'Failed to update product.');
    }
}

// Show product details
public function destroy($id)
{
    $product = Product::find($id);

    if (!$product) {
        return redirect()->back()->with('fail', 'Product not found.');
    }
    if ($product->img_url && file_exists(public_path('productImage/' . $product->img_url))) {
        unlink(public_path('productImage/' . $product->img_url));
    }
    $product->delete();
    return redirect()->back()->with('success', 'Product deleted successfully.');
}

}
