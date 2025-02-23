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

<div class="clearfix modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="col-xs-12 col-sm-7 color3">
        <h4>{l s='Settlement transaction' mod='marketplace'} </h4>
    </div>
    <div class="col-xs-12 col-sm-4 h4 color3 wk_padding_none">
        <div class="kpi-content" style="padding-left:30px;">
            <i class="icon-calendar-empty" style="font-size: 15px;"></i>
            <span class="">{l s='Date' mod='marketplace'}</span>
            <span class="value">{if isset($objTransaction->date_add)}{$objTransaction->date_add|date_format:"%D"|escape:'htmlall':'UTF-8'}{/if}</span>
        </div>
    </div>
</div>
<div class="clearfix modal-body">
    <div class="clearfix form-group">
        <label class="col-lg-4 control-label">{l s='Payment method:' mod='marketplace'}</label>
        <div class="col-lg-5 control-label">
            <p class="form-control-static">
                {if isset($objTransaction->payment_method) && $objTransaction->payment_method}{$objTransaction->payment_method|escape:'htmlall':'UTF-8'}{else}{l s='N/A' mod='marketplace'}{/if}
            </p>
        </div>
    </div>
    <div class="clearfix form-group">
        <label class="col-lg-4 control-label">{l s='Payment details:' mod='marketplace'}</label>
        <div class="col-lg-5 control-label">
            <p class="form-control-static">
                {if isset($payment_mode_details) && $payment_mode_details}{$payment_mode_details|escape:'htmlall':'UTF-8'}{else}N/A{/if}
            </p>
        </div>
    </div>
    <div class="clearfix form-group">
        <label class="col-lg-4 control-label">{l s='Transaction ID:' mod='marketplace'}</label>
        <div class="col-lg-5 control-label">
            <p class="form-control-static">
                {if isset($objTransaction->id_transaction) && $objTransaction->id_transaction}{$objTransaction->id_transaction|escape:'htmlall':'UTF-8'}{else}{l s='N/A' mod='marketplace'}{/if}
            </p>
        </div>
    </div>
    <div class="clearfix form-group">
        <label class="col-lg-4 control-label">{l s='Remark:' mod='marketplace'}</label>
        <div class="col-lg-5 control-label">
            <p class="form-control-static">
                {if isset($objTransaction->remark) && $objTransaction->remark}{$objTransaction->remark|escape:'htmlall':'UTF-8'}{else}{l s='N/A' mod='marketplace'}{/if}
            </p>
        </div>
    </div>
    <div class="clearfix form-group">
        <label class="col-lg-4 control-label required">{l s='Amount:' mod='marketplace'}</label>
        <div class="col-lg-5 control-label">
            <p class="form-control-static">
                {if isset($amount)}{$amount|escape:'htmlall':'UTF-8'}{/if}
            </p>
        </div>
    </div>
    {hook h='displayExtraTransactionDetail' id_transaction_history=$objTransaction->id}

    {if !isset($frontcontroll)}
        {if $objTransaction->seller_receive > 0 && $objTransaction->status != 3}
            <div class="clearfix">
                <form method="POST" action="">
                    <input
                        type="hidden"
                        name="wk_id_settlement"
                        value="{if isset($objTransaction->id)}{$objTransaction->id|escape:'htmlall':'UTF-8'}{/if}"/>
                    {if isset($objTransaction->status) && $objTransaction->status == 2}
                        <button
                            disabled="disabled"
                            type="submit"
                            class="btn btn-info pull-right"
                            name="wk_settlement_canceled">{l s='Canceled' mod='marketplace'}
                        </button>
                    {else}
                        <button
                            type="submit"
                            class="btn btn-info pull-right"
                            name="wk_settlement_cancel">{l s='Cancel settlement' mod='marketplace'}
                        </button>
                    {/if}
                </form>
            </div>
        {/if}
    {/if}
</div>