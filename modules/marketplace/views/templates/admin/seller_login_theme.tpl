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

<form novalidate="" enctype="multipart/form-data" method="post" action="{$current|escape:'htmlall':'UTF-8'}{if isset($token) && $token}&amp;token={$token|escape:'htmlall':'UTF-8'}{/if}" class="defaultForm form-horizontal AdminSelectTheme" id="marketplace_login_theme_form">
	<div id="fieldset_0" class="panel">
		<div class="panel-heading">
			<div class="pull-left">
				<i class="icon-desktop"></i> {l s='Seller login theme' mod='marketplace'}
			</div>
			<a href="{$link->getAdminLink('AdminMpCustomizeLogin')|escape:'htmlall':'UTF-8'}" class="pull-right">
			<button type="button" class="btn btn-primary" style="text-transform: revert;">
				<i class="process-icon-edit"></i> {l s='Edit active theme' mod='marketplace'}
			</button>
			</a>
			<div style="clear: both;"></div>
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required">{l s='Select theme' mod='marketplace'}</label>
				<div class="col-lg-9 ">
					<select id="login_theme" class=" fixed-width-xl" name="login_theme">
						{foreach from=$all_themes key=theme_id item=theme_name}
							<option value="{$theme_id|escape:'htmlall':'UTF-8'}" {if $theme_id == $active_theme_id}selected="selected"{/if}>
								{$theme_name|escape:'htmlall':'UTF-8'}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-9 col-lg-offset-3">
					<img src="{$prev_img|escape:'quotes':'UTF-8'}" class="img-responsive" id="theme_preview">
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submitLoginTheme" value="1" type="submit">
				<i class="process-icon-save"></i> {l s='Save' mod='marketplace'}
			</button>
		</div>
	</div>
</form>

{strip}
	{addJsDef preview_img_dir=$preview_img_dir|escape:'quotes':'UTF-8'}
{/strip}