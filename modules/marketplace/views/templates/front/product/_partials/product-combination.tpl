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

<div class="box-account box-recent">
	<p class="wk_text_right">
		{if isset($controller) && $controller == 'admin'}
			<a href="{$link->getAdminLink('AdminMpAttributeManage')|escape:'htmlall':'UTF-8'}&id={$id|escape:'htmlall':'UTF-8'}">
				<button class="btn btn-info sensitive add" type="button">
					<i class="icon-plus"></i>&nbsp;
					{l s='Create Combination' mod='marketplace'}
				</button>
			</a>
			<a  class="generate_combination" href="{$link->getAdminLink('AdminMpGenerateCombination')|escape:'htmlall':'UTF-8'}&id_mp_product={if isset($id)}{$id|escape:'htmlall':'UTF-8'}{elseif isset($mp_id_product)}{$mp_id_product|escape:'htmlall':'UTF-8'}{/if}">
				<button class="btn btn-info sensitive add" type="button">
					<i class="icon-plus"></i>&nbsp;
					{l s='Generate Combination' mod='marketplace'}
				</button>
			</a>
		{else}
			{if !isset($editPermissionNotAllow) && $permissionData.combinationPermission.add}
				<a href="{$link->getModuleLink('marketplace', 'managecombination', ['id' => $id])|escape:'htmlall':'UTF-8'}">
					<button class="btn btn-primary sensitive add form-group" type="button">
						<i class="material-icons">&#xE145;</i>
						{l s='Create Combination' mod='marketplace'}
					</button>
				</a>
			{/if}
			{if !isset($editPermissionNotAllow) && $permissionData.combinationPermission.add}
				<a  class="generate_combination" href="{if isset($id)}{$link->getModuleLink('marketplace', 'generatecombination', ['id_mp_product' => $id])|escape:'htmlall':'UTF-8'}{elseif isset($mp_id_product)}{$link->getModuleLink('marketplace', 'generatecombination', ['id_mp_product' => $mp_id_product])|escape:'htmlall':'UTF-8'}{/if}">
					<button class="btn btn-primary sensitive add form-group" type="button">
						<i class="material-icons">&#xE145;</i>
						{l s='Generate Combination' mod='marketplace'}
					</button>
				</a>
			{/if}
		{/if}

		{hook h="displayMpCombinationListButton"}
	</p>

	<div class="box-content" id="wk_product_combination">
		<div class="table-responsive clearfix">
		<table class="table clearfix">
			<thead>
				<tr>
					<th style="width:20%;">{l s='Attributes' mod='marketplace'}</th>
					{if Configuration::get('PS_STOCK_MANAGEMENT')}
					<th><center>{l s='Quantity' mod='marketplace'}</center></th>
					{/if}
					<th><center>{l s='Impact on price' mod='marketplace'}</center></th>
					<th><center>{l s='Impact on weight' mod='marketplace'}</center></th>
					<th>{l s='Reference' mod='marketplace'}</th>
					{*--- Hook added for ps combination activate/deactivate module ---*}
					{hook h="displayMpCombinationListBeforeActionButtonColumn"}
					<th><center>{l s='Actions' mod='marketplace'}</center></th>
				</tr>
			</thead>
			<tbody>
				{if isset($combination_detail) && $combination_detail}
					{foreach $combination_detail as $wkRowKey => $combination_val}
						<tr id="combination_{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}" class="{if $combination_val.mp_default_on}highlighted{/if} combination">
							<td>{$combination_val.attribute_designation|escape:'htmlall':'UTF-8'|rtrim:' '|rtrim:','}</td>
							{if Configuration::get('PS_STOCK_MANAGEMENT')}
								<td>
								<center>
								{if (!isset($editPermissionNotAllow) && $permissionData.combinationPermission.edit) || isset($qtyAllow)}
									<input type="text"
									name="combination_qty_{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}"
									id="combination_qty_{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}"
									value="{$combination_val.mp_quantity|escape:'htmlall':'UTF-8'}"
									data-id-combination="{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}"
									class="form-control wk-combi-list-qty">
								{else}
									{$combination_val.mp_quantity|escape:'htmlall':'UTF-8'}
								{/if}
								</center>
							{/if}
							</td>
							<td><center>{$combination_val.mp_price|escape:'htmlall':'UTF-8'}</center></td>
							<td><center>{$combination_val.mp_weight|escape:'htmlall':'UTF-8'}{$ps_weight_unit|escape:'htmlall':'UTF-8'}</center></td>
							<td>{$combination_val.mp_reference|escape:'htmlall':'UTF-8'}</td>
							{*--- Hook added for ps combination activate/deactivate module ---*}
							{hook h="displayMpCombinationListBeforeActionButton" permissionData=$permissionData idProductAttribute=$combination_val.id_product_attribute}

							{if !isset($backendController)}
								<td>
									<a href="{$link->getModuleLink('marketplace', 'managecombination', ['id_combination' => $combination_val.id_product_attribute])|escape:'htmlall':'UTF-8'}" title="{l s='Edit' mod='marketplace'}">
										<i class="material-icons">&#xE254;</i>
									</a>
									{if !isset($editPermissionNotAllow) && $permissionData.combinationPermission.delete}
										<a href="" title="{l s='Delete' mod='marketplace'}" class="delete_attribute" data-id="{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}" id="delete_attribute_{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}" data-default={if $combination_val.mp_default_on}1{else}0{/if}>
											<i class="material-icons">&#xE872;</i>
										</a>
									{/if}
									{if !isset($editPermissionNotAllow) && $permissionData.combinationPermission.edit}
										{if $combination_val.mp_default_on}
											<input type="hidden" id="default_product_attribute" value="{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}">
										{/if}
										<a href="" title="{l s='Make default' mod='marketplace'}" data-controller="front" data-status="{if isset($combination_val.active)}{$combination_val.active|escape:'htmlall':'UTF-8'}{else}1{/if}" class="default_attribute {if $combination_val.mp_default_on}wk_display_none{/if}" data-id="{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}" id="default_attribute_{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}">
											<i class="material-icons">&#xE838;</i>
										</a>
									{/if}
								</td>
							{else}
								<td class="center text-right">
									<div class="btn-group">
										<a class="btn btn-default" href="{$link->getAdminLink('AdminMpAttributeManage')|escape:'htmlall':'UTF-8'}&id_combination={$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}">
											<i class="icon-edit"></i> {l s='Edit' mod='marketplace'}
										</a>
										{if $combination_val.mp_default_on}
											<input type="hidden" id="default_product_attribute" value="{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}">
										{/if}
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="padding: 15px;{if !$combination_val.mp_default_on}margin-left: 1px;{/if}">
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li>
												<a href="" class="delete_attribute" data-id="{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}" id="delete_attribute_{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}" data-default={if $combination_val.mp_default_on}1{else}0{/if}>
													<i class="icon-trash"></i>
													{l s='Delete' mod='marketplace'}
												</a>
											</li>
											<li>
												<a data-controller="admin" href="" title="{l s='Default' mod='marketplace'}" class="default_attribute" data-status="{$combination_val.active|escape:'htmlall':'UTF-8'}" data-id="{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}" id="default_attribute_{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}" {if $combination_val.mp_default_on}style="display:none;"{/if}>
												<i class="icon-star"></i> {l s='Make default' mod='marketplace'}
												</a>
											</li>
										</ul>
									</div>
								</td>
							{/if}
						</tr>
						<div class="left basciattr_update" id="attribute_div_{$combination_val.id_product_attribute|escape:'htmlall':'UTF-8'}">
						</div>
					{/foreach}
				{else}
					<tr>
						<td colspan="7">
							<div class="full left planlistcontent call" style="text-align:center;">{l s='No combination available for this product' mod='marketplace'}</div>
						</td>
					</tr>
				{/if}
			</tbody>
		</table>
		</div>
	</div>
</div>