<?php

namespace App\Http\Controllers;

use App\Enums\ApiCredentialType;
use App\Enums\BudgetType;
use App\Enums\SourceType;
use App\Jobs\FetchTransactions;
use App\Models\ApiCredential;
use App\Models\ConnectionLink;
use App\Models\Country;
use App\Models\Destination;
use App\Models\Source;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ConnectController extends Controller
{
    public function sources(Request $request): \Inertia\Response
    {
        $user = $request->user();
        $destinations = Destination::select('id', 'name', 'account_name AS subtitle', 'type')->where('user_id', $user->id)->where('active', true)->whereNotNull('name')->orderBy('created_at', 'desc')->get();
        $sources = Source::where('user_id', $user->id)->whereNotNull('name')->whereNotNull('identifier')->orderBy('created_at', 'desc')->with('connectionLink')->get();
        $countries = Country::select('name', 'flag AS icon', 'iso_3166_2 AS key')
            ->whereIn('iso_3166_2', array('AT', 'BE', 'DK', 'EE', 'FI', 'FR', 'DE', 'IE', 'IT', 'LV', 'LT', 'NL', 'NO', 'PL', 'PT', 'ES', 'SE', 'GB'))->get();

        foreach($countries as $country) {
            $country->icon = '../assets/flags/' . $country->icon;
        }

        foreach($destinations as $destination) {
            $destination->icon = '../assets/icons/' . strtolower(BudgetType::fromValue($destination->type)->description) . '.png';
            $destination->title = $destination->name;
        }

        foreach($sources as $source) {
            if($source->active){
                // for each source, use connection link ID to get the destination name
                if($source->connectionLink) {
                    // add destination if it exists
                    $destination = $destinations->where('id', $source->connectionLink->destination_id)->first();
                    $source->destination_type = $destination->title;
                    $source->destination_name = $destination->name;
                    $source->destination_account = $destination->subtitle;
                } else {
                    // set source to inactive if no connection link exists
                    $source->active = false;
                    $source->save();
                }
            }
        }

        return Inertia::render('Sources', [
            'sources' => $sources,
            'activeBudgets' => $destinations,
            'countries' => $countries,
            'source_types' => SourceType::asArray(),
            'app_url' => env('APP_URL'),
        ]);
    }

    public function budgets(Request $request): \Inertia\Response
    {
        $user = $request->user();
        $destinations = Destination::where('user_id', $user->id)->where('active', true)->orderBy('created_at', 'desc')->get();
        $connectionLinks = ConnectionLink::where('user_id', $user->id)->where('active', true)->orderBy('created_at', 'desc')->get();

        return Inertia::render('Budgets', [
            'budgets' => $destinations,
            'connection_links' => $connectionLinks,
            'budget_types' => BudgetType::asArray(),
            'app_url' => env('APP_URL'),
        ]);
    }

    public function connectBudgetOauth($budgetType): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $ynab = new YnabController();
        $response = $ynab->getOauthUrl();

        // handle error
        if($response->original['status'] == 'error') {
            Log::error('Failed to get YNAB OAuth URL.', ['response' => $response]);
            return response(['status' => 'error', 'statusTitle' => 'Error', 'Failed to get YNAB OAuth URL.'], 500);
        }

        return response(['url' => $response->original['url']], 200);
    }

    public function connectSourceOauth(Request $request, $sourceType)
    {
        $tink = new TinkController();
        $country = $request->country;

        // Validate country: check that it is a valid ISO 3166-2 code
        if(!Country::where('iso_3166_2', $country)->exists()) {
            Log::error('Invalid country code.', ['country' => $country]);
            return response(['status' => 'error', 'statusMessage' => 'Invalid country code.'], 401);
        }

        if($sourceType == SourceType::Tink) {
            // create Tink user if it doesn't exist
            $response = $tink->createUser($request->user(), $country);

            // handle error
            if($response->original['status'] == 'error') {
                Log::error($response->original['statusMessage'], ['response' => $response]);
                return response(['status' => $response->original['status'], 'statusTitle' => $response->original['statusTitle'], 'statusMessage' => $response->original['statusMessage']], $response->getStatusCode());
            }

            // generate access token and get Tink OAuth URL
            $response = $tink->getOauthUrl($request->user(), $country);

            // handle error
            if($response->original['status'] == 'error') {
                Log::error($response->original['statusMessage'], ['response' => $response]);
                return response(['status' => $response->original['status'], 'statusTitle' => $response->original['statusTitle'], 'statusMessage' => $response->original['statusMessage']], $response->getStatusCode());
            }

            return response(['url' => $response->original['statusMessage']], 200);
        }

        Log::error('Invalid request.', ['request' => $request]);
        return response(['status' => 'error', 'statusMessage' => 'Invalid request.'], 400);
    }


    public function connectSourceCallback(Request $request, $sourceType): \Illuminate\Http\RedirectResponse
    {
        if($sourceType == SourceType::Tink) {
            // check if Tink returned an error
            if($request->query('error') != null) {
                if($request->query('error') == 'USER_CANCELLED') {
                    return redirect()->route('connect.sources')->with([
                        'message' => [
                            'statusTitle' => 'Cancelled',
                            'statusMessage' => 'Authentication cancelled by user.',
                            'status' => 'warning'
                        ]
                    ]);
                }

                // unhandled error
                Log::error('Unhandled Tink callback error occurred.', ['error_code' => $request->query('error')]);
                return redirect()->route('connect.sources')->with([
                    'message' => [
                        'statusTitle' => 'Error',
                        'statusMessage' => $request->query('error'),
                        'status' => 'error'
                    ]
                ]);
            }

            $tink = new TinkController();

            $credentialsId = $request->query('credentialsId');
            $apiCredential = ApiCredential::where('user_id', $request->user()->id)->where('source_type', SourceType::Tink)->first();
            $apiCredential->identifier2 = $credentialsId;
            $apiCredential->save();

            // get access token
            $response = $tink->getAccessToken($request->user(), $apiCredential);

            // handle error
            if($response->original['status'] == 'error') {
                Log::error($response->original['statusMessage'], ['response' => $response]);
                return redirect()->route('connect.sources')->with([
                    'message' => [
                        'statusTitle' => $response->original['statusTitle'],
                        'statusMessage' => $response->original['statusMessage'],
                        'status' => $response->original['status']
                    ]
                ]);
            }


            // get accounts with access token
            $response = $tink->fetchAccounts($request->user()->id, $apiCredential);

            // handle error
            if($response->original['status'] == 'error') {
                Log::error($response->original['statusMessage'], ['response' => $response]);
                return redirect()->route('connect.sources')->with([
                    'message' => [
                        'statusTitle' => $response->original['statusTitle'],
                        'statusMessage' => $response->original['statusMessage'],
                        'status' => $response->original['status']
                    ]
                ]);
            }

            // return with success message
            return redirect()->route('connect.sources')->with([
                'message' => [
                    'statusTitle' => 'Connected',
                    'statusMessage' => 'Your bank has been connected successfully. Activate your accounts to start fetching transactions.',
                    'status' => 'success'
                ]
            ]);
        }

        // return with error message
        Log::error('Invalid request.', ['request' => $request]);
        return redirect()->route('connect.sources')->with([
            'message' => [
                'statusTitle' => 'Error',
                'statusMessage' => 'Invalid request.',
                'status' => 'error'
            ]
        ]);
    }

    public function connectBudgetCallback(Request $request, $budgetType): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        if($budgetType == BudgetType::YNAB) {
            $ynab = new YnabController();
            $authCode = $request->query('code');

            //Use auth code to get access token
            $response = $ynab->getAccessToken($authCode);

            // handle error
            if($response->original['status'] == 'error') {
                Log::error($response->original['statusMessage'], ['response' => $response]);
                return to_route('connect.budgets')->with([
                    'message' => [
                        'statusTitle' => $response->original['statusTitle'],
                        'statusMessage' => $response->original['statusMessage'],
                        'status' => $response->original['status']
                    ]
                ]);
            }

            // define variables
            $accessToken = $response->original['accessToken'];
            $refreshToken = $response->original['refreshToken'];
            $expiresIn = $response->original['expiresIn'];
            $expiresAt =  Carbon::now()->addSeconds($expiresIn);

            // fetch budget
            $response = $ynab->fetchBudget($user, $accessToken);

            // handle error
            if($response->original['status'] == 'error') {
                Log::error($response->original['statusMessage'], ['response' => $response]);
                return to_route('connect.budgets')->with([
                    'message' => [
                        'statusTitle' => $response->original['statusTitle'],
                        'statusMessage' => $response->original['statusMessage'],
                        'status' => $response->original['status']
                    ]
                ]);
            }

            // set destination variable
            $destinations = $response->original['destinations'];

            // check if there are any destinations
            if(count($destinations) == 0) {
                Log::error('No budget accounts found.', ['response' => $response]);
                return to_route('connect.budgets')->with([
                    'message' => [
                        'statusTitle' => 'This budget has no accounts',
                        'statusMessage' => 'No budget accounts found. Make sure you connect a budget that has at least one open account.',
                        'status' => 'warning'
                    ]
                ]);
            }

            // get YNAB user id
            $response = $ynab->getUserId($accessToken);

            // handle error
            if($response->original['status'] == 'error') {
                Log::error($response->original['statusMessage'], ['response' => $response]);
                return to_route('connect.budgets')->with([
                    'message' => [
                        'statusTitle' => $response->original['statusTitle'],
                        'statusMessage' => $response->original['statusMessage'],
                        'status' => $response->original['status']
                    ]
                ]);
            }

            // set user id variable
            $ynabUserId = $response->original['user_id'];

            //Get API credentials registered to this YNAB user
            $apiCredentials = ApiCredential::where('destination_type', BudgetType::YNAB)->where('identifier', $ynabUserId)->get();

            //Check if there are any API credentials registered to this YNAB user
            if($apiCredentials->count() > 0) {
                if($apiCredentials[0]->user_id !== $user->id) {
                    //If the API credential is registered to another user, return error
                    Log::error('This YNAB account is already connected to another user.', ['User ID' => $user->id, 'API Credentials user ID' => $apiCredentials[0]->user_id]);
                    return to_route('connect.budgets')->with([
                        'message' => [
                            'statusTitle' => 'Error',
                            'statusMessage' => 'This YNAB account is already connected to another user.',
                            'status' => 'success'
                        ]
                    ]);
                } else {
                    //If the API credential is registered to this user, update or create
                    ApiCredential::updateOrCreate([
                        'user_id' => $user->id,
                        'destination_type' => BudgetType::YNAB,
                    ], [
                        'user_id' => $user->id,
                        'destination_type' => BudgetType::YNAB,
                        'token1' => Crypt::encryptString($accessToken),
                        'token2' => Crypt::encryptString($refreshToken),
                        'expires_at' => $expiresAt,
                        'type' => ApiCredentialType::OAuth,
                        'active' => true,
                    ]);
                }
            } else {
                //No API credential registered to this YNAB user, create it
                ApiCredential::updateOrCreate([
                    'user_id' => $user->id,
                    'destination_type' => BudgetType::YNAB,
                ], [
                    'user_id' => $user->id,
                    'destination_type' => BudgetType::YNAB,
                    'token1' => Crypt::encryptString($accessToken),
                    'token2' => Crypt::encryptString($refreshToken),
                    'identifier' => $ynabUserId,
                    'expires_at' => $expiresAt,
                    'type' => ApiCredentialType::OAuth,
                    'active' => true,
                ]);
            }

            //Set each destination to active
            foreach($destinations as $destination) {
                $destination->active = true;
                $destination->save();
            }

            return to_route('connect.budgets')->with([
                'message' => [
                    'statusTitle' => 'Budget connected',
                    'statusMessage' => 'Budget "' . $destination->name . '" has been added to Synci.io.',
                    'status' => 'success'
                ]
            ]);
        }

        Log::error('Unable to add budget.');
        return to_route('connect.budgets')->with([
            'message' => [
                'statusTitle' => 'Not found',
                'statusMessage' => 'Unable to add budget.',
                'status' => 'error'
            ]
        ]);
    }

    public function curve(Request $request): \Inertia\Response
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


    public function alterSource(Request $request, $sourceId): \Illuminate\Http\JsonResponse
    {
        $source = Source::find($sourceId);
        $budgetId = $request->query('budgetId');
        $startDate = $request->query('startDate');


        if($source->user_id === $request->user()->id) {
            if($budgetId && $startDate) {
                // budget and start date provided, create connection link
                $connectionLink = ConnectionLink::updateOrCreate([
                    'source_id' => $source->id,
                    'user_id' => $request->user()->id,
                ], [
                    'user_id' => $request->user()->id,
                    'source_id' => $source->id,
                    'destination_id' => $budgetId,
                    'start_date' => $startDate,
                    'active' => true,
                ]);

                // activate source
                $source->active = true;
                $source->start_date = $startDate;
                $source->save();

                if($source->type === SourceType::Tink) {
                    // dispatch fetch transactions job
                    FetchTransactions::dispatch($source, $startDate, Str::uuid());
                }

                $statusTitle = 'Source Activated';
                $statusMessage = 'The source "' . $source->name .  '" has been activated.';
            } else {
                // no budget or start date provided, get & deactivate connection link
                $connectionLink = ConnectionLink::where('source_id', $source->id)->where('user_id', $request->user()->id)->first();

                if($connectionLink) {
                    $connectionLink->active = false;
                    $connectionLink->save();

                    // deactivate source
                    $source->active = false;
                    $source->save();
                } else {
                    // no connection link found with provided budget ID and source ID
                    abort(404);
                }

                $statusTitle = 'Source Deactivated';
                $statusMessage = 'The source "' . $source->name .  '" has been deactivated.';
            }
        } else {
            // source does not belong to user
            abort(401);
        }

        return response()->json([
            'status' => 'success',
            'statusTitle' => $statusTitle,
            'statusMessage' => $statusMessage,
        ]);
    }

    public function deleteBudget(Request $request, $destinationId): \Illuminate\Http\RedirectResponse
    {
        $destination = Destination::find($destinationId);

        if($destination->user_id === $request->user()->id) {
            $destination->delete();
        } else {
            abort(401);
        }

        return to_route('connect.budgets')->with([
            'message' => [
                'statusTitle' => 'Budget deleted',
                'statusMessage' => 'The budget "' . $destination->name .  '" has been deleted.',
                'status' => 'success'
            ]
        ]);
    }

    public function deleteSource(Request $request, $sourceId): \Illuminate\Http\RedirectResponse
    {
        $source = Source::find($sourceId);

        // delete source
        if($source->user_id === $request->user()->id) {
            $source->delete();
        } else {
            abort(401);
        }

        return to_route('connect.sources')->with([
            'message' => [
                'statusTitle' => 'Source deleted',
                'statusMessage' => 'The source "' . $source->name .  '" has been deleted.',
                'status' => 'success'
            ]
        ]);
    }
}
