<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors =  User::where('user_role', User::DOCTOR)->get();

        return view('doctor.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('doctor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'contact_number' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'active_status' => 'required'
        ]);

        if(!$request->filled('active_status')){

            $request->merge(['active_status'=>"0"]);
        }


        $request->merge(['user_role' => User::DOCTOR]);
        User::create($request->all());
        return redirect()->route('doctors.index')->with('status', "Doctor added Successfully..!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        $current_date = Carbon::now()->format('Y-m-d');
        $schedules = Appointment::where('doctor_id',$id)->where('date',$current_date)->get();

        return view('doctor.view', compact('user','schedules'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('doctor.create', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'contact_number' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $id,
        ]);

        $active_status = $request->has('active_status') ? '1' : '0';

        $user =  User::findOrFail($id);
        $user->update([
            'contact_number' => $request->contact_number,
            'name' => $request->name,
            'email' => $request->email,
            'active_status' => $active_status
        ]);
        return redirect()->route('doctors.index')->with('status', "Doctor Updated Successfully..!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('doctors.index')->with('status', "Doctor Deleted Successfully..!");
    }
}
