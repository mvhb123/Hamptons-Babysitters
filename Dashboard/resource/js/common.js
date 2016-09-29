
var default_user_image = base_url + 'theme/images/avtaar.png';

$(document).ready(function () {


});

var call_back_function_temp;
function common_call_back(obj, _this) {
    if (typeof window[call_back_function_temp] === "function") {
        window[call_back_function_temp](_this); //To call the function dynamically!
    }
}

function random_string(length, current) {
    current = current ? current : '';
    return length ? rand(--length, "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz".charAt(Math.floor(Math.random() * 60)) + current) : current;
}
function get_now() {
    var d = new Date();
    return d.getHours() + "" + d.getMinutes() + "" + d.getSeconds() + d.getMilliseconds();
    ;
}

function array_decode(str) {
    var str_json = $.base64.decode(str);
    return $.parseJSON(str_json);
}

function array_encode(array1) {
    var str_json = JSON.stringify(array1);
    return $.base64.encode(str_json);
}
function class_focus(class_name) {
    $('html, body').animate({
        scrollTop: $('.' + class_name).offset().top - 200
    }, 'slow');

}
function this_focus(_this) {
    $('html, body').animate({
        scrollTop: _this.offset().top - 200
    }, 'slow');

}
function countChar(_this, id, max) {
    el = _this;
    if (el.val().length >= max) {
        el.val(el.val().substr(0, max));

        $("#" + id).text(max)
    } else {
        $("#" + id).text(el.val().length);
    }

}

function numeric_representation(str) {
    return str.replace(/[^\d.-]/g, '');
}

function ConvertNumber(number, decimals, dec_point, thousands_sep) {

    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };

    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);

}

function redirect(url) {
    window.location.href = url;
}

function redirect_newtab(url) {
    window.open(url, '_blank');
}

function redirect_delay(url, milisecondss) {
    setTimeout(function () {
        window.location = url;
    }, milisecondss);
}

var _temp;

function before_common_process_theme(type, _this) {
    _temp = _this;
    if (type == 'start') {
        _this.parents('._parent_div').find('._child_span').addClass('fa fa-spin fa-spinner');

    } else {
        _this.parents('._parent_div').find('._child_span').removeClass('fa fa-spin fa-spinner');
    }

    return false;
}
function before_common_process(type, _this,size ) {
    if (type == 'start') {
            _this.after('<i class="fa fa-spinner fa-spin _common_loading_ico '+size+'"></i>');

    } else {
        _this.parent().find('._common_loading_ico').remove();
    }

    return false;
}

function before_common_process_icon(type, _this) {
    if (type == 'start') {
        _this.addClass('fa-spinner').addClass(' fa-spin'); //'('<i class="fa fa-spinner fa-spin _common_loading_ico"></i>');
    } else {
        _this.removeClass('fa-spinner').removeClass('fa-spin');

    }

    return false;
}

function alert_notification(type,title, msg ) {

    toastr.options = {
        "closeButton": true,
        "debug": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "7000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    toastr[type](msg, title)
}

function setCookie(c_name, value, exdays) {
    if (exdays == '' || exdays == null || exdays == 'undefined')
        exdays = _styleExpiry;
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = c_name + "=" + c_value;
}

function getCookie(c_name) {
    var i, x, y, ARRcookies = document.cookie.split(";");
    for (i = 0; i < ARRcookies.length; i++) {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g, "");
        if (x == c_name) {
            return unescape(y);
        }
    }
}


var currentRequest = null;
var currentRequestType = null;
var lastcallurl = '';

function call_ajax_jquery_json_data_all(url, datastring, _this, method, async_type, data_type, before_call, function_name) {

    currentRequestType = method;
    if (typeof window[before_call] === "function")
        window[before_call]('start', _this); //To call the function dynamically!
    currentRequest = $.ajax({
        type: method,
        // POST or GET
        url: url,
        data: datastring,
        async: async_type,
        dataType: data_type,
        beforeSend: function () {
            if ((currentRequestType == 'GET') && currentRequest != null && lastcallurl == url ) {
                currentRequest.abort();
            }
        },
        success: function (data) {

            if (typeof window[before_call] === "function")
                window[before_call]('success', _this); //To call the function dynamically!
            var obj = data;

            if (obj['redirect'] && parseInt(obj['redirect']) == 1)
                redirect(base_url + '');
            else if (obj['function_type'] == "no_action") {
                //console.log('no action required.')
            } else if (typeof window[function_name] == "no_action") {
                //console.log('no action required.')
            } else if (typeof window[function_name] === "function") {
                window[function_name](obj, _this); //To call the function dynamically!
            } else {
                console.log('Please refresh page and try again.');
            }
            return false;

        },
        error: function (xhr, textStatus, errorThrown) {
            if (typeof window[before_call] === "function")
                window[before_call]('error', _this); //To call the function dynamically!
            
            if (typeof window[function_name] == "function") {
            	  window[function_name]('', _this); //To call the function dynamically!
            }
            
            console.log(textStatus);
            console.log(errorThrown);
            console.log(xhr);
            //   console.log('1Server is down, Please refresh page and try again ');
            console.log('Server is down, Please refresh page and try again ');
            return false;
        }
    });

    lastcallurl = url;
}

