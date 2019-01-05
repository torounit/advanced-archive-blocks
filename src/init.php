<?php
/**
 * Initialize.
 *
 * @package Advanced_Archive_Blocks
 */

namespace Advanced_Archive_Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/blocks/archives/class-renderer.php';

add_action(
	'init',
	function () {
		new Renderer( 'advanced-archive-blocks/archives' );
	},
	9999
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		$deps = [
			'wp-blocks',
			'wp-components',
			'wp-data',
			'wp-element',
			'wp-editor',
			'wp-i18n',
		];
		wp_enqueue_script( 'advanced-archive-blocks', plugins_url( 'dist/main.js', PLUGIN_FILE ), $deps, 1, true );
	}
);

