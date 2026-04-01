<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Hash;

class ProfileController extends Controller
{
    public function index(){
        return view('profile.index');
    }

    
    public function update(Request $request){
        $request->validate([
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);

      

        $user = User::where('id',Auth::user()->id)->firstOrFail();

        if($user->provider != 'local'){
            return redirect()->route('profile.index')->with('error', 'Only local user can change password');
        }
        
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Data Saved');
    }
}
