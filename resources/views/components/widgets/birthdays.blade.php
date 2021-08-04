@props(['birthdays', 'emptyDobs'])
@php use App\Models\User; /* @var User $user */ @endphp
<div class="card">
    <div class="card-header"><h3 class="h4">Birthdays</h3></div>
    <div class="list-group list-group-flush">
        @forelse($birthdays as $user)
        <div class="list-group-item">
            @if($user->birthday->isToday())
                <i class="fas fa-fw fa-birthday-cake"></i> <strong>Today!</strong>
            @elseif($user->birthday->isTomorrow())
                <strong>Tomorrow!</strong>
            @elseif($user->birthday->isSameWeek())
                <strong>This week!</strong>
            @endif

            {{ $user->birthday->format('D, '.config('app.formats.date_md')) }} - {{ $user->name }}
        </div>
        @empty
        <div class="list-group-item">No birthdays this month.</div>
        @endforelse
    </div>
    <div class="card-footer">
        <span class="text-muted">{{ $emptyDobs ?? 0 }} active singers have no birthdate listed.</span>
    </div>
</div>