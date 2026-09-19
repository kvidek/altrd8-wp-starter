<?php

namespace App\core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use App\cli\CustomCli;
use App\options\RoleManagement;
use App\options\WordpressHooks;
use App\options\WordpressHooksAdmin;
use App\options\CF7Hooks;
use App\adminMenus\CustomAdminMenus;
use App\rest\CustomRoutes;
use App\postTypes\CustomPostTypes;
use App\postTypes\CustomTaxonomies;
use App\blocks\Blocks;
use App\bundles\Altrd8WpStarterAssets;
use App\optimization\WPDefaults;
use App\optimization\WPPlugins;

final class Core {
	public function init(): void {
		if ( is_admin() ) {
			$this->init_theme_update_checker();
			$this->init_admin_classes();
		}
		$this->init_classes();
	}

	private function init_theme_update_checker(): void {
		if ( class_exists( 'App\core\ThemeUpdateChecker' ) ) {
			//Initialize the update checker.
			$example_update_checker = new ThemeUpdateChecker(
				'altrd8-wp-starter',                                            //Theme folder name, AKA "slug".
				'https://services.bfs.wtf/?identifier=676908c8116c91d384baf1d8d79fae10&type=manifest' //URL of the metadata file.
			);

			add_action(
				'load-themes.php',
				function () use ( $example_update_checker ) {
					$example_update_checker->check_for_updates();
				}
			);
		}
	}

	private function init_admin_classes(): void {
		$wordpress_hooks_admin = new WordpressHooksAdmin();
		$wordpress_hooks_admin->init();

		$custom_admin_menus = new CustomAdminMenus();
		$custom_admin_menus->register();

		// Inline (not wp_enqueue_style()'d) so it's scoped to the block editor's
		// canvas iframe only — see the doc comment on $editor_css for why.
		add_filter(
			'block_editor_settings_all',
			function ( $settings ) {
				$css = Altrd8WpStarterAssets::get_editor_css();

				if ( $css !== '' ) {
					$settings['styles'][] = array( 'css' => $css );
				}

				return $settings;
			}
		);
	}

	private function init_classes(): void {

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$cli_command = new CustomCli();
			$cli_command->register();
		}

		$custom_post_types = new CustomPostTypes();
		$custom_post_types->register();

		$custom_taxonomies = new CustomTaxonomies();
		$custom_taxonomies->register();

		$wordpress_hooks = new WordpressHooks();
		$wordpress_hooks->init();

		$cf7_hooks = new CF7Hooks();
		$cf7_hooks->init();

		$role_management = new RoleManagement();
		$role_management->init();

		$custom_routes = new CustomRoutes();
		$custom_routes->register();

		$blocks = new Blocks();
		$blocks->init();

		// Optimization
		$wp_defaults = new WPDefaults();
		$wp_defaults->init();

		$wp_plugins = new WPPlugins();
		$wp_plugins->init();
	}
}
