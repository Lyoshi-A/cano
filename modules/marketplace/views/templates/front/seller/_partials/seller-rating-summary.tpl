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

{if isset($sellerRating) && isset($totalReview)}
    <div id="wk_review_rating_container">
        <div class="col-md-4 col-sm-12">
            <div class="wk_seller_rating_box_heading">{l s='Seller Rating' mod='marketplace'}</div>
            <div class="wk_average_seller_rating_box">
                {* Display Rating Star *}
                <div id="seller_rating"></div>
                <div class="seller_rating_data">
                    {$sellerRating|escape:'htmlall':'UTF-8'}
                </div>
                <div class="seller_rating_box_content">
                    {l s='Based On' mod='marketplace'}<br>
                    {$totalReview|escape:'htmlall':'UTF-8'} {if $totalReview > 1}{l s='Reviews' mod='marketplace'}{else}{l s='Review' mod='marketplace'}{/if}
                </div>
            </div>
        </div>
        <div class="col-md-8 col-sm-12 wkseller_rating_table">
            {if $sellerRatingDetail}
                <table class="table">
                    <thead>
                        <tr>
                            <th>{l s='Rating' mod='marketplace'}</th>
                            <th style="width:52%;">{l s='Stats' mod='marketplace'}</th>
                            <th>{l s='Based On' mod='marketplace'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$sellerRatingDetail item=rating key=key name=ratings}
                            <tr>
                                <td>{$rating.rating|escape:'htmlall':'UTF-8'}{l s=' star' mod='marketplace'}</td>
                                <td>
                                    <div class="wk_progress" >
                                        <div class="wk_progress_bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:{$rating.percent|escape:'htmlall':'UTF-8'}%">
                                        </div>
                                    </div>
                                </td>
                                <td>{$rating.count|escape:'htmlall':'UTF-8'} {if $rating.count > 1}{l s='Reviews' mod='marketplace'}{else}{l s='Review' mod='marketplace'}{/if}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            {/if}
        </div>
        <div class="clearfix"></div>
    </div>
{/if}