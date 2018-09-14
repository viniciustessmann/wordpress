<?php

namespace Controllers;

use Models\Order;

class OrdersController {

    public function index() {
        $orders = Order::retrieveMany();
    }

    public function getOrders() {
        
        $orders = Order::retrieveMany();

        echo '<pre>';
        var_dump($orders);
        die;
    }
}

