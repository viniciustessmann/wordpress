<?php

namespace MelhorEnvio;

class OrdersController {

    public function __construct(){

    }

    public function getAll() {

        $orders =  wc_get_orders([]);

        $response = [];
        foreach ($orders as $item) {

            $response[] = (object) [
                'id'   => $item->get_id()
            ];
        }

        return json_encode($response);
    }
}

