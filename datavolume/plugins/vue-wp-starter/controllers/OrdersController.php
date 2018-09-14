<?php

namespace Controllers;

use Models\Order;

class OrdersController {

    public function index() {
        echo 'Teste arrombado funciona.';
        die;
    }

    public function getOrders() {
        
        $orders = Order::retrieveMany();

        echo '<pre>';
        var_dump($orders);
        die;
    }
}

