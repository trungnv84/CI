if (jQuery && !jQuery.valid) {
    jQuery.extend({
        valid: function (input, rules, options) {
            options = $.extend({
                type: "inline",
                placement: "after",
                trigger: "manual",
                selector: false,
                class: {input: "invalid", label: "invalid", invalid: ""},
                invalidAttributes: false
            }, options);
            var invalid = 0;
            var messages = [];
            input.removeClass(options.class.input).tooltip('destroy');
            var value = $.trim(input.val());
            var input_id = /^[^\[]*/i.exec(input.first().prop("name"))[0];
            var label = input.first().parents("form").find("label[for=" + input_id + "]");
            if (label.length == 0) label = input.first().parents("label").first();
            label.removeClass(options.class.label);
            var invalid_id = input_id + "_invalid";
            var invalid_tag = input.first().parents("form").find("." + invalid_id).removeClass("visible").tooltip('destroy');

            for (var rule in rules) {
                switch (rule) {
                    case "required":
                        if (input.first().prop("tagName") == "INPUT" && $.inArray(input.first().prop("type"), ["checkbox", "radio"]) != -1) {
                            if (input.is(":checked")) continue;
                        } else {
                            if (value != "") continue;
                        }
                        break;
                    case "email":
                    case "valid_email":
                        var regex = /^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i;
                        if (regex.test(value)) continue;
                        break;
                    case "alpha":
                        var regex = /^([a-z])+$/i;
                        if (regex.test(value)) continue;
                        break;
                    case "alpha_numeric":
                        var regex = /^([a-z0-9])+$/i;
                        if (regex.test(value)) continue;
                        break;
                    case "alpha_dash":
                        var regex = /^([-a-z0-9_-])+$/i;
                        if (regex.test(value)) continue;
                        break;
                    case "numeric":
                        var regex = /^[\-+]?[0-9]*\.?[0-9]+$/;
                        if (regex.test(value)) continue;
                        break;
                    case "is_numeric":
                        if ($.isNumeric(value)) continue;
                        break;
                    case "integer":
                        var regex = /^[\-+]?[0-9]+$/;
                        if (regex.test(value)) continue;
                        break;
                    case "decimal":
                        var regex = /^[\-+]?[0-9]+\.[0-9]+$/;
                        if (regex.test(value)) continue;
                        break;
                    case "is_natural":
                        var regex = /^[0-9]+$/;
                        if (regex.test(value)) continue;
                        break;
                    case "is_natural_no_zero":
                        var regex = /^[0-9]+$/;
                        if (regex.test(value) && value != 0) continue;
                        break;
                    case "valid_base64":
                        var regex = /[^a-zA-Z0-9\/\+=]/;
                        if (!regex.test(value)) continue;
                        break;
                    default:
                        var regex = /^([a-z_]+)\[(.+)\]$/i;
                        var regex = regex.exec(rule);
                        if (regex) {
                            switch (regex[1]) {
                                case "regex_match":
                                    eval("var regex = " + regex[2]);
                                    if (regex.test(value)) continue;
                                    break;
                                case "matches":
                                    if ($(regex[2]).val() == value) continue;
                                    break;
                                case "min_length":
                                    if (/^[0-9]+$/.test(regex[2]) && value.length >= parseInt(regex[2])) continue;
                                    break;
                                case "max_length":
                                    if (/^[0-9]+$/.test(regex[2]) && value.length <= parseInt(regex[2])) continue;
                                    break;
                                case "equal_length":
                                    if (/^[0-9]+$/.test(regex[2]) && value.length == parseInt(regex[2])) continue;
                                    break;
                                case "exact_length":
                                    if (/^[0-9]+$/.test(regex[2]) && value.length != parseInt(regex[2])) continue;
                                    break;
                                case "greater_than":
                                    if ($.isNumeric(regex[2]) && $.isNumeric(value) && parseFloat(value) > parseFloat(regex[2])) continue;
                                    break;
                                case "less_than":
                                    if ($.isNumeric(regex[2]) && $.isNumeric(value) && parseFloat(value) < parseFloat(regex[2])) continue;
                                    break;
                            }
                        }
                        break;
                }
                invalid++;
                messages.push(rules[rule]);
            }

            if (invalid > 0) {
                input.addClass(options.class.input);
                //var input = input.first();
                label.addClass(options.class.label);
                if (false === options.selector) options.selector = input;
                else options.selector = $(options.selector);
                switch (options.type) {
                    case "block":
                    case "inline":
                        if (invalid_tag.length == 0) {
                            invalid_tag = $("<span class='invalid-help visible help-" + options.type +
                                " " + invalid_id + " " + options.class.invalid + "'></span>");
                            if (options.invalidAttributes) invalid_tag.attr(options.invalidAttributes);
                            eval("options.selector." + options.placement + "(invalid_tag)");
                        }
                        invalid_tag.html(messages.join("<br/>")).addClass("visible");
                        break;
                    case "tooltip":
                        options.selector.tooltip({
                            title: messages.join("<br/>"),
                            placement: options.placement,
                            trigger: options.trigger,
                            html: true
                        });
                        if (options.trigger == "manual") options.selector.tooltip("show");
                        break;
                    case "tooltip-icon":
                        if (invalid_tag.length == 0) {
                            invalid_tag = $("<i class='icon-help visible placement-" + options.placement +
                                " " + invalid_id + " " + options.class.invalid + "'></i>")
                            if (options.invalidAttributes) invalid_tag.attr(options.invalidAttributes);
                            options.selector.after(invalid_tag);
                            input.unbind("input").bind("input", function(){
                                $.valid(input, rules, options);
                            });
                        }
                        invalid_tag.removeClass("valid").addClass("visible").tooltip({
                            title: messages.join("<br/>"),
                            placement: options.placement,
                            trigger: options.trigger,
                            html: true
                        });
                        if (options.trigger == "manual") invalid_tag.tooltip("show");
                        else input.focus(function () {
                            invalid_tag.tooltip("show");
                        }).blur(function () {
                            invalid_tag.tooltip('hide');
                        });
                        break;
                }
            } else if (options.type == "tooltip-icon" && invalid_tag.length > 0) {
                invalid_tag.addClass("valid");
            }
            return 0 == invalid;
        }
    });
}