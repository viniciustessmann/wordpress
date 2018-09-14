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
     * @param Array $attributes
     * @return void
     */
    public function setAttributes(Array $attributes) : void
    {
        //  code
    }

    /**
     * @return object
     */
    public function retrieveOne() : object
    {
        $post = get_post(['post_id' => $id, 'post_type' => 'shop_order']);
        
        return (object) [
            'message' => 'Testing OOP WP',
            'method' => 'OrdersModel@retrieveOne',
            'data' => $post
        ];
    }

    /**
     * @param Array $filters
     * @return Array
     */
    public static function retrieveMany(Array $filters = NULL) : Array
    {
        $defaults = array();

        return get_posts([]);
    }


    /**
     * @param Array $data
     * @return object
     */
    public function update(Array $data) : object
    {
        return (object) [
            'message' => 'Testing OOP WP',
            'method' => 'OrdersModel@update'
        ];
    }

}