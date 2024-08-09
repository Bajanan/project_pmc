<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clinic;

class ClinicController extends Controller
{
    public function index()
    {
        $clinic = Clinic::first();
        return view('app.index', compact('clinic'));
    }

    public function edit($id)
    {
        $clinic = Clinic::findOrFail($id);
        return view('app.index', compact('clinic'));
    }

    public function show($id)
    {
        $clinic = Clinic::findOrFail($id);
        return view('clinics.show', compact('clinic'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'clinic_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'mobile' => 'required|string|max:15',
            'bill_message' => 'nullable|string|max:255',
            'clinic_address' => 'required|string|max:255',
            'clinic_logo' => 'nullable|image|mimes:jpeg,png|max:2048',
        ]);

        $clinic = Clinic::findOrFail($id);
        $clinic->clinic_name = $request->clinic_name;
        $clinic->email = $request->email;
        $clinic->phone = $request->phone;
        $clinic->mobile = $request->mobile;
        $clinic->bill_message = $request->bill_message;
        $clinic->clinic_address = $request->clinic_address;

        if ($request->hasFile('clinic_logo')) {
            $image = $request->file('clinic_logo');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $imageName);
            $clinic->clinic_logo = 'uploads/'.$imageName;
        }

        $clinic->save();

        return redirect()->back()->with('success', 'Clinic details updated successfully.');
    }
}
