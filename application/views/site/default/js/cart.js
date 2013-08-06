$(document).ready(function () {
    $("#cart .cart_delete_product").click(function(){
        $(this).prev("input").val(0);
        $("#cart_form").submit();
    });
});