<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\SendEmail;
use App\Models\User;
use Hash;
use Auth;
use Carbon\Carbon;


class AuthController extends Controller
{
    use SendEmail;

    public function index(){
        $site_key = config('services.google_cloud.site_key'); 
        return view('auth.index',compact('site_key'));
    }

    public function login(Request $request){
        $request->validate([
            'username' => 'required|email',
            'password' => 'required'
        ]);
        
        $user = User::where('email', $request->username)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error_message' => 'Invalid credentials'
            ], 401);
        }

        $cooldown = 60;
        if ($user->otp_send_at &&
            Carbon::parse($user->otp_send_at)->diffInSeconds(now()) < $cooldown) {
            $remaining = ceil($cooldown - Carbon::parse($user->otp_send_at)->diffInSeconds(now()));
            return response()->json([
                'error_message' => "Please wait {$remaining}s before requesting OTP again"
            ], 429);
        }

        $user->otp = rand(100000,999999);
        $user->otp_send_at = now();
        $user->otp_expired_at = now()->addMinutes(5);
        $user->save();

        $result = view('email.otp', ['name' => $user->name, 'otp' => $user->otp])->render();
        $this->sendMail($user->email,'OTP Verification MORE', $result,'info@get-intouch.com');

        return response()->json(['message' => 'OTP has been sent to your email']);
    }

    public function verifyRecaptcha(Request $request){
        return response()->json(['message' => 'reCAPTCHA Success'], 200);
    }

    public function verifyOtp(Request $request){
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = User::where(['email' => $request->username, 'otp' => $request->otp])->firstOrFail();
        if (Carbon::parse($user->otp_expired_at)->isPast()) {
            return response()->json(['error_message' => 'OTP Expired!'],403);
        }
      
        Auth::loginUsingId($user->id);
        $request->session()->regenerate();

        return response()->json(['message' => $user->username],200);
    }



    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login.index');
    }


}
