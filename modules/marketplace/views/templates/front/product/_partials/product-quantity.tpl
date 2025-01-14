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

{if isset($hasAttribute)}
    <div class="form-group {if isset($backendController)}row{/if}">
        <div class="{if isset($backendController)}col-lg-6 col-lg-offset-3{/if} alert alert-info">
            {l s='You can manage quantity through combinations.' mod='marketplace'}
        </div>
    </div>
{/if}
<div class="{if empty($backendController)}form-group row{/if}" {if isset($hasAttribute)}style="display:none;"{/if}>
    {if Configuration::get('PS_STOCK_MANAGEMENT')}
        <div class="form-group {if isset($backendController)} row{else}col-md-6{/if}">
            <label for="quantity" class="{if isset($backendController)}col-lg-3{/if} control-label required">
                {l s='Quantity' mod='marketplace'}
                <div class="wk_tooltip">
                    <span class="wk_tooltiptext">{l s='How many products should be available for sale?' mod='marketplace'}</span>
                </div>
            </label>
            <div {if isset($backendController)}class="col-lg-6"{/if}>
                <input type="text"
                class="form-control"
                name="quantity"
                id="quantity"
                value="{if isset($smarty.post.quantity)}{$smarty.post.quantity|escape:'htmlall':'UTF-8'}{else if isset($product_info.quantity)}{$product_info.quantity|escape:'htmlall':'UTF-8'}{else}0{/if}"
                {if isset($hasAttribute)}readonly{/if} />
            </div>
        </div>
    {/if}
    {if Configuration::get('WK_MP_PRODUCT_MIN_QTY') || isset($backendController)}
        <div class="{if isset($backendController)}form-group row{else}col-md-6{/if}">
            <label for="minimal_quantity" class="{if isset($backendController)}col-lg-3{/if} control-label required">
                {l s='Minimum quantity for sale' mod='marketplace'}
                <div class="wk_tooltip">
                    <span class="wk_tooltiptext">{l s='The minimum quantity to buy this product (set to 1 to disable this feature)' mod='marketplace'}</span>
                </div>
            </label>
            <div {if isset($backendController)}class="col-lg-6"{/if}>
                <input type="text"
                class="form-control"
                name="minimal_quantity"
                id="minimal_quantity"
                value="{if isset($smarty.post.minimal_quantity)}{$smarty.post.minimal_quantity|escape:'htmlall':'UTF-8'}{else if isset($product_info.minimal_quantity)}{$product_info.minimal_quantity|escape:'htmlall':'UTF-8'}{else}1{/if}"
                {if isset($hasAttribute)}readonly{/if} />
            </div>
        </div>
    {/if}
</div>

{if Configuration::get('WK_MP_PRODUCT_STOCK_LOCATION') || isset($backendController)}
    <div class="form-group" {if isset($hasAttribute)}style="display:none;"{/if}>
        <div class="{if empty($backendController)}row{/if}">
            <div {if empty($backendController)}class="col-md-6"{/if}>
                <label for="location" class="{if isset($backendController)}col-lg-3{/if} control-label">
                    {l s='Stock location' mod='marketplace'}
                </label>
                <div {if isset($backendController)}class="col-lg-6"{/if}>
                    <input type="text"
                    class="form-control"
                    name="location"
                    id="location"
                    value="{if isset($smarty.post.location)}{$smarty.post.location|escape:'htmlall':'UTF-8'}{else if isset($product_info.location)}{$product_info.location|escape:'htmlall':'UTF-8'}{/if}"
                    {if isset($hasAttribute)}readonly{/if} maxlength="255" />
                </div>
            </div>
        </div>
    </div>
{/if}

{if Configuration::get('WK_MP_PRODUCT_LOW_STOCK_ALERT') || isset($backendController)}
    <div class="form-group" {if isset($hasAttribute)}style="display:none;"{/if}>
        <div class="{if empty($backendController)}row{/if}">
            <div {if empty($backendController)}class="col-md-6"{/if}>
                <div {if empty($backendController)}class="form-group"{/if}>
                    <label for="low_stock_threshold" class="{if isset($backendController)}col-lg-3{/if} control-label">
                        {l s='Low stock level' mod='marketplace'}
                        <div class="wk_tooltip">
                            <span class="wk_tooltiptext">{l s='This option enables you to get notification on product low stock.' mod='marketplace'}</span>
                        </div>
                    </label>
                    <div {if isset($backendController)}class="col-lg-6"{/if}>
                        <input type="text"
                        class="form-control"
                        name="low_stock_threshold"
                        id="low_stock_threshold"
                        value="{if isset($smarty.post.low_stock_threshold)}{$smarty.post.low_stock_threshold|escape:'htmlall':'UTF-8'}{else if isset($product_info.low_stock_threshold) && $product_info.low_stock_threshold != '0'}{$product_info.low_stock_threshold|escape:'htmlall':'UTF-8'}{/if}"
                        pattern="\d*" />
                    </div>
                </div>
            </div>
        </div>
        <div class="{if isset($backendController)}col-lg-6 col-lg-offset-3{/if}">
            <div class="checkbox">
                <label for="low_stock_alert">
                    <input type="checkbox" name="low_stock_alert" id="low_stock_alert" value="1" {if isset($product_info.low_stock_alert) && $product_info.low_stock_alert == '1'}checked{/if} />
                    <span>{l s='Send me an email when the quantity is below or equals this level' mod='marketplace'}</span>
                </label>
            </div>
        </div>
    </div>
{/if}