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

<form novalidate="" enctype="multipart/form-data" method="post" action="{$current|escape:'htmlall':'UTF-8'}{if isset($token) && $token}&amp;token={$token|escape:'htmlall':'UTF-8'}{/if}" class="defaultForm form-horizontal AdminCustomizeLogin" id="marketplace_login_content_form_1">
	<div class="alert alert-info">
		{l s='Active theme for seller login page is' mod='marketplace'}
		<b>{$active_theme|escape:'htmlall':'UTF-8'}</b>
	</div>
	<div id="fieldset_0_1_5" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s='Header block configuration' mod='marketplace'}
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller login and seller panel header background color' mod='marketplace'}">{l s='Header background color' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="row">
						<div class="col-lg-3">
							<div class="input-group">
								<input type="text" {if isset($themeConfig) && $themeConfig['header_bg_color']}value="{$themeConfig['header_bg_color']|escape:'htmlall':'UTF-8'}"{/if} name="header_bg_color" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_0" style="{if isset($themeConfig) && $themeConfig['header_bg_color']|escape:'htmlall':'UTF-8'}background-color:{$themeConfig['header_bg_color']|escape:'htmlall':'UTF-8'}{/if}">
								<span class="mColorPickerTrigger input-group-addon" id="icp_color_0" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
							</div>
						</div>
					</div>
					<p class="help-block">{l s='Same configuration will work for seller panel header.' mod='marketplace'}</p>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller login and seller panel body background color' mod='marketplace'}">{l s='Body background color' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="row">
						<div class="col-lg-3">
							<div class="input-group">
								<input type="text" {if isset($themeConfig) && $themeConfig['body_bg_color']}value="{$themeConfig['body_bg_color']|escape:'htmlall':'UTF-8'}"{/if} name="body_bg_color" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_1" style="{if isset($themeConfig) && $themeConfig['body_bg_color']}background-color:{$themeConfig['body_bg_color']|escape:'htmlall':'UTF-8'}{/if}">
								<span class="mColorPickerTrigger input-group-addon" id="icp_color_1" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
							</div>
						</div>
					</div>
					<p class="help-block">{l s='Same configuration will work for seller panel header.' mod='marketplace'}</p>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Image file dimension must be less than or equal to 350*99 px and image size must be less than 8M' mod='marketplace'}">{l s='Logo' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					{if isset($wk_logo_url)}
						<div class="form-group">
							<div id="wk_logo-images-thumbnails" class="col-lg-12">
								<img src="{$wk_logo_url|escape:'htmlall':'UTF-8'}" class="img-thumbnail" style="background-color: #eee;" height="70px;" />
							</div>
							<p class="help-block">{l s='Same configuration will work for seller panel header.' mod='marketplace'}</p>
						</div>
					{/if}
					<div class="form-group">
						<div class="col-sm-6">
							<input type="file" class="hide" name="wk_logo" id="wk_logo">
							<div class="dummyfile input-group">
								<span class="input-group-addon"><i class="icon-file"></i></span>
								<input type="text" readonly="" name="filename" id="wk_logo-name">
								<span class="input-group-btn">
									<button class="btn btn-default" name="submitAddAttachments" type="button" id="wk_logo-selectbutton">
										<i class="icon-folder-open"></i> {l s='Add file' mod='marketplace'}
									</button>
								</span>
							</div>
							<i>{l s='Recommended Dimension: 130 x 50 pixels' mod='marketplace'}</i>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='This will be your page title' mod='marketplace'}">{l s='Meta title' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="row">
						{if $total_languages > 1}
						<div class="col-md-10">
						{else}
						<div class="col-md-12">
						{/if}
							{foreach from=$languages item=language}
								{assign var="meta_tit" value="metaTitle_`$language.id_lang`"}
								<input type="text"
								id="metaTitle_{$language.id_lang|escape:'htmlall':'UTF-8'}"
								name="metaTitle_{$language.id_lang|escape:'htmlall':'UTF-8'}"
								{if isset($themeConfig)}
									value="{if isset($themeConfig['meta_title'][{$language.id_lang|escape:'htmlall':'UTF-8'}])}{$themeConfig['meta_title'][{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
								{else}
									value="{if isset($smarty.post.$meta_tit)}{$smarty.post.$meta_tit|escape:'htmlall':'UTF-8'}{/if}"
								{/if}
								class="form-control metaTitleAll"
								{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
							{/foreach}
						</div>
						{if $total_languages > 1}
						<div class="col-lg-2">
							<button type="button" id="metaTitleLang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								{$current_lang.iso_code|escape:'htmlall':'UTF-8'}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=language}
									<li>
										<a href="javascript:void(0)" onclick="showMetaTitleLangField('{$language.iso_code|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'});">{$language.name|escape:'htmlall':'UTF-8'}</a>
									</li>
								{/foreach}
							</ul>
						</div>
						{/if}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='This will be your page meta description' mod='marketplace'}">{l s='Meta description' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="row">
						{if $total_languages > 1}
						<div class="col-md-10">
						{else}
						<div class="col-md-12">
						{/if}
							{foreach from=$languages item=language}
								{assign var="meta_desc" value="metaDescription_`$language.id_lang`"}
								<input type="text"
								id="metaDescription_{$language.id_lang|escape:'htmlall':'UTF-8'}"
								name="metaDescription_{$language.id_lang|escape:'htmlall':'UTF-8'}"
								{if isset($themeConfig)}
									value="{if isset($themeConfig['meta_description'][{$language.id_lang|escape:'htmlall':'UTF-8'}])}{$themeConfig['meta_description'][{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
								{else}
									value="{if isset($smarty.post.$meta_desc)}{$smarty.post.$meta_desc|escape:'htmlall':'UTF-8'}{/if}"
								{/if}
								class="form-control metaDescriptionAll"
								{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
							{/foreach}
						</div>
						{if $total_languages > 1}
						<div class="col-lg-2">
							<button type="button" id="metaDescriptionLang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								{$current_lang.iso_code|escape:'htmlall':'UTF-8'}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=language}
									<li>
										<a href="javascript:void(0)" onclick="showMetaTitleLangField('{$language.iso_code|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'});">
											{$language.name|escape:'htmlall':'UTF-8'}
										</a>
									</li>
								{/foreach}
							</ul>
						</div>
						{/if}
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_1" id="marketplace_login_content_form_submit_btn_5" value="1" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='marketplace'}
			</button>
		</div>
	</div>
	<div id="fieldset_2_1_8" class="panel">
		<div class="panel-heading"><i class="icon-cogs"></i> {l s='Registration block configuration' mod='marketplace'}</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required label-tooltip" data-html="true" data-toggle="tooltip" data-original-title="{l s='If No, banner block will not display.' mod='marketplace'}">{l s='Enable registration block' mod='marketplace'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input data-block="2" type="radio" value="1" id="regPBlockActive_on" name="regPBlockActive" {if isset($regPBlockActive) && $regPBlockActive}checked="checked"{/if}>
						<label for="regPBlockActive_on">{l s='Yes' mod='marketplace'}</label>
						<input data-block="2" type="radio" value="0" id="regPBlockActive_off" name="regPBlockActive" {if empty($regPBlockActive)}checked="checked"{/if}>
						<label for="regPBlockActive_off">{l s='No' mod='marketplace'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="form-group wk_block_2">
				<label class="control-label col-lg-3 required">{l s='Banner block position' mod='marketplace'}</label>
				<div class="col-lg-9">
					<select id="regBannerPosition" class="fixed-width-xl" name="regBannerPosition">
						{foreach $two_block_position as $banPos}
							<option value="{$banPos.id|escape:'htmlall':'UTF-8'}" {if isset($regBannerPosition) && $regBannerPosition == $banPos.id}selected="selected"{/if}>{$banPos.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group wk_block_2">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Maximum image size: 8M' mod='marketplace'}">{l s='Banner image' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div id="banner_img-images-thumbnails" class="col-lg-12">
							<div>
								{if isset($bannerImgUrl)}
									<img src="{$bannerImgUrl|escape:'htmlall':'UTF-8'}" height="70px;">
								{/if}
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-6">
							<input type="file" class="hide" name="banner_img" id="banner_img">
							<div class="dummyfile input-group">
								<span class="input-group-addon"><i class="icon-file"></i></span>
								<input type="text" readonly="" name="filename" id="banner_img-name">
								<span class="input-group-btn">
									<button class="btn btn-default" name="submitAddAttachments" type="button" id="banner_img-selectbutton">
										<i class="icon-folder-open"></i> {l s='Add file' mod='marketplace'}
									</button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group wk_block_2">
				<label class="control-label col-lg-3 required label-tooltip" data-html="true" data-toggle="tooltip" data-original-title="{l s='If No, title block will not display.' mod='marketplace'}">{l s='Enable title block' mod='marketplace'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input data-block="21" type="radio" value="1" class="wk_enable_btn" id="regTitleBlockActive_on" name="regTitleBlockActive" {if isset($regBlockTitleDetails) && $regBlockTitleDetails['active']}checked="checked"{/if}>
						<label for="regTitleBlockActive_on">{l s='Yes' mod='marketplace'}</label>
						<input data-block="21" type="radio" value="0" class="wk_enable_btn" id="regTitleBlockActive_off" name="regTitleBlockActive" {if isset($regBlockTitleDetails) && !$regBlockTitleDetails['active']}checked="checked"{/if}>
						<label for="regTitleBlockActive_off">{l s='No' mod='marketplace'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="form-group wk_block_21">
				<label class="control-label col-lg-3 required">{l s='Title block position' mod='marketplace'}</label>
				<div class="col-lg-9">
					<select id="regTitleBlockPos" class=" fixed-width-xl" name="regTitleBlockPos">
						{if $reg_pos}
							{foreach $reg_pos as $title_wid}
								<option value="{$title_wid.id|escape:'htmlall':'UTF-8'}" {if isset($regBlockTitleDetails) && $regBlockTitleDetails['id_position'] == $title_wid.id}selected="selected"{/if}>{$title_wid.name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						{else}
							<option value="1">1</option>
							<option value="2">2</option>
						{/if}
					</select>
				</div>
			</div>
			<div class="form-group wk_block_21">
				<label class="control-label col-lg-3 required">{l s='Title block width' mod='marketplace'}</label>
				<div class="col-lg-9">
					<select id="regTitleBlockWidth" class=" fixed-width-xl" name="regTitleBlockWidth">
						{foreach $width as $title_wid}
							<option value="{$title_wid.id_value|escape:'htmlall':'UTF-8'}" {if isset($regBlockTitleDetails) && $regBlockTitleDetails['width'] == $title_wid.id_value}selected="selected"{/if}>{$title_wid.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group wk_block_21">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Title text color' mod='marketplace'}">{l s='Title text color' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-lg-3">
							<div class="row">
								<div class="input-group">
									<input type="text" {if isset($regBlockTitleDetails) && $regBlockTitleDetails['block_text_color']}value="{$regBlockTitleDetails['block_text_color']|escape:'htmlall':'UTF-8'}"{/if} name="regTitleTextColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_6" style="{if isset($regBlockTitleDetails) && $regBlockTitleDetails['block_text_color']}background-color:{$regBlockTitleDetails['block_text_color']|escape:'htmlall':'UTF-8'}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_6" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group wk_block_21">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='This will be your title text' mod='marketplace'}">{l s='Title line' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="row">
						{if $total_languages > 1}
							<div class="col-md-10">
						{else}
							<div class="col-md-12">
						{/if}
							{foreach from=$languages item=language}
								{assign var="regTitle" value="regTitleLine_`$language.id_lang`"}
								<input type="text"
								id="regTitleLine_{$language.id_lang|escape:'htmlall':'UTF-8'}"
								name="regTitleLine_{$language.id_lang|escape:'htmlall':'UTF-8'}"
								{if isset($regTitleLine)}
									value="{if isset($regTitleLine['content'][{$language.id_lang|escape:'htmlall':'UTF-8'}])}{$regTitleLine['content'][{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}"
								{else}
									value="{if isset($smarty.post.$regTitle)}{$smarty.post.$regTitle|escape:'htmlall':'UTF-8'}{/if}"
								{/if}
								class="form-control regTitleLineAll"
								{if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if} />
							{/foreach}
						</div>
						{if $total_languages > 1}
						<div class="col-lg-2">
							<button type="button" id="regTitleLineLang_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								{$current_lang.iso_code|escape:'htmlall':'UTF-8'}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=language}
									<li>
										<a href="javascript:void(0)" onclick="showTitleLineLangField('{$language.iso_code|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'});">{$language.name|escape:'htmlall':'UTF-8'}</a>
									</li>
								{/foreach}
							</ul>
						</div>
						{/if}
					</div>
				</div>
			</div>
			<div class="form-group wk_block_2">
				<label class="control-label col-lg-3 required label-tooltip" data-html="true" data-toggle="tooltip" data-original-title="{l s='If No, registration block will not display.' mod='marketplace'}">{l s='Enable registration form' mod='marketplace'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input data-block="22" type="radio" value="1" class="wk_enable_btn" id="regBlockActive_on" name="regBlockActive" {if isset($regBlockDetails) && $regBlockDetails['active']}checked="checked"{/if}>
						<label for="regBlockActive_on">{l s='Yes' mod='marketplace'}</label>
						<input data-block="22" type="radio" value="0" class="wk_enable_btn" id="regBlockActive_off" name="regBlockActive" {if !isset($regBlockDetails) || !$regBlockDetails['active']}checked="checked"{/if}>
						<label for="regBlockActive_off">{l s='No' mod='marketplace'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="form-group wk_block_22">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller login page registration blocks background color' mod='marketplace'}">{l s='Registration block background color' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-lg-3">
							<div class="row">
								<div class="input-group">
									<input type="text" {if isset($regBlockDetails) && $regBlockDetails['block_bg_color']}value="{$regBlockDetails['block_bg_color']|escape:'htmlall':'UTF-8'}"{/if} name="regBgColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_7" style="{if isset($regBlockDetails) && $regBlockDetails['block_bg_color']}background-color:{$regBlockDetails['block_bg_color']|escape:'htmlall':'UTF-8'}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_7" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group wk_block_22">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller login page registration blocks text color' mod='marketplace'}">{l s='Registration block text color' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-lg-3">
							<div class="row">
								<div class="input-group">
									<input type="text" {if isset($regBlockDetails) && $regBlockDetails['block_text_color']}value="{$regBlockDetails['block_text_color']|escape:'htmlall':'UTF-8'}"{/if} name="regBlockTextColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_8" style="{if isset($regBlockDetails) && $regBlockDetails['block_text_color']}background-color:{$regBlockDetails['block_text_color']|escape:'htmlall':'UTF-8'}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_8" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group wk_block_22">
				<label class="control-label col-lg-3 required">{l s='Registration block width' mod='marketplace'}</label>
				<div class="col-lg-9">
					<select id="regBlockWidth" class="fixed-width-xl" name="regBlockWidth">
						{foreach $width as $reg_wid}
							<option value="{$reg_wid.id_value|escape:'htmlall':'UTF-8'}" {if isset($regBlockDetails) && $regBlockDetails['width'] == $reg_wid.id_value}selected="selected"{/if}>{$reg_wid.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group wk_block_22">
				<label class="control-label col-lg-3 required">{l s='Registration block position' mod='marketplace'}</label>
				<div class="col-lg-9">
					<select id="regBlockPosition" class="fixed-width-xl" name="regBlockPosition">
						{if $reg_pos}
							{foreach $reg_pos as $subreg_pos}
								<option value="{$subreg_pos.id|escape:'htmlall':'UTF-8'}" {if isset($regBlockDetails) && $regBlockDetails['id_position'] == $subreg_pos.id}selected="selected"{/if}>{$subreg_pos.name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						{else}
							<option value="1">1</option>
							<option value="2">2</option>
						{/if}
					</select>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_2" id="marketplace_login_content_form_submit_btn_8" value="1" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='marketplace'}</button>
		</div>
	</div>

	<div id="fieldset_3_1_7" class="panel">
		<div class="panel-heading"><i class="icon-cogs"></i> {l s='Content block configuration' mod='marketplace'}</div>
		<div class="form-wrapper">
			<div class="alert alert-info">
				{l s='Content block configuration holds feature and terms & condition both block configuration.' mod='marketplace'}
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3 required label-tooltip" data-html="true" data-toggle="tooltip" data-original-title="{l s='If No, feature and terms & conditions both blocks will not display.' mod='marketplace'}">{l s='Enable content block' mod='marketplace'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input data-block="3" type="radio" value="1" class="wk_enable_btn" id="contentPBlockActive_on" name="contentPBlockActive" {if isset($contentPBlockActive) && $contentPBlockActive}checked="checked"{/if}>
						<label for="contentPBlockActive_on">{l s='Yes' mod='marketplace'}</label>
						<input data-block="3" type="radio" value="0" class="wk_enable_btn" id="contentPBlockActive_off" name="contentPBlockActive" {if empty($contentPBlockActive)}checked="checked"{/if}>
						<label for="contentPBlockActive_off">{l s='No' mod='marketplace'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="form-group wk_block_3">
				<label class="control-label col-lg-3 required">{l s='Content block position' mod='marketplace'}</label>
				<div class="col-lg-9">
					<select id="contentPosition" class=" fixed-width-xl" name="contentPosition">
						{foreach $two_block_position as $cont_pos}
							<option value="{$cont_pos.id|escape:'htmlall':'UTF-8'}" {if isset($contentPosition) && $contentPosition == $cont_pos.id}selected="selected"{/if}>{$cont_pos.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_3" id="marketplace_login_content_form_submit_btn_7" value="1" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='marketplace'}</button>
		</div>
	</div>
	<div id="fieldset_4_1_8" class="panel">
		<div class="panel-heading"><i class="icon-cogs"></i> {l s='Feature block configuration' mod='marketplace'}</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required label-tooltip" data-html="true" data-toggle="tooltip" data-original-title="{l s='If No, feature block will not display.' mod='marketplace'}">{l s='Enable feature block' mod='marketplace'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input data-block="4" type="radio" value="1" class="wk_enable_btn" id="featureBlockActive_on" name="featureBlockActive" {if isset($blockFeatureDetail) && $blockFeatureDetail['active']}checked="checked"{/if}>
						<label for="featureBlockActive_on">{l s='Yes' mod='marketplace'}</label>
						<input data-block="4" type="radio" value="0" class="wk_enable_btn" id="featureBlockActive_off" name="featureBlockActive" {if isset($blockFeatureDetail) && !$blockFeatureDetail['active']}checked="checked"{/if}>
						<label for="featureBlockActive_off">{l s='No' mod='marketplace'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="form-group wk_block_4">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller login page feature blocks background color' mod='marketplace'}">{l s='Block background color' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-lg-3">
							<div class="row">
								<div class="input-group">
									<input type="text" {if isset($blockFeatureDetail) && $blockFeatureDetail['block_bg_color']}value="{$blockFeatureDetail['block_bg_color']|escape:'htmlall':'UTF-8'}"{/if} name="featureBgColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_4" style="{if isset($blockFeatureDetail) && $blockFeatureDetail['block_bg_color']}background-color:{$blockFeatureDetail['block_bg_color']|escape:'htmlall':'UTF-8'}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_4" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group wk_block_4">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller login page feature blocks text color' mod='marketplace'}">{l s='Block text color' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-lg-3">
							<div class="row">
								<div class="input-group">
									<input type="text" {if isset($blockFeatureDetail) && $blockFeatureDetail['block_text_color']}value="{$blockFeatureDetail['block_text_color']|escape:'htmlall':'UTF-8'}"{/if} name="featureTextColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_5" style="{if isset($blockFeatureDetail) && $blockFeatureDetail['block_text_color']}background-color:{$blockFeatureDetail['block_text_color']|escape:'htmlall':'UTF-8'}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_5" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group wk_block_4">
				<label class="control-label col-lg-3 required">{l s='Feature block width' mod='marketplace'}</label>
				<div class="col-lg-9">
					<select id="featureBlockWidth" class=" fixed-width-xl" name="featureBlockWidth">
						{foreach $width as $feature_wid}
							<option value="{$feature_wid.id_value|escape:'htmlall':'UTF-8'}" {if isset($blockFeatureDetail) && $blockFeatureDetail['width'] == $feature_wid.id_value}selected="selected"{/if}>{$feature_wid.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group wk_block_4">
				<label class="control-label col-lg-3 required">{l s='Feature block position' mod='marketplace'}</label>
				<div class="col-lg-9">
					<select id="featureBlockPosition" class=" fixed-width-xl" name="featureBlockPosition">
						{if $content_pos}
							{foreach $content_pos as $feature_pos}
								<option value="{$feature_pos.id|escape:'htmlall':'UTF-8'}" {if isset($blockFeatureDetail) && $blockFeatureDetail['id_position'] == $feature_pos.id}selected="selected"{/if}>{$feature_pos.name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						{else}
							<option value="1">1</option>
							<option value="2">2</option>
						{/if}
					</select>
				</div>
			</div>
			<div class="form-group wk_block_4">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Invalid characters: &lt;&gt;;=#{}' mod='marketplace'}">{l s='Page content' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="row">
						{if $total_languages > 1}
						<div class="col-md-10">
						{else}
						<div class="col-md-12">
						{/if}
							{foreach from=$languages item=language}
								{assign var="featureContent_name" value="featureContent_`$language.id_lang`"}
								<div id="featureContentDiv_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="featureContentAll" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
									<textarea
									name="featureContent_{$language.id_lang|escape:'htmlall':'UTF-8'}"
									id="featureContent_{$language.id_lang|escape:'htmlall':'UTF-8'}" cols="2" rows="3"
									class="te autoload_rte rte wk_tinymce" aria-hidden="true">{if isset($smarty.post.$featureContent_name)}{$smarty.post.$featureContent_name|escape:'htmlall':'UTF-8'}{else}{if isset($blockLangContent['content'][{$language.id_lang|escape:'htmlall':'UTF-8'}])}{$blockLangContent['content'][{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}{/if}</textarea>
								</div>
							{/foreach}
						</div>
						{if $total_languages > 1}
						<div class="col-md-2">
							<button type="button" id="featureContent_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								{$current_lang.iso_code|escape:'htmlall':'UTF-8'}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=language}
									<li>
										<a href="javascript:void(0)" onclick="showPageContentLangField('{$language.iso_code|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'});">{$language.name|escape:'htmlall':'UTF-8'}</a>
									</li>
								{/foreach}
							</ul>
						</div>
						{/if}
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_4" id="marketplace_login_content_form_submit_btn_8" value="1" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='marketplace'}</button>
		</div>
	</div>
	<div id="fieldset_5_1_9" class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s='Terms & conditions block configuration' mod='marketplace'}
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3 required label-tooltip" data-html="true" data-toggle="tooltip" data-original-title="{l s='If No, terms & conditions block will not display.' mod='marketplace'}">{l s='Enable terms & conditions block' mod='marketplace'}</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input data-block="5" type="radio" value="1" class="wk_enable_btn" id="tcBlockActive_on" name="tcBlockActive" {if isset($termsConditionDetails) && $termsConditionDetails['active']}checked="checked"{/if}>
						<label for="tcBlockActive_on">{l s='Yes' mod='marketplace'}</label>
						<input data-block="5" type="radio" value="0" class="wk_enable_btn" id="tcBlockActive_off" name="tcBlockActive" {if isset($termsConditionDetails) && !$termsConditionDetails['active']}checked="checked"{/if}>
						<label for="tcBlockActive_off">{l s='No' mod='marketplace'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
			<div class="form-group wk_block_5">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller login page T&C blocks background color' mod='marketplace'}">{l s='Block background color' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-lg-3">
							<div class="row">
								<div class="input-group">
									<input type="text" {if isset($termsConditionDetails) && $termsConditionDetails['block_bg_color']}value="{$termsConditionDetails['block_bg_color']|escape:'htmlall':'UTF-8'}"{/if} name="tcBgColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_9" style="{if isset($termsConditionDetails) && $termsConditionDetails['block_bg_color']}background-color:{$termsConditionDetails['block_bg_color']|escape:'htmlall':'UTF-8'}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_9" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group wk_block_5">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Seller login page T&C blocks text color' mod='marketplace'}">{l s='Block text color' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="form-group">
						<div class="col-lg-3">
							<div class="row">
								<div class="input-group">
									<input type="text" {if isset($termsConditionDetails) && $termsConditionDetails['block_text_color']}value="{$termsConditionDetails['block_text_color']|escape:'htmlall':'UTF-8'}"{/if} name="tcTextColor" class="color mColorPickerInput mColorPicker" data-hex="true" id="color_10" style="{if isset($termsConditionDetails) && $termsConditionDetails['block_text_color']}background-color:{$termsConditionDetails['block_text_color']|escape:'htmlall':'UTF-8'}{/if}">
									<span class="mColorPickerTrigger input-group-addon" id="icp_color_10" style="cursor:pointer;" data-mcolorpicker="true"><img align="absmiddle" style="border:0;margin:0 0 0 3px" src="../img/admin/color.png"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group wk_block_5">
				<label class="control-label col-lg-3 required">{l s='T&C block width' mod='marketplace'}</label>
				<div class="col-lg-9">
					<select id="tcBlockWidth" class=" fixed-width-xl" name="tcBlockWidth">
						{foreach $width as $tc_wid}
							<option value="{$tc_wid.id_value|escape:'htmlall':'UTF-8'}" {if isset($termsConditionDetails) && $termsConditionDetails['width'] == $tc_wid.id_value}selected="selected"{/if}>{$tc_wid.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group wk_block_5">
				<label class="control-label col-lg-3 required">{l s='T&C block position' mod='marketplace'}</label>
				<div class="col-lg-9">
					<select id="tcBlockPosition" class=" fixed-width-xl" name="tcBlockPosition">
						{foreach $content_pos as $tc_pos}
							<option value="{$tc_pos.id|escape:'htmlall':'UTF-8'}" {if isset($termsConditionDetails) && $termsConditionDetails['id_position'] == $tc_pos.id}selected="selected"{/if}>{$tc_pos.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group wk_block_5">
				<label class="control-label col-lg-3">
					<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='	Invalid characters: &lt;&gt;;=#{}' mod='marketplace'}">{l s='T&C content' mod='marketplace'}</span>
				</label>
				<div class="col-lg-9">
					<div class="row">
						{if $total_languages > 1}
						<div class="col-md-10">
						{else}
						<div class="col-md-12">
						{/if}
							{foreach from=$languages item=language}
								{assign var="tcBlockContent_name" value="tcBlockContent_`$language.id_lang`"}
								<div id="tcBlockContentDiv_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="tcBlockContentAll" {if $current_lang.id_lang != $language.id_lang}style="display:none;"{/if}>
									<textarea
									name="tcBlockContent_{$language.id_lang|escape:'htmlall':'UTF-8'}"
									id="tcBlockContent_{$language.id_lang|escape:'htmlall':'UTF-8'}" cols="2" rows="3"
									class="te autoload_rte rte wk_tinymce" aria-hidden="true">{if isset($smarty.post.$tcBlockContent_name)}{$smarty.post.$tcBlockContent_name|escape:'htmlall':'UTF-8'}{else}{if isset($tcBlockContent['content'][{$language.id_lang|escape:'htmlall':'UTF-8'}])}{$tcBlockContent['content'][{$language.id_lang|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}{/if}{/if}</textarea>
								</div>
							{/foreach}
						</div>
						{if $total_languages > 1}
						<div class="col-md-2">
							<button type="button" id="tcBlockContent_btn" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								{$current_lang.iso_code|escape:'htmlall':'UTF-8'}
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								{foreach from=$languages item=language}
									<li>
										<a href="javascript:void(0)" onclick="showTcContentLangField('{$language.iso_code|escape:'htmlall':'UTF-8'}', {$language.id_lang|escape:'htmlall':'UTF-8'});">{$language.name|escape:'htmlall':'UTF-8'}</a>
									</li>
								{/foreach}
							</ul>
						</div>
						{/if}
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submit_5" id="marketplace_login_content_form_submit_btn_9" value="1" type="submit">
			<i class="process-icon-save"></i> {l s='Save' mod='marketplace'}
			</button>
		</div>
	</div>

	{hook h='displayMpAddNewWizard'}
</form>
{block name=script}
<script type="text/javascript">
	var iso = '{$iso|escape:'htmlall':'UTF-8'}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_|escape:'htmlall':'UTF-8'}';
	var ad = '{$ad|escape:'htmlall':'UTF-8'}';

	$(document).ready(function(){
		tinySetup({
			editor_selector :"wk_tinymce"
		});
	});
</script>
{/block}

<script type="text/javascript">
	function showMetaTitleLangField(lang_iso_code, id_lang)
	{
		displayHideLangField('metaTitleLang_btn', 'metaTitleAll', 'metaTitle_', lang_iso_code, id_lang);
		displayHideLangField('metaDescriptionLang_btn', 'metaDescriptionAll', 'metaDescription_', lang_iso_code, id_lang);
	}

	function showTitleLineLangField(lang_iso_code, id_lang)
	{
		displayHideLangField('regTitleLineLang_btn', 'regTitleLineAll', 'regTitleLine_', lang_iso_code, id_lang);
	}

	function showPageContentLangField(lang_iso_code, id_lang)
	{
		displayHideLangField('featureContent_btn', 'featureContentAll', 'featureContentDiv_', lang_iso_code, id_lang);
	}

	function showTcContentLangField(lang_iso_code, id_lang)
	{
		displayHideLangField('tcBlockContent_btn', 'tcBlockContentAll', 'tcBlockContentDiv_', lang_iso_code, id_lang);
	}

	function displayHideLangField(btnField, classField, nameField, lang_iso_code, id_lang)
	{
		$('#'+btnField).html(lang_iso_code + ' <span class="caret"></span>');
		$('.'+classField).hide();
		$('#'+nameField+id_lang).show();
	}
</script>