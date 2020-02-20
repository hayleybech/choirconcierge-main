@if (session('status'))
    <div class="alert {{ isset($Response->error) || session('fail') ? 'alert-danger' : 'alert-success' }}" role="alert">
        {{ session('status') }}

        @isset( $Response->error )
            <pre>
			{{ var_dump($Response) }}
			@ json($args)
		</pre>
        @endisset
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif