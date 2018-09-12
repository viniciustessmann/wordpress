<?php 

    class Info 
    {

        public function getInfo() {

        }

        public function saveToken($token) {
            add_option('token_tessmann', $token, true);
        }
    }