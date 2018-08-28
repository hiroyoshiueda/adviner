if (typeof(console)=='undefined') {
	console={'dir':function(){},'log':function(){},'profile':function(){},'profileEnd':function(){}};
}
String.prototype.trim = function()
{
    return this.replace(/^[\s　]+|[\s　]+$/g, '');
}
String.prototype.escapeHTML = function()
{
    return this.replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}
String.prototype.nl2br = function()
{
	return this.replace(/\n/g, "<br />");
}
function debug(obj)
{
	var str = '';
	if (typeof obj === 'object') {
		for (var i in obj) {
			str += i + ' => ' + debug(obj[i]);
		}
	} else {
		str += obj + "\n";
	}
	return str;
}
function jump(url)
{
	window.location.href = url;
}
function openwin(url, winname)
{
	window.open(url,  winname);
}
function lengthCheck(show_id, limit, num)
{
	$(show_id).html(limit - num);
	if ((limit - num) < 0){
		$(show_id).css('color', '#ff0000');
	} else {
		$(show_id).css('color', '#4c4c4c');
	}
}
function trimZen(str)
{
	return (str+'').trim();
}
function escapeHTML(str)
{
    return (str+'').escapeHTML();
}
function slowScrollTop()
{
	$('html,body').animate({ scrollTop: 0 }, 'normal');
	return false;
}
function getBrowserWidth() {
    if ( window.innerWidth ) {
        return window.innerWidth;
    }
    else if ( document.documentElement && document.documentElement.clientWidth != 0 ) {
        return document.documentElement.clientWidth;
    }
    else if ( document.body ) {
        return document.body.clientWidth;
    }
    return 0;
}
function getBrowserHeight() {
    if ( window.innerHeight ) {
        return window.innerHeight;
    }
    else if ( document.documentElement && document.documentElement.clientHeight != 0 ) {
        return document.documentElement.clientHeight;
    }
    else if ( document.body ) {
        return document.body.clientHeight;
    }
    return 0;
}
function addFigure(str) {
	var num = new String(str).replace(/,/g, "");
	while(num != (num = num.replace(/^(-?\d+)(\d{3})/, "$1,$2")));
	return num;
}

var LOADER_Q = $('<div class="loader"></div>');
var AJAX_DEFAULT_TIMEOUT = 60000;

function ajaxPost(url, postData, succesFunc)
{
	$.ajax({
		'url': url,
		'type': 'POST',
		'dataType': 'json',
		'timeout': AJAX_DEFAULT_TIMEOUT,
		'cache': false,
		'data': postData,
		'success': succesFunc,
		'error': function(XMLHttpRequest, textStatus, errorThrown){
			//alert(textStatus+': '+errorThrown);
		},
		'statusCode': {
			404: function(){
				//alert('ページが見つかりません。: Not Found');
			}
		}
	});
}
function getFormData(idName)
{
	var data = {};
	var input_data = $("#" + idName + " :input");
	$.each(input_data, function(){
		if (this.name!='') {
			if (this.type == 'checkbox') {
				if (this.checked) {
					if (data[this.name]) {
						data[this.name] += ',' + this.value;
					} else {
						data[this.name] = this.value;
					}
				}
			} else {
				data[this.name] = this.value;
			}
		}
	});
	return data;
}
function showTopAlert(alertQ)
{
	if (alertQ.size()) {
		alertQ.slideDown('normal', function(){
			var timerID = setInterval(function(){
				clearInterval(timerID);
				alertQ.slideUp('normal');
			}, 5000);
		});
	}
}
function sendSideFeedback()
{
	var bodyQ = $('#side_feedback_body');
	var feedback_body = (bodyQ.val() || '').trim();
	if (feedback_body == '') {
		bodyQ.focus();
		return false;
	}

	$('#side_feedback_btn').css('display', 'none');
	$('#side_feedback_loader').css('display', 'inline-block');
	$('p.errormsg-text').remove();

	var url = $('#side_feedback_form').attr('action');
	var postData = {
		'body': feedback_body,
		'security_token' : $('#_security_token').val()
	};

	ajaxPost(url, postData, function(data, dataType){
		if (data.lists.result == 1) {
			bodyQ.css('display', 'none').val('');
			$('#side_feedback_loader').css('display', 'none');
			$('#side_feedback_success').fadeIn();
		} else if (data.lists.errmsg != '') {
			alert(data.lists.errmsg);
			if (data.lists.errors) {
				if (data.lists.errors.body) {
					var err = data.lists.errors.body.join("\n");
					bodyQ.after('<p class="errormsg-text">'+err+'</p>');
				}
			}
			$('#side_feedback_loader').css('display', 'none');
			$('#side_feedback_btn').css('display', 'inline-block');
		}
		if (data.lists.security_token) {
			$('#_security_token').val(data.lists.security_token);
		}
	});
}
function confirmUrl(url, msg)
{
	if (confirm(msg)) {
		window.location.href= url;
		return true;
	}
	return false;
}

jQuery.cookie = function(name, value, options)
{
    if (typeof value != 'undefined') {
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString();
        }
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else {
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};
jQuery.timer = function (interval, callback)
{
	var interval = interval || 100;

	if (!callback)
		return false;

	_timer = function (interval, callback) {
		this.stop = function () {
			clearInterval(self.id);
		};

		this.internalCallback = function () {
			callback(self);
		};

		this.reset = function (val) {
			if (self.id)
				clearInterval(self.id);

			var val = val || 100;
			this.id = setInterval(this.internalCallback, val);
		};

		this.interval = interval;
		this.id = setInterval(this.internalCallback, this.interval);

		var self = this;
	};

	return new _timer(interval, callback);
};
