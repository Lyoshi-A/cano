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
{block name='header'}
	{include file='module:marketplace/views/templates/front/_partials/header.tpl'}
{/block}
{block name='content'}
	<div class="wk-mp-block">
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Orders' mod='marketplace'}</span>
			</div>
			{if Configuration::get('WK_MP_SELLER_EXPORT')}
				<div class="wk-mp-right-column">
					<div class="wk_text_right wk_product_list">
						<a title="{l s='Export' mod='marketplace'}" href="javascript:;">
							<button class="btn btn-primary btn-sm wk_export_button" type="button">
								<i class="material-icons">file_download</i>
								{l s='Export' mod='marketplace'}
							</button>
						</a>
					</div>
				</div>
			{/if}
			<div class="wk-mp-right-column">
				{block name='mporder_export'}
					{include file="module:marketplace/views/templates/front/order/_partials/mporderexport.tpl"}
				{/block}
				{block name='mporder_list'}
					{include file="module:marketplace/views/templates/front/order/mporderlist.tpl"}
				{/block}
			</div>
		</div>
	</div>
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}