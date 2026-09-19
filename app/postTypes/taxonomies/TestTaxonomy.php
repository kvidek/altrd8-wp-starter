<?php
namespace App\postTypes\taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bornfight\wpHelpers\postTypes\BaseTaxonomy;
use bornfight\wpHelpers\postTypes\CustomTaxonomiesInterface;

class TestTaxonomy extends BaseTaxonomy implements CustomTaxonomiesInterface {
	public const LABEL = 'Test';
	public const SLUG = 'test';

	public function get_post_types(): array {
		return $this->get_default_post_types();
	}

	// DELETE to register this taxonomy
	public function register(): void {

	}

	public function get_taxonomy_slug(): string {
		return self::SLUG;
	}

	/**
	 * Change default labels here before return
	 *
	 * @return array
	 */
	public function get_labels(): array {
		return $this->get_default_labels( self::LABEL );
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
