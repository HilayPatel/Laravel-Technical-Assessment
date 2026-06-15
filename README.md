------------------------------
## Shopify CSV Importer
An asynchronous background processing pipeline that takes Shopify Product CSV and syncs it to a Shopify store via the Admin GraphQL API (v2026-04).
It automatically runs handle-based upsert checks to decide whether to create or update products, links them to a specified collection, and logs errors row-by-row.


## System Features

* Asynchronous Execution: Heavy CSV parsing and sequential network requests are offloaded to queue workers (ShouldQueue).
* Handle-Based Upserts: Queries Shopify by handle first. If the product exists, it runs a productUpdate instead of a productCreate.
* API 2026-04 Spec Ready: Uses modern decoupled mutations (productCreate/productUpdate -> productVariantUpdate -> collectionAddProducts)
* Separated Log / Data Layer: import_records acts as an operational runtime audit log while products holds the normalized, parsed fields.

------------------------------
## Technical Stack
* **Framework:** Laravel 12
* **Admin Penel:** AdminLTE 4.0
* **Version Control:** Git (GitHub Desktop)
* **Database Management:** SQLyog
* **IDE:** VS Code

------------------------------
## DB Schema Layout
## 1. uploads
Stores metadata for each uploaded file batch.

* file_name (string)
* file_path (string)
* status (string) -> pending, processing, completed, failed
* total_rows (integer)

## 2. import_records
Row-by-row audit trail linked to the main upload.

* upload_id (foreignId)
* handle (string, nullable)
* title (string, nullable)
* sku (string, nullable)
* price (string)
* status (string) -> pending, successful, failed
* payload_data (json) -> stores the full raw row array for debugging
* error_message (text, nullable) -> catches the raw userErrors from Shopify

## 3. products
The parsed and normalized product data.

* Core info: import_record_id, collection_id, handle, title, body_html, vendor, product_type, tags, published
* Variant info: variant_sku, variant_price, variant_compare_at_price, variant_requires_shipping, variant_taxable, variant_inventory_tracker, variant_inventory_qty, variant_inventory_policy, variant_fulfillment_service, variant_weight, variant_weight_unit
* Media: image_src, image_position, image_alt_text

## 4. log
Log all import events

* upload_id (foreignId)
* level (string) -> debug, info, warning, critical, error
* message (string)

------------------------------
## Environment Setup## 1. .env Configuration
```php
QUEUE_CONNECTION=database

SHOPIFY_SHOP_DOMAIN=://myshopify.com
SHOPIFY_ADMIN_ACCESS_TOKEN=shpat_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
SHOPIFY_API_VERSION=2026-04
SHOPIFY_COLLECTION_ID=gid://shopify/Collection/123456789
```
## 2. config/services.php
Add this array to expose your environment values to the app:
```php
'shopify' => [
    'shop_domain' => env('SHOPIFY_SHOP_DOMAIN'),
    'access_token' => env('SHOPIFY_ADMIN_ACCESS_TOKEN'),
    'api_version' => env('SHOPIFY_API_VERSION', '2026-04'),
    'default_collection_id' => env('SHOPIFY_COLLECTION_ID'),
],
```
------------------------------
## Installation & Running Locally

1. Install project dependencies:

    composer install

2. Run the tracking migrations to build the tables:

    php artisan migrate

3. Link the public storage directory for file uploads:

    php artisan storage:link

4. Boot the local server:

    php artisan serve

5. In a separate terminal tab, boot the background worker to process the incoming files:

    php artisan queue:work --queue=default --tries=3

------------------------------
## ⚠️ Known Issue: Missing Location ID & Restricted API Scopes

### Problem Statement
To update inventory levels in Shopify via the API, a valid `location_id` is strictly required. However, the integration faces a blocking issue under the following constraints:
1. **No Admin Access:** The development team cannot log into the Shopify Admin Dashboard to manually grab the location URL.
2. **Restricted API Token:** The assigned API access token lacks the `read_locations` permission scope, causing direct location API queries to fail.
