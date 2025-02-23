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

{if isset($wk_mp_product_link)}
<div class="wk_edit_product_btn">
	<a title="{l s='Edit Product' mod='marketplace'}" class="wk_seller_edit" href="{$wk_mp_product_link|escape:'htmlall':'UTF-8'}">
		<i class="material-icons">&#xE254;</i>
		<span>{l s='Edit Product' mod='marketplace'}</span>
	</a>
</div>
{/if}

{if isset($showDetail)}
	{if isset($mp_seller_info)}
		<div class="clearfix wk_soldby_link">
			{* Display seller rating *}
{*			{if isset($WK_MP_SELLER_DETAILS_ACCESS_9)}*}
				<div class="wk-product-page-seller-rating">
{*					<div class="wk-sold-by-box">*}
{*						<img class="wk-shop-default-icon" src="{$shop_logo_path|escape:'htmlall':'UTF-8'}">*}
{*						<a id="wk-profileconnect" title="{l s='Visit Shop' mod='marketplace'}" target="_blank" href="{$shopstore_link|escape:'htmlall':'UTF-8'}">*}
{*							<span>{$mp_seller_info.shop_name|escape:'htmlall':'UTF-8'}</span>*}
{*						</a>*}
{*					</div>*}
		           {if isset($supplier)&&isset($supplier.name)}
					<div class="wk-sold-by-box">
						<div>
							<img src="{$supplier.image|escape:'htmlall':'UTF-8'}" class="wk-shop-default-icon" >
							<a href="{$link->getModuleLink('marketplace', 'mpsupplierproductslist', ['id' => $supplier.id_wk_mp_supplier])|escape:'htmlall':'UTF-8'}">{$supplier.name|escape:'htmlall':'UTF-8'}</a>
						</div>
						<div>
							<a class="btn btn-primary btn-sm" href="{$link->getModuleLink('marketplace', 'mpsupplierproductslist', ['id' => $supplier.id_wk_mp_supplier])|escape:'htmlall':'UTF-8'}" target="_blank">
								Go to artist’s profile
							</a>
						</div>
					</div>
				   {else}
					 <pre>
						 $mp_seller_info - {$mp_seller_info|@var_dump}
						 $supplier - {$supplier|@var_dump}
						 $suppliers - {$suppliers|@var_dump}
						 $seller_product - {$seller_product|@var_dump}
					 </pre>
				   {/if}
{*					{if Configuration::get('WK_MP_REVIEW_SETTINGS') && isset($totalReview)}*}
{*						{block name='mp-seller-rating-summary'}*}
{*							{include file='module:marketplace/views/templates/front/seller/_partials/seller-rating-summary.tpl'}*}
{*						{/block}*}
{*					{/if}*}
					<div class="products row">
						{block name='mp_product_slider'}
							{include file='module:marketplace/views/templates/front/_partials/mp-product-slider.tpl'}
						{/block}
					</div>
				</div>
{*			{/if}*}
		</div>
	{/if}
{/if}
{hook h="displayMpProductSoldByBottom"}

{* Load rating code again on QUICK VIEW or CART AJAX CALL because on changing of product qty, seller rating was going to hidden *}
{if isset($sellerRating) && $sellerRating && isset($call_ajax) && ($call_ajax == 'quickview' || $call_ajax == 'refresh')}
	{if $call_ajax == 'quickview'}
		{* We have to assign this js from here because hookActionFrontControllerSetMedia is not working on Quick View *}
		<script type="text/javascript" src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}marketplace/views/js/libs/jquery.raty.min.js"></script>
	{/if}
	<script type="text/javascript">
		$('#seller_rating').raty({
			path: "{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}marketplace/views/img/",
			score: "{$sellerRating|escape:'htmlall':'UTF-8'}",
			readOnly: true,
		});
	</script>
{/if}