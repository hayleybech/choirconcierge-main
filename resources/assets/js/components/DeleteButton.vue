<template>
    <div class="delete-button">
        <!-- Button trigger modal -->
        <button type="button" :class="'btn btn-link text-danger '+paddingClass+' '+className" data-toggle="modal" data-target="#deleteModal">
            <i class="fas fa-fw fa-trash"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Are you sure?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ message }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <form :action="action" method="post" class="d-inline-block">
                            <input type="hidden" name="_method" value="delete">
                            <input type="hidden" name="_token" :value="csrf">

                            <button type="submit" class="btn btn-danger"><i class="fas fa-fw fa-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "DeleteButton",
        props: {
            action: {
                type: String,
                required: true
            },
            message: {
                type: String,
                default: 'Do you really want to delete this record?'
            },
            enablePadding: {
                type: Boolean,
                default: false
            },
            className: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        },
        computed: {
            paddingClass() {
                return this.enablePadding ? '' : 'p-0';
            }
        }
    }
</script>

<style scoped>

</style>