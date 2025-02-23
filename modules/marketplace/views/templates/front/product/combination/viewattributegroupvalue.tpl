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
		{hook h="displayMpMenu"}
		<div class="wk-mp-content">
			<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">
					{if isset($attribute_group_name)}{$attribute_group_name|escape:'htmlall':'UTF-8'}{else}{l s='Product Attribute Value' mod='marketplace'}{/if}
				</span>
			</div>
			<div class="wk-mp-right-column">
				{if isset($smarty.get.value_created)}
					<div class="alert alert-success">{l s='Attribute Value created successfully.' mod='marketplace'}</div>
				{/if}
				{if isset($smarty.get.updated)}
					<div class="alert alert-success">{l s='Attribute Value updated successfully.' mod='marketplace'}</div>
				{/if}
				{if isset($smarty.get.deleted)}
					<div class="alert alert-success">{l s='Attribute Value deleted successfully.' mod='marketplace'}</div>
				{/if}
				<div class="wk_product_list">
					<p class="wk_text_right">
						<a href="{$link->getModuleLink('marketplace', 'createattributevalue',['id_group' => $id_group])|escape:'htmlall':'UTF-8'}">
							<button class="btn btn-primary btn-sm" type="button">
								<i class="material-icons">&#xE145;</i>
								{l s='Add new value' mod='marketplace'}
							</button>
						</a>
					</p>
					<div class="table-responsive">
						<table class="table table-striped" {if isset($value_set) && $value_set}id="wk_datatable_list"{/if}>
							<thead>
								<tr>
									<th>{l s='#' mod='marketplace'}</th>
									<th>{l s='ID' mod='marketplace'}</th>
									<th>{l s='Value' mod='marketplace'}</th>
									{if isset($is_color)}
										<th>{l s='Color' mod='marketplace'}</th>
									{/if}
									<th>{l s='Action' mod='marketplace'}</th>
								</tr>
							</thead>
							<tbody>
							{if isset($value_set) && $value_set}
								{assign var=num value=1}
								{foreach $value_set as $value_set_each}
									<tr>
										<td>{$num|escape:'htmlall':'UTF-8'}</td>
										<td>{$value_set_each.id|escape:'htmlall':'UTF-8'}</td>
										<td>{$value_set_each.name|escape:'htmlall':'UTF-8'}</td>
										{if isset($is_color)}
											<td>
											<!-- code for color texture -->
												{if $value_set_each.imageTextureExists}
													<img src="{$img_col_dir|escape:'htmlall':'UTF-8'|cat:$value_set_each.id|cat:'.jpg'}" alt="" class="color_box" />
												{else} {*TEXTURE*}
													<div class="color_box" style="background-color: {$value_set_each.color|escape:'htmlall':'UTF-8'}"></div>
												{/if}

											</td>
										{/if}
										<td>
											<a class="edit_but" title="{l s='Edit' mod='marketplace'}" edit="{$value_set_each.editable|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('marketplace', 'createattributevalue',['id_group'=>$id_group,'id_attribute'=>$value_set_each.editable])|escape:'htmlall':'UTF-8'}">
												<i class="material-icons">&#xE254;</i>
											</a>
											<a class="del_attr_val" title="{l s='Delete' mod='marketplace'}" edit="{$value_set_each.editable|escape:'htmlall':'UTF-8'}" href="{$link->getModuleLink('marketplace', 'viewattributegroupvalue',['id_group'=>$id_group,'id_attribute'=>$value_set_each.editable, 'delete_attribute_value'=>1])|escape:'htmlall':'UTF-8'}">
												<i class="material-icons">&#xE872;</i>
											</a>
										</td>
									</tr>
									{$num = $num + 1}
								{/foreach}
							{else}
								<tr>
									{if isset($is_color)}<td colspan="5">{else}<td colspan="4">{/if}
										{l s='No data found' mod='marketplace'}
									</td>
								</tr>
							{/if}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
{/if}
{/block}
{block name="footer"}
	{include file='module:marketplace/views/templates/front/_partials/footer.tpl'}
{/block}