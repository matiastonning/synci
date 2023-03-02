<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UserController extends Controller
{
    public function generateNewUuid(Request $request): array
    {
        $user = $request->user();
        $user->uuid = Str::uuid();

        return ['success' => $user->save(), 'uuid' => $user->uuid];
    }
}
