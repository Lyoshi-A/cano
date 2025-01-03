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

<div class="wk-collection-top row">
	<div class="col-xs-12 col-sm-12 col-md-6 wk_collection_total_products">
		{l s='There are %1$d Products' sprintf=[$currentProductCount] mod='marketplace'}
	</div>
	<div class="col-md-6">
		<div class="row">
		<div class="select">
			<span class="col-xs-12 col-sm-12 col-md-3 wk-collection-sort-by">{l s='Sort by' mod='marketplace'}</span>
			<div class="col-xs-12 col-sm-12 col-md-9">
				<select id="selectMpProductSort" class="selectMpProductSort form-control form-control-select">
					<option value="{$defaultorederby|escape:'htmlall':'UTF-8'}"{if $orderby eq 'id' AND $orderway eq 'desc'} selected="selected"{/if}>{l s='Select' mod='marketplace'}</option>
					{if !$PS_CATALOG_MODE}
						<option value="price:asc"{if $orderby eq 'price' AND $orderway eq 'asc'} selected="selected"{/if}>{l s='Price: Lowest first' mod='marketplace'}</option>
						<option value="price:desc"{if $orderby eq 'price' AND $orderway eq 'desc'} selected="selected"{/if}>{l s='Price: Highest first' mod='marketplace'}</option>
					{/if}
					<option value="name:asc"{if $orderby eq 'name' AND $orderway eq 'asc'} selected="selected"{/if}>{l s='Product name: A to Z' mod='marketplace'}</option>
					<option value="name:desc"{if $orderby eq 'name' AND $orderway eq 'desc'} selected="selected"{/if}>{l s='Product name: Z to A' mod='marketplace'}</option>
					<option value="quantity:desc"{if $orderby eq 'quantity' AND $orderway eq 'desc'} selected="selected"{/if}>{l s='In stock' mod='marketplace'}</option>
				</select>
			</div>
		</div>
		</div>
	</div>
</div>

