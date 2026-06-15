<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function loginForm(?string $schoolSlug = null)
    {
        $selectedSchool = $schoolSlug ? $this->schoolFromSlug($schoolSlug) : null;

        return view('auth.login', [
            'schools' => School::where('status', 'active')->orderBy('name')->get(),
            'selectedSchool' => $selectedSchool,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'school_id' => [
                'required',
                'integer',
                Rule::exists('schools', 'id')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $schoolId = $credentials['school_id'];
        unset($credentials['school_id']);

        if (Auth::attempt(array_merge($credentials, ['school_id' => $schoolId]), false)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['email' => 'Sekolah, email, atau password tidak sesuai.'])
            ->onlyInput('school_id', 'email');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'npsn' => ['required', 'string', 'max:50', 'unique:schools,npsn'],
            'address' => ['required', 'string', 'max:1000'],
            'phone' => ['required', 'string', 'max:30'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        DB::transaction(function () use ($data) {
            $baseSlug = School::publicSlugFromName($data['school_name']);
            $slug = $baseSlug;
            $suffix = 2;

            while (School::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$suffix;
                $suffix++;
            }

            $school = School::create([
                'name' => $data['school_name'],
                'slug' => $slug,
                'npsn' => $data['npsn'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'email' => $data['admin_email'],
                'status' => 'pending',
            ]);

            return User::create([
                'school_id' => $school->id,
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'role' => 'admin',
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
            ]);
        });

        return redirect()
            ->route('login')
            ->with('status', 'Pendaftaran sekolah berhasil dikirim. Akun admin baru bisa login setelah sekolah disetujui admin pusat.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function schoolFromSlug(string $schoolSlug): School
    {
        $inactiveSchool = School::where('slug', $schoolSlug)->where('status', 'inactive')->first()
            ?: School::where('status', 'inactive')->get()->first(function (School $school) use ($schoolSlug) {
                return $school->public_slug === $schoolSlug;
            });

        if ($inactiveSchool) {
            abort(response()->view('public.school-maintenance', [
                'publicSchool' => $inactiveSchool,
                'schoolQuery' => ['schoolSlug' => $inactiveSchool->public_slug],
            ], 503));
        }

        $school = School::where('slug', $schoolSlug)->where('status', 'active')->first();

        if ($school) {
            return $school;
        }

        $school = School::where('status', 'active')->get()->first(function (School $school) use ($schoolSlug) {
            return $school->public_slug === $schoolSlug;
        });

        abort_unless($school, 404);

        return $school;
    }
}
