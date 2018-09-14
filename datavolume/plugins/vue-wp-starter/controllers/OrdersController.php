<?php

namespace Controllers;

use Models\Order;

class OrdersController {

    public function index() {
       $order = (new Order(749))->retrieveOne();
       var_dump($order);
       die;
    }

    public function getOrders() {
        $order = new Order();
        var_dump($order);
        die;
    }
}

