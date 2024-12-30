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
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Brands' mod='marketplace'}</span>
			</div>
			<div class="wk-mp-right-column">
				{block name='wk-form-validation'}
					{include file='module:marketplace/views/templates/front/_partials/validation.tpl'}
				{/block}
				<div class="alert alert-success" id="deletemanufajax" style="display:none;">
					<button data-dismiss="alert" class="close" type="button">×</button>
					{l s='Deleted successfully.' mod='marketplace'}
				</div>
				<p class="wk_btn_add_product wk_product_list">
					<a title="{l s='Add Brand' mod='marketplace'}"
						href="{$link->getModuleLink('marketplace', 'mpcreatemanufacturers')|escape:'htmlall':'UTF-8'}">
						<button type="button" class="btn btn-primary btn-sm">
							<i class="material-icons">&#xE145;</i>
							{l s='Add brand' mod='marketplace'}
						</button>
					</a>
				</p>
				<div class="">
					<table class="table table-striped" {if isset($manufinfo) && $manufinfo}id="mp_manufacturer_list" {/if}>
						<thead>
							<tr>
								{if isset($manufinfo) && $manufinfo|is_array && $manufinfo|@count > 1}
									<th class="no-sort"><input type="checkbox" title="{l s='Select all' mod='marketplace'}" id="mp_all_manf"/></th>
								{/if}
								<th>{l s='ID' mod='marketplace'}</th>
								<th>{l s='Logo' mod='marketplace'}</th>
								<th>{l s='Name' mod='marketplace'}</th>
								<th>{l s='Products' mod='marketplace'}</th>
								<th>{l s='Status' mod='marketplace'}</th>
								<th class="no-sort">{l s='Actions' mod='marketplace'}</th>
							</tr>
						</thead>
						<tbody>
							{if isset($manufinfo) && $manufinfo}
								{assign var="i" value="1"}
								{foreach $manufinfo as $data}
									<tr id="manufid_{$data.id|escape:'htmlall':'UTF-8'}">
										{if $manufinfo|is_array && $manufinfo|@count > 1}
											<td>
												<input type="checkbox" {if $currentShopId == $data.id_shop}name="mp_manufacturer_selected[]"
												class="mp_bulk_select" value="{$data.id|escape:'htmlall':'UTF-8'}"{else}Disabled="Disabled"{/if}>
											</td>
										{/if}
										<td>{$data.id|escape:'htmlall':'UTF-8'}</td>
										<td>
											<img src="{$data.image|escape:'htmlall':'UTF-8'}" class="img-thumbnail" width="30">
										</td>
										<td>
											<a title="{l s='View' mod='marketplace'}"
												href="{$link->getModuleLink('marketplace', 'manufacturerproductlist',['mp_manuf_id' => $data.id])|escape:'htmlall':'UTF-8'}">
												<u>{$data.name|escape:'htmlall':'UTF-8'}</u>
											</a>
										</td>
										<td>{$data.product_num|escape:'htmlall':'UTF-8'}</td>
										<td>
											{if ($data.active)}
												<span class="wk_product_approved">{l s='Approved' mod='marketplace'}</span>
											{else}
												<span class="wk_product_pending">{l s='Pending' mod='marketplace'}</span>
											{/if}
										</td>
										<td>
											{if $currentShopId == $data.id_shop}
											<a title="{l s='Edit' mod='marketplace'}"
												href="{$link->getModuleLink('marketplace', 'mpcreatemanufacturers', ['id' => $data.id])|escape:'htmlall':'UTF-8'}">
												<i class="material-icons">&#xE254;</i>
											</a>
											<a delmanufid="{$data.id|escape:'htmlall':'UTF-8'}" title="{l s='Delete' mod='marketplace'}"
												class="delete_manuf_data" style="color:#2fb5d2;">
												<i class="material-icons">&#xE872;</i>
											</a>
											{else}
												-
											{/if}
										</td>
									</tr>
									{assign var="i" value=$i+1}
								{/foreach}
							{else}
								<tr>
									<td colspan="7"><center>{l s='No Brand Yet' mod='marketplace'}</center></td>
								</tr>
							{/if}
						</tbody>
					</table>
					{if isset($manufinfo) && $manufinfo|is_array && $manufinfo|@count > 1}
						<div class="btn-group dropup">
							<button class="btn btn-default btn-sm dropdown-toggle wk_language_toggle" type="button"
								data-toggle="dropdown" aria-expanded="false">
								{l s='Bulk actions' mod='marketplace'} <span class="caret"></span>
							</button>
							<ul class="dropdown-menu wk_bulk_actions" role="menu">
								<li>
									<a href="" class="mp_bulk_manufacturer_delete_btn">
										<i class="material-icons">&#xE872;</i> {l s='Delete selected' mod='marketplace'}
									</a>
								</li>
							</ul>
						</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}