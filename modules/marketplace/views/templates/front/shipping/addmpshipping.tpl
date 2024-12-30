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

{extends file=$layout}
{block name='header'}
	{include file='module:marketplace/views/templates/front/_partials/header.tpl'}
{/block}
{block name='content'}
<div class="wk-mp-block">
	{hook h="displayMpMenu"}
	<div class="wk-mp-content">
		<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
			<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">
				{if isset($mp_shipping_id) || (isset($mp_error_message) && $mp_error_message)}
					{l s='Update Carrier' mod='marketplace'}
				{else}
					{l s='Add Carrier' mod='marketplace'}
				{/if}
			</span>
		</div>
		<div class="wk-mp-right-column">
			{if isset($mp_error_message) && $mp_error_message}
				<div class="alert alert-danger">
					{$mp_error_message|escape:'htmlall':'UTF-8'}
				</div>
			{else}
			<div class="shipping_list_container wk_product_list left">
				<input type="hidden" id="getshippingstep" name="getshippingstep" value="">
				<div class="shipping_add swMain"  id="carrier_wizard">
					<ul class="nbr_steps_4 anchor">
						<li style="width:33%;">
							<a class="steptab selected" isdone="1" rel="1" id="step_heading1">
								<label class="stepNumber">1</label>
								<span class="stepDesc">
									{l s='General settings' mod='marketplace'}
									<br>
								</span>
							</a>
						</li>
						<li style="width:33%;">
							<a class="steptab {if isset($mp_shipping_id)} done {else} disabled {/if}" rel="2" id="step_heading2">
								<label class="stepNumber">2</label>
								<span class="stepDesc">
									{l s='Shipping locations and costs' mod='marketplace'}
									<br />
								</span>
							</a>
						</li>
						<li style="width:33%;">
							<a class="steptab {if isset($mp_shipping_id)} done {else} disabled {/if}" rel="3" id="step_heading3">
								<label class="stepNumber">3</label>
								<span class="stepDesc">
									{l s='Size, weight and group access' mod='marketplace'}
									<br />
								</span>
							</a>
						</li>
					</ul>
					<form role="form" id="addshippingmethod" class="defaultForm form-horizontal" enctype="multipart/form-data" method="post" action="{$mpshippingprocess|escape:'htmlall':'UTF-8'}">
					<div class="stepContainer left">
						{if isset($mp_shipping_id)}
							<input type="hidden" name="mp_shipping_id" id="mp_shipping_id" value="{$mp_shipping_id|escape:'htmlall':'UTF-8'}">
						{/if}
						<input type="hidden" name="current_lang" id="current_lang" value="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}">
						<div id="step-1">
							{include file='module:marketplace/views/templates/front/shipping/addshippingstep1.tpl'}
						</div>
						<div id="step-2" style="display:none;">
							{include file='module:marketplace/views/templates/front/shipping/addshippingstep2.tpl'}
						</div>
						<div id="step-3" style="display:none;">
							{include file='module:marketplace/views/templates/front/shipping/addshippingstep3.tpl'}
						</div>
					</div>
					<div class="actionBar">
						<div class="msgBox">`
							<div class="content"></div>
								<a class="close" href="#">X</a>
							</div>
						<div class="loader">{l s='Loading' mod='marketplace'}</div>
						<button type="submit" id="FinishButtonclick" style="{if !isset($mp_shipping_id)}display:none;{/if}cursor:pointer;" class="buttonFinish">{l s='Finish' mod='marketplace'}</button>
					</form>
						<div class="buttonFinish buttonDisabled" id="Finishdisablebutton" style="{if isset($mp_shipping_id)}display:none;{/if}cursor:pointer;">{l s='Finish' mod='marketplace'}</div>

						<div class="buttonNext buttonDisabled" id="Nextdisablebutton" style="display:none;cursor:pointer;">{l s='Next' mod='marketplace'}</div>
						<div class="buttonNext" id="NextButtonclick" style="cursor:pointer;">{l s='Next' mod='marketplace'}</div>

						<div class="buttonPrevious buttonDisabled" id="Previousdisablebutton" style="cursor:pointer;">{l s='Previous' mod='marketplace'}</div>
						<div class="buttonPrevious" id="PreviousButtonclick" style="display:none;cursor:pointer;">{l s='Previous' mod='marketplace'}</div>
					</div>
				</div>
			</div>
			{/if}
		</div>
	</div>
</div>
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}