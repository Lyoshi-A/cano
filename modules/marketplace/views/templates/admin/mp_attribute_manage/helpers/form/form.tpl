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
		{if isset($edit)}
			{l s='Edit combination' mod='marketplace'}
		{else}
			{l s='Create new combination' mod='marketplace'}
		{/if}
	</div>
	<div class="row">
        <div class="col-md-6">
            <a href="{$link->getAdminLink('AdminSellerProductDetail')|escape:'htmlall':'UTF-8'}&updatewk_mp_seller_product&id_mp_product={$mp_id_product|escape:'htmlall':'UTF-8'}" class="btn btn-link wk_padding_none">
                <i class="icon-arrow-left"></i>
                <span>{l s='Back to product' mod='marketplace'}</span>
            </a>
        </div>
        {if isset($edit)}
        <div class="col-md-6 wk_text_right">
            <a href="{$link->getAdminLink('AdminMpAttributeManage')|escape:'htmlall':'UTF-8'}&id={$mp_id_product|escape:'htmlall':'UTF-8'}">
                <button class="btn btn-primary sensitive add" type="button">
                    <i class="icon-plus"></i>
                    {l s='Create new' mod='marketplace'}
                </button>
            </a>
            {hook h="displayMpCombinationListButton"}
        </div>
        {/if}
    </div>
	<div class="form-group">
		<form action="{if isset($edit)}{$current|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}&id_combination={$id_combination|escape:'htmlall':'UTF-8'}{else}{$current|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}&id={$mp_id_product|escape:'htmlall':'UTF-8'}{/if}" method="post" class="defaultForm">
			<div class="row">
				<div class="col-md-11">
					{include file="$wkself/../../views/templates/front/product/combination/_partials/mp-combination-fields.tpl"}
				</div>
			</div>
			<div class="panel-footer">
				<a href="{$link->getAdminLink('AdminSellerProductDetail')|escape:'htmlall':'UTF-8'}&updatewk_mp_seller_product&id_mp_product={$mp_id_product|escape:'htmlall':'UTF-8'}" class="btn btn-default">
					<i class="process-icon-cancel"></i>{l s='Cancel' mod='marketplace'}
				</a>
				<button type="submit" class="btn btn-default pull-right" id="submitCombination" name="submitCombination">
					<i class="process-icon-save"></i>{l s='Save' mod='marketplace'}
				</button>
			</div>
		</form>
	</div>
</div>

{strip}
	{addJsDef path_managecombination = $link->getAdminlink('AdminMpAttributeManage')}
	{addJsDefL name=attribute_req}{l s='Combination attribute cannot be blank.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=attribute_unity_invalid}{l s='Impact on price per unit should be integer.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=req_attr}{l s='Attribute is not selected.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=req_attr_val}{l s='Value is not selected.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=attr_already_selected}{l s='Attribute is already selected.' js=1 mod='marketplace'}{/addJsDefL}
{/strip}


