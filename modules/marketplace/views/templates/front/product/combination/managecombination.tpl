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
{if $logged}
	<div class="wk-mp-block">
		{hook h="DisplayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">
					{if isset($edit)}
						{l s='Edit Combination' mod='marketplace'}
					{else}
						{l s='Add Combination' mod='marketplace'}
					{/if}
				</span>
			</div>
			<form action="{if isset($edit)}{$link->getModuleLink('marketplace', 'managecombination', ['id_combination' => $id_combination])|escape:'htmlall':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'managecombination', ['id' => $mp_id_product])|escape:'htmlall':'UTF-8'}{/if}" method="post" class="form-horizontal">
				<input type="hidden" name="token" id="wk-static-token" value="{$static_token|escape:'htmlall':'UTF-8'}">
				<div class="wk-mp-right-column">
					{block name='wk-form-validation'}
						{include file='module:marketplace/views/templates/front/_partials/validation.tpl'}
					{/block}
					<div class="row">
                        <div class="col-md-6">
                            <a href="{$link->getModuleLink('marketplace', 'updateproduct', ['id_mp_product' => $mp_id_product])|escape:'htmlall':'UTF-8'}" class="btn btn-link wk_padding_none">
                                <i class="material-icons">&#xE5C4;</i>
                                <span>{l s='Back to product' mod='marketplace'}</span>
                            </a>
                        </div>
                        {if isset($edit) && $permissionData.combinationPermission.add}
                        <div class="col-md-6 wk_text_right">
                            <a href="{$link->getModuleLink('marketplace', 'managecombination', ['id' => $mp_id_product])|escape:'htmlall':'UTF-8'}">
                                <button class="btn btn-primary-outline sensitive add" type="button">
                                    <i class="material-icons">&#xE145;</i>
                                    {l s='Create New' mod='marketplace'}
                                </button>
                            </a>
                            {hook h="displayMpCombinationListButton"}
                        </div>
                        {/if}
                    </div>

					{block name='mp-combination-fields'}
						{include file='module:marketplace/views/templates/front/product/combination/_partials/mp-combination-fields.tpl'}
					{/block}

					<div class="form-group row">
						<div class="col-xs-6 col-sm-6 col-md-6">
							<a href="{$link->getModuleLink('marketplace', 'updateproduct', ['id_mp_product' => $mp_id_product])|escape:'htmlall':'UTF-8'}" class="btn wk_btn_cancel wk_btn_extra">
								{l s='CANCEL' mod='marketplace'}
							</a>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6 wk_text_right">
							<button type="submit" class="btn btn-success wk_btn_extra" id="submitStayCombination" name="submitStayCombination">
								{l s='SAVE & STAY' mod='marketplace'}
							</button>
							<button type="submit" class="btn btn-success wk_btn_extra" id="submitCombination" name="submitCombination">
								{l s='SAVE' mod='marketplace'}
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
{else}
	<div class="alert alert-danger">
		{l s='You are logged out. Please login to update combination.' mod='marketplace'}</span>
	</div>
{/if}
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}