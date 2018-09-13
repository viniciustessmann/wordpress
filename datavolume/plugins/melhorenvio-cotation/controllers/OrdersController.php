<?php

namespace Controllers;

use Models\Order;

class OrdersController {

    public function index() {
        //  Retrieve orders and load view.
        echo '<pre>';
        print_r((new Order(749)));
        die;
    }
}

