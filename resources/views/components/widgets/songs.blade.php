@props(['songs'])
@php use App\Models\Song; /* @var Song $song */ @endphp
<div class="card">
    <div class="card-header"><h3 class="h4">Songs to Learn</h3></div>
    <div class="list-group list-group-flush">
        @forelse($songs as $song)
            <div class="list-group-item">
                <a href="{{ route('songs.show', $song) }}">{{ $song->title }}</a>
            </div>
        @empty
            <div class="list-group-item">No songs to learn.</div>
        @endforelse
    </div>
</div>