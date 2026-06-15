<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Models\ImportRecord;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class ProcessCsvImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Upload $upload) {}

    public function handle(): void
    {
        add_log($this->upload->id, 'debug', "ProcessCsvImport started execution for Upload ID: " . $this->upload->id);
        $this->upload->update(['status' => 'processing']);
        $filePath = Storage::path($this->upload->file_path);

        try {
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();

            $this->upload->update(['total_rows' => iterator_count($records)]);
            add_log($this->upload->id, 'debug', "Total parsed rows calculated: " . $this->upload->total_rows);

            $targetCollectionId = config('services.shopify.default_collection_id');

            foreach ($csv->getRecords() as $row) {
                add_log($this->upload->id, 'debug', "Processing single entry for SKU: " . ($row['Variant SKU'] ?? 'N/A'));

                $importRecord = ImportRecord::create([
                    'upload_id'    => $this->upload->id,
                    'handle'       => $row['Handle'] ?? null,
                    'title'        => $row['Title'] ?? 'Unnamed Product',
                    'sku'          => $row['Variant SKU'] ?? null,
                    'price'        => $row['Variant Price'] ?? '0.00',
                    'status'       => 'pending',
                    'payload_data' => $row,
                ]);

                $product = Product::create([
                    'import_record_id'           => $importRecord->id,
                    'collection_id'              => $targetCollectionId,
                    'handle'                     => $row['Handle'] ?? null,
                    'title'                      => $row['Title'] ?? 'Unnamed Product',
                    'body_html'                  => $row['Body HTML'] ?? null,
                    'vendor'                     => $row['Vendor'] ?? null,
                    'product_type'               => $row['Product Type'] ?? null,
                    'tags'                       => $row['Tags'] ?? null,
                    'published'                  => filter_var($row['Published'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'variant_sku'                => $row['Variant SKU'] ?? null,
                    'variant_price'              => !empty($row['Variant Price']) ? $row['Variant Price'] : 0.00,
                    'variant_compare_at_price'   => !empty($row['Variant Compare At Price']) ? $row['Variant Compare At Price'] : null,
                    'variant_requires_shipping'  => filter_var($row['Variant Requires Shipping'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'variant_taxable'            => filter_var($row['Variant Taxable'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'variant_inventory_tracker'  => $row['Variant Inventory Tracker'] ?? null,
                    'variant_inventory_qty'      => !empty($row['Variant Inventory Qty']) ? (int)$row['Variant Inventory Qty'] : 0,
                    'variant_inventory_policy'   => $row['Variant Inventory Policy'] ?? null,
                    'variant_fulfillment_service' => $row['Variant Fulfillment Service'] ?? null,
                    'variant_weight'             => !empty($row['Variant Weight']) ? $row['Variant Weight'] : 0.00,
                    'variant_weight_unit'        => $row['Variant Weight Unit'] ?? null,
                    'image_src'                  => $row['Image Src'] ?? null,
                    'image_position'             => !empty($row['Image Position']) ? (int)$row['Image Position'] : null,
                    'image_alt_text'             => $row['Image Alt Text'] ?? null,
                ]);
                $this->uploadToShopifyGraphQL($importRecord, $product);
            }

            $this->upload->update(['status' => 'completed']);
            add_log($this->upload->id, 'debug', "ProcessCsvImport successfully completed for Upload ID: " . $this->upload->id);
        } catch (\Exception $e) {
            $this->upload->update(['status' => 'failed']);
            add_log($this->upload->id, 'critical', "CSV background process failed on row generation: " . $e->getMessage());
        }
    }

    protected function uploadToShopifyGraphQL(ImportRecord $record, Product $product): void
    {
        add_log($this->upload->id, 'info', "Starting sync cycle for Product: " . $product->title);
        $record->update(['status' => 'processing']);

        $shop = config('services.shopify.shop_domain');
        $token = config('services.shopify.access_token');
        $collection_id = config('services.shopify.default_collection_id');
        $endpoint = "https://{$shop}/admin/api/2026-04/graphql.json";

        $existingProductId = null;
        $variantId = null;

        if (!empty($product->handle)) {
            add_log($this->upload->id, 'info', "Checking if product with handle already exists: " . $product->handle);

            $findQuery = 'query getProductByHandle($query: String!) {
                products(first: 1, query: $query) {
                    edges {
                        node {
                            id
                            variants(first: 1) {
                                edges {
                                    node {
                                        id
                                    }
                                }
                            }
                        }
                    }
                }
            }';

            try {
                $findResponse = Http::withHeaders([
                    'X-Shopify-Access-Token' => $token,
                    'Content-Type' => 'application/json',
                ])->post($endpoint, [
                    'query' => $findQuery,
                    'variables' => ['query' => 'handle:' . $product->handle]
                ]);

                $findResult = $findResponse->json();
                $productEdge = $findResult['data']['products']['edges'][0]['node'] ?? null;

                if ($productEdge) {
                    $existingProductId = $productEdge['id'];
                    $variantId = $productEdge['variants']['edges'][0]['node']['id'] ?? null;
                    add_log($this->upload->id, 'info', "Match found on Shopify. Product ID: {$existingProductId}, Variant ID: {$variantId}");
                }
            } catch (\Exception $e) {
                add_log($this->upload->id, 'error', "Failed to check existing product handle lookup: " . $e->getMessage());
            }
        }

        try {
            if ($existingProductId) {
                add_log($this->upload->id, 'info', "Executing update pipeline for Shopify Product ID: " . $existingProductId);

                $productMutation = 'mutation productUpdate($product: ProductInput!) {
                    productUpdate(input: $product) {
                        product { id }
                        userErrors { field message }
                    }
                }';

                $productVariables = [
                    'product' => [
                        'id' => $existingProductId,
                        'title' => $product->title,
                        'descriptionHtml' => $product->body_html ?? '',
                        'vendor' => $product->vendor ?? '',
                        'productType' => $product->product_type ?? '',
                        'tags' => $product->tags ? array_map('trim', explode(',', $product->tags)) : [],
                        'status' => $product->published ? 'ACTIVE' : 'DRAFT'
                    ]
                ];

                $response = Http::withHeaders([
                    'X-Shopify-Access-Token' => $token,
                    'Content-Type' => 'application/json',
                ])->post($endpoint, ['query' => $productMutation, 'variables' => $productVariables]);
                $result = $response->json();

                if (!empty($result['data']['productUpdate']['userErrors'])) {
                    $errorMsg = $result['data']['productUpdate']['userErrors'][0]['message'];
                    add_log($this->upload->id, 'warning', "Shopify productUpdate validation failed: " . $errorMsg);
                    $record->update(['status' => 'failed', 'error_message' => $errorMsg]);
                    return;
                }
            } else {
                add_log($this->upload->id, 'info', "Executing creation pipeline for new product.");

                $productMutation = 'mutation productCreate($product: ProductCreateInput!) {
                    productCreate(input: $product) {
                        product {
                            id
                            variants(first: 1) {
                                edges {
                                    node {
                                        id
                                    }
                                }
                            }
                        }
                        userErrors { field message }
                    }
                }';

                $productVariables = [
                    'product' => [
                        'title' => $product->title,
                        'descriptionHtml' => $product->body_html ?? '',
                        'vendor' => $product->vendor ?? '',
                        'productType' => $product->product_type ?? '',
                        'tags' => $product->tags ? array_map('trim', explode(',', $product->tags)) : [],
                        'status' => $product->published ? 'ACTIVE' : 'DRAFT'
                    ]
                ];

                $response = Http::withHeaders(['X-Shopify-Access-Token' => $token, 'Content-Type' => 'application/json',])->post($endpoint, ['query' => $productMutation, 'variables' => $productVariables]);
                $result = $response->json();
                if (!empty($result['data']['productCreate']['userErrors'])) {
                    $errorMsg = $result['data']['productCreate']['userErrors'][0]['message'];
                    add_log($this->upload->id, 'warning', "Shopify productCreate validation failed: " . $errorMsg);
                    $record->update(['status' => 'failed', 'error_message' => $errorMsg]);
                    return;
                }
                $shopifyProduct = $result['data']['productCreate']['product'] ?? null;
                if (!$shopifyProduct) {
                    add_log($this->upload->id, 'warning', "Shopify payload structural discrepancy caught during entity generation.");
                    $record->update(['status' => 'failed', 'error_message' => 'Shopify returned an empty product creation response.']);
                    return;
                }
                $existingProductId = $shopifyProduct['id'];
                $variantId = $shopifyProduct['variants']['edges'][0]['node']['id'] ?? null;
            }

            if (!$variantId) {
                add_log($this->upload->id, 'warning', "No default structural variant reference found or returned.");
                $record->update(['status' => 'failed', 'error_message' => 'Unable to resolve product variant identity context.']);
                return;
            }

            add_log($this->upload->id, 'info', "Updating variant properties for Identity: " . $variantId);
            $variantUpdateQuery = 'mutation productVariantUpdate($input: ProductVariantInput!) {productVariantUpdate(input: $input) {userErrors { field message }}}';
            $variantVariables = ['input' => ['id' => $variantId, 'sku' => $product->variant_sku ?? '', 'price' => (string)$product->variant_price, 'compareAtPrice' => $product->variant_compare_at_price ? (string)$product->variant_compare_at_price : null, 'requiresShipping' => $product->variant_requires_shipping, 'taxable' => $product->variant_taxable, 'inventoryPolicy' => !empty($product->variant_inventory_policy) ? strtoupper($product->variant_inventory_policy) : 'DENY', 'weight' => ['value' => (float)$product->variant_weight, 'unit' => strtoupper($product->variant_weight_unit ?? 'KG')]]];
            Http::withHeaders(['X-Shopify-Access-Token' => $token, 'Content-Type' => 'application/json',])->post($endpoint, ['query' => $variantUpdateQuery, 'variables' => $variantVariables]);

            if ($collection_id) {
                add_log($this->upload->id, 'info', "Attaching item to Collection GID context: " . $collection_id);
                $collectionQuery = 'mutation collectionAddProducts($id: ID!, $productIds: [ID!]!) {collectionAddProducts(id: $id, productIds: $productIds) {userErrors { field message }}}';
                $collectionVariables = ['id' => $collection_id, 'productIds' => [$existingProductId]];
                $collectionResponse = Http::withHeaders(['X-Shopify-Access-Token' => $token, 'Content-Type' => 'application/json',])->post($endpoint, ['query' => $collectionQuery, 'variables' => $collectionVariables]);
                $collectionResult = $collectionResponse->json();
                if (!empty($collectionResult['data']['collectionAddProducts']['userErrors'])) {
                    $errorMsg = $collectionResult['data']['collectionAddProducts']['userErrors'][0]['message'];
                    add_log($this->upload->id, 'warning', "Shopify collectionAddProducts failed: " . $errorMsg);
                } else {
                    add_log($this->upload->id, 'info', "Product successfully attached to collection context.");
                }
            }
            $record->update(['status' => 'successful', 'error_message' => null]);
            add_log($this->upload->id, 'info', "Completed single transaction execution upsert path cleanly.");
        } catch (\Exception $e) {
            add_log($this->upload->id, 'critical', "GraphQL sync routine caught structural failure: " . $e->getMessage());
            $record->update(['status' => 'failed', 'error_message' => 'GraphQL sequence failure: ' . $e->getMessage()]);
        }
    }
}
