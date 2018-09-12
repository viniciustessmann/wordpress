<?php

namespace Models;

class Order {

    private $id;
    private $status;
    private $from;
    private $to;
    private $products;
    private $package;
    private $options;

    public function __construct() {
        
    }

    public function setAttribute($attribute, $value) {
        $this->{$attribute} = $value;
    }

    public function get($order_id) {
        
        $this->id = $order_id;
        $this->total = $this->getTotal();

        return $this;
    }   

    private function getTotal() {

    }

    public function send() {

    }

    public function getStatusApi() {

    }
}