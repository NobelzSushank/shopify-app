<?php

use App\Models\Shop;
use GuzzleHttp\Client;

class ShopifyService
{
    protected Client $client;
    
    public function __construct()
    {
        $this->client = new Client();
    }
    
    public function graphql(Shop $shop, string $query, array $variables = []): array
    {
        $resp = $this->client->post("https://{$shop->domain}/admin/api/".env('SHOPIFY_API_VERSION')."/graphql.json", [
            'headers' => [
                'X-Shopify-Access-Token' => $shop->access_token,
                'Content-Type'           => 'application/json',
            ],
            'json' => ['query' => $query, 'variables' => $variables],
        ]);
        
        $body = json_decode((string)$resp->getBody(), true);
        
        if (!empty($body['errors'])) {
            throw new \RuntimeException(json_encode($body['errors']));
        }
        
        return $body['data'] ?? [];
    }
    
    public function fetchAllProducts(Shop $shop, ?string $statusFilter = null): \Generator
    {
        $after = null;
        $queryFilter = $statusFilter ? "status:{$statusFilter}" : null;
        $query = <<<'GQL'
                    query Products($after: String, $query: String) {
                        products(first: 250, after: $after, query: $query) {
                            pageInfo { hasNextPage endCursor }
                            edges {
                                node {
                                    id
                                    title
                                    status
                                    handle
                                    vendor
                                    productType
                                    tags
                                    createdAt
                                    updatedAt
                                    featuredImage { url }
                                }
                            }
                        }
                    }
                GQL;
                
        do {
            $data = $this->graphql($shop, $query, ['after' => $after, 'query' => $queryFilter]);
            $products = $data['products']['edges'] ?? [];
            foreach ($products as $edge) { yield $edge['node']; }
            $pageInfo = $data['products']['pageInfo'] ?? ['hasNextPage' => false, 'endCursor' => null];
            $after = $pageInfo['hasNextPage'] ? $pageInfo['endCursor'] : null;
        } while ($after);
    }
}