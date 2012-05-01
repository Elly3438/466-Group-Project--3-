/** Javascript functions go in here **/

$(document).ready(function() {
	child_delete_clicked();
	inst_delete_clicked();
});

function child_delete_clicked(){
	$('[name="delete_child"]').click(function(){

		$('form#child-update').submit(function() {
			var answer = confirm('Are you sure you wish to delete this child?');
			
			if(answer){
				return true;
			}
			else{
				return false;
			}
		});
		$('[name="delete_child"]').unbind('click');
	});
}

function inst_delete_clicked(){
	$('[name="inst_cancel"]').click(function(){

		$('form#inst-cancel').submit(function() {
			var answer = confirm('Are you sure you wish to cancel this rental?');
			
			if(answer){
				alert('We will contact you shortly. Please return your instrument to the store.');
				return true;
			}
			else{
				return false;
			}
		});
		$('[name="inst_cancel"]').unbind('click');
	});
}