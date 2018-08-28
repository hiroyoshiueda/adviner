<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8" />
<title>Adviner For Websites: Good Button</title>
<style type="text/css">
body {
	font-family: "ヒラギノ角ゴ Pro W3","Hiragino Kaku Gothic Pro","メイリオ",Meiryo,"ＭＳ Ｐゴシック","MS PGothic",sans-serif;
	line-height: 1.0;
	padding:0;
	margin:0;
}
a.ad_good_button_face {
	background: transparent url("/img/good_button.png") no-repeat;
	display: block;
	width: 90px;
	height: 20px;
	overflow: hidden;
	position: relative;
}
a.ad_good_button_face span.ad_good_button_context {
	position: absolute;
	top: 2px;
	left: 26px;
}
a.ad_good_button_face span.ad_good_button_count {
	color: #503F2D;
	font-size: 11px;
	margin: 0 4px 0 0;
}
a.ad_good_button_face span.ad_good_button_text {
	color: #503F2D;
	font-size: 11px;
}
a.ad_good_button_face:HOVER span.ad_good_button_count,
a.ad_good_button_face:HOVER span.ad_good_button_text {
	color: #4C71B6;
}
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script>
$(document).ready(function(){
	var click_good_send = function(){
		var href = $(this).attr('href');
		var countQ = $('span.ad_good_button_count');
		var textQ = $('span.ad_good_button_text');
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
				if (data.lists.result==1) {
					var count = countQ.html();
					countQ.html(count - 0 + 1);
					textQ.html(data.lists.text);
				}
			},
			'error': function(XMLHttpRequest, textStatus, errorThrown){
				//alert(textStatus+': '+errorThrown);
			}
		});
		return false;
	};
	$('a.ad_good_send').click(click_good_send);
	$('a.ad_good_cancel').click(_good_cancel);
	$('a.ad_good_login').click(_good_login);
});
</script>
</head><body>{$form.html nofilter}</body></html>
