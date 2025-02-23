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

<div class="alert alert-info">
	{l s='Enter Social Profile User id\'s to be displayed on Seller\'s profile page and shop page (Display of these will depend on the \'Social Profile\' option selected/not selected by seller in \'Permission\' Tab )' mod='marketplace'}
</div>
<div class="form-group row">
	{if Configuration::get('WK_MP_SELLER_FACEBOOK')}
	<div class="col-md-6 wk_social_tabs">
		<label for="facebook_id" class="control-label">{l s='Facebook ID' mod='marketplace'}</label>
		<input class="form-control"
		type="text"
		value="{if isset($smarty.post.facebook_id)}{$smarty.post.facebook_id|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.facebook_id|escape:'htmlall':'UTF-8'}{/if}"
		name="facebook_id"
		id="facebook_id" />
		<p class="wk_social_notify">
			{l s='Enter the ID that will be added at the end of' mod='marketplace'} www.facebook.com/
		</p>
	</div>
	{/if}
	{if Configuration::get('WK_MP_SELLER_TWITTER')}
	<div class="col-md-6 wk_social_tabs">
		<label for="twitter_id" class="control-label">{l s='Twitter ID' mod='marketplace'}</label>
		<input class="form-control"
		type="text"
		value="{if isset($smarty.post.twitter_id)}{$smarty.post.twitter_id|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.twitter_id|escape:'htmlall':'UTF-8'}{/if}"
		name="twitter_id"
		id="twitter_id" />
		<p class="wk_social_notify">
			{l s='Enter the ID that will be added at the end of' mod='marketplace'} www.twitter.com/
		</p>
	</div>
	{/if}
	<div class="col-md-12 wk_padding_none">
	{if Configuration::get('WK_MP_SELLER_YOUTUBE')}
	<div class="col-md-6 wk_social_tabs">
		<label for="youtube_id" class="control-label">{l s='Youtube ID' mod='marketplace'}</label>
		<input class="form-control"
		type="text"
		value="{if isset($smarty.post.youtube_id)}{$smarty.post.youtube_id|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.youtube_id|escape:'htmlall':'UTF-8'}{/if}"
		name="youtube_id"
		id="youtube_id" />
		<p class="wk_social_notify">
			{l s='Enter the ID that will be added at the end of' mod='marketplace'} www.youtube.com/
		</p>
	</div>
	{/if}
	{if Configuration::get('WK_MP_SELLER_INSTAGRAM')}
	<div class="col-md-6 wk_social_tabs">
		<label for="instagram_id" class="control-label">{l s='Instagram ID' mod='marketplace'}</label>
		<input class="form-control"
		type="text"
		value="{if isset($smarty.post.instagram_id)}{$smarty.post.instagram_id|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.instagram_id|escape:'htmlall':'UTF-8'}{/if}"
		name="instagram_id"
		id="instagram_id" />
		<p class="wk_social_notify">
			{l s='Enter the ID that will be added at the end of' mod='marketplace'} www.instagram.com/
		</p>
	</div>
	{/if}
	</div>
</div>