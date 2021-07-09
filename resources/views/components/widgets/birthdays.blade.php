@props(['birthdays', 'emptyDobs'])
@php use App\Models\Singer; /* @var Singer $singer */ @endphp
<div class="card">
    <div class="card-header"><h3 class="h4">Birthdays</h3></div>
    <div class="list-group list-group-flush">
        @forelse($birthdays as $singer)
        <div class="list-group-item">
            @if($singer->user->birthday->isToday())
                <i class="fas fa-fw fa-birthday-cake"></i> <strong>Today!</strong>
            @elseif($singer->user->birthday->isTomorrow())
                <strong>Tomorrow!</strong>
            @elseif($singer->user->birthday->isSameWeek())
                <strong>This week!</strong>
            @endif

            {{ $singer->user->birthday->format('D, '.config('app.formats.date_md')) }} - {{ $singer->name }}
        </div>
        @empty
        <div class="list-group-item">No birthdays this month.</div>
        @endforelse
    </div>
    <div class="card-footer">
        <span class="text-muted">{{ $emptyDobs ?? 0 }} active singers have no birthdate listed.</span>
    </div>
</div>