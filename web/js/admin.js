$(document).ready (function(){

	removeAlerts();


});

function removeAlerts(){
	$("div.alert-dismissable").each(function( index ) {
		$( this ).fadeTo(2000, 500).delay(index * 1000).slideUp(500, function() {
			$(this).alert('close');
		})
	});
}

function deleteItem(url){
	if (confirm('Are you shoure you wont to delete this item?')){
		$.ajax({
			url: url,
			type: "DELETE",
			dataType: "json",
			success: function (result) {
			//	$('#table_container').prepend( result.data );
			//	removeAlerts();

				if(result.status == 'OK'){
					location.reload();
				}else{
					alert(result.error);
				}
			}
		});
	}
}

function deleteSelectedItems(url){
	var ids = [];
	var selected_ids = table.rows( '.selected' ).ids();

	for (index = 0; index < selected_ids.length; ++index) {
		ids.push(selected_ids[index].replace(/row_/, ''));
	}

	if(ids.length > 0){
		if(confirm('Are you shoure you wont to delete selected items?')){
			$.ajax({
				url: url,
				type: "DELETE",
				dataType: "json",
				data: {ids: ids},
				success: function (result) {
					if(result.status == 'OK'){
						location.reload();
					}else{
						alert(result.error);
					}
				}
			});
		}
	}else{
		alert('You must select at last 1 item to delete!');
	}


}


function moveItem(url, direction){
	$.ajax({
		url: url,
		type: "PUT",
		dataType: "json",
		data: {direction: direction},
		success: function (result) {
			//	$('#table_container').prepend( result.data );
			//	removeAlerts();

			if(result.status == 'OK'){
				location.reload();
			}else{
				alert(result.error);
			}
		}
	});
}

function updateRoles(url, role, action){

	var type;
	switch(action){
		case 'add'   : type = "PUT"   ; break;
		case 'remove': type = "DELETE"; break;

		default: return false;
	}

	$.ajax({
		url: url,
		type: type,
		dataType: "json",
		data: {role: role},
		success: function (result) {
			//$('#table_container').prepend( result.data );
			//removeAlerts();

			if(result.status == 'OK'){
				location.reload();
			}else{
				alert(result.error);
			}

		}
	});
}