<?php

namespace Controllers;

use Models\Order;

class OrdersController {

    public function index() {
        echo 'test';

        echo wp_json_send([
            'test' => 'This is an ajax test'
        ]);
    }
}

