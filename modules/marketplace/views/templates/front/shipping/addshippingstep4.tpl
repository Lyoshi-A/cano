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
			{if isset($updateimpact)}
				{l s='Update Impact Price' mod='marketplace'}
			{else}
				{l s='Add Impact Price' mod='marketplace'}
			{/if}
			{if isset($mpshipping_name)}
				- {$mpshipping_name|escape:'htmlall':'UTF-8'}
			{/if}
			</span>
		</div>
		<div class="wk-mp-right-column" style="border: none;">
			<div id="newbody"></div>
			<div id="impact_price_block">
				{include file='module:marketplace/views/templates/front/shipping/addimpactprice.tpl'}
			</div>
			{if isset($mp_shipping_id)}
				<input type="hidden" name="mpshipping_id" value="{$mp_shipping_id|escape:'htmlall':'UTF-8'}">
			{/if}
			{if isset($addmpshipping_success)}
				{if $addmpshipping_success == 1}
					<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						{l s='Carrier added successfully.' mod='marketplace'}
					</div>
				{/if}
			{/if}
			{if isset($deleteimpact)}
				{if $deleteimpact == 1}
					<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						{l s='Impact price deleted successfully.' mod='marketplace'}
					</div>
				{/if}
			{/if}
			<input type="hidden" name="mpshipping_id" id="mpshipping_id" value="{$mpshipping_id|escape:'htmlall':'UTF-8'}">
			<input type="hidden" name="step4_shipping_method" value="{$shipping_method|escape:'htmlall':'UTF-8'}" class="step4_shipping_method" />
			<div class="left full row mb-1">
				<div class="left lable">
					{l s='Zone' mod='marketplace'}
				</div>
				<div class="left input_label">
					<select name="step4_zone" id="step4_zone" class="form-control" style="width:40%;">
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
					<div class="left input_label">
						<select name="step4_country" id="step4_country" class="form-control" style="width:40%;">
							<option value="-1">{l s='Select country' mod='marketplace'}</option>
						</select>
					</div>
				</div>
				<div class="left full" id="state_container" style="display:none;">
					<div class="left full row">
						<div class="left lable">
							{l s='State' mod='marketplace'}
						</div>
						<div class="left input_label">
							<select name="step4_state" id="step4_state" class="form-control" style="width:40%;">
								<option value="0">{l s='All state' mod='marketplace'}</option>
							</select>
						</div>
					</div>
					<div class="left full row">
						<div class="left lable"></div>
							<div class="left input_label">
								{if isset($updateimpact)}
									<input type="button" class="btn btn-primary btn-sm" id="impactprice_button" value="{l s='Click to update impact price' mod='marketplace'}">
								{else}
									<input type="button" class="btn btn-primary btn-sm" id="impactprice_button" value="{l s='Click to add impact price' mod='marketplace'}">
								{/if}
							</div>
						</div>
					</div>
				</div>
			<div class="left full text-center" id="loading_ajax"></div>
		</div>
		<div class="clearfix"></div>

		{if isset($updateimpact)}
		<div class="wk-mp-right-column">
		<div class="box-content" style="margin: 10px;">
			<table class="table table-striped">
			<thead>
				<tr class="first last">
					<th style="width: 10%;">{l s='ID' mod='marketplace'}</th>
					<th style="width: 20%;">{l s='Zone' mod='marketplace'}</th>
					<th style="width: 20%;">{l s='Country' mod='marketplace'}</th>
					<th style="width: 20%;">{l s='State' mod='marketplace'}</th>
					<th style="width: 20%;">{l s='Impact Price' mod='marketplace'}</th>
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
								<a href="{$link->getModuleLink('marketplace','addmpshipping',['mpshipping_id'=>{$impactprice['mp_shipping_id']|escape:'htmlall':'UTF-8'}, 'impact_id'=>{$impactprice['id']|escape:'htmlall':'UTF-8'}, 'addmpshipping_step4'=>1, 'updateimpact' => 1])|escape:'htmlall':'UTF-8'}" class="delete_shipping" title="{l s='Delete' mod='marketplace'}">
									<i class="material-icons">&#xE872;</i>
								</a>
							</td>
						</tr>
					{/foreach}
				{else}
					<tr>
						<td colspan="7"><center>{l s='No impact price yet' mod='marketplace'}</center></td>
					</tr>
				{/if}
			</tbody>
			</table>
		</div>
		</div>
		{/if}
	</div>
</div>
<div class="loading_overlay">
	<img src="{$modules_dir|escape:'htmlall':'UTF-8'}marketplace/views/img/loader.gif" class="loading-img"/>
</div>
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}
