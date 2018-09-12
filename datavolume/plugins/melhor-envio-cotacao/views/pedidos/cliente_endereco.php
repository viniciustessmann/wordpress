<?php $shippingClient = $order->get_address('shipping'); ?>

<ul>
    <li><strong><?php echo $shippingClient['first_name'] . ' ' . $shippingClient['last_name'] ?> </strong></li>
    <li><?php echo $shippingClient['address_1'] . ' ' . $shippingClient['number'] . ' ' . $shippingClient['address_2'] . ' - ' . $shippingClient['postcode'] ?></li>
    <li><?php echo $shippingClient['neighborhood'] . ' - ' . $shippingClient['city'] . '/'. $shippingClient['state']  ?></li>   
</ul>
<?php
    $infoShippingClient[$index] = [
        'name'             => $shippingClient['first_name'] . ' ' . $shippingClient['last_name'],
        'phone'            => $phone,
        'email'            => $wcOrder->get_billing_email(),
        'document'         => $wcOrder->billing_cpf,
        'company_document' => $wcOrder->billing_cnpj,
        'state_register'   => $wcOrder->billing_ie,
        'address'          => $shippingClient['address_1'],
        'complement'       => $shippingClient['address_2'],
        'number'           => $shippingClient['number'],
        'district'         => $shippingClient['neighborhood'],
        'city'             => $shippingClient['city'],
        'state_abbr'       => $shippingClient['state'],
        'country_id'       => $shippingClient['country'],
        'postal_code'      => $shippingClient['postcode']
    ];
?>

        