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

<div class="form-group">
	<label>{l s='Maximum package width (cm)' mod='marketplace'}</label>
	<input type="text" class="form-control" name="max_width" id="max_width" value="{$max_width|escape:'htmlall':'UTF-8'}">
	<p class="help-block">
		{l s='Maximum width managed by this carrier. Set the value to "0", or leave this field blank to ignore. The value must be an integer.' mod='marketplace'}
	</p>
</div>
<div class="form-group">
	<label>{l s='Maximum package height (cm)' mod='marketplace'}</label>
	<input type="text" class="form-control" name="max_height" id="max_height" value="{$max_height|escape:'htmlall':'UTF-8'}">
	<p class="help-block">
		{l s='Maximum height managed by this carrier. Set the value to "0", or leave this field blank to ignore. The value must be an integer.' mod='marketplace'}
	</p>
</div>
<div class="form-group">
	<label>{l s='Maximum package depth (cm)' mod='marketplace'}</label>
	<input type="text" class="form-control" name="max_depth" id="max_depth" value="{$max_depth|escape:'htmlall':'UTF-8'}">
	<p class="help-block">
		{l s='Maximum depth managed by this carrier. Set the value to "0", or leave this field blank to ignore. The value must be an integer. ' mod='marketplace'}
	</p>
</div>
<div class="form-group">
	<label>{l s='Maximum package weight (kg)' mod='marketplace'}</label>
	<input type="text" class="form-control" name="max_weight" id="max_weight" value="{$max_weight|escape:'htmlall':'UTF-8'}">
	<p class="help-block">
		{l s='Maximum weight managed by this carrier. Set the value to "0", or leave this field blank to ignore. ' mod='marketplace'}
	</p>
</div>
{*Display Group access*}
{if isset($customerAllGroups)}
<div class="left full form-group">
	<label>{l s='Group Access' mod='marketplace'}</label>
	<table class="table" style="width:40%;">
		<thead>
			<tr>
				<th class="fixed-width-xs">
					<span class="title_box">
						<input type="checkbox" id="wk_select_all_checkbox">
					</span>
				</th>
				<th class="fixed-width-xs"><span class="title_box">{l s='ID' mod='marketplace'}</span></th>
				<th>
					<span class="title_box">
						{l s='Group Name' mod='marketplace'}
					</span>
				</th>
			</tr>
		</thead>
		<tbody>
			{foreach $customerAllGroups as $groupVal}
				<tr>
					<td><input type="checkbox" value="{$groupVal.id_group|escape:'htmlall':'UTF-8'}" name="shipping_group[]" {if isset($mp_shipping_id)}{if isset($shippingGroup) && in_array($groupVal.id_group, $shippingGroup)}checked="checked"{/if}{else}checked="checked"{/if}>
					</td>
					<td>{$groupVal.id_group|escape:'htmlall':'UTF-8'}</td>
					<td><label for="groupBox_{$groupVal.id_group|escape:'htmlall':'UTF-8'}">{$groupVal.name|escape:'htmlall':'UTF-8'}</label></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>
{/if}