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

{if $is_seller == -1}
<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" title="{l s='Click Here for Seller Request' mod='marketplace'}" href="{$link->getModuleLink('marketplace', 'sellerlogin')|escape:'htmlall':'UTF-8'}" target="_blank">
	<span class="link-item">
		<i class="material-icons">&#xE15E;</i>
		{l s='Become a Seller' mod='marketplace'}
	</span>
</a>
{else}
	<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" title="{l s='Seller Account' mod='marketplace'}" href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'htmlall':'UTF-8'}" target="_blank">
		<span class="link-item">
			<i class="material-icons">&#xE871;</i>
			{l s='Seller Account' mod='marketplace'}
		</span>
	</a>
{/if}
