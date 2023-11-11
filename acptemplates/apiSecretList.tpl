{include file='header' pageTitle='wcf.acp.menu.link.wscApi.secrets.list'}

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
			<table class="table jsObjectActionContainer" data-object-action-class-name="wcf\data\ApiSecretAction">
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
					<tr class="jsObjectActionObject" data-object-id="{$secret.secretID}">
						<td class="columnIcon">
							<a href="{link controller='ApiSecretEdit' id=$secret.secretID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">{icon name='pencil' size=16}</a>
							{objectAction action="delete" objectTitle=$secret.secretDescription}
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
