var AlertQ = null;
function envSave()
{
	$('#submit-btn').button('loading');
	$('#delete-btn').attr('disabled', 'disabled');
	clearErrors();
	var url = $('#mainform').attr('action') + '_save_api';
	var postData = getFormInputs('#mainform');
	ajaxPost(url, postData, function(data, dataType){
		if (data.result == 1) {
			alertMessage('success', '保存されました。');
		} else if (data.errmsg != '') {
			alertMessage('error', data.errmsg);
			if (data.errors) {
				inputErrors(data.errors);
			}
		}
		if (data.security_token != '') {
			$('#_security_token').val(data.security_token);
		}
		$('#delete-btn').removeAttr('disabled');
		$('#submit-btn').button('reset');
	});
	return false;
}
function envDelete()
{
	if (!confirm("削除してもよろしいですか？")) {
		return false;
	}
	$('#delete-btn').button('loading');
	$('#submit-btn').attr('disabled', 'disabled');
	var url = $('#mainform').attr('action') + '_delete_api';
	ajaxPost(url, null, function(data, dataType){
		if (data.result == 1) {
			alertMessage('success', '削除されました。');
			if (data.values) {
				inputValues(data.values);
			}
		} else if (data.errmsg != '') {
			alertMessage('error', data.errmsg);
			if (data.errors) {
				inputErrors(data.errors);
			}
		}
		if (data.security_token != '') {
			$('#_security_token').val(data.security_token);
		}
		$('#submit-btn').removeAttr('disabled');
		$('#delete-btn').button('reset');
	});
	return false;
}
function submitBtn()
{
	$('#submit-btn').button('loading');
	$('#cancel-btn').attr('disabled', 'disabled');
	$('#delete-btn').attr('disabled', 'disabled');
	clearErrors();
	var url = $('#mainform').attr('action') + '_save_api';
	var postData = getFormInputs('#mainform');
	ajaxPost(url, postData, function(data, dataType){
		if (data.result == 1) {
			alertMessage('success', '保存されました。');
			if (data.redirect != '') {
				window.location.href = data.redirect;
				return;
			}
		} else if (data.errmsg != '') {
			alertMessage('error', data.errmsg);
			if (data.errors) {
				inputErrors(data.errors);
			}
		}
		if (data.security_token != '') {
			$('#_security_token').val(data.security_token);
		}
		$('#delete-btn').removeAttr('disabled');
		$('#cancel-btn').removeAttr('disabled');
		$('#submit-btn').button('reset');
	});
	return false;
}
function cancelBtn()
{
	history.back();
	return false;
}
function deleteBtn()
{
	if (!confirm("削除してもよろしいですか？")) {
		return false;
	}
	$('#delete-btn').button('loading');
	$('#submit-btn').attr('disabled', 'disabled');
	$('#cancel-btn').attr('disabled', 'disabled');
	var url = $('#mainform').attr('action') + '_delete';
	$('#mainform').attr('action', url).submit();
	return true;
}
function getFormInputs(idName)
{
	var data = {};
	var input_data = $(idName + " :input");
	$.each(input_data, function(){
		if (this.name!='') {
			if (this.type == 'checkbox') {
//				if (this.checked) {
//					if (data[this.name]) {
//						data[this.name] += ',' + this.value;
//					} else {
//						data[this.name] = this.value;
//					}
//				}
				data[this.name] = this.checked ? this.value : '';
			} else if (this.type == 'radio') {
				if (this.checked) {
					data[this.name] = this.value;
				}
			} else {
				data[this.name] = this.value;
			}
		}
	});
	return data;
}
function alertMessage(type, text) {
	AlertQ = $('<div class="alert alert-'+type+'"></div>').css({'display':'none'}).html(text);
	$('#mainform').before(AlertQ);
	AlertQ.slideDown();
	setTimeout(function(){
		AlertQ.slideUp('slow', function(){
			AlertQ.remove();
			AlertQ = null;
		});
	}, 5000);
}
function inputErrors(errors) {
	for (var col in errors) {
		var inputQ = $('#' + col);
		inputQ.parent('div.controls').parent('div.control-group').addClass('error');
		var error  = errors[col].join('<br />');
		var errorQ = $('<p class="help-block input-error"></p>').html(error);
		inputQ.before(errorQ);
	}
}
function inputValues(values) {
	for (var col in values) {
		var inputQ = $('#' + col);
		inputQ.val(values[col]);
	}
}
function clearErrors() {
	if (AlertQ != null) {
		AlertQ.stop().remove();
		AlertQ = null;
	}
	$('div.control-group').removeClass('error');
	$('p.input-error').remove();
}