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

{extends file=$layout}
{block name='content'}
<div class="form-group">
        <label for="sel1" class="wk_sel_label"><strong>{l s='Sort by' mod='mpsellerlist'}</strong></label>
        <select name="wk_orderby" class="form-control" id="wk_orderby">
            <option>--</option>
            <option {if isset($orderby) && $orderby == 1}selected="selected"{/if} id="1">{l s='Lowest price' mod='mpsellerlist'}</option>
            <option {if isset($orderby) && $orderby == 2}selected="selected"{/if} id="2">{l s='Highest price' mod='mpsellerlist'}</option>
            <option {if isset($orderby) && $orderby == 3}selected="selected"{/if} id="3">{l s='A To Z' mod='mpsellerlist'}</option>
            <option {if isset($orderby) && $orderby == 4}selected="selected"{/if} id="4">{l s='Z To A' mod='mpsellerlist'}</option>
        </select>
</div>
<div class="container wk_product_container">
    <input type="hidden" id="orderby" name="orderby" value="{if isset($orderby)}{$sortby|escape:'htmlall':'UTF-8'}{/if}">
    <input type="hidden" id="orderway" name="orderway" value="{if isset($orderway)}{$orderway|escape:'htmlall':'UTF-8'}{/if}">
    <div class="row wk_seller_main">
        <div class="col-lg-12 heading_list">
            <div class="wk_seller_list" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
                <h1 class="wk_p_header" style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='All products' mod='mpsellerlist'}</h1>
            </div>
        </div>
        {if $active_seller_product==0}
            {l s='No product found' mod='mpsellerlist'}
        {else}
            {assign var=k value=0}
            {foreach $seller_product_info as $seller_prod}
                <div class="col-lg-3 col-md-4 col-xs-6 thumb" id="{$seller_prod['id_mp_product']|escape:'htmlall':'UTF-8'}">
                    <a class="thumbnail" href="{$link->getProductLink($seller_prod['main_id_product'])|escape:'htmlall':'UTF-8'}">
                        {if $seller_prod['image']}
                            <img class="img-responsive" src="{$link->getImageLink($seller_prod['link_rewrite'], $seller_prod['image'], 'home_default')|escape:'htmlall':'UTF-8'}" title="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="">
                        {else}
                            <img class="img-responsive" src="{$default_product|escape:'htmlall':'UTF-8'}" title="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}">
                        {/if}
                    </a>
                    <div class="wk_seller_details">
                        <p class="wk_seller_name">{$seller_prod['product_name']|escape:'htmlall':'UTF-8'|truncate:45:'...'}</p>
                        <div class="mp-product-price">
                            {if $seller_prod.show_price && $showPriceByCustomerGroup}
                                {$seller_prod.price|escape:'htmlall':'UTF-8'}
                                {if $seller_prod.price != $seller_prod.retail_price}
                                    <span class="wk_retail_price">{$seller_prod.retail_price|escape:'htmlall':'UTF-8'}</span>
                                {/if}
                            {/if}
                        </div>
                        <a href="{$link->getProductLink($seller_prod['main_id_product'])|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn_product_shop">{l s='View' mod='mpsellerlist'}</a>
                    </div>
                </div>
                {assign var=k value=$k+1}
            {/foreach}
            {if  $count_all_active_product > $active_seller_product}
                <div class="col-lg-12 wk_view_more" data-count-prod="{$k|escape:'htmlall':'UTF-8'}">
                    <img class="view-more-img" src="{$module_dir|escape:'htmlall':'UTF-8'}/mpsellerlist/views/img/ajax-loader.gif">
                    <a href="" class="btn btn-primary btn-all" id="wk-more-product">{l s='View more products' mod='mpsellerlist'}</a>
                </div>
            {/if}
        {/if}
    </div>
</div>
{/block}
