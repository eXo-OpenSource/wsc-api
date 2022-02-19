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
			{foreach from=$apiData item=data}
			<li><a href="{@$__wcf->getAnchor($data['endpoint'])}">{lang}wcf.acl.option.category.at.megathorx.wsc_api.apiSecret.api.{$data['name']}{/lang}</a></li>
			{/foreach}
			{event name='tabMenuTabs'}
		</ul>
	</nav>

	<div id="general" class="hidden tabMenuContent">
		<div class="section">
			<pre>/index.php?user-api</pre>
		</div>
	</div>

	{foreach from=$apiData item=data}
	<div id="{$data['endpoint']}" class="hidden tabMenuContent">
		{foreach from=$data['methods'] item=method}
		<div class="section">
			<h2 class="sectionTitle">{lang}wcf.api.documentation.{$data['name']}.{$method['name']}{/lang}</h2>
			<dl>
				<dt><label>{lang}wcf.api.documentation.endpoint{/lang}</label></dt>
				<dd>POST <kbd>{$host}/index.php?{$data['endpoint']}-api&method={$method['name']}</kbd></dd>
			</dl>
            {capture assign='requiredParams'}
                {foreach from=$method['params'] item=param}
                {if !$param['hasDefaultValue']}
                <tr>
                    <td><kbd>{$param['name']}</kbd></td>
                    <td><kbd>{$param['types_text']}</kbd></td>
                    <td>{lang}wcf.api.documentation.{$data['name']}.{$method['name']}.{$param['name']}{/lang}</td>
                </tr>
                {/if}
                {/foreach}
            {/capture}
            {assign var='requiredParams' value=$requiredParams|trim}

            {capture assign='optionalParams'}
                {foreach from=$method['params'] item=param}
                    {if $param['hasDefaultValue']}
                    <tr>
                        <td><kbd>{$param['name']}</kbd></td>
                        <td><kbd>{$param['types_text']}</kbd></td>
                        <td>{if $param['defaultValue'] != null}<kbd>{$param['defaultValue']}</kbd>{/if}</td>
                        <td>{lang}wcf.api.documentation.{$data['name']}.{$method['name']}.{$param['name']}{/lang}</td>
                    </tr>
                    {/if}
                {/foreach}
            {/capture}
            {assign var='optionalParams' value=$optionalParams|trim}
			<dl>
				<dt><label>{lang}wcf.api.documentation.requiredParameters{/lang}</label></dt>
                <table class="table">
                    <thead>
                        <tr>
                            <th>{lang}wcf.api.documentation.name{/lang}</th>
                            <th>{lang}wcf.api.documentation.type{/lang}</th>
                            <th>{lang}wcf.api.documentation.description{/lang}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><kbd>secret</kbd></td>
                            <td><kbd>string</kbd></td>
                            <td>{lang}wcf.acp.secret.secret{/lang}</td>
                        </tr>

                        {if !$requiredParams|empty}
                            {@$requiredParams}
                        {/if}
                    </tbody>
                </table>
			</dl>
            {if !$optionalParams|empty}
			<dl>
				<dt><label>{lang}wcf.api.documentation.optionalParameters{/lang}</label></dt>

                <table class="table">
                    <thead>
                        <tr>
                            <th>{lang}wcf.api.documentation.name{/lang}</th>
                            <th>{lang}wcf.api.documentation.type{/lang}</th>
                            <th>{lang}wcf.api.documentation.defaultValue{/lang}</th>
                            <th>{lang}wcf.api.documentation.description{/lang}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {@$optionalParams}
                    </tbody>
                </table>
			</dl>
            {/if}
		</div>
		{/foreach}
	</div>
	{/foreach}
</div>

{include file='footer'}
