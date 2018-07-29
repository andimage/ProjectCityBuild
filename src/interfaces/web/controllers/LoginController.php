<?php

namespace Interfaces\Web\Controllers;

use Domains\Modules\Forums\Exceptions\BadSSOPayloadException;
use Domains\Modules\Accounts\Services\Login\AccountLoginService;
use Domains\Modules\Accounts\Services\Login\AccountSocialLoginExecutor;
use Domains\Library\Discourse\Api\DiscourseUserApi;
use Domains\Library\Discourse\Api\DiscourseAdminApi;
use Domains\Library\Discourse\Authentication\DiscoursePayloadValidator;
use Domains\Library\Discourse\Authentication\DiscoursePayload;
use Domains\Modules\Accounts\Repositories\AccountRepository;
use Domains\Modules\Accounts\Repositories\AccountLinkRepository;
use Domains\Services\Login\LogoutService;
use Domains\Library\OAuth\OAuthLoginHandler;
use Domains\Modules\Accounts\Exceptions\InvalidDiscoursePayloadException;
use Interfaces\Web\Requests\LoginRequest;
use Infrastructure\Environment;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Log\Logger;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Domains\Library\OAuth\Exceptions\UnsupportedOAuthAdapter;

class LoginController extends WebController
{
    /**
     * @var DiscoursePayloadValidator
     */
    private $discoursePayloadValidator;

    /**
     * @var DiscourseUserApi
     */
    private $discourseUserApi;

    /**
     * @var DiscourseAdminApi
     */
    private $discourseAdminApi;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var AccountLinkRepository
     */
    private $accountLinkRepository;

    /**
     * @var OAuthLoginHandler
     */
    private $oauthLoginHandler;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Logger
     */
    private $log;

    /**
     * @var LogoutService
     */
    private $logoutService;

    public function __construct(DiscoursePayloadValidator $discoursePayloadValidator,
                                DiscourseUserApi $discourseUserApi,
                                DiscourseAdminApi $discourseAdminApi,
                                AccountRepository $accountRepository,
                                AccountLinkRepository $accountLinkRepository,
                                OAuthLoginHandler $oauthLoginHandler,
                                Auth $auth,
                                Connection $connection,
                                Logger $logger,
                                LogoutService $logoutService
    ) {
        $this->discoursePayloadValidator = $discoursePayloadValidator;
        $this->discourseUserApi = $discourseUserApi;
        $this->discourseAdminApi = $discourseAdminApi;
        $this->accountRepository = $accountRepository;
        $this->accountLinkRepository = $accountLinkRepository;
        $this->oauthLoginHandler = $oauthLoginHandler;
        $this->auth = $auth;
        $this->connection = $connection;
        $this->log = $logger;
        $this->logoutService = $logoutService;
    }

    public function showLoginView(Request $request)
    {
        if ($this->auth->check()) {
            $this->log->debug('Already logged-in; redirecting...');
            return redirect()->route('front.home');
        }

        // login route should have a valid payload in the url
        // generated by discourse when being redirected here
        $sso        = $request->get('sso');
        $signature  = $request->get('sig');

        if ($sso === null || $signature === null) {
            return redirect()->route('front.home');
        }

        // validate that the given signature matches the
        // payload when signed with our private key. This
        // prevents any payload tampering
        $isValidPayload = $this->discoursePayloadValidator->isValidPayload($sso, $signature);
        if ($isValidPayload === false) {
            $this->log->debug('Received invalid SSO payload (sso: '.$sso.' | sig: '.$signature);
            abort(400);
        }

        // ensure that the payload has all the necessary
        // data required to create a new payload after
        // authentication
        $payload = null;
        try {
            $payload = $this->discoursePayloadValidator->unpackPayload($sso);
        } catch (BadSSOPayloadException $e) {
            $this->log->debug('Failed to unpack SSO payload (sso: '.$sso.' | sig: '.$signature);
            abort(400);
        }

        // store the nonce and return url in a session so
        // the user cannot access or tamper with it at any
        // point during authentication
        $request->session()->put([
            'discourse_nonce'   => $payload['nonce'],
            'discourse_return'  => $payload['return_sso_url'],
        ]);

        $this->log->debug('Storing SSO data in session for login');

        return view('front.pages.login.login');
    }

    /**
     * Manual login with email and password via form post
     *
     * @param LoginRequest $request
     * @return void
     */
    public function login(LoginRequest $request)
    {
        $session = $request->session();

        $nonce     = $session->get('discourse_nonce');
        $returnUrl = $session->get('discourse_return');

        if ($nonce === null || $returnUrl === null) {
            $this->log->debug('Missing nonce or return key in session...', ['session' => $session]);
            throw new InvalidDiscoursePayloadException('`nonce` or `return` key missing in session');
        }

        $request->validated();

        $account = $this->auth->user();
        if ($account === null) {
            throw new \Exception('Account was null after authentication');
        }

        $payload = (new DiscoursePayload($nonce))
            ->setPcbId($account->getKey())
            ->setEmail($account->email)
            ->requiresActivation(false)
            ->build();
        
        $session->remove('discourse_nonce');
        $session->remove('discourse_return');
    

        // generate new payload to send to discourse
        $payload    = $this->discoursePayloadValidator->makePayload($payload);
        $signature  = $this->discoursePayloadValidator->getSignedPayload($payload);

        // attach parameters to return url
        $endpoint   = $this->discoursePayloadValidator->getRedirectUrl($returnUrl, $payload, $signature);

        $this->log->info('Logging in user: '.$account->getKey());

        return redirect()->to($endpoint);
    }


    public function redirectToProvider(string $providerName)
    {
        try {
            $this->oauthLoginHandler->setProvider($providerName);
        } catch (UnsupportedOAuthAdapter $e) {
            abort(404);
        }

        $redirectUri = route('front.login.provider.callback', $providerName);

        return $this->oauthLoginHandler->redirectToLogin($redirectUri);
    }

    public function handleProviderCallback(string $providerName, Request $request)
    {
        try {
            $this->oauthLoginHandler->setProvider($providerName);
        } catch (UnsupportedOAuthAdapter $e) {
            abort(404);
        }

        if ($request->get('denied')) {
            return redirect()->route('front.home');
        }

        $session = $request->session();

        $nonce     = $session->get('discourse_nonce');
        $returnUrl = $session->get('discourse_return');

        if ($nonce === null || $returnUrl === null) {
            throw new InvalidDiscoursePayloadException('`nonce` or `return` key missing in session');
        }

        $providerAccount = $this->oauthLoginHandler->getOAuthUser();

        $existingLink = $this->accountLinkRepository->getByProviderAccount($providerName, $providerAccount->getId());

        if ($existingLink === null) {
            
            // if an account link doesn't exist, we need to
            // check that the email is not already in use
            // by a different account, because PCB and Discourse
            // accounts must have a unique email
            $existingAccount = $this->accountRepository->getByEmail($providerAccount->getEmail());
            if ($existingAccount !== null) {
                $this->log->debug('Account with email ('.$providerAccount->getEmail().') already exists; showing error to user');

                return view('front.pages.register.register-oauth-failed', [
                    'email' => $providerAccount->getEmail(),
                ]);
            }

            // otherwise send them to the register confirmation
            // view using their provider account data
            $url = URL::temporarySignedRoute(
                'front.login.social-register',
                                             now()->addMinutes(10),
                                             $providerAccount->toArray()
            );

            $this->log->debug('Generating OAuth register URL: '.$url);

            return view('front.pages.register.register-oauth', [
                'social' => $providerAccount->toArray(),
                'url'    => $url,
            ]);
        }

        // otherwise login the account linked to
        // the provider account's id
        if ($existingLink->account === null) {
            throw new \Exception('Account link is missing an account');
        }
        
        $account = $existingLink->account;

        $this->auth->setUser($account);

        $session->remove('discourse_nonce');
        $session->remove('discourse_return');

        $payload = (new DiscoursePayload($nonce))
            ->setPcbId($account->getKey())
            ->setEmail($account->email)
            ->requiresActivation(false)
            ->build();

    
        $payload    = $this->discoursePayloadValidator->makePayload($payload);
        $signature  = $this->discoursePayloadValidator->getSignedPayload($payload);

        $url = $this->discoursePayloadValidator->getRedirectUrl($returnUrl, $payload, $signature);

        $this->log->info('Logging in PCB user ('.$account->getKey().') via OAuth');
        $this->log->debug($payload);
        $this->log->debug($providerAccount->toArray());

        return redirect()->to($url);
    }

    public function createSocialAccount(Request $request)
    {
        $providerEmail = $request->get('email');
        $providerId    = $request->get('id');
        $providerName  = $request->get('provider');

        if ($providerEmail === null) {
            abort(400, 'Missing social email');
        }
        if ($providerId === null) {
            abort(400, 'Missing social id');
        }
        if ($providerName === null) {
            abort(400, 'Missing social provider name');
        }
        
        $session = $request->session();

        $nonce     = $session->get('discourse_nonce');
        $returnUrl = $session->get('discourse_return');

        if ($nonce === null || $returnUrl === null) {
            throw new InvalidDiscoursePayloadException('`nonce` or `return` key missing in session');
        }

        
        $accountLink = $this->accountLinkRepository->getByProviderAccount($providerName, $providerId);
        if ($accountLink !== null) {
            throw new \Exception('Attempting to create PCB account via OAuth, but OAuth account already exists');
        }

        $account = null;
        $this->connection->beginTransaction();
        try {
            // create a PCB account for the user
            $account = $this->accountRepository->create($providerEmail,
                                                        Hash::make(time()),
                                                        null,
                                                        Carbon::now());

            // and then create an account link to it
            $accountLink = $this->accountLinkRepository->create($account->getKey(),
                                                                $providerName,
                                                                $providerId,
                                                                $providerEmail);

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        if ($account === null) {
            throw new \Exception('Account is null after OAuth creation');
        }

        $payload = (new DiscoursePayload($nonce))
            ->setPcbId($account->getKey())
            ->setEmail($account->email)
            ->requiresActivation(false)
            ->build();
        
        $session->remove('discourse_nonce');
        $session->remove('discourse_return');
    

        // generate new payload to send to discourse
        $payload    = $this->discoursePayloadValidator->makePayload($payload);
        $signature  = $this->discoursePayloadValidator->getSignedPayload($payload);

        // attach parameters to return url
        $endpoint   = $this->discoursePayloadValidator->getRedirectUrl($returnUrl, $payload, $signature);

        return redirect()->to($endpoint);
    }

    /**
     * Logs out the current PCB account
     *
     * (called from Discourse)
     *
     * @param Request $request
     * @return void
     */
    public function logoutFromDiscourse(Request $request)
    {
        $this->logoutService->logoutOfPCB();

        return redirect()->route('front.home');
    }

    /**
     * Logs out the current PCB account and
     * its associated Discourse account
     *
     * (called from this site)
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        $this->logoutService->logoutOfDiscourseAndPcb();
        
        return redirect()->route('front.home');
    }
}
