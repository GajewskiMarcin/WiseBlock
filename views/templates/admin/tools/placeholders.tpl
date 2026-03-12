<!-- Placeholders Reference -->
<div class="wb-tool-card wb-tool-card-standalone">
    <div class="wb-tool-card-header">
        <h3 class="wb-tool-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;">
                <polyline points="4 7 4 4 20 4 20 7"></polyline>
                <line x1="9" y1="20" x2="15" y2="20"></line>
                <line x1="12" y1="4" x2="12" y2="20"></line>
            </svg>
            {l s='Available Placeholders' mod='wiseblock'}
        </h3>
        <p class="wb-tool-subtitle">{l s='Use these placeholders in HTML content. They will be replaced with real values when the block is displayed.' mod='wiseblock'}</p>
    </div>
    <div class="wb-tool-card-body">

        <!-- Product -->
        <div class="wb-ph-group">
            <div class="wb-ph-group-header" onclick="this.parentElement.classList.toggle('wb-ph-collapsed')">
                <span class="wb-ph-group-icon">🛒</span>
                <span class="wb-ph-group-title">{l s='Product' mod='wiseblock'}</span>
                <span class="wb-ph-group-badge">{l s='Product pages only' mod='wiseblock'}</span>
                <svg class="wb-ph-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="wb-ph-group-body">
                <table class="wb-ph-table">
                    <tr><td class="wb-ph-code"><code>{literal}{{product_name}}{/literal}</code></td><td>{l s='Product name' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{reference}}{/literal}</code></td><td>{l s='Product reference / SKU' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{ean13}}{/literal}</code></td><td>{l s='EAN13 barcode' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{manufacturer}}{/literal}</code></td><td>{l s='Manufacturer name' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{category_name}}{/literal}</code></td><td>{l s='Default category name' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{category_description}}{/literal}</code></td><td>{l s='Default category description' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{product_url}}{/literal}</code></td><td>{l s='Product page URL' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{add_to_cart_url}}{/literal}</code></td><td>{l s='Add to cart URL' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{price}}{/literal}</code></td><td>{l s='Price with tax (formatted)' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{price_without_tax}}{/literal}</code></td><td>{l s='Price without tax (formatted)' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{weight}}{/literal}</code></td><td>{l s='Product weight with unit' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{stock_quantity}}{/literal}</code></td><td>{l s='Stock quantity (number)' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{stock_status}}{/literal}</code></td><td>{l s='Stock status text (In Stock / Out of Stock)' mod='wiseblock'}</td></tr>
                </table>
            </div>
        </div>

        <!-- Shop & Search -->
        <div class="wb-ph-group">
            <div class="wb-ph-group-header" onclick="this.parentElement.classList.toggle('wb-ph-collapsed')">
                <span class="wb-ph-group-icon">🏪</span>
                <span class="wb-ph-group-title">{l s='Shop & Search' mod='wiseblock'}</span>
                <span class="wb-ph-group-badge wb-ph-badge-green">{l s='All pages' mod='wiseblock'}</span>
                <svg class="wb-ph-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="wb-ph-group-body">
                <table class="wb-ph-table">
                    <tr><td class="wb-ph-code"><code>{literal}{{shop_name}}{/literal}</code></td><td>{l s='Shop name' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{customer_name}}{/literal}</code></td><td>{l s='Logged-in customer first name' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{search_query}}{/literal}</code></td><td>{l s='Current search query (search results page)' mod='wiseblock'}</td></tr>
                </table>
            </div>
        </div>

        <!-- Cart -->
        <div class="wb-ph-group">
            <div class="wb-ph-group-header" onclick="this.parentElement.classList.toggle('wb-ph-collapsed')">
                <span class="wb-ph-group-icon">🛍️</span>
                <span class="wb-ph-group-title">{l s='Cart' mod='wiseblock'}</span>
                <span class="wb-ph-group-badge wb-ph-badge-green">{l s='All pages' mod='wiseblock'}</span>
                <svg class="wb-ph-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="wb-ph-group-body">
                <table class="wb-ph-table">
                    <tr><td class="wb-ph-code"><code>{literal}{{cart_total}}{/literal}</code></td><td>{l s='Cart subtotal (formatted, without shipping)' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{cart_total_with_shipping}}{/literal}</code></td><td>{l s='Cart total with shipping (formatted)' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{cart_products_count}}{/literal}</code></td><td>{l s='Number of products in cart' mod='wiseblock'}</td></tr>
                </table>
            </div>
        </div>

        <!-- Free Shipping -->
        <div class="wb-ph-group">
            <div class="wb-ph-group-header" onclick="this.parentElement.classList.toggle('wb-ph-collapsed')">
                <span class="wb-ph-group-icon">🚚</span>
                <span class="wb-ph-group-title">{l s='Free Shipping' mod='wiseblock'}</span>
                <span class="wb-ph-group-badge wb-ph-badge-green">{l s='All pages' mod='wiseblock'}</span>
                <svg class="wb-ph-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="wb-ph-group-body">
                <div style="padding:10px 12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;margin-bottom:12px;font-size:12px;color:#1e40af;">
                    <strong>{l s='Threshold is read from:' mod='wiseblock'}</strong> {l s='Shipping Preferences' mod='wiseblock'} &rarr; {l s='Free shipping starts at' mod='wiseblock'}
                </div>
                <table class="wb-ph-table">
                    <tr><td class="wb-ph-code"><code>{literal}{{free_shipping_threshold}}{/literal}</code></td><td>{l s='Formatted minimum order for free shipping' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{free_shipping_remaining}}{/literal}</code></td><td>{l s='Amount remaining to free shipping (formatted)' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{free_shipping_remaining_raw}}{/literal}</code></td><td>{l s='Amount remaining (raw number, 2 decimals)' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{free_shipping_progress}}{/literal}</code></td><td>{l s='Progress percentage (0-100)' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{free_shipping_achieved}}{/literal}</code></td><td>{l s='1 if free shipping reached, 0 otherwise' mod='wiseblock'}</td></tr>
                </table>
            </div>
        </div>

        <!-- Conditional Blocks -->
        <div class="wb-ph-group">
            <div class="wb-ph-group-header" onclick="this.parentElement.classList.toggle('wb-ph-collapsed')">
                <span class="wb-ph-group-icon">🔀</span>
                <span class="wb-ph-group-title">{l s='Conditional Blocks' mod='wiseblock'}</span>
                <svg class="wb-ph-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="wb-ph-group-body">
                <p class="wb-ph-hint">{l s='Wrap content in conditional tags to show/hide based on conditions.' mod='wiseblock'}</p>

                <h4 class="wb-ph-subgroup">{l s='Free Shipping' mod='wiseblock'}</h4>
                <table class="wb-ph-table">
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_free_shipping_not_achieved}}...{{/if_free_shipping_not_achieved}}{/literal}</code></td><td>{l s='Show only when free shipping NOT reached' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_free_shipping_achieved}}...{{/if_free_shipping_achieved}}{/literal}</code></td><td>{l s='Show only when free shipping reached' mod='wiseblock'}</td></tr>
                </table>

                <h4 class="wb-ph-subgroup">{l s='Stock' mod='wiseblock'} <span class="wb-ph-group-badge">{l s='Product pages only' mod='wiseblock'}</span></h4>
                <table class="wb-ph-table">
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_in_stock}}...{{/if_in_stock}}{/literal}</code></td><td>{l s='Show only when product is in stock' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_out_of_stock}}...{{/if_out_of_stock}}{/literal}</code></td><td>{l s='Show only when product is out of stock' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_low_stock}}...{{/if_low_stock}}{/literal}</code></td><td>{l s='Show only when stock is low' mod='wiseblock'}</td></tr>
                </table>

                <h4 class="wb-ph-subgroup">{l s='Price' mod='wiseblock'} <span class="wb-ph-group-badge">{l s='Product pages only' mod='wiseblock'}</span></h4>
                <table class="wb-ph-table">
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_on_sale}}...{{/if_on_sale}}{/literal}</code></td><td>{l s='Show only when product is on sale' mod='wiseblock'}</td></tr>
                </table>

                <h4 class="wb-ph-subgroup">{l s='Cart' mod='wiseblock'}</h4>
                <table class="wb-ph-table">
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_cart_empty}}...{{/if_cart_empty}}{/literal}</code></td><td>{l s='Show only when cart is empty' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_cart_not_empty}}...{{/if_cart_not_empty}}{/literal}</code></td><td>{l s='Show only when cart has products' mod='wiseblock'}</td></tr>
                </table>

                <h4 class="wb-ph-subgroup">{l s='Customer' mod='wiseblock'}</h4>
                <table class="wb-ph-table">
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_logged_in}}...{{/if_logged_in}}{/literal}</code></td><td>{l s='Show only for logged-in customers' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_guest}}...{{/if_guest}}{/literal}</code></td><td>{l s='Show only for guests (not logged in)' mod='wiseblock'}</td></tr>
                </table>

                <h4 class="wb-ph-subgroup">{l s='Product' mod='wiseblock'} <span class="wb-ph-group-badge">{l s='Product pages only' mod='wiseblock'}</span></h4>
                <table class="wb-ph-table">
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_has_manufacturer}}...{{/if_has_manufacturer}}{/literal}</code></td><td>{l s='Show only when product has a manufacturer' mod='wiseblock'}</td></tr>
                    <tr><td class="wb-ph-code"><code>{literal}{{#if_new_product}}...{{/if_new_product}}{/literal}</code></td><td>{l s='Show only for new products' mod='wiseblock'}</td></tr>
                </table>
            </div>
        </div>

    </div>
</div>

<style>
.wb-ph-group { border:1px solid #e5e7eb; border-radius:8px; margin-bottom:12px; overflow:hidden; }
.wb-ph-group-header { display:flex; align-items:center; gap:8px; padding:12px 16px; cursor:pointer; background:#fafafa; transition:background .15s; user-select:none; }
.wb-ph-group-header:hover { background:#f3f4f6; }
.wb-ph-group-icon { font-size:16px; }
.wb-ph-group-title { font-weight:600; font-size:14px; color:#1f2937; flex:1; }
.wb-ph-group-badge { font-size:11px; padding:2px 8px; border-radius:10px; background:#fef3c7; color:#92400e; font-weight:500; }
.wb-ph-badge-green { background:#d1fae5; color:#065f46; }
.wb-ph-chevron { transition:transform .2s; color:#9ca3af; flex-shrink:0; }
.wb-ph-collapsed .wb-ph-group-body { display:none; }
.wb-ph-collapsed .wb-ph-chevron { transform:rotate(-90deg); }
.wb-ph-group-body { padding:12px 16px; border-top:1px solid #e5e7eb; }
.wb-ph-table { width:100%; border-collapse:collapse; }
.wb-ph-table tr { border-bottom:1px solid #f3f4f6; }
.wb-ph-table tr:last-child { border-bottom:none; }
.wb-ph-table td { padding:6px 8px; font-size:13px; color:#4b5563; vertical-align:top; }
.wb-ph-code { white-space:nowrap; width:1%; }
.wb-ph-code code { background:#f3f4f6; padding:2px 6px; border-radius:4px; font-size:12px; color:#7c3aed; font-family:monospace; }
.wb-ph-hint { font-size:12px; color:#6b7280; margin:0 0 12px 0; font-style:italic; }
.wb-ph-subgroup { font-size:12px; font-weight:600; color:#6b7280; margin:14px 0 6px 0; padding:0; text-transform:uppercase; letter-spacing:0.5px; }
.wb-ph-subgroup:first-child { margin-top:0; }
.wb-ph-subgroup .wb-ph-group-badge { font-size:10px; vertical-align:middle; margin-left:4px; }
</style>
