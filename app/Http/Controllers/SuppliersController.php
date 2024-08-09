<?php

namespace App\Http\Controllers;

use App\Models\GRN;
use App\Models\User;
use App\Models\BrandName;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{

    public function index()
    {
        $suppliers =  User::where('user_role', User::SUPPLIER)->get();

        return view('supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'nullable|unique:users,email',
            'active_status' => 'required',
        ]);

        if(!$request->filled('active_status')){

            $request->merge(['active_status'=>"0"]);
        }

        $request->merge(['user_role' => User::SUPPLIER]);

        User::create($request->all());
        return redirect()->route('suppliers.index')->with('status', "Supplier added Successfully..!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        $grns = GRN::where('supplier_id', $id)->get();

        return view('supplier.view', compact('user','grns'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('supplier.create', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'unique:users,email,' . $id,
        ]);

        $active_status = $request->has('active_status') ? '1' : '0';

        $user =  User::findOrFail($id);
        $user->update([
            'contact_number' => $request->contact_number,
            'name' => $request->name,
            'email' => $request->email,
            'active_status' => $active_status
        ]);
        return redirect()->route('suppliers.index')->with('status', "Supplier Updated Successfully..!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $hasRelatedItems = BrandName::where('supplier_id', $id)->exists();
        if ($hasRelatedItems) {
            return redirect()->route('suppliers.index')->with('error', 'Cannot delete this Supplier because it has associated with brands.');
        }

        $user->delete();
        return redirect()->route('suppliers.index')->with('status', "Supplier Deleted Successfully..!");
    }
}
