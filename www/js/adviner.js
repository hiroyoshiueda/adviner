var Adviner = {
	userInfo: {},
	loaderQ : null,
	notificationTime : 180000,
//	notificationTime : 30000,
	isNoticeTab : false,
	paypalDg : null,
	init: function() {
		Adviner.userInfo = CONST.USER_INFO;
		Adviner.loaderQ = $('<img src="'+CONST.IMG_URL+'/img/ajax-loader.gif" width="32" height="32" />');
	},
	FBLogin : function()
	{
		FB.login(function(response) {
			if (response.authResponse && response.authResponse.accessToken) {
				window.location.href = '/sessions/facebook/?at=' + response.authResponse.accessToken + '&rd_url=' + encodeURIComponent(window.location.href);
			} else {
				Adviner.log('User cancelled login or did not fully authorize.');
			}
		}, {scope: 'user_about_me,publish_stream,email,user_birthday,user_website'});
	},
	FBParse : function(content) {
		FB.XFBML.parse(content);
	},
//	followUsers: function(user_id, method) {
//		if (!CONST.USER_INFO.id) {
//			return Adviner.loginMessage((method=='follow'?'フォロー':'フォロー解除') + 'するにはログインしてください。');
//		}
//		var postData = {
//			'follow_user_id': user_id
//		};
//		var btnQ = $('#follow-u-btn-' + user_id);
//		var tbtn = btnQ.html();
//		var loading = $('<div style="height:20px;margin-right:30px;padding-top:2px;display:inline-block;"></div>').html(Adviner.loaderQ.css({'width':'16','height':'16'}));
//		btnQ.html(loading);
//		ajaxPost('/api/users/' + method, postData, function(data, dataType){
//			if (data.lists.result == '1') {
//				btnQ.html(data.lists.html);
//			} else if (data.lists.errmsg != '') {
//				alert(data.lists.errmsg);
//				btnQ.html(tbtn);
//			}
//		});
//		return false;
//	},
	followAdvice: function(advice_id, method) {
		if (!CONST.USER_INFO.id) {
			return Adviner.loginMessage((method=='follow'?'フォロー':'フォロー解除') + 'するにはログインしてください。');
		}
		var postData = {
			'follow_advice_id': advice_id
		};
		var btnQ = $('#follow-a-btn-' + advice_id);
		var tbtn = btnQ.html();
		var loading = $('<div style="height:20px;margin-right:30px;padding-top:2px;display:inline-block;"></div>').html(Adviner.loaderQ.css({'width':'16','height':'16'}));
		btnQ.html(loading);
		ajaxPost('/api/advice/' + method, postData, function(data, dataType){
			if (data.lists.result == '1') {
				btnQ.html(data.lists.html);
			} else if (data.lists.errmsg != '') {
				alert(data.lists.errmsg);
				btnQ.html(tbtn);
			}
		});
		return false;
	},
	topNotification: function() {
		if (Adviner.isNoticeTab) return;
		ajaxPost('/api/notice/get_unread', {}, function(data, dataType){
			if (data.lists.result == '1') {
				if (data.lists.unread > 0) {
					$('#top-notice').removeClass('top_notice_zero').html(data.lists.html);
				} else {
					$('#top-notice').addClass('top_notice_zero').html(data.lists.html);
				}
			}
		});
	},
	bindOpenPopupPayment: function(expr) {
		$(expr).live('click', function(event){
			Adviner.paypalDg = new PAYPAL.apps.DGFlow();
			Adviner.paypalDg.setTrigger(event.target.id);
			Adviner.paypalDg.startFlow(event.target.href);
		});
	},
	closePopupPayment: function() {
		if (Adviner.paypalDg) {
			Adviner.paypalDg.closeFlow();
			Adviner.paypalDg = null;
		}
	},
	openNotification: function() {
		ajaxPost('/api/notice/get_list', {}, function(data, dataType){
			if (data.lists.result == '1') {
				$('#header-notice-msg').html(data.lists.html).css({"display":"none","z-index":999}).fadeIn();
				var bg = $(document.createElement('div')).attr('id','header-notice-bg').appendTo('body')
				.css({
					position: 'absolute',
					width: '100%',
					height: $('body').height(),
					top: 0,
					left: 0,
					'z-index':1
				}).click(function() {
					$(this).remove();
					$('#header-notice-msg').hide();
				});
			}
		});
	},
	activeForm: function() {
		$('div.input_item > :input').focus(function(){
			$(this).css({'backgroundColor':'#ffffdd'});
		}).blur(function(){
			$(this).css({'backgroundColor':'#ffffff'});
		});
	},
	bindConsultThreadEvent : function(content) {
		$('a.click_reply_form_button', content).click(Adviner.clickReplyFormButton);
		$('a.click_advice_form_button', content).click(Adviner.clickAdviceFormButton);
		$('a.click_not_advice_form_button', content).click(Adviner.clickNotAdviceFormButton);
		$('a.click_review_form_button', content).click(Adviner.clickReviewFormButton);
		$('.click_popup_payment', content).bind('click', function(event){
			Adviner.paypalDg = new PAYPAL.apps.DGFlow();
			Adviner.paypalDg.setTrigger(event.target.id);
			Adviner.paypalDg.startFlow(event.target.href);
		});
		Adviner.parseAutosize(content);
	},
	clickReplyFormButton : function(){
		var consult_id = $(this).attr('href').replace('#', '');
		var textQ = $('#reply-form-text-' + consult_id);
		textQ.next('p.errormsg-text').remove();
		var reply_body = textQ.val();
		reply_body = trimZen(reply_body);
		if (reply_body == '') {
			alert('送信内容を入力してください。');
			textQ.focus();
			return false;
		}
		var listQ = $('#reply-list-area-' + consult_id);
		var formQ = $('#reply-form-item-' + consult_id);
		var areaQ = $('#reply-form-area-' + consult_id);
		areaQ.css({'display':'none'});
		var loading = $('<div style="height:32px;width:32px;margin:5px 0 0 10px;"></div>').html(Adviner.loaderQ.css({'width':'32','height':'32'}));
		$('div.balloon_thread_data', formQ).append(loading);
		var replynum = $('div.response_item', listQ).length;
		var is_fb_share = $('li > div.check_fb_share input.reply_fb_share', $(this).parent('li').parent('ul')).prop('checked') ? 1 : 0;
		var postData = {
			'consult_id' : consult_id,
			'reply_body' : reply_body,
			'reply_opt' : 0,
			'replynum' : replynum,
			'is_fb_share' : is_fb_share
		};
		ajaxPost('/api/response/post_reply', postData, function(data, dataType){
			if (data.lists.result == '1') {
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					//Adviner.good.parse_button(htmlQ);
					listQ.append(htmlQ);
					htmlQ.fadeIn();
					//Adviner.FBParse(listQ.get(0));
				}
				if (is_fb_share && data.lists.fb_share) {
					Adviner.postFeedToFacebook(data.lists.fb_share);
				}
				textQ.val('');
				//textQ.removeClass('form_input_open');
			} else if (data.lists.errmsg != '') {
				if (data.lists.errors) {
					if (data.lists.errors.reply_body) {
						textQ.after(Adviner.errormsg(data.lists.errors.reply_body));
					}
				} else {
					alert(data.lists.errmsg);
				}
			}
			loading.remove();
			areaQ.fadeIn();
		});
		return false;
	},
	clickAdviceFormButton : function() {
		var consult_id = $(this).attr('href').replace('#', '');
		var textQ = $('#reply-form-text-' + consult_id);
		textQ.next('p.errormsg-text').remove();
		var reply_body = textQ.val();
		reply_body = trimZen(reply_body);
		if (reply_body == '') {
			alert('アドバイスの内容を入力してください。');
			textQ.focus();
			return false;
		}
		var listQ = $('#reply-list-area-' + consult_id);
		var formQ = $('#reply-form-item-' + consult_id);
		var areaQ = $('#reply-form-area-' + consult_id);
		areaQ.css({'display':'none'});
		var loading = $('<div style="height:32px;width:32px;margin:5px 0 0 10px;"></div>').html(Adviner.loaderQ.css({'width':'32','height':'32'}));
		$('div.balloon_thread_data', formQ).append(loading);
		var replynum = $('div.response_item', listQ).length;
		var is_fb_share = $('li > div.check_fb_share input.reply_fb_share', $(this).parent('li').parent('ul')).prop('checked') ? 1 : 0;
		var postData = {
			'consult_id' : consult_id,
			'reply_body' : reply_body,
			'reply_opt' : 1,
			'replynum' : replynum,
			'is_fb_share' : is_fb_share
		};
		ajaxPost('/api/response/post_reply', postData, function(data, dataType){
			if (data.lists.result == '1') {
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					//Adviner.good.parse_button(htmlQ);
					listQ.append(htmlQ);
					htmlQ.fadeIn();
					//Adviner.FBParse(listQ.get(0));
				}
				if (is_fb_share && data.lists.fb_share) {
					Adviner.postFeedToFacebook(data.lists.fb_share);
				}
				if (data.lists.is_public == 1) {
					$('#consult-frame-' + consult_id).removeClass('balloon_private');
				}
				$('#consult-frame-' + consult_id + ' > p.balloon_infomsg').css({'display':'none'});
				$('a.advice_form_btn, a.not_advice_form_btn', areaQ).css({'display':'none'});
				$('a.reply_form_btn', areaQ).css({'display':''});
				textQ.val('');
				//textQ.removeClass('form_input_open');
			} else if (data.lists.errmsg != '') {
				if (data.lists.errors) {
					if (data.lists.errors.reply_body) {
						textQ.after(Adviner.errormsg(data.lists.errors.reply_body));
					}
				} else {
					alert(data.lists.errmsg);
				}
			}
			loading.remove();
			areaQ.fadeIn();
		});
		return false;
	},
	clickNotAdviceFormButton : function() {
		var consult_id = $(this).attr('href').replace('#', '');
		var textQ = $('#reply-form-text-' + consult_id);
		textQ.next('p.errormsg-text').remove();
		var reply_body = textQ.val();
		reply_body = trimZen(reply_body);
		if (reply_body == '') {
			alert('アドバイスできない理由を入力してください。');
			textQ.focus();
			return false;
		}
		var confirm_msg = '入力内容の送信後、この相談スレッドは非公開のまま終了します。よろしいですか？';
//		if (reply_body == '') {
//			confirm_msg = 'この相談スレッドは非公開のまま終了します。よろしいですか？';
//		} else {
//			confirm_msg = '入力内容の送信後、この相談スレッドは非公開のまま終了します。よろしいですか？';
//		}
		if (confirm(confirm_msg) == false) {
			textQ.focus();
			return false;
		}
		var listQ = $('#reply-list-area-' + consult_id);
		var formQ = $('#reply-form-item-' + consult_id);
		var areaQ = $('#reply-form-area-' + consult_id);
		areaQ.css({'display':'none'});
		var loading = $('<div style="height:32px;width:32px;margin:5px 0 0 10px;"></div>').html(Adviner.loaderQ.css({'width':'32','height':'32'}));
		$('div.balloon_thread_data', formQ).append(loading);
		var replynum = $('#reply-frame-' + consult_id + ' > div.response_item').length;
		$('#reply-frame-' + consult_id + ' li.balloon_navi_open_reply_form_button').remove();
		var postData = {
			'consult_id' : consult_id,
			'reply_body' : reply_body,
			'reply_opt' : 3,
			'replynum' : replynum
		};
		ajaxPost('/api/response/post_reply', postData, function(data, dataType){
			if (data.lists.result == '1') {
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					//Adviner.good.parse_button(htmlQ);
					listQ.append(htmlQ);
					htmlQ.fadeIn();
					//Adviner.FBParse(listQ.get(0));
				}
				$('a.advice_form_btn, a.not_advice_form_btn', areaQ).css({'display':'none'});
				$('a.reply_form_btn', areaQ).css({'display':''});
				textQ.val('');
				textQ.removeClass('form_input_open');
				formQ.css({'display':'none'});
				$('#consult-frame-' + consult_id + ' > p.balloon_infomsg').css({'display':'none'});
				var msgQ = $('<p class="balloon_infomsg" style="text-align:center;">この相談は非公開のまま終了しました。</p>');
				formQ.after(msgQ);
			} else if (data.lists.errmsg != '') {
				if (data.lists.errors) {
					if (data.lists.errors.reply_body) {
						textQ.after(Adviner.errormsg(data.lists.errors.reply_body));
					}
				} else {
					alert(data.lists.errmsg);
				}
			}
			loading.remove();
			areaQ.fadeIn();
		});
		return false;
	},
	clickReviewFormButton : function() {
		var consult_id = $(this).attr('href').replace('#', '');
		var textQ = $('#review-form-text-' + consult_id);
		textQ.next('p.errormsg-text').remove();
		var review_body = textQ.val();
		review_body = trimZen(review_body);
		if (review_body == '') {
			alert('評価コメントを入力してください。');
			textQ.focus();
			return false;
		}
		var listQ = $('#reply-list-area-' + consult_id);
		var evaluate_type = $('#review-form-evaluate-type-' + consult_id).val();
		var review_public_flag = 0;
		if ($('#review-form-public-flag-' + consult_id).size()) {
			review_public_flag = $('#review-form-public-flag-' + consult_id).prop('checked') ? 2 : 1;
		}
		var is_fb_share = $('li > div.check_fb_share input.review_fb_share', $(this).parent('li').parent('ul')).prop('checked') ? 1 : 0;
		var formQ = $('#reply-form-item-' + consult_id);
		var areaQ = $('#review-form-area-' + consult_id);
		areaQ.css({'display':'none'});
		var loading = $('<div style="height:32px;width:32px;margin:5px 0 0 10px;"></div>').html(Adviner.loaderQ.css({'width':'32','height':'32'}));
		$('div.balloon_thread_data', formQ).append(loading);
		//$('#reply-frame-' + consult_id + ' li.balloon_navi_open_reply_form_button').css({'display':'none'});
		//$('#reply-frame-' + consult_id + ' li.balloon_navi_open_review_form_button').css({'display':'none'});
		var postData = {
			'consult_id' : consult_id,
			'review_body' : review_body,
			'evaluate_type' : evaluate_type,
			'review_public_flag' : review_public_flag,
			'is_fb_share' : is_fb_share
		};
		ajaxPost('/api/response/post_review', postData, function(data, dataType){
			if (data.lists.result == '1') {
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					//Adviner.good.parse_button(htmlQ);
					listQ.append(htmlQ);
					htmlQ.fadeIn();
					//Adviner.FBParse(listQ.get(0));
				}
				if (is_fb_share && data.lists.fb_share) {
					Adviner.postFeedToFacebook(data.lists.fb_share);
				}
				textQ.val('');
				textQ.removeClass('form_input_open');
				formQ.css({'display':'none'});
				if (data.lists.is_finish == '1') {
					var msgQ = $('<p class="balloon_finishmsg">この相談は終了しました。</p>');
					formQ.after(msgQ);
				}
			} else if (data.lists.errmsg != '') {
				if (data.lists.errors) {
					if (data.lists.errors.review_body) {
						textQ.after(Adviner.errormsg(data.lists.errors.review_body));
					}
				} else {
					alert(data.lists.errmsg);
				}
			}
			loading.remove();
			areaQ.fadeIn();
		});
		return false;
	},
	clickQuestionPostButton : function() {
		var textQ = $('#please-advice-form-textarea');
		textQ.next('p.errormsg-text').remove();
		var please_body = textQ.val();
		please_body = trimZen(please_body);
		if (please_body == '') {
			alert('質問内容を入力してください。');
			textQ.focus();
			return false;
		}
		var is_fb_share = $('#please-advice-form-fb-share').prop('checked') ? 1 : 0;
		var btnQ = $('#please-advice-form-btn');
		btnQ.css({'display':'none'});
		var loading = $('<div style="height:28px;width:28px;float:right;"></div>').html(Adviner.loaderQ.css({'width':'28','height':'28'}));
		btnQ.after(loading);
		var postData = {
			'is_fb_share' : is_fb_share,
			'question_body' : please_body,
			'security_token' : $('#_security_token').val()
		};
		ajaxPost('/api/posts/qa/post_question', postData, function(data, dataType){
			if (data.lists.result == 1) {
				if (is_fb_share && data.lists.fb_share) {
					Adviner.postFeedToFacebook(data.lists.fb_share);
				}
				Adviner.headermsg('投稿されました。');
				textQ.val('');
				var parse = decodeURIComponent(window.location.hash).split('/');
				var pname = parse[1] || '';
				if (pname == "qa") {
					showQa();
				} else {
					window.location.href = '/#!/qa';
				}
			} else if (data.lists.errmsg != '') {
				if (data.lists.errors) {
					if (data.lists.errors.question_body) {
						textQ.after(Adviner.errormsg(data.lists.errors.question_body));
					}
				} else {
					alert(data.lists.errmsg);
				}
			}
			if (data.lists.security_token) $('#_security_token').val(data.lists.security_token);
			loading.remove();
			btnQ.fadeIn();
		});
		return false;
	},
	clickAnswerFormButton : function() {
		var btnQ = $(this);
		var question_id = btnQ.attr('href').replace('#', '');
		var areaQ = btnQ.parent('li').parent('ul').parent('div.qa-tool');
		if (areaQ.next('div.answer-post-form').size()) {
			areaQ.next('div.answer-post-form').show();
			return false;
		}
		btnQ.css({'display':'none'});
		var loading = $('<div style="height:16px;width:60px;"></div>').html(Adviner.loaderQ.css({'width':'16','height':'16'}));
		btnQ.after(loading);
		var postData = {
			'question_id' : question_id
		};
		ajaxPost('/api/posts/qa/get_answer_form', postData, function(data, dataType){
			if (data.lists.result == '1') {
				if (data.lists.html != '') {
					var htmlQ = $(data.lists.html).css({'display':'none'});
					//Adviner.good.parse_button(htmlQ);
					Adviner.parseAutosize(htmlQ);
					$('a.answer-post-btn', htmlQ).click(Adviner.clickAnswerPostButton);
					$('a.answer-cancel-btn', htmlQ).click(Adviner.clickAnswerCancelButton);
					areaQ.after(htmlQ);
					$('#answer-form-textarea-' + question_id).focus();
					htmlQ.fadeIn();
				}
			} else if (data.lists.errmsg != '') {
				alert(data.lists.errmsg);
			}
			loading.remove();
			btnQ.fadeIn();
		});
		return false;
	},
	clickAnswerPostButton : function() {
		var btnQ = $(this);
		var question_id = btnQ.attr('href').replace('#', '');
		var textQ = $('#answer-form-textarea-' + question_id);
		textQ.next('p.errormsg-text').remove();
		var input_body = textQ.val();
		input_body = trimZen(input_body);
		if (input_body == '') {
			alert('回答を入力してください。');
			textQ.focus();
			return false;
		}
		btnQ.css({'display':'none'});
		var loading = $('<div style="height:26px;width:122px;float:right;margin-top:6px;"></div>').html(Adviner.loaderQ.css({'width':'16','height':'16'}));
		btnQ.after(loading);
		var postData = {
			'question_id' : question_id,
			'answer_body' : input_body,
			'security_token' : $('#_security_token').val()
		};
		ajaxPost('/api/posts/qa/post_answer', postData, function(data, dataType){
			if (data.lists.result == '1') {
				var dataQ = btnQ.parent('div.answer-post-form-btn').parent('div.answer-post-form').parent('div.qa-tool');
				if (dataQ.size()) {
					dataQ.slideUp('normal', function(){
						dataQ.html('<div class="infomsg" style="margin-bottom:5px;">アドバイスが送信されました。<a href="'+data.lists.redirect+'">&raquo; 相談スレッドを見る</a></div>');
						dataQ.fadeIn();
					});
					Adviner.headermsg('アドバイスが送信されました。');
				}
			} else if (data.lists.errmsg != '') {
				if (data.lists.errors) {
					if (data.lists.errors.reply_body) {
						textQ.after(Adviner.errormsg(data.lists.errors.reply_body));
					}
				} else {
					alert(data.lists.errmsg);
				}
				loading.remove();
				btnQ.fadeIn();
			}
		});
		return false;
	},
	clickAnswerCancelButton : function() {
		var btnQ = $(this);
		var question_id = btnQ.attr('href').replace('#', '');
		$('#answer-post-form-' + question_id).hide();
		return false;
	},
	clickPleaseAdvicePostButton : function() {
		var btnQ = $(this);
		var consult_id = btnQ.attr('href').replace('#', '');
		var textQ = $('#please-advice-form-textarea-' + consult_id);
		textQ.next('p.errormsg-text').remove();
		var reply_body = textQ.val();
		reply_body = trimZen(reply_body);
		if (reply_body == '') {
			alert('アドバイスを入力してください。');
			textQ.focus();
			return false;
		}
		var advice_id = $('#please-advice-form-select-' + consult_id).val();
		btnQ.css({'display':'none'});
		var loading = $('<div style="height:26px;width:122px;float:right;margin-top:6px;"></div>').html(Adviner.loaderQ.css({'width':'16','height':'16'}));
		btnQ.after(loading);
		var postData = {
			'advice_id' : advice_id,
			'consult_id' : consult_id,
			'reply_body' : reply_body
		};
		ajaxPost('/api/please/post_advice', postData, function(data, dataType){
			if (data.lists.result == '1') {
				if (data.lists.redirect != '') {
					//window.location.href = data.lists.redirect;
					var dataQ = btnQ.parent('div.please_advice_post_form_btn').parent('div.please_advice_post_form').parent('div.balloon_data');
					if (dataQ.size()) {
						dataQ.slideUp('normal', function(){
							dataQ.html('<div class="infomsg" style="margin-bottom:5px;">アドバイスが送信されました。<a href="'+data.lists.redirect+'">&raquo; 相談スレッドを見る</a></div>');
							dataQ.fadeIn();
						});
						Adviner.headermsg('アドバイスが送信されました。');
					}
				}
			} else if (data.lists.errmsg != '') {
				if (data.lists.errors) {
					if (data.lists.errors.reply_body) {
						textQ.after(Adviner.errormsg(data.lists.errors.reply_body));
					}
				} else {
					alert(data.lists.errmsg);
				}
				loading.remove();
				btnQ.fadeIn();
			}
		});
		return false;
	},
//	focusReplyFormInput : function() {
//		$(this).addClass('form_input_open');
//	},
//	blurReplyFormInput : function() {
//		if (trimZen($(this).val()) == '') {
//			$(this).removeClass('form_input_open');
//		}
//	},
//	focusPleaseFormInput : function() {
//		//$(this).addClass('form_input_open');
//		$('#please-advice-form-btn').fadeIn('slow');
//	},
//	blurPleaseFormInput : function() {
//		if (trimZen($(this).val()) == '') {
//			//$(this).removeClass('form_input_open');
//			$('#please-advice-form-btn').fadeOut('slow');
//		}
//	},
	clickOpenConsultThread : function(idName, url, thisObj) {
		_gaq.push(['_trackPageview', url]);
		if ($(idName).css('display') == 'none') {
			$(idName).fadeIn();
		} else {
			$(idName).css({'display':'none'});
		}
		$(thisObj).remove();
	},
	loginMessage : function(message) {
		alert(message);
	},
	parseAutosize : function(content){
		$('textarea.input_autosize', content).autosize();
	},
	postFeedToFacebook : function(post_data) {
		FB.api('/me/feed', 'post', post_data, function(response) {
			if (!response || response.error) {
				alert('【エラー】Facebookへの共有に失敗しました。手動で投稿してください。');
			} else {
				//alert('Post ID: ' + response.id);
			}
		});
	},
	postSearch : function() {
		var qurl = '';
		var qtype = $('#search-type').val();
		var qopt = $('#search-opt').val();
		var q = $('#header-search-q').val();
		q = trimZen(q);
		if (qtype == 2) {
			window.location.href = '/member/search/' + q;
			return false;
		} else if (qtype == 3) {
			window.location.href = '/consult/search/' + q;
			return false;
		}
		if (q == '') {
			qurl = (qopt == 'private') ? 'private:' : '';
		} else {
			qurl = (qopt == 'private') ? 'search/private:' + q : 'search/' + q;
		}
		if ($('#search-category').size() > 0 && $('#search-category').val() != '') {
			window.location.href = '/category/' + $('#search-category').val() + '/' + qurl;
		} else if (q == '') {
			window.location.href = '/search/' + qurl;
		} else {
			window.location.href = '/' + qurl;
		}
	},
	clickSearchType : function() {
		if ($('#header-search-type-box').size()) {
			$('#header-search-type-box').show();
			$('#header-search-q').focus();
			return;
		}
		var boxQ = $('<div id="header-search-type-box" style="z-index:999;"></div>');
		var t1 = $('<li><span class="search-type-box-1"></span>相談窓口を検索</li>').click(function(){
			Adviner.hideSearchTypeBox(1, '相談窓口を検索');
		});
		var t2 = $('<li><span class="search-type-box-2"></span>人を検索</li>').click(function(){
			Adviner.hideSearchTypeBox(2, '人を検索');
		});
		var t3 = $('<li><span class="search-type-box-3"></span>相談内容を検索</li>').click(function(){
			Adviner.hideSearchTypeBox(3, '相談内容を検索');
		});
		var ul = $('<ul></ul>').append(t1).append(t2).append(t3);
		boxQ.append(ul);
		$(this).append(boxQ);
		boxQ.show();
		$('#header-search-q').focus();
	},
	hideSearchTypeBox : function(num, text) {
		$('#header-search-type').removeClass('search-type-1 search-type-2 search-type-3').addClass('search-type-'+num);
		$('#search-type').val(num);
		//$('#header-search-type-box').fadeOut('fast');
		$('#header-search-q').attr('placeholder', text).focus();
		if (num == 1) {
			$('#header-search-opt').css({'display':'block'});
		} else {
			$('#header-search-opt').css({'display':'none'});
		}
	},
	clickSearchOpt : function() {
		if ($('#header-search-opt-box').size()) {
			$('#header-search-opt-box').show();
			$('#header-search-q').focus();
			return;
		}
		var boxQ = $('<div id="header-search-opt-box" style="z-index:999;"></div>');
		var t1 = $('<li>すべて<span class="search-opt-box-default"></span></li>').click(function(){
			Adviner.hideSearchOptBox('default');
		}).blur(function(){
			$('#header-search-opt-box').fadeOut();
		}).focus();
		var t2 = $('<li>非公開相談が可能<span class="search-opt-box-private"></span></li>').click(function(){
			Adviner.hideSearchOptBox('private');
		});
		var ul = $('<ul></ul>').append(t1).append(t2);
		boxQ.append(ul);
		$(this).append(boxQ);
		boxQ.show();
		$('#header-search-q').focus();
	},
	hideSearchOptBox : function(optName) {
		$('#header-search-opt').removeClass('search-opt-default search-opt-private').addClass('search-opt-'+optName);
		$('#search-opt').val(optName);
		//$('#header-search-opt-box').fadeOut('fast');
		$('#header-search-q').focus();
	},
	hideSearchBox : function() {
		$('#header-search-type-box').fadeOut('fast');
		$('#header-search-opt-box').fadeOut('fast');
		$('#header-search-q').focus();
	},
	getPageNameHash : function() {
		var parse = decodeURIComponent(window.location.hash).split('/');
		return parse[1] || '';
	},
	headermsg : function(text) {
		var msg = $('<div id="header-message"><div class="header_message_success"><div class="header_message_text">'+text+'</div></div></div>');
		$('body').after(msg);
		msg.slideDown();
		setTimeout(function(){
			msg.slideUp('slow', function(){
				msg.remove();
			});
		}, 5000);
	},
	errormsg : function(errors){
		var errmsg = errors.join('<br />');
		return '<p class="errormsg-text">' + errmsg + '</p>';
	},
	showTopMessage: function(){
		showTopAlert($('#alert-errormsg, #alert-successmsg'));
	},
	closePopup : function() {
		if (typeof $.closeDOMWindow !== 'undefined') {
			$.closeDOMWindow();
		} else {
			Adviner.closePopupPayment();
		}
	},
	requestCallback : function(response) {
		Adviner.log(response);
	},
	log: function(arg) {
		if (typeof console !== 'undefined') {
			console.log(arg);
		}
	}
};

$(document).ready(function(){
	Adviner.init();
	if ($('#facebook-login-btn').length > 0) {
		$('#facebook-login-btn').click(function(){
			var loading = $('<div style="height:40px;margin-bottom:5px;padding-top:10px;"></div>').html(Adviner.loaderQ);
			$(this).css({'display':'none'}).after(loading);
			if ($('#rememberme').prop('checked')) {
				$.cookie('rememberme', '1', {'expires':CONST.USER_REMEMBER_DAY});
			} else {
				$.cookie('rememberme');
			}
			Adviner.FBLogin();
			return false;
		});
	}
	if ($('#header-facebook-login-btn').size() > 0) {
		$('#header-facebook-login-btn').click(function(){
			Adviner.FBLogin();
			return false;
		});
	} else if ($('#header-facebook-logout-btn').size() > 0) {
		$('#header-facebook-logout-btn').click(function(){
			window.location.href = '/logout?rd_url=' + encodeURIComponent(window.location.href);
			return false;
		});
	}
	if ($('#header-search-form').size()) {
		$('#header-search-type').click(Adviner.clickSearchType);
		$('#header-search-opt').click(Adviner.clickSearchOpt);
		$('#header-search-q').blur(Adviner.hideSearchBox);
		$('#header-search-form').submit(function(){
			Adviner.postSearch();
			return false;
		});
	}
	if ($('#top-notice').size()) {
		$('#top-notice').click(function(){
			Adviner.openNotification();
		});
		setTimeout(function(){
			Adviner.topNotification();
			setInterval(Adviner.topNotification, Adviner.notificationTime);
		}, 2000);
	}
//	$('.social_fb_like').socialbutton('facebook_like', {
//		button: 'button_count',
//		url: CONST.URL,
//		show_faces: false,
//		width: 105,
//		action: 'like',
//		locale: 'ja_JP',
//		font: 'arial',
//		colorscheme: 'light'
//	});
//	$('.social_twitter').socialbutton('twitter', {
//		button: 'horizontal',
//		url: CONST.URL,
//		text: CONST.TITLE,
//		lang: 'en',
//		via: 'advinercom',
//		related: 'advinercom'
//	});
//	$('.social_g_plusone').socialbutton('google_plusone', {
//		button: 'medium',
//		url: CONST.URL,
//		lang: 'ja',
//		parsetags: 'onload',
//		count: true
//	});
//	$('.social_hatena').socialbutton('hatena', {
//		button: 'standard',
//		url: CONST.URL,
//		title: CONST.TITLE
//	});
});