<?php

/**
 * Plugin Name: RajaOngkir Shipping
 * Plugin URI: http://code.tutsplus.com/tutorials/create-a-custom-shipping-method-for-woocommerce--cms-26098
 * Description: Calculate shipping fee with rajaongkir API
 * Version: 1.0.0
 * Author: Pandhu Hutomo Aditya
 * Author URI: http://pandhuha.wordpress.com
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 * Text Domain: tutsplus
 */

if ( ! defined( 'WPINC' ) ) {

    die;

}

/*
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    function rajaongkir_shipping_method() {
        if ( ! class_exists( 'RajaOngkir_Shipping_Method' ) ) {
            class RajaOngkir_Shipping_Method extends WC_Shipping_Method {
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct() {
                    $this->id                 = 'rajaongkir';
                    $this->method_title       = __( 'RajaOngkir Shipping', 'rajaongkir' );
                    $this->method_description = __( 'Custom Shipping Method for RajaOngkir', 'rajaongkir' );
                    // Availability & Countries
                    $this->availability = 'including';
                    $this->countries = array(
                        'ID', // Indonesia
                        );
                    $this->init();

                    $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'RajaOngkir Shipping', 'rajaongkir' );
                }

                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init() {
                    // Load the settings API
                    $this->init_form_fields();
                    $this->init_settings();

                    // Save settings in admin if you have any defined
                    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                }

                /**
                 * Define settings field for this shipping
                 * @return void
                 */
                function init_form_fields() {

                    // We will add our settings here
                    $this->form_fields = array(
                        'enabled' => array(
                            'title' => __( 'Enable', 'rajaongkir' ),
                            'type' => 'checkbox',
                            'description' => __( 'Enable this shipping.', 'rajaongkir' ),
                            'default' => 'yes'
                        ),

                        'title' => array(
                            'title' => __( 'Title', 'rajaongkir' ),
                            'type' => 'text',
                            'description' => __( 'Title to be display on site', 'rajaongkir' ),
                            'default' => __( 'RajaOngkir Shipping', 'rajaongkir' )
                        ),
                        'weight' => array(
                           'title' => __( 'Weight (kg)', 'rajaongkir' ),
                             'type' => 'number',
                             'description' => __( 'Maximum allowed weight', 'rajaongkir' ),
                             'default' => 100
                             ),
                    );
                }

                /**
                 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
                 *
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                 public function calculate_shipping( $package ) {

                     // We will add the cost, rate and logics in here
                     $weight = 0;
                     $cost = 0;
                     $country = $package["destination"]["country"];

                     foreach ( $package['contents'] as $item_id => $values )
                     {
                         $_product = $values['data'];
                         $weight = $weight + $_product->get_weight() * $values['quantity'];
                     }
                     $weight = wc_get_weight( $weight, 'kg' );
                     if( $weight <= 10 ) {
                         $cost = 0;
                     } elseif( $weight <= 30 ) {
                         $cost = 5;
                     } elseif( $weight <= 50 ) {
                         $cost = 10;
                     } else {
                         $cost = 20;
                     }
                     $countryZones = array(
                        'ID' => 0,
                        );

                    $zonePrices = array(
                        0 => 8000,
                        );

                    $zoneFromCountry = $countryZones[ $country ];
                    $priceFromZone = $zonePrices[ $zoneFromCountry ];

                    $cost += $priceFromZone;

                    $rate = array(
                        'id' => $this->id,
                        'label' => 'Penghitungan Ongkir di Form Checkout',
                        'cost' => '',
                    );

                    $this->add_rate( $rate );
                 }
             }
         }
     }

     add_action( 'woocommerce_shipping_init', 'rajaongkir_shipping_method' );

     function add_rajaongkir_shipping_method( $methods ) {
         $methods[] = 'RajaOngkir_Shipping_Method';
         return $methods;
     }
     add_filter( 'woocommerce_shipping_methods', 'add_rajaongkir_shipping_method' );

     // Hook in
     add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

     // Our hooked in function - $fields is passed via the filter!
     function custom_override_checkout_fields($fields) {

         unset($fields['billing']['billing_company']);
         unset($fields['billing']['billing_country']);
         $fields['billing']['billing_state']['label'] = 'Provinsi';
         $fields['billing']['billing_city'] = array(
             'type'=>'select',
             'label'=>'Kota/Kabupaten',
             'required'=>true,
             'options'=> array(
                 '' => 'Pilih Kota'
             ),
         );
         $fields['billing']['billing_district'] = array(
             'type'=>'select',
             'label'=>'Kecamatan',
             'required'=>true,
             'options'=> array(
                 '' => 'Pilih Kecamtan'
             ),
         );

         return $fields;
     }
     add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_true' );

     /**
      * Display field value on the order edit page
      */
     add_action( 'woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

     function my_custom_checkout_field_display_admin_order_meta($order){
         echo '<p><strong>'.__('Phone From Checkout Form').':</strong> ' . get_post_meta( $order->id, '_shipping_phone', true ) . '</p>';
     }

     add_filter("woocommerce_checkout_fields", "order_fields");

     function order_fields($fields) {

         $order = array(
             "billing_first_name",
             "billing_last_name",
             "billing_state",
             "billing_city",
             "billing_district",
             "billing_address_1",
             "billing_postcode",
             "billing_email",
             "billing_phone"
         );
         foreach($order as $field)
         {
             $ordered_fields[$field] = $fields["billing"][$field];
         }

         $fields["billing"] = $ordered_fields;
         return $fields;

     }

     //add script
     add_action( 'wp_enqueue_scripts', 'add_scripts', 999);
     add_action( 'wp_enqueue_scripts', 'add_styles', 999);
     function add_styles(){
         wp_enqueue_style( 'bootstrap',plugins_url( '/css/bootstrap.css', __FILE__ ),false,'1.1','all');
         wp_enqueue_style( 'select-bootstrap',plugins_url( '/css/select-bootstrap.css', __FILE__ ),false,'1.1','all');
     }
     function add_scripts(){
         wp_register_script( 'rajaongkir-jquery', plugins_url( '/js/jquery.min.js', __FILE__ ), '','20120208', true);
         wp_enqueue_script( 'rajaongkir-jquery' );
         wp_register_script( 'rajaongkir-bootstrap', plugins_url( '/js/bootstrap.min.js', __FILE__ ), '','20120208', true);
         wp_enqueue_script( 'rajaongkir-bootstrap' );
         wp_register_script( 'rajaongkir-bootstrap-select', plugins_url( '/js/bootstrap-select.js', __FILE__ ), '','20120208', true);
         wp_enqueue_script( 'rajaongkir-bootstrap-select' );
         wp_register_script( 'rajaongkir-checkout', plugins_url( '/js/checkout.js', __FILE__ ),array( 'jquery' ),'20120208', true);
         wp_enqueue_script( 'rajaongkir-checkout' );
     }

}
