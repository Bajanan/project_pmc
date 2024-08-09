<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $doctors = User::where('user_role', User::DOCTOR)->get();

        $patients = User::where('user_role', User::PATIENT)->get();

        $patientId = $request->input('patient_id');

        return view('appointment.create', compact('doctors', 'patients', 'patientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'doctor' => 'required',
            'patient' => 'required',
            'shift' => 'required',
            'date' => 'required',
            'time' => 'required',
            'token_number' => 'required'
        ]);
        $time = Carbon::parse($request->time)->format('h:i A');
        // dd ($time);
        Appointment::create([
            'doctor_id' => $request->doctor,
            'patient_id' => $request->patient,
            'shift' => $request->shift,
            'date' => $request->date,
            'time' => $time,
            'token_number' => $request->token_number,
            'status' => Appointment::PENDING
        ]);

        return back()->with('status', "Appointment created Successfully.!");
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'status' =>  Appointment::CANCELLED
        ]);
        return back()->with('status', "Appointment cancelled Successfully.!");
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function checkAvailability(Request $request)
    {

        //check whether appointments exists
        $availability = Appointment::where('doctor_id', $request->doctor)->where('shift', $request->shift)->where('date', $request->date)->latest()->first();
        $availability_all_data = Appointment::where('doctor_id', $request->doctor)->where('shift', $request->shift)->where('date', $request->date)->get();
        if ($availability) {
            //generate next appointment time
            $time =  Carbon::parse($availability->time);
            $next_time = Carbon::parse($time)->addMinutes(10)->format('H:i');
            //generate next token no
            $token_no = $availability->token_number;
            $token_no = $token_no + 1;
            return response()->json([
                "all_appointments" => $availability_all_data,
                "next_token_no" => $token_no,
                "doctor" => $availability->doctor->name,
                'doctor_id' => $availability->doctor_id,
                "shift" => $availability->shift,
                "date" => $availability->date,
                "time" => $next_time


            ]);
        } else {
            return response()->json(["next_token_no" => "1"]);
        }
    }

    public function reschedule(Request $request)
    {
        try {
            $old_schedules = Appointment::where('doctor_id', $request->Doctor)
                                        ->where('shift', $request->Shift)
                                        ->where('date', $request->Date)
                                        ->get();

            $newTime = Carbon::parse($request->newTime);
            $newHour = $newTime->hour;
            $newMinute = $newTime->minute;

            $interval = 10; // interval in minutes

            foreach ($old_schedules as $index => $schedule) {
                $newAppointmentTime = Carbon::parse($request->newDate . ' ' . $newHour . ':' . $newMinute)
                    ->addMinutes($index * $interval);

                $formattedTime = $newAppointmentTime->format('h:i A');

                $schedule->update([
                    'time' => $formattedTime,
                    'shift' => $request->newShift,
                    'date' => $request->newDate
                ]);
            }

            return response()->json(["success" => "Appointments rescheduled successfully."]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }

    public function cancelSchedule(Request $request){

        $schedules = Appointment::where('doctor_id', $request->Doctor)->where('shift', $request->Shift)->where('date', $request->Date)->get();
        foreach($schedules as $schedule){
            $schedule->update([
                'status'=>Appointment::CANCELLED
            ]);
        }
        return response()->json("Success");
    }

    public function newPatient(Request $request)
    {
        // dd($request->all());
        $request->merge(['user_role' => User::PATIENT]);
        $patient =  User::create($request->all());

        return redirect()->route('appointments.create');
    }

    public function events()
    {
        $appointments = Appointment::select('doctor_id', 'date', 'shift', DB::raw('MIN(DATE_FORMAT(STR_TO_DATE(time, "%h:%i %p"), "%H:%i:%s")) as start_time'), DB::raw('MAX(DATE_FORMAT(STR_TO_DATE(time, "%h:%i %p"), "%H:%i:%s")) as end_time'), DB::raw('COUNT(*) as total_appointments'))
            ->wherenot('status', 'Cancelled')
            ->groupBy('doctor_id', 'date', 'shift')
            ->get();

        $events = [];

        foreach ($appointments as $appointment) {

            $formatted_end_time = date('H:i:s', strtotime($appointment->end_time . ' + 10 minutes'));

            $doctorName = $appointment->doctor->name;

            $startTime = $appointment->date . 'T' . $appointment->start_time;
            $endTime = $appointment->date . 'T' . $formatted_end_time;
            $totalAppointments = $appointment->total_appointments;
            $shift = $appointment->shift;

            $title = $doctorName . ' (' . $totalAppointments . ')';

            $events[] = [
                'title' => $title,
                'start' => $startTime,
                'end' => $endTime,
                'doctor_id' => $appointment->doctor_id,
                'adate' => $appointment->date,
                'shift' => $shift
            ];
        }

        return response()->json($events);
    }
}
