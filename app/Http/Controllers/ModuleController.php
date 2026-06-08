<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Message;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function attendance()
    {
        return view('modules.attendance', [
            'items' => Attendance::latest('date')->take(20)->get(),
        ]);
    }

    public function lms()
    {
        return view('modules.lms', [
            'courses' => Course::latest()->get(),
        ]);
    }

    public function grades()
    {
        return view('modules.grades', [
            'grades' => Grade::latest()->take(20)->get(),
        ]);
    }

    public function communication(Request $request)
    {
        return view('modules.communication', [
            'messages' => Message::latest()->take(20)->get(),
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load(['children', 'studentPoints', 'sanctions']);

        return view('profile', compact('user'));
    }
}
