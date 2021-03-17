@unless ($breadcrumbs->isEmpty())

    <ol class="breadcrumb">
        @foreach ($breadcrumbs as $breadcrumb)

            @if ($breadcrumb->url && $loop->remaining > 1)
                <li class="breadcrumb-item d-none d-lg-inline"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
            @elseif($loop->remaining===1)
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb->url }}">
                        <i class="fa fa-fw fa-angle-left d-lg-none"></i>
                        <span class="d-none d-lg-inline">{{ $breadcrumb->title }}</span>
                    </a>
                </li>
            @else
                <li class="breadcrumb-item active">{{ $breadcrumb->title }}</li>
            @endif

        @endforeach
    </ol>

@endunless
