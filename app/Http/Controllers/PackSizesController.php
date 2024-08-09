<?php

namespace App\Http\Controllers;

use App\Models\PackSize;
use App\Models\Product;
use Illuminate\Http\Request;

class PackSizesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function create()
    {
        $packSizes = PackSize::withTrashed()->get();
        return view('pharmacy.pack',compact('packSizes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        PackSize::create($request->all());
        return back()->with('status', "Pack size saved successfully");;
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

        $packSizes = PackSize::all();
        $packSize = PackSize::findOrFail($id);
        return view('pharmacy.pack',compact('packSizes','packSize'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $packSize = PackSize::findOrFail($id);
        $packSize->update([
            'pack_size'=>$request->pack_size,
            'pack_size_value'=>$request->pack_size_value
        ]);
        return redirect()->route('pack-sizes.create')->with('status', "Pack size updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $packSize= PackSize::findOrFail($id);

        $hasRelatedItems = Product::where('pack_size_id', $packSize->id)->exists();

        if ($hasRelatedItems) {
            return redirect()->route('pack-sizes.create')->with('error', 'Cannot delete pack size because it has associated with products.');
        }

        $packSize->delete();
        return redirect()->route('pack-sizes.create')->with('status', "Service Deleted Successfully..!");
    }

    public function restore(string $id){

        $service = PackSize::where('id',$id)->onlyTrashed()->first();
        $service->restore();
        return redirect()->route('pack-sizes.create')->with('status', "Service Restored Successfully..!");
    }
}
