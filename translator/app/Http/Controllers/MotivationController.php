<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\MotivationEmail;

class MotivationController extends Controller
{
    public function sendMotivationEmail(Request $request)
    {
        try {
            Log::info('Starting motivation email process', ['request_data' => $request->all()]);
            
            $userId = $request->input('user_id');
            Log::info('Looking for user', ['user_id' => $userId]);
            
            $user = User::find($userId);
            if (!$user) {
                Log::error('User not found for motivation email', ['user_id' => $userId]);
                return response()->json(['error' => 'User not found'], 404);
            }
            
            Log::info('User found', ['user_id' => $userId, 'email' => $user->email]);

            // Check if user is active
            $isActive = UserActivity::isUserActive($userId);

            if ($isActive) {
                // User is active, no need to send email
                return response()->json(['message' => 'User is active, no email sent']);
            }

            // Get a random motivational message
            $messages = [
                "Hey {$user->name}! Time to get back to work! 🚀",
                "Don't let your dreams be dreams, {$user->name}! 💪",
                "The only way to do great work is to love what you do, {$user->name}! ❤️",
                "Every moment is a fresh beginning, {$user->name}! 🌟",
                "Success is not final, failure is not fatal: it is the courage to continue that counts, {$user->name}! 🎯"
            ];

            $randomMessage = $messages[array_rand($messages)];
            Log::info('Selected message', ['message' => $randomMessage]);

            // Prepare email data
            $emailData = [
                'user' => $user,
                'message' => $randomMessage,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'last_active' => UserActivity::where('user_id', $userId)
                    ->latest('last_activity_at')
                    ->value('last_activity_at')
            ];

            Log::info('Attempting to send email', [
                'to' => $user->email,
                'data' => $emailData
            ]);

            // Send the email
            Mail::to($user->email)->send(new MotivationEmail($emailData));

            Log::info('Email sent successfully', [
                'user_id' => $userId,
                'email' => $user->email
            ]);

            return response()->json([
                'message' => 'Motivation email sent successfully',
                'email' => $user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending motivation email: ' . $e->getMessage(), [
                'user_id' => $userId ?? 'unknown',
                'error' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }
} 