
<div id="regist-form">
<form id="mainform" method="post" action="save">
{include file="_parts/hidden.tpl"}
<h2 style="margin-bottom:15px;">{$form.htitle}</h2>
<table class="form-01" width="700">
	<tbody>
		<tr>
			<th width="160">利用状態<span class="must">*</span></th>
			<td class="must"></td>
			<td><p>{if $form.status==0}利用中{elseif $form.status==1}仮登録{elseif $form.status==2}利用停止{/if}</p></td>
		</tr>
	</tbody>
</table>
<h3>装丁.jp IDの情報</h3>
<table class="form-01" width="700">
	<tbody>
		<tr>
			<th width="160">ニックネーム<span class="must">*</span></th>
			<td class="must"></td>
			<td><p>{$form.nickname}</p></td>
		</tr>
		<tr>
			<th>メールアドレス</th>
			<td class="must"><span class="lock"></span></td>
			<td><p>{$form.email}</p></td>
		</tr>
		<tr>
			<th>パスワード</th>
			<td class="must"><span class="lock"></span></td>
			<td>{if $form.password!=""}<p>{$form.password_text}</p>
				<p class="notice">※セキュリティ上、伏せて表示しています。</p>
				{/if}
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
			<td><p>{$form.name_sei}　{$form.name_mei}</p></td>
		</tr>
		<tr>
			<th>氏名フリガナ<span class="must">*</span></th>
			<td class="must"><span class="lock"></span></td>
			<td><p>{$form.kana_sei}　{$form.kana_mei}</p></td>
		</tr>
		<tr>
			<th>住所<span class="must">*</span></th>
			<td class="must"><span class="lock"></span></td>
			<td><p class="subname">郵便番号</p>
				<p>{$form.zip}</p>
				<p class="subname">都道府県</p>
				<p>{$form.area}</p>
				<p class="subname">市区町村</p>
				<p>{$form.addr1}</p>
				<p class="subname">番地・建物</p>
				<p>{$form.addr2}</p>
			</td>
		</tr>
		<tr>
			<th>電話番号<span class="must">*</span></th>
			<td class="must"><span class="lock"></span></td>
			<td><p>{$form.tel}</p></td>
		</tr>
		<tr>
			<th>FAX番号</th>
			<td class="must"><span class="lock"></span></td>
			<td><p>{$form.fax}</p></td>
		</tr>
		<tr>
			<th>緊急連絡先</th>
			<td class="must"><span class="lock"></span></td>
			<td><p>{$form.mobile_tel}</p></td>
		</tr>
{*		<tr>
			<th>ホームページURL</th>
			<td class="must"></td>
			<td><p>{$form.url}</p></td>
		</tr>*}
		<tr>
			<th>イメージ</th>
			<td class="must"></td>
			<td>{if $form.filedelete=="1"}<p>削除する</p>
				{else}<p>{$form.image_path|user_image:'user':$form.user_id}</p>{/if}
			</td>
		</tr>
		<tr>
			<th>キャリア<span class="must">*</span></th>
			<td class="must"></td>
			<td><p>{$form.career|nl2br}</p></td>
		</tr>
		<tr>
			<th>得意分野<span class="must">*</span></th>
			<td class="must"></td>
			<td><p>{$form.forte|nl2br}</p></td>
		</tr>
		<tr>
			<th>制作環境<span class="must">*</span></th>
			<td class="must"></td>
			<td><p>{$form.production_env|nl2br}</p></td>
		</tr>
		<tr>
			<th>対応範囲<span class="must">*</span></th>
			<td class="must"></td>
			<td><p>{$form.coverage|@join:", "}</p></td>
		</tr>
		<tr>
			<th>営業時間<span class="must">*</span></th>
			<td class="must"></td>
			<td><p>{$form.business_hours}</p></td>
		</tr>
		<tr>
			<th>打ち合わせ方法<span class="must">*</span></th>
			<td class="must"></td>
			<td><p>{$form.meeting_method|@join:", "|show_meeting_other:$form.meeting_other1}</p>
				{if $form.meeting_other2!=""}
					<p style="margin-top:4px;"><strong>その他</strong></p>
					<p>{$form.meeting_other2|nl2br}</p>
				{/if}
			</td>
		</tr>
		<tr>
			<th>新着案件のメール配信<span class="must">*</span></th>
			<td class="must"><span class="lock"></span></td>
			<td><p>{if $form.mailmaga_flag==1}受け取る{elseif $form.mailmaga_flag==2}受け取らない{/if}</p></td>
		</tr>
	</tbody>
</table>
</form>
<form id="backform" method="post" action="edit">
{include file="_parts/hidden.tpl"}
<div id="btn-area">
<p style="margin-bottom:5px;text-align:right;"><a href="#" onclick="$('#backform').submit(); return false;">&laquo; 入力した内容を修正する</a></p>
<input onclick="$('#mainform').submit();" id="btn-mainform" type="button" value="保存する" />
</div>
</form>
<!-- #regist-form --></div>
