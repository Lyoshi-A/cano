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

<div class="block_newsletter col-lg-8 col-md-12 col-sm-12" id="blockEmailSubscription_{$hookName}">
  <div class="row">
    <div class="left-block col-md-5 col-xs-12">
      <img class="im2 hidden-sm-up" src="https://artxe.lyoshi.me/img/cms/b93a897c82a66947e106608a563c7928_.png" alt="" />
      <p id="block-newsletter-label" >{l s='let us inspire you' d='Shop.Theme.Global'}</p>
      <img class="im1 hidden-xs-down" src="https://artxe.lyoshi.me/img/cms/48a34520140de48744a1a80e8f1b5b50_.png" alt="" />
    </div>
    <div class="right-block col-md-7 col-xs-12">
      <form action="{$urls.current_url}#blockEmailSubscription_{$hookName}" method="post">
        <div class="row">
          <div class="col-xs-12">
	    <img class="im2 hidden-xs-down" src="https://artxe.lyoshi.me/img/cms/b93a897c82a66947e106608a563c7928_.png" alt="" /> 
	    <div class="right-details">
	    <p class="button-label">Want to receive exciting art and lifestyle content, directly to your inbox?</p>
            <div class="input-wrapper">
              <input
                class="email-input"
                name="email"
                type="email"
                value="{$value}"
                placeholder="{l s='Your E-Mail' d='Shop.Forms.Labels'}"
                aria-labelledby="block-newsletter-label"
                required
              >
            </div>
            <input
              class="btn hidden-xs-down"
              name="submitNewsletter"
              type="submit"
              value="{l s='Subscribe' d='Shop.Theme.Actions'}"
            >
            <input
              class="btn hidden-sm-up"
              name="submitNewsletter"
              type="submit"
              value="{l s='Subscribe' d='Shop.Theme.Actions'}"
            >
            <input type="hidden" name="blockHookName" value="{$hookName}" />
            <input type="hidden" name="action" value="0">
            <img class="im1 hidden-sm-up" src="https://artxe.lyoshi.me/img/cms/48a34520140de48744a1a80e8f1b5b50_.png" alt="" />
          <div class="col-xs-12">
              {if $conditions}
                <p>{$conditions}</p>
              {/if}
              {if $msg}
                <p class="alert {if $nw_error}alert-danger{else}alert-success{/if}">
                  {$msg}
                </p>
              {/if}
              {hook h='displayNewsletterRegistration'}
              {if isset($id_module)}
                {hook h='displayGDPRConsent' id_module=$id_module}
              {/if}
          </div>

            </div>
            <div class="clearfix"></div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
