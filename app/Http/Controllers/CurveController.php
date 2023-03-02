<?php

namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CurveController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        $user = $request->user();
        $latestEmail = $user->inboundEmails()->where('to', 'LIKE', $user->uuid . '%')->latest()->first();

        return Inertia::render('Curve', [
            'subscribed' => $user->subscribed('default'),
            'hasExpiredTrial' => $user->hasExpiredTrial('default'),
            'onTrial' => $user->onTrial('default'),
            'trialEndsAt' => Carbon::parse($user->trialEndsAt('default'))->diffForHumans(),
            'confirmationCode' => $latestEmail->confirmation_code ?? null,
            'confirmationUrl' => $latestEmail->confirmation_url ?? null,
            'uuid' => $user->uuid,
        ]);
    }

    public function store(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $stripeCheckoutUrl = $request->user()
            ->newSubscription('default', 'price_1Lo7RBB5W9VYy4wynQ2ern7o')
            ->trialDays(8)
            ->checkout([
                'success_url' => route('plans', ['checkout' => 'success']),
                'cancel_url' => route('plans', ['checkout' => 'cancelled']),
            ])->redirect()->getTargetUrl();

        return Inertia::location($stripeCheckoutUrl);
    }
}
