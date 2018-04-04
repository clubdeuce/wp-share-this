<?php

namespace Clubdeuce\WPShareThis;

/**
 * Class WP_Share_This
 * @package Clubdeuce\WPShareThis
 *
 * @link http://www.sharethis.com/support/customization/how-to-set-custom-buttons/
 */
class WP_Share_This {

	/**
	 * @var bool
	 */
	private static $_facebook_og = true;

	/**
	 * @var string
	 */
	private static $_id = null;

	/**
	 * @var array
	 */
	private static $_services = array();

	/**
	 *
	 */
	public static function initialize() {

		add_action( 'wp_enqueue_scripts', array( __CLASS__, '_wp_enqueue_scripts' ) );
		add_action( 'wp_head', array( __CLASS__, '_wp_head' ) );

	}

	/**
	 *
	 */
	public static function _wp_enqueue_scripts() {

		$id = self::$_id;

		wp_enqueue_script('sharethis', "//platform-api.sharethis.com/js/sharethis.js#property={$id}&product=unknown", null, false, true );

	}

	/**
	 *
	 */
	public static function _wp_head() {

		if ( self::$_facebook_og ) {
			self::_facebook_og();
		}

	}

	/**
	 * The ShareThis account id.
	 *
	 * @param string $id
	 */
	public static function register_id( $id ) {

		self::$_id = $id;

	}

	/**
	 * @param string $service
	 * @param array  $params
	 */
	public static function register_service( $service, $params = array() ) {

		self::$_services[ $service ] = $params;

	}

	/**
	 * @param \WP_Post|null $post
	 * @param array         $args
	 */
	public static function the_sharing_links( $post = null, $args = array() ) {

		foreach ( self::$_services as $service => $params ) {
			$args = array_merge( $params, $args );
			self::_render_sharing_link( $args, $service, $post );
		}

	}

	/**
	 * @param array    $params
	 * @param string   $service
	 * @param \WP_Post $post
	 */
	public static function _render_sharing_link( $params, $service, $post ) {

		$classes = apply_filters( "wpst_link_classes_{$service}", array() );

		// set some defaults
		$args = wp_parse_args( $params, array(
			'url'         => null,
			'short_url'   => null,
			'title'       => null,
			'image'       => null,
			'description' => null,
			'username'    => null,
			'message'     => null,
			'share_count' => true,
		) );

		// if we have a post, we will use post values for the defaults
		if ( $post instanceof \WP_Post ) {
			$args = wp_parse_args( $params, array(
				'url'         => get_permalink( $post ),
				'short_url'   => null,
				'title'       => $post->post_title,
				'image'       => null,
				'description' => get_the_excerpt( $post ),
				'username'    => null,
				'message'     => null,
				'share_count' => true,
			) );

			if ( has_post_thumbnail( $post ) ) {
				$args['image'] = get_the_post_thumbnail_url( $post );
			}
		}

		printf(
			'<div data-network="%1$s" class="st-custom-button %2$s"%3$s%4$s%5$s%6$s%7$s%8$s%9$s>%10$s%11$s</div>',
			$service,
			implode( ' ', apply_filters( 'wpst_link_classes', $classes, $service ) ),
			self::_item_sharing_property( 'url',         $args['url'] ),
			self::_item_sharing_property( 'short_url',   $args['short_url'] ),
			self::_item_sharing_property( 'title',       $args['title'] ),
			self::_item_sharing_property( 'image',       $args['image'] ),
			self::_item_sharing_property( 'description', $args['description'] ),
			self::_item_sharing_property( 'username',    $args['username'] ),
			self::_item_sharing_property( 'message',     $args['message'] ),
			apply_filters( 'wpst_link_text', ucfirst( $service ) ),
			self::_item_sharing_count( $args['share_count'] )
		);

	}

	/**
	 * @link https://developers.facebook.com/docs/sharing/webmasters#basic
	 */
	private static function _facebook_og() {

		printf( '<meta property="og:url" content="%1$s" />' . PHP_EOL, self::_og_url() );
		print '<meta property="og:type" content="article" />' . PHP_EOL;
		printf( '<meta property="og:title" content="%1$s" />' . PHP_EOL, self::_og_title() );

		if ( ! ( empty( $description = self::_og_description() ) ) ) {
			printf(	'<meta property="og:description" content="%1$s" />' . PHP_EOL, $description );
		}

		if ( $image_url = self::_og_image() ) {
			printf( '<meta property="og:image" content="%1$s" />' . PHP_EOL, $image_url );
		}

	}

	/**
	 * @return mixed|void
	 */
	private static function _og_url() {

		$url = get_permalink();

		if ( is_home() || is_front_page() ) {
			$url = home_url();
		}

		return apply_filters( 'wpst_og_url', esc_url( $url ) );

	}

	/**
	 * @return mixed|void
	 */
	private static function _og_title() {

		$title = get_the_title();

		if ( is_home() || is_front_page() ) {
			$title = get_bloginfo( 'name' );
		}

		return apply_filters( 'wpst_og_title', wp_kses_post( $title ) );

	}

	/**
	 * @return mixed|void
	 */
	private static function _og_description() {

		$description = apply_filters( 'the_excerpt', get_the_excerpt() );

		if ( is_home() || is_front_page() ) {
			$description = get_bloginfo( 'description' );
		}

		return apply_filters( 'wpst_og_description', esc_html( $description ) );

	}

	/**
	 * @return mixed|void
	 */
	private static function _og_image() {

		$image_url = get_the_post_thumbnail_url();

		if ( is_home() || is_front_page() ) {
			$image_url = '';
		}

		return apply_filters( 'wpst_og_image', esc_url( $image_url ) );

	}

	/**
	 * @param string $property
	 * @param string $value
	 *
	 * @return string
	 */
	private static function _item_sharing_property( $property, $value ) {

		$text  = '';
		$maybe = apply_filters( "wpst_item_{$property}", $value );

		if ( ! empty( $maybe ) ) {
			$text = sprintf( ' data-%1$s="%2$s" ', str_replace('_', '-', $property ), esc_attr( $maybe ) );
		}

		return $text;

	}

	/**
	 * @param  bool $show
	 *
	 * @return string
	 */
	private static function _item_sharing_count( $show ) {

		$text = '';

		if ( $show ) {
			$text = '<span class="count"></span>';
		}

		return $text;

	}

}
