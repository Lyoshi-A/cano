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
{extends file=$layout}

{block name='content'}
  <section id="main">
    {block name='suppliers_header'}
      <div id="suppliers_header" class="suppliers-header">
        <div class="suppliers-header-main">
          <div class="suppliers-header-top">
            <img class="im2" src="{$urls["img_ps_url"]}cms/f380c1c1a95978d5eb4578eacab81bde_.png" alt="f380c1c1a95978d5eb4578eacab81bde_.png" />
          </div>
          <div class="suppliers-header-text">
            <h1 class="name">OUR ARTISTS</h1>
            <p class="description">Explore our artist library to find the perfect piece that resonates with your style and inspires your space</p>
          </div>
        </div>
        <div class="suppliers-header-bottom">
          <div class="text-info">
            <p class="text2">“</p>
            <p class="text3">First charity-oriented online marketplace connecting young creators with art enthusiasts all around the world</p>
          </div>
          {assign var="cmsLink2" value=$link->getCMSLink(5)}
          <img class="im7" src="{$urls["img_ps_url"]}cms/f99fdac17a60225d96cfd6f76ac7ee6f_.png" alt="f99fdac17a60225d96cfd6f76ac7ee6f_.png" />
        </div>
      </div>
{*      <h1>{l s='Suppliers' d='Shop.Theme.Catalog'}</h1>*}
    {/block}

    {block name='subcategory_list'}
      {if isset($subcategories) && $subcategories|@count > 0}
        {include file='catalog/_partials/subcategories.tpl' subcategories=$subcategories}
      {/if}
    {/block}

    {block name='supplier_miniature'}
      <section id="suppliers">
        {if $brands|count}

          {block name='supplier_list_top'}
            {include file='catalog/_partials/suppliers-top.tpl' listing=$brands}
          {/block}

{*          {block name='supplier_list_active_filters'}*}
{*            <div class="hidden-sm-down">*}
{*              {$listing.rendered_active_filters nofilter}*}
{*            </div>*}
{*          {/block}*}

          {block name='supplier_list'}
            {include file='catalog/_partials/suppliers.tpl' listing=$brands supplierClass="col-xs-12 col-sm-6 col-xl-4"}
          {/block}

{*          {block name='supplier_list_bottom'}*}
{*            {include file='catalog/_partials/suppliers-bottom.tpl' listing=$brands}*}
{*          {/block}*}

        {else}
          <div id="js-supplier-list-top"></div>

          <div id="js-supplier-list">
            {capture assign="errorContent"}
              <h4>{l s='No products available yet' d='Shop.Theme.Catalog'}</h4>
              <p>{l s='Stay tuned! More products will be shown here as they are added.' d='Shop.Theme.Catalog'}</p>
            {/capture}

            {include file='errors/not-found.tpl' errorContent=$errorContent}
          </div>

          <div id="js-supplier-list-bottom"></div>
        {/if}
      </section>
    {/block}

  </section>

{/block}