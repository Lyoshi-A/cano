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
					{l s='Product Features' mod='marketplace'}
				</span>
			</div>
			<div class="wk-mp-right-column">
				{if isset($smarty.get.success_attr)}
					<div class="error">
						{if $smarty.get.success_attr == 1}
							<div class="alert alert-success">{l s='Feature created successfully.' mod='marketplace'}</div>
						{else if $smarty.get.success_attr == 2}
							<div class="alert alert-success">{l s='Feature updated successfully.' mod='marketplace'}</div>
						{else if $smarty.get.success_attr == 3}
							<div class="alert alert-success">{l s='Feature deleted successfully.' mod='marketplace'}</div>
						{/if}
					</div>
				{else if isset($smarty.get.error_attr)}
					<div class="error">
						<div class="alert alert-danger" id="wk-error-msg_1">
							{l s='This Feature is already in use you cannot edit or delete it.' mod='marketplace'}
						</div>
					</div>
				{/if}
				<div class="wk_product_list">
					<p class="wk_text_right">
						<a href="{$link->getModuleLink('marketplace', 'createfeature')|escape:'htmlall':'UTF-8'}">
							<button class="btn btn-primary btn-sm mb-1" type="button">
								<i class="material-icons">&#xE145;</i>
								{l s='Add feature' mod='marketplace'}
							</button>
						</a>
						<a href="{$link->getModuleLink('marketplace', 'addfeaturevalue')|escape:'htmlall':'UTF-8'}">
							<button class="btn btn-primary btn-sm mb-1" type="button">
								<i class="material-icons">&#xE145;</i>
								{l s='Add Value' mod='marketplace'}
							</button>
						</a>
					</p>
					<div class="table-responsive">
						<table class="table table-striped" {if !(empty($feature_set))}id="wk_datatable_list"{/if}>
							<thead>
								<tr>
									<th>{l s='#' mod='marketplace'}</th>
									<th>{l s='ID' mod='marketplace'}</th>
									<th>{l s='Name' mod='marketplace'}</th>
									<th>{l s='Values' mod='marketplace'}</th>
									<th>{l s='Actions' mod='marketplace'}</th>
								</tr>
							</thead>
							{if !(empty($feature_set))}
								{assign var=num value=1}
								{foreach $feature_set as $feature_set_each}
									<tr class="wk-mp-data-list" data-value-url="{$link->getModuleLink('marketplace', 'viewfeaturevalue',['id_feature' => $feature_set_each.id])|escape:'htmlall':'UTF-8'}">
										<td>{$num|escape:'htmlall':'UTF-8'}</td>
										<td>{$feature_set_each.id|escape:'htmlall':'UTF-8'}</td>
										<td>{$feature_set_each.name|escape:'htmlall':'UTF-8'}</td>
										<td>{$feature_set_each.values|escape:'htmlall':'UTF-8'}</td>
										<td>
											<a title="{l s='View Values' mod='marketplace'}" href="{$link->getModuleLink('marketplace', 'viewfeaturevalue',['id_feature' => $feature_set_each.id])|escape:'htmlall':'UTF-8'}">
												<i class="material-icons">&#xE417;</i>
											</a>
											&nbsp;
											<a class="edit_button"  title="{l s='Edit' mod='marketplace'}" edit="{$feature_set_each.editable|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('marketplace', 'createfeature',['id_feature'=>$feature_set_each.editable])|addslashes}">
												<i class="material-icons">&#xE254;</i>
											</a>
											<a class="delete_button" title="{l s='Delete' mod='marketplace'}" edit="{$feature_set_each.editable|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('marketplace', 'productfeature',['id_feature'=>$feature_set_each.editable,'delete_feature'=>1])|addslashes}">
												<i class="material-icons">&#xE872;</i>
											</a>
										</td>
									</tr>
									{$num = $num + 1}
								{/foreach}
							{else}
								<tr>
									<td colspan="5">
										<div id="empty_list">{l s='No Feature Yet.' mod='marketplace'}</div>
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