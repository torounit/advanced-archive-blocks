<?php
/**
 * Posts Renderer Class.
 *
 * @package Advanced_Archive_Blocks
 */

namespace Advanced_Archive_Blocks;

/**
 * Class Renderer
 *
 * Posts blocks.
 */
class Renderer {

	/**
	 * Name of Block.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Attributes schema for blocks.
	 *
	 * @var array
	 */
	protected $attributes = [
		'postType'          => [
			'type'    => 'string',
			'default' => 'post',
		],
		'className'         => [
			'type' => 'string',
		],
		'type'              => [
			'type'    => 'string',
			'default' => 'monthly',
		],
		'align'             => [
			'type' => 'string',
		],
		'displayAsDropdown' => [
			'type'    => 'boolean',
			'default' => false,
		],
		'showPostCounts'    => [
			'type'    => 'boolean',
			'default' => false,
		],
	];

	/**
	 * Constructor
	 *
	 * @param string $name block name.
	 */
	public function __construct( string $name ) {
		if ( $name ) {
			$this->name = $name;
		}

		$this->register();
	}

	/**
	 * Render callback
	 *
	 * @param array $attributes block attributes.
	 *
	 * @return false|string
	 */
	public function render( $attributes ) {
		$show_post_count = ! empty( $attributes['showPostCounts'] );
		$post_type       = $attributes['postType'];
		$type            = $attributes['type'];

		$class = 'wp-block-archives';

		if ( isset( $attributes['align'] ) ) {
			$class .= " align{$attributes['align']}";
		}

		if ( isset( $attributes['className'] ) ) {
			$class .= " {$attributes['className']}";
		}

		if ( ! empty( $attributes['displayAsDropdown'] ) ) {
			$class .= ' wp-block-archives-dropdown';

			$dropdown_id = esc_attr( uniqid( 'wp-block-archives-' ) );
			$title       = __( 'Archives' );

			$dropdown_args = array(
				'type'            => $type,
				'format'          => 'option',
				'post_type'       => $post_type,
				'show_post_count' => $show_post_count,
			);

			$dropdown_args['echo'] = 0;

			$archives = wp_get_archives( $dropdown_args );

			switch ( $dropdown_args['type'] ) {
				case 'yearly':
					$label = __( 'Select Year' );
					break;
				case 'monthly':
					$label = __( 'Select Month' );
					break;
				case 'daily':
					$label = __( 'Select Day' );
					break;
				case 'weekly':
					$label = __( 'Select Week' );
					break;
				default:
					$label = __( 'Select Post' );
					break;
			}

			$label = esc_attr( $label );

			$block_content = '<label class="screen-reader-text" for="' . $dropdown_id . '">' . $title . '</label>
	<select id="' . $dropdown_id . '" name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;">
	<option value="">' . $label . '</option>' . $archives . '</select>';

			$block_content = sprintf(
				'<div class="%1$s">%2$s</div>',
				esc_attr( $class ),
				$block_content
			);
		} else {
			$class .= ' wp-block-archives-list';

			$archives_args = array(
				'type'            => $type,
				'post_type'       => $post_type,
				'show_post_count' => $show_post_count,
			);

			$archives_args['echo'] = 0;

			$archives = wp_get_archives( $archives_args );

			$classnames = esc_attr( $class );

			if ( empty( $archives ) ) {
				$block_content = sprintf(
					'<div class="%1$s">%2$s</div>',
					$classnames,
					__( 'No archives to show.' )
				);
			} else {
				$block_content = sprintf(
					'<ul class="%1$s">%2$s</ul>',
					$classnames,
					$archives
				);
			}
		}

		return $block_content;
	}

	/**
	 * Regsiter Block Type.
	 */
	protected function register() {
		register_block_type(
			$this->name,
			[
				'attributes'      => $this->get_attributes(),
				'render_callback' => [ $this, 'render' ],
			]
		);
	}

	/**
	 * Getter for attirbutes.
	 *
	 * @return array
	 */
	public function get_attributes(): array {
		return $this->attributes;
	}
}
