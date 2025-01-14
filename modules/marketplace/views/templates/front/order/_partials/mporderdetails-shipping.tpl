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

{if isset($smarty.get.tracking)}
	<div class="alert alert-success" id="tracking_number_update_success_message">
		{l s='Tracking details updated successfully' mod='marketplace'}
	</div>
{else if isset($smarty.get.sent)}
	<div class="alert alert-success" id="tracking_number_update_success_message">
		{l s='Tracking email sent to customer successfully' mod='marketplace'}
	</div>
{else if isset($smarty.get.is_order_state_updated)}
	<div class="alert alert-success">
		{l s='Order status updated successfully' mod='marketplace'}
	</div>
{/if}
{if empty($editOrderPermission)}
	<p class="alert alert-danger">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='You do not have permission to update order status and tracking details.' mod='marketplace'}
	</p>
{/if}

<!-- Tab -->
<div class="tabs">
	<ul class="nav nav-tabs">
		{if Configuration::get('WK_MP_SELLER_ORDER_STATUS_CHANGE')}
		<li class="nav-item">
			<a class="nav-link {if empty($smarty.get.tracking)}active{/if}" href="#status" data-toggle="tab">
				<i class="icon-time"></i>
				<span>{l s='Status' mod='marketplace'}</span>
				<span class="badge">({$history|@count|escape:'htmlall':'UTF-8'})</span>
			</a>
		</li>
		{/if}
		{if Configuration::get('WK_MP_SELLER_ORDER_TRACKING_ALLOW')}
		<li class="nav-item">
			<a class="nav-link {if isset($smarty.get.tracking)}active{else if !Configuration::get('WK_MP_SELLER_ORDER_STATUS_CHANGE')}active{/if}" href="#shipping_tracking" data-toggle="tab">
				<i class="icon-truck"></i>
				<span>{l s='Tracking detail' mod='marketplace'}</span>
			</a>
		</li>
		{/if}
		{hook h="displayOrderDetailsExtraTab" id_order=$id_order}
	</ul>
	<div class="tab-content" id="tab-content">
		{if Configuration::get('WK_MP_SELLER_ORDER_STATUS_CHANGE')}
		<div class="tab-pane fade in {if empty($smarty.get.tracking)}active show{/if}" id="status">
			<div class="table-responsive">
				<table class="table history-status row-margin-bottom">
					<tbody>
						{foreach from=$history item=row key=key}
							{if ($key == 0)}
								<tr>
									<td style="background-color:{$row['color']|escape:'htmlall':'UTF-8'}">
										<img src="{$img_url|escape:'htmlall':'UTF-8'}os/{$row['id_order_state']|intval}.gif" width="16" height="16" alt="{$row['ostate_name']|stripslashes}" /></td>
									<td style="background-color:{$row['color']|escape:'htmlall':'UTF-8'};color:{$row['text-color']|escape:'htmlall':'UTF-8'}">
										{$row['ostate_name']|escape:'htmlall':'UTF-8'}
									</td>
									<td style="background-color:{$row['color']|escape:'htmlall':'UTF-8'};color:{$row['text-color']|escape:'htmlall':'UTF-8'}">
									</td>
									<td style="background-color:{$row['color']|escape:'htmlall':'UTF-8'};color:{$row['text-color']|escape:'htmlall':'UTF-8'}">
										{dateFormat date=$row['date_add'] full=true}
									</td>
								</tr>
							{else}
								<tr>
									<td><img src="{$img_url|escape:'htmlall':'UTF-8'}os/{$row['id_order_state']|intval}.gif" width="16" height="16" /></td>
									<td>{$row['ostate_name']|escape:'htmlall':'UTF-8'}</td>
									<td></td>
									<td>{dateFormat date=$row['date_add'] full=true}</td>
								</tr>
							{/if}
						{/foreach}
					</tbody>
				</table>
			</div>
			<!-- Change status form -->
			<form action="{$update_url_link|escape:'htmlall':'UTF-8'}" method="post" class="form-horizontal well" id="change_order_status_form">
				<div class="row">
					<div class="col-md-8 form-group" id="select_ele_id">
						<select id="id_order_state" class="chosen form-control form-control-select" name="id_order_state" style="width:100%;">
						{foreach from=$states item=state}
							<option value="{$state['id_order_state']|intval}"{if $state['id_order_state'] == $currentState} selected="selected" disabled="disabled"{/if}>{$state['name']|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
						</select>
						<input type="hidden" name="id_order_state_checked" class="id_order_state_checked" value="{$currentState|escape:'htmlall':'UTF-8'}" />
					</div>
					<div class="col-md-4">
						<button type="submit" name="submitState" class="btn btn-primary" id="update_order_status">
							<span>{l s='Update status' mod='marketplace'}</span>
						</button>
					</div>
				</div>
			</form>
		</div>
		{/if}
		{if Configuration::get('WK_MP_SELLER_ORDER_TRACKING_ALLOW')}
		<div class="tab-pane fade in {if isset($smarty.get.tracking)}active show{else if !Configuration::get('WK_MP_SELLER_ORDER_STATUS_CHANGE')}active show{/if}" id="shipping_tracking">
			<form action="{$update_url_link|escape:'htmlall':'UTF-8'}" method="post" class="form-horizontal well" id="change_order_status_form">
				<div class="form-group">
					<label class="label-control">{l s='Tracking Number' mod='marketplace'}</label>
					<div>
						<input type="text" class="form-control" name="tracking_number" value="{if isset($smarty.post.tracking_number)}{$smarty.post.tracking_number|escape:'htmlall':'UTF-8'}{else if isset($trackingInfo.tracking_number)}{$trackingInfo.tracking_number|escape:'htmlall':'UTF-8'}{/if}" />
					</div>
				</div>
				<div class="form-group">
					<label class="label-control">{l s='Tracking URL' mod='marketplace'}</label>
					<div>
						<input type="text" class="form-control" name="tracking_url" value="{if isset($smarty.post.tracking_url)}{$smarty.post.tracking_url|escape:'htmlall':'UTF-8'}{else if isset($trackingInfo.tracking_url)}{$trackingInfo.tracking_url|escape:'htmlall':'UTF-8'}{/if}"/>
					</div>
					{if Configuration::get('WK_MP_TRACKING_NUMBER_IN_URL')}
						<p class="help-block">
							<i>{l s='Type @ where the tracking number should appear. It will be automatically replaced by the tracking number.' mod='marketplace'}</i>
						</p>
					{/if}
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary" name="submitTracking">
					{l s='Submit' mod='marketplace'}
					</button>
					{if isset($trackingInfo)}
						{if $trackingInfo.tracking_url && $trackingInfo.tracking_number}
							<button type="submit" class="btn btn-primary" name="submitTrackingMail">
							{l s='Send mail' mod='marketplace'}
							</button>
						{/if}
					{/if}
				</div>
			</form>
		</div>
		{/if}
		{hook h="displayOrderDetailsExtraTabContent" id_order=$id_order}
	</div>
</div>