<?php 


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    add_action( 'woocommerce_shipping_init', 'jadlog_package_v2_shipping_method_init' );
	function jadlog_package_v2_shipping_method_init() {

		// if ( ! class_exists( 'WC_Jadlog_package_V2_Shipping_Method' ) ) {
			class WC_Jadlog_Package_V2_Shipping_Method extends WC_Shipping_Method {

             

                protected $code = '38s';
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct($instance_id = 0) {

					$this->id                 = "jadlog_package_v2"; 
                    $this->instance_id 		  = absint( $instance_id );
                    $this->method_title       = "Jadlog Package (Melhor envio) V2"; 
					$this->method_description = '.Package, serviço de entregas econômicas da Jadlog';
					$this->enabled            = "yes"; 
					$this->title              = "Jadlog Package V2"; 
                    $this->supports = array(
                        'shipping-zones',
                        'instance-settings'
                    );
					$this->init_form_fields();
				}
				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					$this->init_form_fields(); 
					$this->init_settings(); 
				}

				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package = []) {

                    $rate = array(
                        'id'       => "wpmelhorenviov2_novo_package",
                        'label'    => 'valor frete',
                        'cost'     => 120.99,
                        'calc_tax' => 'per_item',
                        'meta_data' => [
                            'delivery_time' => 2,
                            'company' => 'jadlog'
                        ]
                    );
                    $this->add_rate( $rate );

                    // $result = wpmelhorenviopackage_getPackageInternal($package, $this->code, $ar, $mp);
                    // if ($result != null && $result->price > 0) {

                    //     $rate = array(
                    //         'id'       => "wpmelhorenvio_".$result->company->name."_".$result->name,
                    //         'label'    => $this->title . calculate_delivery_time_jadlog_package($result->delivery_range),
                    //         'cost'     => calcute_value_shipping_jadlog_package($result->price),
                    //         'calc_tax' => 'per_item',
					// 		'meta_data' => [
					// 			'delivery_time' => $result->delivery_time,
					// 			'company' => $result->company->name
					// 		]
                    //     );
                    //     $this->add_rate( $rate );
                    // }
                    
                }  
			}
		// }
	}
}
