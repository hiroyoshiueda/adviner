{if $form.save}
<div id="alert-successmsg" class="successmsg" style="display:none;">
<span class="img-success"></span>&nbsp;保存しました。
</div>
{else if $form.delete}
<div id="alert-successmsg" class="successmsg" style="display:none;">
<span class="img-success"></span>&nbsp;削除しました。
</div>
{else if $form.new_advice}
<div id="alert-successmsg" class="successmsg" style="margin-top:10px;display:none;">
<span class="img-success"></span>&nbsp;相談窓口の登録を申し込みました。
</div>
{else if $form.edit_advice}
<div id="alert-successmsg" class="successmsg" style="margin-top:10px;display:none;">
<span class="img-success"></span>&nbsp;相談窓口を変更しました。
</div>
{else if $form.chage_advice_status=="1"}
<div id="alert-successmsg" class="successmsg" style="margin-top:10px;display:none;">
<span class="img-success"></span>&nbsp;相談窓口の受付を再開しました。
</div>
{else if $form.chage_advice_status=="0"}
<div id="alert-successmsg" class="successmsg" style="margin-top:10px;display:none;">
<span class="img-success"></span>&nbsp;相談窓口の受付を停止しました。
</div>
{else if $form.delete_advice}
<div id="alert-successmsg" class="successmsg" style="margin-top:10px;display:none;">
<span class="img-success"></span>&nbsp;相談窓口を削除しました。
</div>
{else if $form.post_advice}
<div id="alert-successmsg" class="successmsg" style="margin-top:10px;display:none;">
<span class="img-success"></span>&nbsp;アドバイスを送信しました。
</div>
{else if $form.post_consult}
<div id="alert-successmsg" class="successmsg" style="margin-top:10px;display:none;">
<span class="img-success"></span>&nbsp;相談内容を送信しました。
</div>
{else if $show_successmsg}
<div id="alert-successmsg" class="successmsg" style="display:none;">
<span class="img-success"></span>&nbsp;{$show_successmsg}
</div>
{/if}
