<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function login(Request $request): JsonResponse
    {

        if (!$request->email || !$request->password) {
            return $this->sendError('Error validation', ['email and password fields are required.'],422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();
            $result['token'] = $authUser->createToken(
                $authUser->name,
                [],
                Carbon::now()->addHour() // expiración del token en 1 hora
            )
            ->plainTextToken;
            $result['name'] = $authUser->name;

            return $this->sendResponse($result, 'User signed in');
        }
        else {
            return $this->sendError('Unauthorised.', ['error'=>'Email/Password incorrecto'],401);
        }
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors()->toArray(),422);
        }

        try {
            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            $user = User::create($input);
            $result['token'] =  $user->createToken($user->name)->plainTextToken;
            $result['name'] =  $user->name;

            return $this->sendResponse($result, 'User created successfully.');
        } catch(\Exception $e) {
            return $this->sendError('Registration Error' , [$e->getMessage()],500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        // Get user who requested the logout
        $user = Auth::user();
        // Revoke current user token
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        $success['name'] =  $user->name;
        // return response()->json(['message' => 'User successfully signed out']);
        return $this->sendResponse($success, 'User successfully signed out.');
    }
}
