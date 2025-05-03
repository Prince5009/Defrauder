<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Feedback;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function login()
    {
        return view('admin.login');
    }

    public function dashboard()
    {
        // Check if admin is logged in
        if (!session()->has('adminLoggedIn')) {
            return redirect('/admin/login');
        }

        $totalUsers = User::count();
        $totalFeedback = Feedback::count();
        $averageRating = Feedback::avg('rating') ?? 0;

        $users = User::latest()->paginate(10);
        $feedback = Feedback::with('user')->latest()->paginate(10);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalFeedback',
            'averageRating',
            'users',
            'feedback'
        ));
    }

    public function authenticate(Request $request)
    {
        if ($request->email === 'onesolution123@gmail.com' && $request->password === 'OneSolution@UTU1') {
            session(['adminLoggedIn' => true]);
            return redirect('/admin/dashboard');
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        session()->forget('adminLoggedIn');
        return redirect('/admin/login');
    }
} 