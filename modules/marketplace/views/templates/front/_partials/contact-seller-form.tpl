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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content" style="text-align:left;">
			{if isset($contactSellerAllowed)}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">{l s='Write your query' mod='marketplace'}</h4>
				</div>
				<form id="wk_contact_seller_form" method="post" action="#">
					<div class="modal-body">
						<div class="form-group">
							<label class="control-label required">{l s='Email' mod='marketplace'}</label>
							<input type="text" name="customer_email" id="customer_email" class="form-control" value="{if $customer_email|escape:'htmlall':'UTF-8'}{$customer_email|escape:'htmlall':'UTF-8'}{/if}" />
						</div>
						<div class="form-group">
							<label class="control-label required">{l s='Subject' mod='marketplace'}</label>
							<input type="text" name="query_subject" class="form-control" id="query_subject" />
						</div>
						<div class="form-group">
							<label class="control-label required">{l s='Description' mod='marketplace'}</label>
							<textarea name="query_description" class="form-control" id="query_description" style="height:100px;"></textarea>
						</div>
						<input type="hidden" name="id_seller" value="{$seller_id|escape:'htmlall':'UTF-8'}"/>
						<input type="hidden" name="token" id="wk-static-token" value="{$static_token|escape:'htmlall':'UTF-8'}">
						<div class="form-group">
							<div class="contact_seller_message"></div>
						</div>
						{block name='mp-form-fields-notification'}
							{include file='module:marketplace/views/templates/front/_partials/mp-form-fields-notification.tpl'}
						{/block}
					</div>
					<div class="modal-footer">
						<div class="form-group row">
							<div class="col-xs-6 col-sm-6 col-md-6" style="text-align:left">
								<button type="button" class="btn wk_btn_cancel wk_btn_extra" data-dismiss="modal">
									{l s='Cancel' mod='marketplace'}
								</button>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 wk_text_right">
								<button type="submit" class="btn btn-success wk_btn_extra" id="wk_contact_seller" name="wk_contact_seller">
									{l s='Send' mod='marketplace'}
								</button>
							</div>
						</div>
					</div>
				</form>
			{else}
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">
							{l s='Please login for contacting a seller.' mod='marketplace'}
						</h4>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn wk_btn_cancel wk_btn_extra" data-dismiss="modal">{l s='Cancel' mod='marketplace'}</button>
						<a href="{$myAccount|escape:'htmlall':'UTF-8'}">
							<button type="button" class="btn btn-success wk_btn_extra">{l s='Login' mod='marketplace'}</button>
						</a>
					</div>
				</div>
			{/if}
		</div>
	</div>
</div>