<h1>{l s='Our Suppliers'}</h1>
<pre>
    {$suppliers|@var_dump}
</pre>
{if $suppliers}
    <ul class="suppliers-list">
        {foreach from=$suppliers item=supplier}
            <li>
                <a href="{$supplier.link}" title="{$supplier.name}">
                    <img src="{$supplier.image}" alt="{$supplier.name}" />
                    <h2>{$supplier.name}</h2>
                </a>
            </li>
        {/foreach}
    </ul>
{else}
    <p>{l s='No suppliers available at this time.'}</p>
{/if}