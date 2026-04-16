# Vendor_SavingsDisplay
 
Magento 2 module that displays a "You save $X (Y% off)" badge on product pages when a special price is active.
 
---
 
## Features
 
- Shows savings amount and/or percentage below the product price
- Configurable label text, thresholds, and toggles via admin
- Only renders when a special price exists and savings exceed the configured minimum
- Lightweight — one block, one template, one CSS file
- Theme-friendly CSS with dark mode support
---
 
## Requirements
 
- Magento 2.4.x
- PHP 8.1+
---
 
## Installation
 
**Manual (recommended for development):**
 
```bash
cp -r Vendor/SavingsDisplay app/code/Vendor/SavingsDisplay
bin/magento module:enable Vendor_SavingsDisplay
bin/magento setup:upgrade
bin/magento cache:flush
```
 
**With Composer** *(if published to a registry)*:
 
```bash
composer require vendor/module-savings-display
bin/magento module:enable Vendor_SavingsDisplay
bin/magento setup:upgrade
bin/magento cache:flush
```
 
---
 
## Configuration
 
Navigate to: **Stores → Configuration → Vendor Extensions → Savings Display**
 
| Setting | Default | Description |
|---|---|---|
| Enable Savings Display | Yes | Master on/off switch |
| Show Percentage Saved | Yes | Show `(25% off)` in badge |
| Show Amount Saved | Yes | Show `$30.00` in badge |
| Label Text | `You save` | Customizable prefix label |
| Minimum Savings to Display | `0` | Hide badge if savings are below this $ amount |
 
---
 
## Module Structure
 
```
Vendor/SavingsDisplay/
├── registration.php
├── etc/
│   ├── module.xml                   # Module declaration
│   ├── config.xml                   # Default config values
│   ├── acl.xml                      # Admin permissions
│   └── adminhtml/
│       └── system.xml               # Admin config panel fields
├── Helper/
│   └── Data.php                     # Config accessors + savings calculation
├── Block/
│   └── SavingsBadge.php             # Block class for the template
└── view/frontend/
    ├── layout/
    │   ├── default.xml              # Loads CSS on all pages
    │   └── catalog_product_view.xml # Injects block on product page
    ├── templates/
    │   └── savings_badge.phtml      # Badge HTML template
    └── web/css/
        └── savings_badge.css        # Badge styles
```
 
---
 
## How It Works
 
1. `catalog_product_view.xml` injects the `SavingsBadge` block after the price container on the product page.
2. The block reads the current product from the registry and calls `Helper\Data::getSavingsData()`.
3. The helper compares `regular_price` vs `final_price` from Magento's price info model.
4. If savings exist and exceed the configured threshold, the template renders the badge.
5. If both toggles (amount + percentage) are off, or no savings apply, nothing is rendered.
---
 
## Customisation
 
**Override the template** in your theme:
 
```
app/design/frontend/<Vendor>/<theme>/Vendor_SavingsDisplay/templates/savings_badge.phtml
```
 
**Override the CSS** in your theme:
 
```
app/design/frontend/<Vendor>/<theme>/Vendor_SavingsDisplay/web/css/savings_badge.css
```
 
**Change the block position** — edit `catalog_product_view.xml` and adjust the `referenceContainer` name or `sortOrder` to match your theme's price layout.
 
---
 
## Uninstall
 
```bash
bin/magento module:disable Vendor_SavingsDisplay
bin/magento setup:upgrade
bin/magento cache:flush
rm -rf app/code/Vendor/SavingsDisplay
```
 
---
 
## License
 
MIT
