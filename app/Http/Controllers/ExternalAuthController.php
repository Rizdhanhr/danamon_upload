<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Auth;
use Hash;

class ExternalAuthController extends Controller
{
    public function handle(Request $request){

   
        $username = $request->query('user');
        $timestamp = $request->query('timestamp');
        $encodedSignature = $request->query('sign');

        if (!$username || !$timestamp || !$encodedSignature) {
            abort(400, 'Invalid request');
        }

     
        if (abs(now()->timestamp - (int)$timestamp) > 300) {
            abort(403, 'Timestamp expired');
        }

      
        $signature =   base64_decode(strtr($encodedSignature, '-_', '+/'));
        $publicKey = config('services.sso.public_key');
        $data = $username.$timestamp;
        $result = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        if ($result !== 1) {
            abort(403, 'Invalid Signature');
        }
        

        $email = $username.'@mail.com';
        $role = Role::where('is_default_sso',1)->first();

        if (!$role) {
            abort(403, 'SSO Not Configured');
        }
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $username,
                'provider' => 'sso',
                'role_id' => $role->id
            ]
        );


        Auth::login($user);

        return redirect()->route('dashboard.index');
       
    }

}
