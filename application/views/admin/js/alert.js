if (jQuery && !jQuery.alert) {
    jQuery.extend({
        alerts: [],
        alert: function (key, title, message, type, display, wrapper, buttons, events) {
            if (!key || !$.alerts[key]) {
                var alert = $("<div class='alert_box alert_box_" + type + "'>" +
                    (title ? "<div class='alert_title alert_title_" + type + "'>" + title + "</div>" : "") +
                    "<div class='alert_message alert_message_" + type + "'>" + message + "</div></div>");
                if (key) {
                    $.alerts[key] = alert;
                }
                var alertEnd = function () {
                    if (display) {
                        clearTimeout(endTimer);
                    }
                    if (title && overlay.length > 0) {
                        overlay.clearQueue().stop(true, true).animate({opacity:0}, "", function () {
                            $(this).css({opacity:0.7}).hide();
                        });
                    }
                    if (!title || wrapper) {
                        alert.clearQueue().stop(true, true).slideUp("", function () {
                            $(this).remove();
                        });
                    } else {
                        alert.clearQueue().stop(true, true).fadeOut("", function () {
                            $(this).remove();
                        });
                    }
                    if (key && $.alerts[key]) {
                        $.alerts[key] = false;
                    }
                    if (events && events.hide) {
                        events.hide.call(alert);
                    }
                };
                alert.alertEnd = alertEnd;
                var closeBtn = $("<div class='alert_close_button' title='Close'>x</div>");
                closeBtn.click(alertEnd);
                alert.prepend(closeBtn);
                if (buttons) {
                    var buttons_wrapper = $("<div class='alert_buttons'/>");
                    for (var k in buttons) {
                        var button = $("<button type='button'>" + buttons[k].label + "</button>");
                        if(buttons[k].htmlAttributes)
                            button.attr(buttons[k].htmlAttributes);
                        if (buttons[k].click) {
                            button.prop("btnIndex", k);
                            button.click(function () {
                                buttons[$(this).prop("btnIndex")].click.call(alert);
                            });
                        } else {
                            button.click(alertEnd);
                        }
                        buttons_wrapper.append(button);
                    }
                    alert.append(buttons_wrapper);
                }
                if (wrapper) {
                    var wp = $(wrapper);
                    closeBtn.addClass("alert_flat_close_button");
                } else {
                    var wp = (title ? $("#popup_wrapper") : $("#alert_wrapper"));
                    if (wp.length == 0) {
                        if (title) {
                            wp = $("<div id='popup_wrapper' class='popup_wrapper' align='center'><div class='popup_overlay'></div></div>");
                        } else {
                            wp = $("<div id='alert_wrapper' class='alert_wrapper' align='center'/>");
                        }
                        $("body").prepend(wp);
                    }
                }
                var overlay = wp.find("div.popup_overlay").first();
                if (title) {
                    overlay.unbind();
                    overlay.click(function () {
                        if (!buttons) alertEnd();
                    });
                }
                if (!title || wrapper) {
                    wp.prepend(alert);
                } else {
                    wp.append(alert);
                }
                if (!$.isNumeric(display)) {
                    display = 5000;
                }
                if (display) {
                    var endTimer = setTimeout(alertEnd, display);
                    alert.mouseenter(function(){
                        clearTimeout(endTimer);
                    }).mouseleave(function(){
                        endTimer = setTimeout(alertEnd, display);
                    });
                }
                if (!title || wrapper) {
                    if (!wrapper && navigator.appVersion.indexOf("MSIE 7.") != -1) {
                        var width = alert.find("div.alert_message").first().text().length * 6;
                        var lineHeight = parseInt(alert.css("line-height")) * 1.5;
                        do {
                            alert.css("width", width);
                            width += 5;
                        } while (alert.height() > lineHeight);

                    }
                    alert.hide();
                    alert.slideDown();
                } else {
                    var top = parseInt(($(window).height() - alert.height()) / 3);
                    var left = parseInt(($(window).width() - alert.width()) / 2);
                    alert.css({top:top, left:left});
                    alert.hide();
                    alert.fadeIn("slow");
                    overlay.clearQueue().stop(true, true).css({opacity:0}).show().animate({opacity:0.7});
                }
                if (events && events.show) {
                    events.show.call(alert);
                }
                return alert;
            } else return $.alerts[key];
        }
    });
}

$(document).ready(function () {
    $(document).ajaxStop(function () {
        if ($.alerts["ajax_loading"])
            $.alerts["ajax_loading"].alertEnd();
    });
});