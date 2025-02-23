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

<section class="box">
	<h3>{l s='Track the delivery of your order' mod='marketplace'}</h3>
	{if isset($sellerTrackingData)}
		{foreach $sellerTrackingData as $trackingData}
            <section class="box">
				<h4>
                    {l s='Shop' mod='marketplace'}:
                    <a title="{l s='Visit Shop' mod='marketplace'}" target="_blank" href="{$trackingData.shopstore_link|escape:'htmlall':'UTF-8'}">
                        <span>{$trackingData.shop_name|escape:'htmlall':'UTF-8'}</span>
                    </a>
                </h4>
				<table class="table table-bordered">
					<thead class="thead-default">
						<tr>
							<th width="50%">{l s='Tracking URL:' mod='marketplace'}</th>
							<th>{l s='Tracking Number:' mod='marketplace'}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>{$trackingData.tracking_url|escape:'htmlall':'UTF-8'}</td>
							<td>{$trackingData.tracking_number|escape:'htmlall':'UTF-8'}</td>
						</tr>
					</tbody>
				</table>
			</section>
		{/foreach}
	{/if}
</section>