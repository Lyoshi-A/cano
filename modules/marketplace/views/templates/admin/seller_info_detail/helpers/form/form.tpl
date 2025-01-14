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

<div class="panel">
	<div class="panel-heading">
		<i class="icon-user"></i>
		{if isset($edit)}
			{l s='Edit seller' mod='marketplace'}
		{else}
			{l s='Add new seller' mod='marketplace'}
		{/if}
	</div>
	<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal"
		action="{if isset($edit)}{$current|escape:'htmlall':'UTF-8'}&update{$table|escape:'htmlall':'UTF-8'}&id_seller={$mp_seller_info.id_seller|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}{else}{$current|escape:'htmlall':'UTF-8'}&add{$table|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}{/if}"
		method="post" enctype="multipart/form-data">
		<input type="hidden" name="current_lang_id" id="current_lang_id" value="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}">
		<div class="form-group">
			{if !isset($edit)}
				<label class="col-lg-2 control-label required">{l s='Choose customer' mod='marketplace'}</label>
				<div class="col-lg-4">
					{if isset($customer_info)}
						<select name="shop_customer" class="fixed-width-xl">
							{foreach $customer_info as $cusinfo}
								<option value="{$cusinfo.id_customer|escape:'htmlall':'UTF-8'}" {if isset($smarty.post.shop_customer)}
										{if $smarty.post.shop_customer == $cusinfo.id_customer}Selected="Selected" {/if} {/if}>
										{$cusinfo.email|escape:'htmlall':'UTF-8'}
										{if isset($all_shop) && $all_shop && isset($cusinfo.ps_shop_name)}
											({$cusinfo.ps_shop_name|escape:'htmlall':'UTF-8'})
										{/if}
									</option>
								{/foreach}
							</select>
						{else}
							<p class="alert alert-danger">
								{l s='There is no customer found on your shop to add as a Marketplace seller. You can add only registered customer as a marketplace seller' mod='marketplace'}
							</p>
						{/if}
					</div>
				{/if}
				{if $allow_multilang && $total_languages > 1}
					<div class="col-lg-6">
						<label class="control-label">{l s='Choose language' mod='marketplace'}</label>
						<button type="button" id="seller_lang_btn" class="btn btn-default dropdown-toggle wk_language_toggle"
							data-toggle="dropdown">
							{$current_lang.name|escape:'htmlall':'UTF-8'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu wk_language_menu" style="left:14%;top:32px;">
							{foreach from=$languages item=language}
								<li>
									<a href="javascript:void(0)"
										onclick="showSellerLangField('{$language.name|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'});">
										{$language.name|escape:'htmlall':'UTF-8'}
									</a>
								</li>
							{/foreach}
						</ul>
						<p class="help-block">
							{l s='Change language for updating information in multiple language.' mod='marketplace'}</p>
					</div>
				{/if}
				{if isset($edit)}
					<div class="col-lg-6">
						<input type="hidden" value="{$mp_seller_info.id_seller|escape:'htmlall':'UTF-8'}" name="mp_seller_id" id="mp_seller_id" />
						<input type="hidden" value="{$mp_seller_info.shop_name_unique|escape:'htmlall':'UTF-8'}" name="pre_shop_name_unique" />
					</div>
				{/if}
				<input type="hidden" name="active_tab" value="{if isset($active_tab)}{$active_tab|escape:'htmlall':'UTF-8'}{/if}" id="active_tab">
			</div>
			<div class="alert alert-danger wk_display_none" id="wk_mp_form_error"></div>
			<hr>
			<div class="tabs wk-tabs-panel">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#wk-information" data-toggle="tab">
							<i class="icon-info-sign"></i>
							{l s='Information' mod='marketplace'}
						</a>
					</li>
					<li>
						<a href="#wk-contact" data-toggle="tab">
							<i class="icon-envelope"></i>
							{l s='Address' mod='marketplace'}
						</a>
					</li>
					<li>
						<a href="#wk-images" data-toggle="tab">
							<i class="icon-image"></i>
							{l s='Images' mod='marketplace'}
						</a>
					</li>
					<li>
						<a href="#wk-social" data-toggle="tab">
							<i class="icon-user"></i>
							{l s='Social' mod='marketplace'}
						</a>
					</li>
					<li>
						<a href="#wk-permission" data-toggle="tab">
							<i class="icon-ok-circle"></i>
							{l s='Permission' mod='marketplace'}
						</a>
					</li>
					<li>
						<a href="#wk-seller-payment-details" data-toggle="tab">
							<i class="icon-credit-card"></i>
							{l s='Payment details' mod='marketplace'}
						</a>
					</li>
					{if Configuration::get('WK_MP_PRODUCT_CATEGORY_RESTRICTION')}
						<li>
							<a href="#wk-seller-category" data-toggle="tab">
								<i class="icon-ok-circle"></i>
								{l s='Category access' mod='marketplace'}
							</a>
						</li>
					{/if}
					{hook h='displayMpEditProfileTab'}
				</ul>
				<div class="tab-content panel collapse in" id="tab-content">
					<div class="tab-pane fade in active" id="wk-information">
						{if $total_languages > 1}
							<div class="form-group">
								<label class="col-lg-3 control-label required">
									{l s='Default language' mod='marketplace'}
								</label>
								<div class="col-lg-4">
									<select class="form-control fixed-width-xl" name="default_lang" id="default_lang">
										{foreach from=$languages item=language}
											{if $language.active}
												{if isset($edit)}
													{if $allow_multilang}
														<option data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}" value="{$language.id_lang|escape:'htmlall':'UTF-8'}"
															{if $current_lang.id_lang == $language.id_lang}Selected="Selected" {/if}>
															{$language.name|escape:'htmlall':'UTF-8'}
														</option>
													{else}
														{if $mp_seller_info.default_lang == $language.id_lang}
															<option data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}" value="{$language.id_lang|escape:'htmlall':'UTF-8'}">
																{$language.name|escape:'htmlall':'UTF-8'}
															</option>
														{/if}
													{/if}
												{else}
													<option data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}" value="{$language.id_lang|escape:'htmlall':'UTF-8'}"
														{if isset($smarty.post.default_lang)}
															{if $smarty.post.default_lang == $language.id_lang}Selected="Selected" {/if} {else}
															{if $current_lang.id_lang == $language.id_lang}Selected="Selected" {/if} {/if}>
															{$language.name|escape:'htmlall':'UTF-8'}
														</option>
													{/if}
												{/if}
											{/foreach}
										</select>
										{if isset($edit) && !$allow_multilang}
											<p class="help-block">{l s='You can\'t change default language.' mod='marketplace'}</p>
										{/if}
									</div>
								</div>
							{else}
								<input type="hidden" name="default_lang"
									value="{if isset($edit)}{$mp_seller_info.default_lang|escape:'htmlall':'UTF-8'}{else}{$context_language|escape:'htmlall':'UTF-8'}{/if}" />
							{/if}
							<div class="form-group seller_shop_name_uniq">
								<label class="col-lg-3 control-label required">
									{l s='Shop unique name' mod='marketplace'}
									<div class="wk_tooltip">
										<span
											class="wk_tooltiptext">{l s='This name will be used in your shop URL.' mod='marketplace'}</span>
									</div>
								</label>
								<div class="col-lg-6">
									<input class="form-control wk_text_field" type="text"
										value="{if isset($edit)}{if isset($smarty.post.shop_name_unique)}{$smarty.post.shop_name_unique|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.shop_name_unique|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.shop_name_unique)}{$smarty.post.shop_name_unique|escape:'htmlall':'UTF-8'}{/if}{/if}"
										id="shop_name_unique" name="shop_name_unique" onblur="onblurCheckUniqueshop();"
										autocomplete="off" />
									<p class="help-block wk-msg-shopnameunique" style="color:#8F0000;"></p>
								</div>
							</div>
							<div class="form-group seller_shop_name">
								<label class="col-lg-3 control-label required">
									{l s='Shop name' mod='marketplace'}
									{include file="$wkself/../../views/templates/front/_partials/mp-form-fields-flag.tpl"}
								</label>
								<div class="col-lg-6">
									{foreach from=$languages item=language}
										{assign var="shop_name" value="shop_name_`$language.id_lang`"}
										<input
											class="form-control wk_text_field shop_name_all wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'} {if isset($edit)}{if $mp_seller_info.default_lang == $language.id_lang}seller_default_shop{/if}{else}{if $current_lang.id_lang == $language.id_lang}seller_default_shop{/if}{/if}"
											type="text" data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}"
											value="{if isset($edit)}{if isset($smarty.post.$shop_name)}{$smarty.post.$shop_name|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.shop_name.{$language.id_lang|escape:'htmlall':'UTF-8'}|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.$shop_name)}{$smarty.post.$shop_name|escape:'htmlall':'UTF-8'}{/if}{/if}"
											id="shop_name_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="shop_name_{$language.id_lang|escape:'htmlall':'UTF-8'}"
											{if $current_lang.id_lang != $language.id_lang}style="display:none;" {/if} />
									{/foreach}
									<p class="help-block wk-msg-shopname" style="color:#971414;"></p>
								</div>
							</div>
							{if !isset($edit)}
								<div class="form-group">
									<label class="col-lg-3 control-label">{l s='Enable seller' mod='marketplace'}</label>
									<div class="col-lg-6">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" checked="checked" value="1" id="seller_active_on"
												name="seller_active">
											<label for="seller_active_on">{l s='Yes' mod='marketplace'}</label>
											<input type="radio" value="0" id="seller_active_off" name="seller_active">
											<label for="seller_active_off">{l s='No' mod='marketplace'}</label>
											<a class="slide-button btn"></a>
										</span>
									</div>
								</div>
							{/if}
							<div class="form-group">
								<label for="seller_firstname" class="col-lg-3 control-label required">
									<span class="label-tooltip" "="" ?{}_$%:=" title="" data-html="true" data-toggle="tooltip"
										data-original-title="{l s='Invalid characters' mod='marketplace'} 0-9!&lt;&gt;,;?=+()@#">{l s='First name' mod='marketplace'}</span>
								</label>
								<div class="col-lg-6">
									<input type="text" name="seller_firstname" id="seller_firstname"
										value="{if isset($edit)}{if isset($smarty.post.seller_firstname)}{$smarty.post.seller_firstname|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.seller_firstname|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.seller_firstname)}{$smarty.post.seller_firstname|escape:'htmlall':'UTF-8'}{/if}{/if}" />
								</div>
							</div>
							<div class="form-group">
								<label for="seller_lastname" class="col-lg-3 control-label required">
									<span class="label-tooltip" "="" ?{}_$%:=" title="" data-html="true" data-toggle="tooltip"
										data-original-title="{l s='Invalid characters' mod='marketplace'} 0-9!&lt;&gt;,;?=+()@#">{l s='Last name' mod='marketplace'}</span>
								</label>
								<div class="col-lg-6">
									<input type="text" name="seller_lastname" id="seller_lastname"
										value="{if isset($edit)}{if isset($smarty.post.seller_lastname)}{$smarty.post.seller_lastname|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.seller_lastname|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.seller_lastname)}{$smarty.post.seller_lastname|escape:'htmlall':'UTF-8'}{/if}{/if}" />
								</div>
							</div>
							<div class="form-group">
								<label for="business_email" class="col-lg-3 control-label required">
									{l s='Business email' mod='marketplace'}
								</label>
								<div class="col-lg-6">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="icon-envelope-o"></i>
										</span>
										<input class="form-control-static" type="text" name="business_email" id="business_email"
											value="{if isset($edit)}{if isset($smarty.post.business_email)}{$smarty.post.business_email|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.business_email|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.business_email)}{$smarty.post.business_email|escape:'htmlall':'UTF-8'}{/if}{/if}"
											onblur="onblurCheckUniqueSellerEmail();" />
									</div>
									<p class="help-block wk-msg-selleremail" style="color:#971414;"></p>
								</div>
							</div>
							<div class="form-group">
								<label for="phone" class="col-lg-3 control-label required">
									{l s='Phone' mod='marketplace'}
								</label>
								<div class="col-lg-6">
									<input class="form-control" type="text"
										value="{if isset($edit)}{if isset($smarty.post.phone)}{$smarty.post.phone|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.phone|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.phone)}{$smarty.post.phone|escape:'htmlall':'UTF-8'}{/if}{/if}"
										name="wk_phone" id="phone" maxlength="{$max_phone_digit|escape:'htmlall':'UTF-8'}" />
								</div>
							</div>
							<div class="form-group">
								<label for="fax" class="col-lg-3 control-label">{l s='Fax' mod='marketplace'}</label>
								<div class="col-lg-6">
									<input class="form-control-static" type="text" name="fax" id="fax"
										value="{if isset($edit)}{if isset($smarty.post.fax)}{$smarty.post.fax|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.fax|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.fax)}{$smarty.post.fax|escape:'htmlall':'UTF-8'}{/if}{/if}" />
								</div>
							</div>
							<div class="form-group">
								<label for="fax"
									class="col-lg-3 control-label">{l s='Tax identification number' mod='marketplace'}</label>
								<div class="col-lg-6">
									<input class="form-control-static" type="text" name="tax_identification_number"
										id="tax_identification_number"
										value="{if isset($edit)}{if isset($smarty.post.tax_identification_number)}{$smarty.post.tax_identification_number|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.tax_identification_number|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.tax_identification_number)}{$smarty.post.tax_identification_number|escape:'htmlall':'UTF-8'}{/if}{/if}" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label">
									{l s='Shop description' mod='marketplace'}

									{include file="$wkself/../../views/templates/front/_partials/mp-form-fields-flag.tpl"}
								</label>
								<div class="col-lg-6">
									{foreach from=$languages item=language}
										{assign var="about_shop" value="about_shop_`$language.id_lang`"}
										<div id="about_business_div_{$language.id_lang|escape:'htmlall':'UTF-8'}"
											class="wk_text_field_all wk_text_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
											{if $current_lang.id_lang != $language.id_lang}style="display:none;" {/if}>
											<textarea name="about_shop_{$language.id_lang|escape:'htmlall':'UTF-8'}" id="about_shop_{$language.id_lang|escape:'htmlall':'UTF-8'}"
												cols="2" rows="3" class="about_business wk_tinymce form-control">{if isset($edit)}{if isset($smarty.post.$about_shop)}{$smarty.post.$about_shop|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.about_shop.{$language.id_lang|escape:'htmlall':'UTF-8'}|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.$about_shop)}{$smarty.post.$about_shop|escape:'htmlall':'UTF-8'}{/if}{/if}
																				</textarea>
										</div>
									{/foreach}
								</div>
							</div>
							{hook h="displayMpEditProfileInformationBottom"}
						</div>
						<div class="tab-pane fade in" id="wk-contact">
							<div class="form-group">
								<label for="address" class="col-lg-3 control-label">{l s='Address' mod='marketplace'}</label>
								<div class="col-lg-6">
									<div id="address_div">
										<textarea name="address" id="address" rows="4" cols="35"
											class="validate form-control">{if isset($edit)}{if isset($smarty.post.address)}{$smarty.post.address|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.address|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.address)}{$smarty.post.address|escape:'htmlall':'UTF-8'}{/if}{/if}</textarea>
									</div>
								</div>
							</div>
							<div class="form-group" id="seller_zipcode">
								<label for="postcode"
									class="col-lg-3 control-label {if Configuration::get('WK_MP_SELLER_COUNTRY_NEED')}required{/if}">{l s='Zip/Postal code' mod='marketplace'}</label>
								<div class="col-lg-6">
									<input class="form-control-static" type="text" name="postcode" id="postcode" maxlength="10"
										value="{if isset($edit)}{if isset($smarty.post.postcode)}{$smarty.post.postcode|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.postcode|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.postcode)}{$smarty.post.postcode|escape:'htmlall':'UTF-8'}{/if}{/if}" />
								</div>
							</div>
							<div class="form-group">
								<label for="city"
									class="col-lg-3 control-label {if Configuration::get('WK_MP_SELLER_COUNTRY_NEED')}required{/if}">{l s='City' mod='marketplace'}</label>
								<div class="col-lg-6">
									<input class="form-control" type="text"
										value="{if isset($edit)}{if isset($smarty.post.city)}{$smarty.post.city|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.city|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.city)}{$smarty.post.city|escape:'htmlall':'UTF-8'}{/if}{/if}"
										name="city" id="city" maxlength="64" />
								</div>
							</div>
							<div class="form-group">
								<label for="id_country"
									class="col-lg-3 control-label {if Configuration::get('WK_MP_SELLER_COUNTRY_NEED')}required{/if}">{l s='Country' mod='marketplace'}</label>
								<div class="col-lg-6">
									{if isset($wk_country)}
										<select name="id_country" id="id_country" class="form-control">
											<option value="">{l s='Select country' mod='marketplace'}</option>
											{foreach $wk_country as $countrydetail}
												<option value="{$countrydetail.id_country|escape:'htmlall':'UTF-8'}" {if isset($edit)}
														{if $mp_seller_info.id_country == $countrydetail.id_country}Selected="Selected"
														{/if}
													{/if}>
													{$countrydetail.name|escape:'htmlall':'UTF-8'}
												</option>
											{/foreach}
										</select>
									{/if}
								</div>
							</div>
							<div id="wk_seller_state_div" class="form-group
								{if isset($edit)}{if !$mp_seller_info.id_state}wk_display_none{/if}{else}wk_display_none{/if}">
								<label for="id_state"
									class="col-lg-3 control-label {if Configuration::get('WK_MP_SELLER_COUNTRY_NEED')}required{/if}">{l s='State' mod='marketplace'}</label>
								<div class="col-lg-6">
									<select name="id_state" id="id_state" class="form-control">
										<option value="0">{l s='Select state' mod='marketplace'}</option>
									</select>
									<input type="hidden" name="state_available" id="state_available" value="0" />
								</div>
							</div>
						</div>
						<div class="tab-pane fade in" id="wk-images">
							{if isset($edit)}
								{include file="$wkself/../../views/templates/front/seller/_partials/editprofile-images.tpl"}
							{else}
								<div class="alert alert-danger">
									{l s='You must save this seller before adding images.' mod='marketplace'}
								</div>
							{/if}
						</div>
						<div class="tab-pane fade in" id="wk-social">
							<div class="alert alert-info">
								{l s='Enter Social Profile User id’s to be displayed on seller’s product page, profile page and shop page (Display of these will depend on the "Seller Social profile link" option selected/not selected by seller in ‘Permission’ Tab )' mod='marketplace'}
							</div>
							<div class="form-group">
								<label for="facebook_id"
									class="col-lg-3 control-label">{l s='Facebook ID' mod='marketplace'}</label>
								<div class="col-lg-6">
									<input class="form-control" type="text"
										value="{if isset($edit)}{if isset($smarty.post.facebook_id)}{$smarty.post.facebook_id|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.facebook_id|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.facebook_id)}{$smarty.post.facebook_id|escape:'htmlall':'UTF-8'}{/if}{/if}"
										name="facebook_id" id="facebook_id" />
								</div>
							</div>
							<div class="form-group">
								<label for="twitter_id"
									class="col-lg-3 control-label">{l s='Twitter ID' mod='marketplace'}</label>
								<div class="col-lg-6">
									<input class="form-control" type="text"
										value="{if isset($edit)}{if isset($smarty.post.twitter_id)}{$smarty.post.twitter_id|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.twitter_id|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.twitter_id)}{$smarty.post.twitter_id|escape:'htmlall':'UTF-8'}{/if}{/if}"
										name="twitter_id" id="twitter_id" />
								</div>
							</div>
							<div class="form-group">
								<label for="youtube_id"
									class="col-lg-3 control-label">{l s='Youtube ID' mod='marketplace'}</label>
								<div class="col-lg-6">
									<input class="form-control" type="text"
										value="{if isset($edit)}{if isset($smarty.post.youtube_id)}{$smarty.post.youtube_id|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.youtube_id|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.youtube_id)}{$smarty.post.youtube_id|escape:'htmlall':'UTF-8'}{/if}{/if}"
										name="youtube_id" id="youtube_id" />
								</div>
							</div>
							<div class="form-group">
								<label for="instagram_id"
									class="col-lg-3 control-label">{l s='Instagram ID' mod='marketplace'}</label>
								<div class="col-lg-6">
									<input class="form-control" type="text"
										value="{if isset($edit)}{if isset($smarty.post.instagram_id)}{$smarty.post.instagram_id|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.instagram_id|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.instagram_id)}{$smarty.post.instagram_id|escape:'htmlall':'UTF-8'}{/if}{/if}"
										name="instagram_id" id="instagram_id" />
								</div>
							</div>
						</div>
						<div class="tab-pane fade in" id="wk-permission">
							{if isset($selectedDetailsByAdmin) && $selectedDetailsByAdmin && Configuration::get('WK_MP_SHOW_SELLER_DETAILS')}
								<div class="alert alert-info">
									{l s='Select which details to be displayed for customers on seller’s product page, Profile page and Shop page (subject to access provided by admin)' mod='marketplace'}
								</div>
								<div class="form-group">
									<label class="control-label col-lg-3">
										<span title="" data-toggle="tooltip" class="label-tooltip"
											data-original-title="{l s='Select which details to be displayed for customers on seller’s product page, Profile page and Shop page (subject to access provided by admin)' mod='marketplace'}">
											{l s='Seller permission' mod='marketplace'}
										</span>
									</label>
									<div class="col-lg-4">
										<table class="table table-bordered">
											<thead>
												<tr>
													<th class="fixed-width-xs">
														<span class="title_box">
															<input type="checkbox" name="checkme" id="checkme"
																onclick="checkDelBoxes(this.form, 'groupBox[]', this.checked)" />
														</span>
													</th>
													<th class="fixed-width-xs"><span
															class="title_box">{l s='ID' mod='marketplace'}</span></th>
													<th><span class="title_box">{l s='Permission name' mod='marketplace'}</span>
													</th>
												</tr>
											</thead>
											<tbody>
												{foreach $selectedDetailsByAdmin as $key => $detailsVal}
													<tr>
														<td>
															<input id="groupBox_{$detailsVal.id_group|escape:'htmlall':'UTF-8'}" type="checkbox"
																name="groupBox[]" value="{$detailsVal.id_group|escape:'htmlall':'UTF-8'}" class="groupBox"
																{if isset($edit)}
																	{if isset($selectedDetailsBySeller) && in_array($detailsVal.id_group, $selectedDetailsBySeller)}
																	checked {/if}
																	{else} checked
																	{/if}>
															</td>
															<td>{$detailsVal.id_group|escape:'htmlall':'UTF-8'}</td>
															<td><label for="">{$detailsVal.name|escape:'htmlall':'UTF-8'}</label></td>
														</tr>
													{/foreach}
												</tbody>
											</table>
										</div>
									</div>
								{else}
									<div class="alert alert-danger">
										{l s='You do not permit display of seller details.' mod='marketplace'}
									</div>
								{/if}
							</div>
							<div class="tab-pane fade in" id="wk-seller-payment-details">
								{if isset($mp_payment_option)}
									<div class="form-wrapper">
										<div class="form-group">
											<label for="payment_mode_id"
												class="col-lg-3 control-label">{l s='Payment mode' mod='marketplace'}</label>
											<div class="col-lg-6">
												<select id="payment_mode_id" name="payment_mode_id" class="form-control">
													<option value="">{l s='--- Select payment mode ---' mod='marketplace'}</option>
													{foreach $mp_payment_option as $payment}
														<option id="{$payment.id_mp_payment|escape:'htmlall':'UTF-8'}" value="{$payment.id_mp_payment|escape:'htmlall':'UTF-8'}"
															{if isset($seller_payment_details)}{if $seller_payment_details.payment_mode_id == $payment.id_mp_payment}selected{/if}{/if}>
															{$payment.payment_mode|escape:'htmlall':'UTF-8'}
														</option>
													{/foreach}
												</select>
												<div class="mp_payment_error"></div>
											</div>
										</div>
										<div class="form-group">
											<label for="payment_detail"
												class="col-lg-3 control-label">{l s='Account details' mod='marketplace'}</label>
											<div class="col-lg-6">
												<textarea id="payment_detail" name="payment_detail" class="form-control" rows="4"
													cols="50">{if isset($seller_payment_details)}{$seller_payment_details.payment_detail|escape:'htmlall':'UTF-8'}{/if}</textarea>
											</div>
										</div>
									</div>
								{else}
									<div class="alert alert-info">
										{l s='There are no payment method yet.' mod='marketplace'}
									</div>
								{/if}
							</div>
							{if Configuration::get('WK_MP_PRODUCT_CATEGORY_RESTRICTION')}
								<div class="tab-pane fade in" id="wk-seller-category">
									{$category_tree nofilter}
									{l s='Note:' mod='marketplace'}
									<ul>
										<li>{l s='When child category is selected then parent category will be automatically selected.' mod='marketplace'}
										</li>
										<li>{l s='When parent category is unselected then child category will be automatically unselected.' mod='marketplace'}
										</li>
										<li>{l s='If no category is selected then all categories will be automatically allowed.' mod='marketplace'}
										</li>
									</ul>
								</div>
							{/if}
							{hook h="displayMpEditProfileTabContent"}
						</div>
					</div>

					{if isset($edit)}
						{hook h="displayMpEditProfileFooter"}
					{else}
						{hook h="displayMpSellerRequestFooter"}
					{/if}
					<div class="panel-footer">
						<a href="{$link->getAdminLink('AdminSellerInfoDetail')|escape:'htmlall':'UTF-8'}" class="btn btn-default">
							<i class="process-icon-cancel"></i>{l s='Cancel' mod='marketplace'}
						</a>
						<button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right"
							id="mp_seller_save_button">
							<i class="process-icon-save"></i>{l s='Save' mod='marketplace'}
						</button>
						<button type="submit" name="submitAdd{$table|escape:'htmlall':'UTF-8'}AndStay" class="btn btn-default pull-right"
							id="mp_seller_saveas_button">
							<i class="process-icon-save"></i> {l s='Save and stay' mod='marketplace'}
						</button>
					</div>
					{if isset($edit)}
						{hook h="displayUpdateMpSellerBottom"}
					{else}
						{hook h="displayAddMpSellerBottom"}
					{/if}
				</form>
			</div>

			<style type="text/css">
				.mce-tinymce {
					width: auto !important;
				}
			</style>
			<script type="text/javascript">
				$(document).ready(function() {
					tinySetup({
						editor_selector: "about_business",
						width: 550
					});
				});
			</script>

			{strip}
				{addJsDef path_uploader = $link->getAdminlink('AdminSellerInfoDetail')}
				{addJsDef path_sellerdetails = $link->getAdminlink('AdminSellerInfoDetail')}

				{addJsDef adminupload = 1}
				{addJsDef backend_controller = 1}
				{addJsDef iso = $iso}
				{addJsDef ad = $ad}
				{addJsDef pathCSS = $smarty.const._THEME_CSS_DIR_}
				{addJsDef multi_lang = $multi_lang}

				{if isset($edit)}
					{addJsDef actionIdForUpload = $mp_seller_info.id_seller}
					{addJsDef upload_single = 1}
					{addJsDef actionpage = 'seller'}
					{addJsDef deleteaction = ''}
					{addJsDef id_country = $mp_seller_info['id_country']}
					{addJsDef id_state = $mp_seller_info['id_state']}
					{addJsDef seller_default_img_path=$seller_default_img_path}
					{addJsDef shop_default_img_path=$shop_default_img_path}
				{else}
					{addJsDef actionIdForUpload = ''}
					{addJsDef id_country = 0}
					{addJsDef id_state = 0}
				{/if}

				{addJsDefL name='drag_drop'}{l s='Drag & drop to upload' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name='or'}{l s='or' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name='pick_img'}{l s='Pick image' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=choosefile}{l s='Choose images' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=choosefiletoupload}{l s='Choose images to upload' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=imagechoosen}{l s='Images were chosen' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=dragdropupload}{l s='Drop file here to upload' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=confirm_delete_msg}{l s='Are you sure want to delete this image?' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=only}{l s='Only' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=imagesallowed}{l s='Images are allowed to be uploaded.' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=onlyimagesallowed}{l s='Images are allowed to be uploaded.' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=imagetoolarge}{l s='is too large! Please upload image up to' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=imagetoolargeall}{l s='Images you have choosed are too large! Please upload images up to' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=notmorethanone}{l s='You can not upload more than one image.' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=selectstate}{l s='Select State' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=city_req}{l s='City is required.' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=country_req}{l s='Country is required.' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=state_req}{l s='State is required.' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=req_shop_name_lang}{l s='Shop name is required in default language -' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=shop_name_exist_msg}{l s='Shop unique name already taken. Try another.' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=shop_name_error_msg}{l s='Shop name can not contain any special character except underscore. Try another.' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=seller_email_exist_msg}{l s='Email ID already exist.' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=success_msg}{l s='Success' js=1 mod='marketplace'}{/addJsDefL}
				{addJsDefL name=error_msg}{l s='Error' js=1 mod='marketplace'}{/addJsDefL}
			{/strip}