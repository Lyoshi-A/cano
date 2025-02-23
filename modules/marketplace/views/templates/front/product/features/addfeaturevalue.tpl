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

{extends file=$layout}
{block name='header'}
	{include file='module:marketplace/views/templates/front/_partials/header.tpl'}
{/block}
{block name='content'}
{if $logged}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{if isset($id_feature_value)}{l s='Edit Value' mod='marketplace'}{else}{l s='Add New Value' mod='marketplace'}{/if}</span>
			</div>
			<div class="wk-mp-right-column">
				{if isset($mp_error_message) && $mp_error_message}
					<div class="alert alert-danger">
						{$mp_error_message|escape:'htmlall':'UTF-8'}
					</div>
				{else}
				<form action="{if isset($id_feature_value)}{$link->getModuleLink('marketplace', 'addfeaturevalue', ['id_feature_value' => $id_feature_value])|escape:'htmlall':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'addfeaturevalue')|escape:'htmlall':'UTF-8'}{/if}" method="POST" class=" defaultForm">
					<input type="hidden" name="default_lang" id="default_lang" value="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}">
					{block name='change-product-language'}
						{include file='module:marketplace/views/templates/front/product/_partials/change-product-language.tpl'}
					{/block}
					<div class="form-group">
						<label for="feature_group" class="control-label required">
							{l s='Feature type' mod='marketplace'}
						</label>
						<div class="row">
							<div class="col-md-7">
								{if isset($id_feature_value)}
									<select name="feature_group" id="feature_group" class="form-control">
										<option selected="selected" value="{$feature_info.id|escape:'htmlall':'UTF-8'}">{$feature_info.name|escape:'htmlall':'UTF-8'}</option>
									</select>
								{else}
									<select name="feature_group" id="feature_group" class="form-control lang_height">
										{foreach $feature_set as $feature_set_each}
										<option {if isset ($id_feature)}{if ($id_feature == $feature_set_each.id)} selected="selected" {/if}{/if} value="{$feature_set_each.id|escape:'htmlall':'UTF-8'}">{$feature_set_each.name|escape:'htmlall':'UTF-8'}</option>
										{/foreach}
									</select>
								{/if}
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="feature_value" class="control-label required">
							{l s='Value' mod='marketplace'}
						</label>
						{foreach from=$languages item=language}
							{assign var="feature_value" value="feature_value_`$language.id_lang`"}
							<input type="text"
							id="feature_value_{$language.id_lang|escape:'htmlall':'UTF-8'}"
							name="feature_value_{$language.id_lang|escape:'htmlall':'UTF-8'}"
							class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'} {if $current_lang.id_lang == $language.id_lang}current_feature_val{/if}"
							value="{if isset($smarty.post.$feature_value)}{$smarty.post.$feature_value|escape:'htmlall':'UTF-8'}{elseif isset($id_feature_value)}{$feature_val[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
							data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}"
							{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}
							maxlength="255" />
						{/foreach}
					</div>
					{block name='mp-form-fields-notification'}
						{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-notification.tpl'}
					{/block}
					<div class="form-group row">
						<div class="col-xs-4 col-sm-4 col-md-6">
							<a href="{if isset($id_feature)}{$link->getModuleLink('marketplace', 'viewfeaturevalue', ['id_feature' => $id_feature])|escape:'htmlall':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'viewfeaturevalue')|escape:'htmlall':'UTF-8'}{/if}" class="btn wk_btn_cancel wk_btn_extra">
								{l s='Cancel' mod='marketplace'}
							</a>
						</div>
						<div class="col-xs-8 col-sm-8 col-md-6 wk_text_right" data-action="{l s='Save' mod='marketplace'}">
							<button type="submit" id="SubmitFeatureValue" name="SubmitFeatureValue" class="btn btn-success wk_btn_extra form-control-submit">
								<span>{l s='Save' mod='marketplace'}</span>
							</button>
						</div>
					</div>
				</form>
				{/if}
			</div>
		</div>
	</div>
{/if}
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}