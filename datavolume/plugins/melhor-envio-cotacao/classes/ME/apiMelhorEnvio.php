<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once WC_ABSPATH.'/includes/wc-order-functions.php';

//COTAÇÂO 
function getNewQuotation($orders) {
    return getAllQuotationWordpress($orders);
}

function findQuotation($order_id) {

    $result = get_post_meta($order_id, 'cotacao_melhor_envio', true);
    if (!$result) {
        $resultQuotation = createQuotationMelhorEnvio($order_id);
        add_post_meta($order_id, 'cotacao_melhor_envio', $resultQuotation);
        return $resultQuotation;
    }
    return $result;
}

function createQuotationMelhorEnvio($order_id) {
    $postcodeClient = getPostCodeClient($order_id);
    $postcode = getPostCodeShop();
    $products = getProductsOrders($order_id);
    return calculatePackageApiMelhorEnvio($products, $postcode, $postcodeClient, $order_id);
}

//ENDEREÇOS
function getPostCodeClient($order_id) {
    $order = new WC_Order($order_id); // Order id
    $shipping_address = $order->get_address('shipping'); 
    return str_replace('-', '', $shipping_address['postcode']);
}

function getPostCodeShop() {
    $address = json_decode(str_replace("\\" ,"", get_option('wpmelhorenvio_address')));
    return $address->postal_code;
}

//PRODUTOS
function getProductsOrders($order_id) {
    $weight_unit = get_option('woocommerce_weight_unit');
    $order = new WC_Order( $order_id );
    $items = $order->get_items();
    $products = [];
    foreach ($items as $item) {
        $prod = wc_get_product($item->get_product_id());
        $products[] = [
            'id' => $item->get_product_id(),
            'name' => $item['name'],
            'quantity' => $item->get_quantity(),
            'insurance_value' => floatval($prod->get_price()),
            'width' => floatval($prod->get_width()),
            'height' => floatval($prod->get_height()),
            'weight' =>($weight_unit == 'g') ? floatval($prod->get_weight()) / 1000 : floatval($prod->get_weight()),
            'length' => floatval($prod->get_length())
        ];
    }
    return $products;
}

function getProductsCartME($cart) {
    $weight_unit = get_option('woocommerce_weight_unit');
    $products = [];
    foreach ($cart as $item) {
        $prod = wc_get_product($item['data']->id);
        $products[] = [
            'id' => $prod->get_id(),
            'name' => $prod->get_name(),
            'quantity' => $item['quantity'],
            'insurance_value' => floatval($prod->get_price()),
            'width' => floatval($prod->get_width()),
            'height' => floatval($prod->get_height()),
            'weight' =>($weight_unit == 'g') ? floatval($prod->get_weight()) / 1000 : floatval($prod->get_weight()),
            'length' => floatval($prod->get_length())
        ];
    }
    return $products;
}

//TODO
function getValueInsuranceProducts($products) {
    $value = 0;
    foreach ($products as $product) {
        $value = $value + floatval($product['insurance_value']);
    }
    return $value;
}

function calculatePackageApiMelhorEnvio($products, $postcode, $postcodeClient, $order_id) {

    $token = get_option('wpmelhorenvio_token');
    $client = new WP_Http();

    $body = [
        "from" => [
            "postal_code" => $postcode
        ],
        'to' => [
            'postal_code' => $postcodeClient
        ],
        'products' => $products,
        'options' => [
            "insurance_value" => getValueInsuranceProducts($products),
            "receipt"         => false, 
            "own_hand"        => false, 
            "collect"         => false 
        ],
        "services" => '1,2,3,4,7'
    ];

    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$token,
            'user-agent'    => sprintf("WordPress/%s; WooCommerce/%s; MelhorEnvio/%s (PHP %s); %s", 
                get_bloginfo('version'), 
                $woocommerce->version, 
                VERSION_PLUGIN_MELHOR_ENVIO,    
                phpversion(),
                "http://$_SERVER[HTTP_HOST]"
            )
        ],
        'body'  => json_encode($body),
        'timeout'=>10
    );

    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/calculate',$params);

    if (is_wp_error($response)) {
        insertLogErrorMelhorEnvioGeneric(json_encode($response->errors));
        return null;
    }

    $respApi = json_decode($response['body']);
    if($respApi->errors){
        foreach ($respApi->errors as $key => $errors) {
            foreach ($errors as $error) {
                insertLogErrorMelhorEnvioGeneric($error);
            }
        }
        return false;
    }

    $response = setShippingSelected(json_decode($response['body']), $order_id);
    $response = normalizeToArray($response, $postcode, $postcodeClient);
    return $response;
}

function normalizeToArray($data, $postcode, $postcodeClient) {
    $result = [];
    foreach ($data as $item) {
        $packages = [];
        if (!empty($item->packages)) {
            foreach ($item->packages as $pack) { 
                $products = [];
                foreach ($pack->products as $product) {
                    $products[] = [
                        'id' => $product->id,
                        'quantity' => $product->quantity
                    ];
                }
                $packages[] = [
                    'price' => $pack->price,
                    'discount' => $pack->discount,
                    'format' => $pack->format,
                    'dimensions' => [
                        'height' => $pack->dimensions->height,
                        'width' => $pack->dimensions->width,
                        'length' => $pack->dimensions->length
                    ],
                    'weight' => $pack->weight,
                    'insurance_value' => $pack->insurance_value,
                    'products' => $products
                ];
            }
        }

        $result[] = [
            'id' => $item->id,
            'name' => $item->name,
            'price' => $item->price,
            'delivery_time' => $item->delivery_time,
            'currency' => $item->currency,
            'delivery_range' => [
                'min' => $item->delivery_range->min,
                'max' => $item->delivery_range->max
            ],
            'packages' => $packages,
            'additional_services' => [
                'receipt' => $item->additional_services->receipt,
                'own_hand' => $item->additional_services->own_hand,
                'collect' => $item->additional_services->collect
            ],
            'company' => [
                'id' => $item->company->id,
                'name' => $item->company->name
            ],
            'selected' => $item->selected,
            'postcode' => $postcode,
            'postcode_client' => $postcodeClient
        ];
    }
    return $result;
}

//OPÇÔES DE ENTREGA
function setShippingSelected($response, $order_id) {
    $result = [];
    $choose = get_post_meta($order_id, 'quotation_choose', true);

    foreach ($response as $key => $item) {
        if ($item->id == intval($choose)) {
            $response[$key]->selected = true;
        } else {
            $response[$key]->selected = false;
        }   
    }
    return $response;
}

//BANCO DE DADOS WORDPRESS
function getAllQuotationWordpress($orders) {

    $ids = '';
    foreach ($orders as $order) {
        $ids = $ids . "'" . $order->get_id() . "',";
    }
    $ids = rtrim($ids,',');

    global $wpdb;
    $result = $wpdb->get_results("
        SELECT * 
        FROM $wpdb->postmeta 
        WHERE $wpdb->postmeta.meta_key = 'cotacao_melhor_envio' AND $wpdb->postmeta.post_id IN ($ids)",
        ARRAY_A
    );

    $metas = [];
    foreach ($result as $item) {
        $metas[$item['post_id']] = $item['meta_value'];
    }
		
    $responseMeta = [];
    foreach ($orders as $item) {
        $metaId = $metas[$item->get_id()];

        if (is_null($metaId)) {
			$id   =  intval($item->get_id());
            $quot = findQuotation($item->get_id());
            if (!$quot) {
                return false;
            }
            $responseMeta[$id] = $quot;
        } else {
			$id   =  intval($item->get_id());
            $quot =  unserialize($metas[$item->get_id()]);
            $responseMeta[$id] = $quot;
		}
    }
    return $responseMeta;
}

// Pegar dados do documento do usuário.
function wpmelhorenvio_getDocumentsApi() {
    $documents = get_option('documents_melhor_envio');
    if (!$documents) {
        update_documents_get_api();

        return $documents;
    }

    if (!isset($documents['updated_at']) || $documents['updated_at'] < date('Y-m-d') ) {
        update_documents_get_api();
        return $documents;
    }
    return $documents;
}

function update_documents_get_api() {
    $data = wpmelhorenvio_getApiCompanies();
    $saved_company =  json_decode(str_replace("\\",'',get_option('wpmelhorenvio_company')));
    if($saved_company == null){
        $saved_company = new stdClass();
        $saved_company->id = '';
    }
    
    $storeSelect = null;
    foreach($data['data'] as $store) {
        if($store->id == $saved_company->id){
            $storeSelect = $store;
        }
    } 

    $documents = [
        'document'       => $storeSelect->document,
        'state_register' => $storeSelect->state_register,
        'updated_at'     => date('Y-m-d')
    ];

    add_option('documents_melhor_envio', $documents, true);
    return $documents;
}

// Função para pegar Nota fiscal e Chave da nota fiscal
function wpmelhorenvio_getDocsOrdes($orders) {

    $ids = '';
    foreach ($orders as $order) {
        $ids = $ids . "'" . $order->get_id() . "',";
    }
    $ids = rtrim($ids,',');

    global $wpdb;
    $result = $wpdb->get_results("
        SELECT * 
        FROM $wpdb->postmeta 
        WHERE $wpdb->postmeta.meta_key = 'wpme_info_order_docs' AND $wpdb->postmeta.post_id IN ($ids)",
        ARRAY_A
    );

    $response = [];
    foreach ($result as $item) {
        $response[$item['post_id']] = unserialize($item['meta_value']);
    }
    return $response;
}