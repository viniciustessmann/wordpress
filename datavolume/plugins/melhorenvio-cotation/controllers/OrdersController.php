<?php

namespace MelhorEnvio;

class OrdersController {

    public function __construct(){

    }

    public function getAll() {
        include_once WC_ABSPATH.'/includes/wc-order-functions.php';
        return wc_get_orders([]);
    }
}

