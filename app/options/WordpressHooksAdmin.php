<?php

namespace App\options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WordpressHooksAdmin {
	public function init(): void {
		if ( !defined( 'WP_ENVIRONMENT_TYPE' ) || 'local' === wp_get_environment_type() ) {
			add_filter( 'theme_page_templates', array( $this, 'filter_page_templates' ) );
		}
		// Add the page template column to the pages list in admin
        $this->show_page_template_column();
	}

	// Show slice templates if not production environment
	public function filter_page_templates( array $page_templates ): array {
		$pattern   = trailingslashit( get_stylesheet_directory() ) . 'slice/page-templates';
		$templates = glob( $pattern . '/*.php' );

		if ( ! empty( $templates ) ) {
			foreach ( $templates as $template ) {
				$class_name = explode( '/', $template );
				$last_item  = end( $class_name );

				if ( is_file( $template ) ) {
					$template_data = implode( '', file( $template ) );

					if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) ) {
						/* translators: %s: Template name. */
						$template_name = sprintf( __( '%s' ), _cleanup_header_comment( $name[1] ) );
					}
				}

				$page_templates[ 'slice/page-templates/' . $last_item ] = ! empty( $template_name ) ? $template_name : str_replace( '.php', '', $last_item );
			}
		}

		return $page_templates;
	}

	/**
	 * Show the page template column in the admin pages list.
	 * @return void 
	 */
    public function show_page_template_column(): void {

        add_filter( 'manage_pages_columns', function ( $columns ) {
            // column position from left
            $position = 2;
            $before   = array_slice( $columns, 0, $position, true );
            $after    = array_slice( $columns, $position, null, true );
            $custom   = array( 'page_template' => __( 'Page Template', 'altrd8-wp-starter' ) );

            return $before + $custom + $after;
        } );

        add_action( 'manage_pages_custom_column', function ( $column, $post_id ) {
            if ( $column === 'page_template' ) {
                $template = get_page_template_slug( $post_id );
                if ( ! $template ) {
                    echo esc_html__( 'Default', 'altrd8-wp-starter' );
                } else {
                    // Directly get the template name from registered templates
                    $templates     = wp_get_theme()->get_page_templates();
                    $template_name = isset( $templates[ $template ] ) ? $templates[ $template ] : null;

                    // If not found, try to parse the file in slice/page-templates
                    if ( ! $template_name && strpos( $template, 'slice/page-templates/' ) === 0 ) {
                        $template_path = get_stylesheet_directory() . '/' . $template;
                        if ( file_exists( $template_path ) ) {
                            $template_data = implode( '', file( $template_path ) );
                            if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) ) {
                                $template_name = _cleanup_header_comment( $name[1] );
                            }
                        }
                    }

                    echo esc_html( $template_name ? $template_name : $template );
                }
            }
        }, 10, 2 );
    }
}
