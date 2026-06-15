<?php

namespace App\Http\Controllers;

use App\Models\School;

class AdminSchoolController extends Controller
{
    public function index()
    {
        return view('admin.schools', [
            'pendingSchools' => School::where('status', 'pending')
                ->with(['users' => function ($query) {
                    $query->where('role', 'admin')->orderBy('created_at');
                }])
                ->latest()
                ->get(),
            'activeSchools' => School::where('status', 'active')
                ->withCount('users')
                ->orderBy('name')
                ->get(),
            'inactiveSchools' => School::where('status', 'inactive')
                ->withCount('users')
                ->orderBy('name')
                ->get(),
            'rejectedSchools' => School::where('status', 'rejected')
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function approve(School $school)
    {
        abort_if($school->status !== 'pending', 422);

        $school->update(['status' => 'active']);

        return redirect()
            ->route('admin.schools.index')
            ->with('status', $school->name.' sudah disetujui dan dapat login.');
    }

    public function reject(School $school)
    {
        abort_if($school->status !== 'pending', 422);

        $school->update(['status' => 'rejected']);

        return redirect()
            ->route('admin.schools.index')
            ->with('status', $school->name.' sudah ditolak dan tidak dapat login.');
    }

    public function deactivate(School $school)
    {
        abort_if($school->status !== 'active', 422);

        $school->update(['status' => 'inactive']);

        return redirect()
            ->route('admin.schools.index')
            ->with('status', $school->name.' sudah dinonaktifkan. User sekolah tersebut tidak dapat login sampai diaktifkan kembali.');
    }

    public function reactivate(School $school)
    {
        abort_if($school->status !== 'inactive', 422);

        $school->update(['status' => 'active']);

        return redirect()
            ->route('admin.schools.index')
            ->with('status', $school->name.' sudah diaktifkan kembali.');
    }
}
