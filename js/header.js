$(document).ready(function(){
    $('#login_form').submit(function( event ) {
    	event.preventDefault();
    	
    	$.ajax({
			type: "POST",
			url: "/login.php",
			data: $('#login_form').serialize(),
			success: function(data){
				var result = $.parseJSON(data);
				
				 if(result.success){
					 location.reload(true);
                 } else {
                     alert(result.reason);
                 }
			},
			error: function(){
				alert("failure");
			}
		});
	});
});