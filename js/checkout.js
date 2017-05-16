$(document).ready(initialize);

function initialize(){
    $('#billing_city').addClass('bs-select').attr('data-mobile', 'true');
    $('#billing_district').addClass('bs-select').attr('data-mobile', 'true');
}

$('#billing_state').on('change', function(){

});

$('#billing_state').on('change', function(){
		$('#city-loader').show();
		var idProvince = $('option:selected', this).val();
		var request = $.ajax({
			url: "/wordpress/wp-admin/admin-ajax.php",
			method: "POST",
			data: { action: "get_cities", idProvince : idProvince},
			dataType: "html"
		});
		request.done(function( msg ) {
			$( "#billing_city" ).html( msg );
			$( "#city" ).selectpicker('refresh');
			$('#city-loader').hide();
		});

		request.fail(function( jqXHR, textStatus ) {
			alert( "Request failed: " + textStatus );
		});
	});

	$('#billing_city').on('change', function(){
		$('#district-loader').show();
		var idCity = $('option:selected', this).val();
		var request = $.ajax({
            url: "/wordpress/wp-admin/admin-ajax.php",
			method: "POST",
			data: { action: "get_district", idCity : idCity},
			dataType: "html"
		});
		request.done(function( msg ) {
			$( "#billing_district" ).html( msg );
			$( "#district" ).selectpicker('refresh');
			$('#district-loader').hide();
		});

		request.fail(function( jqXHR, textStatus ) {
			alert( "Request failed: " + textStatus );
		});
	});
