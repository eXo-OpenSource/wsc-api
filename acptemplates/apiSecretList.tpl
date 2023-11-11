{include file='header' pageTitle='wcf.acp.menu.link.wscApi.secrets.list'}

<script data-relocate="true">
	$(function() {
		new WCF.Action.Delete('wcf\\data\\ApiSecretAction', '.jsSecretRow');
	});
</script>

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.wscApi.secrets.list{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link application='wcf' controller='ApiSecretAdd'}{/link}" class="button">{icon name='plus' size=16} <span>{lang}wcf.acp.menu.link.wscApi.secrets.add{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{hascontent}
	<div id="boardNodeList" class="section sortableListContainer">
		<ol id="boardContainer0" class="sortableList" data-object-id="0">
			<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>{lang}wcf.global.objectID{/lang}</th>
					<th>{lang}wcf.acp.secret.secretDescription{/lang}</th>
				</tr>
			</thead>
			<tbody>
			{content}
				{foreach from=$secrets item=secret}
					<tr class="jsSecretRow">
						<td class="columnIcon">
							<a href="{link controller='ApiSecretEdit' id=$secret.secretID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">{icon name='pencil' size=16}</a>
							<span class="icon icon16 fa-times jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$secret.secretID}" data-confirm-message-html="{lang}TODO{/lang}"></span>
						</td>
						<td>{$secret.secretID}</td>
						<td><a title="{lang}wcf.acp.user.edit{/lang}" href="{link controller='ApiSecretEdit' id=$secret.secretID}{/link}">{$secret.secretDescription}</a></td>
					</tr>
					
				{/foreach}
			{/content}
			</tbody>
			</table>
		</ol>
	</div>
{hascontentelse}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/hascontent}

{include file='footer'}
