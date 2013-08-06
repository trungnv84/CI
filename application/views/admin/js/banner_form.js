admin.elemFocus = "#name";
$(document).ready(function() {
    $("#cat_id").change(function(){
        $("#branch").val($(this).find("option:selected").first().attr("data-branch"));
    });

    admin.dynamic_order_select('cat_id', 'ordering', 'banner_Controller/get_ordering');

    $("#start_date, #end_date").datepicker({
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
    $("#non_end").click(function(){
        $("#end_date").val("");
    });

    $("#cost").keyup(function(){
        var self = $(this);
        var str = $.trim(self.val());
        if(str!="" && !/\D+/.test(str)) {
            str = parseInt(str);
            if(str>0) $("#cost_text").html(" = "+ str.formatMoney(0, ".", ",")+ "đ");
            else $("#cost_text").html("");
        } else $("#cost_text").html("");
    }).change(function(){
        $(this).trigger("keyup");
    });

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
        var el = $("#cost");
        var str = $.trim(el.val());
        if(str!="" && /\D+/.test(str)) {
            admin.invalid($("#cost_text"), "Bạn phải nhập chi phí là số.", el);
            result = false;
        }
        var el = $("#cat_id");
        var str = el.val();
        if(!str || str=="" || str=="0" || str==0) {
            admin.invalid(el, "Bạn phải chọn một nhóm cho banner.");
            result = false;
        }
        return result;
    };
});