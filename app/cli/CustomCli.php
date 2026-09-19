<?php

namespace App\cli;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use WP_CLI;
use bornfight\wpHelpers\services\Service;
use bornfight\wpHelpers\cli\attributes\Attr;

class CustomCli extends Service {

	public function get_namespace(): string {
		return __NAMESPACE__ . '\\commands\\';
	}

	public function get_pattern(): string {
		return trailingslashit( get_stylesheet_directory() ) . 'app/cli/commands';
	}

	public function register(): void {
		$items = $this->get_classes_by_namespace( $this->get_namespace(), $this->get_pattern() );

		foreach ( $items as $item ) {
			if ( ! class_exists( $item ) ) {
				continue;
			}

			$reflection = new \ReflectionClass( $item );
			$attrs      = $reflection->getAttributes( Attr::class );

			if ( empty( $attrs ) ) {
				continue;
			}

			$attr_instance = $attrs[0]->newInstance();
			$instance      = new $item();

			WP_CLI::add_command(
				$attr_instance->command,
				$instance,
				array(
					'shortdesc' => $attr_instance->shortdesc,
				)
			);
		}
	}
}
