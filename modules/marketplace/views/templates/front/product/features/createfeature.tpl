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
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">
					{if isset($id_feature)}
						{l s='Edit Feature' mod='marketplace'}
					{else}
						{l s='Add New Feature' mod='marketplace'}
					{/if}
				</span>
			</div>
			<div class="wk-mp-right-column">
				{if isset($mp_error_message) && $mp_error_message}
					<div class="alert alert-danger">
						{$mp_error_message|escape:'htmlall':'UTF-8'}
					</div>
				{else}
				<form action="{if isset($id_feature)}{$link->getModuleLink('marketplace', 'createfeature', ['id_feature' => $id_feature])|escape:'htmlall':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'createfeature')|escape:'htmlall':'UTF-8'}{/if}" method="POST" class=" defaultForm">
					<input type="hidden" name="default_lang" id="default_lang" value="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}">
					{block name='change-product-language'}
						{include file='module:marketplace/views/templates/front/product/_partials/change-product-language.tpl'}
					{/block}
					<div class="form-group">
						<label for="feature_name" class="control-label required">
							{l s='Feature name' mod='marketplace'}
						</label>
						{foreach from=$languages item=language}
							{assign var="feature_name" value="feature_name_`$language.id_lang`"}
							<input type="text"
							id="feature_name_{$language.id_lang|escape:'htmlall':'UTF-8'}"
							name="feature_name_{$language.id_lang|escape:'htmlall':'UTF-8'}"
							class="form-control wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'} {if $current_lang.id_lang == $language.id_lang}current_feature_name{/if}"
							data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}"
							value="{if isset($smarty.post.$feature_name)}{$smarty.post.$feature_name|escape:'htmlall':'UTF-8'}{elseif isset($id_feature)}{$feature_name_val[{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
							{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}
							maxlength="128" />
						{/foreach}
					</div>
					{block name='mp-form-fields-notification'}
						{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-notification.tpl'}
					{/block}
					<div class="form-group row">
						<div class="col-xs-4 col-sm-4 col-md-6">
							<a href="{$link->getModuleLink('marketplace', 'productfeature')|escape:'htmlall':'UTF-8'}" class="btn wk_btn_cancel wk_btn_extra">
								{l s='Cancel' mod='marketplace'}
							</a>
						</div>
						<div class="col-xs-8 col-sm-8 col-md-6 wk_text_right" data-action="{l s='Save' mod='marketplace'}">
							<button type="submit" id="SubmitFeature" name="SubmitFeature" class="btn btn-success wk_btn_extra form-control-submit">
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