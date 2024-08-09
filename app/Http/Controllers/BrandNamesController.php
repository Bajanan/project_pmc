<?php

namespace App\Http\Controllers;

use App\Models\BrandName;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class BrandNamesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = User::where('user_role',User::SUPPLIER)->get();
        $brandNames = BrandName::withTrashed()->get();

        return view('pharmacy.brand-name',compact('suppliers','brandNames'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|unique:brand_names,brand_name',
        ]);

        BrandName::create([
            'supplier_id' => $request->supplier,
            'brand_name' => $request->brand_name,
        ]);

        return back()->with('status', "Brand saved successfully");;
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
        $suppliers = User::where('user_role',User::SUPPLIER)->get();
        $brandNames = BrandName::all();
        $brandName = BrandName::findOrFail($id);
        return view('pharmacy.brand-name',compact('suppliers','brandName','brandNames'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       $brandName =  BrandName::findOrFail($id);
       $oldbrandName = $brandName->brand_name;
       $newbrandName = $request->brand_name;

       $brandName->update([
        'brand_name' => $newbrandName,
       ]);

       Product::where('brand_name', $oldbrandName)
           ->update(['brand_name' => $newbrandName]);

       return redirect()->route('brand-names.create')->with('status', "Brand updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brandName= BrandName::findOrFail($id);

        $hasRelatedItems = Product::where('brand_name', $brandName->brand_name)->exists();
        if ($hasRelatedItems) {
            return redirect()->route('brand-names.create')->with('error', 'Cannot delete brand name because it has associated with products.');
        }

        $brandName->delete();
        return redirect()->route('brand-names.create')->with('status', "Brand Name Deleted Successfully..!");
    }

    public function restore(string $id){

        $service = BrandName::where('id',$id)->onlyTrashed()->first();
        $service->restore();
        return redirect()->route('brand-names.create')->with('status', "Brand Name Restored Successfully..!");
    }
}
