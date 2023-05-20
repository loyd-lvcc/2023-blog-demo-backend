<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request) {
        $username = $request->get('username');
        $password = $request->get('password');
        $fullname = $request->get('fullname');

        if (!$username || !$password || !$fullname) {
            return response()->json(['error' => 'Invalid request'], 401);
        }

        $user = User::create([
            'username' => $username,
            'password' =>  Hash::make($password),
            'fullname' => $fullname
        ]);

        return response()->json(['data' => $user]);
    }

    public function login(Request $request) {
        $username = $request->get('username');
        $password = $request->get('password');

        $userModel = User::where('username', $username)->first();
        if (!$userModel) {
            return response()->json(['error' => 'Wrong usernmae!']);
        }

        if (!Hash::check($password, $userModel->password)) {
            return response()->json(['error' => 'Wrong password!']);
        }

        $userModel->token = md5($userModel->id . date("Y/m/d h:i:s"));
        $userModel->save();

        return response()->json(['data' => $userModel]);
    }

    public function logout(Request $request) {
        $token = $request->bearerToken();
        
        $user = User::where('token', $token)->first();
        if (!$user) {
            return response()->json(['error' => 'Invalid request'], 401);
        }

        $user->token = null;
        $user->save();

        return response()->json(['data' => true]);
    }
}
