<?php

namespace App\acf;

class ACFWoocommerce
{
    static $wc_loop_variation_id = null;

    public function __construct() {
    }

    public function init(): void {
        $this->enable_acf_on_variations();
        $this->enable_acf_on_shop_page();
	}

    /**
     * Add Location Rule to support ACF for Shop page
     *
     * @return void
     */
    public function enable_acf_on_shop_page() {
        add_filter( 'acf/location/rule_values/page_type', function ( $choices ) {
            $choices['woo_shop_page'] = 'WooCommerce Shop Page';
            return $choices;
        });

        add_filter( 'acf/location/rule_match/page_type', function ( $match, $rule, $options ) {
            if ( $rule['value'] == 'woo_shop_page' )
            {
                if ( $rule['operator'] == '==' )
                    $match = ( ! empty( $options['post_id'] ) && $options['post_id'] == wc_get_page_id( 'shop' ) );
                if ( $rule['operator'] == '!=' )
                    $match = ( ! empty( $options['post_id'] ) && $options['post_id'] != wc_get_page_id( 'shop' ) );
            }
            return $match;
        }, 10, 3 );
    }

    public function enable_acf_on_variations() {
        add_filter( 'acf/location/rule_values/post_type', function ( $choices ) {
            $choices['product_variation'] = 'Product Variation';
            return $choices;
        } );
        
        add_action( 'woocommerce_product_after_variable_attributes', function ( $loop, $variation_data, $variation ) {
            ACFWoocommerce::$wc_loop_variation_id = $variation->ID;
        
            add_filter( 'acf/prepare_field', array( $this, 'acf_prepare_field_update_field_name' ) );
        
            $acf_field_groups = acf_get_field_groups();
        
            foreach( $acf_field_groups as $acf_field_group ) {
                foreach( $acf_field_group['location'] as $group_locations ) {
                    foreach( $group_locations as $rule ) {
                        if( $rule['param'] == 'post_type' && $rule['operator'] == '==' && $rule['value'] == 'product_variation' ) {
                            acf_render_fields( $variation->ID, acf_get_fields( $acf_field_group ) );
                            break 2;
                        }
                    }
                }
            }
        
            ACFWoocommerce::$wc_loop_variation_id = null;
        
            remove_filter( 'acf/prepare_field', array( $this, 'acf_prepare_field_update_field_name' ) );
        }, 10, 3 );
        
        add_action( 'woocommerce_save_product_variation', function ( $variation_id, $i = -1 ) {
            $fields = array();
            if( ! empty( $_POST['acf']['variation_id_' . $variation_id] ) ) {
                $fields = $_POST['acf']['variation_id_' . $variation_id];
            }
        
            if ( ! empty( $fields ) ) {
              foreach ( $fields as $key => $val ) {
                foreach ( $fields as $key => $val ) {
                    update_field( $key, $val, $variation_id );
                }
              }
            }
          }, 10, 2 );

        
        /* actions fired when adding/editing posts or pages */
        /* admin_head-(hookname) */
        add_action( 'admin_head-post.php', array( $this, 'admin_head_post' ) );
        add_action( 'admin_head-post-new.php', array( $this, 'admin_head_post' ) );
    }



    public function acf_prepare_field_update_field_name( $field ) {
        
        // field['name] = acf[field_662f98155181d][acfcloneindex][field_662f99eb869ba]
        if( ! str_starts_with( $field['name'], 'acf[variation_id_' ) ) {
            $field['name'] = str_replace( 'acf[', 'acf[variation_id_' . ACFWoocommerce::$wc_loop_variation_id . '][', $field['name'] );
        }
    
        return $field;
    }

    public function admin_head_post() {
        global $post_type;
        if ( $post_type === 'product' ) {
            wp_register_script( 'xxx-acf-variation', get_template_directory_uri() . '/static/js/admin/acf-variation.js', array(
                'jquery-core',
                'jquery-ui-core',
            ), '1.1.0', true ); // Custom scripts
    
            wp_enqueue_script( 'xxx-acf-variation' ); // Enqueue it!
        }
    }
}
