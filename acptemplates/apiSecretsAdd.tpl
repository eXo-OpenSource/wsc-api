{include file='header' pageTitle='wcf.acp.menu.link.wscApi.secrets.add'}
<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.wscApi.secrets.{$action}{/lang}</h1>
		{if $action == 'edit'}<p class="contentHeaderDescription"></p>{/if}
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			{if $action == 'edit'}
				
				<li><a class="jsButtonBoardCopy button"><span class="icon icon16 fa-files-o"></span> <span>{lang}wbb.acp.board.button.copy{/lang}</span></a></li>
			{/if}
			<li><a href="{link application='wbb' controller='BoardList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wbb.acp.menu.link.board.list{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<form method="post" action="{if $action == 'add'}{link application='wcf' controller='ApiSecretsAdd'}{/link}{else}{link application='wcf' controller='ApiSecretsEdit' id=$secretID}{/link}{/if}">

    <dl{if $errorField == 'secret'} class="formError"{/if}>
        <dt><label for="secret">{lang}wcf.acp.wscApi.secret{/lang}</label></dt>
            <dd>
                <input type="text" id="secret" name="secret" value="{$secret}" maxlength="255" class="medium">
                {if $errorField == 'secret'}
                    <small class="innerError">
                    {if $errorType == 'empty'}
                        {lang}wcf.global.form.error.empty{/lang}
                    {elseif $errorType == 'multilingual'}
                        {lang}wcf.global.form.error.multilingual{/lang}
                    {else}
                        {lang}wbb.acp.board.title.error.{@$errorType}{/lang}
                    {/if}
                </small>
            {/if}
        </dd>
    </dl>
</form>

{include file='footer'}