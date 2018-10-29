{include file='header' pageTitle='wcf.acp.menu.link.wscApi.secrets.'|concat:$action}
{include file='aclPermissions'}
<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.wscApi.secrets.{$action}{/lang}</h1>
		{if $action == 'edit'}<p class="contentHeaderDescription"></p>{/if}
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link application='wcf' controller='ApiSecretList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.menu.link.wscApi.secrets.list{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<form method="post" id="secretForm" action="{if $action == 'add'}{link application='wcf' controller='ApiSecretAdd'}{/link}{else}{link application='wcf' controller='ApiSecretEdit' id=$secretID}{/link}{/if}">
	<div class="section tabMenuContainer" data-active="{$activeTabMenuItem}" data-store="activeTabMenuItem">
		<nav class="tabMenu">
			<ul>
				<li><a href="{@$__wcf->getAnchor('general')}">{lang}wcf.acp.secret.category.general{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('permissions')}">{lang}wcf.acp.secret.category.permissions{/lang}</a></li>
				{event name='tabMenuTabs'}
			</ul>
		</nav>

        <div id="general" class="tabMenuContent">
            <div class="section">
                <dl{if $errorField == 'secretKey'} class="formError"{/if}>
                    <dt><label for="secretKey">{lang}wcf.acp.secret.secret{/lang}</label></dt>
                        <dd>
                            <input type="text" id="secretKey" name="secretKey" value="{if $secretKey|isset && $secretKey != ''}{$secretKey}{elseif $apiSecret|isset}{$apiSecret->secretKey}{/if}" maxlength="255" class="medium">
                            <small>{lang}wcf.acp.secret.generation{/lang}</small>
                            {if $errorField == 'secretKey'}
                                <small class="innerError">
                                {if $errorType == 'empty'}
                                    {lang}wcf.global.form.error.empty{/lang}
                                {else}
                                    {lang}wcf.acp.secret.error.{@$errorType}{/lang}
                                {/if}
                            </small>
                        {/if}
                    </dd>
                </dl>

                <dl{if $errorField == 'secretDescription'} class="formError"{/if}>
                    <dt><label for="secretDescription">{lang}wcf.acp.secret.secretDescription{/lang}</label></dt>
                        <dd>
                            <input type="text" id="secretDescription" name="secretDescription" value="{if $secretDescription|isset && $secretDescription != ''}{$secretDescription}{elseif $apiSecret|isset}{$apiSecret->secretDescription}{/if}" maxlength="255" class="medium">
                            {if $errorField == 'secretDescription'}
                                <small class="innerError">
                                {if $errorType == 'empty'}
                                    {lang}wcf.global.form.error.empty{/lang}
                                {/if}
                            </small>
                        {/if}
                    </dd>
                </dl>
            </div>
        </div>

        
        <div id="permissions" class="tabMenuContent">
            <div class="section">
                <dl id="permissionsContainer">
                    <dd>
                        <ul id="permissionList" class="aclPermissionList containerList" data-grant="{lang}wcf.acl.option.grant{/lang}" data-deny="{lang}wcf.acl.option.deny{/lang}">

                            <li class="aclFullAccess"><span>{lang}wcf.acl.option.fullAccess{/lang}</span>
                                <label class="jsTooltip" title="{lang}wcf.acl.option.grant{/lang}">
                                    <input type="checkbox" data-type="grant" {if $permissions.grantAll|isset && $permissions.grantAll == true}checked{/if} id="grantAll_permissionsContainer">
                                </label>
                                <label class="jsTooltip" title="{lang}wcf.acl.option.deny{/lang}">
                                    <input type="checkbox" data-type="deny" {if $permissions.denyAll|isset && $permissions.denyAll == true}checked{/if} id="denyAll_permissionsContainer">
                                </label>
                            </li>

                        {foreach from=$permissions.categories key=category item=categoryName}
                            <li class="aclCategory">{lang}{$categoryName}{/lang}</li>
                            {foreach from=$permissions.options key=id item=option}
                                {if $option.categoryName == $category}
                                <li><span>{lang}{$option.label}{/lang}</span>
                                    <label class="jsTooltip" title="{lang}wcf.acl.option.grant{/lang}">
                                        <input type="checkbox" data-type="grant" {if $permissions.option[$id]|isset}{if $permissions.option[$id] == 1}checked{/if}{/if} data-option-id="{$id}" id="grant{$id}">
                                    </label>
                                    <label class="jsTooltip" title="{lang}wcf.acl.option.deny{/lang}">
                                        <input type="checkbox" data-type="deny" {if $permissions.option[$id]|isset}{if $permissions.option[$id] == 0}checked{/if}{/if} data-option-id="{$id}" id="deny{$id}">
                                    </label>
                                </li>
                                {/if}
                            {/foreach}
                        {/foreach}
                        </ul>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    


    
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

<script data-relocate="true">
    WCF.API = {};
    WCF.API.ACL = Class.extend({
		/**
		 * list of ACL container elements
		 * @var        object
		 */
		_containerElements: {
            grantAll: null,
            denyAll: null,
            permissionList: []
        },

        init: function() {
            $('#permissionList').find('input[type=checkbox]').each($.proxy(function(index, item) {
                var $item = $(item); 
                var type = $item.data('type');
                if ($item.prop('id') === 'grantAll_permissionsContainer') {
                    this._containerElements.grantAll = $item;
                    $item.change($.proxy(this._changeAll, this));
                } else if ($item.prop('id') === 'denyAll_permissionsContainer') {
                    this._containerElements.denyAll = $item;
                    $item.change($.proxy(this._changeAll, this));
                } else {
                    this._containerElements.permissionList.push($item);
                    $item.change($.proxy(this._change, this));
                }
            }, this));

            
			var $form = $($('#secretForm'));
			$form.submit($.proxy(this.submit, this));
        },

        _changeAll: function(event) {
			var $checkbox = $(event.currentTarget);
			var $type = $checkbox.data('type');
			
			if ($checkbox.is(':checked')) {
				if ($type === 'deny') {
					this._containerElements.grantAll.prop('checked', false);
					
					this._containerElements.permissionList.forEach($.proxy(function ($item) {
						if ($item.data('type') === 'deny' && $item.attr('id') !== 'denyAll_permissionsContainery') {
							$item.prop('checked', true).trigger('change');
						}
					}, this));
				}
				else {
					this._containerElements.denyAll.prop('checked', false);
					
					this._containerElements.permissionList.forEach($.proxy(function ($item) {
						if ($item.data('type') === 'grant' && $item.attr('id') !== 'grantAll_permissionsContainer') {
							$item.prop('checked', true).trigger('change');
						}
					}, this));
				}
			}
			else {
				if ($type === 'deny') {
					this._containerElements.grantAll.prop('checked', false);
					
					this._containerElements.permissionList.forEach($.proxy(function ($item) {
						if ($item.data('type') === 'deny' && $item.attr('id') !== 'denyAll_permissionsContainery') {
							$item.prop('checked', false).trigger('change');
						}
					}, this));
				}
				else {
					this._containerElements.denyAll.prop('checked', false);
					
					this._containerElements.permissionList.forEach($.proxy(function ($item) {
						if ($item.data('type') === 'grant' && $item.attr('id') !== 'grantAll_permissionsContainer') {
							$item.prop('checked', false).trigger('change');
						}
					}, this));
				}
			}
        },

        _change: function (event) {
            var $checkbox = $(event.currentTarget);
            var $optionID = $checkbox.data('optionID');
            var $type = $checkbox.data('type');
            
            if ($checkbox.is(':checked')) {
                if ($type === 'deny') {
                    $('#grant' + $optionID).prop('checked', false);
                        
                    if (this._containerElements.grantAll !== null) {
                        this._containerElements.grantAll.prop('checked', false);
                    }
                }
                else {
                    $('#deny' + $optionID).prop('checked', false);
                        
                    if (this._containerElements.denyAll !== null) {
                        this._containerElements.denyAll.prop('checked', false);
                    }
                }
            }
            else {
                if ($type === 'deny' && this._containerElements.denyAll !== null) {
                       this._containerElements.denyAll.prop('checked', false);
                }
                else if ($type === 'grant' && this._containerElements.grantAll !== null) {
                    this._containerElements.grantAll.prop('checked', false);
                }
            }
                
            var $allChecked = true;
            this._containerElements.permissionList.forEach($.proxy(function ($item) {
                if ($item.data('type') === $type && $item.attr('id') !== $type + 'All_permissionsContainer') {
                    if (!$item.is(':checked')) {
                        $allChecked = false;
                        return false;
                    }
                }
            }, this));
            if ($type == 'deny') {
                if (this._containerElements.denyAll !== null) {
                    if ($allChecked) this._containerElements.denyAll.prop('checked', true);
                    else this._containerElements.denyAll.prop('checked', false);
                }
            }
            else {
                if (this._containerElements.grantAll !== null) {
                    if ($allChecked) this._containerElements.grantAll.prop('checked', true);
                    else this._containerElements.grantAll.prop('checked', false);
                }
            }
        },
        
        submit: function (event) {
			this._savePermissions();
		},

        _savePermissions: function () {
			// clear old values
			this._values = {};
			this._containerElements.permissionList.forEach((function (checkbox) {
				var $checkbox = $(checkbox);


				if ($checkbox.attr('id') != 'grantAll_permissionsContainer' && $checkbox.attr('id') != 'denyAll_permissionsContainer') {
					var $optionValue = ($checkbox.data('type') === 'deny') ? 0 : 1;
					var $optionID = $checkbox.data('optionID');
					
					if ($checkbox.is(':checked')) {
						// store value
						this._values[$optionID] = $optionValue;
						
						// reset value afterwards
						$checkbox.prop('checked', false);
					}
				}

			}).bind(this));

            
			if ($.getLength(this._values)) {
			    var $form = $($('#secretForm'));
				
				for (var $optionID in this._values) {
					var $value = this._values[$optionID];

					$('<input type="hidden" name="aclValues[' + $optionID + ']" value="' + $value + '" />').appendTo($form);
				}
			}
		},
		
    });

	$(function() {
        (new WCF.API.ACL).init();
    });
</script>

{include file='footer'}