<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return User::all();
    }

    /**
     * Create new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'Required|String',
            'email' => 'Required|String|Unique:users,email',
            'password' => 'Required|String|Confirmed'
        ]);

        $user = User::create(
            [
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password'])
            ]
        );

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request) {
        $fields = $request->validate(
            [
                'email' => 'Required|String',
                'password' => 'Required|String'
            ]
        );

        $user = User::where('email', $fields['email'])->first();

        if (!$user) return response(['messages'=>'There is no account with that email address. Please register a new account.'], 401);

        if (!Hash::check($fields['password'], $user['password'])) return response(['messages'=>'Unable to log in with these credentials.'], 401);

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return response(['messages' => 'You have successfully logged out.'], 200);
    }
}
