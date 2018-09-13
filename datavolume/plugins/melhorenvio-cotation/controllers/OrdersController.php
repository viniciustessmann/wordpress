<?php

namespace Controllers;

use Models\Order;

class OrdersController {

    public function index() {
        var_dump(Order::retrieveMany());
        die;
    }
}

