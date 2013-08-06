$(document).ready(function () {
    $(".dropdown-inline").find("input, select, button").focus(function(){
        $(this).parents(".dropdown-inline").first().prop("active", true).addClass("active");
    }).blur(function(){
        var dropdown_inline = $(this).parents(".dropdown-inline").first().prop("active", false);
        setTimeout(function(){
            if(!dropdown_inline.prop("active"))
                dropdown_inline.removeClass("active");
        }, 200);
    });
});