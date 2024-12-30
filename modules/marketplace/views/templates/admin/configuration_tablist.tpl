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

<div id="bar-menu">
    <div class="col-sm-2">
        <div class="list-group" v-on:click.prevent>
            {if isset($tab_name)}
                {foreach from=$tab_name item=tab}
                    <a href="javascript:void(0)" class="list-group-item"
                        v-bind:class="{ 'active': isActive('{$tab.tab_name|escape:'htmlall':'UTF-8'}') }"
                        v-on:click="makeActive('{$tab.tab_name|escape:'htmlall':'UTF-8'}')"
                        class="list-group-item">
                        <i class="{$tab.icon|escape:'htmlall':'UTF-8'}"></i>
                        {$tab.label|escape:'htmlall':'UTF-8'}
                    </a>
                {/foreach}
            {/if}
        </div>
    </div>
    {foreach from=$form key=index item=forms}
        <div id="{$index|escape:'htmlall':'UTF-8'}" class="col-sm-10 wk_bar-menu wk_display_none">
            {$forms nofilter}
        </div>
    {/foreach}
</div>
<style>
    .bootstrap #dashboard .data_list li, .bootstrap .list-group-item {
        border-bottom: none;
    }
</style>