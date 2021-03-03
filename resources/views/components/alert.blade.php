<div class="alert alert-{{ $variant }} d-flex align-items-center">
    <i class="fas fa-fw {{ $icon() }} fa-2x mr-4"></i>
    <div>
        @if($title)
        <h5 class="alert-heading">{{ $title }}</h5>
        @endif

        {{ $slot }}
    </div>
</div>