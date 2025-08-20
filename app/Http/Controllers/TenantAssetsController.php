<?php

namespace App\Http\Controllers;

use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Storage;
use Throwable;

class TenantAssetsController extends Controller
{
    public static $tenancyMiddleware = InitializeTenancyByPath::class;

    public function __construct()
    {
        $this->middleware(static::$tenancyMiddleware);
    }

    public function asset($path = null)
    {
        abort_if($path === null, 404);

        try {
            return Storage::disk('tenant')->download($path);
        } catch (Throwable $th) {
            abort(404);
        }
    }
}
