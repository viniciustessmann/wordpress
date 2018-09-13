<?php

namespace Bases;

use Interfaces\iOrders;

class bOrders implements iOrders {

    private $id;
    private $status;
    private $from;
    private $to;
    private $products;
    private $package;
    private $options;

    /**
     * Set class attributes from an Array.
     *
     * @param [Array] $attributes
     * @return void
     */
    public function setAttributes($attributes) : void
    {
        //  code
    }

    /**
     * Retrieve One Order by its ID.
     *
     * @param [Int] $id
     * @return object
     */
    public function retrieveOne($id) : object
    {
        $post = get_post($id);
        
        return (object) [
            'message' => 'Testing OOP WP',
            'method' => 'OrdersModel@retrieveOne',
            'data' => $post
        ];
    }

    /**
     * Return Many orders.
     *
     * @param [type] $orders
     * @param [type] $filters
     * @return object
     */
    public function retrieveMany($orders, $filters = NULL) : object
    {
        return (object) [
            'message' => 'Testing OOP WP',
            'method' => 'OrdersModel@retrieveMany'
        ];
    }

    /**
     * Update a specific order by its ID.
     *
     * @param [type] $order
     * @param [type] $data
     * @return object
     */
    public function update($order, $data) : object
    {
        return (object) [
            'message' => 'Testing OOP WP',
            'method' => 'OrdersModel@update'
        ];
    }

    /**
     * Delete specific order by its ID.
     *
     * @param [type] $order
     * @return void
     */
    public function delete($order) : void
    {
        //  code
    }
}