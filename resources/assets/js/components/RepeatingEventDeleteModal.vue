<template>
    <div class="modal fade" id="repeatingEventDeleteModal" tabindex="-1" role="dialog" aria-labelledby="repeatingEventDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="repeatingEventDeleteModalLabel">Delete repeating event <small class="text-muted">{{ eventId }}</small></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <fieldset class="form-group">
                        <legend class="col-form-label">The event "{{ eventTitle }}" is a repeating event. What do you want to delete?</legend>

                        <div id="delete_mode" class="btn-group-vertical btn-group-toggle d-flex bg-white" data-toggle="buttons">

                            <label for="delete_mode_single" class="btn btn-outline-dark btn-radio py-3 px-3 text-left d-flex align-items-center active">
                                <i class="far fa-fw fa-calendar-day fa-2x mr-3"></i>
                                <span>
                                      <input id="delete_mode_single" name="delete_mode" value="single" type="radio" autocomplete="off" v-model="deleteMode">
                                      <span class="h5">Only this event</span>
                                      <span class="form-text">
                                          All other events in the series will remain the same.
                                      </span>
                                  </span>
                            </label>

                            <label for="delete_mode_following" :class="'btn btn-outline-dark btn-radio py-3 px-3 text-left d-flex align-items-center ' + followingModeDisabledClass">
                                <i class="far fa-fw fa-calendar-week fa-2x mr-3"></i>
                                <span>
                                    <input id="delete_mode_following" name="delete_mode" value="following" type="radio" autocomplete="off" v-model="deleteMode" :disabled="followingModeDisabled">
                                    <span class="h5">Following events</span>
                                    <span class="form-text">
                                        This and all the following events will be deleted.<br>
                                        <strong>Any changes to future events will be lost, including RSVPs.</strong>
                                    </span>
                                    <span class="form-text text-danger" v-if="eventInPast">This option affects events in the past. To protect attendance data, you cannot bulk delete past events. Please delete individually instead.</span>
                                </span>
                            </label>

                            <label for="delete_mode_all" :class="'btn btn-outline-dark btn-radio py-3 px-3 text-left d-flex align-items-center ' + allModeDisabledClass">
                                <i class="far fa-fw fa-calendar-alt fa-2x mr-3"></i>
                                <span>
                                    <input id="delete_mode_all" name="delete_mode" value="all" type="radio" autocomplete="off" v-model="deleteMode" :disabled="allModeDisabled">
                                    <span class="h5">All events</span>
                                    <span class="form-text">
                                        All events in the series will be deleted.<br>
                                        <strong>Any changes to other events will be lost, including RSVPs and attendance records.</strong>
                                    </span>
                                    <span class="form-text text-danger" v-if="parentInPast">This option affects events in the past. To protect attendance data, you cannot bulk delete past events. Please delete individually instead.</span>
                                </span>
                            </label>

                        </div>

                    </fieldset>

                </div>

                <div class="modal-footer">
                    <a :href="url" class="btn btn-primary"><i class="far fa-fw fa-check"></i> Start</a>
                    <button type="button" class="btn btn-link text-danger" data-dismiss="modal">Cancel</button>
                </div>

            </div>

        </div>
    </div>
</template>

<script>
export default {
    name: "RepeatingEventDeleteModal",
    data() {
        return {
            route: '#',
            eventId: '#',
            eventTitle: 'Unknown',
            eventInPast: false,
            eventIsParent: false,
            parentInPast: false,
            deleteMode: 'single',
        }
    },
    computed: {
        url() {
            let filteredDeleteMode = this.deleteMode;
            if( filteredDeleteMode === 'following' && this.eventIsParent) {
                filteredDeleteMode = 'all';
            }
            return this.route.replace('--replace--', filteredDeleteMode);
        },
        followingModeDisabled() {
            // here, we're NOT disabling if the event is the parent, but as shown in the url() method above, we ARE simply swapping the URL to the 'all' one.
            return this.eventInPast;
        },
        followingModeDisabledClass() {
            return this.followingModeDisabled ? 'disabled' : '';
        },
        allModeDisabled() {
            return this.parentInPast;
        },
        allModeDisabledClass() {
            return this.allModeDisabled ? 'disabled' : '';
        }
    },
    mounted: function () {
        this.$root.$on('repeating-event-delete-button-clicked', data => {
            this.route = data.route;
            this.eventId = data.eventId;
            this.eventTitle = data.eventTitle;
            this.eventInPast = data.eventInPast;
            this.eventIsParent = data.eventIsParent;
            this.parentInPast = data.parentInPast;
        });
    },
    beforeDestroy: function () {
        this.$root.$off('repeating-event-delete-button-clicked');
    },
}
</script>

<style scoped>

</style>