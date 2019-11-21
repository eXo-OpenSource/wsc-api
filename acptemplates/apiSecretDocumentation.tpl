{include file='header' pageTitle='wcf.acp.menu.link.wscApi.secrets.documentation'}

<script data-relocate="true">
	$(function() {
		new WCF.Action.Delete('wcf\\data\\ApiSecretAction', '.jsSecretRow');
	});
</script>

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.wscApi.secrets.documentation{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link application='wcf' controller='ApiSecretList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.menu.link.wscApi.secrets.list{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

<div class="section tabMenuContainer">
	<nav class="tabMenu">
		<ul>
			<li><a href="{@$__wcf->getAnchor('general')}">{lang}wcf.acp.secret.category.general{/lang}</a></li>
			{foreach from=$apiData key=name item=data}
			<li><a href="{@$__wcf->getAnchor($name)}">{lang}wcf.acl.option.category.at.megathorx.wsc_api.apiSecret.api.{$name}{/lang}</a></li>
			{/foreach}	
			{event name='tabMenuTabs'}
		</ul>
	</nav>

	<div id="general" class="hidden tabMenuContent">
		<div class="section">
			<pre>/index.php?user-api</pre>
		</div>
	</div>

	{foreach from=$apiData key=name item=data}
	<div id="{$name}" class="hidden tabMenuContent">
		{foreach from=$data item=method}
		<div class="section">
			<h2 class="sectionTitle">{$method['name']}</h2>
			<dl>
				<dt><label>Endpoint</label></dt>
				<dd><kbd>{$host}/index.php?{$name}-api&method={$method['name']}</kbd></dd>
			</dl>
			<dl>
				<dt><label>Benötigte Parameter</label></dt>
				{foreach from=$method['params'] item=param}
				<dd><kbd>{$param['name']}</kbd> - {$param['types_text']}</dd>
				{/foreach}
			</dl>
		</div>
		{/foreach}
	</div>
	{/foreach}
</div>

{include file='footer'}
