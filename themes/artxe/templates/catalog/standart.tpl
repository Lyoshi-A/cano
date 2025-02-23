  <section id="main">
    <meta content="{$product.url}">

    <div class="row product-container js-product-container">
      <div class="col-md-6">
        {block name='page_content_container'}
          <section class="page-content" id="content">
            {block name='page_content'}
              {include file='catalog/_partials/product-flags.tpl'}

              {block name='product_cover_thumbnails'}
                {include file='catalog/_partials/product-cover-thumbnails.tpl'}
              {/block}
              <div class="scroll-box-arrows">
                <i class="material-icons left">&#xE314;</i>
                <i class="material-icons right">&#xE315;</i>
              </div>

            {/block}
          </section>
        {/block}
        </div>
        <div class="col-md-6">
          {block name='page_header_container'}
              {assign var="categoryUrl" value=$link->getCategoryLink($category->id)}
              <a href="{$categoryUrl}" class="btn btn-default back-to-category" rel="noreferrer noopener">< Back to All {$category->name}</a>
            {block name='page_header'}
              <h1 class="h1">{block name='page_title'}{$product.name}{/block}</h1>
            {/block}
          {/block}
          {block name='product_prices'}
            {include file='catalog/_partials/product-prices.tpl'}
          {/block}

          <div class="product-information">
{*            {block name='product_description_short'}*}
{*              <div id="product-description-short-{$product.id}" class="product-description">{$product.description_short nofilter}</div>*}
{*            {/block}*}
              {block name='product_tabs'}
              <div class="tabs">
                  <ul class="nav nav-tabs" role="tablist">
                      {if $product.description}
                          <li class="nav-item">
                              <a
                                      class="nav-link{if $product.description} active js-product-nav-active{/if}"
                                      data-toggle="tab"
                                      href="#description"
                                      role="tab"
                                      aria-controls="description"
                                      {if $product.description} aria-selected="true"{/if}>{l s='Description' d='Shop.Theme.Catalog'}</a>
                          </li>
                      {/if}
                      <li class="nav-item">
                          <a
                                  class="nav-link{if !$product.description} active js-product-nav-active{/if}"
                                  data-toggle="tab"
                                  href="#product-details"
                                  role="tab"
                                  aria-controls="product-details"
                                  {if !$product.description} aria-selected="true"{/if}>{l s='Product Details' d='Shop.Theme.Catalog'}</a>
                      </li>
                      {if $product.attachments}
                          <li class="nav-item">
                              <a
                                      class="nav-link"
                                      data-toggle="tab"
                                      href="#attachments"
                                      role="tab"
                                      aria-controls="attachments">{l s='Attachments' d='Shop.Theme.Catalog'}</a>
                          </li>
                      {/if}
                      {foreach from=$product.extraContent item=extra key=extraKey}
                          <li class="nav-item">
                              <a
                                      class="nav-link"
                                      data-toggle="tab"
                                      href="#extra-{$extraKey}"
                                      role="tab"
                                      aria-controls="extra-{$extraKey}">{$extra.title}</a>
                          </li>
                      {/foreach}
                  </ul>

                  <div class="tab-content" id="tab-content">
                      <div class="tab-pane fade in{if $product.description} active js-product-tab-active{/if}" id="description" role="tabpanel">
                          {block name='product_description'}
                              <div class="product-description">{$product.description nofilter}</div>
                          {/block}
                      </div>

                      {block name='product_details'}
                          {include file='catalog/_partials/product-details.tpl'}
                      {/block}

                      {block name='product_attachments'}
                          {if $product.attachments}
                              <div class="tab-pane fade in" id="attachments" role="tabpanel">
                                  <section class="product-attachments">
                                      <p class="h5 text-uppercase">{l s='Download' d='Shop.Theme.Actions'}</p>
                                      {foreach from=$product.attachments item=attachment}
                                          <div class="attachment">
                                              <h4><a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.name}</a></h4>
                                              <p>{$attachment.description}</p>
                                              <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                                                  {l s='Download' d='Shop.Theme.Actions'} ({$attachment.file_size_formatted})
                                              </a>
                                          </div>
                                      {/foreach}
                                  </section>
                              </div>
                          {/if}
                      {/block}

                      {foreach from=$product.extraContent item=extra key=extraKey}
                      <div class="tab-pane fade in {$extra.attr.class}" id="extra-{$extraKey}" role="tabpanel" {foreach $extra.attr as $key => $val} {$key}="{$val}"{/foreach}>
                      {$extra.content nofilter}
                  </div>
                  {/foreach}
              </div>
          </div>
            {/block}

            {if $product.is_customizable && count($product.customizations.fields)}
              {block name='product_customization'}
                {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations}
              {/block}
            {/if}

            <div class="product-actions js-product-actions">
              {block name='product_buy'}
                <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                  <input type="hidden" name="token" value="{$static_token}">
                  <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                  <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id" class="js-product-customization-id">

                  {block name='product_variants'}
                    {include file='catalog/_partials/product-variants.tpl'}
                  {/block}

                  {block name='product_pack'}
                    {if $packItems}
                      <section class="product-pack">
                        <p class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</p>
                        {foreach from=$packItems item="product_pack"}
                          {block name='product_miniature'}
                            {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack showPackProductsPrice=$product.show_price}
                          {/block}
                        {/foreach}
                    </section>
                    {/if}
                  {/block}

                  {block name='product_discounts'}
                    {include file='catalog/_partials/product-discounts.tpl'}
                  {/block}

                  {block name='product_add_to_cart'}
                    {include file='catalog/_partials/product-add-to-cart.tpl'}
                  {/block}

                  {* Input to refresh product HTML removed, block kept for compatibility with themes *}
                  {block name='product_refresh'}{/block}
                </form>
              {/block}

            </div>

            {block name='hook_display_reassurance'}
              {hook h='displayReassurance'}
            {/block}

        </div>
      </div>
    </div>
    {block name='product_additional_info'}
      {include file='catalog/_partials/product-additional-info.tpl'}
    {/block}
    {block name='product_accessories'}
      {if $accessories}
        <section class="product-accessories clearfix">
          <p class="h5 text-uppercase">{l s='You might also like' d='Shop.Theme.Catalog'}</p>
          <div class="products row">
            {foreach from=$accessories item="product_accessory" key="position"}
              {block name='product_miniature'}
                {include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory position=$position productClasses="col-xs-12 col-sm-6 col-lg-4 col-xl-3"}
              {/block}
            {/foreach}
          </div>
        </section>
      {/if}
    {/block}

{*    {block name='product_footer'}*}
{*      {hook h='displayFooterProduct' product=$product category=$category}*}
{*    {/block}*}

    {block name='product_images_modal'}
      {include file='catalog/_partials/product-images-modal.tpl'}
    {/block}

    {block name='page_footer_container'}
      <footer class="page-footer">
        {block name='page_footer'}
          <!-- Footer content -->
        {/block}
      </footer>
    {/block}
  </section>
