<?php


namespace App\Http\Controllers\Settings;


use Barryvdh\Debugbar\Controllers\BaseController;
use Illuminate\Http\Request;

final class AccountDonationController extends BaseController
{
    public function index(Request $request)
    {
        $donations = $request->user()->donations;
        return view('front.pages.account.account-donations')->with(compact('donations'));
    }
}
