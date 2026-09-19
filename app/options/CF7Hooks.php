<?php

namespace App\options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Class CF7Hooks
 *
 * This class is used to modify the behavior of Contact Form 7 (CF7) forms. This class adds support for custom data attributes on select elements used to configure SlimSelect.js (custom select boxes).
 * This class best works in combination with BFS b-creative boilerplate — SelectInput.js. and _vendors.slim-select.scss
 *
 * Mandatory attribute to enable SlimSelect configuration due to backend optimisation (so the loop does not go through all select elements in FC7):
 * - ss-config-enable
 *
 * Currently supported attributes:
 * - SlimSelect specific:
 * 
 *      - ss-placeholder - Adds a empty placeholder option to the select element first option.
 *      - ss-placeholder:Foobar - Adds a placeholder option with Foobar to the select element first option.
 * 
 *      - ss-data-placeholder - Adds a empty placeholder option to the select element first option. Adds data-placeholder attribute.
 *      - ss-data-placeholder:Foobar - Adds a empty placeholder option to the select element first option. Adds data-placeholder="Foobar" attribute.
 * 
 *      - ss-data-search - Adds a search field to the select element.
 *      - ss-data-content-location - Adds custom data attributes to the select element.
 * 
 *      - TBD and more... (see SlimSelect documentation for more ideas and options for further development)
 *
 * - Any custom data attribute can be added to the select element by prefixing the attribute with 'ss-data-'.:
 *      - ss-data-xyz - Adds empty custom data attributes to the select element.
 *      - ss-data-xyz:Foobar - Adds custom data attributes with value Foobar to the select element.
 */


/**
 * Custom CF7 Select Input Tag
 * ---------------------------
 * usage example:
 * 
 * [select country id:country
 * class:js-select-input
 * ss-config-enable <----- THIS IS MANDATORY
 * ss-placeholder
 * ss-placeholder:Country
 * ss-data-search
 * ss-data-allow-deselect
 * ss-data-hide-selected
 * ss-multiple
 * ss-data-xxx
 * ss-data-xxx:yyy
 * ss-xxx
 * ss-xxx:yyy
 * "Austria" "Belgium" "Bulgaria" "Croatia" "Cyprus" "Czech Republic" "Denmark" "Estonia" "Finland" "France" "Germany" "Greece" "Hungary" "Ireland, Republic of" "Italy" "Latvia" "Lithuania" "Luxembourg" "Malta" "Netherlands" "Poland" "Portugal" "Romania" "Slovakia" "Slovenia" "Spain" "Sweden" "Other"]
 *
 * [select country id:country
 * class:js-select-input
 * ss-config-enable <----- THIS IS MANDATORY
 * ss-data-placeholder
 * ss-data-placeholder:Country
 * ss-data-search
 * ss-data-allow-deselect
 * ss-data-hide-selected
 * ss-multiple
 * ss-data-xxx
 * ss-data-xxx:yyy
 * ss-xxx
 * ss-xxx:yyy
 * "Austria" "Belgium" "Bulgaria" "Croatia" "Cyprus" "Czech Republic" "Denmark" "Estonia" "Finland" "France" "Germany" "Greece" "Hungary" "Ireland, Republic of" "Italy" "Latvia" "Lithuania" "Luxembourg" "Malta" "Netherlands" "Poland" "Portugal" "Romania" "Slovakia" "Slovenia" "Spain" "Sweden" "Other"]
 * 
 * Credits: @luzel [https://github.com/luzel]
 *
 *
 * Privacy Policy Link Tag:
 * -----------------------
 *
 * Custom CF7 tag [privacy_page_link] outputs a link to the WordPress privacy policy page. You can optionally provide a custom link title.
 *
 * Usage examples:
 *   [privacy_page_link]
 *   [privacy_page_link "Custom Title"]
 *
 * This will render a link to the privacy policy page, using the page title or your custom title.
 */
class CF7Hooks
{
	/**
	 * Initializes the hooks for the CF7 form.
	 *
	 * This method is called to set up the hooks that modify the CF7 form.
	 * It adds a filter to the 'wpcf7_form_tag' hook, which allows us to modify the form tags.
	 */
	public function init(): void
	{
		if( ! is_admin() ) {
			add_action(
				'wpcf7_init',
				function () {
					// CF7 select
					add_filter( 'wpcf7_form_tag', array( $this, 'slim_select_attributes' ), 10, 2 );

					// Register custom tag for privacy page link [privacy_page_link] or [privacy_page_link title "Custom Title"]
					if ( function_exists( 'wpcf7_add_form_tag' ) ) {
						wpcf7_add_form_tag( 'privacy_page_link', array( $this, 'privacy_page_link_tag_handler' ), array( 'html' => true ) );
					}
				}
			);
		}
	}

	/**
	 * Adds custom attributes to select form elements for usage with SlimSelect.js.
	 *
	 * This method is called for each form tag in the CF7 form.
	 * If the tag is a select tag, it adds custom attributes to it.
	 *
	 * @param array $tag The form tag.
	 * @param bool $replace Whether to replace the existing attributes.
	 * @return array The modified form tag.
	 */
	function slim_select_attributes( $tag, $replace ) {


		// skip if not select or select*
		if( ! ( 'select' === $tag['type'] || 'select*' === $tag['type'] ) ) {
			return $tag;
		}

		// skip if not enabled by attribute ss-config-enable
		if( ! in_array('ss-config-enable', (array) $tag['options'] ) ) {
			return $tag;
		}

		//run once
		if( ! empty( $replace ) ) {
			return $tag;
		}

		$cf7_options = array();
		$ss_options  = array();

		foreach ( (array) $tag['options'] as $option ) {

			// skip ss-config-enable attribute
			if( 'ss-config-enable' === $option ) {
				continue;
			}

			if( 0 === strpos( $option, 'ss-data' ) ) {

				$new_option = str_replace( 'ss-data', 'data', $option );

				if ( false !== strpos( $option, ':' ) ) {
					$ex                   = explode( ':', $new_option );
					$ss_options[ $ex[0] ] = apply_filters( 'wpcf7_option_value', $ex[1], $ex[0] );
				} else {
					$ss_options[ $new_option ] = '';
				}
			} elseif ( 0 === strpos( $option, 'ss-' ) ) {

				$new_option = str_replace( 'ss-', '', $option );

				if ( false !== strpos( $option, ':' ) ) {
					$ex                   = explode( ':', $new_option );
					$ss_options[ $ex[0] ] = apply_filters( 'wpcf7_option_value', $ex[1], $ex[0] );
				}

			} else {
				$cf7_options[] = $option;
			}
		}

		$tag['options'] = $cf7_options;

		$name = $tag['name'];
		if( in_array( 'multiple', (array) $tag['options'] ) ) {
			$name .= '[]';
		}

		add_filter( 'wpcf7_form_elements', function ( $content ) use ( $name, $ss_options ) {
			static $register = array();

			if( isset ( $register[ $name ] ) ) {
				return $content;
			}


			$register[ $name ] = true;

			$attributesHtml = '';

			foreach ( $ss_options as $key => $value ) {
				$attributesHtml .= ' ' . $key;

				if ( is_string( $value ) && strlen( $value ) > 0 ) {
					$attributesHtml .= '="' . $value . '"';
				}
			}

			$content = str_replace( ' name="' . $name . '">', $attributesHtml . ' name="' . $name . '">', $content );

			if( isset( $ss_options['data-placeholder'] ) ) {
				$content = str_replace( ' name="' . $name . '">', ' name="' . $name . '"><option data-placeholder="true"></option>', $content );
			} else if( isset( $ss_options['placeholder'] ) ) {
				$search =  ' name="' . $name . '">';
				$replace = ' name="' . $name . '"><option data-placeholder="true">' . $ss_options['placeholder'] . '</option>';
				$content = str_replace($search , $replace, $content );
			}

			return $content;
		} );

		return $tag;
	}

	/**
	 * Handler for [privacy_page_link] CF7 tag.
	 * Outputs a link to the privacy policy page with its title or custom title if provided.
     *
     * example usage:
     * [privacy_page_link class:c-link "Privacy Policy"]
     * or [privacy_page_link class:c-link] then it will use the page title as link text.
	 *
	 * @param $tag
	 * @return string
	 */
    public function privacy_page_link_tag_handler( $tag ): string {
        // Get privacy page ID from WP settings
        $privacy_page_id = get_option( 'wp_page_for_privacy_policy' );
        
        if ( empty($privacy_page_id) ) {
            return '';
        }

        $url = get_permalink( $privacy_page_id );
        $page_title = get_the_title( $privacy_page_id );
        
        if ( empty($url) || empty($page_title) ) {
            return '';
        }

        // Use custom title if provided in tag values, otherwise use page title
        $link_title = isset($tag->values[0]) && !empty($tag->values[0]) 
            ? $tag->values[0] 
            : $page_title;

        $attributes = [
            'class' => $tag->get_class_option( wpcf7_form_controls_class( $tag->type ) ),
            'href' => esc_url( $url ),
            'target' => '_blank',
            'rel' => 'noopener noreferrer'
        ];

        return sprintf(
            '<a %s>%s</a>',
            wpcf7_format_atts( $attributes ),
            esc_html( $link_title )
        );
    }
}
