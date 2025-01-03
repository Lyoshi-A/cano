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

<input type="hidden" id="mp_id_product" name="mp_id_product" value="{$mp_id_product|escape:'htmlall':'UTF-8'}"/>
<input type="hidden" value="{if isset($edit)}{$id_combination|escape:'htmlall':'UTF-8'}{/if}" name="id_combination"/>
<div class="wk_combination_fields_head">
	{if isset($backendController)}
		<h4>{l s='Set Attribute' mod='marketplace'}</h4>
	{else}
		<h6>{l s='Set Attribute' mod='marketplace'}</h6>
	{/if}
</div>
<div class="form-group row">
	<div class="col-md-4">
		<label class="control-label">{l s='Attribute' mod='marketplace'}</label>
		<select id="attribute_select" name="attribute_select" class="form-control form-control-select">
			<option value="">{l s='Select Attribute' mod='marketplace'}</option>
			{foreach $attributeGroup as $attriGroupValue}
				<option value="{$attriGroupValue.id_attribute_group|escape:'htmlall':'UTF-8'}">{$attriGroupValue['name']|escape:'htmlall':'UTF-8'}</option>
			{/foreach}
		</select>
	</div>
	<div class="col-md-4">
		<label class="control-label">{l s='Value' mod='marketplace'}</label>
		<select class="form-control form-control-select" id="attribute_value_select" name="attribute_value_select">
			<option value="">{l s='Select Value' mod='marketplace'}</option>
		</select>
	</div>
	<div class="col-md-4">
		<button type="button" class="btn btn-primary" id ="wk_add_attribute_button">
			<span><i class="icon-plus-sign-alt"></i> {l s='Add' mod='marketplace'}</span>
		</button>
	</div>
</div>
<div id="attribute_error" class="wk-error-msg"></div>
<div id="selected_combination_list">
	{if isset($selectedAttributeInBox)}
		{foreach $selectedAttributeInBox as $selectedAttributeInBoxVal}
			<p class="wk_each_group" id="wk_each_group_{$selectedAttributeInBoxVal.groupid|escape:'htmlall':'UTF-8'}">
				<span>{$selectedAttributeInBoxVal.name|escape:'htmlall':'UTF-8'}</span>
				<span class="wk_delete_attribute_option" onclick="deleteSelectedAttribute('{$selectedAttributeInBoxVal.groupid|escape:'htmlall':'UTF-8'}')">x</span>
			</p>
		{/foreach}
	{/if}
</div>
<div class="clearfix"></div>
<!-- This portion will be hidden -->
<div class="wk_display_none">
	<select class="form-control form-control-select" id="product_att_list" multiple="multiple" name="attribute_combination_list[]">
	{if isset($selectedAttributeInBox)}
		{foreach $selectedAttributeInBox as $selectedAttributeInBoxVal}
			<option value="{$selectedAttributeInBoxVal.id|escape:'htmlall':'UTF-8'}" id="group_id_{$selectedAttributeInBoxVal.groupid|escape:'htmlall':'UTF-8'}">{$selectedAttributeInBoxVal.name|escape:'htmlall':'UTF-8'}</option>
		{/foreach}
	{/if}
	</select>
</div>
<div class="wk_combination_fields_head">
	{if isset($backendController)}
		<h4>{l s='Set Quantity' mod='marketplace'}</h4>
	{else}
		<h6>{l s='Set Quantity' mod='marketplace'}</h6>
	{/if}
</div>
<div class="form-group row">
	{if Configuration::get('PS_STOCK_MANAGEMENT')}
		<div class="col-md-4">
			<label for="mp_quantity" class="control-label required">
				{l s='Quantity' mod='marketplace'}
			</label>
			<input type="text"
			name="mp_quantity"
			id="mp_quantity"
			value="{if isset($smarty.post.mp_quantity)}{$smarty.post.mp_quantity|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$quantity|escape:'htmlall':'UTF-8'}{else}0{/if}{/if}"
			class="form-control"
			required />
		</div>
	{/if}
    {if Configuration::get('WK_MP_PRODUCT_MIN_QTY') || isset($backendController)}
		<div class="col-md-4">
			<label for="mp_minimal_quantity" class="control-label required">
				{l s='Minimum quantity' mod='marketplace'}
				<div class="wk_tooltip">
					<span class="wk_tooltiptext">{l s='The minimum quantity to buy this product (set to 1 to disable this feature)' mod='marketplace'}</span>
				</div>
			</label>
			<input type="text"
			name="mp_minimal_quantity"
			id="mp_minimal_quantity"
			value="{if isset($smarty.post.mp_minimal_quantity)}{$smarty.post.mp_minimal_quantity|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$productAttribute.minimal_quantity|escape:'htmlall':'UTF-8'}{else}1{/if}{/if}"
			class="form-control"
			pattern="\d*"
			required />
		</div>
	{/if}
	<div class="col-md-4">
		<label for="mp_available_date" class="control-label">
			{l s='Available date' mod='marketplace'}
			<div class="wk_tooltip">
				<span class="wk_tooltiptext">{l s='If this product is out of stock, you can indicate when the product will be available again.' mod='marketplace'}</span>
			</div>
		</label>
		<div class="input-group">
			<input type="text"
			name="mp_available_date"
			id="mp_available_date"
			value="{if isset($smarty.post.mp_available_date)}{$smarty.post.mp_available_date|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$productAttribute.available_date|escape:'htmlall':'UTF-8'}{/if}{/if}"
			class="form-control"
			placeholder="YYYY-MM-DD"
			autocomplete="off" />
			<span class="input-group-addon wk_calender_icon">
				<i class="material-icons">&#xE916;</i>
			</span>
		</div>
	</div>
</div>

{if Configuration::get('WK_MP_PRODUCT_STOCK_LOCATION') || isset($backendController)}
	<div class="form-group row">
		<div class="col-md-4">
			<label for="location" class="control-label">
				{l s='Stock location' mod='marketplace'}
			</label>
			<input type="text"
			class="form-control"
			name="location"
			id="location"
			value="{if isset($smarty.post.location)}{$smarty.post.location|escape:'htmlall':'UTF-8'}{else if isset($productAttribute.location)}{$productAttribute.location|escape:'htmlall':'UTF-8'}{/if}"
			maxlength="255"
			/>
		</div>
	</div>
{/if}

{* Low Stock Level *}
{if Configuration::get('WK_MP_PRODUCT_LOW_STOCK_ALERT') || isset($backendController)}
	<div class="form-group row">
		<div class="col-md-4">
			<label for="low_stock_threshold" class="control-label">
				{l s='Low stock level' mod='marketplace'}
				<div class="wk_tooltip">
					<span class="wk_tooltiptext">{l s='This option enables you to get notification on product low stock.' mod='marketplace'}</span>
				</div>
			</label>
			<input type="text"
			class="form-control"
			name="low_stock_threshold"
			id="low_stock_threshold"
			value="{if isset($smarty.post.low_stock_threshold)}{$smarty.post.low_stock_threshold|escape:'htmlall':'UTF-8'}{else if isset($edit) && $productAttribute.low_stock_threshold != '0'}{$productAttribute.low_stock_threshold|escape:'htmlall':'UTF-8'}{/if}"
			pattern="\d*" />
		</div>
	</div>
	<div class="checkbox">
		<label for="low_stock_alert">
			<input type="checkbox" name="low_stock_alert" id="low_stock_alert" value="1" {if isset($edit) && $productAttribute.low_stock_alert == '1'}checked{/if} />
			<span>{l s='Send me an email when the quantity is below or equals this level' mod='marketplace'}</span>
		</label>
	</div>
{/if}
<div class="wk_combination_fields_head">
	{if isset($backendController)}
		<h4>{l s='Price And Impact' mod='marketplace'}</h4>
	{else}
		<h6>{l s='Price And Impact' mod='marketplace'}</h6>
	{/if}
</div>
<div class="form-group row">
	<div class="col-md-4">
		<label for="mp_price" class="control-label required">
			{l s='Impact on price (tax excl.)' mod='marketplace'}
			<div class="wk_tooltip">
				<span class="wk_tooltiptext">{l s='Does this combination have a different price? Is it cheaper or more expensive than the default retail price?' mod='marketplace'}</span>
			</div>
		</label>
		<div class="input-group">
			<input type="hidden" value="{$mp_product_price|escape:'htmlall':'UTF-8'}" id="mp_product_price">
			<input type="text"
			name="mp_price"
			id="mp_price"
			value="{if isset($smarty.post.mp_price)}{$smarty.post.mp_price|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$productAttribute.price|escape:'htmlall':'UTF-8'}{else}0.000000{/if}{/if}"
			class="form-control"
			pattern="^-?\d+(\.\d+)?"
			autocomplete="off"
			required />
			<span class="input-group-addon">{$def_currency->sign|escape:'htmlall':'UTF-8'}</span>
		</div>
		<span class="form-control-comment">{l s='Use minus(-) for decrease impact' mod='marketplace'}</span>
	</div>
	<div class="col-md-4">
		<label for="mp_weight" class="control-label">
			{l s='Impact on weight' mod='marketplace'}
		</label>
		<div class="input-group">
			<span class="input-group-addon">{$ps_weight_unit|escape:'htmlall':'UTF-8'}</span>
			<input type="text"
			name="mp_weight"
			id="mp_weight"
			value="{if isset($smarty.post.mp_weight)}{$smarty.post.mp_weight|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$productAttribute.weight|escape:'htmlall':'UTF-8'}{else}0{/if}{/if}"
			size="6"
			class="form-control"
			pattern="^-?\d+(\.\d+)?" />
		</div>
	</div>
</div>
<div class="form-group">
	<h4 class="wk_combination_final_price">
		<label>
			{l s='Final product price (tax excl.) will be' mod='marketplace'}
			<span>
				{$def_currency->sign|escape:'htmlall':'UTF-8'}
				<span id="attribute_final_product_price"></span>
			</span>
		</label>
	</h4>
</div>
<div class="form-group row">
	{if Configuration::get('WK_MP_PRODUCT_WHOLESALE_PRICE') || isset($backendController)}
		<div class="col-md-4">
			<label for="mp_wholesale_price" class="control-label">
				{l s='Cost price' mod='marketplace'}
			</label>
			<div class="input-group">
				<input type="text"
				name="mp_wholesale_price"
				id="mp_wholesale_price"
				value="{if isset($smarty.post.mp_wholesale_price)}{$smarty.post.mp_wholesale_price|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$productAttribute.wholesale_price|escape:'htmlall':'UTF-8'}{else}0.000000{/if}{/if}"
				class="form-control"
				pattern="^-?\d+(\.\d+)?" />
				<span class="input-group-addon">{$def_currency->sign|escape:'htmlall':'UTF-8'}</span>
			</div>
		</div>
	{/if}
	{if Configuration::get('WK_MP_PRODUCT_PRICE_PER_UNIT') || isset($backendController)}
		<div class="col-md-4">
			<label for="mp_unit_price_impact" class="control-label">
				{l s='Impact on price per unit (tax excl.)' mod='marketplace'}
				<div class="wk_tooltip">
					<span class="wk_tooltiptext">{l s='Does this combination have a different price per unit?' mod='marketplace'}</span>
				</div>
			</label>
			<div class="input-group">
				<input type="text"
				name="mp_unit_price_impact"
				id="mp_unit_price_impact"
				value="{if isset($smarty.post.mp_unit_price_impact)}{$smarty.post.mp_unit_price_impact|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$productAttribute.unit_price_impact|escape:'htmlall':'UTF-8'}{else}0.000000{/if}{/if}"
				size="6"
				class="form-control"
				pattern="^-?\d+(\.\d+)?" />
				<span class="input-group-addon">{$def_currency->sign|escape:'htmlall':'UTF-8'}</span>
			</div>
		</div>
	{/if}
</div>
{if isset($backendController) || Configuration::get('WK_MP_SELLER_PRODUCT_REFERENCE') || Configuration::get('WK_MP_SELLER_PRODUCT_EAN') || Configuration::get('WK_MP_SELLER_PRODUCT_UPC') || Configuration::get('WK_MP_SELLER_PRODUCT_ISBN') || Configuration::get('WK_MP_PRODUCT_MPN')}
	<div class="wk_combination_fields_head">
		{if isset($backendController)}
			<h4>{l s='Specific References' mod='marketplace'}</h4>
		{else}
			<h6>{l s='Specific References' mod='marketplace'}</h6>
		{/if}
	</div>
{/if}
<div class="form-group row">
	{if isset($backendController) || Configuration::get('WK_MP_SELLER_PRODUCT_REFERENCE')}
		<div class="col-md-4">
			<label for="mp_reference" class="control-label">
				{l s='Reference' mod='marketplace'}
				<div class="wk_tooltip">
					<span class="wk_tooltiptext">{l s='Your internal reference code for this product. Allowed max 32 character. Allowed special characters' mod='marketplace'}:.-_#.</span>
				</div>
			</label>
			<div>
				<input type="text"
				name="mp_reference"
				id="mp_reference"
				value="{if isset($smarty.post.mp_reference)}{$smarty.post.mp_reference|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$productAttribute.reference|escape:'htmlall':'UTF-8'}{/if}{/if}"
				class="form-control"
				maxlength="32" />
			</div>
		</div>
	{/if}
	{if isset($backendController) || Configuration::get('WK_MP_SELLER_PRODUCT_EAN')}
		<div class="col-md-4 form-group">
			<label for="mp_ean13" class="control-label">
				{l s='EAN-13 or JAN barcode' mod='marketplace'}
				<div class="wk_tooltip">
					<span class="wk_tooltiptext">{l s='This type of product code is specific to Europe and Japan, but is widely used internationally. It is a superset of the UPC code: all products marked with an EAN will be accepted in North America.' mod='marketplace'}</span>
				</div>
			</label>
			<div>
				<input type="text"
				name="mp_ean13"
				id="mp_ean13"
				value="{if isset($smarty.post.mp_ean13)}{$smarty.post.mp_ean13|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$productAttribute.ean13|escape:'htmlall':'UTF-8'}{/if}{/if}"
				class="form-control"
				maxlength="13" />
			</div>
		</div>
	{/if}
	{if isset($backendController) || Configuration::get('WK_MP_SELLER_PRODUCT_UPC')}
		<div class="col-md-4">
			<label for="mp_upc" class="control-label">
				{l s='UPC barcode' mod='marketplace'}
				<div class="wk_tooltip">
					<span class="wk_tooltiptext">{l s='Allowed max 12 character. This type of product code is widely used in the United States, Canada, the United Kingdom, Australia, New Zealand and in other countries.' mod='marketplace'}</span>
				</div>
			</label>
			<div>
				<input type="text"
				name="mp_upc"
				id="mp_upc"
				value="{if isset($smarty.post.mp_upc)}{$smarty.post.mp_upc|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$productAttribute.upc|escape:'htmlall':'UTF-8'}{/if}{/if}"
				class="form-control"
				maxlength="12" />
			</div>
		</div>
	{/if}
</div>
<div class="form-group row">
	{if (isset($backendController) || Configuration::get('WK_MP_SELLER_PRODUCT_ISBN')) && !(_PS_VERSION_ < '1.7.7.0')}
		<div class="col-md-4 form-group">
			<label for="mp_isbn" class="control-label">
				{l s='ISBN' mod='marketplace'}
				<div class="wk_tooltip">
					<span class="wk_tooltiptext">{l s='MPN is used internationally to identify the Manufacturer Part Number.' mod='marketplace'}</span>
				</div>
			</label>
			<div>
				<input type="text"
				name="mp_isbn"
				id="mp_isbn"
				value="{if isset($smarty.post.mp_isbn)}{$smarty.post.mp_isbn|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$productAttribute.isbn|escape:'htmlall':'UTF-8'}{/if}{/if}"
				class="form-control"
				maxlength="13" />
			</div>
		</div>
	{/if}
	{if isset($backendController) || Configuration::get('WK_MP_PRODUCT_MPN')}
		<div class="col-md-4">
			<label for="mp_mpn" class="control-label">
				{l s='MPN' mod='marketplace'}
				<div class="wk_tooltip">
					<span class="wk_tooltiptext">{l s='Allowed max 13 character. This type of code is widely used internationally to identify books and their various editions' mod='marketplace'}</span>
				</div>
			</label>
			<div>
				<input type="text"
				name="mp_mpn"
				id="mp_mpn"
				value="{if isset($smarty.post.mp_mpn)}{$smarty.post.mp_mpn|escape:'htmlall':'UTF-8'}{else}{if isset($edit)}{$productAttribute.mpn|escape:'htmlall':'UTF-8'}{/if}{/if}"
				class="form-control"
				maxlength="40"
				/>
			</div>
		</div>
	{/if}
</div>
<div class="wk_combination_fields_head">
	{if isset($backendController)}
		<h4>{l s='Image' mod='marketplace'}</h4>
	{else}
		<h6>{l s='Image' mod='marketplace'}</h6>
	{/if}
</div>
<div class="form-group">
	{if isset($mp_pro_image) && $mp_pro_image}
		<div id="id_image_attr" class="row">
			{if isset($is_ps_product)}
				{foreach from=$mp_pro_image key=k item=image}
					<div class="col-md-2 wk_padding_right_none">
						<div>
							<input type="checkbox" name="id_image_attr[]" value="{$image.id_image|escape:'htmlall':'UTF-8'}" id="id_image_attr_{$image.id_image|escape:'htmlall':'UTF-8'}" class="attri_images" {if isset($ps_attribute_images)}{foreach $ps_attribute_images as $ps_image}{if $image.id_image == $ps_image['id_image']}checked{/if}{/foreach}{/if}/>
						</div>
						<label for="id_image_attr_{$image.id_image|escape:'htmlall':'UTF-8'}">
							<img class="img-thumbnail" width="150" src="{$smarty.const._THEME_PROD_DIR_|escape:'htmlall':'UTF-8'}{$image.obj->getExistingImgPath()|escape:'htmlall':'UTF-8'}-small_default.jpg" alt="{l s='Image' mod='marketplace'}" />
						</label>
					</div>
				{/foreach}
			{else}
				{foreach from=$mp_pro_image key=k item=image}
					<div class="col-md-2 wk_padding_right_none">
						<div>
							<input type="checkbox" name="id_image_attr[]" value="{$image.id_mp_product_image|escape:'htmlall':'UTF-8'}" id="id_image_attr_{$image.id_mp_product_image|escape:'htmlall':'UTF-8'}" class="checkbox-inline attri_images" {if isset($attribute_images)}{foreach $attribute_images as $mp_image}{if $image.id_mp_product_image == $mp_image.id_image}checked{/if}{/foreach}{/if}/>
						</div>
						<label for="id_image_attr_{$image.id_mp_product_image|escape:'htmlall':'UTF-8'}">
							<img class="img-thumbnail" width="150" src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}marketplace/views/img/product_img/{$image.seller_product_image_name|escape:'htmlall':'UTF-8'}" alt="{l s='Image' mod='marketplace'}" />
						</label>
					</div>
				{/foreach}
			{/if}
		</div>
	{else}
		<div class="alert alert-warning">
			{l s='You must upload an image before you can select one for your combination.' mod='marketplace'}
		</div>
	{/if}
</div>