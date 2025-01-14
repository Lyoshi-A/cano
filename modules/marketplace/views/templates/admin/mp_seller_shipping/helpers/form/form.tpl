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

<style>
#add_new_range.btn-primary-outline, .delbutton {
	background-color: #fff !important;
	border: 1px solid #2fb5d2 !important;
	color: #2fb5d2 !important;
	margin-top: 7px;
}
</style>
<div class="main_block panel">
{if isset($updateimpact) && $updateimpact}
	<div id="newbody"></div>
	<div id="impact_price_block">
		{include file="$self/../../views/templates/front/shipping/addimpactprice.tpl"}
	</div>

	<input type="hidden" name="mpshipping_id" id="mpshipping_id" value="{$mp_shipping_id|escape:'htmlall':'UTF-8'}">
	<input type="hidden" name="step4_shipping_method" value="{$shipping_method|escape:'htmlall':'UTF-8'}" class="step4_shipping_method" />
	<div class="left full row mb-1">
		<div class="left lable">
			{l s='Zone' mod='marketplace'}
		</div>
		<div class="left input_label col-lg-4">
			<select name="step4_zone" id="step4_zone">
				<option value="-1">{l s='Select zone' mod='marketplace'}</option>
			{foreach $zones as $zon}
				<option value="{$zon['id_zone']|escape:'htmlall':'UTF-8'}">{$zon['name']|escape:'htmlall':'UTF-8'}</option>
			{/foreach}
			</select>
		</div>
	</div>
	<div class="left full" id="country_container" style="display:none;">
		<div class="left full row">
			<div class="left lable">
				{l s='Country' mod='marketplace'}
			</div>
			<div class="left input_label col-lg-4">
				<select name="step4_country" id="step4_country">
					<option value="-1">{l s='Select country' mod='marketplace'}</option>
				</select>
			</div>
		</div>
		<div class="left full" id="state_container" style="display:none;">
			<div class="left full row">
				<div class="left lable">
					{l s='State' mod='marketplace'}
				</div>
				<div class="left input_label col-lg-4">
					<select name="step4_state" id="step4_state">
						<option value="0">{l s='All state' mod='marketplace'}</option>
					</select>
				</div>
			</div>
			<div class="left full row">
				<div class="left lable"></div>
					<div class="left input_label col-lg-4">
						<input type="button" class="btn btn-default button button-small" id="impactprice_button" value="{l s='Click to update impact price' mod='marketplace'}">
					</div>
				</div>
			<div class="left full row" style="text-align:center;">
			</div>
		</div>
	</div>
	<div class="left full text-center" id="loading_ajax"></div>
	<div style="clear:both;"></div>

	<div class="panel" style="margin-top: 10px;">
		<table class="table">
		<thead>
			<tr class="first last">
				<th style="width: 10%;">{l s='ID' mod='marketplace'}</th>
				<th style="width: 20%;">{l s='Zone' mod='marketplace'}</th>
				<th style="width: 20%;">{l s='Country' mod='marketplace'}</th>
				<th style="width: 20%;">{l s='State' mod='marketplace'}</th>
				<th style="width: 20%;">{l s='Impact price' mod='marketplace'}</th>
				<th style="width: 20%;">
					{if $shipping_method == 2}
						{l s='Price range' mod='marketplace'}
					{else}
						{l s='Weight range' mod='marketplace'}
					{/if}
				</th>
				<th style="width: 10%;">{l s='Action' mod='marketplace'}</th>
			</tr>
		</thead>
		<tbody>
			{if isset($impactprice_arr)}
				{foreach $impactprice_arr as $impactprice}
					<tr class="even">
						<td>{$impactprice.id|escape:'htmlall':'UTF-8'}</td>
						<td>{$impactprice.id_zone|escape:'htmlall':'UTF-8'}</td>
						<td>{$impactprice.id_country|escape:'htmlall':'UTF-8'}</td>
						<td>{$impactprice.id_state|escape:'htmlall':'UTF-8'}</td>
						<td>{$impactprice.impact_price_display|escape:'htmlall':'UTF-8'}</td>
						<td>
							{if $shipping_method == 2}
								{$impactprice.price_range|escape:'htmlall':'UTF-8'}
							{else}
								{$impactprice.weight_range|escape:'htmlall':'UTF-8'}
							{/if}
						</td>
						<td>
							<a href="{$link->getAdminLink('AdminMpSellerShipping')|escape:'htmlall':'UTF-8'}&id_wk_mp_shipping={$impactprice.mp_shipping_id|escape:'htmlall':'UTF-8'}&impact_id={$impactprice.id|escape:'htmlall':'UTF-8'}&deleteimpact=1" class="delete_impact btn btn-default" title="{l s='Delete' mod='marketplace'}">
								<i class="icon-trash"></i>
							</a>

						</td>
					</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan="6"><center>{l s='No impact price yet' mod='marketplace'}</center></td>
				</tr>
			{/if}
		</tbody>
		</table>
	</div>
{else}
	<div class="dashboard_content">
		<div class="wk_right_col">
			<div class="shipping_list_container left">
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
					<form role="form" id="addshippingmethod" class="defaultForm form-horizontal" enctype="multipart/form-data" method="post" action="">
					<div class="stepContainer left">
						{if isset($mp_shipping_id)}
							<input type="hidden" name="mp_shipping_id" id="mp_shipping_id" value="{$mp_shipping_id|escape:'htmlall':'UTF-8'}">
						{/if}
						<input type="hidden" name="multilang" id="multilang" value="{$multi_lang|escape:'htmlall':'UTF-8'}">
						<input type="hidden" name="current_lang" id="current_lang" value="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}">

						<div id="step-1">
							{include file="$self/../../views/templates/front/shipping/addshippingstep1.tpl"}
						</div>
						<div id="step-2" style="display:none;">
							{include file="$self/../../views/templates/front/shipping/addshippingstep2.tpl"}
						</div>
						<div id="step-3" style="display:none;">
							{include file="$self/../../views/templates/front/shipping/addshippingstep3.tpl"}
						</div>

					</div>
					<div class="actionBar">
						<div class="msgBox">`
							<div class="content"></div>
								<a class="close" href="#">X</a>
							</div>
						<div class="loader">{l s='Loading' mod='marketplace'}</div>
						<button type="submit" id="FinishButtonclick" name="FinishButtonclick" style="{if !isset($mp_shipping_id)}display:none;{/if}" class="buttonFinish">{l s='Finish' mod='marketplace'}</button>
					</form>
						<div class="buttonFinish buttonDisabled" id="Finishdisablebutton" style="{if isset($mp_shipping_id)}display:none;{/if}cursor:pointer;">{l s='Finish' mod='marketplace'}</div>

						<div class="buttonNext buttonDisabled" id="Nextdisablebutton" style="display:none;cursor:pointer;">{l s='Next' mod='marketplace'}</div>
						<div class="buttonNext" id="NextButtonclick" style="cursor:pointer;">{l s='Next' mod='marketplace'}</div>

						<div class="buttonPrevious buttonDisabled" id="Previousdisablebutton" style="cursor:pointer;">{l s='Previous' mod='marketplace'}</div>
						<div class="buttonPrevious" id="PreviousButtonclick" style="display:none;cursor:pointer;">{l s='Previous' mod='marketplace'}</div>
					</div>

				</div>
			</div>
		</div>
	</div>
{/if}
<div style="clear:both;"></div>
</div>

{strip}
	{addJsDef adminproducturl = $adminproducturl}
	{addJsDef currency_sign = $currency_sign}
	{addJsDefL name='string_price'}{l s='Will be applied when the price is' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name='string_weight'}{l s='Will be applied when the weight is' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name='impact_price_text'}{l s='Impact Price' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name='interger_price_text'}{l s='Enter price should be an integer' js=1 mod='marketplace'}{/addJsDefL}
	{if isset($updateimpact) && $updateimpact}
		{addJsDef shipping_ajax_link = $shipping_ajax_link}
		{addJsDefL name='select_country'}{l s='Select country' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='select_state'}{l s='All' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='zone_error'}{l s='Select zone' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='zone_error'}{l s='Select country' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='no_range_available_error'}{l s='No range available' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='ranges_info'}{l s='Ranges' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDef currency_sign = $currency_sign}
		{addJsDef img_ps_dir = $img_ps_dir}
		{addJsDefL name='message_impact_price_error'}{l s='Impact price is invalid.' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='message_impact_price'}{l s='Impact added sucessfully' js=1 mod='marketplace'}{/addJsDefL}
	{else}
		{addJsDefL name='invalid_range'}{l s='This range is not valid' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='need_to_validate'}{l s='Please validate the last range before create a new one.' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='delete_range_confirm'}{l s='Are you sure to delete this range ?' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDef currency_sign = $currency_sign}
		{addJsDef PS_WEIGHT_UNIT = $PS_WEIGHT_UNIT}
		{addJsDefL name='labelDelete'}{l s='Delete' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='labelValidate'}{l s='Validate' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='range_is_overlapping'}{l s='Ranges are overlapping' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDef shipping_method = $shipping_method}
		{addJsDefL name='finish_error'}{l s='You need to go through all step' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='shipping_name_error'}{l s='Carrier name is required field' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='transit_time_error'}{l s='Transit time is required atleast in ' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='transit_time_error_other'}{l s='Transit time is required' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='speedgradeinvalid'}{l s='Speed grade must be interger' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='speedgradevalue'}{l s='Speed grade must be from 0 to 9' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='invalid_logo_file_error'}{l s='Invalid logo file!' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='shipping_charge_error_message'}{l s='' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='shipping_charge_lower_limit_error1'}{l s='Shipping charge lower limit must be numeric' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='shipping_charge_lower_limit_error2'}{l s='Please enter positive shipping charge' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='shipping_charge_upper_limit_error1'}{l s='Shipping charge upper limit must be numeric' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='shipping_charge_upper_limit_error2'}{l s='Please enter positive shipping charge' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='shipping_charge_limit_error'}{l s='Shipping charge upper limit must be greater than lower limit' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='shipping_charge_limit_equal_error'}{l s='Shipping charge lower limit and upper limit should not equal' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='invalid_logo_size_error'}{l s='Invalid logo size' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='invalid_range_value'}{l s='Ranges upper and lower values should not clash to one another.' js=1 mod='marketplace'}{/addJsDefL}
		{addJsDefL name='shipping_select_zone_err'}{l s='Please Check at lease one zone.' js=1 mod='marketplace'}{/addJsDefL}
	{/if}
	{if $shipping_method == 2}
		{addJsDef range_sign = $currency_sign}
	{else}
		{addJsDef range_sign = $PS_WEIGHT_UNIT}
	{/if}
	{addJsDef update_impact_link = $update_impact_link}
{/strip}