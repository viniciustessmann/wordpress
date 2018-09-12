<?php
    $wcOrder = new WC_Order($order->get_id());
    $lineItems = wpmelhorenvio_getLineItems($wcOrder);    

    // Pega documento do cliente, CPF ou CNPJ
    $client_document = null;
    if ($wcOrder->billing_cpf){
        $client_document = $wcOrder->billing_cpf;
    }
    if ($wcOrder->billing_cnpj){
        $client_document = $wcOrder->billing_cnpj;
    }

    // Pega telefone ou celular do cliente.
    $phone = null;
    if ($wcOrder->get_billing_phone()) {
        $phone = $wcOrder->get_billing_phone();
    }
?>
<input type="hidden" class="order_id_index_<?php echo $index; ?>" value="<?php echo $order->get_id(); ?>" />
<?php
    $infoPackage = $cotacoesAll[$order->get_id()][0]['packages'][0];
    $infoOrderClient[$index] = [
        'order_id'       => $order->get_id(),
        'document'       => $client_document,
        'price_declared' => $wcOrder->get_total(),
        'products'       => $lineItems,
        'packages' => [
            'weight' => $infoPackage['weight'],
            'width'  => $infoPackage['dimensions']['width'],
            'height' => $infoPackage['dimensions']['height'],
            'length' => $infoPackage['dimensions']['length']
        ]
    ];

    $id          = $order->get_id();
    $status_wc   = $infosTrackings[$id]['status_wc'];
    $status_me   = $infosTrackings[$id]['status_me'];
    $tracking_id = $infosTrackings[$id]['tracking_id'];
    $tracking_mr = $infosTrackings[$id]['tracking_mr'];
?>