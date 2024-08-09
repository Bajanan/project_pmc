<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LogDetails;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $redirectTo = '/dashboard';

   
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {

        LogDetails::create([
            'user_id' => $user->id,
            'logged_in' => now()
        ]);

        return redirect('/dashboard');
       
    }

    public function logOut(Request $request)
    {
        $user = Auth::user();
       
        if(Auth::check()){
       $log_detail =  LogDetails::where('user_id', $user->id)->latest()->first();
        
       $log_detail->update([
        'logged_out' => now()
       ]);

       Auth::logout();
       $request->session()->invalidate();
    }
       
       return redirect('/');

        // $user = Auth::user();
        // $user->last_logout_at = now();
        // $user->save();
       
        // return redirect('/');
    }
}
