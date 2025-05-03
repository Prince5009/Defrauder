<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        return view('feedback');
    }

    public function store(Request $request)
    {
        $request->validate([
            'suggestion' => 'required|string|min:10',
            'rating' => 'required|integer|between:1,5'
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'suggestion' => $request->suggestion,
            'rating' => $request->rating
        ]);

        return response()->json(['success' => true]);
    }
} 