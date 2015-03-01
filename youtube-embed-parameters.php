<?php
/**
 * YouTube Embed Parameters
 *
 * @package   YouTubeEmbedParameters
 * @author    Brady Vercher
 * @link      http://www.cedaro.com/
 * @copyright Copyright (c) 2015 Cedaro, Inc.
 * @license   GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: YouTube Embed Parameters
 * Plugin URI:  https://github.com/cedaro/youtube-embed-parameters
 * Description: Customize parameters for embedded YouTube players, including oEmbed.
 * Version:     1.0.0
 * Author:      Cedaro
 * Author URI:  http://www.cedaro.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: youtube-embed-parameters
 * Domain Path: /languages
 * GitHub Plugin URI: cedaro/youtube-embed-parameters
 */

/**
 * Main plugin class.
 *
 * @package YouTubeEmbedParameters
 * @since 1.0.0
 */
class Cedaro_YouTube_Embed_Parameters {
	/**
	 * Default player parameters.
	 *
	 * @type array
	 * @since 1.0.0
	 */
	protected $parameters = array();

	/**
	 * Constructor method.
	 *
	 * Sets default parameters from user settings.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->set_parameters( $this->get_setting( 'query_args' ) );
	}

	/**
	 * Load the plugin.
	 *
	 * @since 1.0.0
	 */
	public function load() {
		$this->load_textdomain();
		$this->register_hooks();
	}

	/**
	 * Localize the plugin's strings.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		$plugin_rel_path = dirname( plugin_basename( __FILE__ ) ) . '/languages';
		load_plugin_textdomain( 'youtube-embed-parameters', false, $plugin_rel_path );
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_filter( 'embed_oembed_html', array( $this, 'filter_embed_html' ), 10, 3 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Retrieve player parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_parameters() {
		return $this->parameters;
	}

	/**
	 * Retrieve default parameters.
	 *
	 * @since 1.0.0
	 *
	 * @link https://developers.google.com/youtube/player_parameters
	 *
	 * @return array
	 */
	public function get_default_parameters() {
		return array(
			'autohide'       => 2,
			'autoplay'       => 0,
			'cc_load_policy' => '',
			'color'          => 'red',
			'controls'       => 1,
			'disablekb'      => 0,
			'enablejsapi'    => 0,
			'end'            => 0,
			'fs'             => 1,
			'hl'             => '',
			'iv_load_policy' => 1,
			'list'           => '',
			'listType'       => '',
			'loop'           => 0,
			'modestbranding' => 0,
			'origin'         => '',
			'playerapiid'    => '',
			'playlist'       => '',
			'playsinline'    => '',
			'rel'            => 1,
			'showinfo'       => 1,
			'start'          => 0,
			'theme'          => 'dark',
		);
	}

	/**
	 * Retrieve setting(s).
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Optional. Setting name.
	 * @return mixed The value for a setting if a key was passed. Defaults to an array of all settings.
	 */
	public function get_setting( $key = '' ) {
		$value = get_option( 'youtube_embed_parameters', array() );

		if ( ! empty( $key ) ) {
			$value  = isset( $value[ $key ] ) ? $value[ $key ] : null;
		}

		return $value;

	}

	/**
	 * Set player parameters.
	 *
	 * Merges valid parameters into the defaults.
	 *
	 * @since 1.0.0
	 *
	 * @param array $parameters An associative array of parameters.
	 */
	public function set_parameters( $parameters ) {
		if ( is_string( $parameters ) ) {
			parse_str( $parameters, $parameters );
		}

		$allowed_parameters = array_intersect_key( (array) $parameters, $this->get_default_parameters() );
		$this->parameters   = wp_parse_args( $allowed_parameters, $this->get_parameters() );
	}

	/**
	 * Filter embed HTML.
	 *
	 * Adds parameters to the URL in the `src` attribute of YouTube player
	 * iframes.
	 *
	 * Parameters come from the following places:
	 * - A user-defined setting.
	 * - Query arguments from the original YouTube URL.
	 * - Shortcode or API method arguments.
	 *
	 * Developers may also call Cedaro_YouTube_Embed_Parameters::set_parameters()
	 * directly.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html Embed HTML.
	 * @param string $url The embed URL.
	 * @param array $args Shortcode attributes or args from a method.
	 * @return string Filtered HTML.
	 */
	public function filter_embed_html( $html, $url, $args = array() ) {
		if ( false === strpos( $html, 'youtu' ) ) {
			return $html;
		}

		// Parse the URL and pass along valid query args.
		$query_args = $this->get_query_args( $url );

		// Mix in shortcode attributes or wp_oembed_get() args.
		$user_args = wp_parse_args( $args, $query_args );

		return $this->filter_html( $html, $user_args );
	}

	/**
	 * Add parameters to a YouTube URL in an iframe tag.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html HTML.
	 * @param array $args Parameters to override the global defaults.
	 * @return string
	 */
	public function filter_html( $html, $args ) {
		if ( preg_match( '/(?:src=)(?P<quote>[\'"])?(?P<url>.*?)(?(quote)(?P=quote))/im', $html, $matches ) ) {
			$html = preg_replace(
				'/src=[\'"].*?[\'"]/im',
				sprintf( 'src=%1$s%2$s%1$s',
					$matches['quote'],
					$this->add_query_args( $matches['url'], $args )
				),
				$html
			);
		}

		return $html;
	}

	/**
	 * Register settings and sections.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting(
			'media',
			'youtube_embed_parameters',
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			'youtube-embed-parameters',
			__( 'YouTube Embeds', 'youtube-embed-parameters' ),
			'__return_null',
			'media'
		);

		add_settings_field(
			'query_args',
			__( 'Default Parameters', 'youtube-embed-parameters' ),
			array( $this, 'render_field_parameters_string' ),
			'media',
			'youtube-embed-parameters'
		);
	}

	/**
	 * Display the field for entering default embed parameters.
	 *
	 * @since 1.0.0
	 */
	public function render_field_parameters_string() {
		$value = $this->get_setting( 'query_args' );
		?>
		<p>
			<input type="text" name="youtube_embed_parameters[query_args]" id="youtube-embed-parameters_query-args" value="<?php echo esc_attr( $value ); ?>" class="regular-text">
			<br>
			<span class="description">
				<?php _e( 'Add <a href="https://developers.google.com/youtube/player_parameters" target="_blank">player parameters</a> in query string format.', 'youtube-embed-parameters' ); ?>
				<?php printf( __( 'Example: %s.', 'youtube-embed-parameters' ), '<code>color=white&rel=0</code>' ); ?>
			</span>
		</p>
		<?php
	}

	/**
	 * Sanitize settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $value Settings array.
	 * @return array
	 */
	public function sanitize_settings( $value ) {
		$sanitized_value = array();

		if ( isset( $value['query_args'] ) ) {
			$sanitized_value['query_args'] = sanitize_text_field( $value['query_args'] );
		}

		return $sanitized_value;
	}

	/**
	 * Add parameters as query arguments to a URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url URL to add parameters to.
	 * @param array $args Optional. Parameter overrides.
	 * @return string URL with added query arguments.
	 */
	protected function add_query_args( $url, $args = array() ) {
		$defaults = $this->get_default_parameters();

		// Mix in global parameters with local args.
		$parameters = wp_parse_args( $args, $this->get_parameters() );

		// Whitelist allowed keys.
		$parameters = array_intersect_key( $parameters, $defaults );

		return add_query_arg( $parameters, $url );
	}

	/**
	 * Parse a URL and return any query arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url A URL.
	 * @return array An associative array of query arguments.
	 */
	protected function get_query_args( $url ) {
		$query_args   = array();
		$query_string = parse_url( $url, PHP_URL_QUERY );

		if ( ! empty( $query_string ) ) {
			parse_str( $query_string, $query_args );
		}

		return $query_args;
	}
}

/**
 * Initialize the plugin.
 */
$youtube_embed_parameters = new Cedaro_YouTube_Embed_Parameters();
add_action( 'plugins_loaded', array( $youtube_embed_parameters, 'load' ) );
