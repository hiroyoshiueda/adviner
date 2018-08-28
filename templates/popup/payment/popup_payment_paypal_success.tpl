<div id="payment_frame">
{if $form.is_error}
<p>システムエラーが発生し、決済処理が完了しませんでした。</p>
<p>しばらくしてから再度お試しください。</p>
<a class="small-btn green-btn" onclick="parent.Adviner.closePopupPayment();return false;">閉じる</a>
{else}
<p>ありがとうございます。決済が完了しました。</p>
<script>
//parent.Adviner.closePopupPayment();
parent.window.location.reload(true);
</script>
{/if}
</div>
