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

{if isset($isPackProduct) && $isPackProduct}
	<h4 style="border-bottom: 1px solid #ccc;">{l s='Pack Contents' mod='marketplace'}</h4>
	{foreach from=$packProducts key=k item=v}
		<div class="col-sm-4 col-xs-12 wk_padding_none">
			<div class="col-sm-12 col-xs-12 pk_sug_prod">
				<img class="img-responsive pk_sug_img" src="{$v->image_link|escape:'htmlall':'UTF-8'}" style="width: 100%">
				<p class="text-center">({$v->pack_quantity|escape:'htmlall':'UTF-8'} x) {$v->name|escape:'htmlall':'UTF-8'}</p>
			</div>
		</div>
	{/foreach}
{/if}