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

<div class="clearfix modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="col-xs-12 col-sm-7 color3 wktitle">
        {if isset($frontcontroll)}
            <h5>{l s='Order' mod='marketplace'} <strong>{$orderInfo.reference|escape:'htmlall':'UTF-8'}</strong> {l s='from' mod='marketplace'} {$orderInfo.customer_name|escape:'htmlall':'UTF-8'}</h5>
        {else}
            <h4>{l s='Order' mod='marketplace'} <strong>{$orderInfo.reference|escape:'htmlall':'UTF-8'}</strong> {l s='from' mod='marketplace'} {$orderInfo.customer_name|escape:'htmlall':'UTF-8'}</h4>
        {/if}
    </div>
    <div class="col-xs-12 col-sm-4 h4 color3 wk_padding_none">
        <div class="kpi-content" style="padding-left:30px;">
            {if isset($frontcontroll)}
                <i class="material-icons"  style="vertical-align: -5px;">date_range</i>
            {else}
                <i class="icon-calendar-empty" style="font-size: 15px;"></i>
            {/if}
            <span class="">{l s='Date' mod='marketplace'}</span>
            <span class="value">{$orderInfo.date|date_format:"%D"|escape:'htmlall':'UTF-8'}</span>
        </div>
    </div>
</div>
<div class="modal-body">
    <div class="table-responsive">
        <table id="orderProducts" class="table">
            <thead>
                <tr>
                    <th><span class="title_box ">{l s='Product' mod='marketplace'}</span></th>
                    <th><span class="title_box ">{l s='Seller amount' mod='marketplace'}</span></th>
                    <th><span class="title_box ">{l s='Seller tax' mod='marketplace'}</span></th>
                    <th><span class="title_box ">{l s='Admin commission' mod='marketplace'}</span></th>
                    <th><span class="title_box ">{l s='Admin tax' mod='marketplace'}</span></th>
                    <th><span class="title_box ">{l s='Total' mod='marketplace'}</span></th>
                </tr>
            </thead>
            <tbody>
                {if isset($result)}
                    {foreach $result as $data}
                        <tr class="product-line-row">
                            <td>
                                {if isset($data.product_link) && $data.product_link}
                                    <a target="_blank" href="{$data.product_link|escape:'htmlall':'UTF-8'}">
                                        <span class="productName">{$data.product_name|escape:'htmlall':'UTF-8'}</span><br>
                                    </a>
                                {else}
                                    <span class="productName">{$data.product_name|escape:'htmlall':'UTF-8'}</span><br>
                                {/if}
                            </td>
                            <td><span>{$data.seller_amount|escape:'htmlall':'UTF-8'}</span></td>
                            <td>{$data.seller_tax|escape:'htmlall':'UTF-8'}</td>
                            <td>{$data.admin_commission|escape:'htmlall':'UTF-8'}</td>
                            <td>{$data.admin_tax|escape:'htmlall':'UTF-8'}</td>
                            <td>{$data.price_ti|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                    {/foreach}
                {/if}
            </tbody>
        </table>
    </div>
    <div class="clearfix model-footer">
        <a target="_blank" class="btn btn-info pull-right" href="{$orderlink|escape:'htmlall':'UTF-8'}">
            <span>{l s='View order' mod='marketplace'}</span>
        </a>
    </div>
</div>
<style>
.product-line-row {
    height: 35px;
}

#wk_seller_product_line .wktitle h4,
#wk_seller_product_line .wkdate span.title,
#wk_seller_product_line .wkdate span.value {
    text-align: left;
}
</style>