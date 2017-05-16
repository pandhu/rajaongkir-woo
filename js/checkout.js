$(document).ready(initialize);

function initialize(){
    $('#billing_city').addClass('bs-select').attr('data-mobile', 'true');
    $('#billing_district').addClass('bs-select').attr('data-mobile', 'true');
}

$('#billing_state').on('change', function(){
    console.log('yeay');
});
