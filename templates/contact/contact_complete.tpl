<div id="main-content">

<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-link" href="/contact/">お問い合わせ</a>&gt;
<a class="gnavi-last" href="{$HTTPS_URL}contact/form">{$form.htitle}</a>
</div>

<div id="contact">
<h1 class="page-title">{$form.htitle}</h1>
<div class="successmsg" style="font-weight:bold;font-size:110%;margin-top:20px;">
<span class="success-icon"></span>&nbsp;お問い合わせ内容を送信しました。
</div>
<div style="text-align:center;margin-bottom:20px;">
<p>お問い合わせありがとうございました。<br />
土日祝日はサポート業務をお休みさせて頂いております。<br />
平日はスタッフが確認次第なるべく早く対応させて頂きますが、<br />
混み具合により、若干お時間を頂く場合があります。ご了承ください。</p>
</div>
<div class="infomsg">
<p><strong>携帯電話メールアドレスでお問合せいただくお客様へ</strong><br />
迷惑メール拒否設定などをされている場合、返信メールがお手元に届かない場合がございますので<br />
[ {$smarty.const.APP_CONST_SITE_DOMAIN} ] を必ずドメイン指定受信ができるよう設定をお願いいたします</p>
</div>

<div style="text-align:center;"><a href="/">トップページへ</a></div>

</div>
</div>

<div id="side-content">

{include file="_parts/side_feedback_form.tpl"}

</div>
