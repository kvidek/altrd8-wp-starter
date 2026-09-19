<?php

namespace App\interfaces;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

interface BlockInterface {
	public function get_settings(): array;

	public function get_view( array $block ): void;
}