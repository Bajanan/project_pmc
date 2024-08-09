<?php

namespace App\Http\Controllers;

use App\Models\LogDetails;
use App\Models\User;
use Illuminate\Http\Request;

class StaffsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $staffs =  User::where('user_role', User::STAFF)
        ->orWhere('user_role', User::MANAGER)
        ->get();

        return view('staff.index', compact('staffs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('staff.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'password' => 'required',
            'contact_number' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'user_role' => 'required',

        ]);

        if(!$request->filled('active_status')){
            $request->merge(['active_status'=>"0"]);
        }


        User::create($request->all());
        return redirect()->route('staffs.index')->with('status', "Staff added Successfully..!");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // dd($id);
        $user = User::findOrFail($id);
        $log_details = LogDetails::where('user_id',$id)->limit(5)->get();


        return view('staff.view', compact('user','log_details'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('staff.create', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'contact_number' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $id,
            'user_role' => 'required',
        ]);

        $active_status = $request->has('active_status') ? '1' : '0';

        $user =  User::findOrFail($id);
        $user->update([
            'contact_number' => $request->contact_number,
            'name' => $request->name,
            'email' =>$request->email,
            'user_role' => $request->user_role,
            'active_status' => $active_status,
        ]);
        return redirect()->route('staffs.index')->with('status', "Staff Updated Successfully..!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('staffs.index')->with('status', "Staff Deleted Successfully..!");
    }
}
