{include file='header' pageTitle='wcf.acp.menu.link.wscApi.secrets.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wbb.acp.board.list{/lang}</h1>
	</div>
</header>

{hascontent}
	<div id="boardNodeList" class="section sortableListContainer">
		<ol id="boardContainer0" class="sortableList" data-object-id="0">
			<table class="table">
			<thead>
				<tr>
					<th>{lang}wcf.global.objectID{/lang}</th>
					<th>{lang}wcf.acp.secret.secretDescription{/lang}</th>
				</tr>
			</thead>
			<tbody>
			{content}
				{foreach from=$secrets item=secret}
					<tr>
						<td><a title="{lang}wcf.acp.user.edit{/lang}" href="{link controller='ApiSecretEdit' id=$secret.secretID}{/link}">{$secret.secretID}</a></td>
						<td>{$secret.secretDescription}</td>
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
