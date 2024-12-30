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
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Products' mod='marketplace'}</span>
			</div>
			<div class="wk-mp-right-column">
				{block name='wk-form-validation'}
					{include file='module:marketplace/views/templates/front/_partials/validation.tpl'}
				{/block}
				<div class="wk_product_list">
					<p class="wk_text_right">
						{if Configuration::get('WK_MP_SELLER_EXPORT')}
							<a title="{l s='Export' mod='marketplace'}" href="javascript:;">
								<button class="btn btn-primary btn-sm wk_product_export_button mb-1" type="button">
									<i class="material-icons">file_download</i>
									{l s='Export' mod='marketplace'}
								</button>
							</a>
						{/if}
						{if $add_permission}
							<a title="{l s='Add product' mod='marketplace'}"
								href="{$link->getModuleLink('marketplace', 'addproduct')|escape:'htmlall':'UTF-8'}">
								<button class="btn btn-primary btn-sm mb-1" type="button">
									<i class="material-icons">&#xE145;</i>
									{l s='Add Product' mod='marketplace'}
								</button>
							</a>
							{if Configuration::get('WK_MP_SELLER_SHIPPING')}
								{include file="module:marketplace/views/templates/front/product/_partials/assign_shipping_method.tpl"}
							{else}
								{hook h="displayMpProductListTop"}
							{/if}
						{/if}
					</p>
					{block name='mpproduct_export'}
						{include file="module:marketplace/views/templates/front/product/_partials/mpproductexport.tpl"}
					{/block}
					<form action="{$link->getModuleLink('marketplace', productlist)|escape:'htmlall':'UTF-8'}" method="post"
						id="mp_productlist_form">
						<input type="hidden" name="token" id="wk-static-token" value="{$static_token|escape:'htmlall':'UTF-8'}">
						<table class="table table-striped" id="mp_product_list">
							<thead>
								<tr>
									{if $product_lists|is_array}
										{if $product_lists|@count > 1}
											<th class="no-sort"><input type="checkbox" title="{l s='Select all' mod='marketplace'}"
													id="mp_all_select" /></th>
										{/if}
									{/if}
									<th>{l s='ID' mod='marketplace'}</th>
									<th>{l s='Image' mod='marketplace'}</th>
									<th>{l s='Name' mod='marketplace'}</th>
									<th>
										<center>{l s='Price' mod='marketplace'}</center>
									</th>
									<th>
										<center>{l s='Quantity' mod='marketplace'}</center>
									</th>
									<th>
										<center>{l s='Status' mod='marketplace'}</center>
									</th>
									{if isset($isMultiShopEnabled) && $isMultiShopEnabled && $shareCustomerEnabled}
										<th>
											<center>{l s='Shop' mod='marketplace'}</center>
										</th>
									{/if}
									<th class="no-sort" width="15%">
										<center>{l s='Actions' mod='marketplace'}</center>
									</th>
								</tr>
							</thead>
							<tbody>
								{if $product_lists != 0}
									{foreach $product_lists as $key => $product}
										<tr class="{if $key%2 == 0}even{else}odd{/if}">
											{if $product_lists|is_array}
												{if $product_lists|@count > 1}
													<td><input type="checkbox"
															{if $currentShopId == $product.id_mp_shop_default}name="mp_product_selected[]"
																class="mp_bulk_select" value="{$product.id_mp_product|escape:'htmlall':'UTF-8'}"
															{else}Disabled="Disabled"
															{/if} /></td>
												{/if}
											{/if}
											<td>{$product.id_mp_product|escape:'htmlall':'UTF-8'}</td>
											<td>
												{if isset($product.cover_image)}
													<a class="mp-img-preview" href="{$link->getImageLink($product.link_rewrite, $product.cover_image, 'large_default')|escape:'htmlall':'UTF-8'}">
														<img class="img-thumbnail" width="45" height="45" src="{$link->getImageLink($product.link_rewrite, $product.cover_image, 'home_default')|escape:'htmlall':'UTF-8'}">
													</a>
												{else}
													<img class="img-thumbnail" alt="{l s='No image' mod='marketplace'}" width="45"
														height="45"
														src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}/marketplace/views/img/home-default.jpg">
												{/if}
											</td>
											<td>
												{if $currentShopId == $product.id_mp_shop_default}
													<a
														href="{$link->getModuleLink('marketplace', 'productdetails', ['id_mp_product' => $product.id_mp_product])|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a>
												{else}
													{$product.name|escape:'htmlall':'UTF-8'}
												{/if}
											</td>
											<td data-order="{$product.price_per_context_without_sign|escape:'htmlall':'UTF-8'}">
												<center>{$product.price_per_context_with_sign|escape:'htmlall':'UTF-8'}</center>
											</td>
											<td>
												<center>{$product.quantity|escape:'htmlall':'UTF-8'}</center>
											</td>
											<td>
												<center>
													{if isset($product.admin_approved) && $product.admin_approved}
														{if $product.active}
															{if $products_status == 1 && $edit_permission && $currentShopId == $product.id_mp_shop_default}
																<a
																	href="{$link->getModuleLink('marketplace', 'productlist', ['id_product' => {$product.id_product|escape:'htmlall':'UTF-8'}, 'mp_product_status' => 1])|addslashes}">
																	<img alt="{l s='Enabled' mod='marketplace'}"
																		title="{l s='Enabled' mod='marketplace'}" class="mp_product_status"
																		src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}marketplace/views/img/icon/icon-check.png" />
																</a>
															{else}
																<span class="wk_product_approved">{l s='Approved' mod='marketplace'}</span>
															{/if}
														{else}
															{if $products_status == 1 && $edit_permission && $currentShopId == $product.id_mp_shop_default}
																<a
																	href="{$link->getModuleLink('marketplace', 'productlist', ['id_product' => {$product.id_product|escape:'htmlall':'UTF-8'}, 'mp_product_status' => 1])|addslashes}">
																	<img alt="{l s='Disabled' mod='marketplace'}"
																		title="{l s='Disabled' mod='marketplace'}" class="mp_product_status"
																		src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}marketplace/views/img/icon/icon-close.png" />
																</a>
															{else}
																<span class="wk_product_pending">{l s='Pending' mod='marketplace'}</span>
															{/if}
														{/if}
													{else}
														<span class="wk_product_pending">{l s='Pending' mod='marketplace'}</span>
													{/if}
												</center>
											</td>
											{if isset($isMultiShopEnabled) && $isMultiShopEnabled && $shareCustomerEnabled}
												<td>
													<center>{$product.ps_shop_name|escape:'htmlall':'UTF-8'}</center>
												</td>
											{/if}
											<td>
												<center>
													{if $currentShopId == $product.id_mp_shop_default}
														<a title="{l s='Edit' mod='marketplace'}"
															href="{$link->getModuleLink('marketplace', 'updateproduct', ['id_mp_product' => $product.id_mp_product])|escape:'htmlall':'UTF-8'}">
															<i class="material-icons">&#xE254;</i>
														</a>
														{if $delete_permission}
															<a title="{l s='Delete' mod='marketplace'}"
																href="{$link->getModuleLink('marketplace', 'updateproduct', ['id_mp_product' => $product.id_mp_product, 'deleteproduct' => 1])|escape:'htmlall':'UTF-8'}"
																class="delete_mp_product">
																<i class="material-icons">&#xE872;</i>
															</a>
														{/if}
														<a class="edit_seq open_image_form" alt="1"
															product-id="{$product['id_mp_product']|escape:'htmlall':'UTF-8'}" data-toggle="modal"
															data-target="#content{$product['id_mp_product']|escape:'htmlall':'UTF-8'}" href="javascript:void(0)">
															<i class="material-icons mp-list-img-link"
																title="{l s='View Image' mod='marketplace'}"
																id="edit_seq{$product['id_mp_product']|escape:'htmlall':'UTF-8'}">&#xE3F4;</i>
														</a>
														{if Configuration::get('WK_MP_PRODUCT_ALLOW_DUPLICATE')}
															<a title="{l s='Duplicate' mod='marketplace'}"
																href="{$link->getModuleLink('marketplace', 'updateproduct', ['id_mp_product' => $product.id_mp_product, 'duplicateproduct' => 1])|escape:'htmlall':'UTF-8'}"
																class="duplicate_mp_product">
																<i class="material-icons">content_copy</i>
															</a>
														{/if}
														<a title="{l s='Preview' mod='marketplace'}"
															href="{url entity='product' id=$product.id_ps_product}">
															<i class="material-icons">remove_red_eye</i>
														</a>
														<input type="hidden" id="urlimageedit" value="{$imageediturl|escape:'htmlall':'UTF-8'}" />
														{hook h="displayMpProductListAction" id_product=$product.id_mp_product}
													{else}
														-
													{/if}
												</center>
											</td>
										</tr>

										<div class="modal fade" id="content{$product['id_mp_product']|escape:'htmlall':'UTF-8'}" tabindex="-1" role="dialog"
											aria-labelledby="myModalLabel">
										</div>
									{/foreach}
								{/if}
							</tbody>
						</table>
					</div>
					{if $product_lists|is_array}
						{if $product_lists|@count > 1}
							<div class="btn-group">
								<button class="btn btn-default btn-sm dropdown-toggle wk_language_toggle" type="button"
									data-toggle="dropdown" aria-expanded="false">
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
					{/if}
				</form>
			</div>
		</div>
		<div class="left full">
			{hook h="displayMpProductListFooter"}
		</div>

		{block name='mp_image_preview'}
			{include file='module:marketplace/views/templates/front/product/_partials/mp-image-preview.tpl'}
		{/block}
	</div>
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}