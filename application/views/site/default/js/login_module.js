$(document).ready(function () {
    $("#mod_login_form").submit(function () {
        var login_form = $(this);
        if($.valid(login_form.find("input[name=username]"), {
                required: "Bạn chưa nhập tên.",
                "max_length[60]": "Tên đăng nhập phải ngắn hơn 60 ký tự.",
                alpha_dash: "Tên đăng nhập không được chứa các ký tự đặc biệt."
            }, {
                type: "tooltip-icon",
                placement: "right",
                trigger: "hover focus",
                class: {input: "invalid", label: "invalid", invalid: "inner"}
            }) &
            $.valid(login_form.find("input[name=password]"), {
                required: "Bạn chưa nhập mật khẩu."
            }, {
                type: "tooltip-icon",
                placement: "right",
                trigger: "hover focus",
                class: {input: "invalid", label: "invalid", invalid: "inner"}
            })
            ) {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: this.action,
                data: login_form.serialize(),
                beforeSend: function() {
                    $.alert("ajax_loading", null, 'Đang đăng nhập...', 0);
                },
                success: function (data) {
                    switch (data.status) {
                        case 1:
                            location.reload();
                            break;
                        case 2:
                            $.alert(null, "Cảnh báo!", data.message, "alert", 0, null, [
                                {
                                    label: "Tải lại",
                                    click: function() {
                                        this.alertEnd();
                                        location.reload();
                                    },
                                    htmlAttributes: {class: "btn"}
                                },
                                {
                                    label: "Không tải",
                                    click: function() {
                                        this.alertEnd();
                                    },
                                    htmlAttributes: {class: "btn"}
                                }
                            ]);
                            break;
                        default:
                            if(data.message)
                                $.alert(null, null, data.message, "alert");
                            else
                                $.alert(null, null, "Lỗi đăng nhập. Vui lòng đăng nhập lại.", "alert");
                    }
                }
            });
        }
        return false;
    });

    $("#mod_register_form").submit(function () {
        var register_form = $(this);
        if(
            $.valid(register_form.find("input[name=username]"), {
                required: "Bạn chưa nhập tên.",
                "max_length[60]": "Tên đăng nhập phải ngắn hơn 60 ký tự.",
                alpha_dash: "Tên đăng nhập không được chứa<br/>các ký tự đặc biệt."
            }, {
                type: "tooltip-icon",
                placement: "right",
                trigger: "hover focus",
                class: {input: "invalid", label: "invalid", invalid: "inner"}
            }) &
            $.valid(register_form.find("input[name=email]"), {
                required: "Bạn chưa nhập email.",
                "max_length[120]": "Email phải ngắn hơn 120 ký tự.",
                valid_email: "Email không đúng định dạng<br/>(email@domain.abc)."
            }, {
                type: "tooltip-icon",
                placement: "right",
                trigger: "hover focus",
                class: {input: "invalid", label: "invalid", invalid: "inner"}
            }) &
            $.valid(register_form.find("input[name=password]"), {
                required: "Bạn chưa nhập mật khẩu."
            }, {
                type: "tooltip-icon",
                placement: "right",
                trigger: "hover focus",
                class: {input: "invalid", label: "invalid", invalid: "inner"}
            }) &
            $.valid(register_form.find("input[name=password2]"), {
                required: "Bạn chưa nhập xác nhận mật khẩu.",
                "matches[#mod_register_form input[name=password]]": "Xác nhận mật khẩu không giống<br/>mật khẩu."
            }, {
                type: "tooltip-icon",
                placement: "right",
                trigger: "hover focus",
                class: {input: "invalid", label: "invalid", invalid: "inner"}
            })
        ) {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: this.action,
                data: register_form.serialize(),
                beforeSend: function() {
                    $.alert("ajax_loading", null, 'Đang đăng ký...', 0);
                },
                success: function (data) {
                    switch (data.status) {
                        case 1:
                            $.alert(null, null, data.message, "success");
                            var login_form = $("#mod_login_form");
                            login_form.parents("dropdown-inline").first().addClass("active");
                            login_form.find("input[name=username]").val(register_form.find("input[name=username]").val()).focus();
                            login_form.find("input[name=password]").val(register_form.find("input[name=password]").val());
                            register_form.find("input").val("");
                            break;
                        case 2:
                            $.alert(null, "Cảnh báo!", data.message, "alert", 0, null, [
                                {
                                    label: "Tải lại",
                                    click: function() {
                                        this.alertEnd();
                                        location.reload();
                                    },
                                    htmlAttributes: {class: "btn"}
                                },
                                {
                                    label: "Không tải",
                                    click: function() {
                                        this.alertEnd();
                                    },
                                    htmlAttributes: {class: "btn"}
                                }
                            ]);
                            break;
                        default:
                            if(data.message)
                                $.alert(null, null, data.message, data.type?data.type:"alert", 0);
                            else
                                $.alert(null, null, "Lỗi đăng ký. Vui lòng đăng ký lại.", "alert");
                            if(data.js) eval(data.js);
                    }
                }
            });
        }
        return false;
    });
});