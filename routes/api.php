<?php

use App\Models\User;
use App\Navigation\Navigation;
use App\Navigation\UserNavigation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
        'code' => 'nullable|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    if ($user->hasTwoFactorEnabled()) {
        if (!$request->code) {
            return response()->json(['message' => 'Two-factor authentication required.'], 403);
        }

        if (!$user->validateTwoFactorCode($request->code) && !$user->validateTwoFactorCode($request->code, true)) {
            throw ValidationException::withMessages([
                'code' => ['The provided two-factor authentication code was invalid.'],
            ]);
        }
    }

    $tenant = $user->defaultTenant
        ?? $user->memberships()->latest()->first()?->tenant;

    return response()->json([
        'token' => $user->createToken($request->device_name)->plainTextToken,
        'navigation' => (new Navigation())->get($tenant->id, true),
        'tenant' => $tenant?->primary_domain,
        'user' => $user->setVisible([
            'id', 'name', 'email', 'avatar_url',
        ])->toJson(),
    ]);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/navigation/{tenant}', function (Request $request) {
        return response()->json((new Navigation())->get($request->tenant, true));
    });

    Route::get('/user/navigation/{tenant}', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'avatar_url' => $user->avatar_url,
            'navigation' => (new UserNavigation())->get($request->tenant, true),
        ]);
    });
});

