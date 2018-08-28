<div id="payment_frame">
{if $form.is_error}
<p>システムエラーが発生しました。</p>
<p>しばらくしてから再度お試しください。</p>
{else}
<p>この有料相談は<span class="highlight">お支払い済</span>のようです。</p>
<p>ご確認をお願いします。</p>
{/if}
<a class="small-btn green-btn" onclick="top.Adviner.closePopupPayment();return false;">閉じる</a>
</div>
