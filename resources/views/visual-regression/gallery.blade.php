<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CGAL</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Arial, sans-serif;
            color: #1f2937;
            background: #f3f4f6;
        }

        body {
            margin: 0;
        }

        header {
            padding: 1.5rem 2rem;
            color: white;
            background: #312e81;
        }

        h1 {
            margin: 0;
            font-size: 1.75rem;
        }

        main {
            padding: 1.5rem 2rem;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .screenshot-count {
            margin: 0 0 1rem;
            color: #4b5563;
        }

        label {
            display: grid;
            gap: .35rem;
            font-size: .875rem;
        }

        select {
            min-width: 10rem;
            padding: .5rem;
            border: 1px solid #9ca3af;
            border-radius: .25rem;
            background: white;
        }

        .case {
            margin-bottom: 1.5rem;
            padding: 1rem;
            border: 1px solid #d1d5db;
            border-radius: .5rem;
            background: white;
        }

        .case h2 {
            margin: 0 0 .5rem;
            font-size: 1.125rem;
        }

        .case-indicators { display: inline-flex; align-items: center; gap: .35rem; margin-left: .35rem; vertical-align: middle; }
        .indicator-icon { width: 1.15rem; height: 1.15rem; vertical-align: middle; }

        .metadata {
            margin: 0 0 1rem;
            color: #4b5563;
            font-size: .875rem;
        }

        .gallery[data-view="grid"] {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .gallery[data-view="grid"] .case {
            min-width: 0;
            margin: 0;
        }

        .gallery[data-view="grid"] .metadata {
            display: none;
        }

        .gallery[data-view="grid"] .case h2 {
            font-size: 1rem;
            overflow-wrap: anywhere;
        }

        .grid-captures figure {
            overflow: hidden;
            border: 1px solid #d1d5db;
            background: #f9fafb;
        }

        .grid-captures img {
            width: 100%;
            height: 24rem;
            object-fit: contain;
            object-position: top;
        }

        .zoom-trigger {
            display: block;
            width: 100%;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: zoom-in;
        }

        figure {
            margin: 0;
        }

        img {
            display: block;
            max-width: 100%;
            border: 1px solid #d1d5db;
        }

        .image-zoom {
            position: relative;
            max-width: min(96vw, 1600px);
            max-height: 96vh;
            padding: 3rem 1rem 1rem;
            border: 0;
            background: #111827;
        }

        .image-zoom::backdrop {
            background: rgb(17 24 39 / 85%);
        }

        .image-zoom img {
            max-width: 94vw;
            max-height: 88vh;
            object-fit: contain;
            margin: auto;
        }

        .image-zoom p {
            margin: .75rem 0 0;
            color: white;
            text-align: center;
        }

        .image-zoom-close {
            position: absolute;
            top: .5rem;
            right: .5rem;
            padding: .35rem .65rem;
            border: 1px solid #9ca3af;
            border-radius: .25rem;
            color: white;
            background: #374151;
            cursor: pointer;
        }

        .missing {
            padding: 2rem;
            color: #6b7280;
            background: #f9fafb;
        }

        [hidden] {
            display: none !important;
        }

        @media (max-width: 960px) {
            .gallery[data-view="grid"] {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 620px) {
            .gallery[data-view="grid"] {
                grid-template-columns: minmax(0, 1fr);
            }

            main {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
<header>
    <h1>CGAL Screenshot Gallery</h1>
    <p>Local catalog screenshot captures</p>
</header>
<main>
    <form class="filters" aria-label="Screenshot filters">
        @foreach ($filters as $field => $values)
            <label>
                {{ $field === 'persona' ? 'Role' : str($field)->replace('_', ' ')->title() }}
                <select data-filter="{{ $field }}">
                    <option value="">All</option>
                    @if (isset($filterGroups[$field]))
                        @foreach ($filterGroups[$field] as $group => $groupValues)
                            <optgroup label="{{ $group }}">
                                @foreach ($groupValues as $value)
                                    <option value="{{ $value }}" @selected($field === 'state' && $value === 'normal')>{{ $field === 'module' ? ($moduleLabels[$value] ?? ucwords(str_replace('-', ' ', $value))) : $value }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @else
                        @foreach ($values as $value)
                            <option value="{{ $value }}" @selected($field === 'state' && $value === 'normal')>{{ $field === 'module' ? ($moduleLabels[$value] ?? ucwords(str_replace('-', ' ', $value))) : $value }}</option>
                        @endforeach
                    @endif
                </select>
            </label>
        @endforeach
        <label>
            Device
            <select data-filter="viewport">
                <option value="">All</option>
                @foreach ($entries->pluck('viewport.label')->unique() as $label)
                    <option value="{{ $label }}" @selected($label === 'Desktop')>{{ $label }}</option>
                @endforeach
            </select>
        </label>
    </form>

    <p class="screenshot-count" data-screenshot-count role="status" aria-live="polite"></p>

    <section class="gallery" data-gallery data-view="grid" aria-label="Screenshot cases">
        @forelse ($entries as $entry)
            <article class="case" data-screenshot data-case-id="{{ $entry['id'] }}"
                     data-module="{{ $entry['module'] }}"
                     data-page_type="{{ $entry['page_type'] }}"
                     data-state="{{ $entry['state'] }}"
                     data-persona="{{ $entry['persona'] }}"
                     data-browser="{{ $entry['browser'] ?? 'chromium' }}"
                     data-route="{{ $entry['route'] }}"
                     data-viewport="{{ $entry['viewport']['label'] }}">
                <h2>{{ $entry['component'] }} — {{ $entry['viewport']['label'] }}
                    <span class="case-indicators" aria-label="{{ $entry['viewport']['label'] }} device, {{ $entry['capture_status'] }}">
                        @if (str_contains(strtolower($entry['viewport']['label']), 'mobile'))
                            <svg class="indicator-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>
                        @elseif (str_contains(strtolower($entry['viewport']['label']), 'tablet'))
                            <svg class="indicator-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M11 18h2"/></svg>
                        @else
                            <svg class="indicator-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/></svg>
                        @endif
                    </span>
                </h2>
                <p class="metadata">
                    {{ $entry['id'] }} · {{ $entry['route'] }} · {{ $entry['viewport']['width'] }}
                    × {{ $entry['viewport']['height'] }} · {{ $entry['persona'] }} · {{ $entry['capture_status'] }}
                </p>
                <div class="grid-captures" data-grid-captures>
                    <figure>
                        @if ($entry['images']['latest'])
                            <button type="button" class="zoom-trigger" data-zoom-trigger
                                    aria-label="Zoom latest capture for {{ $entry['component'] }} at {{ $entry['viewport']['label'] }}">
                                <img src="{{ route('local.visual-regression.image', [$entry['id'], $entry['viewport']['id'], 'latest']) }}"
                                     alt="Latest capture for {{ $entry['component'] }} at {{ $entry['viewport']['label'] }}">
                            </button>
                        @else
                            <p class="missing">No capture available.</p>
                        @endif
                    </figure>
                </div>
            </article>
        @empty
            <p>No screenshot cases are currently catalogued.</p>
        @endforelse
    </section>
</main>
<dialog class="image-zoom" data-image-zoom aria-label="Screenshot preview">
    <form method="dialog">
        <button class="image-zoom-close" aria-label="Close image preview">Close</button>
    </form>
    <img data-zoom-image alt="">
    <p data-zoom-caption aria-live="polite"></p>
    <p class="zoom-help" data-zoom-help hidden>Use the left and right arrow keys to browse screenshots, R to change device, and M to change module.</p>
</dialog>
<script>
	const filters = Array.from(document.querySelectorAll('[data-filter]'));
	const cards = Array.from(document.querySelectorAll('[data-screenshot]'));
	const screenshotCount = document.querySelector('[data-screenshot-count]');
	const imageZoomDialog = document.querySelector('[data-image-zoom]');
	const zoomTriggers = Array.from(document.querySelectorAll('[data-zoom-trigger]'));
	const zoomImage = imageZoomDialog.querySelector('[data-zoom-image]');
	const zoomCaption = imageZoomDialog.querySelector('[data-zoom-caption]');
	const zoomHelp = imageZoomDialog.querySelector('[data-zoom-help]');
	let currentZoomTrigger = null;

	function applyFilters() {
		let visibleCount = 0;

		cards.forEach(card => {
			card.hidden = filters.some(filter => filter.value && card.dataset[filter.dataset.filter] !== filter.value);
			if (!card.hidden) {
				visibleCount++;
			}
		});

		screenshotCount.textContent = `Showing ${visibleCount} of ${cards.length} screenshots`;
	}

	function handleFilterChange(filter) {
		applyFilters();
		if (!imageZoomDialog.open) {
			return;
		}

		const currentCard = currentZoomTrigger?.closest('[data-screenshot]');
		const filterName = filter.dataset.filter;
		const matchingTrigger = zoomTriggers.find(trigger => {
			const card = trigger.closest('[data-screenshot]');
			if (!card) {
				return false;
			}

			const matches = filterName === 'viewport'
				? card.dataset.caseId === currentCard?.dataset.caseId && card.dataset.viewport === filter.value
				: card.dataset.viewport === currentCard?.dataset.viewport && card.dataset[filterName] === filter.value;

			return matches && filters.every(activeFilter => !activeFilter.value || activeFilter.dataset.filter === filterName || card.dataset[activeFilter.dataset.filter] === activeFilter.value);
		});

		if (matchingTrigger) {
			showZoomImage(matchingTrigger);
			return;
		}

		zoomCaption.textContent = `${zoomImage.alt} — no matching image for the selected ${filterName === 'viewport' ? 'device' : 'filter'}. The current image remains open; change filters or press Escape to close.`;
	}

	function showZoomImage(trigger) {
		const image = trigger.querySelector('img');
		zoomImage.src = image.currentSrc || image.src;
		zoomImage.alt = image.alt;
		zoomCaption.textContent = image.alt;
		zoomHelp.hidden = false;
		currentZoomTrigger = trigger;
		if (!imageZoomDialog.open) {
			imageZoomDialog.showModal();
		}
	}

	function navigateZoomedImage(direction) {
		const visibleGridTriggers = zoomTriggers.filter(trigger => {
			const card = trigger.closest('[data-screenshot]');
			return card && !card.hidden;
		});
		const currentIndex = visibleGridTriggers.indexOf(currentZoomTrigger);

		if (currentIndex === -1 || visibleGridTriggers.length < 2) {
			return;
		}

		const nextIndex = (currentIndex + direction + visibleGridTriggers.length) % visibleGridTriggers.length;
		showZoomImage(visibleGridTriggers[nextIndex]);
	}

	filters.forEach(filter => filter.addEventListener('change', () => handleFilterChange(filter)));
	applyFilters();

	zoomTriggers.forEach(trigger => {
		trigger.addEventListener('click', () => showZoomImage(trigger));
	});

	imageZoomDialog.addEventListener('keydown', event => {
		if (!imageZoomDialog.open) {
			return;
		}

		if (['r', 'm'].includes(event.key.toLowerCase())) {
			const filterName = event.key.toLowerCase() === 'r' ? 'viewport' : 'module';
			const filter = filters.find(item => item.dataset.filter === filterName);
			if (filter) {
				const options = Array.from(filter.options).filter(option => option.value);
				const currentIndex = options.findIndex(option => option.value === filter.value);
				filter.value = options[(currentIndex + 1) % options.length]?.value ?? '';
				filter.dispatchEvent(new Event('change'));
				event.preventDefault();
			}
			return;
		}

		const direction = event.key === 'ArrowRight' ? 1 : event.key === 'ArrowLeft' ? -1 : 0;

		if (direction !== 0) {
			event.preventDefault();
			navigateZoomedImage(direction);
		}
	});

	imageZoomDialog.addEventListener('click', event => {
		if (event.target === imageZoomDialog) {
			imageZoomDialog.close();
		}
	});
</script>
</body>
</html>