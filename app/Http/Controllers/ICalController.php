<?php

namespace App\Http\Controllers;

use App\EventIcalFeed;
use App\Models\Ensemble;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class ICalController extends Controller
{
    private const LEGACY_FEED_EXPIRES_AT = '2026-12-25 00:00:00';

    public function index(Request $request): Response
    {
        $isLegacy = ! $request->has('user') && ! $request->has('signature');

        abort_unless(
            $request->hasValidSignature() || ($isLegacy && now()->lt(Carbon::parse(self::LEGACY_FEED_EXPIRES_AT))),
            403,
        );

        if ($isLegacy) {
            return $this->response(Event::query()->get(), true);
        }

        $user = User::query()->findOrFail(Crypt::decryptString($request->string('user')->toString()));
        $membership = $user->memberships()
            ->withoutTenancy()
            ->where('tenant_id', tenant()->id)
            ->firstOrFail();

        $userEnsembleIds = $membership->enrolments->pluck('ensemble_id');
        $events = Event::query()
            ->when(Ensemble::exists(), function (Builder $query) use ($userEnsembleIds): void {
                $query->whereHas('ensembles', function (Builder $query) use ($userEnsembleIds): void {
                    $query->whereKey($userEnsembleIds);
                });
            })
            ->get();

        return $this->response($events);
    }

    private function response(Collection $events, bool $legacy = false): Response
    {
        return response((new EventIcalFeed($events, $legacy))->get())
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'attachment; filename="events-calendar.ics"')
            ->header('charset', 'utf-8');
    }
}
