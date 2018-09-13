<?php

namespace Interfaces;

interface iOrders
{
    /**
     * Set class attributes from an Array.
     *
     * @param [Array] $attributes
     * @return void
     */
    public function setAttributes($attributes) : void;

    /**
     * Retrieve One Order by its ID.
     *
     * @param [Int] $order
     * @return object
     */
    public function retrieveOne($order) : object;

    /**
     * Return Many orders.
     *
     * @param [type] $orders
     * @param [type] $filters
     * @return object
     */
    public function retrieveMany($orders, $filters = NULL) : object;

    /**
     * Update a specific order by its ID.
     *
     * @param [type] $order
     * @param [type] $data
     * @return object
     */
    public function update($order, $data) : object;

    /**
     * Delete specific order by its ID.
     *
     * @param [type] $order
     * @return void
     */
    public function delete($order) : void;
}