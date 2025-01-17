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
{block name='header_banner'}
  <div class="header-banner">
    {hook h='displayBanner'}
  </div>
{/block}

{block name='header_nav'}
  <nav class="header-nav">
    <div class="container">
      <div class="row">
        <div class="hidden-md-down desktop">
          <div class="col-md-1 col-xs-12" id="menu-ds-icon">
            <i class="material-icons d-inline">&#xE5D2;</i>
          </div>
          <div class="col-md-2 col-xs-12">
            {hook h='displayNav1'}
          </div>
        <div class="col-md-2 hidden-sm-down" id="_desktop_logo">
          {if $shop.logo_details}
            {if $page.page_name == 'index'}
              <h1>
                {renderLogo}
              </h1>
            {else}
              {renderLogo}
            {/if}
          {/if}
        </div>
          <div class="col-md-7 right-nav">
{*              {hook h='displayNav1'}*}
              {hook h='displayNav2'}
          </div>
        </div>
        <div class="hidden-md-up text-sm-center mobile">
          <div class="float-xs-left" id="menu-icon">
            <i class="material-icons d-inline">&#xE5D2;</i>
          </div>
          <div class="float-xs-right" id="_mobile_cart"></div>
          <div class="float-xs-right" id="_mobile_user_info"></div>
          <div class="top-logo" id="_mobile_logo"></div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </nav>
  <div class="popup-menu">
    <ul>
      {assign var="artWorks" value=$link->getCategoryLink(3)}
      <li><p>01</p><a href="{$artWorks}">Artworks</a></li>
      <li><p>02</p><a href="#">artists</a></li>
      {assign var="categoryLink" value=$link->getCategoryLink(20)}
      <li><p>03</p><a href="{$categoryLink}">charity</a></li>
      {assign var="securePayment" value=$link->getCMSLink(5)}
      <li><p>04</p><a href="{$securePayment}">buy & sell</a></li>
      {assign var="abousUs" value=$link->getCMSLink(4)}
      <li><p>05</p><a href="{$abousUs}">about</a></li>
    </ul>
    <img class="im6" src="https://artxe.lyoshi.me/img/cms/8b3e6ed0f7237a53f918779368f6fc1f_.png" alt="8b3e6ed0f7237a53f918779368f6fc1f_.png" />
  </div>
{/block}
