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

<div class="wk_profile_container">
	{block name='shopcollection_pagination_sort'}
		{include file='module:marketplace/views/templates/front/shop/_partials/shopcollection-categoty-sort.tpl'}
	{/block}

	{hook h="displayMpCollectionLeftColumn"}

	<div class="wk-mp-content">
		<div class="wk_product_collection">
			{if isset($mp_shop_collection) && !empty($mp_shop_collection)}
				{block name='shopcollection_pagination_sort'}
					{include file='module:marketplace/views/templates/front/shop/_partials/shopcollection-top.tpl'}
				{/block}
				<div class="wk-product-collection">
					{foreach $mp_shop_collection as $key => $product}
						{if $product.active}
							<a href="{$link->getProductLink($product.product)|escape:'htmlall':'UTF-8'}" class="product_img_link" title="{$product.product_name|escape:'htmlall':'UTF-8'}">
								<div class="wk_collection_data {if ($key+1)%3 == 0}wk-collection-last-item{/if}">
									{if Configuration::get('WK_MP_PRODUCT_ON_SALE') && isset($product.on_sale) && $product.on_sale}
										<div class="wk_product_on_sale">{l s='On Sale!' mod='marketplace'}</div>
									{/if}
									<div class="wk_img_block">
										{if isset($product.image)}
											<img class="img-responsive" src="{$link->getImageLink($product.link_rewrite|escape:'htmlall':'UTF-8', $product.image|escape:'htmlall':'UTF-8', 'home_default')}" alt="{$product.product_name|escape:'htmlall':'UTF-8'}">
										{else}
											<img class="img-responsive" src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}/marketplace/views/img/home-default.jpg" alt="{$product.product_name|escape:'htmlall':'UTF-8'}">
										{/if}
									</div>
									<div class="wk_collecion_details">
										<div class="mp-product-name">
											{$product.product_name|escape:'htmlall':'UTF-8'|truncate:45:'...'}
										</div>
										<div class="mp-product-price">
											{if $product.show_price && $showPriceByCustomerGroup}
												{$product.price|escape:'htmlall':'UTF-8'}
												{if $product.price != $product.retail_price}
													<span class="wk_retail_price">{$product.retail_price|escape:'htmlall':'UTF-8'}</span>
												{/if}
											{/if}
										</div>
										<div>
											{* if catalog mode is disabled by config (by default) *}
											{if !$PS_CATALOG_MODE && $product.available_for_order && $showPriceByCustomerGroup}
												{* If product qty is not avalaible or allow orders is ON *}
												{if ($product.qty_available > 0) || ($product.out_of_stock == 1) || !Configuration::get('PS_STOCK_MANAGEMENT')}
													{block name='shopcollection_add_to_cart'}
														{if $product.customizable}
															<a href="{$link->getProductLink($product.product)|escape:'htmlall':'UTF-8'}" class="btn btn-primary">
																<i class="material-icons shopping-cart">&#xE8CC;</i>
																{l s='Customize' mod='marketplace'}
															</a>
														{else}
															{include file='module:marketplace/views/templates/front/_partials/product-add-to-cart.tpl' product=$product}
														{/if}
													{/block}
												{else}
													<button class="btn btn-primary disabled">
														<i class="material-icons shopping-cart">&#xE8CC;</i>
														{if $product.customizable}{l s='Customize' mod='marketplace'}{else}{l s='Add to Cart' mod='marketplace'}{/if}
													</button>
												{/if}
											{else}
												<button class="btn btn-primary disabled">
													<i class="material-icons shopping-cart">&#xE8CC;</i>
												{if $product.customizable}{l s='Customize' mod='marketplace'}{else}{l s='Add to Cart' mod='marketplace'}{/if}
												</button>
											{/if}
										</div>
									</div>
									{hook h="displayMpShopProductListReviews" product=$product}
								</div>
							</a>
						{/if}
					{/foreach}
					<div class="clearfix"></div>
				</div>
				{block name='shopcollection_pagination_sort'}
					{include file='module:marketplace/views/templates/front/shop/_partials/shopcollection-pagination.tpl'}
				{/block}
			{else}
				<div class="alert alert-info">{l s='No item found' mod='marketplace'}</div>
			{/if}
		</div>

		{hook h="displayMpCollectionFooter"}

	</div>
	<div class="clearfix"></div>
</div>