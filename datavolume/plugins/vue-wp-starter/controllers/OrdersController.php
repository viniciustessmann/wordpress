<?php

namespace Controllers;

use Models\Order;

class OrdersController {

    public function index() {
        echo 'Teste arrombado funciona.';
        die;
    }

    public function getOrders() {
        $order = new Order(749)->retrieveOne();
        echo json_encode($order);
        die;
    }
}

