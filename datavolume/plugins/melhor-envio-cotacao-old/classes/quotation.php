<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Created by PhpStorm.
 * User: VHSoa
 * Date: 03/12/2017
 * Time: 23:33
 */
include_once ABSPATH.WPINC.'/option.php';
include_once ABSPATH.WPINC.'/class-requests.php';

function wpmelhorenvio_getCotacao($package){

    $client = new WP_Http();
    $token = get_option('wpmelhorenvio_token');
    $pacote =  wpmelhorenvio_getPackage($package);

    $opcionais = wpmelhorenvio_getOptionals();
    $seguro = wpmelhorenvio_getValueInsurance($pacote->value,$opcionais->VD);
    $params = array(
        'headers'=>['Content-Type'  => 'application/json',
            'Accept'        =>'application/json',
            'Authorization' => 'Bearer '.$token],
        'body'  =>['from'   => wpmelhorenvio_getFrom(),
            'to'        => wpmelhorenvio_getTo($package),
            'width'     => $pacote->width,
            'height'    => $pacote->height,
            'length'    => $pacote->length,
            'weight'    => $pacote->weight,
            'receipt'   => $opcionais->AR,
            'own_hand'  => $opcionais->MP,
            'insurance_value' => $seguro,
            'services'  => wpmelhorenvio_getSavedServices()
        ],
        'timeout'=>10);


    $response = $client->get("https://www.melhorenvio.com.br/api/v2/calculator",$params);

    wpmelhorenvio_logsError($response, $package, $pacote);

    return is_array($response) ?  json_decode($response['body']) : [];
}

function wpmelhorenvio_logsError($response, $package, $pacote) {

    $errors = '';
    foreach (json_decode($response['body']) as $item) {
        if (isset($item->error)) {
            $errors .=   "\n\r" . $item->name . " - " . $item->error ;
        }
    }

    if (empty($errors)) {
        return false;
    }

    $errors .=   "\n\r" .   "Largura:  " . $pacote->width;
    $errors .=   "\n\r" .   "Altura:  " . $pacote->height;
    $errors .=   "\n\r" .   "Comprimento:  " . $pacote->length;
    $errors .=   "\n\r" .   "Peso:  " . $pacote->weight;

    $monthPast = date('Y-m-d', strtotime('-1 month', time())); 
    $dateNow = date('Y-m-d');
    $uploads = wp_upload_dir(); 

    if (!is_dir($uploads['basedir'] . '/logs_melhor_envio')) {
        mkdir($uploads['basedir'] . '/logs_melhor_envio');
    }

    wpmelhorenvio_deleteOldLogs($monthPast);

    if (!file_exists($uploads['basedir'] . '/logs_melhor_envio/' . $dateNow)) {
        $fileError = fopen($uploads['basedir'] . '/logs_melhor_envio/' . $dateNow, "w");
    }
    
    $contentError = file_get_contents($uploads['basedir'] . '/logs_melhor_envio/' . $dateNow);
    foreach ($package['contents'] as $index => $item) {

        $key = $item['key'];
        if (isset($package['contents'][$key])){
            foreach ($package['contents'][$key] as $index => $item) {
                
                if ($item instanceof WC_Product_Variation) {
                    $contentError .=  '###########################################################';
                    $contentError .=  "\n\r" . 'Data: ' .date('Y-m-d h:i:s') . "\n\r" . '('. $item->get_parent_id() . ') ' . $item->get_name() . '|' . $errors . "\n\r";
                }
            }
        }
    }

    $fileError = fopen($uploads['basedir'] . '/logs_melhor_envio/' . $dateNow, "w");
    fwrite($fileError, $contentError);
}

function wpmelhorenvio_deleteOldLogs($monthPast) {

    $uploads = wp_upload_dir(); 
    $folder = $uploads['basedir'] . '/logs_melhor_envio';

    foreach (scandir($folder) as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        if ($file < $monthPast) {
            unlink($folder . '/' . $file);
        }
    }
}

function wpmelhorenvio_getPackage($package){

    $weight =0;
    $valor =0;
    $pacote = new stdClass();

    $products = [];
    foreach ($package['contents'] as $item){

        $weight = $weight + 1  * $item['quantity'];
        $valor = wc_get_product($item['product_id'])->get_price() * $item['quantity'];

        $products[] = [
            "id"       => $item['product_id'], 
            "weight"   =>  1, 
            "width"    =>  wc_get_product($item['product_id'])->get_width(), 
            "height"   => wc_get_product($item['product_id'])->get_height(), 
            "length"   => wc_get_product($item['product_id'])->get_length(),
            "quantity" => $item['quantity'], 
            "insurance_value" => wc_get_product($item['product_id'])->get_price() * $item['quantity']
        ];

    }

    $token = get_option('wpmelhorenvio_token');
    $client = new WP_Http();

    $body = [
        "from" => [
            "postal_code" => "96020360",
            'address'     => 'Endereço do remetente',
            'number'      => '1'
        ],
        'to' => [
            'postal_code' => '96065710',
            'address'     => 'Endereço do destinatario',
            'number'      => '2'
        ],
        'products' => $products,
        'options' => [
            "insurance_value" => 20.50,
            "receipt"         => false, 
            "own_hand"        => false, 
            "collect"         => false 
        ],
        "services" => "1,2" 
    ];

    $params = array(
        'headers'           =>  [
            'Content-Type'  => 'application/json',
            'Accept'        =>  'application/json',
            'Authorization' =>  'Bearer '.$token
        ],
        'body'  => json_encode($body),
        'timeout'=>10);

    $response = $client->post('https://www.melhorenvio.com.br/api/v2/me/shipment/calculate',$params);

    $resposta = json_decode($response['body']);

    $side   =  ceil(pow($volume,1/3));
    $pacote->width  = $resposta[0]->packages[0]->dimensions->width >= 12  ? $resposta[0]->packages[0]->dimensions->width : 12;
    $pacote->height = $resposta[0]->packages[0]->dimensions->height >= 4   ? $resposta[0]->packages[0]->dimensions->height : 4;
    $pacote->length = $resposta[0]->packages[0]->dimensions->length >= 17  ? $resposta[0]->packages[0]->dimensions->length : 17;
    $pacote->weight = $weight;
    $pacote->value = $valor;

    return $pacote;
}

function wpmelhorenvio_getFrom(){
    $remetente = json_decode(str_replace("\\" ,"", get_option('wpmelhorenvio_address')));
    $from = $remetente->postal_code;

    return $from;
}

function wpmelhorenvio_getTo($package){
    $destinatario = $package['destination']['postcode'];
    return $destinatario;
}

function wpmelhorenvio_getOptionals(){
    $optionals = json_decode(get_option('wpmelhorenvio_pluginconfig'));
    $optionals->AR = $optionals->AR? 'true' : 'false';
    $optionals->MP = $optionals->MP? 'true' : 'false';
    $optionals->PL = is_numeric($optionals->PL)? $optionals->PL : 0;
    $optionals->DE = is_numeric($optionals->DE)? $optionals->DE : 0;
    return $optionals;
}

function wpmelhorenvio_getPostOptionals(){
    $optionals = json_decode(get_option('wpmelhorenvio_pluginconfig'));
    $optionals->PL = is_numeric($optionals->PL)? $optionals->PL : 0;
    $optionals->DE = is_numeric($optionals->DE)? $optionals->DE : 0;
    return $optionals;
}

function wpmelhorenvio_getSavedServices(){
    $services = join(',',json_decode(get_option('wpmelhorenvio_services')));
    return $services;
}

function wpmelhorenvio_getValueInsurance($valor,$situacao){
    return $situacao ? $valor : 0;
}

