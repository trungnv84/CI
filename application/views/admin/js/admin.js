Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
    var n = this,
        decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSeparator = decSeparator == undefined ? "." : decSeparator,
        thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
        sign = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};

var admin = {
    dynamic_select: function(parent, child, url) {
    },
    dynamic_order_select: function(parent, child, url) {
        var parent = $("#"+parent);
        var child = $("#"+child);
        parent.change(function(){
            $.get(url, {
                parent_id: parent.val()
            }, function(data){
                if(data && data.status==1) {
                    var old_ordering = parseInt($("#old_ordering").val());
                    child.find("option").remove();
                    for(var k in data.orderings) {
                        child.append("<option value='"+ data.orderings[k].ordering+ "'"+
                            (old_ordering==data.orderings[k].ordering?"selected='selected'":"")+
                            ">" + (parseInt(k)+1)+ ". "+ data.orderings[k].name+ "</option>");
                    }
                    if(data.orderings.length>0) {
                        child.prepend("<option value='"+ data.orderings[0].ordering+ "'>- Đầu tiên -</option>");
                        child.append("<option value='"+ (parseInt(data.orderings[data.orderings.length-1].ordering)+1)+
                            "'"+ (old_ordering!=child.val()?" selected='selected'":"")+ ">- Cuối cùng -</option>");
                    } else {
                        child.append("<option value='"+ (data.orderFirst?data.orderFirst:1)+ "'>- Đầu tiên -</option>");
                    }
                }
            }, 'json');
        });
    },
    invalidEmail: function(email) {
        return /^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i.test(email);
    },
    invalid: function(el, msg, input) {
        if(!input || input == undefined || input.length==0) input = el;
        var aim =  el.next(".admin_invalid_message").first();
        if(aim.length==0) {
            el.after("<div class='admin_invalid_message'>"+ msg+ "</div>");
        } else aim.show();
        if(input.prop("data-invalid")!=1) {
            input.prop("data-invalid", 1);
            input.change(function(){
                $("label[for="+ input.attr("id")+ "]").removeClass("red");
                var aim =  el.next(".admin_invalid_message").first();
                aim.hide();
            });
        }
        $("label[for="+ input.attr("id")+ "]").addClass("red");
    },
    elemFocus: false,
    autoFocus: function(elem) {
        var focused = $("*:focus");
        if(focused.length>0) {
            if($.inArray(focused.prop("tagName"), ["INPUT", "TEXTAREA", "SELECT", "BUTTON", "A"]))
                return;
        }
        if(!elem) elem = admin.elemFocus;
        if(!elem) elem = "input[type!=hidden],textarea,select";
        if(elem) {
            elem = $(elem).first();
            if(elem.length>0)
                elem.focus();
        }
    }
};

$(document).ready(function() {
    //if(window.name=="") window.name = "w"+$.now();
    admin.baseUrl = $("base").first().attr("href");
    var admin_top_panels = $("#admin_top_panels");
    if(admin_top_panels.length>0) {
        var top_panel_wrap = admin_top_panels.parent("#top_panel_wrap");
        if(top_panel_wrap.length==0) {
            top_panel_wrap = $("<div id='top_panel_wrap'></div>");
            top_panel_wrap.height(admin_top_panels.outerHeight(true));
            admin_top_panels.wrap(top_panel_wrap);
            admin_top_panels.width(admin_top_panels.width());
        }
        $(window).scroll(function (event) {
            var eTop = $("#top_panel_wrap").offset().top;
            var wTop = $(this).scrollTop();
            if(wTop>eTop) {
                admin_top_panels.addClass("position_fixed_top");
            } else {
                admin_top_panels.removeClass("position_fixed_top");
            }
        });
    }
    var filter_search = $("#filter_search");
    if(filter_search.length>0 && filter_search.attr("value")!="") {
        filter_search.keyup(function(event){
            if(event.which==27) {
                filter_search.val("");
                $('#admin_form').submit();
            }
        });
    }
    $("#admin_check_all").click(function(){
        $("input[name^=cid]").prop("checked", ($(this).prop("checked")));
    });
    $("a.admin_submit_button").click(function(event){
        event.preventDefault();
        if(admin.validate && !admin.validate($(this)))
            return false;
        $("#admin_form").attr("action", $(this).attr("href")).submit();
        return false;
    });
    $("form.admin_form_validate").submit(function(event){
        if(admin.validate && !admin.validate($(this)))
            event.preventDefault();
    });
    $(".alert_flat_close_button").click(function(){
        $(this).parent().slideUp("", function () {
            $(this).remove();
        });
    });
    admin.autoFocus();
    var admin_label = $("#admin_form").find(".admin_label").first();
    admin_label.width(admin_label.width());
});