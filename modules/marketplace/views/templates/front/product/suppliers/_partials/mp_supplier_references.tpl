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
<div>
	{if isset($selected_suppliers_data)}
		<br>
		<h4>{l s='Supplier reference(s)' mod='marketplace'}</h4>
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-info" role="alert">
					<p class="alert-text">
						{l s='You can specify product reference(s) for each associated supplier. Click "Save" after changing selected suppliers to display the associated product references.' mod='marketplace'}
					</p>
				</div>
			</div>
		</div>
		{foreach $selected_suppliers_data as $selected_supplier_attr}
			<div class="panel panel-default">
				<div class="panel-heading"><strong>{$selected_supplier_attr[0].id_supplier|escape:'htmlall':'UTF-8'} -
						{$selected_supplier_attr[0].name|escape:'htmlall':'UTF-8'}</strong>
				</div>
				<div class="panel-body" id="supplier_combination_{$selected_supplier_attr[0].id_supplier|escape:'htmlall':'UTF-8'}">
					<div>
						<table class="table">
							<thead class="thead-default">
								<tr>
									<th width="30%">{l s='Product name' mod='marketplace'}</th>
									<th width="30%">{l s='Supplier reference' mod='marketplace'}</th>
									<th width="20%">{l s='Cost price (tax excl.)' mod='marketplace'}</th>
									<th width="20%">{l s='Currency' mod='marketplace'}</th>
								</tr>
							</thead>
							<tbody>
								{foreach $selected_supplier_attr as $selected_supplier}
									{if (isset($combination_detail) && $selected_supplier.id_product_attribute != 0) || empty($combination_detail)}
										<tr>
											{if isset($combination_detail) && $selected_supplier.id_product_attribute > 0}
												{foreach from=$combination_detail item=$combination}
													{if $combination.id_product_attribute == $selected_supplier.id_product_attribute}
														<td>{$combination.attribute_designation|escape:'htmlall':'UTF-8'}</td>
													{/if}
												{/foreach}
											{else}
												<td>{$product_info.name[$default_lang]|escape:'htmlall':'UTF-8'}</td>
											{/if}

											<td>
												<input type="text"
													name="supplier_combination_{$selected_supplier.id_supplier|escape:'htmlall':'UTF-8'}[{$selected_supplier.id_product_attribute|escape:'htmlall':'UTF-8'}][supplier_reference]"
													value='{$selected_supplier.product_supplier_reference|escape:'htmlall':'UTF-8'}' class="form-control">
											</td>
											<td>
												<div class="input-group">
													<span class="input-group-addon">
														{foreach from=$currencies item=curr}
															{if $selected_supplier.id_currency == $curr.id_currency}{$curr.symbol|escape:'htmlall':'UTF-8'}{/if}
														{/foreach}
													</span>
													<input type="text" id="" class="form-control wk_text_field"
														name="supplier_combination_{$selected_supplier.id_supplier|escape:'htmlall':'UTF-8'}[{$selected_supplier.id_product_attribute|escape:'htmlall':'UTF-8'}][product_price]"
														value='{$selected_supplier.product_supplier_price_te|escape:'htmlall':'UTF-8'}'
														class="form-control wk_text_field">
												</div>
											</td>
											<td>
												<select
													id="supplier_combination_{$selected_supplier.id_supplier|escape:'htmlall':'UTF-8'}_{$selected_supplier.id_product_attribute|escape:'htmlall':'UTF-8'}_product_price_currency"
													name="supplier_combination_{$selected_supplier.id_supplier|escape:'htmlall':'UTF-8'}[{$selected_supplier.id_product_attribute|escape:'htmlall':'UTF-8'}][product_price_currency]"
													onchange="changeSuppliersCurrency({$selected_supplier.id_supplier|escape:'htmlall':'UTF-8'}, {$selected_supplier.id_product_attribute|escape:'htmlall':'UTF-8'});"
													class="form-control form-control-select wkinput">
													{foreach from=$currencies item=curr}
														<option value="{$curr.id_currency|escape:'htmlall':'UTF-8'}" data-symbol='{$curr.symbol|escape:'htmlall':'UTF-8'}'
															{if $selected_supplier.id_currency == $curr.id_currency}selected{/if}>
															{$curr.id_currency|escape:'htmlall':'UTF-8'} - {$curr.name|escape:'htmlall':'UTF-8'}
														</option>
													{/foreach}
												</select>
												<input type="hidden"
													name="supplier_combination_{$selected_supplier.id_supplier|escape:'htmlall':'UTF-8'}[{$selected_supplier.id_product_attribute|escape:'htmlall':'UTF-8'}][id_product_attribute]"
													class="form-control" value="{$selected_supplier.id_product_attribute|escape:'htmlall':'UTF-8'}">

												<input type="hidden"
													name="supplier_combination[{$selected_supplier.id_supplier|escape:'htmlall':'UTF-8'}][{$selected_supplier.id_product_attribute|escape:'htmlall':'UTF-8'}][supplier_id]"
													class="form-control" value="{$selected_supplier.id_supplier|escape:'htmlall':'UTF-8'}">
											</td>
										</tr>
									{/if}
								{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		{/foreach}
	</div>
{/if}