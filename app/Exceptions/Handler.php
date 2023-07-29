<?php

namespace App\Exceptions;

use App\Models\Membership;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = ['password', 'password_confirmation'];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception) && app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     *
     * @return JsonResponse|RedirectResponse|Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        $response = parent::render($request, $exception);

        $this->initialiseTenancyByPath($request->getRequestUri());

        if (! config('app.debug') && in_array($response->status(), [500, 503, 404, 403])) {
            return Inertia::render('Error', [
                'tenant' => tenant(),
                'status' => $response->status(),
                'orgAdmins' => Membership::role('Admin')->limit(5)->with('user')->get()->values(),
                'isMember' => auth()?->user()?->membership,
            ])
                ->toResponse($request)
                ->setStatusCode($response->status());
        } else if ($response->status() === 419) {
            return back()->with([
                'status' => 'The page expired, please try again.',
                'success' => false,
            ]);
        }

        return $response;
    }

    private function initialiseTenancyByPath(string $uri) {
        $found = preg_match('/\/(.*?)\//', $uri, $match) === 1;

        if(! $found) {
            return;
        }

        try {
            tenancy()->initialize($match[1]);
        } catch (TenantCouldNotBeIdentifiedById $e) {
            // Allow handler to continue without tenant
        }
    }
}
