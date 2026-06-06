<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }
            return redirect()->intended('/menu');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errCode = $_FILES['profile_image_file']['error'];
            if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
                $maxSize = ini_get('upload_max_filesize') ?: '15M';
                return back()->withErrors([
                    'profile_image_file' => "The uploaded profile image file is too large. Please select an image smaller than {$maxSize}."
                ])->withInput();
            } elseif ($errCode !== UPLOAD_ERR_OK) {
                return back()->withErrors([
                    'profile_image_file' => "The profile image failed to upload. Please try a different or smaller image."
                ])->withInput();
            }
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_image_url' => ['nullable', 'url', 'max:2048'],
            'profile_image_file' => ['bail', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:15360'],
        ], [
            'profile_image_file.max' => 'The uploaded profile image file is too large. Please select an image smaller than 15 Megabytes (15MB).',
            'profile_image_file.image' => 'The selected file must be a valid image format (JPEG, PNG, JPG, GIF, WebP).',
            'profile_image_file.mimes' => 'The selected file must be a valid image format (JPEG, PNG, JPG, GIF, WebP).',
        ]);

        $imagePath = null;

        if ($request->hasFile('profile_image_file')) {
            $file = $request->file('profile_image_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $filename);
            $imagePath = '/uploads/profile/' . $filename;
        } elseif ($request->filled('profile_image_url')) {
            $imagePath = $request->profile_image_url;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', // Default role
            'profile_image' => $imagePath,
        ]);

        Auth::login($user);

        return redirect('/menu')->with('success', 'Welcome to Clara’s Best!');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Successfully logged out.');
    }

    /**
     * Show general settings for staff and customers
     */
    public function settingsShow()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.settings');
        }
        return view('settings', compact('user'));
    }

    /**
     * Update generic profile details including photo uploads
     */
    public function settingsUpdateProfile(Request $request)
    {
        $user = auth()->user();

        if (isset($_FILES['profile_image_file']) && $_FILES['profile_image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errCode = $_FILES['profile_image_file']['error'];
            if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
                $maxSize = ini_get('upload_max_filesize') ?: '15M';
                return back()->withErrors([
                    'profile_image_file' => "The uploaded profile image file is too large. Please select an image smaller than {$maxSize}."
                ])->withInput();
            } elseif ($errCode !== UPLOAD_ERR_OK) {
                return back()->withErrors([
                    'profile_image_file' => "The profile image failed to upload. Please try a different or smaller image."
                ])->withInput();
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_image_url' => 'nullable|url|max:2048',
            'profile_image_file' => 'bail|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:15360',
        ], [
            'profile_image_file.max' => 'The uploaded profile image file is too large. Please select an image smaller than 15 Megabytes (15MB).',
            'profile_image_file.image' => 'The selected file must be a valid image format (JPEG, PNG, JPG, GIF, WebP).',
            'profile_image_file.mimes' => 'The selected file must be a valid image format (JPEG, PNG, JPG, GIF, WebP).',
        ]);

        $imagePath = $user->profile_image;

        if ($request->hasFile('profile_image_file')) {
            $file = $request->file('profile_image_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $filename);
            $imagePath = '/uploads/profile/' . $filename;
        } elseif ($request->filled('profile_image_url')) {
            $imagePath = $request->profile_image_url;
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'profile_image' => $imagePath,
        ]);

        return redirect()->route('settings')->with('success', 'Profile and avatar updated successfully!');
    }

    /**
     * Update secure passwords
     */
    public function settingsUpdatePassword(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('settings')->with('success', 'Password changed successfully!');
    }
}
