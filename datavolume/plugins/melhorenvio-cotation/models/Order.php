<?php

namespace Models;

use Bases\bOrders;

class Order extends bOrders {

    public function __construct() {

    }

    /**
     * Retrieve One Order by its ID.
     *
     * @param [Int] $id
     * @return object
     */
    // public function retrieveOne($id) : object
    // {
    //     $post = get_post($id);

    //     $order = [
    //         'ID' => $post->ID,
    //     ];

    //     return (object) [
    //         'message' => 'Testing OOP WP',
    //         'method' => 'OrdersModel@retrieveOne',
    //         'data' => $post
    //     ];
    // }

}