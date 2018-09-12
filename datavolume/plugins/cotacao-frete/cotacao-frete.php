<?php

/*
 *
Plugin Name: Tessamnn Cotação frete
Plugin URI:  
Description: Plugin para cotação de fretes utitilizando a api pública da Melhor Envio
Version:     1.0.0
Author:      Vinícius Schlee Tessmann
Author URI:  
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// create custom plugin settings menu
add_action('admin_menu', 'create_menu_settings');
function create_menu_settings() {
	//create menu settings
	add_menu_page('Tessmann Frete', 'Tessmann configurações', 'administrator', __FILE__, 'page_settings');
}

function page_settings() {
    $context = [
        'posturl' => esc_url( admin_url('admin-post.php'))
    ];
    Timber::render( 'token.twig', $context);
} 

// Function to get request POST
add_action( 'init', 'receive_post' );
function receive_post() {

    // Save token 
	if ( isset( $_POST['action'] ) && 'savetoken' == $_POST['action'] ) {
        $token = $_POST['token'];
        echo $token;
        die;
	}
} 
