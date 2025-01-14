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

{if isset($currenct_cust_review)}
<div class="box-account">
	<div class="box-content">
		<div class="row">
			<div class="col-md-6 text-uppercase wk_text_left wk-review-heading">
				{l s='Your Review' mod='marketplace'}
			</div>
			<div class="col-md-6 wk-review-btns">
				<a class="wk_anchor_links mp-delete-review" href="{$link->getModuleLink('marketplace', 'sellerprofile', ['delete_review' => 1, 'review_id' => $currenct_cust_review.id_review, 'mp_shop_name' => $shop_link_rewrite])|escape:'htmlall':'UTF-8'}" style="margin-left:5px;">
					<span>{l s='Delete' mod='marketplace'}</span>
				</a>
				<a class="wk_anchor_links" href="#" data-toggle="modal" data-target="#wk_review_model">
					<span>{l s='Edit' mod='marketplace'}</span>
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				{assign var=i value=0}
				{while $i != $currenct_cust_review.rating}
					<img src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}/marketplace/views/img/star-on.png" />
				{assign var=i value=$i+1}
				{/while}

			  	{assign var=k value=0}
			  	{assign var=j value=5-$currenct_cust_review.rating}
			  	{while $k!=$j}
			   		<img src="{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}/marketplace/views/img/star-off.png" />
			  	{assign var=k value=$k+1}
			 	{/while}
			</div>
			<div class="col-md-6 wk_text_right">
				<p>{l s='Review date:' mod='marketplace'} {dateFormat date=$currenct_cust_review.date_add full=1}</p>
				<p>{l s='Last update date:' mod='marketplace'} {dateFormat date=$currenct_cust_review.date_upd full=1}</p>
				<p>
					{l s='Status:' mod='marketplace'}
					{if $currenct_cust_review.active == 1}
						{l s='Approved' mod='marketplace'}
					{else}
						{l s='Pending' mod='marketplace'}
					{/if}
				</p>
			</div>
		</div>
		{if !empty($currenct_cust_review.review)}
			<div class="wk_review_content">
				{$currenct_cust_review.review|escape:'htmlall':'UTF-8'}
			</div>
		{/if}
	</div>
	<div class="wk_border_line"></div>
</div>
{/if}
<div class="box-account">
	<div class="box-content">
		<div class="row">
			<div class="col-md-6 text-uppercase wk_text_left wk-review-heading">
				{l s='Reviews' mod='marketplace'}
			</div>
			{if !isset($currenct_cust_review) AND ($smarty.get.mp_shop_name != $login_mp_shop_name)}
				<div class="col-md-6 wk_write_review">
					<a class="wk_anchor_links {if $logged}forloginuser{/if}" href="#" data-toggle="modal" data-target="#wk_review_model">
						<span>{l s='Write a Review' mod='marketplace'}</span>
					</a>
				</div>
			{/if}
		</div>
		<div class="wk-profile-page-seller-rating">
			{block name='mp-seller-rating-summary'}
				{include file='module:marketplace/views/templates/front/seller/_partials/seller-rating-summary.tpl'}
			{/block}
		</div>
		{if !empty($reviews)}
			{foreach from=$reviews item=review}
				{block name='mp-seller-review-list'}
					{include file='module:marketplace/views/templates/front/seller/_partials/seller-review-list.tpl'}
				{/block}
			{/foreach}
			{*If number of reviews are greater than configuration value then view all button will be shown*}
			{if isset($count_all_reviews) && $count_all_reviews}
				{if Configuration::get('WK_MP_REVIEW_DISPLAY_COUNT')}
					{assign var=wkReviewDisplayCount value=Configuration::get('WK_MP_REVIEW_DISPLAY_COUNT')}
				{else}
					{assign var=wkReviewDisplayCount value=2}
				{/if}
				{if $count_all_reviews > $wkReviewDisplayCount}
					<div style="text-align:right;">
						<a class="wk_anchor_links" href="{$link->getModuleLink('marketplace', 'allreviews', ['mp_shop_name' => $shop_link_rewrite])|escape:'htmlall':'UTF-8'}">
							<span>{l s='View all' mod='marketplace'}</span>
						</a>
					</div>
				{/if}
			{/if}
		{else}
			<p class="alert alert-info">{l s='No reviews found' mod='marketplace'}</p>
		{/if}
	</div>
</div>