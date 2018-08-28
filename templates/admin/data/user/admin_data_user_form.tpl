
<form id="mainform" method="post" action="confirm" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="{$smarty.const.APP_CONST_FILE_IMAGE_MAX_SIZE}" />
{include file="_parts/hidden.tpl"}
<div id="regist-form">
<h2 style="margin-bottom:15px;">{$form.htitle}</h2>
{if $form.errors}
<div class="errormsg"><img src="/img/icon_exclamation.png" width="16" height="16" style="vertical-align:middle" /> 入力内容に誤りがあります</div>
{/if}
<table class="form-01" width="700">
	<tbody>
		<tr>
			<th width="160">利用状態<span class="must">*</span></th>
			<td class="must"></td>
			<td>{tag type="radio" name="status" value="0" label="利用中" checked=$form.status style="width:auto;"}　
				{tag type="radio" name="status" value="1" label="仮登録" checked=$form.status style="width:auto;"}　
				{tag type="radio" name="status" value="2" label="利用停止" checked=$form.status style="width:auto;"}
				{$form.errors.status|@errormsg}
			</td>
		</tr>
	</tbody>
</table>
<h3>装丁.jp IDの情報</h3>
<table class="form-01" width="700">
	<tbody>
		<tr>
			<th width="160">ニックネーム<span class="must">*</span></th>
			<td class="must"></td>
			<td>
				<input id="nickname" name="nickname" value="{$form.nickname}" style="width:150px;ime-mode:active;" maxlength="10" type="text" size="20" />
				{$form.errors.nickname|@errormsg}
				<p class="notice">※3文字以上10文字以内。記号不可。</p>
			</td>
		</tr>
		<tr>
			<th>メールアドレス<span class="must">*</span></th>
			<td class="must"><span class="lock"></span></td>
			<td>
				<input id="email" name="email" value="{$form.email}" style="ime-mode: disabled;" type="text" size="20" />
				{$form.errors.email|@errormsg}
			</td>
		</tr>
		<tr>
			<th>パスワード<p class="errormsg-bg">変更時のみ入力</p></th>
			<td class="must"><span class="lock"></span></td>
			<td>
				<input id="password" name="password" value="{$form.password}" type="password" size="20" />
				{$form.errors.password|@errormsg}
				<p class="notice">※6文字以上の半角英数記号（ <strong>. @ # % $ = _ * & -</strong> ）のみ。</p>
			</td>
		</tr>
		<tr>
			<th>パスワード（確認）</th>
			<td class="must"><span class="lock"></span></td>
			<td>
				<input id="password_confirm" name="password_confirm" value="{$form.password_confirm}" type="password" size="20" />
				<p class="notice">※確認のため、同じパスワードをもう一度入力してください。</p>
			</td>
		</tr>
	</tbody>
</table>
<h3>ご自身の情報</h3>
<table class="form-01" width="700">
	<tbody>
		<tr>
			<th width="160">氏名<span class="must">*</span></th>
			<td class="must"><span class="lock"></span></td>
			<td>
				（姓）<input id="name_sei" name="name_sei" value="{$form.name_sei}" style="width:100px;ime-mode:active;" type="text" size="20" />
				　（名）<input id="name_mei" name="name_mei" value="{$form.name_mei}" style="width:100px;ime-mode:active;" type="text" size="20" />
				{$form.errors.name_sei|@errormsg}
				{$form.errors.name_mei|@errormsg}
				{$form.errors.name|@errormsg}
				<p class="exmsg">例）山田　太郎</p>
			</td>
		</tr>
		<tr>
			<th>氏名フリガナ<span class="must">*</span></th>
			<td class="must"><span class="lock"></span></td>
			<td>（姓）<input id="kana_sei" name="kana_sei" value="{$form.kana_sei}" style="width:100px;ime-mode:active;" type="text" size="20" />
				　（名）<input id="kana_mei" name="kana_mei" value="{$form.kana_mei}" style="width:100px;ime-mode:active;" type="text" size="20" />
				{$form.errors.kana_sei|@errormsg}
				{$form.errors.kana_mei|@errormsg}
				{$form.errors.kana|@errormsg}
				<p class="exmsg">例）ヤマダ　タロウ</p>
			</td>
		</tr>
		<tr>
			<th>住所<span class="must">*</span></th>
			<td class="must"><span class="lock"></span></td>
			<td><p class="subname">郵便番号</p>
				<input id="zip" name="zip" value="{$form.zip}" type="text" size="20" style="width:80px;ime-mode:disabled;" maxlength="8" />
				{$form.errors.zip|@errormsg}
				<p class="exmsg">例）101-0051</p>
				<p class="subname">都道府県</p>
				{tag type="select" id="area" name="area" options=$areaOptions blank="お選びください" style="width:auto;" selected=$form.area}
				{$form.errors.area|@errormsg}
				<p class="subname" style="margin-top:4px;">市区町村</p>
				<input id="addr1" name="addr1" value="{$form.addr1}" style="ime-mode:active;" type="text" size="20" />
				{$form.errors.addr1|@errormsg}
				<p class="exmsg">例）千代田区神田神保町</p>
				<p class="subname">番地・建物名</p>
				<input id="addr2" name="addr2" value="{$form.addr2}" style="ime-mode:active;" type="text" size="20" />
				{$form.errors.addr2|@errormsg}
				<p class="exmsg">例）1-1　○×ビル201</p>
			</td>
		</tr>
		<tr>
			<th>電話番号<span class="must">*</span></th>
			<td class="must"><span class="lock"></span></td>
			<td><input id="tel" name="tel" value="{$form.tel}" style="ime-mode:disabled;" type="text" size="20" />
				{$form.errors.tel|@errormsg}
				<p class="exmsg">例）03-1111-1111</p></td>
		</tr>
		<tr>
			<th>FAX番号</th>
			<td class="must"><span class="lock"></span></td>
			<td><input id="fax" name="fax" value="{$form.fax}" style="ime-mode:disabled;" type="text" size="20" />
				{$form.errors.fax|@errormsg}
				<p class="exmsg">例）03-2222-2222</p></td>
		</tr>
		<tr>
			<th>緊急連絡先<span class="must">*</span></th>
			<td class="must"><span class="lock"></span></td>
			<td><input id="mobile_tel" name="mobile_tel" value="{$form.mobile_tel}" style="ime-mode:disabled;" type="text" size="20" />
				{$form.errors.mobile_tel|@errormsg}
				<p class="exmsg">例）090-1111-1111</p></td>
		</tr>
{*		<tr>
			<th>ホームページURL</th>
			<td class="must"></td>
			<td><input id="url" name="url" value="{$form.url}" type="text" size="20" />
				{$form.errors.url|@errormsg}
				<p class="exmsg">例）http://www.xxxx.co.jp/</p></td>
		</tr>*}
		<tr>
			<th>作品イメージ<p class="errormsg-bg">変更時のみ選択</p></th>
			<td class="must"></td>
			<td>{if $form.image_file!=""}
				<p class="subname">選択中のイメージ</p>
				<p>{$form.image_path|user_image:'user':$form.id}</p>
				<p style="margin:4px 0;">{tag type="checkbox" name="filedelete" value="1" checked=$form.filedelete label="削除する" style="width:auto;"}</p>
				{/if}
				<input name="image_file" type="file" size="40" />
				{$form.errors.image_file|@errormsg}
				<p class="notice">※{$smarty.const.APP_CONST_UPLOAD_IMAGE_EXT}のみ対応しています。</p>
				<p class="notice">※縦横160ピクセル程度の大きさを推奨します。</p></td>
		</tr>
		<tr>
			<th>キャリア<span class="must">*</span></th>
			<td class="must"></td>
			<td><textarea name="career" style="width:400px;ime-mode:active;" rows="5" cols="10">{$form.career}</textarea>
				{$form.errors.career|@errormsg}
			</td>
		</tr>
		<tr>
			<th>得意分野<span class="must">*</span></th>
			<td class="must"></td>
			<td><textarea name="forte" style="width:400px;ime-mode:active;" rows="5" cols="10">{$form.forte}</textarea>
				{$form.errors.forte|@errormsg}
			</td>
		</tr>
		<tr>
			<th>制作環境<span class="must">*</span></th>
			<td class="must"></td>
			<td><textarea name="production_env" style="width:400px;ime-mode:active;" rows="5" cols="10">{$form.production_env}</textarea>
				{$form.errors.production_env|@errormsg}
			</td>
		</tr>
		<tr>
			<th>対応範囲<span class="must">*</span></th>
			<td class="must"></td>
			<td>{tag type="checkbox" name="coverage[]" value="装丁" label="装丁" checks=$form.coverage style="width:auto;"}　
				{tag type="checkbox" name="coverage[]" value="本文組版" label="本文組版" checks=$form.coverage style="width:auto;"}　
				{$form.errors.coverage|@errormsg}
			</td>
		</tr>
		<tr>
			<th>営業時間<span class="must">*</span></th>
			<td class="must"></td>
			<td><input id="business_hours" name="business_hours" value="{$form.business_hours}" style="ime-mode:active;" type="text" size="20" />
				{$form.errors.business_hours|@errormsg}
				<p class="exmsg">例）平日10時～19時</p>
			</td>
		</tr>
		<tr>
			<th>打ち合わせ方法<span class="must">*</span></th>
			<td class="must"></td>
			<td><p>{tag type="checkbox" name="meeting_method[]" value="電話" label="電話" checks=$form.meeting_method style="width:auto;"}　
				{tag type="checkbox" name="meeting_method[]" value="メール" label="メール" checks=$form.meeting_method style="width:auto;"}　
				{tag type="checkbox" name="meeting_method[]" value="訪問" label="訪問" checks=$form.meeting_method style="width:auto;"}（対応地域：<input id="meeting_other1" name="meeting_other1" value="{$form.meeting_other1}" style="width:100px;ime-mode:active;" type="text" size="20" />）</p>
				<p>{tag type="checkbox" name="meeting_method[]" value="スカイプ" label="スカイプ" checks=$form.meeting_method style="width:auto;"}　
				{tag type="checkbox" name="meeting_method[]" value="WindowsLiveメッセンジャー" label="WindowsLiveメッセンジャー" checks=$form.meeting_method style="width:auto;"}</p>
				<p style="margin-top:4px;"><strong>その他</strong></p>
				<textarea name="meeting_other2" style="width:400px;ime-mode:active;" rows="2" cols="10">{$form.meeting_other2}</textarea>
				{$form.errors.meeting_method|@errormsg}
			</td>
		</tr>
		<tr>
			<th>新着案件のメール配信<span class="must">*</span></th>
			<td class="must"><span class="lock"></span></td>
			<td>{tag type="radio" name="mailmaga_flag" value="1" checked=$form.mailmaga_flag label="受け取る" style="width:auto;"}
				　{tag type="radio" name="mailmaga_flag" value="2" checked=$form.mailmaga_flag label="受け取らない" style="width:auto;"}
				{$form.errors.mailmaga_flag|@errormsg}
			</td>
		</tr>
	</tbody>
</table>
<div id="btn-area">
<p style="margin-bottom:5px;text-align:right;"><a href="#" onclick="deleteJump('delete?id={$form.id}');return false;">&raquo; 削除する</a></p>
<input id="btn-mainform" type="submit" value="確認画面に進む" />
</div>
<!-- #regist-form --></div>
</form>
