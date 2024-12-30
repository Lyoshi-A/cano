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
<div class="row">
    <div class="col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12">
        <div class="pull-right">
            <div class="dropdown pull-left">
                <button class="btn btn-default dropdown-toggle search_value" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true" data-value="1">
                    <span class="" id="search_for">{l s='Seller name' mod='mpsellerlist'}</span>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdownMenu1_ul" role="menu" aria-labelledby="dropdownMenu1">
                    <li role="presentation"><a class="dropdown-item" role="menuitem" tabindex="-1" href="#" class="search_category" data-value="1">{l s='Seller name' mod='mpsellerlist'}</a></li>
                    <li role="presentation"><a class="dropdown-item" role="menuitem" tabindex="-1" href="#" class="search_category" data-value="2">{l s='Shop name' mod='mpsellerlist'}</a></li>
                    <li role="presentation"><a class="dropdown-item" role="menuitem" tabindex="-1" href="#" class="search_category" data-value="3">{l s='Shop location' mod='mpsellerlist'}</a></li>
                </ul>
            </div>
            <div class="search_container pull-left">
                <label class="input-group pull-left">
                    <input type="text" class="mp_search_box"  placeholder="{l s='Search' mod='mpsellerlist'}" aria-describedby="sizing-addon1" id="seller-search">
                    <span class="input-group-addon" id="mpseller-search"><i class="material-icons">search</i></span>
                </label>
                <ul class="mp_search_sugg"></ul>
            </div>
        </div>
    </div>
</div>
<div class="brand_search">
    <label>{l s='Select seller' mod='mpsellerlist'}:</label>
    <span class="content">
        {if $friendly_url}
            {foreach $wkCapAlphabet as $key=>$value}
                <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}?alp={$key|escape:'htmlall':'UTF-8'}" {if $alph == $key}class="btn btn-default btn_seller_selected"{/if}>{l s=$value mod='mpsellerlist'}</a>
            {/foreach}
        {else}
            {foreach $wkCapAlphabet as $key=>$value}
                <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp={$key|escape:'htmlall':'UTF-8'}" {if $alph == $key}class="btn btn-default btn_seller_selected"{/if}>{l s=$value mod='mpsellerlist'}</a>
            {/foreach}
        {/if}
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn_seller_shop">{l s='All seller' mod='mpsellerlist'}</a>
    </span>
</div>
<div class="container wk_seller_container">
    <div class="row wk_seller_main">
        <div class="col-lg-12 heading_list">
            <div class="wk_seller_list" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
                <h1 class="wk_p_header" style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Seller List' mod='mpsellerlist'}</h1>
            </div>
        </div>
        {if $total_active_seller==0}
        <div class="wk_seller_product_note_found col-sm-12 col-xs-12">
            <h1 style="padding:40px;">{l s='No shop available' mod='mpsellerlist'}</h1>
         </div>
        {else}
            {foreach $all_active_seller as $act_seller}
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="{$link->getModuleLink('marketplace','shopstore', ['mp_shop_name'=>{$act_seller['link_rewrite']|escape:'htmlall':'UTF-8'}])|escape:'htmlall':'UTF-8'}">
                        <img class="img-responsive" src="{$act_seller['shop_logo']|escape:'htmlall':'UTF-8'}" alt="">
                    </a>
                    <div class="wk_seller_details">
                        <p class="wk_seller_name" title="{$act_seller['shop_name']|escape:'htmlall':'UTF-8'}">{$act_seller['shop_name']|escape:'htmlall':'UTF-8'|truncate:30:'...'}</p>
                        <a href="{$link->getModuleLink('marketplace','shopstore', ['mp_shop_name'=>{$act_seller['link_rewrite']|escape:'htmlall':'UTF-8'}])|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn_seller_shop">{l s='View Shop' mod='mpsellerlist'}</a>
                    </div>
                </div>
            {/foreach}
        {/if}
    </div>
</div>
{/block}