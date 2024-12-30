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

{if isset($smarty.get.updated)}
		<p class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Profile updated successfully' mod='marketplace'}
		</p>
{else if isset($smarty.get.created_conf)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Created successfully' mod='marketplace'}
	</p>
{else if isset($smarty.get.edited_conf)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Updated successfully' mod='marketplace'}
	</p>
{else if isset($smarty.get.edited_withdeactive)}
	<p class="alert alert-info">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Product has been updated successfully but it has been deactivated. Please wait till the approval from admin.' mod='marketplace'}
	</p>
{else if isset($smarty.get.edited_qty) && isset($smarty.get.edited_price)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Only quantity and price have been updated successfully.' mod='marketplace'}
	</p>
{else if isset($smarty.get.edited_qty)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Only quantity has been updated successfully.' mod='marketplace'}
	</p>
{else if isset($smarty.get.edited_price)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Only price has been updated successfully.' mod='marketplace'}
	</p>
{else if isset($smarty.get.duplicate)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Product duplicated successfully.' mod='marketplace'}
	</p>
{else if isset($smarty.get.deleted)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Deleted successfully.' mod='marketplace'}
	</p>
{else if isset($smarty.get.status_updated)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Status updated successfully' mod='marketplace'}
	</p>
{else if isset($smarty.get.error)}
	<p class="alert alert-danger">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='There is some error.' mod='marketplace'}
	</p>
{else if isset($smarty.get.pack_permission_error)}
	<p class="alert alert-danger">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='You do not have permission to edit pack products.' mod='marketplace'}
	</p>
{else if isset($smarty.get.virtual_permission_error)}
	<p class="alert alert-danger">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='You do not have permission to edit virtual products.' mod='marketplace'}
	</p>
{else if isset($smarty.get.add)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Combination created successfully.' mod='marketplace'}
	</p>
{else if isset($smarty.get.update)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Combination updated successfully.' mod='marketplace'}
	</p>
{else if isset($smarty.get.createmanuf)}
	<div class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Brand created successfully.' mod='marketplace'}
	</div>
{else if isset($smarty.get.updatemanuf)}
	<div class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Brand updated successfully.' mod='marketplace'}
	</div>
{/if}

{if (isset($editProductPermissionNotAllow) || isset($editPermissionNotAllow)) && isset($edit)}
	<p class="alert alert-danger">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{if isset($qtyAllow)}
			{l s='You can edit only quantity. You do not have permission to edit other fields.' mod='marketplace'}
		{else}
			{l s='You do not have permission to edit this.' mod='marketplace'}
		{/if}
	</p>
{else if isset($editPermissionNotAllow)}
	<p class="alert alert-danger">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{if isset($qtyAllow) && isset($priceAllow)}
			{l s='You can only edit the product quantity and price. You do not have permission to edit other fields.' mod='marketplace'}
		{else if isset($qtyAllow)}
			{l s='You can only edit the product quantity. You do not have permission to edit other fields.' mod='marketplace'}
		{else if isset($priceAllow)}
			{l s='You can only edit the product price. You do not have permission to edit other fields.' mod='marketplace'}
		{else}
			{l s='You do not have permission to edit this.' mod='marketplace'}
		{/if}
	</p>
{/if}

{if isset($smarty.get.logo_delete_success)}
	{if $smarty.get.logo_delete_success == 1}
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Logo deleted successfully.' mod='marketplace'}
		</div>
	{/if}
{/if}