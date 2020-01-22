$( document ).ready(function() {
    $('a.disabled').click(function(e) { 
      e.preventDefault(); 
    });
	
	$('a.link-confirm').on('click', function (e) {
		console.log(e);
        return confirm( 'Are you sure? Action:' + e.target.innerText );
    });

    bsCustomFileInput.init();
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