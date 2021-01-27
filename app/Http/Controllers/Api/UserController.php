<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //

    public function login(Request $request)
    {

        try {
            $request->validate([
                'email' =>
                'email|required',
                'password' => 'required'
            ]);

            //Checkin Credentials
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error(
                    ['message' =>
                    'Unauthorized'],
                    'Authentication failed',
                    500
                );
            }

            //if user has pass credentials
            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception("Invalid Credentials");
            } else {
                $tokenResult = $user->createToken('authToken')->plainTextToken;
                return ResponseFormatter::success([
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user
                ], 'Authenticated');
            }
        } catch (Exception $exception) {
            return ResponseFormatter::error(
                [
                    'message' =>
                    'Something went wrong',
                    'error' => $exception
                ],
                'Authentication failed',
                500
            );
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'string|required|max:255',
                'email' => 'string|required|max:255|email|unique:users',
                'password' => $this->passwordRules()
            ]);

            User::Create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'phoneNumber' => $request->phoneNumber,
                'houseNumber' => $request->houseNumber,
                'city' => $request->city,
                'password' => Hash::make($request->password)
            ]);

            $user = User::where('email', $request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Successfully Registered');
            
        } catch (Exception $exception) {
            return ResponseFormatter::error(
                [
                    'message' =>
                    'Something went wrong',
                    'error' => $exception
                ],
                'Authentication failed',
                500
            );
        }
    }

    public function logout(Request $request){
         $token=$request->user()->currentAccessToken()->delete();
         return ResponseFormatter::success($token,"Token Revoked");
    }
}
