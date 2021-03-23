$( document ).ready(function() {
    $('a.disabled').click(function(e) { 
      e.preventDefault(); 
    });
	
	$('.link-confirm').on('click', function (e) {
		console.log(e);
        return confirm( 'Are you sure? Action:' + e.target.innerText );
    });

	// Setup menu collapse
	$menu_collapsibles = $('#app-header .navbar .logo, #app-header .navbar .logo-collapse, #app-header .navbar .link-text, .nav-heading');
	$collapse_link = $('a.nav-collapse-link');
	$collapse_icon = $collapse_link.find('i');
	$collapse_link.on('click', function(e){
	    // Swap logo and toggle text
        $menu_collapsibles.toggle();

        // Toggle expand/collapse icon
        $collapse_icon.toggleClass('fa-caret-square-left fa-caret-square-right');

	    e.preventDefault();
    });

	// Set initial menu state
    if( $('.logo-collapse').is(':visible') ) {
        $collapse_icon.toggleClass('fa-caret-square-left fa-caret-square-right');
    }

    // Setup mobile menu collapse
    $('.mobile-nav-collapse-link').on('click', function (e) {
        //$menu_collapsibles.toggle();
        $('#app-header').toggleClass('fullscreen').toggle();

        e.preventDefault();
    });

    bsCustomFileInput.init();

    // Title-Slug generation
    let $title = $('.form-group #title');
    let $slug = $('.form-group #slug');
    $title.change(function(){
        $slug.val( toSlug( $title.val() ) );
    });


    // Run Select2
    const URL_USERS = '/users/find';
    const URL_ROLES = '/users/roles/find';
    const URL_PARTS = '/users/voice-parts/find'
    const URL_SINGER_CATS = '/users/singer-categories/find'
    const SELECT2_CONFIG = {
        placeholder: "Start typing...",
        minimumInputLength: 2,
        ajax: {
            url: '', // Set a moment later
            dataType: 'json',
            data: function (params) {
                return {
                    q: $.trim(params.term),
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        theme: "bootstrap4"
    };

    SELECT2_CONFIG.ajax.url = URL_USERS;
    $('.select2[data-model=users]').select2(SELECT2_CONFIG);

    SELECT2_CONFIG.ajax.url = URL_ROLES;
    $('.select2[data-model=roles]').select2(SELECT2_CONFIG);

    SELECT2_CONFIG.ajax.url = URL_PARTS;
    $('.select2[data-model=voice_parts]').select2(SELECT2_CONFIG);

    SELECT2_CONFIG.ajax.url = URL_SINGER_CATS;
    $('.select2[data-model=singer_categories]').select2(SELECT2_CONFIG);


    /**
     * Conditional Fields
     */

    // Hide Senders unless the list type is Mailout/Distribution
    const $senders = $('#senders')
    const $type_distribution = $('#list_type_distribution');

    // Check initial state on page load
    if( ! $type_distribution[0]?.checked ) {
        $senders.hide();
    } else {
        $senders.show();
    }

    // Set enable event handler
    $type_distribution.change(function(){
        if( this.checked ) {
            $senders.show();
        }
    });

    // Set disable event handler
    $('#list_type_chat, #list_type').change(function(){
        if( this.checked ) {
            $senders.hide();
        }
    });

    /**
     * CREATE/EDIT ROLE
     * Select All/None
     */
    $('#role-abilities-table .check-all').change(function(){
        $(this).parents('tr').find('input[type=checkbox]').prop('checked', $(this).prop('checked'));
    });

    /**
     * Recurring Events
     */
    const $is_repeating = $('#is_repeating');
    const $repeatDetails = $('#repeat_details');
    
    // Set initial state
    if(! $is_repeating[0].checked) {
        $repeatDetails.hide();
    } else {
        $repeatDetails.show();
    }
    // Set event handler
    $is_repeating.change(function () {
        if(this.checked) {
            $repeatDetails.show();
        } else {
            $repeatDetails.hide();
        }
    });

});


// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

/**
 * Converts a title to its matching slug
 * See https://stackoverflow.com/questions/1053902/how-to-convert-a-title-to-a-url-slug-in-jquery
 *
 * @param title
 * @returns {string} slug
 */
function toSlug(title) {
    return title
        .toLowerCase()
        .replace(/[^\w ]+/g,'')
        .replace(/ +/g,'-')
    ;
}