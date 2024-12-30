{*
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
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
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
			<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Carriers' mod='marketplace'}</span>
		</div>
		<div class="wk-mp-right-column" style="border:none">
			{if isset($smarty.get.addmpshipping_success)}
				{if $smarty.get.addmpshipping_success == 1}
					<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						{l s='Carrier added successfully.' mod='marketplace'}
					</div>
				{/if}
			{/if}
			{if isset($smarty.get.updatempshipping_success)}
				{if $smarty.get.updatempshipping_success == 1}
					<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						{l s='Updated successfully.' mod='marketplace'}
					</div>
				{/if}
			{/if}
			{if isset($smarty.get.delete_success)}
				{if $smarty.get.delete_success == 1}
					<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						{l s='Deleted successfully.' mod='marketplace'}
					</div>
				{/if}
			{/if}
			{if isset($smarty.get.no_shipping)}
				{if $smarty.get.no_shipping == 1}
					<div class="alert alert-danger">
						<button data-dismiss="alert" class="close" type="button">×</button>
						{l s='Select atleast one carrier' mod='marketplace'}
					</div>
				{/if}
			{/if}
			<div class="alert alert-success" id="deletecarrierajax" style="display:none;">
				<button data-dismiss="alert" class="close" type="button">×</button>
				{l s='Deleted successfully.' mod='marketplace'}
			</div>
			<div class="shipping_list_container wk_product_list left">
				<div class="box-account box-recent">
					<div class="box-head">
						<div class="box-head-right">
							<a href="{$link->getModuleLink('marketplace','addmpshipping')|escape:'htmlall':'UTF-8'}">
								<button class="btn btn-primary btn-sm mb-1" id="add_new_shipping">
									<span><i class="material-icons">&#xE145;</i> {l s='Add Carrier' mod='marketplace'}</span>
								</button>
							</a>
							<button class="btn btn-primary btn-sm mb-1" id="add_default_shipping">
								<span><i class="material-icons">&#xE83A;</i> {l s='Set Default carrier' mod='marketplace'}</span>
							</button>
						</div>
					</div>
					<div class="box-content" id="wk_shipping_list">
						<div id="default_shipping_div" style="display:none;">
							<div class="panel panel-default">
								<h4 class="panel-heading" style="margin: 0;">{l s='Default carrier' mod='marketplace'}</h4>
								<div class="panel-body">
									{if isset($mp_shipping_active)}
										<form method="post" class="form-horizontal default_shipping_form">
											<div class="form-group">
												<label for="default_shipping" class="col-lg-2 col-md-2 col-sm-2 col-xs-12 text-right">{l s='Select carrier' mod='marketplace'}</label>
												<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
													<div style="max-height:155px;overflow:auto;">
													<input type="hidden" name="token" value="{Tools::getToken(false)|escape:'htmlall':'UTF-8'}">
													{foreach $mp_shipping_active as $mp_sp_det}
														<div>
															<div class="shipping_checkbox">
																<input type="checkbox" name="default_shipping[]" id="default_shipping_{$mp_sp_det.id|escape:'htmlall':'UTF-8'}" value="{$mp_sp_det.id|escape:'htmlall':'UTF-8'}" {if $mp_sp_det.is_default_shipping == 1}checked="checked" {/if}>
															</div>
															<div class="floatleft" style="padding:4px 10px;">
																<label for="default_shipping_{$mp_sp_det.id|escape:'htmlall':'UTF-8'}" style="font-weight: normal;">
																	{$mp_sp_det.name|escape:'htmlall':'UTF-8'}
																</label>
															</div>
															<div style="clear:both;"></div>
														</div>
													{/foreach}
													</div>
												</div>
											</div>
											<div class="form-group" style="text-align:center;">
												<button type="button" id="submit_default_shipping" class="btn btn-primary btn-sm"><span>{l s='Update' mod='marketplace'}</span></button>
												<button type="button" id="cancel_default_shipping" class="btn btn-danger btn-sm"><span>{l s='Cancel' mod='marketplace'}</span></button>
											</div>
										</form>
									{else}
										<div class="alert alert-info">{l s='You do not have any active carrier(s).' mod='marketplace'}</div>
									{/if}
								</div>
							</div>
						</div>
						<table id="default_shipping_show" cellpadding="7" class="data-table" style="margin-bottom:15px;">
							<tr class="first last">
								<th><span class="mand_field">*</span> {l s='Default carrier' mod='marketplace'}</th>
								<td>
									{if isset($default_shipping_name)}
										{$default_shipping_name|escape:'htmlall':'UTF-8'}
									{else}
										{l s='There is no default carrier' mod='marketplace'}
									{/if}
								</td>
							</tr>
						</table>
						<table class="table table-striped" {if isset($mp_shipping_detail)}id="mp_shipping_list"{/if}>
							<thead>
								<tr>
									{if isset($mp_shipping_detail) && $mp_shipping_detail|is_array && $mp_shipping_detail|@count > 1}
										<th class="no-sort"><input type="checkbox" title="{l s='Select all' mod='marketplace'}" id="mp_all_carriers" /></th>
									{/if}
									<th>{l s='ID' mod='marketplace'}</th>
									<th>{l s='Carrier Name' mod='marketplace'}</th>
									<th class="no-sort">{l s='Logo' mod='marketplace'}</th>
									<th>{l s='Shipping Method' mod='marketplace'}</th>
									<th>{l s='Status' mod='marketplace'}</th>
									{if isset($isMultiShopEnabled) && $isMultiShopEnabled && $shareCustomerEnabled}
									<th>{l s='Shop' mod='marketplace'}</th>
									{/if}
									<th class="no-sort">{l s='Actions' mod='marketplace'}</th>
								</tr>
							</thead>
							<tbody>
								{if isset($mp_shipping_detail)}
									{foreach $mp_shipping_detail as $num => $mp_sp_det}
										<tr id="shippingid_{$mp_sp_det.id|escape:'htmlall':'UTF-8'}">
											{if $mp_shipping_detail|is_array && $mp_shipping_detail|@count > 1}
												<td>
													<input type="checkbox" {if $currentShopId == $mp_sp_det.id_shop}name="mp_shipping_selected[]"
													class="mp_bulk_select" value="{$mp_sp_det.id|escape:'htmlall':'UTF-8'}"{else}Disabled="Disabled"{/if}>
												</td>
											{/if}
											<td>{$mp_sp_det.id|escape:'htmlall':'UTF-8'}</td>
											<td>{$mp_sp_det.name|escape:'htmlall':'UTF-8'}</td>
											<td>
												{if $mp_sp_det.image_exist == 1}
													<img class="img-thumbnail" src="{$smarty.const._THEME_SHIP_DIR_|escape:'htmlall':'UTF-8'}{$mp_sp_det.id_carrier|escape:'htmlall':'UTF-8'}.jpg" width="30" alt="{$mp_sp_det.name|escape:'htmlall':'UTF-8'}">
												{else}
													<img class="img-thumbnail" alt="{l s='No image' mod='marketplace'}" width="30" src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}/marketplace/views/img/home-default.jpg">
												{/if}
											</td>
											<td>
												{if $mp_sp_det.is_free == 1}
													{l s='Free Shipping' mod='marketplace'}
												{else}
													{if $mp_sp_det.shipping_method == 2}
														{l s='Shipping charge on price' mod='marketplace'}
													{elseif $mp_sp_det.shipping_method == 1}
														{l s='Shipping charge on weight' mod='marketplace'}
													{/if}
												{/if}
											</td>
											<td>
												{if $mp_sp_det.active == 0}
													<span class="wk_product_pending">{l s='Pending' mod='marketplace'}</span>
												{else}
													<span class="wk_product_approved">{l s='Approved' mod='marketplace'}</span>
												{/if}
											</td>
											{if isset($isMultiShopEnabled) && $isMultiShopEnabled && $shareCustomerEnabled}
												<td>{$mp_sp_det.ps_shop_name|escape:'htmlall':'UTF-8'}</td>
											{/if}
											<td>
											{if $currentShopId == $mp_sp_det.id_shop}
												{if !($mp_sp_det.is_free)}
													<a title="{l s='View Impact Price' mod='marketplace'}" href="{$link->getModuleLink('marketplace','addmpshipping',['mpshipping_id'=>{$mp_sp_det.id|escape:'htmlall':'UTF-8'}, 'addmpshipping_step4'=>1, 'updateimpact' => 1])|escape:'htmlall':'UTF-8'}" id="impact_edit">
														<i class="material-icons">&#xE417;</i>
													</a>
													&nbsp;
												{/if}
												<a title="{l s='Basic Edit' mod='marketplace'}" href="{$link->getModuleLink('marketplace','addmpshipping',['mpshipping_id'=>{$mp_sp_det.id|escape:'htmlall':'UTF-8'} ])|escape:'htmlall':'UTF-8'}" id="shipping_basicedit">
													<i class="material-icons">&#xE254;</i>
												</a>
												&nbsp;
												{if $mp_sp_det.shipping_on_product == 1}
													<a title="{l s='Delete' mod='marketplace'}" href="#delete_shipping_form" data-prod="{$mp_sp_det.shipping_on_product|escape:'htmlall':'UTF-8'}" data-shipping-id="{$mp_sp_det.id|escape:'htmlall':'UTF-8'}" class="delete_shipping">
														<i class="material-icons">&#xE872;</i>
													</a>
												{else}
													<a title="{l s='Delete' mod='marketplace'}" href="{$link->getModuleLink('marketplace','mpshippinglist',['mpshipping_id'=>{$mp_sp_det.id|escape:'htmlall':'UTF-8'},'delete_shipping'=>1])|escape:'htmlall':'UTF-8'}" data-prod="{$mp_sp_det.shipping_on_product|escape:'htmlall':'UTF-8'}" class="delete_shipping">
														<i class="material-icons">&#xE872;</i>
													</a>
												{/if}
											{/if}
											</td>
										</tr>
									{/foreach}
								{else}
									<tr>
										<td colspan="7"><center>{l s='No Carrier Yet' mod='marketplace'}</center></td>
									</tr>
								{/if}
							</tbody>
						</table>
						{if isset($mp_shipping_detail) && $mp_shipping_detail|is_array && $mp_shipping_detail|@count > 1}
							<div class="btn-group dropup">
								<button class="btn btn-default btn-sm dropdown-toggle wk_language_toggle" type="button"
									data-toggle="dropdown" aria-expanded="false">
									{l s='Bulk actions' mod='marketplace'} <span class="caret"></span>
								</button>
								<ul class="dropdown-menu wk_bulk_actions" role="menu">
									<li>
										<a href="" class="mp_bulk_carrier_delete_btn">
											<i class="material-icons">&#xE872;</i> {l s='Delete selected' mod='marketplace'}
										</a>
									</li>
								</ul>
							</div>
						{/if}
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>

<div id="delete_shipping_form" style="display:none;">
	<label style="text-align: left;"><strong>{l s='Note: This carrier is assigned on product(s). So before deleting this carrier, you have to choose a new carrier for that products.' mod='marketplace'}</strong></label>
	<div class="panel-body">
		<form method="post" action="{$link->getModuleLink('marketplace','mpshippinglist')|escape:'htmlall':'UTF-8'}" class="form-horizontal">
		{if isset($mp_shipping_active)}
			<input type="hidden" name="delete_shipping_id" id="delete_shipping_id" value="">
			<div id="shippingactive" class="form-group" style="display: flex;align-items: center;">
				<label class="col-lg-5 col-md-5 col-sm-3 col-xs-12 text-right">{l s='Select carrier' mod='marketplace'}</label>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<select name="extra_shipping" id="extra_shipping" class="form-control">
						{foreach $mp_shipping_active as $mp_sp_det}
							<option value="{$mp_sp_det.id|escape:'htmlall':'UTF-8'}">{$mp_sp_det.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div id="noshippingactive" style="display:none; padding-bottom: 10px;">
				<div class="alert alert-info">{l s='There is no other active carrier.' mod='marketplace'}</div>
				<div class="help-block">{l s='If no carrier selected, Admin first default carrier will apply on product(s)' mod='marketplace'}</div>
			</div>
		{else}
			<div class="alert alert-info">{l s='There is no other active carrier.' mod='marketplace'}</div>
			<div class="help-block">{l s='If no carrier selected, Admin first default carrier will apply on product(s)' mod='marketplace'}</div>
		{/if}
			<div class="form-group" style="text-align:center;">
				<button type="submit" name="submit_extra_shipping" class="btn btn-primary btn-sm"><span>{l s='Submit' mod='marketplace'}</span></button>
			</div>
		</form>
	</div>
</div>
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}