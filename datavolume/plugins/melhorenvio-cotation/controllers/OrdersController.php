<?php

namespace Controllers;

use Models\Order;

class OrdersController {

    public function index() {
        //  Retrieve orders and load view.
        print_r((new Order)->retrieveOne(749));
        die;
    }
}

