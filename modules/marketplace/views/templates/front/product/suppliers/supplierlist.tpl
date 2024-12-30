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
			<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Suppliers' mod='marketplace'}</span>
		</div>
		<div class="wk-mp-right-column">
			<div id="alert_div">
				{if isset($msg_code)}
					{if $msg_code == 1}
						<div class="alert alert-success">{l s='Supplier added successfully.' mod='marketplace'}</div>
					{elseif $msg_code == 2}
						<div class="alert alert-success">{l s='Supplier updated successfully.' mod='marketplace'}</div>
					{elseif $msg_code == 3}
						<div class="alert alert-danger">{l s='There is some technical error in updating supplier.' mod='marketplace'}</div>
					{elseif $msg_code == 4}
						<div class="alert alert-success">{l s='Supplier assigned successfully.' mod='marketplace'}</div>
					{/if}
				{/if}
			</div>
			<p class="wk_text_right wk_product_list">
				<a href="{$link->getModuleLink('marketplace', 'mpaddsupplier')|escape:'htmlall':'UTF-8'}" class="pull-right">
					<button class="btn btn-primary btn-sm" type="button">
						<i class="material-icons">&#xE145;</i>
						{l s='Add supplier' mod='marketplace'}
					</button>
				</a>
			</p>
			<div class="clearfix"></div>
			<div class="mt-2">
				<div class="">
					<table class="table table-striped" {if isset($mpSupplierInfo) && $mpSupplierInfo}id="mpSupplierList"{/if}>
						<thead>
							<tr>
								{if isset($mpSupplierInfo) && $mpSupplierInfo|is_array && $mpSupplierInfo|@count > 1}
									<th class="no-sort"><input type="checkbox" title="{l s='Select all' mod='marketplace'}" id="mp_all_select"/></th>
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
							{if isset($mpSupplierInfo)}
								{foreach from=$mpSupplierInfo key=k item=supplier}
									<tr id="mp_spllier_{$supplier.id_wk_mp_supplier|escape:'htmlall':'UTF-8'}">
										{if $mpSupplierInfo|is_array && $mpSupplierInfo|@count > 1}
											<td>
												<input type="checkbox" {if $currentShopId == $supplier.id_shop}name="mp_product_selected[]" class="mp_bulk_select" value="{$supplier.id_wk_mp_supplier|escape:'htmlall':'UTF-8'}"{else}Disabled="Disabled"{/if}/>
											</td>
										{/if}
										<td>{$supplier.id_wk_mp_supplier|escape:'htmlall':'UTF-8'}</td>
										<td>
											<img src="{$supplier.image|escape:'htmlall':'UTF-8'}" class="img-thumbnail" width="30">
										</td>
										<td><a href="{$link->getModuleLink('marketplace', 'mpsupplierproductslist', ['id' => $supplier.id_wk_mp_supplier])|escape:'htmlall':'UTF-8'}">{$supplier.name|escape:'htmlall':'UTF-8'}</a></td>
										<td>{$supplier.no_of_products|escape:'htmlall':'UTF-8'}</td>
										<td>
											{if ($supplier.active)}
												<span class="wk_product_approved">{l s='Approved' mod='marketplace'}</span>
											{else}
												<span class="wk_product_pending">{l s='Pending' mod='marketplace'}</span>
											{/if}
										</td>
										<td>
											{if $currentShopId == $supplier.id_shop}
											<a title="{l s='Edit' mod='marketplace'}" href="{$link->getModuleLink('marketplace', 'mpupdatesupplier', ['id' => $supplier.id_wk_mp_supplier])|escape:'htmlall':'UTF-8'}">
												<i class="material-icons">&#xE254;</i>
											</a>
											&nbsp;
											<a ps_supplier_id="{$supplier.id_ps_supplier|escape:'htmlall':'UTF-8'}" mp_supplier_id="{$supplier.id_wk_mp_supplier|escape:'htmlall':'UTF-8'}" style="color:#2fb5d2 !important;cursor: pointer;" title="{l s='Delete' mod='marketplace'}" class="mp_supplier_delete" style="cursor:pointer;">
												<i class="material-icons">&#xE872;</i>
											</a>
											{else}
												-
											{/if}
										</td>
									</tr>
								{/foreach}
							{else}
								<tr>
									<td colspan="7"><center>{l s='No Supplier Yet' mod='marketplace'}</center></td>
								</tr>
							{/if}
						</tbody>
					</table>
				</div>
				{if isset($mpSupplierInfo) && $mpSupplierInfo|is_array && $mpSupplierInfo|@count > 1}
					<div class="btn-group">
						<button class="btn btn-default btn-sm dropdown-toggle wk_language_toggle" type="button" data-toggle="dropdown" aria-expanded="false">
						{l s='Bulk actions' mod='marketplace'} <span class="caret"></span>
						</button>
						<ul class="dropdown-menu wk_bulk_actions" role="menu">
							<li>
								<a href="" class="mp_bulk_delete_btn">
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