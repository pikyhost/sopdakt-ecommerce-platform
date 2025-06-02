<?php

namespace App\Http\Controllers;

use Filament\Facades\Filament;
use Filament\Http\Controllers\Auth\LogoutController;
use Illuminate\Http\Request;


class FilamentLogoutController extends LogoutController
{
    public function logout(Request $request)
    {

        auth('web')->user()->userLoginToken()->updateOrCreate([
            'user_id' => auth('web')->id(),
        ], [
            'token' => null,
            'session_id' => null,
            'is_login' => false,
        ]);
        Filament::auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->away("https://sopdakt.com");

    }
}
