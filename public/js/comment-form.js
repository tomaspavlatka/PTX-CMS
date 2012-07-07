jQuery(document).ready(function() {
	jQuery(".comment-form-move").click(function() {
		var id_string = jQuery(this).attr('id');
		var id = id_string.substr(14);
		
		var form_data = '<div id="replyform">'+$("#replyform").html()+'</div>';
		
		$(".comment-container").html('');
		if(id != '') {
			jQuery("#comment-container-"+id).html(form_data);
			jQuery("#parent").val(id);
		} else {
			jQuery("#comment-container").html(form_data);
			jQuery("#parent").val('');
		}
		
		return false;
	});
});