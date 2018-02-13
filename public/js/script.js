$( document ).ready(function() {
    $('a.disabled').click(function(e) { 
      e.preventDefault(); 
    });
	
	$('a.link-confirm').on('click', function (e) {
		console.log(e);
        return confirm( 'Are you sure? Action:' + e.target.innerText );
    });
});	