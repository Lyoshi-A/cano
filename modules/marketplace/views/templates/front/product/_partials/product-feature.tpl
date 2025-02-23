{**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{if isset($available_features) && !empty($available_features)}
<div class="content" id="features-content">
	<div id="wk_display_none" class="alert alert-danger wk_display_none"></div>
	<input type="hidden" name="wk_feature_row" id="wk_feature_row" value="{if isset($productfeature)}{count($productfeature)|escape:'htmlall':'UTF-8'}{else}0{/if}">
	{if isset($productfeature) && !empty($productfeature)}
		{foreach $productfeature as $key => $selectedfeature}
		<div class="row content wk_mp_feature_row" id="wk_mp_feature_row_field">
			<div class="col-lg-12 col-xl-4">
				<fieldset class="form-group">
					<label class="form-control-label">{l s='Feature' mod='marketplace'}</label>
					<select data-id-feature="{$key+1|escape:'htmlall':'UTF-8'}" class="form-control form-control-select wk_mp_feature" name="wk_mp_feature_{$key+1|escape:'htmlall':'UTF-8'}" >
						<option value="0">{l s='Choose a feature' mod='marketplace'}</option>
						{foreach $available_features as $feature}
							<option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}" {if $selectedfeature.id_feature == $feature.id_feature}selected="selected"{/if}>{$feature.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</fieldset>
			</div>
			<div class="col-lg-12 col-xl-4">
				<fieldset class="form-group">
					<label class="form-control-label">{l s='Pre-defined value' mod='marketplace'}</label>
					<select data-id-feature-val="{$key+1|escape:'htmlall':'UTF-8'}" class="form-control form-control-select wk_mp_feature_val" name="wk_mp_feature_val_{$key+1|escape:'htmlall':'UTF-8'}">
						<option {if !isset($selectedfeature.id_feature_value)}selected="selected"{/if} value="0">{l s='Choose a value' mod='marketplace'}</option>
						{foreach $selectedfeature.field_value_option as $ps_feature_value}
							<option {if $ps_feature_value.id_feature_value == $selectedfeature.id_feature_value}selected="selected"{/if} value="{$ps_feature_value.id_feature_value|escape:'htmlall':'UTF-8'}">{$ps_feature_value.value|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</fieldset>
			</div>
			<div class="col-lg-12 col-xl-3">
				<fieldset class="form-group">
					<label class="form-control-label">
						{l s='OR Customized value' mod='marketplace'}

						{if $allow_multilang && $total_languages > 1}
							<img class="all_lang_icon" data-lang-id="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}" src="{$ps_img_dir|escape:'htmlall':'UTF-8'}{$current_lang.id_lang|escape:'htmlall':'UTF-8'}.jpg">
						{/if}
					</label>
					<div class="translationsFields translation-label-en">
						{foreach from=$languages item=language}
							{assign var="wk_mp_feature_custom" value="wk_mp_feature_custom_`$language.id_lang`"}
							<input type="text"
							name="wk_mp_feature_custom_{$language.id_lang|escape:'htmlall':'UTF-8'}_{$key+1|escape:'htmlall':'UTF-8'}"
							value="{if isset($smarty.post.$wk_mp_feature_custom)}{$smarty.post.$wk_mp_feature_custom|escape:'htmlall':'UTF-8'}{else if isset($selectedfeature.custom) && $selectedfeature.custom && isset($selectedfeature.mp_field_value.{$language.id_lang|escape:'htmlall':'UTF-8'}.value)|escape:'htmlall':'UTF-8'}{$selectedfeature.mp_field_value.{$language.id_lang|escape:'htmlall':'UTF-8'}.value|escape:'htmlall':'UTF-8'}{/if}"
							class="form-control wkmp_feature_custom wk_mp_feature_custom_{$language.id_lang|escape:'htmlall':'UTF-8'} custom_value_{$key+1|escape:'htmlall':'UTF-8'}"
							data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}"
							{if $current_lang.id_lang != $language.id_lang} style="display: none;"{/if}/>
						{/foreach}
					</div>
				</fieldset>
			</div>
			{if !isset($editPermissionNotAllow) && $permissionData.featuresPermission.delete}
				<div class="col-lg-1 col-xl-1 wk_mp_feature_delete_row" data-feature-delete="1">
					<fieldset class="form-group">
						<label class="form-control-label">&nbsp;</label>
						<a title="{l s='Delete' mod='marketplace'}" href="javascript:void(0)" class="btn btn-invisible btn-block wkmp_feature_delete" type="button" style="padding: 0px;">
							<i class="material-icons">&#xE872;</i>
						</a>
					</fieldset>
				</div>
			{/if}
		</div>
		{/foreach}
	{/if}

</div>
<div class="row" id="wk_mp_feature_more">
	<div class="col-md-4">
		{if isset($controller) && $controller == 'admin'}
			<button id="add_feature_button" class="btn btn-info sensitive add" type="button">
				<i class="icon-plus"></i>
				{l s='Add Feature' mod='marketplace'}
			</button>
		{else if !isset($editPermissionNotAllow) && $permissionData.featuresPermission.edit}
			<button id="add_feature_button" class="btn btn-primary sensitive add" type="button">
				<i class="material-icons">&#xE145;</i>
				{l s='Add Feature' mod='marketplace'}
			</button>
		{/if}
		<img class="wk-feature-loader wk_display_none" src="{$module_dir|escape:'htmlall':'UTF-8'}marketplace/views/img/loader.gif" width="25" />
	</div>
</div>
{else}
	<div class="alert alert-warning">{l s='No Features Available' mod='marketplace'}</div>
{/if}
{if isset($controller) && $controller == 'admin'}
<style type="text/css">
	.col-xl-4 {
		width: 25% !important;
		margin-right: 50px;
	}
	.col-xl-3 {
		width: 25% !important;
	}
	#features-content h2 {
		color: #363a41;
    	font-weight: 600;
    	font-size: 1rem;
    	margin-bottom: 0.9375rem;
    	font-family: Open Sans,sans-serif;
    	line-height: 1.1;
    	margin-top: 0;
	}
</style>
{/if}