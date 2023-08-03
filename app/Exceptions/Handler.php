<?php

namespace App\Exceptions;

use App\Models\Membership;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
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
     * @param Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        if ($this->shouldReport($e) && app()->bound('sentry')) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     *
     * @return JsonResponse|RedirectResponse|Response|SymfonyResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response|JsonResponse|SymfonyResponse|RedirectResponse
    {
        $response = parent::render($request, $e);

        $this->initialiseTenancyByPath($request->getRequestUri());

        if (! config('app.debug') && in_array($response->status(), [500, 503, 402, 403, 404])) {
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

    private function initialiseTenancyByPath(string $uri): void
    {
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
