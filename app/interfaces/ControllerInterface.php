<?php

namespace App\interfaces;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

interface ControllerInterface {
	public function get_view(): string;
}