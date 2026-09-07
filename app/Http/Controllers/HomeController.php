<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user && $user->isAdmin()) {
            $surveys = Survey::with(['activePeriod', 'responses'])->get();
        } else {
            $surveys = Survey::where('is_active', true)
                ->with(['activePeriod', 'responses'])
                ->get()
                ->filter(function ($survey) {
                    return $survey->isWithinActiveDate();
                });
        }

        return view('home.index', compact('surveys'));
    }
}
