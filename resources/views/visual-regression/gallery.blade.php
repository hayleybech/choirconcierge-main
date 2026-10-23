<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choir Gallery</title>
    <style>
        :root { color-scheme: light; font-family: Arial, sans-serif; color: #1f2937; background: #f3f4f6; }
        body { margin: 0; }
        header { padding: 1.5rem 2rem; color: white; background: #312e81; }
        h1 { margin: 0; font-size: 1.75rem; }
        main { padding: 1.5rem 2rem; }
        .filters { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
        label { display: grid; gap: .35rem; font-size: .875rem; }
        select { min-width: 10rem; padding: .5rem; border: 1px solid #9ca3af; border-radius: .25rem; background: white; }
        .case { margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #d1d5db; border-radius: .5rem; background: white; }
        .case h2 { margin: 0 0 .5rem; font-size: 1.125rem; }
        .metadata { margin: 0 0 1rem; color: #4b5563; font-size: .875rem; }
        .images { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 22rem), 1fr)); gap: 1rem; }
        figure { margin: 0; }
        figcaption { margin-bottom: .5rem; font-weight: 700; }
        img { display: block; max-width: 100%; border: 1px solid #d1d5db; }
        .missing { padding: 2rem; color: #6b7280; background: #f9fafb; }
        [hidden] { display: none !important; }
    </style>
</head>
<body>
<header>
    <h1>CGAL — Choir Gallery</h1>
    <p>Local visual-regression captures and approved references</p>
</header>
<main>
    <form class="filters" aria-label="Screenshot filters">
        @foreach ($filters as $field => $values)
            <label>
                {{ $field === 'persona' ? 'Role' : str($field)->replace('_', ' ')->title() }}
                <select data-filter="{{ $field }}">
                    <option value="">All</option>
                    @foreach ($values as $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </label>
        @endforeach
        <label>
            Device
            <select data-filter="viewport">
                <option value="">All</option>
                @foreach ($entries->pluck('viewport.label')->unique() as $label)
                    <option value="{{ $label }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>
    </form>

    <section aria-label="Screenshot cases">
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
                    {{ $entry['id'] }} · {{ $entry['route'] }} · {{ $entry['viewport']['width'] }} × {{ $entry['viewport']['height'] }} · {{ $entry['persona'] }} · {{ $entry['comparison'] }}
                </p>
                <div class="images">
                    @foreach (['baseline' => 'Approved baseline', 'latest' => 'Latest capture', 'diff' => 'Diff'] as $variant => $label)
                        <figure>
                            <figcaption>{{ $label }}</figcaption>
                            @if ($entry['images'][$variant])
                                <img src="{{ route('local.visual-regression.image', [$entry['id'], $entry['viewport']['id'], $variant]) }}"
                                     alt="{{ $label }} for {{ $entry['component'] }} at {{ $entry['viewport']['label'] }}">
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
<script>
    const filters = Array.from(document.querySelectorAll('[data-filter]'));
    const cards = Array.from(document.querySelectorAll('[data-screenshot]'));

    function applyFilters() {
        cards.forEach(card => {
            card.hidden = filters.some(filter => filter.value && card.dataset[filter.dataset.filter] !== filter.value);
        });
    }

    filters.forEach(filter => filter.addEventListener('change', applyFilters));
</script>
</body>
</html>