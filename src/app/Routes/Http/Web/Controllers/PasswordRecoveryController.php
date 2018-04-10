<?php

namespace App\Routes\Http\Web\Controllers;

use App\Modules\Discourse\Services\Authentication\DiscourseAuthService;
use App\Modules\Forums\Exceptions\BadSSOPayloadException;
use App\Modules\Accounts\Services\AccountSocialLinkService;
use App\Modules\Accounts\Repositories\AccountRepository;
use App\Modules\Accounts\Repositories\AccountPasswordResetRepository;
use App\Modules\Accounts\Notifications\AccountPasswordResetNotification;
use App\Modules\Accounts\Notifications\AccountPasswordResetCompleteNotification;
use App\Routes\Http\Web\WebController;
use Illuminate\Validation\Factory as Validation;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Database\Connection;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;
use Hash;

class PasswordRecoveryController extends WebController {

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var AccountPasswordResetRepository
     */
    private $passwordResetRepository;

    /**
     * @var Connection
     */
    private $connection;


    public function __construct(
        AccountRepository $accountRepository, 
        AccountPasswordResetRepository $passwordResetRepository,
        Connection $connection
    ) {
        $this->accountRepository = $accountRepository;
        $this->passwordResetRepository = $passwordResetRepository;
        $this->connection = $connection;
    }

    public function showEmailForm(Request $request) {
        return view('password-reset');
    }

    public function sendVerificationEmail(Request $request, Factory $validation) {
        $validator = $validation->make($request->all(), [
            'email' => 'email|required',
        ]);

        $email = $request->get('email');
        $account = null;
        $validator->after(function($validator) use(&$account, $email) {
            $account = $this->accountRepository->getByEmail($email);

            if($account === null) {
                $validator->errors()->add('email', 'No account belongs to the given email address');
            }
        });

        if($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $token = hash_hmc('sha256', time(), env('APP_KEY'));
        $passwordReset = $this->passwordResetRepository->updateOrCreateByEmail($email, $token);

        $account->notify(new AccountPasswordResetNotification($passwordReset));

        return redirect()
            ->back()
            ->with(['success' => $email]);
    }

    public function showResetForm(Request $request) {
        // make sure a token was provided
        $token = $request->get('token');
        if($token === null) {
            abort(401, "No token provided");
        }

        $passwordReset = $this->passwordResetRepository->getByToken($token);
        if($passwordReset === null) {
            abort(401, "Token invalid or has expired");
        }

        return view('password-reset-form', [
            'passwordToken' => $request->get('token')
        ]);
    }

    public function resetPassword(Request $request, Factory $validation) {
        $validator = $validation->make($request->all(), [
            'password_token'    => 'required',
            'password'          => 'required|min:4',
            'password_confirm'  => 'required_with:password|same:password',
        ]);

        if($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $passwordReset = $this->passwordResetRepository->getByToken($request->get('password_token'));
        if($passwordReset === null) {
            return redirect()
                ->route('front.password-reset')
                ->withErrors('error', 'Password tokekn not found');
        }

        $account = $this->accountRepository->getByEmail($passwordReset->email);
        if($account === null) {
            return redirect()
                ->route('front.password-reset')
                ->withErrors('error', 'Account not found');
        }

        $this->connection->beginTransaction();
        try {
            $account->password = Hash::make($request->get('password'));
            $account->save();

            $passwordReset->delete();
            $this->connection->commit();

        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        $account->notify(new AccountPasswordResetCompleteNotification);

        return view('password-reset-success');
    }



}