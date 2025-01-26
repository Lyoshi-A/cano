{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{block name='supplier_miniature_item'}
<div class="js-supplier supplier{if !empty($supplierClasses)} {$supplierClasses}{/if}">
    {assign var="supplierImg" value=$link->getSupplierImageLink($supplier.id_supplier, 'large_default')}
    <div class="supplier-img">
        <img src="{$supplierImg}" alt="{$supplier.name}" loading="lazy">
    </div>
    <div class="supplier-infos">
        <div class="supplier-title">
            <span class="supplier-icon"></span>
            <span class="supplier-text">
                <a href="{$supplier.url}">{$supplier.name}</a>
{*                {$supplier.text|unescape:'html'|truncate:100:'...' nofilter}*}
            </span>
        </div>
    </div>
    <div class="supplier-products">
{*      <a href="{$supplier.url}">{$supplier.nb_products}</a>*}
{*      <a href="{$supplier.url}">{l s='Profile' d='Shop.Theme.Actions'}</a>*}
      <a target="_blank" href="{$supplier.url}" class="btn btn-default" rel="noreferrer noopener">Profile</a>
    </div>
</div>
{/block}
