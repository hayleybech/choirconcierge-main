<?php

namespace App\Http\Controllers;

use App\Models\MailLog;
use App\Support\VisualRegression\ScreenshotCatalog;
use JsonException;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Laragear\TwoFactor\Models\TwoFactorAuthentication;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LocalVisualRegressionGalleryController extends Controller
{
    private const PRIMARY_MODULES = [
        'dashboard',
        'singers',
        'songs',
        'events',
        'documents',
        'riser-stacks',
        'mailing-lists',
        'tasks',
        'polls',
        'tenants',
        'users',
    ];

    private const MODULE_LABELS = [
        'account' => 'Account Settings',
        'auth' => 'Authentication',
        'central' => 'Platform',
        'dashboard' => 'Dashboard',
        'documents' => 'Documents',
        'errors' => 'Errors',
        'events' => 'Events',
        'mailing-lists' => 'Communications',
        'polls' => 'Polls',
        'riser-stacks' => 'Riser Stacks',
        'roles' => 'Roles',
        'singers' => 'Singers',
        'songs' => 'Songs',
        'tasks' => 'Onboarding',
        'tenants' => 'Tenants',
        'users' => 'Users',
        'voice-parts' => 'Voice Parts',
    ];

    public function __construct(private readonly ScreenshotCatalog $catalog)
    {
    }

    public function index(): View
    {
        $this->ensureLocalEnvironment();

        $comparisonStatuses = $this->comparisonStatuses();
        $entries = collect($this->catalog->cases())
            ->flatMap(fn(array $case): Collection => collect($this->catalog->viewports())
                ->map(fn(array $viewport): array => [
                    ...$case,
                    'browser' => $case['browser'] ?? 'chromium',
                    'viewport' => $viewport,
                    'comparison' => $comparisonStatuses[$case['id'] . '::' . $viewport['id']]
                        ?? ($this->imageExists($case['id'], $viewport['id'], 'latest') ? 'captured' : 'not_run'),
                    'images' => [
                        'baseline' => $this->imageExists($case['id'], $viewport['id'], 'baseline'),
                        'latest' => $this->imageExists($case['id'], $viewport['id'], 'latest'),
                        'diff' => $this->imageExists($case['id'], $viewport['id'], 'diff'),
                    ],
                ]))
            ->values();

        return view('visual-regression.gallery', [
            'entries' => $entries,
            'filters' => $this->filters($entries),
            'filterGroups' => $this->filterGroups($entries),
            'moduleLabels' => self::MODULE_LABELS,
        ]);
    }

    public function image(string $case, string $viewport, string $variant): BinaryFileResponse
    {
        $this->ensureLocalEnvironment();

        $screenshotCase = $this->catalog->find($case);
        $screenshotViewport = collect($this->catalog->viewports())->firstWhere('id', $viewport);

        abort_if($screenshotCase === null || $screenshotViewport === null, Response::HTTP_NOT_FOUND);
        abort_unless(in_array($variant, ['baseline', 'latest', 'diff'], true), Response::HTTP_NOT_FOUND);

        $path = $this->imagePath($case, $viewport, $variant);

        abort_unless(File::isFile($path), Response::HTTP_NOT_FOUND);

        return response()->file($path, [
            'Cache-Control' => 'no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function errorPreview(int $status): never
    {
        $this->ensureLocalEnvironment();

        abort_unless(in_array($status, [403, 404, 500], true), Response::HTTP_NOT_FOUND);

        abort($status);
    }

    public function twoFactorPreview(Request $request): RedirectResponse
    {
        $this->ensureLocalEnvironment();

        $request->session()->put('2fa.id', 'visual-regression');

        return redirect()->route('auth.2fa.challenge');
    }

    public function changelogEmptyPreview(): InertiaResponse
    {
        $this->ensureLocalEnvironment();

        return Inertia::render('Central/Changelog', ['logs' => collect()]);
    }

    public function twoFactorSetupPreview(): InertiaResponse
    {
        $this->ensureLocalEnvironment();

        $twoFactorAuth = (new TwoFactorAuthentication())->forceFill([
            'shared_secret' => 'JBSWY3DPEHPK3PXP',
            'label' => 'Choir Concierge:visual-regression@example.test',
            'digits' => 6,
            'seconds' => 30,
            'window' => 0,
            'algorithm' => 'sha1',
        ]);

        return Inertia::render('Account/TwoFactor', [
            'enabled' => false,
            'qr_code' => $twoFactorAuth->toQr(),
            'uri' => $twoFactorAuth->toUri(),
            'string' => $twoFactorAuth->toString(),
        ]);
    }

    public function centralMailLogsEmptyPreview(): InertiaResponse
    {
        $this->ensureLocalEnvironment();

        return Inertia::render('Central/MailLogs/Index', [
            'mailTypes' => collect(MailLog::TYPE_LABELS)
                ->map(fn (string $name, string $id): array => ['id' => $id, 'name' => $name])
                ->values(),
            'defaultMailTypes' => MailLog::DEFAULT_TYPES,
            'tenants' => collect(),
            'logs' => MailLog::query()
                ->whereRaw('1 = 0')
                ->with(['latestEvent', 'tenants'])
                ->withCount('opens')
                ->latest()
                ->paginate(50),
        ]);
    }

    /**
     * @param Collection<int, array<string, mixed>> $entries
     * @return array<string, list<string>>
     */
    private function filters(Collection $entries): array
    {
        return collect(['module', 'page_type', 'state', 'persona', 'browser', 'route', 'comparison'])
            ->mapWithKeys(fn(string $field): array => [
                $field => $entries->pluck($field)->filter()->unique()->sort()->values()->all(),
            ])
            ->all();
    }

    /**
     * @param Collection<int, array<string, mixed>> $entries
     * @return array<string, array<string, list<string>>>
     */
    private function filterGroups(Collection $entries): array
    {
        $modules = $entries->pluck('module')->filter()->unique();
        $otherModules = ['auth', 'errors'];
        $primaryModules = $modules
            ->filter(fn(string $module): bool => in_array($module, self::PRIMARY_MODULES, true))
            ->sortBy(fn(string $module): int => (int) array_search($module, self::PRIMARY_MODULES, true))
            ->values();
        $secondaryModules = $modules
            ->reject(fn(string $module): bool => in_array($module, self::PRIMARY_MODULES, true)
                || in_array($module, $otherModules, true))
            ->sortBy(fn(string $module): string => self::MODULE_LABELS[$module] ?? ucwords(str_replace('-', ' ', $module)))
            ->values();
        $otherModuleValues = $modules
            ->filter(fn(string $module): bool => in_array($module, $otherModules, true))
            ->sort()
            ->values();
        $pageTypes = $entries->pluck('page_type')->filter()->unique()->sort()->groupBy(
            fn(string $pageType): string => in_array($pageType, ['index', 'create', 'show', 'edit'], true)
                ? 'crud'
                : 'other',
        );

        $routes = $entries->groupBy(fn(array $entry): string => match (true) {
            $entry['module'] === 'central' || str_starts_with($entry['route'], 'central.') => 'central',
            in_array($entry['module'], ['auth', 'errors'], true) => 'other',
            default => 'tenant',
        })->map(fn(Collection $group): Collection => $group->pluck('route')->unique()->sort()->values());

        return [
            'module' => collect([
                'Primary' => $primaryModules,
                'Secondary' => $secondaryModules,
                'Other' => $otherModuleValues,
            ])->filter(fn(Collection $group): bool => $group->isNotEmpty())
                ->map(fn(Collection $group): array => $group->all())
                ->all(),
            'page_type' => $pageTypes->map(fn(Collection $group): array => $group->values()->all())->all(),
            'route' => $routes->all(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function comparisonStatuses(): array
    {
        $summaryPath = config('visual-regression.latest_directory') . DIRECTORY_SEPARATOR . 'summary.json';

        if (!File::isFile($summaryPath)) {
            return [];
        }

        try {
            $summary = json_decode(File::get($summaryPath), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }

        if (!is_array($summary) || !is_array($summary['results'] ?? null)) {
            return [];
        }

        return collect($summary['results'])
            ->filter(fn(mixed $result): bool => is_array($result)
                && is_string($result['case_id'] ?? null)
                && is_string($result['viewport'] ?? null)
                && is_string($result['status'] ?? null))
            ->mapWithKeys(fn(array $result): array => [
                $result['case_id'] . '::' . $result['viewport'] => $result['status'],
            ])
            ->all();
    }

    private function imageExists(string $case, string $viewport, string $variant): bool
    {
        return File::isFile($this->imagePath($case, $viewport, $variant));
    }

    private function imagePath(string $case, string $viewport, string $variant): string
    {
        $directory = match ($variant) {
            'baseline' => config('visual-regression.baseline_directory'),
            'latest' => config('visual-regression.latest_directory'),
            'diff' => config('visual-regression.diff_directory'),
            default => '',
        };

        return $directory . DIRECTORY_SEPARATOR . $case . DIRECTORY_SEPARATOR . $viewport . '.png';
    }

    private function ensureLocalEnvironment(): void
    {
        abort_unless(app()->environment(['local', 'testing']), Response::HTTP_NOT_FOUND);
    }
}