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

<div class="form-group row">
	<div class="col-md-6">
		<div class="form-group">
			<button type="button" class="btn btn-info wk_uploader_margin" id="uploadprofileimg">{l s='Upload profile image' mod='marketplace'}</button>
			<div id="profileuploader" class="wk_uploader_wholediv">
				<div class="col-md-11 wk_padding_none">
					<input type="file" name="sellerprofileimage[]" class="uploadimg_container" data-jfiler-name="seller_img">
			    </div>
				<div class="clearfix"></div>
				<label class="wk_formfield_required_notify">{l s='Recommended Dimension: 200 x 200 pixels' mod='marketplace'}</label>
		    </div>
			<div class="jFiler-items-seller_img {if isset($seller_img_path)}wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<img src="{if isset($seller_img_path)}{$seller_img_path|escape:'htmlall':'UTF-8'}?timestamp={$timestamp|escape:'htmlall':'UTF-8'}{else}{$seller_default_img_path|escape:'htmlall':'UTF-8'}?timestamp={$timestamp|escape:'htmlall':'UTF-8'}{/if}" alt="{if isset($seller_img_path)}{l s='Seller Profile Image' mod='marketplace'}{else}{l s='Default Image' mod='marketplace'}{/if}"/>
								{if isset($seller_img_path)}
								<div class="wk_text_right">
									<a class="icon-jfi-trash wk_delete_img" data-id_seller="{$mp_seller_info.id_seller|escape:'htmlall':'UTF-8'}" data-imgtype="seller_img" data-uploaded="1" title="{l s='Delete' mod='marketplace'}"></a>
								</div>
								{/if}
							</div>
						</div>
					</li>
					<div class="clearfix"></div>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<button type="button" class="btn btn-info wk_uploader_margin" id="uploadshoplogo">{l s='Upload shop logo' mod='marketplace'}</button>
			<div id="shopuploader" class="wk_uploader_wholediv">
				<div class="col-md-11 wk_padding_none">
					<input type="file" name="shopimage[]" class="uploadimg_container" data-jfiler-name="shop_img">
			    </div>
				<div class="clearfix"></div>
				<label class="wk_formfield_required_notify">{l s='Recommended Dimension: 200 x 200 pixels' mod='marketplace'}</label>
		    </div>
			<div class="jFiler-items-shop_img {if isset($shop_img_path)}wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<img src="{if isset($shop_img_path)}{$shop_img_path|escape:'htmlall':'UTF-8'}?timestamp={$timestamp|escape:'htmlall':'UTF-8'}{else}{$shop_default_img_path|escape:'htmlall':'UTF-8'}?timestamp={$timestamp|escape:'htmlall':'UTF-8'}{/if}" alt="{if isset($shop_img_path)}{l s='Shop Logo' mod='marketplace'}{else}{l s='Default Image' mod='marketplace'}{/if}"/>
								{if isset($shop_img_path)}
								<div class="wk_text_right">
									<a class="icon-jfi-trash wk_delete_img" data-id_seller="{$mp_seller_info.id_seller|escape:'htmlall':'UTF-8'}" data-imgtype="shop_img" data-uploaded="1" title="{l s='Delete' mod='marketplace'}"></a>
								</div>
								{/if}
							</div>
						</div>
					</li>
					<div class="clearfix"></div>
				</ul>
			</div>
		</div>
	</div>
</div>

<h2 class="text-uppercase" style="border-bottom: 1px solid #d5d5d5;padding-bottom: 11px;">
	{l s='Banner image' mod='marketplace'}
</h2>

<div class="form-group row">
	<!-- Seller Profile Page Banner -->
	<div class="col-md-6">
		<div class="form-group">
			<button type="button" class="btn btn-info wk_uploader_margin" id="uploadsellerbanner">{l s='Upload profile banner' mod='marketplace'}</button>
			<div id="profilebanneruploader" class="wk_uploader_wholediv">
				<div class="col-md-11 wk_padding_none">
					<input type="file" name="profilebannerimage[]" class="uploadimg_container" data-jfiler-name="seller_banner">
			    </div>
				<div class="clearfix"></div>
				<label class="wk_formfield_required_notify">{l s='Recommended Dimension: 1140 x 285 pixels' mod='marketplace'}</label>
		    </div>
			<div class="jFiler-items-seller_banner {if isset($seller_banner_path)}wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<img width="225" src="{if isset($seller_banner_path)}{$seller_banner_path|escape:'htmlall':'UTF-8'}?timestamp={$timestamp|escape:'htmlall':'UTF-8'}{else}{$no_image_path|escape:'htmlall':'UTF-8'}{/if}" alt="{if isset($seller_banner_path)}{l s='Seller Profile Banner' mod='marketplace'}{else}{l s='No Image' mod='marketplace'}{/if}"/>
								{if isset($seller_banner_path)}
								<div class="wk_text_right">
									<a class="icon-jfi-trash wk_delete_img" data-id_seller="{$mp_seller_info.id_seller|escape:'htmlall':'UTF-8'}" data-imgtype="seller_banner" data-uploaded="1" title="{l s='Delete' mod='marketplace'}"></a>
								</div>
								{/if}
							</div>
						</div>
					</li>
					<div class="clearfix"></div>
				</ul>
			</div>
		</div>
	</div>

	<!-- Shop Store Page Banner -->
	<div class="col-md-6">
		<div class="form-group">
			<button type="button" class="btn btn-info wk_uploader_margin" id="uploadshopbanner">{l s='Upload shop banner' mod='marketplace'}</button>
			<div id="shopbanneruploader" class="wk_uploader_wholediv">
				<div class="col-md-11 wk_padding_none">
					<input type="file" name="shopbannerimage[]" class="uploadimg_container" data-jfiler-name="shop_banner">
			    </div>
				<div class="clearfix"></div>
				<label class="wk_formfield_required_notify">{l s='Recommended Dimension: 1140 x 285 pixels' mod='marketplace'}</label>
		    </div>
			<div class="jFiler-items-shop_banner {if isset($shop_banner_path)}wk_hover_img{/if}">
				<ul class="jFiler-items-list jFiler-items-grid" style="padding:0px;">
					<li class="jFiler-item">
						<div class="jFiler-item-container">
							<div class="jFiler-item-inner">
								<img width="225" src="{if isset($shop_banner_path)}{$shop_banner_path|escape:'htmlall':'UTF-8'}?timestamp={$timestamp|escape:'htmlall':'UTF-8'}{else}{$no_image_path|escape:'htmlall':'UTF-8'}{/if}" alt="{if isset($shop_banner_path)}{l s='Shop Logo' mod='marketplace'}{else}{l s='No Image' mod='marketplace'}{/if}"/>
								{if isset($shop_banner_path)}
								<div class="wk_text_right">
									<a class="icon-jfi-trash wk_delete_img" data-id_seller="{$mp_seller_info.id_seller|escape:'htmlall':'UTF-8'}" data-imgtype="shop_banner" data-uploaded="1" title="{l s='Delete' mod='marketplace'}"></a>
								</div>
								{/if}
							</div>
						</div>
					</li>
					<div class="clearfix"></div>
				</ul>
			</div>
		</div>
	</div>
</div>