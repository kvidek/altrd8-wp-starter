<?php


namespace App\core;


use stdClass;

class ThemeUpdateChecker {

	protected static $filter_prefix   = 'tuc_request_update_';
	public $theme                     = '';
	public $metadata_url              = '';
	public $enable_automatic_checking = true;
	protected $option_name            = '';
	protected $automatic_check_done   = false;

	/**
	 * Class constructor.
	 *
	 * @param string $theme Theme slug, e.g. "twentyten".
	 * @param string $metadata_url The URL of the theme metadata file.
	 * @param boolean $enable_automatic_checking Enable/disable automatic update checking. If set to FALSE, you'll need to explicitly call checkForUpdates() to, err, check for updates.
	 */
	public function __construct( $theme, $metadata_url ) {
		$this->metadata_url = $metadata_url;

		// Is this staging site. URL contanis word staging
		if ( FALSE !== strpos( get_site_url(), 'staging' ) ) {
			$this->metadata_url .= '&staging=1';
		}

		$this->enable_automatic_checking = true;
		$this->theme                     = $theme;
		$this->option_name               = 'external_theme_updates-' . $this->theme;

		$this->install_hooks();
	}

	/**
	 * Install the hooks required to run periodic update checks and inject update info
	 * into WP data structures.
	 *
	 * @return void
	 */
	public function install_hooks() {
		//Check for updates when WordPress does. We can detect when that happens by tracking
		//updates to the "update_themes" transient, which only happen in wp_update_themes().
		if ( $this->enable_automatic_checking ) {
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'on_transient_update' ) );
		}

		//Insert our update info into the update list maintained by WP.
		add_filter( 'site_transient_update_themes', array( $this, 'inject_update' ) );

		//Delete our update info when WP deletes its own.
		//This usually happens when a theme is installed, removed or upgraded.
		add_action( 'delete_site_transient_update_themes', array( $this, 'delete_stored_data' ) );
	}

	/**
	 * Run the automatic update check, but no more than once per page load.
	 * This is a callback for WP hooks. Do not call it directly.
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function on_transient_update( $value ) {
		if ( ! $this->automatic_check_done ) {
			$this->check_for_updates();
			$this->automatic_check_done = true;
		}

		return $value;
	}

	/**
	 * Check for theme updates.
	 *
	 * @return void
	 */
	public function check_for_updates() {
		$state = get_option( $this->option_name );
		if ( empty( $state ) ) {
			$state                  = new StdClass();
			$state->last_check      = 0;
			$state->checked_version = '';
			$state->update          = null;
		}

		$state->last_check      = time();
		$state->checked_version = $this->get_installed_version();
		update_option( $this->option_name, $state ); //Save before checking in case something goes wrong

		$state->update = $this->request_update();
		update_option( $this->option_name, $state );
	}

	/**
	 * Get the currently installed version of our theme.
	 *
	 * @return string Version number.
	 */
	public function get_installed_version() {
		if ( function_exists( 'wp_get_theme' ) ) {
			$theme = wp_get_theme( $this->theme );

			return $theme->get( 'Version' );
		}

		return '';
	}

	/**
	 * Retrieve update info from the configured metadata URL.
	 *
	 * Returns either an instance of ThemeUpdate, or NULL if there is
	 * no newer version available or if there's an error.
	 *
	 * @param array $query_args Additional query arguments to append to the request. Optional.
	 *
	 * @return ThemeUpdate
	 * @uses wp_remote_get()
	 *
	 */
	public function request_update( $query_args = array() ) {
		//Query args to append to the URL. Themes can add their own by using a filter callback (see addQueryArgFilter()).
		$query_args['installed_version'] = $this->get_installed_version();
		$query_args                      = apply_filters( self::$filter_prefix . 'query_args_' . $this->theme, $query_args );

		//Various options for the wp_remote_get() call. Themes can filter these, too.
		$options = array(
			'timeout' => 10, //seconds
		);
		$options = apply_filters( self::$filter_prefix . 'options_' . $this->theme, $options );

		$url = $this->metadata_url;
		if ( ! empty( $query_args ) ) {
			$url = add_query_arg( $query_args, $url );
		}

		//Send the request.
		$result = wp_remote_get( $url, $options );

		//Try to parse the response
		$theme_update = null;
		$code         = wp_remote_retrieve_response_code( $result );
		$body         = wp_remote_retrieve_body( $result );
		if ( ( 200 === (int) $code ) && ! empty( $body ) ) {
			$theme_update = new ThemeUpdate();
			$theme_update = $theme_update->from_json( $body );
			//The update should be newer than the currently installed version.
			if ( ( null !== $theme_update ) && version_compare( $theme_update->version, $this->get_installed_version(), '<=' ) ) {
				$theme_update = null;
			}
		}

		return apply_filters( self::$filter_prefix . 'result_' . $this->theme, $theme_update, $result );
	}

	/**
	 * Insert the latest update (if any) into the update list maintained by WP.
	 *
	 * @param StdClass $updates Update list.
	 *
	 * @return StdClass Modified update list.
	 */
	public function inject_update( $updates ) {
		// Ako je $updates boolean (false), pretvori ga u stdClass
		if ( ! is_object( $updates ) ) {
			$updates = new \stdClass();
		}
		// Ako ->response ne postoji, inicijaliziraj ga kao array
		if ( ! isset( $updates->response ) || ! is_array( $updates->response ) ) {
			$updates->response = [];
		}
		$state = get_option( $this->option_name );
		if ( ! empty( $state ) ) {
			if ( isset( $state->update ) && is_object( $state->update ) ) {
				$updates->response[ $this->theme ] = $state->update->to_wp_format();
			}
		}
		return $updates;
	}

	/**
	 * Delete any stored book-keeping data.
	 *
	 * @return void
	 */
	public function delete_stored_data() {
		delete_option( $this->option_name );
	}

	/**
	 * Register a callback for filtering query arguments.
	 *
	 * The callback function should take one argument - an associative array of query arguments.
	 * It should return a modified array of query arguments.
	 *
	 * @param callable $callback
	 *
	 * @return void
	 */
	public function add_query_arg_filter( $callback ) {
		add_filter( self::$filter_prefix . 'query_args_' . $this->theme, $callback );
	}

	/**
	 * Register a callback for filtering arguments passed to wp_remote_get().
	 *
	 * The callback function should take one argument - an associative array of arguments -
	 * and return a modified array or arguments. See the WP documentation on wp_remote_get()
	 * for details on what arguments are available and how they work.
	 *
	 * @param callable $callback
	 *
	 * @return void
	 */
	public function add_http_request_arg_filter( $callback ) {
		add_filter( self::$filter_prefix . 'options_' . $this->theme, $callback );
	}

	/**
	 * Register a callback for filtering the theme info retrieved from the external API.
	 *
	 * The callback function should take two arguments. If a theme update was retrieved
	 * successfully, the first argument passed will be an instance of  ThemeUpdate. Otherwise,
	 * it will be NULL. The second argument will be the corresponding return value of
	 * wp_remote_get (see WP docs for details).
	 *
	 * The callback function should return a new or modified instance of ThemeUpdate or NULL.
	 *
	 * @param callable $callback
	 *
	 * @return void
	 */
	public function add_result_filter( $callback ) {
		add_filter( self::$filter_prefix . 'result_' . $this->theme, $callback, 10, 2 );
	}
}
