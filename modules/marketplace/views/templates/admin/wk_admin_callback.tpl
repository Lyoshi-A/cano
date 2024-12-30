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
{if $callback == 'checkTransactionType' && isset($success, $amount)}
    {if $success}
        <span class="badge badge-success">{$amount|escape:'htmlall':'UTF-8'}{l s='(Cr)' mod='marketplace'}</span>
    {else}
        <span class="badge badge-danger">{$amount|escape:'htmlall':'UTF-8'}{l s='(Dr)' mod='marketplace'}</span>
    {/if}
{else if $callback == 'viewSettlementBtn'}
    {if isset($currentIndex, $token, $table, $sellerCustomerId)}
        <span class="btn-group-action">
            <span class="btn-group">
                <a class="btn btn-default"
                    href="{$currentIndex|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}&view{$table|escape:'htmlall':'UTF-8'}&mp_seller_transaction=1&id_customer_seller={$sellerCustomerId|escape:'htmlall':'UTF-8'}"><i
                        class="icon-search-plus"></i>{l s='View transaction' mod='marketplace'}</a>
            </span>
        </span>
    {/if}
{else if $callback == 'displayProductImage' || $callback == 'displayBrandImage' || $callback == 'displaySupplierImage' || $callback == 'displayCarrierImage'}
    {if isset($image_link)}
        <img class="img-thumbnail" width="45" height="45" src="{$image_link|escape:'htmlall':'UTF-8'}">
    {/if}
{else if $callback == 'previewProduct'}
    {if isset($productLink)}
        <span class="btn-group-action"><span class="btn-group">
                <a target="_blank" class="btn btn-default" href="{$productLink|escape:'htmlall':'UTF-8'}">
                    <i class="icon-eye"></i>{l s='Preview' mod='marketplace'}</a>
            </span>
        </span>
    {/if}
{else if $callback == 'previewProfile'}
    {if isset($sellerProfileLink)}
        <span class="btn-group-action"><span class="btn-group">
                <a target="_blank" class="btn btn-default" href="{$sellerProfileLink|escape:'htmlall':'UTF-8'}">
                    <i class="icon-eye"></i>{l s='Preview' mod='marketplace'}</a>
            </span>
        </span>
    {/if}
{else if $callback == 'viewDetailBtn'}
    {if isset($sellerOrderCurrentIndex) && isset($sellerOrderToken) && isset($sellerCustomerId)}
        <span class="btn-group-action">
            <span class="btn-group">
                <a class="btn btn-default"
                    href="{$sellerOrderCurrentIndex|escape:'htmlall':'UTF-8'}&token={$sellerOrderToken|escape:'htmlall':'UTF-8'}&viewwk_mp_seller_order&mp_seller_details=1&id_customer_seller={$sellerCustomerId|escape:'htmlall':'UTF-8'}"><i
                        class="icon-search-plus"></i>{l s='View orders' mod='marketplace'}
                </a>
            </span>
        </span>
    {/if}
{else if $callback == 'viewSellerShippingBtn'}
    {if isset($sellerOrderCurrentIndex) && isset($sellerOrderToken) && isset($sellerCustomerId)}
        <span class="btn-group-action">
            <span class="btn-group">
                <a class="btn btn-default"
                    href="{$sellerOrderCurrentIndex|escape:'htmlall':'UTF-8'}&token={$sellerOrderToken|escape:'htmlall':'UTF-8'}&viewwk_mp_seller_order&mp_seller_details=1&id_customer_seller=&viewwk_mp_seller_order&mp_shipping_detail=1&seller_id_customer={$sellerCustomerId|escape:'htmlall':'UTF-8'}"><i
                        class="icon-search-plus"></i>{l s='View shipping' mod='marketplace'}
                </a>
            </span>
        </span>
    {/if}
{else if $callback == 'viewOrderDetail'}
    {if isset($orderLink)}
        <span class="btn-group-action">
            <span class="btn-group">
                <a class="btn btn-default" href="{$orderLink|escape:'htmlall':'UTF-8'}"><i class="icon-search-plus"></i>
                    {l s='View order detail' mod='marketplace'}
                </a>
            </span>
        </span>
    {/if}
{else if $callback == 'assignImpact'}
    {if isset($sellerShippingCurrentIndex) && isset($sellerShippingToken) && isset($mpShippingId)}
        <a class="edit btn btn-default" title="{l s='View' mod='marketplace'}"
            href="{$sellerShippingCurrentIndex|escape:'htmlall':'UTF-8'}&id_wk_mp_shipping={$mpShippingId|escape:'htmlall':'UTF-8'}&updatewk_mp_seller_shipping&updateimpact=1&token={$sellerShippingToken|escape:'htmlall':'UTF-8'}"><i
                class="icon-search-plus"></i>{l s='View' mod='marketplace'}</a>
    {/if}
{else if $callback == 'displayDuplicateLink'}
    {if isset($adminSellerProductLink)}
        <li><a href="{$adminSellerProductLink|escape:'htmlall':'UTF-8'}"><i
                    class="icon-copy"></i>{l s='Duplicate' mod='marketplace'}</a></li>
    {/if}
{/if}