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
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{if isset($feature_name)}{$feature_name|escape:'htmlall':'UTF-8'}{/if}</span>
			</div>
			<div class="wk-mp-right-column">
				{if isset($smarty.get.success_attr)}
					<div class="error">
						{if $smarty.get.success_attr == 1}
							<div class="alert alert-success">{l s='Feature value added successfully.' mod='marketplace'}</div>
						{else if $smarty.get.success_attr == 2}
							<div class="alert alert-success">{l s='Feature value updated successfully.' mod='marketplace'}</div>
						{else if $smarty.get.success_attr == 3}
							<div class="alert alert-success">{l s='Feature value deleted successfully.' mod='marketplace'}</div>
						{/if}
					</div>
				{else if isset($smarty.get.error_attr)}
					<div class="error">
						<div class="alert alert-danger">{l s='This feature value is already in use you cannot edit or delete it.' mod='marketplace'}</div>
					</div>
				{/if}
				<div class="wk_product_list">
					<p class="wk_text_right">
						<a
						{if Configuration::get('PS_REWRITING_SETTINGS')}
							href="{$link->getModuleLink('marketplace', 'addfeaturevalue')|escape:'htmlall':'UTF-8'}?id_feature={Tools::getValue('id_feature')|escape:'htmlall':'UTF-8'}"
						{else}
							href="{$link->getModuleLink('marketplace', 'addfeaturevalue')|escape:'htmlall':'UTF-8'}&id_feature={Tools::getValue('id_feature')|escape:'htmlall':'UTF-8'}"
						{/if}
							>
							<button class="btn btn-primary btn-sm" type="button">
								<i class="material-icons">&#xE145;</i>
								{l s='Add Value' mod='marketplace'}
							</button>
						</a>
					</p>
					<div class="table-responsive">
						<table class="table table-striped" {if !(isset($empty_list))}id="wk_datatable_list"{/if}>
							<thead>
								<tr>
									<th>{l s='#' mod='marketplace'}</th>
									<th>{l s='ID' mod='marketplace'}</th>
									<th>{l s='Values' mod='marketplace'}</th>
									<th>{l s='Actions' mod='marketplace'}</th>
								</tr>
							</thead>
							{if !(isset($empty_list))}
								{assign var=num value=1}
								{foreach $value_set as $value_set_each}
									<tr>
										<td>{$num|escape:'htmlall':'UTF-8'}</td>
										<td>{$value_set_each['id']|escape:'htmlall':'UTF-8'}</td>
										<td>{$value_set_each['val_name']|escape:'htmlall':'UTF-8'}</td>
										<td>
											<a class="edit_button_v" title="{l s='Edit' mod='marketplace'}" edit="{$value_set_each['editable']|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('marketplace', 'addfeaturevalue',['id_feature_value' => $value_set_each['editable'],'id_feature'=>$id_feature])|addslashes}">
												<i class="material-icons">&#xE254;</i>
											</a>
											<a class="delete_button_v" title="{l s='Delete' mod='marketplace'}" edit="{$value_set_each['editable']|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('marketplace', 'viewfeaturevalue',['id_feature_value'=>$value_set_each['editable'], 'id_feature'=>$id_feature, 'delete_feature_val'=>1])|addslashes}">
												<i class="material-icons">&#xE872;</i>
											</a>
										</td>
									</tr>
									{$num = $num + 1}
								{/foreach}
							{else}
								<tr>
									<td colspan="4">
										<div id="empty_list">{l s='This feature have no values yet.' mod='marketplace'}</div>
									</td>
								</tr>
							{/if}
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
{/if}
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}