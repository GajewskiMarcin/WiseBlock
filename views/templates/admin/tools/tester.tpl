<!-- Block Tester Section -->
<div class="wb-tool-card wb-tool-card-standalone">
    <div class="wb-tool-card-header">
        <h3 class="wb-tool-title">{l s='Block Tester' mod='wiseblock'}</h3>
        <p class="wb-tool-subtitle">{l s='Test which blocks will be displayed for a specific product and hook' mod='wiseblock'}</p>
    </div>
    <div class="wb-tool-card-body">
        <form method="post" class="wb-tester-form">
            <div class="wb-tester-inputs">
                <div class="wb-form-group">
                    <label class="wb-form-label">{l s='Product ID' mod='wiseblock'}</label>
                    <input type="number" name="wiseblock_test_id_product" class="wb-form-input" placeholder="{l s='Enter product ID' mod='wiseblock'}" required value="{if isset($smarty.post.wiseblock_test_id_product)}{$smarty.post.wiseblock_test_id_product|escape:'html':'UTF-8'}{/if}">
                </div>
                <div class="wb-form-group">
                    <label class="wb-form-label">{l s='Hook' mod='wiseblock'}</label>
                    <select name="wiseblock_test_hook" class="wb-form-select" required>
                        <option value="">{l s='Select a hook' mod='wiseblock'}</option>
                        {foreach from=$available_hooks item=hook}
                            <option value="{$hook.hook_name|escape:'html':'UTF-8'}" {if isset($smarty.post.wiseblock_test_hook) && $smarty.post.wiseblock_test_hook == $hook.hook_name}selected{/if}>{$hook.hook_name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div>
                <button type="submit" name="submitWiseBlockTester" value="1" class="wb-btn-run-test">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                    {l s='Run Test' mod='wiseblock'}
                </button>
            </div>
        </form>
    </div>
</div>

{if isset($wiseblock_result)}
<div class="wb-tool-card wb-tool-card-standalone wb-test-result-card">
    <div class="wb-tool-card-header">
        <h3 class="wb-tool-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #10b981;">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            {l s='Test Results' mod='wiseblock'}
        </h3>
    </div>
    <div class="wb-tool-card-body">
        <pre class="wb-test-result-pre">{$wiseblock_result}</pre>
    </div>
</div>
{/if}
