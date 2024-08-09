<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\BrandName;
use App\Models\GenericName;
use App\Models\PackSize;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::with('batch')->withTrashed()->get();

        return view('pharmacy.index',compact('product'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $suppliers = User::where('user_role',User::SUPPLIER)->orderBy('name', 'asc')->get();
        $generic_names = GenericName::orderBy('generic_name', 'asc')->get();
        $pack_sizes= PackSize::all();
        return view('pharmacy.create',compact('suppliers','generic_names','pack_sizes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        if(!$request->filled('active_status')){
            $request->merge(['active_status'=>"0"]);
        }

        Product::create([

            'supplier_id' => $request->supplier,
            'product_name' => $request->product_name,
            'barcode' => $request->barcode,
            'brand_name' => $request->brand,
            'generic_name'  => $request->generic_name,
            'pack_size_id' => $request->pack_size,
            'active_status' => $request->active_status,
            'category' => $request->category

        ]);
        return back()->with('status', "Product Added Successfully..!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $suppliers = User::where('user_role',User::SUPPLIER)->get();
        $generic_names = GenericName::all();
        $pack_sizes= PackSize::all();
        return view('pharmacy.create',compact('product','suppliers','generic_names','pack_sizes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       $active_status = $request->has('active_status') ? '1' : '0';

       Product::where('id',$id)->update([
        /* 'supplier_id' => $request->supplier, */
        'product_name' => $request->product_name,
        'barcode' => $request->barcode,
        /* 'brand_name' => $request->brand, */
        'generic_name'  => $request->generic_name,
        'pack_size_id' => $request->pack_size,
        'active_status' => $active_status,
        'category' => $request->category
       ]);

       return back()->with('status', "Product Updated Successfully..!");
    }

    /**
     * Remove the specified resource from storage.
     */


    public function filterBrandNames(Request $request){

        $brands = BrandName::where('supplier_id',$request->Supplier)->get();
        return response()->json(['brands'=> $brands]);
    }

    public function sellingUnit(Request $request){
        $packValue = PackSize::where('id',$request->Packs)->value('pack_size_value');
        return response()->json(["value"=> $packValue ]);
    }

    public function destroy(string $id)
    {

        $product= Product::findOrFail($id);
        //check whether the product is related with a batch if so it cannot be deleted
        $hasRelatedItems = Batch::where('product_id', $id)->exists();
        if ($hasRelatedItems) {
            return redirect()->route('products.index')->with('error', 'Cannot delete product because it has associated with a batch.');
        }

        $product->delete();
        return back()->with('status', "Product Deleted Successfully..!");
    }


    public function restore(string $id){

        $service = Product::where('id',$id)->onlyTrashed()->first();
        $service->restore();
        return redirect()->route('products.index')->with('status', "Product Restored Successfully..!");
    }
}
