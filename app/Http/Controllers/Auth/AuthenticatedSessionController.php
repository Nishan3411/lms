<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Laravel\Fortify\Features;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        /** @var User $user */
        $user = Auth::user();

        if (
            Features::enabled(Features::twoFactorAuthentication())
            && $user?->two_factor_secret
            && (! Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm') || $user->two_factor_confirmed_at !== null)
        ) {
            Auth::logout();

            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => $request->boolean('remember'),
            ]);

            return redirect()->route('two-factor.login');
        }

        $request->session()->regenerate();

        return match ($user->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            'parent'  => redirect()->route('parent.dashboard'),
            default   => redirect('/'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
