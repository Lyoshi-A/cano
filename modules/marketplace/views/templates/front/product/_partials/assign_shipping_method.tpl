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

<a class="assign_shipping" href="#assign_shipping_form">
	<button class="btn btn-primary btn-sm mb-1" type="button">
		<i class="material-icons">&#xE896;</i>
		{l s='Assign Shipping' mod='marketplace'}
	</button>
</a>
{hook h="displayMpProductListTop"}

<div class="modal fade" id="assign_shipping_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-body">
		  	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{l s='Close' mod='marketplace'}</span></button>
			<div>
				<div class="headingclass">{l s='Note: Previously applied shipping will get unselected and the shipping methods selected by you will get assigned to all the products.' mod='marketplace'}</div>
				<form method="post" action="{$ajax_link|escape:'htmlall':'UTF-8'}" id="shipping_form">
					<input type="hidden" name="token" value="{Tools::getToken(false)|escape:'htmlall':'UTF-8'}">
					<input type="hidden" value="{$mp_id_seller|escape:'htmlall':'UTF-8'}" name="mp_id_seller">
					{foreach $shipping_method as $shipping_data}
						<div class="wk_shipping_data">
							<div class="wk_shipping_name">
								<input type="checkbox" id="shipping_method_{$shipping_data.id_reference|escape:'htmlall':'UTF-8'}" name="shipping_method[]" value="{$shipping_data.id_reference|escape:'htmlall':'UTF-8'}">
							</div>
							<div style="float:left;">
								<label for="shipping_method_{$shipping_data.id_reference|escape:'htmlall':'UTF-8'}"  style="font-weight: normal;">
									{$shipping_data.id_carrier|escape:'htmlall':'UTF-8'} - {$shipping_data.name|escape:'htmlall':'UTF-8'}
								</label>
							</div>
							<div style="clear:both;"></div>
						</div>
					{/foreach}
					<button class="btn btn-primary btn-sm" id="assign" style="margin-top:10px;">
						{l s='Submit' mod='marketplace'}
					</button>
				</form>
			</div>
		  </div>
		</div>
	</div>
</div>
