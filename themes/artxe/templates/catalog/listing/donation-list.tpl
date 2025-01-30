<section id="main">
{*    {block name='product_list_header'}*}
        <div id="donation" class="category-header">
            <div class="category-header-main">
                <div class="category-header-top">
                    <img class="im2" src="{$urls["img_ps_url"]}cms/f380c1c1a95978d5eb4578eacab81bde_.png" alt="" />
                </div>
                <div class="category-header-text">
                    <h1 class="name">{$category.name}</h1>
                    <p class="description">{$category.description}</p>
                </div>
            </div>
            <div class="category-header-bottom">
                {assign var="cmsLink2" value=$link->getCMSLink(5)}
                <img class="im7" src="{$urls["img_ps_url"]}cms/f99fdac17a60225d96cfd6f76ac7ee6f_.png" alt="" />
            </div>
        </div>
        <div id="donation_steps" class="donation-hiw">
            <a href="#">
                <div class="donation-hiw-step">
                    <div class="step">
                        <img src="{$urls["img_ps_url"]}cms/hand.png" />
                    </div>
                    <p>Describe how it works step one here</p>
                </div>
            </a>
            <a href="#">
                <div class="donation-hiw-step">
                    <div class="step">
                        <img src="{$urls["img_ps_url"]}cms/donation.png" />
                    </div>
                    <p>Describe How it works step two here</p>
                </div>
            </a>
            <a href="#">
                <div class="donation-hiw-step">
                    <div class="step">
                        <img src="{$urls["img_ps_url"]}cms/global.png" />
                    </div>
                    <p>Describe How it works step three here</p>
                </div>
            </a>
        </div>
{*    {/block}*}

{*    {block name='subcategory_list'}*}
{*        {if isset($subcategories) && $subcategories|@count > 0}*}
{*            {include file='catalog/_partials/subcategories.tpl' subcategories=$subcategories}*}
{*        {/if}*}
{*    {/block}*}

    {hook h="displayHeaderCategory"}

    <section id="donations">
        {if $listing.products|count}

            {block name='donation_list_top'}
                {include file='catalog/_partials/donations-top.tpl' listing=$listing}
            {/block}

            {block name='product_list_active_filters'}
                <div class="hidden-sm-down">
                    {$listing.rendered_active_filters nofilter}
                </div>
            {/block}

            {block name='donation_list'}
                {include file='catalog/_partials/donations.tpl' listing=$listing productClass="col-xs-12 col-sm-6 col-xl-4"}
            {/block}

{*            {block name='product_list_bottom'}*}
{*                {include file='catalog/_partials/products-bottom.tpl' listing=$listing}*}
{*            {/block}9*}

        {else}
            <div id="js-product-list-top"></div>

            <div id="js-product-list">6_
                {capture assign="errorContent"}
                    <h4>{l s='No products available yet' d='Shop.Theme.Catalog'}</h4>
                    <p>{l s='Stay tuned! More products will be shown here as they are added.' d='Shop.Theme.Catalog'}</p>
                {/capture}

                {include file='errors/not-found.tpl' errorContent=$errorContent}
            </div>

            <div id="js-product-list-bottom"></div>
        {/if}
    </section>

{*    {block name='product_list_footer'}{/block}*}

    {hook h="displayFooterCategory"}

</section>