<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
		if ($exception instanceof AuthorizationException) {
			if ($request->expectsJson()) {
				return response()->json(['error' => 'Unauthorized.'], 403);
			}

			return redirect()->route('dash');
		}

		return parent::render($request, $exception);
	}
}
