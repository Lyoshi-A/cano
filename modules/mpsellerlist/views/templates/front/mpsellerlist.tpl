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
{capture name=path}
    {l s='Marketplace SellerList' mod='mpsellerlist'}
{/capture}
<div class="form-group wk_seller_top">
	<h1>{l s='Marketplace' mod='mpsellerlist'}</h1>
	<div class="wk_profiledata">
		<p class="wk_profile_text">{$mp_seller_text nofilter}</p>
	</div>
    {if isset($gotoshop_link)}
    	<p>
    		<a class="btn btn-primary btn-all" href="{$gotoshop_link|escape:'htmlall':'UTF-8'}">{l s='Go To Dashboard' mod='mpsellerlist'}
    		</a>
    	</p>
    {/if}
</div>
<hr>
<div class="container wk_seller_container">
	<div class="row wk_seller_main">
    	<div class="col-lg-12 heading_list">
    		<div class="wk_seller_list" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
            	<h1 class="wk_p_header" style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Seller List' mod='mpsellerlist'}</h1>
            </div>
        </div>
        {if $total_active_seller==0}
            <div class="wk_seller_product_note_found col-sm-12 col-xs-12">
        	    <div>{l s='No shop found' mod='mpsellerlist'}</div>
            </div>
        {else}
    		{foreach $all_active_seller as $act_seller}
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="{$link->getModuleLink('marketplace','shopstore', ['mp_shop_name'=>{$act_seller['link_rewrite']|escape:'htmlall':'UTF-8'}])|escape:'htmlall':'UTF-8'}">
                        <img class="img-responsive" src="{$act_seller['shop_logo']|escape:'htmlall':'UTF-8'}" alt="">
                    </a>
                    <div class="wk_seller_details">
                        <p class="wk_seller_name" title ="{$act_seller['shop_name']|escape:'htmlall':'UTF-8'}">{$act_seller['shop_name']|escape:'htmlall':'UTF-8'|truncate:45:'...'}</p>
                        <a href="{$link->getModuleLink('marketplace','shopstore', ['mp_shop_name'=>{$act_seller['link_rewrite']|escape:'htmlall':'UTF-8'}])|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn_seller_shop">{l s='View Shop' mod='mpsellerlist'}</a>
                    </div>
                </div>
    		{/foreach}
    		{if $total_active_seller>1}
        		<div class="col-lg-12 wk_view_more">
        			<a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn-all">
        				{l s='View all sellers' mod='mpsellerlist'}
        			</a>
        		</div>
            {/if}
		{/if}
    </div>
</div>
<div class="container wk_product_container">
	<div class="row wk_seller_main">
    	<div class="col-lg-12 heading_list">
    		<div class="wk_seller_list" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
            	<h1 class="wk_p_header" style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Latest Products' mod='mpsellerlist'}</h1>
            </div>
        </div>
        {if $active_seller_product==0}
        	<div class="wk_seller_product_note_found col-sm-12 col-xs-12">
                <div>{l s='No product found' mod='mpsellerlist'}</div>
            </div>
        {else}
            {foreach $seller_product_info as $seller_prod}
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="{$link->getProductLink($seller_prod['main_id_product'])|escape:'htmlall':'UTF-8'}">
                    {if $seller_prod['image']}
                        <img class="img-responsive" src="{$link->getImageLink($seller_prod['link_rewrite'], $seller_prod['image'], 'home_default')|escape:'htmlall':'UTF-8'}" title="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}">
                    {else}
                        <img class="img-responsive" src="{$default_product|escape:'htmlall':'UTF-8'}" alt="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" title="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}">
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
    		{/foreach}
    		{if $count_all_active_product > $active_seller_product}
        		<div class="col-sm-12 col-xs-12 wk_view_more">
        			<a href="{$viewmoreproduct_link|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn-all">{l s='View all products' mod='mpsellerlist'}</a>
        		</div>
            {/if}
		{/if}
    </div>
</div>
{/block}
