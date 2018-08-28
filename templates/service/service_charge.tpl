<div id="gnavi">
<a class="gnavi-link" href="{$HTTP_URL}">HOME</a>&gt;
<a class="gnavi-last" href="{$base_url}" title="{$form.htitle}">{$form.htitle}</a>
</div>

<div id="main-content" style="width:920px;">
<div class="normal-frame">
<div id="service">

<ul class="tabs bottom25">
<li><a href="/service/" title="Advinerについて">Advinerについて</a></li>
<li class="active"><a href="/service/charge" title="有料アドバイスについて">有料アドバイスについて</a></li>
{*<li><a href="/service/pay" title="有料相談する方法">有料相談する方法</a></li>*}
<li><a href="/help/" title="よくある質問">よくある質問</a></li>
</ul>

<h1 class="service-title">有料アドバイスについて</h1>

<h2 class="sub-title">相談料の７０％が収入</h2>
<p class="bottom15">誰でも初期費用なしで、有料アドバイスを行うことができます。設定した相談料のうち、７０％がアドバイザーの報酬になります。</p>

<h2 class="sub-title">有料アドバイスで収入を得る仕組み</h2>
<p class="bottom15">誰でも有料相談窓口を開設（審査有り）することで、アドバイザーになることができます。相談料に応じた助言・アドバイスをすることで、相談者に課金される仕組みです。登録料や年会費等は一切かからず、相談料から利用手数料を差し引いた報酬が支払われます。</p>

<h2 class="sub-title">あなたの知識・経験が収入になる</h2>
<p class="bottom15">誰でも気付かないうちに、身近な人や友人などに簡単な助言やコンサルティングを行っていると思います。あなたのプロとしての知識や経験だけでなく、趣味の分野で誰にも負けない知識やユニークな経験などをアドバイスの形でサービスとして提供してみませんか？</p>

<h2 class="sub-title">相談された時だけ働く</h2>
<p class="bottom20">有料アドバイスにかかる費用は、相談された時に発生する相談料の手数料（３０％）のみ。とりあえず相談窓口を開設しておけば、相談された時にアドバイスを回答するだけです。</p>

<h2 class="service-title">有料アドバイスから報酬のお支払いまで</h2>

<div style="margin:30px 0;">
<img src="/img/service_charge.png" width="800" height="153" alt="有料アドバイスから報酬のお支払いまでの流れ" />
</div>

<h2 class="service-title">お支払いする報酬について</h2>

<p>お支払いする報酬の計算式は以下になります。</p>
<div style="margin:10px 0 10px 12px;font-size:124%;font-weight:bold;">相談料 × 0.7 ＝ お支払いする報酬</div>
<ul class="pin-line bottom20">
<li>相談料は、100円 ～ 3,000円の範囲で設定していただけます。</li>
<li>報酬は、毎月月末に確定し翌々月20日に振り込まれます。</li>
<li>月末時点の残高が3,000円以上の場合、ご指定の口座に報酬が振り込まれます。<br />
	3,000円未満の場合は翌月に繰り越されます。</li>
</ul>

<h2 class="service-title">こんな方に活用してほしい！</h2>
<ul class="pin-line bottom20">
<li>士業の方やフリーランスの専門家など、ご自分のビジネスに繋げる前段階として</li>
<li>知識と経験を持て余している主婦など、時間に制約されない副業として</li>
</ul>

{if !$userInfo}
<div class="blue-frame" style="text-align:center;">
<p style="margin-bottom:5px;font-size:124%;">さっそくはじめてみましょう！</p>
{include file="_parts/side_login_btn.tpl" is_fb_likebox=false}
</div>
{/if}

</div>
</div>
</div>
