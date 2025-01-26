
<pre>
{*    {$id|@var_dump}*}
    {$supplier|@var_dump}
</pre>
<h1>{$supplier->name}</h1>
<p>{$supplier->description}</p>

{if $supplier->logo}
    <img src="{$supplier->logo}" alt="{$supplier->name}" />
{/if}

{*<a href="{url entity='product' id_product=$product.id_product}" title="{$product.name}">*}
{*    View Products*}
{*</a>*}