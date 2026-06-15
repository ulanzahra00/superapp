<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $teachers = User::where('role', 'guru')
            ->forSchool($schoolId)
            ->orderBy('class_name')
            ->orderBy('name')
            ->get();
        $students = User::where('role', 'siswa')
            ->forSchool($schoolId)
            ->orderBy('class_name')
            ->orderBy('name')
            ->get();
        $parentsList = User::where('role', 'orang_tua')
            ->forSchool($schoolId)
            ->orderBy('name')
            ->get();

        return view('admin.users', [
            'users' => $teachers->concat($students)->concat($parentsList),
            'teachers' => $teachers,
            'students' => $students,
            'parentsList' => $parentsList,
            'parents' => $parentsList,
            'classes' => User::where('role', 'siswa')
                ->forSchool($schoolId)
                ->whereNotNull('class_name')
                ->distinct()
                ->orderBy('class_name')
                ->pluck('class_name'),
        ]);
    }

    public function store(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['guru', 'siswa', 'orang_tua'])],
            'nis' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'nis')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                }),
            ],
            'class_name' => ['nullable', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId)->where('role', 'orang_tua');
                }),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if ($data['role'] === 'siswa') {
            $request->validate([
                'nis' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users', 'nis')->where(function ($query) use ($schoolId) {
                        return $query->where('school_id', $schoolId);
                    }),
                ],
                'class_name' => ['required', 'string', 'max:255'],
            ]);
        }

        if ($data['role'] === 'orang_tua') {
            $data['nis'] = null;
            $data['class_name'] = null;
            $data['parent_id'] = null;
        }

        if ($data['role'] === 'guru') {
            $data['nis'] = null;
            $data['parent_id'] = null;
        }

        $data['password'] = Hash::make($data['password'] ?: 'password');
        $data['school_id'] = $schoolId;

        User::create($data);

        return redirect()->route('admin.users.index')->with('status', 'User baru berhasil ditambahkan.');
    }
}
