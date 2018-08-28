<div style="text-align:center;width:250px;margin:0 auto;">
<a id="facebook-login-btn" class="btn-login-facebook" title="advinerにログイン">advinerにログイン</a>
<p id="login-small-msg">ログインするには<a href="http://www.facebook.com/" target="_blank" title="Facebookアカウントの新規登録">Facebookアカウント</a>が必要です。</p>
<div id="login-rememberme">{tag type="checkbox" id="rememberme" name="rememberme" value="1" checked=$form.rememberme label="ログイン状態を保存する"}</div>
{if $nanapi}
<div style="width:240px;margin:20px auto -15px auto;">
<a href="http://nanapi.jp/web/adviner" target="_blank"><img src="/img/nanapi.png" width="240" height="70" alt="nanapi web（ナナピウェブ）" /></a>
</div>
{/if}
{if $is_fb_likebox}
<div style="background-color:#fff;width:240px;height:308px;margin:30px auto 0 auto;">
<iframe src="//www.facebook.com/plugins/likebox.php?href={"http://www.facebook.com/advinercom"|escape:"url"}&amp;width=240&amp;colorscheme=light&amp;show_faces=true&amp;border_color=%23DCC7A7&amp;stream=false&amp;header=false&amp;height=308" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:240px; height:308px;" allowTransparency="true"></iframe>
</div>
{/if}
</div>