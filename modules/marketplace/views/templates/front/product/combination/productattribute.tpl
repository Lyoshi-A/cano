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
					{l s='Product Attributes' mod='marketplace'}
				</span>
			</div>
			<div class="wk-mp-right-column">
				{if isset($smarty.get.created)}
					<div class="alert alert-success">{l s='Attribute created successfully.' mod='marketplace'}</div>
				{/if}
				{if isset($smarty.get.updated)}
					<div class="alert alert-success">{l s='Attribute updated successfully.' mod='marketplace'}</div>
				{/if}
				{if isset($smarty.get.deleted)}
					<div class="alert alert-success">{l s='Attribute deleted successfully.' mod='marketplace'}</div>
				{/if}
				<div class="wk_product_list">
					<p class="wk_text_right">
						<a href="{$link->getModuleLink('marketplace', 'createattribute')|escape:'htmlall':'UTF-8'}">
							<button class="btn btn-primary btn-sm mb-1" type="button">
								<i class="material-icons">&#xE145;</i>
								{l s='Add attribute' mod='marketplace'}
							</button>
						</a>
						<a href="{$link->getModuleLink('marketplace', 'createattributevalue')|escape:'htmlall':'UTF-8'}">
							<button class="btn btn-primary btn-sm mb-1" type="button">
								<i class="material-icons">&#xE145;</i>
								{l s='Add value' mod='marketplace'}
							</button>
						</a>
					</p>
					<div class="table-responsive">
						<table class="table table-striped" {if isset($attributeSet) && $attributeSet}id="wk_datatable_list"{/if}>
							<thead>
								<tr>
									<th>{l s='#' mod='marketplace'}</th>
									<th>{l s='ID' mod='marketplace'}</th>
									<th>{l s='Name' mod='marketplace'}</th>
									<th>{l s='Public Name' mod='marketplace'}</th>
									<th>{l s='Type' mod='marketplace'}</th>
									<th>{l s='Values Count' mod='marketplace'}</th>
									<th>{l s='Actions' mod='marketplace'}</th>
								</tr>
							</thead>
							<tbody>
							{if isset($attributeSet) && $attributeSet}
								{assign var=num value=1}
								{foreach $attributeSet as $attributeEach}
									<tr class="wk-mp-data-list" data-value-url="{$link->getModuleLink('marketplace', 'viewattributegroupvalue',['id_group' => $attributeEach.id])|escape:'htmlall':'UTF-8'}">
										<td>{$num|escape:'htmlall':'UTF-8'}</td>
										<td>{$attributeEach.id|escape:'htmlall':'UTF-8'}</td>
										<td>{$attributeEach.name|escape:'htmlall':'UTF-8'}</td>
										<td>{$attributeEach.public_name|escape:'htmlall':'UTF-8'}</td>
										<td>{$attributeEach.group_type|escape:'htmlall':'UTF-8'}</td>
										<td>{$attributeEach.count_value|escape:'htmlall':'UTF-8'}</td>
										<td>
											<a title="{l s='View Values' mod='marketplace'}" href="{$link->getModuleLink('marketplace', 'viewattributegroupvalue',['id_group' => $attributeEach.id])|escape:'htmlall':'UTF-8'}">
												<i class="material-icons">&#xE417;</i>
											</a>
											&nbsp;
											<a class="edit_button" title="{l s='Edit' mod='marketplace'}" edit="{$attributeEach.editable|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('marketplace', 'createattribute',['id_group'=>$attributeEach.editable])|escape:'htmlall':'UTF-8'}">
												<i class="material-icons">&#xE254;</i>
											</a>
											&nbsp;
											<a class="delete_button" title="{l s='Delete' mod='marketplace'}" edit="{$attributeEach.editable|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('marketplace', 'productattribute',['id_group'=>$attributeEach.editable, 'delete_attribute'=>1])|escape:'htmlall':'UTF-8'}">
												<i class="material-icons">&#xE872;</i>
											</a>
										</td>
									</tr>
									{$num = $num + 1}
								{/foreach}
							{else}
								<tr>
									<td colspan="7">{l s='No data found' mod='marketplace'}</td>
								</tr>
							{/if}
							</tbody>
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