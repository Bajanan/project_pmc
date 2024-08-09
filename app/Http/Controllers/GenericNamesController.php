<?php

namespace App\Http\Controllers;

use App\Models\GenericName;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GenericNamesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genericNames = GenericName::withTrashed()->get();
        return view('pharmacy.generic-name',compact('genericNames'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'generic_name' => 'required|unique:generic_names,generic_name',
        ]);

        GenericName::create($request->all());
        return back()->with('status', "Generic Name saved successfully");
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

        $genericNames = GenericName::all();
        $Name = GenericName::findOrFail($id);
        return view('pharmacy.generic-name',compact('genericNames','Name'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $genericName = GenericName::findOrFail($id);
        $oldGenericName = $genericName->generic_name;
        $newGenericName = $request->generic_name;

        $genericName->update(["generic_name" => $newGenericName]);

        Product::where('generic_name', $oldGenericName)
            ->update(['generic_name' => $newGenericName]);

        return redirect()->route('generic-names.create')->with('status', "Generic Name updated successfully");

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $genericName= GenericName::findOrFail($id);
        //check whether the generic name is related with a product if so it cannot be deleted
        $hasRelatedItems = Product::where('generic_name', $genericName->generic_name)->exists();
        if ($hasRelatedItems) {
            return redirect()->route('generic-names.create')->with('error', 'Cannot delete generic name because it has associated with products.');
        }

        $genericName->delete();
        return redirect()->route('generic-names.create')->with('status', "Generic name Deleted Successfully..!");
    }


    public function restore(string $id){

        $service = GenericName::where('id',$id)->onlyTrashed()->first();
        $service->restore();
        return redirect()->route('generic-names.create')->with('status', "Generic name Restored Successfully..!");
    }
}
