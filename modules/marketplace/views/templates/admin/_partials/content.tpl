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

{if $content == 1}
    <span class="tc_cont">
        {l s='By sending the seller request you agree to abide by all the terms and conditions laid by us.' mod='marketplace'}
    </span>
{elseif $content == 2 || $content == 3 || $content == 4}
    <p class="ftr_heading">{l s='Features' mod='marketplace'}</p>
    <p class="ftr_desc">{l s='Doing Business With Us Is Really Easy' mod='marketplace'}</p>
    {if $content == 2}
        <div class="row ftr_detail">
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-12">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme1-icon1.png" alt="" width="155" height="155" />
                    </div>
                    <div class="col-sm-12">
                        <p class="ftr_subhead">{l s='Register Online as a Seller' mod='marketplace'}</p>
                        <p class="ftr_subdesc">{l s='Just fill the registration form and create your own online shop. Start selling' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-12">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme1-icon2.png" alt="" width="155" height="155" />
                    </div>
                    <div class="col-sm-12">
                        <p class="ftr_subhead">{l s='Add your products' mod='marketplace'}</p>
                        <p class="ftr_subdesc">{l s='Upload your products with images and have your own attractive collection page' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-12">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme1-icon3.png" alt="" width="155" height="155" />
                    </div>
                    <div class="col-sm-12">
                        <p class="ftr_subhead">{l s='Process the Orders' mod='marketplace'}</p>
                        <p class="ftr_subdesc">{l s='Timely order processing will help you gain more customers' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-12">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme1-icon4.png" alt="" width="155" height="155" />
                    </div>
                    <div class="col-sm-12">
                        <p class="ftr_subhead">{l s='Start Earning BIG' mod='marketplace'}</p>
                        <p class="ftr_subdesc">{l s='Grow big and earn big by selling with us' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
        </div>
    {elseif $content == 3}
        <div class="row ftr_detail">
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme2-icon1.png" alt="" width="36" height="53" />
                    </div>
                    <div class="col-sm-10">
                        <p class="ftr_subhead">{l s='Register Online as a Seller' mod='marketplace'}</p>
                        <p class="ftr_subdesc">{l s='Just fill the registration form and create your own online shop. Start selling' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme2-icon2.png" alt="" width="44" height="40" />
                    </div>
                    <div class="col-sm-10">
                        <p class="ftr_subhead">Add your products</p>
                        <p class="ftr_subdesc">{l s='Upload your products with images and have your own attractive collection page' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme2-icon3.png" alt="" width="59" height="54" />
                    </div>
                    <div class="col-sm-10">
                        <p class="ftr_subhead">{l s='Process the Orders' mod='marketplace'}</p>
                        <p class="ftr_subdesc">{l s='Timely order processing will help you gain more customers' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme2-icon4.png" alt="" width="53" height="53" />
                    </div>
                    <div class="col-sm-10">
                        <p class="ftr_subhead">{l s='Start Earning BIG' mod='marketplace'}</p>
                        <p class="ftr_subdesc">{l s='Grow big and earn big by selling with us' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
        </div>
    {elseif $content == 4}
        <div class="row ftr_detail">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-1">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme2-icon1.png" alt="" width="36" height="53" />
                    </div>
                    <div class="col-sm-11">
                        <p class="ftr_subhead">{l s='Register Online as a Seller' mod='marketplace'}</p>
                        <p class="ftr_subdesc">{l s='Become a seller with us by following these simple steps. Just fill the registration form and create your own online shop. Start selling by creating your own shop.' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-1">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme2-icon2.png" alt="" width="44" height="40" />
                    </div>
                    <div class="col-sm-11">
                        <p class="ftr_subhead">{l s='Add your products' mod='marketplace'}</p>
                        <p class="ftr_subdesc">{l s='Once the seller request is accepted, upload your products with images and have your own attractive collection page. You can also use the url of the shop collection page at various places to showcase your shop.' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-1">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme2-icon3.png" alt="" width="59" height="54" />
                    </div>
                    <div class="col-sm-11">
                        <p class="ftr_subhead">{l s='Process the Orders' mod='marketplace'}</p>
                        <p class="ftr_subdesc">{l s='You need to process your orders and update the status of all the orders being processed. Timely order processing will help you gain more customers' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-1">
                        <img src="{$wk_ps_img|escape:'htmlall':'UTF-8'}cms/wktheme2-icon4.png" alt="" width="53" height="53" />
                    </div>
                    <div class="col-sm-11">
                        <p class="ftr_subhead">{l s='Start Earning BIG' mod='marketplace'}</p>
                        <p class="ftr_subdesc">{l s='By becoming a seller on any marketplace, you will gain more customers as people have trust in the marketplaces. Grow big and earn big by selling with us' mod='marketplace'}</p>
                    </div>
                </div>
            </div>
        </div>
    {/if}
{/if}
