@props(['songs'])
@php use App\Models\Song; /* @var Song $song */ @endphp
<div class="card">
    <div class="card-header"><h3 class="h4">Songs to Learn</h3></div>
    <div class="list-group list-group-flush">
        @foreach($songs as $learning)
            @if($learning->songs->count() > 0)
                <div class="list-group-item" style="background-color: #ddd; border-bottom: 1px solid #ccc;">
                    <strong class="text-{{ $learning->status_colour }}"><i class="fas fa-fw mr-2 {{ $learning->status_icon }}"></i> {{ $learning->status_name }}</strong>
                </div>
                @foreach($learning->songs as $song)
                    <div class="list-group-item">
                        <a href="{{ route('songs.show', $song) }}">{{ $song->title }}</a><br>
                    </div>
                @endforeach
            @endif
        @endforeach
    </div>
</div>