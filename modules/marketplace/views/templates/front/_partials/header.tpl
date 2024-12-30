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

<div id="wk-header-container" style="background-color: {if isset($themeConf['header_bg_color'])}{$themeConf['header_bg_color']|escape:'htmlall':'UTF-8'}{/if}">
	<div style="background-color: {if isset($themeConf['header_bg_color'])}{$themeConf['header_bg_color']|escape:'htmlall':'UTF-8'}{/if}">
	<div class="wk-margin-container">
		<div class="wk-header-left">
			<a id="wk-header-logo" href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'htmlall':'UTF-8'}">
				<img class="img-responsive"
				id="shop_logo"
				src="{if isset($wk_logo_url)}{$wk_logo_url|escape:'htmlall':'UTF-8'}{else}{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}marketplace/views/img/mpsellerlogin/logo.png{/if}"
				alt="{$shop.name|escape:'htmlall':'UTF-8'}" />
			</a>
		</div>
		<div class="wk-header-right" {if isset($seller_login_page)}style="margin-top: 10px;"{/if}>
			{if isset($seller_login_page)}
				<div id="header_login">
					<div class="row wk_mp_seller_login_form">
						<form method="POST" action="{$link->getModuleLink('marketplace', 'sellerlogin')|escape:'htmlall':'UTF-8'}" class="pull-right" id="mp_login_form">
							<label class="text-capitalize margin-right-5 wk-seller-login-text">
								{l s='Seller Login' mod='marketplace'}
							</label>
							{hook h="displayMpHookBeforeLoginField"}
							<input type="email" placeholder="{l s='Email' mod='marketplace'}" name="email" id="login_email" class="wk_login_field margin-right-5 wk_mp_seller_login_field">
							<input type="password" placeholder="{l s='Password' mod='marketplace'}" name="passwd" id="login_passwd" class="wk_login_field margin-right-5 wk_mp_seller_login_field">
							{hook h="displayMpHookAfterLoginField"}
							<button type="submit" class="btn btn-primary text-capitalize wk_loginform" name="loginform" style="vertical-align: unset;">{l s='Login' mod='marketplace'}</button>
						</form>
					</div>
					<div class="row margin-right-5 wk-forgot">
						<a href="{url entity='password'}">{l s='Forgot your password?' mod='marketplace'}</a>
					</div>
				</div>
			{else}
				<div class="wk-header-icon">
					<a href="{$urls.base_url|escape:'htmlall':'UTF-8'}" target="_blank" title="{l s='Back to shop' mod='marketplace'}">
						<i class="material-icons">screen_share</i>
					</a>
				</div>
				<div class="wk-header-icon">
					<a href="{$link->getModuleLink('marketplace', 'sellerprofile', ['mp_shop_name' => $mp_shop_name])|escape:'htmlall':'UTF-8'}" target="_blank" title="{l s='View profile' mod='marketplace'}">
						<i class="material-icons">&#xE851;</i>
					</a>
				</div>
				<div class="wk-header-icon">
					<a href="{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $mp_shop_name])|escape:'htmlall':'UTF-8'}" target="_blank" title="{l s='View shop' mod='marketplace'}">
						<i class="material-icons">&#xE8D1;</i>
					</a>
				</div>
				<div class="wk-header-icon wk-header-profile">
					<img src="{$wk_profile_image|escape:'htmlall':'UTF-8'}">
				</div>
				<div class="wk-header-icon wk-header-sellername">
					{$seller_name|escape:'htmlall':'UTF-8'}
				</div>
				<div class="wk-header-icon">
					<div class="dropdown seller-dropdown">
						<div data-toggle="dropdown" aria-expanded="true">
							<i class="material-icons">more_vert</i>
						</div>
						<div class="dropdown-menu" x-placement="top-end" style="top:45px">
							<a class="dropdown-item text-center" href="{if isset($edit_profile_link)}{$edit_profile_link|escape:'htmlall':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'editprofile')|addslashes}{/if}">
								{l s='Edit Profile' mod='marketplace'}
							</a>
							<p class="divider"></p>
							<a class="dropdown-item text-center" href="{$link->getModuleLink('marketplace', 'sellerlogin', ['wk_logout' => 1])|escape:'htmlall':'UTF-8'}">
								<span>{l s='Sign out' mod='marketplace'}</span>
							</a>
						</div>
					</div>
				</div>
			{/if}
			<div style="clear: both;"></div>
		</div>
		<div style="clear: both;"></div>
	</div>
	</div>
</div>
{if isset($themeConf['body_bg_color'])}
<style>
	.wk-mp-block,
	.wk-mp-block, .wk-mp-block .menutitle,
	.wk-mp-block .wk_menu_item .menutitle:hover {
		background-color: {$themeConf['body_bg_color']|escape:'htmlall':'UTF-8'} !important;
	}
</style>
{/if}