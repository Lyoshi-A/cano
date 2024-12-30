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

<div class="wk_menu_item">
	{if $is_seller == 1}
		<div class="list_content">
			<ul>
				<li class="menutitle"><span>{l s='Marketplace' mod='marketplace'}</span></li>
				<li {if $logic == 1}class="menu_active"{/if}>
					<span>
						<a href="{if isset($dashboard_link)}{$dashboard_link|escape:'htmlall':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'dashboard')|addslashes}{/if}">
							<i class="material-icons">&#xE871;</i>
							{l s='Dashboard' mod='marketplace'}
						</a>
					</span>
				</li>
				<li {if $logic == 3}class="menu_active"{/if}>
					<span>
						<a href="{if isset($product_list_link)}{$product_list_link|escape:'htmlall':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'productlist')|addslashes}{/if}">
							<i class="material-icons">&#xE149;</i>
							{l s='Products' mod='marketplace'}
							<span class="wkbadge-primary" style="float:right;">{$totalSellerProducts|escape:'htmlall':'UTF-8'}</span>
							<div class="clearfix"></div>
						</a>
					</span>
				</li>
				<li {if $logic == 4}class="menu_active"{/if}>
					<span>
						<a href="{if isset($my_order_link)}{$my_order_link|escape:'htmlall':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'mporder')|addslashes}{/if}">
							<i class="material-icons">&#xE8F6;</i>
							{l s='Orders' mod='marketplace'}
						</a>
					</span>
				</li>
				<li {if $logic == 5}class="menu_active"{/if}>
					<span>
						<a href="{if isset($my_transaction_link)}{$my_transaction_link|escape:'htmlall':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'mptransaction')|addslashes}{/if}">
							<i class="material-icons">swap_horiz</i>
							{l s='Transaction' mod='marketplace'}
						</a>
					</span>
				</li>
				{if Configuration::get('WK_MP_PRESTA_ATTRIBUTE_ACCESS')}
					<li {if $logic=='mp_prod_attribute'}class="menu_active"{/if}>
						<span>
							<a href="{$link->getModuleLink('marketplace', 'productattribute')|escape:'htmlall':'UTF-8'}" title="{l s='Product Attributes' mod='marketplace'}">
								<i class="material-icons">&#xE839;</i>
								{l s='Product Attributes' mod='marketplace'}
							</a>
						</span>
					</li>
				{/if}
				{if Configuration::get('WK_MP_PRESTA_FEATURE_ACCESS')}
					<li {if $logic=='mp_prod_features'}class="menu_active"{/if}>
						<span>
							<a href="{$link->getModuleLink('marketplace', 'productfeature')|escape:'htmlall':'UTF-8'}" title="{l s='Product Features' mod='marketplace'}">
								<i class="material-icons">&#xE8D0;</i>
								{l s='Product Features' mod='marketplace'}
							</a>
						</span>
					</li>
				{/if}
				{if Configuration::get('WK_MP_PRODUCT_MANUFACTURER')}
					<li {if $logic=='mpmanufacturerlist'}class="menu_active"{/if}>
						<span>
							<a href="{$link->getModuleLink('marketplace', 'mpmanufacturerlist')|escape:'htmlall':'UTF-8'}" title="{l s='Brand' mod='marketplace'}" >
								<i class="material-icons">&#xE7EE;</i>
								{l s='Brands' mod='marketplace'}
							</a>
						</span>
						<div class="loading_overlay" style="display:none;"></div>
					</li>
				{/if}
				{if Configuration::get('WK_MP_PRODUCT_SUPPLIER')}
					<li {if $logic=='mpsupplierlist'}class="menu_active"{/if}>
						<span>
							<a href="{$link->getModuleLink('marketplace', 'mpsupplierlist')|escape:'htmlall':'UTF-8'}" title="{l s='Suppliers' mod='marketplace'}" >
								<i class="material-icons">local_shipping</i>
								{l s='Suppliers' mod='marketplace'}
							</a>
						</span>
					</li>
				{/if}
				{if Configuration::get('WK_MP_SELLER_SHIPPING')}
					<li {if $logic=='mp_carriers'}class="menu_active"{/if}>
						<span>
							<a href="{$link->getModuleLink('marketplace', 'mpshippinglist')|escape:'htmlall':'UTF-8'}" title="{l s='Carriers' mod='marketplace'}" >
								<i class="material-icons">&#xE905;</i>
								{l s='Carriers' mod='marketplace'}
							</a>
						</span>
					</li>
				{/if}
				{hook h="displayMPMenuBottom"}
			</ul>
		</div>
	{else}
		{hook h="displayMPStaffMenu"}
	{/if}
</div>
{* Breadcrumb for seller panel each page *}
<div data-depth="{$breadcrumb.count|escape:'htmlall':'UTF-8'}" class="wk-mp-breadcrumb">
	<ol>
		{foreach from=$breadcrumb.links item=path name=breadcrumb}
		  {block name='breadcrumb_item'}
			<li>
			  {if not $smarty.foreach.breadcrumb.last}
				<a href="{$path.url|escape:'htmlall':'UTF-8'}"><span>{$path.title|escape:'htmlall':'UTF-8'}</span></a>
			  {else}
				<span>{$path.title|escape:'htmlall':'UTF-8'}</span>
			  {/if}
			</li>
		  {/block}
		{/foreach}
	</ol>
</div>