<?php

namespace Controllers;
use Models\User;

class OrdersController {

    public function __construct(){

    }

    public function getAll() {

        $user = new User();

        print_r($user);
        die;

        $orders =  wc_get_orders([]);
        $response = [];

        foreach ($orders as $item) {
            $response[] = (object) [
                'id'   => $item->get_id(),
                'status' => null,
                'from' => [],
                'to' => [],
                'products' => [
                    'name' => null,
                    'quantity' => 1,
                    'unitary_value' => 9.90,
                    'weight' => 1
                ],
                'package' => [
                    'weight' => 1,
                    'width' => 12,
                    'height' => 4,
                    'length' => 17
                ],
                'options' => [
                    'insurance_value' => 0,
                    'receipt' => false,
                    'own_hand' => false,
                    'collect' => false,
                    'reverse' => false,
                    'non_commercial' => false,
                    'invoice' => [
                        'number' => null,
                        'key' => null
                    ]
                ]
            ];
        }
        return $response;
    }

    private function getStatus() {

    }

    private function getOrdersMelhorEnvio() {

    }

    public function send($order) {

    }
}

