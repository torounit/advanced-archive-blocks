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
			'default' => 'monthly'
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

	/**
	 * Get html class names.
	 *
	 * @param array $attributes block attributes.
	 *
	 * @return array
	 */
	public function get_class_names( $attributes ): array {
		$class_names = [];
		if ( ! empty( $attributes['className'] ) ) {
			$class_names = explode( ' ', $attributes['className'] );
		}
		if ( ! empty( $attributes['align'] ) ) {
			$class_names[] = 'align' . $attributes['align'];
		}

		return $class_names;
	}

	/**
	 * Get template part directory.
	 *
	 * @return string
	 */
	public function get_template_part_dir() {
		$template_part_dir = apply_filters( 'Advanced_Archive_Blocks_template_part_directory', 'template-parts/blocks', $this->name );

		return trim( $template_part_dir, '/\\' );
	}

	/**
	 * Loads a template part into a template.
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 *
	 * @return string
	 */
	public function get_template_part( $slug, $name = null ) {
		ob_start();
		get_template_part( $slug, $name );
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Get content from template.
	 *
	 * Examples:
	 *
	 *   1. template-parts/blocks/advanced-archive-blocks/archives/post-{style}.php
	 *   2. template-parts/blocks/advanced-archive-blocks/archives/post.php
	 *   3. template-parts/blocks/advanced-archive-blocks/archives-{style}.php
	 *   4. template-parts/blocks/advanced-archive-blocks/archives.php
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return false|string
	 */
	protected function get_content_from_template( $attributes ) {
		$class_name = join( ' ', $this->get_class_names( $attributes ) );
		set_query_var( 'class_name', $class_name );
		$path = [
			$this->get_template_part_dir(),
			$this->name,
			$attributes['postType'],
		];

		$output = $this->get_template_part( join( '/', $path ), $this->get_style_name( $class_name ) );

		if ( ! $output ) {
			$path   = [
				$this->get_template_part_dir(),
				$this->name,
			];
			$output = $this->get_template_part( join( '/', $path ), $this->get_style_name( $class_name ) );
		}

		return $output;
	}

	/**
	 * Get component style name.
	 *
	 * @param string $class_name class strings.
	 *
	 * @return string
	 */
	protected function get_style_name( $class_name ) {
		$classes = explode( ' ', $class_name );
		$styles  = array_filter(
			$classes,
			function ( $class ) {
				return strpos( $class, 'is-style-' ) !== false;
			}
		);

		if ( ! empty( $styles ) && is_array( $styles ) ) {
			$style = reset( $styles );

			return str_replace( 'is-style-', '', $style );
		}

		return '';
	}
}
