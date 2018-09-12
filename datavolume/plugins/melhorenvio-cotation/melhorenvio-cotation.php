<?php
/*
Plugin Name:  Melhor Envio Cotation
Plugin URI:   https://www.melhorenvio.com.br
Description:  Plugin de cotações do Melhor Envio para WooCommerce
Version:      2.0.0
Author:       Melhor Envio
Author URI:   https://www.melhorenvio.com.br
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wporg
Domain Path:  /languages
*/

CONST VERSION_PLUGIN_MELHOR_ENVIO = '2.0.0';

require __DIR__ . '/vendor/autoload.php';
//include_once WC_ABSPATH.'/includes/wc-order-functions.php';

use Controllers\OrdersController;
use Controllers\ConfigurationController;

class woocommercemelhorenviointegration 
{
    public function __construct() {
        add_action('plugins_loaded',array($this,'init'));
    }

    public function init() {

        //Create side menu
        add_action("admin_menu", function() {

            $order = new OrdersController();

            add_menu_page("Melhor Envios",  "Melhor Envio", "administrator", "wpmelhorenvio-melhor-envio", null, null, null);
            add_submenu_page("wpmelhorenvio-melhor-envio", "Pedidos", "Pedidos", "administrator", "wpmelhorenvio-melhor-envio-pedidos", [$order, 'index']);

            
            add_submenu_page("wpmelhorenvio-melhor-envio", "Meus dados", "Meus dados", "administrator", "wpmelhorenvio-melhor-envio-dados", function() {
                echo 'Meus dados';
                die;
            });
            add_submenu_page("wpmelhorenvio-melhor-envio", "Configurações", "Configurações", "administrator", "wpmelhorenvio-melhor-envio-configuracoes", function() {
                echo 'configuraçoes';
                die;
            });
        });

        // Get orders
        add_action( 'wp_ajax_wpmelhorenvio_get_orders', function() {
            $order = new ordersController();
            echo wp_send_json([
                'success' => true,
                'data' => $order->getAll()
            ]); 
        });

        // Send to cart
        add_action( 'wp_ajax_wpmelhorenvio_send_order', function() {
            $order = new ordersController();
            echo $order->send();
        });

        //Save token
        add_action( 'wp_ajax_wpmelhorenvio_save_token', function() {
            if (!$_GET['token']) {
                echo wp_send_json([
                    'error' => true,
                    'message' => 'Campo "token" é obrigatorio'
                ]); 
            }

            $configuration = new ConfigurationController();
            $token = $configuration->saveToken($_GET['token']);

            echo wp_send_json([
                'success' => true,
                'token' => $token
            ]);
        });

        // Get info user
        add_action( 'wp_ajax_wpmelhorenvio_get_user', function() {
            $user = new UsersController();
            $data = $user->getInfo();

            if ($data['error']) {
                echo wp_send_json($data);
                die();
            }

            echo wp_send_json($data);
            die();
            
        });
    }
}
$WPMelhorEnvioIntegration = new woocommercemelhorenviointegration(__FILE__);
