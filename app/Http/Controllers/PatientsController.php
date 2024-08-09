<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PatientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $all_patients = User::where('user_role', User::PATIENT)->orderBy('id', 'desc')->get();

       return view('patient.index',compact('all_patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $latest_regid = User::where('user_role', User::PATIENT)->latest()->first();

        if ($latest_regid) {
            $current_regid = $latest_regid->reg_no;
            $numeric_part = preg_replace('/\D/', '', $current_regid);
            $numeric_part = intval($numeric_part);
        }
        else{
            $numeric_part = 0;
        }
        $new_regid = $numeric_part + 1;
        $reg_no = "PMC" .str_pad($new_regid, 4, '0', STR_PAD_LEFT);

        return view("patient.create",compact('reg_no'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

       $validate =  $this->validate($request,[
            'contact_number' => 'required',
            'name' => 'required',
            'DOB' => 'required',
            'address' => 'required',

        ]);

        //advance validation check existing data
        $existing_data = User::where('contact_number', $request->contact_number)->where('name',$request->name)->where('DOB',$request->DOB)->exists();

        if($existing_data){

            return back()->with('error', " Contact Number, Name, DOB already exists..!");
        }

        $request->merge(['user_role' => User::PATIENT]);
        $patient = User::create($request->all());

        return redirect()->route('patients.show', $patient->id)->with('status',"Patient added Successfully..!" );


    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
       $user = User::findOrFail($id);
       $query = Bill::query();
       $bills =  $query->where('patient_id',$id)->where('status', 'paid')->latest('created_at')->get();
       $total_dues = $query->sum('due_amount');
       $current_date = Carbon::now()->format('Y-m-d');
       $appointment = Appointment::where('patient_id',$id)->where('date',$current_date)->first();

       return view("patient.view",compact('user','bills','total_dues','appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $latest_regid = User::where('user_role', User::PATIENT)->latest()->first();

        if ($latest_regid) {
            $current_regid = $latest_regid->reg_no;
            $numeric_part = preg_replace('/\D/', '', $current_regid);
            $numeric_part = intval($numeric_part);
        }
        else{
            $numeric_part = 0;
        }
        $new_regid = $numeric_part + 1;
        $reg_no = "PMC" .str_pad($new_regid, 4, '0', STR_PAD_LEFT);

        $user = User::findOrFail($id);
        return view('patient.create',compact('user', 'reg_no'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            "contact_number" => $request->contact_number,
            "name" => $request->name,
            "reg_no" => $request->reg_no,
            "DOB" => $request->DOB,
            "gender" => $request->gender,
            "address" => $request->address,
            "medical_history" => $request->medical_history,
            "credit_due" => $request->credit_due,
            "credit_limit" => $request->credit_limit,
            "user_title" => $request->user_title

        ]);

        return redirect()->route('patients.index')->with('status',"Patient updated Successfully..!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
       $user = User::findOrFail($id);
       $user->delete();
       return redirect()->route('patients.index')->with('status',"Patient deleted Successfully..!");
    }
}
