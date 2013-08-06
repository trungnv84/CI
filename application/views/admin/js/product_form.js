admin.elemFocus = "#code";
$(document).ready(function() {
    $("#cat_ids").change(function(){
        var branch = '';
        $(this).find("option:selected").each(function () {
            branch += $(this).attr("data-branch");
        });
        $("#branches").val(branch.substr(1));
    });

    $("#start_date, #end_date, #start, #expire").datepicker({
        showOtherMonths: true,
        showButtonPanel: true,
        selectOtherMonths: true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 2,
        dateFormat: "dd-mm-yy",
        /*showOn: "both",
         buttonText: "Chọn",*/
        showWeek: true,
        firstDay: 1
    });
    $("#start_date").datepicker("option", "onClose",
        function( selectedDate, inst ) {
            $( "#end_date" ).datepicker( "option", "minDate", new Date(inst.currentYear, inst.currentMonth, parseInt(inst.currentDay) + 1 ));
        }
    );
    $("#start").datepicker("option", "onClose",
        function( selectedDate, inst ) {
            $( "#expire" ).datepicker( "option", "minDate", new Date(inst.currentYear, inst.currentMonth, parseInt(inst.currentDay) + 1 ));
        }
    );

    $("#non_end").click(function(){
        $("#end_date").val("");
    });
    $("#non_expire").click(function(){
        $("#expire").val("").trigger("keyup");
    });

    $("#expire").keyup(function(){
        var self = $(this);
        var str = $.trim(self.val());
        var expire_text = $("#expire_text");
        if(str!="" && /^\d{1,2}-\d{1,2}-\d{4}$/.test(str)) {
            str = $.datepicker.parseDate( "dd-mm-yy", str );
            var today = new Date();
            if(str<today) {
                expire_text.html("Đã hết hạn");
                expire_text.addClass("blue");
            }
            else {
                expire_text.html("");
                expire_text.removeClass("blue");
            }
        } else if(str!="") {
            expire_text.html("Ngày hết hạn không đúng định dạng (d-m-yyyy).");
            expire_text.removeClass("blue");
            $("label[for=expire]").addClass("red");
        } else {
            expire_text.html("");
            expire_text.removeClass("blue");
            $("label[for=expire]").removeClass("red");
        }
    }).change(function(){
            $(this).trigger("keyup");
        }).datepicker("option", "onClose",
        function( selectedDate, inst ) {
            $(this).trigger("keyup");
        }
    ).trigger("keyup");

    $("#price").keyup(function(){
        var self = $(this);
        var str = $.trim(self.val());
        if(str!="" && !/\D+/.test(str)) {
            str = parseInt(str);
            if(str>0) $("#price_text").html(" = "+ str.formatMoney(0, ".", ",")+ "đ");
            else $("#price_text").html("");
        } else $("#price_text").html("");
        $("#discount").trigger("keyup");
    }).change(function(){
        $(this).trigger("keyup");
    }).trigger("keyup");

    $("#discount").keyup(function(){
        var self = $(this);
        var str = $.trim(self.val());
        if(str!="" && /^-?\d+$/.test(str)) {
            str = parseInt(str);
            if(str>0 && str>100) $("#discount_text").html(" Giá bán = "+ str.formatMoney(0, ".", ",")+ "đ");
            else if(str>0) {
                var price = parseInt($("#price").val());
                if(!isNaN(price) || price) price = ", Giá bán = " + (price*(100-str)/100).formatMoney(0, ".", ",")+ "đ";
                else price = "";
                $("#discount_text").html(" KM = "+ str+ "%"+ price);
            }
            else if(str<0) {
                var price = parseInt($("#price").val());
                if(!isNaN(price) || price) price = ", Giá bán = " + (price+str).formatMoney(0, ".", ",")+ "đ";
                else price = "";
                $("#discount_text").html(" KM = "+ Math.abs(str).formatMoney(0, ".", ",")+ "đ"+ price);
            }
            else $("#discount_text").html("");
        } else $("#discount_text").html("");
    }).change(function(){
        $(this).trigger("keyup");
    }).trigger("keyup");

    $('textarea.tinymce').tinymce({
        // Location of TinyMCE script
        script_url : admin.baseUrl+ "application/third_party/tinymce/jscripts/tiny_mce/tiny_mce.js",

        // General options
        theme : "advanced",
        plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,imagemanager,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,undo,redo,link,unlink,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,image,media,advhr,|,forecolor,backcolor,|,removeformat,cleanup,code",
        theme_advanced_buttons2 : "tablecontrols,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons3 : "",
        theme_advanced_buttons4 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Example content CSS (should be your site CSS)
        content_css : admin.baseUrl+ "application/views/admin/css/content.css",

        // image manager path
        imagemanager_path : admin.imagemanager_path,

        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        convert_urls : true,
        relative_urls : true,
        document_base_url : admin.baseUrl,

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "tinymce/lists/template_list.js",
        external_link_list_url : "tinymce/lists/link_list.js",
        external_image_list_url : "tinymce/lists/image_list.js",
        media_external_list_url : "tinymce/lists/media_list.js"

        // Replace values for the template plugin
        /*template_replace_values : {
         username : "Some User",
         staffid : "991234"
         }*/
    });

    admin.validate = function() {
        var result = true;
        $("#start_date, #end_date").each(function(index, el){
            el = $(el);
            var str = $.trim(el.val());
            if(str!="" && !/^\d{1,2}-\d{1,2}-\d{4}$/.test(str)) {
                admin.invalid($("#non_end"), "Ngày tháng không đúng định dạng (d-m-yyyy).", el);
                result = false;
            }
        });
        var el = $("#name");
        var str = $.trim(el.val());
        if(str=="") {
            admin.invalid(el, "Bạn phải nhập tên.");
            result = false;
        }
        var el = $("#price");
        var str = $.trim(el.val());
        if(str!="" && /\D+/.test(str)) {
            admin.invalid($("#price_text"), "Bạn phải nhập giá là số.", el);
            result = false;
        }
        var el = $("#discount");
        var str = $.trim(el.val());
        if(str!="" && !/^-?\d+$/.test(str)) {
            admin.invalid($("#discount_text"), "Bạn phải nhập khuyến mãi là số.", el);
            result = false;
        }
        var el = $("#expire");
        var str = $.trim(el.val());
        if(str!="" && !/^\d{1,2}-\d{1,2}-\d{4}$/.test(str)) {
            result = false;
        }
        var el = $("#cat_ids");
        var str = el.find("option:selected");
        if(str.length==0) {
            admin.invalid(el, "Bạn phải chọn ít nhất một nhóm.");
            result = false;
        }
        return result;
    };
});