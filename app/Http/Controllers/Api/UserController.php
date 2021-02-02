<?php

namespace App\Http\Controllers\API;
use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
class UserController extends Controller
{
    use PasswordValidationRules;

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
                'Registered Failed',
                500
            );
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, "Token Revoked");
    }

    public function updateUser(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $user->update($data);
        return ResponseFormatter::success($user, 'Profile Updated');
    }

    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user, '
        User data has been retrieved successfully');
    }

    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(['error'=>$validator->errors()], 'Update Photo Fails', 422);
        }

        if ($request->file('file')) {
            $file = $request->file->store('assets/user', 'public');
            $user = Auth::user();
            $user->profile_photo_path = $file;
            $user->update();
            return ResponseFormatter::success([$file],'File successfully uploaded');
        }
    }
    
}
