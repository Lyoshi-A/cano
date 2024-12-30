{*
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
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

<div class="bootstrap" style="display:none;">
    <div class="module_error alert alert-danger" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span></span>
    </div>
    <div class="module_confirmation conf confirm alert alert-success" style="display:none;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
            <span></span>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='Assign carriers to admin products' mod='marketplace'}
    </div>
    <div class="form-wrapper">
        <button class="btn btn-info" type="submit" name="assign_shipping" id="assign_shipping">
            {l s='Assign carriers to admin products' mod='marketplace'}
        </button>
        <div id="wk-loader" style="display: inline-block;margin-left: 5px;"></div>
        <div class="alert alert-info" style="margin-top: 10px;">
            {l s='Using this option, You can assign all the admin carriers to all the admin products. Seller carriers will not be assigned on admin products.' mod='marketplace'}
            <br>
            {l s='In case, if you have selected any specific carrier on any of the admin product, then it will be replaced by all the available carriers of admin.' mod='marketplace'}
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='Admin default carrier' mod='marketplace'}
    </div>
    <div class="panel-body">
        {if isset($all_ps_carriers_arr) && $all_ps_carriers_arr}
            <form method="post" class="form-horizontal">
                <div class="alert alert-info" style="margin-top: 10px;">
                    {l s='If no seller carrier applied on products then Admin default carrier will applied on seller products.' mod='marketplace'}
                </div>
                <div class="form-group">
                    <label for="default_shipping" class="control-label col-lg-2 text-right">
                        <span class="label-tooltip" title="" data-html="true" data-toggle="tooltip" data-original-title="{l s='If No seller carrier applied on products then Admin default carrier will applied on seller products.' mod='marketplace'}"> {l s='Select default carrier' mod='marketplace'} </span>
                    </label>
                    <div class="col-lg-10">
                        <div style="max-height:155px;overflow:auto;">
                        {foreach $all_ps_carriers_arr as $carrier}
                            <div>
                                <div class="shipping_checkbox">
                                    <input type="checkbox" name="default_shipping[]" id="default_shipping_{$carrier.id_reference|escape:'htmlall':'UTF-8'}" value="{$carrier.id_reference|escape:'htmlall':'UTF-8'}"
                                    {if in_array($carrier.id_reference, $admin_def_shipping)}checked="checked"{/if}>
                                </div>
                                <div class="checkbox_name">
                                    <label for="default_shipping_{$carrier.id_reference|escape:'htmlall':'UTF-8'}" style="font-weight: normal;">
                                        {$carrier.name|escape:'htmlall':'UTF-8'}
                                    </label>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                        {/foreach}
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button class="btn btn-default pull-right" id="submit_admin_default_shipping" name="submit_admin_default_shipping" value="1" type="submit">
                        <i class="process-icon-save"></i> {l s='Save' mod='marketplace'}
                    </button>
                </div>
            </form>
        {else}
            <div class="alert alert-info">{l s='You do not have any active carrier(s).' mod='marketplace'}</div>
        {/if}
    </div>
</div>