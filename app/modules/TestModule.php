<?php

namespace App\modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use App\interfaces\ControllerInterface;

// PLEASE DELETE ME
// Here goes module logic
class TestModule implements ControllerInterface{
	private bool $show;
	private string $title;

	public function __construct( array $module ) {
		$this->show     = ! empty( $module['show'] );
		$this->title     = $module['title'];
	}

	public function get_view(): string {
		if ( empty( $this->show ) ) {
			return '';
		}

		return get_partial('modules/test', array(
			'title' => $this->title,
		), true);
	}
}