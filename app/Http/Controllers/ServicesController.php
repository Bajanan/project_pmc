<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::withTrashed()->get();
        return view('services.index',compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'service_type' => 'required',
            'description' => 'required',
            'unit_price' => 'required'
        ]);

        Service::create($request->all());
        return back()->with('status', "Service added Successfully..!");
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
        $services = Service::all();
        $service =  Service::findOrFail($id);
        return view('services.index', compact('service', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'service_type' => 'required',
            'description' => 'required',
            'unit_price' => 'required'
        ]);
        $service = Service::findOrFail($id);
        $service->update([
            'service_type' => $request->service_type,
            'description' =>  $request->description,
            'unit_price' =>  $request->unit_price
        ]);

        return redirect()->route('services.create')->with('status', "Service updated Successfully..!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return redirect()->route('services.create')->with('status', "Service Deleted Successfully..!");
    }

    public function restore(string $id){

        $service = Service::where('id',$id)->onlyTrashed()->first();
        $service->restore();
        return redirect()->route('services.create')->with('status', "Service restore Successfully..!");
    }
}
