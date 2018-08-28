{if $user.consult_mail_to!='1' && $user.consult_reply_to!='1' && $user.advice_reply_to!='1' && $user.consult_review_to!='1'}
<p>メール通知しない</p>
{/if}
{if $user.consult_mail_to=='1'}<p>相談者から相談された場合</p>{/if}
{if $user.consult_reply_to=='1'}<p>相談者から返信があった場合</p>{/if}
{if $user.advice_reply_to=='1'}<p>アドバイザーから返信があった場合</p>{/if}
{if $user.consult_review_to=='1'}<p>相談者から評価コメントがあった場合</p>{/if}
