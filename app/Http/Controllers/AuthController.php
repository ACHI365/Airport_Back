<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $fields = $request->validate([
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password),
            'is_email_validated' => false,
        ]);

        $verificationToken = bin2hex(random_bytes(32));
        $user->verification_token = $verificationToken;
        $user->save();

        $verificationUrl = url("/verify-email?token={$verificationToken}");

        Mail::send('emails.verify-email', ['verificationUrl' => $verificationUrl, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email)->subject('Verify Your Email');
        });

        return response()->json(['message' => 'User registered successfully. Check your email for verification.'], 201);
    }

    public function verifyEmail(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['token' => 'required']);


        $user = User::where('verification_token', $request->token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 400);
        }

        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Email verified successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 403);
        }

        if (empty($user->email_verified_at)) {
            return response()->json(['message' => 'Please verify your email before logging in.'], 403);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.'], 201);
    }
}
