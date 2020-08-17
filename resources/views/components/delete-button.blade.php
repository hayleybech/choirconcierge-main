<!-- Button trigger modal -->
<button type="button" class="btn btn-link text-danger p-0" data-toggle="modal" data-target="#deleteModal-{{ $id }}">
    <i class="far fa-fw fa-trash-alt"></i>
</button>

<!-- Modal -->
<div class="modal fade" id="deleteModal-{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Are you sure?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ $message }}
            </div>
            <div class="modal-footer">
                <form action="{{ $action }}" method="post" class="d-inline-block">
                    @method('delete')
                    @csrf
                    <button type="submit" class="btn btn-link text-danger"><i class="far fa-fw fa-trash-alt"></i> Delete</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>