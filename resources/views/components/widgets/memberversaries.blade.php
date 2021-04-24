@props(['memberversaries'])
@php use App\Models\Singer; /* @var Singer $singer */ @endphp
<div class="card">
    <div class="card-header"><h3 class="h4">Memberversaries</h3></div>
    <div class="list-group list-group-flush">
        @forelse($memberversaries as $singer)
        <div class="list-group-item">
            @if($singer->memberversary->isToday())
                <i class="fas fa-fw fa-birthday-cake"></i> <strong>Today!</strong>
            @elseif($singer->memberversary->isTomorrow())
                <strong>Tomorrow!</strong>
            @elseif($singer->memberversary->isSameWeek())
                <strong>This week!</strong>
            @endif

            {{ $singer->joined_at->longAbsoluteDiffForHumans() }} - {{ $singer->name }}
        </div>
        @empty
        <div class="list-group-item">No memberversaries this month.</div>
        @endforelse
    </div>
</div>