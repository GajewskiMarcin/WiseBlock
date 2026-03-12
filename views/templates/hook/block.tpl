{* module: wiseblock *}
<div class="wiseblock-content" data-wiseblock-id="{$block.id_block|intval}"{if !empty($block._auto_refresh)} data-wiseblock-refresh="true"{/if}{if !empty($block._lazy_load)} data-wiseblock-lazy="true"{/if}{if !empty($block._variant)} data-wiseblock-variant="{$block._variant|escape:'html':'UTF-8'}"{/if}>
  {if !empty($block._lazy_load)}
    <div class="wiseblock-lazy-placeholder" style="min-height:1px;"></div>
  {else}
    {$content nofilter}
  {/if}
</div>
