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

{if isset($customer_id) && $customer_id == 0}
<div class="alert alert-danger">
	<p>{l s='This seller has been removed by admin from prestashop.' mod='marketplace'}</p>
</div>
{/if}
<div id="mp-container-customer">
	<div class="row">
		<div class="col-lg-6">
			<div class="panel clearfix">
				{if isset($mp_seller)}
					<div class="panel-heading">
						<i class="icon-user"></i>
						{$mp_seller.seller_firstname|escape:'htmlall':'UTF-8'} {$mp_seller.seller_lastname|escape:'htmlall':'UTF-8'} -
						<a href="mailto:{$mp_seller.business_email|escape:'htmlall':'UTF-8'}">
							<i class="icon-envelope"></i>
							{$mp_seller.business_email|escape:'htmlall':'UTF-8'}
						</a>
						<div class="panel-heading-action">
							<a href="{$current|escape:'htmlall':'UTF-8'}&amp;updatewk_mp_seller&amp;id_seller={$mp_seller.id_seller|intval}&amp;token={$token|escape:'htmlall':'UTF-8'}" class="btn btn-default" style="width: 90px;">
								<i class="icon-edit"></i>
								{l s='Edit' mod='marketplace'}
							</a>
						</div>
					</div>
					<div class="form-horizontal">
						<div class="row">
							<label class="control-label col-lg-4">{l s='Social title' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{if $gender->name}{$gender->name|escape:'htmlall':'UTF-8'}{else}{l s='Unknown' mod='marketplace'}{/if}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-4">{l s='Registration date' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{dateFormat date=$mp_seller.date_add full=1}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-4">{l s='Unique shop name' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">
									{if $mp_seller.active}
										<a href="{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $mp_seller.mp_shop_rewrite])|escape:'htmlall':'UTF-8'}" target="_balnk" title="{l s='View shop' mod='marketplace'}">
											{$mp_seller.shop_name_unique|escape:'htmlall':'UTF-8'}
										</a>
									{else}
										{$mp_seller.shop_name_unique|escape:'htmlall':'UTF-8'}
									{/if}
								</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-4">{l s='Default language' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$mp_seller.default_lang|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-4">{l s='Shop name' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$mp_seller.shop_name|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-4">{l s='Phone' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$mp_seller.phone|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						{if $mp_seller.fax != ''}
						<div class="row">
							<label class="control-label col-lg-4">{l s='Fax' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$mp_seller.fax|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						{/if}
						{if $mp_seller.tax_identification_number != ''}
						<div class="row">
							<label class="control-label col-lg-4">{l s='Tax identification number' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$mp_seller.tax_identification_number|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						{/if}
						{if $mp_seller.address != ''}
						<div class="row">
							<label class="control-label col-lg-4">{l s='Address' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$mp_seller.address|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						{/if}
						{if $mp_seller.postcode != ''}
						<div class="row">
							<label class="control-label col-lg-4">{l s='Zip/Postal code' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$mp_seller.postcode|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						{/if}
						{if $mp_seller.city != ''}
						<div class="row">
							<label class="control-label col-lg-4">{l s='City' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$mp_seller.city|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						{/if}
						{if isset($mp_seller.country)}
						<div class="row">
							<label class="control-label col-lg-4">{l s='Country' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$mp_seller.country|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						{/if}
						{if isset($mp_seller.state)}
						<div class="row">
							<label class="control-label col-lg-4">{l s='State' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$mp_seller.state|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						{/if}
						<div class="row">
							<label class="control-label col-lg-4">{l s='Rating' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">
									{if isset($avg_rating)}
										<span class="avg_rating"></span>
									{else}
										{l s='No rating' mod='marketplace'}
									{/if}
								</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-4">{l s='Seller logo' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">
									<img class="img-thumbnail" width="100" height="100" src="{if isset($seller_img_path)}{$seller_img_path|escape:'htmlall':'UTF-8'}?timestamp={$timestamp|escape:'htmlall':'UTF-8'}{else}{$seller_default_img_path|escape:'htmlall':'UTF-8'}?timestamp={$timestamp|escape:'htmlall':'UTF-8'}{/if}" alt="{if isset($seller_img_path)}{l s='Seller profile image' mod='marketplace'}{else}{l s='Default image' mod='marketplace'}{/if}"/>
								</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-4">{l s='Shop logo' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">
									<img class="img-thumbnail" width="100" height="100" src="{if isset($shop_img_path)}{$shop_img_path|escape:'htmlall':'UTF-8'}?timestamp={$timestamp|escape:'htmlall':'UTF-8'}{else}{$shop_default_img_path|escape:'htmlall':'UTF-8'}?timestamp={$timestamp|escape:'htmlall':'UTF-8'}{/if}" alt="{if isset($shop_img_path)}{l s='Shop logo' mod='marketplace'}{else}{l s='Default image' mod='marketplace'}{/if}"/>
								</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-4">{l s='Status' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">
									{if $mp_seller.active}
										<span class="label label-success">
											<i class="icon-check"></i>
											{l s='Active' mod='marketplace'}
										</span>
									{else}
										<span class="label label-danger">
											<i class="icon-remove"></i>
											{l s='Inactive' mod='marketplace'}
										</span>
									{/if}
								</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-4">{l s='Seller products' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<a href="{$link->getAdminLink('AdminSellerProductDetail')|addslashes}&amp;id_seller={$mp_seller.id_seller|intval}" class="btn btn-default" target="_blank"><i class="icon-search-plus"></i> {l s='View products' mod='marketplace'}</a>
							</div>
						</div>
						{hook h='displayAdminSellerDetailViewBottom'}
					</div>
				{/if}
			</div>
			{hook h='displayAdminSellerDetailViewLeftColumn'}
		</div>
		<div class="col-lg-6">
			<div class="panel clearfix">
				<div class="panel-heading">
					<i class="icon-money"></i>
					{l s='Payment Account details' mod='marketplace'}
				</div>
				<div class="form-horizontal">
					{if isset($payment_detail)}
						<div class="row">
							<label class="control-label col-lg-4">{l s='Paymet method' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$payment_detail.payment_mode|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-4">{l s='Account details' mod='marketplace'}:</label>
							<div class="col-lg-8">
								<p class="form-control-static">{$payment_detail.payment_detail|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
					{else}
						<p class="text-muted text-center">{l s='No account details available' mod='marketplace'}</p>
					{/if}
				</div>
			</div>
			{hook h='displayAdminSellerDetailViewRightColumn'}
		</div>
	</div>
</div>
{if isset($avg_rating)}
<script type="text/javascript">
	$('.avg_rating').raty(
	{
		path: '{$modules_dir|escape:'htmlall':'UTF-8'}/marketplace/views/img',
		score: {$avg_rating|escape:'htmlall':'UTF-8'},
		readOnly: true,
	});
</script>
{/if}
