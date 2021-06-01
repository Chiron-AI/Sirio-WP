<?php
	/**
	 * Enqueues Class.
	 *
	 * @since   0.0.1
	 *
	 * @package Chiron_Sirio
	 */
	
	namespace Chiron_Sirio;
	
	/**
	 * Class Enqueues
	 *
	 * Enqueues JS and CSS dependencies.
	 *
	 * @since   0.0.1
	 *
	 * @package Chiron_Sirio
	 */
	class Enqueues {
		
		/**
		 * Path to the root plugin file.
		 *
		 * @var     string
		 * @since   0.0.1
		 */
		private $plugin_root;
		
		/**
		 * Plugin name.
		 *
		 * @var     string
		 * @since   0.0.1
		 */
		private $plugin_name;
		
		/**
		 * Plugin slug.
		 *
		 * @var     string
		 * @since   0.0.1
		 */
		private $plugin_slug;
		
		/**
		 * Plugin prefix.
		 *
		 * @var     string
		 * @since   0.0.1
		 */
		private $plugin_prefix;
		
		/**
		 * Debug mode status
		 *
		 * @var     bool
		 * @since   0.0.1
		 */
		private $debug_mode;
		
		/**
		 * Asset Suffix
		 *
		 * @var     string
		 * @since   0.0.1
		 */
		private $asset_suffix;
		
		/**
		 * Constructor.
		 *
		 * @since   0.0.1
		 */
		public function __construct() {
			$this->plugin_root   = PLUGIN_NAME_ROOT;
			$this->plugin_name   = PLUGIN_NAME_NAME;
			$this->plugin_slug   = PLUGIN_NAME_SLUG;
			$this->plugin_prefix = PLUGIN_NAME_PREFIX;
			
			// Determine whether we're in debug mode, and what the
			// asset suffix should be.
			$this->debug_mode   = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? true : false;
			$this->asset_suffix = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
		}
		
		/**
		 * Unleash Hell.
		 *
		 * @since   0.0.1
		 */
		public function run() {
			// Enqueue Front-end JS.
			
			//exit;
			
			add_action( 'wp_enqueue_scripts', array( $this, 'public_enqueue_scripts' ), 10 );
		}
		
		/**
		 * Enqueue Public Scripts.
		 *
		 * @since   0.0.1
		 */
		public function public_enqueue_scripts() {
			
			$do_public_enqueue     = apply_filters( $this->plugin_prefix . 'do_public_enqueue', true );
			$do_public_js_enqueue  = apply_filters( $this->plugin_prefix . 'do_public_js_enqueue', true );
			
			// Public JS.
			if ( $do_public_enqueue && $do_public_js_enqueue ) {
				$public_js_url  = 'https://api.sirio.chiron.ai/api/v1/profiling';
				$public_js_path = dirname( $this->plugin_root ) . '/assets/dist/js/' . $this->plugin_slug . '-public' . $this->asset_suffix . '.js';
				
				wp_enqueue_script(
					$this->plugin_slug . '-public-js',
					$public_js_url,
					array( 'jquery' ),
					filemtime( $public_js_path ),
					true
				);
				
				
				$header_request = getallheaders();
				$header_response = headers_list();
				$header_response_status_code = http_response_code();
				
				
				$header_response_filtered = array();
				
				
				
				foreach ($header_response as $response) {
					$explode_pos = strpos($response,':');
					$key = substr($response, 0, $explode_pos);
					if($key !== 'Link'){
						$value = substr($response, $explode_pos);
						$header_response_filtered[] = array($key, $value);
					}
				}
				
				$headers = array(
					'request'=>array(
						'Accept-Encoding'=>$header_request['Accept-Encoding'],
						'Accept-Language'=>$header_request['Accept-Language'],
						'Cookie'=>$header_request['Cookie']
					),
					'response'=>array(
						$header_response_filtered,
						'status_code'=>$header_response_status_code
					)
				);
				
				$script = '
	 		 		var sirioCustomObject = {};
	 				sirioCustomObject.headers = '.json_encode($headers).';
	 		 ';
				
				wp_add_inline_script(
					$this->plugin_slug . '-public-js',
					$script,
					'after'
				);
			}
			
		}
	}
