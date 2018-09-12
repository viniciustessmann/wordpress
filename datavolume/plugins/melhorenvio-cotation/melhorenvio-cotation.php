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

use MelhorEnvio\OrdersController;

class woocommercemelhorenviointegration 
{

    public function __construct() {
        add_action('plugins_loaded',array($this,'init'));
    }

    public function init() {

        //Create side menu
        add_action("admin_menu", function() {
            add_menu_page("Melhor Envios",  "Melhor Envio", "administrator", "wpmelhorenvio-melhor-envio", null, null, null);
            add_submenu_page("wpmelhorenvio-melhor-envio", "Pedidos", "Pedidos", "administrator", "wpmelhorenvio-melhor-envio-pedidos", function() {
                
                echo '<pre>';
                $order = new ordersController();
                $orders = $order->getAll();

                foreach ($orders as $item) {
                    var_dump($item->get_id());
                }
                die;
            });
            add_submenu_page("wpmelhorenvio-melhor-envio", "Meus dados", "Meus dados", "administrator", "wpmelhorenvio-melhor-envio-dados", function() {
                echo 'Meus dados';
                die;
            });
            add_submenu_page("wpmelhorenvio-melhor-envio", "Configurações", "Configurações", "administrator", "wpmelhorenvio-melhor-envio-configuracoes", function() {
                echo 'configuraçoes';
                die;
            });
        });
    }

}

$WPMelhorEnvioIntegration = new woocommercemelhorenviointegration(__FILE__);


