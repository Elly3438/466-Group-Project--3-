/** Javascript functions go in here **/

$(document).ready(function() {
	child_delete_clicked();
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