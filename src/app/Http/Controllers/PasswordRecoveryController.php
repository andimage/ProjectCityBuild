<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordEmailRequest;
use App\Http\WebController;
use App\Http\Actions\AccountPasswordReset\SendPasswordResetEmail;
use App\Http\Actions\AccountPasswordReset\ResetAccountPassword;
use App\Exceptions\Http\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;

final class PasswordRecoveryController extends WebController
{
    public function showEmailForm()
    {
        return view('front.pages.password-reset.password-reset');
    }

    public function sendVerificationEmail(SendPasswordEmailRequest $request, SendPasswordResetEmail $sendPasswordResetEmail)
    {
        $input = $request->validated();
        $email = $input['email'];

        $sendPasswordResetEmail->execute(
            $request->getAccount(),
            $email
        );

        return redirect()->back()->with(['success' => $email]);
    }

    public function showResetForm(Request $request)
    {
        $token = $request->get('token');
        
        if ($token === null) {
            return redirect()
                ->route('front.password-reset')
                ->withErrors('error', 'Invalid URL. Please try again');
        }

        $passwordReset = PasswordReset::where('token', $token)->first();
        if ($passwordReset === null) {
            return redirect()
                ->route('front.password-reset')
                ->withErrors('error', 'URL is invalid or has expired. Please try again');
        }

        return view('front.pages.password-reset.password-reset-form', [
            'passwordToken' => $token
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request, ResetAccountPassword $resetAccountPassword)
    {
        $input = $request->validated();

        try {
            $resetAccountPassword->execute(
                $input['password_token'],
                $input['password']
            );
        } catch(NotFoundException $e) {
            return redirect()
                ->route('front.password-reset')
                ->withErrors('error', $e->getMessage());
        }
        
        return view('front.pages.password-reset.password-reset-success');
    }
}
