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

<div class="wk_combination_fields_head">
	<h4>{l s='Generate Combination' mod='marketplace'}</h4>
</div>
<div class="box-content">
	<div class="alert alert-danger" {if $message==0}style="display:none;"{/if}>
		{if $message==1}
			{l s='Please select at least one attribute' mod='marketplace'}
		{elseif $message==2}
			{l s='Unable to initialize these parameters. A combination is missing or an object cannot be loaded.' mod='marketplace'}
		{/if}
	</div>
	<div class="wk_attr_selector_form">
		<form enctype="multipart/form-data" method="post" id="generator" action="{if isset($backendController)}{$current|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}&id_mp_product={$id_mp_product|escape:'htmlall':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'generatecombination',['id_mp_product'=>$id_mp_product])|escape:'htmlall':'UTF-8'}{/if}">
			<div class="col-md-4 col-xs-8 wk_attr_selector">
				<div class="form-group">
					<select multiple name="attributes[]" id="attribute_group" class="form-control">
						{foreach $attribute_groups as $k => $attribute_group}
							{if isset($attribute_js[$attribute_group['id_attribute_group']])}
								<optgroup name="{$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}" id="{$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}" label="{$attribute_group['name']|escape:'htmlall':'UTF-8'}">
									{foreach $attribute_js[$attribute_group['id_attribute_group']] as $k => $v}
										<option name="{$k|escape:'htmlall':'UTF-8'}" id="attr_{$k|escape:'htmlall':'UTF-8'}" value="{$v|escape:'htmlall':'UTF-8'}" title="{$v|escape:'htmlall':'UTF-8'}">{$v|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								</optgroup>
							{/if}
						{/foreach}
					</select>
					<div class="wk_attr_gp_btn">
						<input class="btn btn-primary btn-sm" type="button" value="{l s='Add' mod='marketplace'}" onclick="add_attr_multiple();" />
						<input class="btn btn-primary btn-sm" type="button" value="{l s='Delete' mod='marketplace'}" onclick="del_attr_multiple();" />
					</div>
				</div>
			</div>
			<div class="col-md-8 col-xs-10">
				<div class="form-group">
					{foreach $attribute_groups as $k => $attribute_group}
						{if isset($attribute_js[$attribute_group['id_attribute_group']])}
							<table class="table clear" cellpadding="0" cellspacing="0" style="margin-bottom: 10px;{if !isset($attributes[$attribute_group['id_attribute_group']])}display: none;{/if}">
								<thead>
									<tr>
										<th id="tab_h1" style="width: 150px">{$attribute_group['name']|escape:'htmlall':'UTF-8'}</th>
										<th id="tab_h2" style="width: 350px">{l s='Impact on the product price' mod='marketplace'} ({$currency_sign|escape:'htmlall':'UTF-8'})</th>
										<th style="width: 150px">{l s='Impact on the product weight' mod='marketplace'} ({$weight_unit|escape:'htmlall':'UTF-8'})</th>
									</tr>
								</thead>
								<tbody id="table_{$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}" name="result_table">
									{if isset($attributes[$attribute_group['id_attribute_group']])}
										{foreach $attributes[$attribute_group['id_attribute_group']] AS $k => $attribute}
											{if isset($attribute['price']) || isset($attribute['weight'])}
												<tr id="result_{$k|escape:'htmlall':'UTF-8'}">
													<td><input type="hidden" value="{$k|escape:'htmlall':'UTF-8'}" name="options[{$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}][{$k|escape:'htmlall':'UTF-8'}]" />{$attribute['attribute_name']|addslashes}</td>
													<td>
														{l s='Tax Excluded' mod='marketplace'}<br>
														<input id="related_to_price_impact_ti_{$k|escape:'htmlall':'UTF-8'}" class="text-center price_impact form-control" style="width:70px" type="text" value="{$attribute['price']|escape:'htmlall':'UTF-8'}" name="price_impact_{$k|escape:'htmlall':'UTF-8'}" onkeyup="calcPrice($(this), false)" pattern="^-?\d+(\.\d+)?">
													</td>
													<td><input style="width:50px;margin-top:15px;" type="text" class="text-center form-control" value="{$attribute['weight']|escape:'htmlall':'UTF-8'}" name="weight_impact_{$k|escape:'htmlall':'UTF-8'}" pattern="^-?\d+(\.\d+)?"></td>
												</tr>
											{else}
												<tr id="result_{$k|escape:'htmlall':'UTF-8'}">
													<td><input type="hidden" value="{$k|escape:'htmlall':'UTF-8'}" name="options[{$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}][{$k|escape:'htmlall':'UTF-8'}]" />{$attribute['attribute_name']|addslashes}</td>
													<td>
														{l s='Tax Excluded' mod='marketplace'}<br>
														<input id="related_to_price_impact_ti_{$k|escape:'htmlall':'UTF-8'}" class="text-center price_impact form-control" style="width:70px" type="text" value="{$attribute['mp_price']|escape:'htmlall':'UTF-8'}" name="price_impact_{$k|escape:'htmlall':'UTF-8'}" onkeyup="calcPrice($(this), false)" pattern="^-?\d+(\.\d+)?">
													</td>
													<td><input style="width:50px;margin-top:15px;" type="text" class="text-center form-control" value="{$attribute['mp_weight']|escape:'htmlall':'UTF-8'}" name="weight_impact_{$k|escape:'htmlall':'UTF-8'}" pattern="^-?\d+(\.\d+)?"></td>
												</tr>
											{/if}
										{/foreach}
									{/if}
								</tbody>
							</table>
						{/if}
					{/foreach}
				</div>
				<div class="form-group">
					<div class="left col-lg-12">
						<h4>{l s='Select a default quantity, and reference, for each combination the generator will create for this product.' mod='marketplace'}</h4>
						<table border="0" class="table" cellpadding="0" cellspacing="0">
							{if Configuration::get('PS_STOCK_MANAGEMENT')}
								<tr>
									<td>{l s='Default Quantity' mod='marketplace'}</td>
									<td><input type="text" name="quantity" value="0" class="form-control" style="width: 75px;" /></td>
								</tr>
							{/if}
							{if isset($backendController) || Configuration::get('WK_MP_SELLER_PRODUCT_REFERENCE')}
								<tr>
									<td>{l s='Default Reference' mod='marketplace'}</td>
									<td><input type="text" maxlength="32" class="form-control" name="reference" value="" /></td>
								</tr>
							{/if}
						</table>
						<button type="submit" class="btn btn-success wk_btn_extra form-control-submit" name="GenerateCombination" id="GenerateCombination" style="margin-bottom:10px;" />
							<span>{l s='Generate these Combinations' mod='marketplace'}</span>
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>