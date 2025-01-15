<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utility;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            if ($request->user()->hasVerifiedEmail()) {
                return redirect()->intended(RouteServiceProvider::HOME);
            }
            Utility::getSMTPDetails(1);

            $request->user()->sendEmailVerificationNotification();
        } catch (\Throwable $th) {
            return back()->with('status', 'verification-link-not-sent');
        }
        return back()->with('status', 'verification-link-sent');
    }
}
