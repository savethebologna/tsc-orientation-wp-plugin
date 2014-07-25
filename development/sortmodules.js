jQuery(document).ready(function(){
	jQuery('.tscmod_sortable').sortable();
	jQuery('#submit').on('click', tscmodUpdateOptionsByPOST);
	function tscmodUpdateOptionsByPOST(){
		jQuery('#savestate').removeClass('hidden');
		var pathname = document.URL;
		var tscmodForm = document.createElement('form');
		tscmodForm.action = pathname;
		tscmodForm.method = 'post';
		tscmodForm.id = 'tscmod_hidden_order_form';
		var tscmodInput = [];
		jQuery('.tscmod_sortable > li').each(function(i,j){
			i++;
			jQuery(this).attr('data-modnumber', i);
			var modnumber = jQuery(this).attr('data-modnumber');
			var modid = jQuery(this).attr('data-modid');
			tscmodInput[i] = document.createElement('input');
			tscmodInput[i].type = 'hidden';
			tscmodInput[i].name = 'tscmodid_' + modid;
			tscmodInput[i].value = modnumber;
			tscmodForm.appendChild(tscmodInput[i]);
		});  
		try { document.getElementById('tscmod_hidden_order_form').remove(); } catch(err) {  } //do nothing if there's a problem
		document.getElementById('hidden_form_elements').appendChild(tscmodForm);
		var posting = jQuery.post( wp_tsc_object.saveurl, jQuery('#tscmod_hidden_order_form').serialize() );
		posting.done(function( data ) {
			var content = jQuery( data ).find('#saveresult');
			jQuery('#saving').empty().append(data);
			jQuery('#savestate > .spinner').addClass('hidden');
			jQuery('#savestate').fadeOut(5000, 'swing', function(){
				jQuery('#savestate').addClass('hidden');
				jQuery('#savestate > .spinner').removeClass('hidden');
				jQuery('#savestate').css('display','block');
				jQuery('#saving').empty().append('saving');
			});
		});
		posting.fail
	}
});