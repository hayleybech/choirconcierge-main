@if(session('status') || $errors->any())
<x-alert variant="{{ $errors->any() || session('fail') || isset($Response->error) ? 'danger' : 'success' }}">
    @if($errors->any())
    <x-slot name="title">Error</x-slot>

    <ul class="list-unstyled">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>

    @elseif( isset($Response->error) )
    <x-slot name="title">Error</x-slot>

    <pre>
    {{ var_dump($Response) }}
    @ json($args)
    </pre>

    @else
    {{ session('status') }}
    @endif
</x-alert>
@endif