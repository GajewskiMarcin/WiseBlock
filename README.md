# WiseBlock - Contextual HTML Content Blocks for PrestaShop

**WiseBlock** is a powerful PrestaShop module that enables you to create and manage contextual HTML content blocks with advanced targeting rules. Display the right content to the right customers at the right time.

## Key Features

- **Advanced Targeting Rules** - Display blocks based on categories, tags, customer groups, countries, cart values, manufacturers, suppliers, features, currencies, UTM parameters, and products in cart
- **Dynamic Hooks** - Create custom `displayWiseBlock*` hooks directly from the back office without editing theme files
- **Smart Placeholders** - Use dynamic placeholders like `{{product_name}}`, `{{price}}`, `{{free_shipping_remaining}}` and more
- **Conditional Content** - Show/hide parts of content based on conditions (stock status, cart state, customer login, etc.)
- **Scheduling** - Set publish date ranges and time-of-day restrictions
- **Import/Export** - Backup and transfer configurations between shops
- **Block Tester** - Built-in tool to verify block matching rules
- **Multi-language** - Full support for multiple languages
- **Performance Optimized** - Built-in caching for optimal performance

## Requirements

- PrestaShop 8.0.0 - 9.x
- PHP 7.4+

## Installation

1. Download or clone this repository
2. Zip the `wiseblock` folder
3. Upload via PrestaShop Back Office: **Modules > Module Manager > Upload a module**
4. Click **Install**
5. Access the module via sidebar: **Improve > Secret Sauce > WiseBlock**

---

## Targeting Rules

WiseBlock supports multiple targeting rule types. Rules can be combined using **OR** (any rule matches) or **AND** (all rules must match) logic. Each rule can be set to **Include** or **Exclude** mode.

### Available Rule Types

| Rule Type | Description |
|-----------|-------------|
| **Category** | Target products in specific categories (includes parent categories automatically) |
| **Tag** | Target products with specific tags |
| **Customer Group** | Show content only to specific customer groups (e.g., VIP, Wholesale) |
| **Country** | Target customers from specific countries |
| **Cart Value** | Show content when cart total is within a specified range (min/max) |
| **Manufacturer** | Target products from specific manufacturers/brands |
| **Supplier** | Target products from specific suppliers |
| **Feature** | Target products with specific feature values |
| **Currency** | Show content only for specific currencies |
| **UTM Parameters** | Target visitors coming from specific marketing campaigns (utm_source, utm_campaign) |
| **Cart Product** | Show content when specific product(s) are in the cart |

### Rule Logic

- **OR Mode**: Block is displayed if ANY include rule matches
- **AND Mode**: Block is displayed only if ALL include rules match
- **Exclude rules**: Always take priority - if any exclude rule matches, block is hidden

---

## Dynamic Placeholders

Insert dynamic content into your blocks using placeholders. All placeholders are replaced at render time with actual values.

### Product Placeholders

| Placeholder | Description |
|-------------|-------------|
| `{{product_name}}` | Current product name |
| `{{reference}}` | Product reference/SKU |
| `{{ean13}}` | Product EAN13 barcode |
| `{{manufacturer}}` | Manufacturer/brand name |
| `{{category_name}}` | Default category name |
| `{{category_description}}` | Default category description |
| `{{product_url}}` | Full URL to product page |
| `{{add_to_cart_url}}` | Direct add-to-cart URL |
| `{{price}}` | Product price (with tax, formatted) |
| `{{price_without_tax}}` | Product price without tax |
| `{{stock_status}}` | "In Stock" or "Out of Stock" |
| `{{stock_quantity}}` | Available quantity number |
| `{{weight}}` | Product weight with unit |

### Cart Placeholders

| Placeholder | Description |
|-------------|-------------|
| `{{cart_total}}` | Cart total without shipping |
| `{{cart_total_with_shipping}}` | Cart total with shipping |
| `{{cart_products_count}}` | Number of products in cart |

### Free Shipping Placeholders

| Placeholder | Description |
|-------------|-------------|
| `{{free_shipping_threshold}}` | Amount needed for free shipping (formatted) |
| `{{free_shipping_remaining}}` | Amount still needed for free shipping (formatted) |
| `{{free_shipping_remaining_raw}}` | Amount remaining as raw number (for calculations) |
| `{{free_shipping_progress}}` | Progress percentage toward free shipping (0-100) |
| `{{free_shipping_achieved}}` | "1" if free shipping achieved, "0" otherwise |

### Shop Placeholders

| Placeholder | Description |
|-------------|-------------|
| `{{shop_name}}` | Shop name from configuration |

---

## Conditional Content Blocks

Show or hide parts of your content based on conditions. Content between conditional tags is only rendered when the condition is met.

### Stock Conditions

```html
{{#if_in_stock}}
  <p>Order now - ships today!</p>
{{/if_in_stock}}

{{#if_out_of_stock}}
  <p>Currently unavailable - notify me when back in stock</p>
{{/if_out_of_stock}}

{{#if_low_stock}}
  <p>Only {{stock_quantity}} left - order soon!</p>
{{/if_low_stock}}
```

### Free Shipping Conditions

```html
{{#if_free_shipping_not_achieved}}
  <div class="shipping-progress">
    Add {{free_shipping_remaining}} more for FREE shipping!
    <div class="progress-bar" style="width: {{free_shipping_progress}}%"></div>
  </div>
{{/if_free_shipping_not_achieved}}

{{#if_free_shipping_achieved}}
  <p>You qualify for FREE shipping!</p>
{{/if_free_shipping_achieved}}
```

### Cart Conditions

```html
{{#if_cart_empty}}
  <p>Your cart is empty - start shopping!</p>
{{/if_cart_empty}}

{{#if_cart_not_empty}}
  <p>You have {{cart_products_count}} items in your cart</p>
{{/if_cart_not_empty}}
```

### Customer Conditions

```html
{{#if_logged_in}}
  <p>Welcome back! Check your loyalty points.</p>
{{/if_logged_in}}

{{#if_guest}}
  <p>Sign up today for exclusive discounts!</p>
{{/if_guest}}
```

### Product Conditions

```html
{{#if_on_sale}}
  <span class="sale-badge">SALE!</span>
{{/if_on_sale}}

{{#if_new_product}}
  <span class="new-badge">NEW!</span>
{{/if_new_product}}

{{#if_has_manufacturer}}
  <p>Brand: {{manufacturer}}</p>
{{/if_has_manufacturer}}
```

---

## Dynamic Hooks

Create custom hooks directly from the WiseBlock admin panel without editing theme files.

### How It Works

1. Go to **WiseBlock > Hooks** tab
2. Click **Create New Hook**
3. Enter a hook name (e.g., `UnderPrice`) - the full hook name will be `displayWiseBlockUnderPrice`
4. Add the hook to your theme template:

```smarty
{hook h='displayWiseBlockUnderPrice'}
```

### Default Hooks

WiseBlock comes with two pre-configured hooks:
- `displayWiseBlockUnderCart` - Content under the Add to Cart button
- `displayWiseBlockUnderDescription` - Content under product description

### Hook Management

- **Enable/Disable** hooks without deleting them
- **View block count** assigned to each hook
- **Delete** unused hooks

---

## Scheduling

Control when your blocks are displayed with flexible scheduling options.

### Date Range

Set **Publish From** and **Publish To** dates to create time-limited campaigns:
- Flash sales
- Holiday promotions
- Limited-time offers

### Time of Day

Set **Time From** and **Time To** to display content only during specific hours:
- Lunch specials (11:00 - 14:00)
- Evening promotions (18:00 - 22:00)
- Overnight deals (22:00 - 06:00) - overnight ranges are supported

---

## Shortcodes

Embed WiseBlock content anywhere using shortcodes:

```
[wiseblock id="123"]
```

Shortcodes work in:
- CMS pages
- Product descriptions
- Any content that supports hooks

---

## Import/Export

### Export

Export your blocks and hooks configuration as a JSON file for:
- Backup purposes
- Migration to another shop
- Sharing configurations

### Import

Import previously exported JSON files to:
- Restore configurations
- Copy settings between environments
- Set up new shops quickly

---

## Block Tester

The built-in Block Tester helps you verify that your targeting rules work correctly.

### How to Use

1. Go to **WiseBlock > Dashboard** tab
2. Find the **Block Tester** section
3. Enter a **Product ID** and select a **Hook**
4. Click **Run Test**

### Test Results

The tester shows:
- Product categories (including parent tree)
- Product tags
- All blocks assigned to the hook
- Match result for each block (matched/not matched)
- Logic mode used (OR/AND)

---

## Custom CSS

Add custom CSS styles that will be injected on the frontend.

1. Go to **WiseBlock > Dashboard** tab
2. Find the **Custom CSS** section
3. Add your styles
4. Click **Save CSS**

The CSS is output in the `<head>` section via the `displayHeader` hook.

---

## UTM Tracking

WiseBlock can target visitors based on UTM parameters from marketing campaigns.

### How It Works

1. Create a block with UTM targeting rule
2. Specify `utm_source` and/or `utm_campaign` values
3. Visitors arriving with matching UTM parameters will see the content

### UTM Persistence

UTM parameters are stored in cookies for 30 days, so returning visitors from the same campaign will continue to see targeted content even after navigating through the site.

### Example Use Cases

- Show special offers for Google Ads visitors (`utm_source=google`)
- Display welcome message for email campaign visitors (`utm_campaign=spring_sale`)
- Target social media traffic (`utm_source=facebook`)

---

## Admin Interface

WiseBlock features a modern, intuitive admin interface with:

- **Blocks List** - Manage all content blocks with search, filtering, and bulk actions
- **Block Editor** - Rich form with sections for content, targeting rules, scheduling, and hooks
- **Hooks Manager** - Create and manage custom hooks with card-based layout
- **Dashboard** - Statistics, block tester, import/export, and custom CSS

### Navigation

The module is accessible via the PrestaShop sidebar under:
**Improve > Secret Sauce > WiseBlock**

Internal tabs provide navigation between:
- Blocks (main content management)
- Hooks (hook management)
- Dashboard (tools and statistics)
- About (module information)

---

## Performance

WiseBlock is optimized for performance:

- **Caching** - Block output is cached per product/hook/language/shop combination
- **Efficient queries** - Optimized database queries with proper indexing
- **Lazy loading** - Rules are only evaluated when needed

---

## Translations

WiseBlock is fully translatable and includes translations for 20+ languages:
- English, German, French, Spanish, Italian, Dutch, Polish, Portuguese
- Czech, Slovak, Hungarian, Romanian, Bulgarian, Greek
- Danish, Swedish, Norwegian, Finnish
- Croatian, Slovenian, Lithuanian, Latvian, Estonian

---

## Database Structure

WiseBlock creates the following database tables:

| Table | Description |
|-------|-------------|
| `wiseblock_block` | Main block configuration |
| `wiseblock_block_lang` | Multilingual content (title, HTML) |
| `wiseblock_block_hook` | Block-to-hook assignments |
| `wiseblock_rule` | Targeting rules |
| `wiseblock_hook` | Custom hook definitions |

---

## Support

For bug reports and feature requests, please create an issue on GitHub.

---

## Author

Created by [marcingajewski.pl](https://marcingajewski.pl)

---

## License

This module is provided as-is for use with PrestaShop.
