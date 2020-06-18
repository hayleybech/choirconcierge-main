<form action="{{ $action }}" method="post" class="d-inline-block">
    @method('delete')
    @csrf
    <button type="submit" class="btn btn-link text-danger link-confirm p-0"><i class="fas fa-fw fa-trash"></i></button>
</form>