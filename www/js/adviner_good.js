var Adviner = Adviner || {};
Adviner.good = {
	load_button : function() {
		$('div.ad_good > span.ad_good_button > a.ad_good_button_face').live('click', Adviner.good.click_event);
	},
//	parse_button : function(context) {
//		$('div.ad_good > span.ad_good_button > a.ad_good_button_face', context).bind('click', Adviner.good.click_event);
//	},
	click_event : function() {
		var btnQ = $(this);
		var className = btnQ.attr('class');
		if (className.match("ad_good_send")) {
			return Adviner.good.click_send(btnQ);
		} else if (className.match("ad_good_cancel")) {
			return Adviner.good.click_cancel(btnQ);
		} else if (className.match("ad_good_login")) {
			return Adviner.good.click_login(btnQ);
		}
		return false;
	},
	click_send : function(btnQ) {
		btnQ.removeClass('ad_good_send');
		var href = btnQ.attr('href');
		var textQ = $('span.ad_good_button_text', btnQ);
		var countQ = btnQ.parent('span.ad_good_button').next('span.ad_good_count').find('a.ad_good_count_face > span.ad_good_count_text');
		$.ajax({
			'url': '/api/good/send',
			'type': 'POST',
			'dataType': 'json',
			'data': {
				"href":href
			},
			'timeout': 60000,
			'cache': false,
			'success': function(data, textStatus){
				if (data.lists.result == 1) {
					var count = parseInt(countQ.html());
					countQ.html(count + 1);
					btnQ.addClass('ad_good_cancel ad_good_ok');
				} else {
					btnQ.addClass('ad_good_send');
				}
				//if (data.lists.errmsg != '') {
				//	alert(data.lists.errmsg);
				//}
			},
			'error': function(XMLHttpRequest, textStatus, errorThrown){
				//alert(textStatus+': '+errorThrown);
				btnQ.addClass('ad_good_send');
			}
		});
		return false;
	},
	click_cancel: function(btnQ) {
		btnQ.removeClass('ad_good_cancel');
		var href = btnQ.attr('href');
		var textQ = $('span.ad_good_button_text', btnQ);
		var countQ = btnQ.parent('span.ad_good_button').next('span.ad_good_count').find('a.ad_good_count_face > span.ad_good_count_text');
		$.ajax({
			'url': '/api/good/cancel',
			'type': 'POST',
			'dataType': 'json',
			'data': {
				"href":href
			},
			'timeout': 60000,
			'cache': false,
			'success': function(data, textStatus){
				if (data.lists.result == 1) {
					var count = parseInt(countQ.html());
					countQ.html(count - 1);
					btnQ.removeClass('ad_good_ok').addClass('ad_good_send');
				} else {
					btnQ.addClass('ad_good_cancel');
				}
				//if (data.lists.errmsg != '') {
				//	alert(data.lists.errmsg);
				//}
			},
			'error': function(XMLHttpRequest, textStatus, errorThrown){
				//alert(textStatus+': '+errorThrown);
				btnQ.addClass('ad_good_cancel');
			}
		});
		return false;
	},
	click_login: function(btnQ) {
		alert('Goodボタンをクリックするにはログインする必要があります。');
		return false;
	}
};
$(document).ready(function(){
	Adviner.good.load_button();
});