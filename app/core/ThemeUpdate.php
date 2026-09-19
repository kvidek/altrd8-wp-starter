<?php


namespace App\core;


class ThemeUpdate {

    public $version;      //Version number.
    public $details_url;  //The URL where the user can learn more about this version.
    public $download_url; //The download URL for this version of the theme. Optional.
    public $identifier;   //Theme identifier
    public $theme_folder; //Theme folder

	/**
	 * Create a new instance of ThemeUpdate from its JSON-encoded representation.
	 *
	 * @param string $json Valid JSON string representing a theme information object.
	 * @return \rickyRichardsWeb2021\core\ThemeUpdate New instance of ThemeUpdate, or NULL on error.
	 */
	public function from_json( $json ) {
		$api_response = json_decode( $json );
		if ( empty( $api_response ) || ! is_object( $api_response ) ) {
			return null;
		}

		//Very, very basic validation.
		$valid = isset( $api_response->version ) && ! empty( $api_response->version ) && isset( $api_response->details_url ) && ! empty( $api_response->details_url );
		if ( ! $valid ) {
			return null;
		}

		$update = new self();
		foreach ( get_object_vars( $api_response ) as $key => $value ) {
			$update->$key = $value;
		}

		return $update;
	}

	/**
	 * Transform the update into the format expected by the WordPress core.
	 *
	 * @return array
	 */
	public function to_wp_format() {
		$update = array(
			'new_version' => $this->version,
			'url'         => $this->details_url,
		);

		if ( ! empty( $this->download_url ) ) {
			$update['package'] = $this->download_url;
		}

		return $update;
	}
}
