<?php

namespace Interfaces;

interface iOrders
{
    /**
     * @param Array $attributes
     * @return void
     */
    public function setAttributes(Array $attributes) : void;


    /**
     * @return object
     */
    public function retrieveOne() : Array;

    /**
     * @param Array $filters
     * @return Array
     */
    public static function retrieveMany(Array $filters = NULL) : Array;

    /**
     * @param Array $data
     * @return object
     */
    public function update(Array $data) : object;

    /**
     * @return void
     */
    public function delete() : void;
}