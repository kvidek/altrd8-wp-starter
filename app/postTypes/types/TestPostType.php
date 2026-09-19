<?php

namespace App\postTypes\types;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bornfight\wpHelpers\postTypes\BasePostType;
use bornfight\wpHelpers\postTypes\CustomPostTypeInterface;

class TestPostType extends BasePostType implements CustomPostTypeInterface {
	public const LABEL = 'Test';
	public const LABEL_PLURAL = 'Tests';
	public const SLUG = 'test';

	public function get_post_type_slug(): string {
		return self::SLUG;
	}

	// DELETE to register post type
	public function register(): void {

	}

	/**
	 * Change default labels here before return
	 *
	 * @return array
	 */
	public function get_labels(): array {
		return $this->get_default_labels( self::LABEL, self::LABEL_PLURAL );
	}

	/**
	 * Change default args here before return
	 *
	 * @return array
	 */
	public function get_args(): array {
		return $this->get_default_args( $this->get_labels() );
	}
}
