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

        .images {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 22rem), 1fr));
            gap: 1rem;
        }

        figure {
            margin: 0;
        }

        figcaption {
            margin-bottom: .5rem;
            font-weight: 700;
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
    <p>Local visual-regression captures and approved references</p>
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
                                    <option value="{{ $value }}" @selected($field === 'state' && $value === 'normal')>{{ $value }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @else
                        @foreach ($values as $value)
                            <option value="{{ $value }}" @selected($field === 'state' && $value === 'normal')>{{ $value }}</option>
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
        <label>
            View
            <select data-gallery-view>
                <option value="grid" selected>Grid</option>
                <option value="compare">Compare</option>
            </select>
        </label>
        <label>
            Grid image
            <select data-grid-variant-select>
                <option value="latest" selected>Latest capture</option>
                <option value="baseline">Approved baseline</option>
            </select>
        </label>
    </form>

    <p class="screenshot-count" data-screenshot-count role="status" aria-live="polite"></p>

    <section class="gallery" data-gallery data-view="grid" aria-label="Screenshot cases">
        @forelse ($entries as $entry)
            <article class="case" data-screenshot
                     data-module="{{ $entry['module'] }}"
                     data-page_type="{{ $entry['page_type'] }}"
                     data-state="{{ $entry['state'] }}"
                     data-persona="{{ $entry['persona'] }}"
                     data-browser="{{ $entry['browser'] ?? 'chromium' }}"
                     data-route="{{ $entry['route'] }}"
                     data-comparison="{{ $entry['comparison'] }}"
                     data-viewport="{{ $entry['viewport']['label'] }}">
                <h2>{{ $entry['component'] }} — {{ $entry['viewport']['label'] }}</h2>
                <p class="metadata">
                    {{ $entry['id'] }} · {{ $entry['route'] }} · {{ $entry['viewport']['width'] }}
                    × {{ $entry['viewport']['height'] }} · {{ $entry['persona'] }} · {{ $entry['comparison'] }}
                </p>
                <div class="grid-captures" data-grid-captures>
                    @foreach (['baseline' => 'Approved baseline', 'latest' => 'Latest capture'] as $variant => $label)
                        <figure data-grid-variant="{{ $variant }}" @if ($variant !== 'latest') hidden @endif>
                            @if ($entry['images'][$variant])
                                <button type="button" class="zoom-trigger" data-zoom-trigger
                                        aria-label="Zoom {{ strtolower($label) }} for {{ $entry['component'] }} at {{ $entry['viewport']['label'] }}">
                                    <img src="{{ route('local.visual-regression.image', [$entry['id'], $entry['viewport']['id'], $variant]) }}"
                                         alt="{{ $label }} for {{ $entry['component'] }} at {{ $entry['viewport']['label'] }}">
                                </button>
                            @else
                                <p class="missing">No {{ strtolower($label) }} captured.</p>
                            @endif
                        </figure>
                    @endforeach
                </div>
                <div class="images comparison-images" data-comparison-captures hidden>
                    @foreach (['baseline' => 'Approved baseline', 'latest' => 'Latest capture', 'diff' => 'Diff'] as $variant => $label)
                        <figure>
                            <figcaption>{{ $label }}</figcaption>
                            @if ($entry['images'][$variant])
                                <button type="button" class="zoom-trigger" data-zoom-trigger
                                        aria-label="Zoom {{ strtolower($label) }} for {{ $entry['component'] }} at {{ $entry['viewport']['label'] }}">
                                    <img src="{{ route('local.visual-regression.image', [$entry['id'], $entry['viewport']['id'], $variant]) }}"
                                         alt="{{ $label }} for {{ $entry['component'] }} at {{ $entry['viewport']['label'] }}">
                                </button>
                            @else
                                <p class="missing">No {{ strtolower($label) }} captured.</p>
                            @endif
                        </figure>
                    @endforeach
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
    <p data-zoom-caption></p>
</dialog>
<script>
	const filters = Array.from(document.querySelectorAll('[data-filter]'));
	const cards = Array.from(document.querySelectorAll('[data-screenshot]'));
	const gallery = document.querySelector('[data-gallery]');
	const screenshotCount = document.querySelector('[data-screenshot-count]');
	const galleryView = document.querySelector('[data-gallery-view]');
	const gridVariantSelect = document.querySelector('[data-grid-variant-select]');
	const imageZoomDialog = document.querySelector('[data-image-zoom]');
	const zoomImage = imageZoomDialog.querySelector('[data-zoom-image]');
	const zoomCaption = imageZoomDialog.querySelector('[data-zoom-caption]');

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

	function applyGalleryView() {
		gallery.dataset.view = galleryView.value;

		document.querySelectorAll('[data-grid-captures]').forEach(captures => {
			captures.hidden = galleryView.value !== 'grid';
		});

		document.querySelectorAll('[data-comparison-captures]').forEach(captures => {
			captures.hidden = galleryView.value !== 'compare';
		});

		document.querySelectorAll('[data-grid-variant]').forEach(variant => {
			variant.hidden = variant.dataset.gridVariant !== gridVariantSelect.value;
		});
	}

	filters.forEach(filter => filter.addEventListener('change', applyFilters));
	galleryView.addEventListener('change', applyGalleryView);
	gridVariantSelect.addEventListener('change', applyGalleryView);
	applyFilters();

	document.querySelectorAll('[data-zoom-trigger]').forEach(trigger => {
		trigger.addEventListener('click', () => {
			const image = trigger.querySelector('img');
			zoomImage.src = image.currentSrc || image.src;
			zoomImage.alt = image.alt;
			zoomCaption.textContent = image.alt;
			imageZoomDialog.showModal();
		});
	});

	imageZoomDialog.addEventListener('click', event => {
		if (event.target === imageZoomDialog) {
			imageZoomDialog.close();
		}
	});
</script>
</body>
</html>