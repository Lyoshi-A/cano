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
{if isset($themeConf['meta_title']) && $themeConf['meta_title']}
	{block name='head_seo_title'}
		{$themeConf['meta_title']|escape:'htmlall':'UTF-8'}
	{/block}
{/if}
{if isset($themeConf['meta_description']) && $themeConf['meta_description']}
	{block name='head_seo_description'}
		{$themeConf['meta_description']|escape:'htmlall':'UTF-8'}
	{/block}
{/if}
{block name='product_activation'}{/block}

{block name='header'}
   {include file='module:marketplace/views/templates/front/_partials/header.tpl'}
{/block}

{block name='content'}
{if isset($parentBlock)}
	{foreach from=$parentBlock key=key item=value}
		{if $value['name'] == 'registration'}
			{* --- Registration block configuration --- *}
			<div class="container block-configuration">
				<div class="top-block col-sm-12">
					{foreach from=$value['sub_block'] key=sub_k item=sub_v}
						{if $sub_v['block_name'] == 'reg_title'}
							<div class="left-block col-sm-{$sub_v['width']|escape:'htmlall':'UTF-8'}">
								<img class="im-start" src="{$urls["img_ps_url"]}cms/mpstart.png" alt="Take Your First Step Towards Online Selling" />
{*								<p style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'}" class="title_style">{$sub_v['data']['content']|escape:'htmlall':'UTF-8'}</p>*}
							</div>
						{/if}
						{if $sub_v['block_name'] == 'reg_block'}
							<div class="right-block col-sm-{$sub_v['width']|escape:'htmlall':'UTF-8'}">
							{if empty($wk_seller_request_pending)}
								<form method="POST" action="{$link->getModuleLink('marketplace', 'sellerlogin')|escape:'htmlall':'UTF-8'}" class="defaultForm form-horizontal" enctype="multipart/form-data" id="mp_register_form">
									<input type="hidden" name="ps_customer_id" value="{if isset($wk_customer_id)}{$wk_customer_id|escape:'htmlall':'UTF-8'}{/if}" id="ps_customer_id">
									<input type="hidden" name="idSeller" value="" id="idSeller">
									<div class="col-sm-12 form_wrapper" id="form_acc_info">
										<p class="text-left t1 margin-top-10 form_heading">{l s='Take Your First Step' mod='marketplace'}</p>
										<p class="text-left t2 margin-top-10 form_heading">{l s='Towards Online Selling' mod='marketplace'}</p>
{*										<hr class="hr_style">*}

										{hook h="displayMpBeforeAccountInfoField"}

{*										<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">*}
{*											<span class="field_heading">{l s='Title' mod='marketplace'}</span>*}
{*											<div class="">*}
{*												{foreach from=$genders key=k item=gender}*}
{*													<label class="radio-inline" for="id_gender{$gender->id|escape:'htmlall':'UTF-8'}">*}
{*														<span class="custom-radio">*}
{*														<input name="id_gender" id="id_gender{$gender->id|escape:'htmlall':'UTF-8'}" type="radio"*}
{*															value="{$gender->id|escape:'htmlall':'UTF-8'}" {if isset($smarty.post.id_gender) &&*}
{*															$smarty.post.id_gender==$gender->id}checked="checked"*}
{*														{/if}>*}
{*														<span></span>*}
{*														</span>*}
{*														{$gender->name|escape:'htmlall':'UTF-8'}*}
{*													</label>*}
{*												{/foreach}*}
{*											</div>*}
{*										</label>*}
										<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
{*											<span class="field_heading"><span class="mand_field">*</span> {l s='First name' mod='marketplace'}</span>*}
											<input type="text" placeholder="First Name" class="form-control" name="firstname" id="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname|trim|escape:'htmlall':'UTF-8'}{/if}">
										</label>
										<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
{*											<span class="field_heading"><span class="mand_field">*</span> {l s='Last name' mod='marketplace'}</span>*}
											<input type="text" placeholder="Last Name" class="form-control" name="lastname" id="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname|trim|escape:'htmlall':'UTF-8'}{/if}">
										</label>
										<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
{*											<span class="field_heading"><span class="mand_field">*</span> {l s='Email' mod='marketplace'}</span>*}
											<input type="email" placeholder="Email" class="form-control" name="email" id="email" value="{if isset($smarty.post.email)}{$smarty.post.email|trim|escape:'htmlall':'UTF-8'}{else}{if isset($wk_email)}{$wk_email|escape:'htmlall':'UTF-8'}{/if}{/if}" required>
										</label>
										<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
{*											<span class="field_heading"><span class="mand_field">*</span> {l s='Password' mod='marketplace'}</span>*}
											<input type="password" class="form-control" placeholder="Password" name="passwd" id="passwd" value="{if isset($smarty.post.passwd)}{$smarty.post.passwd|trim|escape:'htmlall':'UTF-8'}{/if}">
										</label>
										<div class="check_email" style="{if isset($wk_customer_logged)}display:block;{else}display:none;{/if}">
											<span class="email_notify">{l s='This email is already registered as customer, if you want to continue with same Email-Id then enter password and' mod='marketplace'} <span id="wk-shop-form">{l s='click here' mod='marketplace'}</span></span>
										</div>

										{hook h="displayMpAfterAccountInfoField"}

										<div class="mp_error_block login_act_err">
											<span class="mp_error"></span>
										</div>
										<button type="button" class="btn btn-success form_button" id="account_btn">
											{l s='Get Started' mod='marketplace'}</button>
									</div>
									<div class="col-sm-12 form_wrapper" id="form_shop_info" style="background-color: {$sub_v['block_bg_color']|escape:'htmlall':'UTF-8'}; color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};{if isset($wk_customer_logged)}display:block;{else}display:none;{/if}">
										<input type="button" class="btn btn-info btn-xs pull-right" value="{l s='BACK' mod='marketplace'}" id="back_account" />
										<p class="text-left margin-top-10 form_heading"><strong>{l s='Create your shop' mod='marketplace'}</strong></p>

										{hook h="displayMpBeforeShopInfoField"}

										<input type="hidden" name="multi_lang" id="multi_lang" value="{$multi_lang|escape:'htmlall':'UTF-8'}">
										<input type="hidden" name="current_lang" id="current_lang" value="{$current_lang.id_lang|escape:'htmlall':'UTF-8'}">
										<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
											{l s='Default language' mod='marketplace'}
											<select class="form-control" name="seller_default_lang" id="seller_default_lang">
											{foreach from=$languages item=language}
												<option data-lang-iso="{$language.iso_code|escape:'htmlall':'UTF-8'}"
												value="{$language.id_lang|escape:'htmlall':'UTF-8'}"
												{if isset($smarty.post.seller_default_lang)}
													{if $smarty.post.seller_default_lang == $language.id_lang}Selected="Selected"
													{/if}
												{else}
													{if $current_lang.id_lang == $language.id_lang}Selected="Selected"
													{/if}
												{/if}>
												{$language.name|escape:'htmlall':'UTF-8'}
												</option>
											{/foreach}
											</select>
										</label>
										<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
{*											<span class="field_heading"><span class="mand_field">*</span> {l s='Unique Shop Name' mod='marketplace'}</span>*}
											<input type="text" class="form-control" placeholder="Shop unique name" name="mp_shop_name_unique" id="mp_shop_name_unique" value="{if isset($smarty.post.mp_shop_name_unique)}{$smarty.post.mp_shop_name_unique|trim|escape:'htmlall':'UTF-8'}{/if}"id="mp_shop_name_unique" autocomplete="off">
										</label>
										<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
											<span class="field_heading">
												<span class="mand_field">*</span>
												{l s='Shop name' mod='marketplace'}
											</span>
											<div class="row">
												{if $allow_multilang && $total_languages > 1}
													<div class="col-md-9">
												{else}
													<div class="col-md-12">
												{/if}
													{foreach from=$languages item=language}
														{assign var="mp_shop_name_lang" value="mp_shop_name_`$language.id_lang`"}
														<input type="text" class="form-control shop_name_all {if $current_lang.id_lang == $language.id_lang}seller_default_shop{/if}" name="mp_shop_name_{$language.id_lang|escape:'htmlall':'UTF-8'}" id="mp_shop_name_{$language.id_lang|escape:'htmlall':'UTF-8'}" value="{if isset($smarty.post.$mp_shop_name_lang)}{$smarty.post.$mp_shop_name_lang|trim}{/if}"
														data-lang-name="{$language.name|escape:'htmlall':'UTF-8'}"
														{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
													{/foreach}
												</div>
												{if $allow_multilang && $total_languages > 1}
												<div class="col-md-3">
													<button type="button" id="shop_name_lang_btn" class="btn btn-default dropdown-toggle lang_padding" data-toggle="dropdown">
													{$current_lang.iso_code|escape:'htmlall':'UTF-8'}
													<span class="caret"></span>
													</button>
													<ul class="dropdown-menu dropdown-menu-right">
														{foreach from=$languages item=language}
														<li>
															<a href="javascript:void(0)" onclick="showLangField('{$language.iso_code|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'});">{$language.name|escape:'htmlall':'UTF-8'}</a>
														</li>
														{/foreach}
													</ul>
												</div>
												{/if}
											</div>
										</label>
										<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
{*											<span class="field_heading"><span class="mand_field">*</span> {l s='Business Email' mod='marketplace'}</span>*}
											<input type="text" placeholder="Business Email" class="form-control" name="mp_seller_email" id="mp_seller_email" value="{if isset($smarty.post.mp_seller_email)}{$smarty.post.mp_seller_email|trim}{else}{if isset($wk_email)}{$wk_email|escape:'htmlall':'UTF-8'}{/if}{/if}" required>
										</label>
										{if $MP_SELLER_COUNTRY_NEED}
											{if isset($wk_countries)}
												<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
													<span class="field_heading">
														<span class="mand_field">*</span>
														{l s='Country' mod='marketplace'}
													</span>
													<select class="form-control" name="seller_country" id="seller_country">
														{foreach $wk_countries as $country}
															{if isset($smarty.post.seller_country) && $country.id_country == $smarty.post.seller_country}
																<option value="{$country.id_country|escape:'htmlall':'UTF-8'}" Selected>{$country.name|escape:'htmlall':'UTF-8'}</option>
															  {else}
																<option value="{$country.id_country|escape:'htmlall':'UTF-8'}">{$country.name|escape:'htmlall':'UTF-8'}</option>
															{/if}
														{/foreach}
													</select>
												</label>
											{/if}
											{if isset($smarty.post.seller_country) && !empty($smarty.post.seller_country)}
												{assign var=firstCountry value=$wk_countries[$smarty.post.seller_country]}
											{else}
												{assign var=firstCountry value=$wk_countries|@current}
											{/if}
											<label class="field_label" id="sellerStateCont" style="{if !isset($firstCountry.states)}display:none;{/if} color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
												<span class="mand_field">*</span>
												{l s='State' mod='marketplace'}
												<select class="form-control" name="seller_state" id="seller_state">
													<option value="0">{l s='Select state' mod='marketplace'}</option>
													{if isset($firstCountry.states)}
														{foreach $firstCountry.states as $state}
															{if isset($smarty.post.seller_state) && $state.id_state == $smarty.post.seller_state}
																<option value="{$state.id_state|escape:'htmlall':'UTF-8'}" Selected>{$state.name|escape:'htmlall':'UTF-8'}</option>
															  {else}
																<option value="{$state.id_state|escape:'htmlall':'UTF-8'}">{$state.name|escape:'htmlall':'UTF-8'}</option>
															{/if}
														{/foreach}
													{/if}
												</select>
												<input type="hidden" name="state_avl" id="state_avl" value="{if !isset($firstCountry.states)}0{else}1{/if}"/>
											</label>
											<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
												<span class="field_heading"><span class="mand_field">*</span> {l s='City' mod='marketplace'}</span>
												<input type="text" class="form-control" name="seller_city" id="seller_city" value="{if isset($smarty.post.seller_city)}{$smarty.post.seller_city|trim|escape:'htmlall':'UTF-8'}{/if}">
											</label>
											<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
												<span class="field_heading">
													<span class="mand_field">*</span>
													{l s='Zip/postal code' mod='marketplace'}
												</span>
												<input type="text" class="form-control" name="seller_postcode" id="seller_postcode" value="{if isset($smarty.post.seller_postcode)}{$smarty.post.seller_postcode|trim|escape:'htmlall':'UTF-8'}{/if}" required>
											</label>
										{/if}
										<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
											<span class="field_heading"><span class="mand_field">*</span> {l s='Phone' mod='marketplace'}</span>
											<input type="text" class="form-control" name="mp_seller_phone" id="mp_seller_phone" maxlength="{$max_phone_digit|escape:'htmlall':'UTF-8'}" value="{if isset($smarty.post.mp_seller_phone)}{$smarty.post.mp_seller_phone|trim}{/if}" required>
										</label>
										{if $terms_and_condition_active}
											<label class="field_label" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">
												<input type="checkbox" required name="terms_and_conditions" id="terms_and_conditions" />
												<span>
													{l s='I agree to the' mod='marketplace'}
													{if isset($linkCmsPageContent)}
														<a href="{$linkCmsPageContent|escape:'htmlall':'UTF-8'}" class="wk_terms_link">
															{l s='terms and condition' mod='marketplace'}
														</a>
													{else}
														{l s='terms and condition' mod='marketplace'}
													{/if}
													{l s='and will adhere to them unconditionally.' mod='marketplace'}
												</span>
											</label>
											{if isset($linkCmsPageContent)}
												<div class="modal fade" id="wk_terms_condtion_div" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																	<span aria-hidden="true">&times;</span>
																</button>
															</div>
															<div id="wk_terms_condtion_content" class="modal-body"></div>
														</div>
													</div>
												</div>
											{/if}
										{/if}
										{hook h="displayMpAfterShopInfoField"}
										{hook h="displayMpSellerRequestFooter"}
										{hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
										<div class="mp_error_block login_shop_err">
											<span class="mp_error"></span>
										</div>
										<button type="submit" class="btn btn-success form_button" name="registrationform">
										{l s='Go To Dashboard' mod='marketplace'}</button>
									</div>
								</form>
							{else}
								<div class="col-sm-12 form_wrapper" style="background-color: {$sub_v['block_bg_color']|escape:'htmlall':'UTF-8'}; color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};height:320px;">
									<p class="text-left margin-top-10 form_heading" style="color: {$sub_v['block_text_color']|escape:'htmlall':'UTF-8'};">{l s='Seller Account' mod='marketplace'}</p>
{*									<hr class="hr_style">*}
									{if isset($mpSellerShopSettings) && $mpSellerShopSettings && isset($shop_approved) && $shop_approved}
										<div class="alert alert-info wk-seller-request-alert">
											{l s='Your request has been approved by admin. ' mod='marketplace'}
											<a title="{l s='Re-Activate Your Shop' mod='marketplace'}" href="{$link->getModuleLink('marketplace', 'editprofile', ['reactivate' => 1])|escape:'htmlall':'UTF-8'}">
												<button class="btn btn-primary btn-sm">
													{l s='Re-Activate Your Shop' mod='marketplace'}
												</button>
											</a>
										</div>
									{else}
										<div class="alert alert-info wk-seller-request-alert">
											{l s='Your request has been sent to admin. Please wait the request gets approved by admin' mod='marketplace'}
										</div>
									{/if}
								</div>
							{/if}
							</div>
						{/if}
					{/foreach}
				</div>
			</div>
		{/if}
		{* --- Content block configuration --- *}
		{if $value['name'] == 'content'}
			{* --- Feature block configuration --- *}
			<div class="container block-features" style="clear: both;">
				<div class="row">
					{foreach from=$value['sub_block'] key=subc_k item=subc_v}
						{if $subc_v['block_name'] == 'feature'}
							<div class="col-sm-{$subc_v['width']|escape:'htmlall':'UTF-8'} ftr_cont" style="background-color: {$subc_v['block_bg_color']|escape:'htmlall':'UTF-8'}; color: {$subc_v['block_text_color']|escape:'htmlall':'UTF-8'};">
								{$subc_v['data']['content'] nofilter}
							</div>
						{/if}
					{/foreach}
				</div>
			</div>
			{* --- Terms and conditions block --- *}
			<div class="container" style="clear: both;">
				<div class="row">
					{foreach from=$value['sub_block'] key=subc_k item=subc_v}
						{if $subc_v['block_name'] == 'termscondition'}
							<div class="col-sm-{$subc_v['width']|escape:'htmlall':'UTF-8'} tc_cont" style="background-color: {$subc_v['block_bg_color']|escape:'htmlall':'UTF-8'};">
								{$subc_v['data']['content'] nofilter}
							</div>
							<style>
								.tc_cont span {
									color: {$subc_v['block_text_color']|escape:'htmlall':'UTF-8'} !important;
								}
							</style>
						{/if}
					{/foreach}
				</div>
			</div>
		{/if}
	{/foreach}
{/if}
{if isset($themeConf['body_bg_color'])}
	<style type="text/css">
		body #wrapper {
			background-color: {$themeConf['body_bg_color']|escape:'htmlall':'UTF-8'};
			font-family: Arial,sans-serif;
		}
	</style>
{/if}
{/block}
{block name="footer"}
{*{if isset($error)}*}
{*	<input type="hidden" id="wk_slerror" value="{$error|escape:'htmlall':'UTF-8'}">*}
{*	<div class="error_block">*}
{*		{if $error == 1}*}
{*			{l s='Email is required.' mod='marketplace'}*}
{*		{elseif $error == 2}*}
{*			{l s='Invalid email address.' mod='marketplace'}*}
{*		{elseif $error == 3}*}
{*			{l s='Password is required.' mod='marketplace'}*}
{*		{elseif $error == 4}*}
{*			{l s='Invalid password.' mod='marketplace'}*}
{*		{elseif $error == 5}*}
{*			{l s='Please enter your valid credentials.' mod='marketplace'}*}
{*		{elseif $error == 6}*}
{*			{l s='Your account isn\'t available at this time, please contact us.' mod='marketplace'}*}
{*		{elseif $error == 7}*}
{*			{l s='Authentication failed.' mod='marketplace'}*}
{*		{elseif $error == 8}*}
{*			{l s='Please enter your first name.' mod='marketplace'}*}
{*		{elseif $error == 9}*}
{*			{l s='Please enter your last name.' mod='marketplace'}*}
{*		{elseif $error == 11}*}
{*			{l s='An account using this email address has already been registered.' mod='marketplace'}*}
{*		{elseif $error == 13}*}
{*			{l s='Shop name is required.' mod='marketplace'}*}
{*		{elseif $error == 14}*}
{*			{l s='Please enter valid shop name.' mod='marketplace'}*}
{*		{elseif $error == 15}*}
{*			{l s='Unique shop name already exist.' mod='marketplace'}*}
{*		{elseif $error == 16}*}
{*			{l s='Phone number is required.' mod='marketplace'}*}
{*		{elseif $error == 17}*}
{*			{l s='Please enter valid phone number.' mod='marketplace'}*}
{*		{elseif $error == 18}*}
{*			{l s='Email already exist as seller.' mod='marketplace'}*}
{*		{elseif $error == 19}*}
{*			{l s='Unique shop name is required.' mod='marketplace'}*}
{*		{elseif $error == 20}*}
{*			{l s='Please enter valid unique shop name.' mod='marketplace'}*}
{*		{elseif $error == 21}*}
{*			{l s='Shop name is required in default language.' mod='marketplace'}*}
{*		{elseif $error == 22}*}
{*			{l s='You are not registered as a seller.' mod='marketplace'}*}
{*		{elseif $error == 23}*}
{*			{l s='City is required field.' mod='marketplace'}*}
{*		{elseif $error == 24}*}
{*			{l s='Invalid city name.' mod='marketplace'}*}
{*		{elseif $error == 25}*}
{*			{l s='Country is required field.' mod='marketplace'}*}
{*		{elseif $error == 26}*}
{*			{l s='State is required field.' mod='marketplace'}*}
{*		{elseif $error == 27}*}
{*			{l s='Zip/postal code is required field.' mod='marketplace'}*}
{*		{elseif $error == 28}*}
{*			{l s='Invalid zip/postal code format.' mod='marketplace'}*}
{*		{elseif $error == 29}*}
{*			{l s='Invalid zip/postal code.' mod='marketplace'}*}
{*		{/if}*}
{*	</div>*}
{*{/if}*}
{/block}