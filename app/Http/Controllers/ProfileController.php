<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.profile.index');
        }

        $student = $user->student;
        $teacher = $user->teacher;

        $isStudent = $student !== null;
        $profile = $isStudent ? $student : $teacher;

        $stats = [
            'classes_count' => 0,
            'secondary_count' => 0,
            'tertiary_count' => 0,
        ];

        $classes = collect();

        if ($isStudent && $student) {
            $student->load(['schoolClass']);
            $classes = $student->joinedClasses()
                ->with(['subject', 'schoolClass', 'teacher.user'])
                ->get();

            $stats = [
                'classes_count' => $classes->count(),
                'submissions_count' => $student->submissions()->count(),
                'grades_count' => $student->grades()->count(),
            ];
        } elseif ($teacher) {
            $teacher->load(['homeroomClasses']);
            $classes = $teacher->classSubjectTeachers()
                ->with(['subject', 'schoolClass'])
                ->withCount('joinedStudents')
                ->get();

            $cstIds = $classes->pluck('cst_id');
            $stats = [
                'classes_count' => $classes->count(),
                'students_count' => $classes->sum('joined_students_count'),
                'assignments_count' => \App\Models\Assignment::whereIn('cst_id', $cstIds)->count(),
            ];
        }

        return view('profile.edit', compact(
            'user',
            'profile',
            'isStudent',
            'stats',
            'classes'
        ));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Hanya administrator yang memiliki hak akses untuk mengedit informasi akun
        if (!$user->isAdmin()) {
            return Redirect::route('profile.edit')
                ->with('error', 'Pembaruan informasi akun hanya dapat dilakukan oleh Administrator Sekolah.');
        }

        $validated = $request->validated();

        // Handle photo removal or upload
        if ($request->boolean('remove_photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = null;
        } elseif ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->photo = $path;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Penghapusan akun resmi hanya dapat dilakukan oleh Administrator
        if (!$user->isAdmin()) {
            return Redirect::route('profile.edit')
                ->with('error', 'Penghapusan atau penonaktifan akun hanya dapat dilakukan oleh Administrator Sekolah.');
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
