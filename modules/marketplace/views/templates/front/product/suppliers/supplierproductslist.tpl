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
			<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{$supplierInfo['name']|escape:'htmlall':'UTF-8'}{l s=' - Supplier Products List' mod='marketplace'}</span>
		</div>
		<div class="wk-mp-right-column">
			<p class="wk_text_right wk_product_list">
				<a href="{$link->getModuleLink('marketplace', 'mpsupplierlist')|escape:'htmlall':'UTF-8'}" class="pull-right">
					<button class="btn btn-primary btn-sm" type="button">
						<i class="material-icons">&#xE15E;</i>
						{l s='Suppliers List' mod='marketplace'}
					</button>
				</a>
			</p>

			<div class="table-responsive">
				<table class="table table-hover table-bordered">
					<thead>
						<tr>
							<th>{l s='ID' mod='marketplace'}</th>
							<th>{l s='Product name' mod='marketplace'}</th>
						</tr>
					</thead>
					<tbody>
						{if isset($productList)}
							{foreach from=$productList key=id item=product}
								<tr>
									<td>{$id +1|escape:'htmlall':'UTF-8'}</td>
									<td><a class="btn btn-link btn-sm" href="{$link->getModuleLink('marketplace', 'updateproduct', ['id_mp_product' => $product.id_mp_product])|escape:'htmlall':'UTF-8'}">{$product.product_name|escape:'htmlall':'UTF-8'}</a></td>
								</tr>
							{/foreach}
						{else}
							<tr>
								<td colspan="2">{l s='No product(s) assigned to this supplier' mod='marketplace'}</td>
							</tr>
						{/if}
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}