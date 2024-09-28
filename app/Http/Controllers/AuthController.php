<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\ResetUserPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ResetUserPasswordRequest;

class AuthController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }
    public function login(LoginUserRequest $request)
    {
        $user = $this->user->where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => $user->role,
            'data' => $user,
        ]);
    }

    public function register(RegisterUserRequest $request)
    {
        $user = $this->user->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'age' => $request->age,
            'role' => $request->role ?? 'visitor',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => $user
        ]);
    }

    public function logout()
    {
        $user = Auth::user();
        
        if ($user) {

            $user->tokens()->delete();

            return response()->json([
                'message' => 'User logged out Successfully',
            ], 200);
        }

        return response()->json([
            'message' => 'No authenticated user found',
        ], 401);
    }

    public function sendResetLinkEmail(ForgetPasswordRequest $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $token = Str::random(60);

        $this->user->updateOrCreate(
            ['email' => $email],
            ['token' => $token]
        );

        $resetLink = url("password/reset/{$token}");

        Mail::to($email)->send(new ResetUserPasswordMail($resetLink));

        return response()->json(['message' => 'Reset link sent to your email.']);
    }

    public function resetPassword(ResetUserPasswordRequest $request)
    {
        $passwordReset = $this->user->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$passwordReset) {
            return response()->json(['error' => 'Invalid or expired reset token.'], 422);
        }

        $user = $this->user->where('email', $passwordReset->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->token = null;
        $user->save();

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
