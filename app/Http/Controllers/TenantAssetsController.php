<?php

namespace App\Http\Controllers;

use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TenantAssetsController extends Controller
{
    public static $tenancyMiddleware = InitializeTenancyByPath::class;

    public function __construct()
    {
        $this->middleware(static::$tenancyMiddleware);
    }

    public function asset(?string $path = null): Response
    {
        abort_if($path === null, 404);

        try {
            if (app()->environment('testing') && str_ends_with(mb_strtolower($path), '.pdf')) {
                return response(Storage::disk('tenant')->get($path))
                    ->header('Content-Type', 'application/pdf');
            }

            return Storage::disk('tenant')->download($path);
        } catch (Throwable $th) {
            abort(404);
        }
    }
}
