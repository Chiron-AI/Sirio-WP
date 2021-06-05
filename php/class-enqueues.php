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
					false,
					false
				);
				
				$this->script = $this->getHeaders();
				
				if(is_front_page() || is_home()) {
					$this->appendHomeJS();
				}
				else if(is_product()) {
					$this->appendProductJS();
				}
				else if(is_product_category()) {
					$this->appendProductCategoryJS();
				}
				else if (is_search()) {
					$this->appendProductSearchJS();
				}
				else if (is_checkout()) {
					$this->appendCheckoutJS();
				}
				else if (is_order_received_page()) {
					$this->appendCheckoutSuccessJS();
				}
				
				wp_add_inline_script(
					$this->plugin_slug . '-public-js',
					$this->script,
					'after'
				);
			}
			
		}
		
		protected function getHeaders(){
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
			
			$script = 'var sirioCustomObject = {};
	 				 sirioCustomObject.headers = '.json_encode($headers).';';
			
			return $script;
		}
		
		private function appendHomeJS() {
			$locale = explode('-', get_bloginfo('language'))[0];
			$currency_code = get_woocommerce_currency();
			$this->script = ' //<![CDATA[
                     '.$this->script.'
                     sirioCustomObject.pageType = "home";
                     sirioCustomObject.locale = "'.$locale.'";
                     sirioCustomObject.currency = "'.$currency_code.'";
                     //]]>';
		}
		
		private function appendProductJS() {
			$current_product = wc_get_product();
			$locale = explode('-', get_bloginfo('language'))[0];
			$currency_code = get_woocommerce_currency();
			$image_id  = $current_product->get_image_id();
			$image_url = wp_get_attachment_image_url( $image_id, 'full' );
			
			if($current_product->get_variation_price( 'max' )){
				// Regular price min and max
				$regular_price = $current_product->get_variation_regular_price( 'max' );
				// Sale price min and max
				$sale_price = $current_product->get_variation_sale_price( 'max' );
			}
			else{
				$regular_price = $current_product->regular_price;
				$sale_price = $current_product->sale_price;
			}
			
			
			$this->script = ' //<![CDATA[
                     '.$this->script.'
                     sirioCustomObject.productDetails = {"sku":"'.$current_product->get_slug().'", "name":"'.$current_product->get_name().'","image":"'.$image_url.'","description":"'.addslashes(str_replace("\n","", str_replace("\r","", str_replace("\t","", strip_tags($current_product->get_description()))))).'","price":"'.$regular_price.'","special_price":"'.$sale_price.'"};
                     sirioCustomObject.pageType = "product";
                     sirioCustomObject.locale = "'.$locale.'";
                     sirioCustomObject.currency = "'.$currency_code.'";
                     //]]>';
			
		}
		
		private function appendProductCategoryJS() {
			$locale = explode('-', get_bloginfo('language'))[0];
			$page = (get_query_var('paged')) ? get_query_var('paged') : 1;;
			$current_category = get_queried_object();
			
			$products = wc_get_products(array(
				'category' => array($current_category->slug),
			));
			$max_product_count = count($products);
			$limit = get_option( 'posts_per_page' );
			$products_count = $limit;
			$currency_code = get_woocommerce_currency();
			
			if($max_product_count % $limit > 0){
				$pages = (int)($max_product_count / $limit) + 1 ;
			}
			else{
				$pages = $max_product_count / $limit ;
			}
			if($page == $pages){
				$products_count = $max_product_count % $limit;
			}
			
			$thumbnail_id = get_term_meta( $current_category->term_id, 'thumbnail_id', true );
			$image_url = wp_get_attachment_url( $thumbnail_id );
			
			$this->script = ' //<![CDATA[
                     '.$this->script.'
                     sirioCustomObject.categoryDetails = {"name":"'.$current_category->name.'","image":"'.$image_url.'","description":"'.addslashes(str_replace("\n","", str_replace("\r","", str_replace("\t","",strip_tags($current_category->description))))).'"};
                     sirioCustomObject.pageType = "category";
                     sirioCustomObject.numProducts = '.$products_count.';
                     sirioCustomObject.pages = '.$pages.';
                     sirioCustomObject.currentPage = '.$page.';
                     sirioCustomObject.locale = "'.$locale.'";
                     sirioCustomObject.currency = "'.$currency_code.'";
                     //]]>';
		}
		
		private function appendProductSearchJS() {
			$locale = explode('-', get_bloginfo('language'))[0];
			$page = (get_query_var('paged')) ? get_query_var('paged') : 1;;
			$current_category = get_queried_object();
			$max_product_count = count($products);
			$limit = get_option( 'posts_per_page' );
			$products_count = $limit;
			
			$currency_code = get_woocommerce_currency();
			$query = new ProductSearchQuery();
			$search_string = Tools::getValue('s');
			$query->setSortOrder(new SortOrder('product', 'position', 'desc'))
				->setSearchString($search_string);
			$provider = $this->getProductSearchProviderFromModules($query);
			
			// if no module wants to do the query, then the core feature is used
			if (null === $provider) {
				$provider = $this->getDefaultProductSearchProvider();
			}
			// the search provider will need a context (language, shop...) to do its job
			$context = $this->getProductSearchContext();
			$result = $provider->runQuery(
				$context,
				$query
			);
			$max_product_count = $result->getTotalProductsCount();
			if($max_product_count % $limit > 0){
				$pages = (int)($max_product_count / $limit) + 1 ;
			}
			else{
				$pages = $max_product_count / $limit ;
			}
			if($page == $pages){
				$products_count = $max_product_count % $limit;
			}
			$this->script = '//<![CDATA[
                     '.$this->script.'
                     sirioCustomObject.pageType = "search";
                     sirioCustomObject.numProducts = '.$products_count.';
                     sirioCustomObject.pages = '.$pages.';
                     sirioCustomObject.currentPage = '.$page.';
                     sirioCustomObject.locale = "'.$locale.'";
                     sirioCustomObject.currency = "'.$currency_code.'";
                     //]]>';
		}
		
		private function appendCheckoutJS() {
			$locale = explode('-', get_bloginfo('language'))[0];
			$currency_code = get_woocommerce_currency();
			$this->script = '//<![CDATA[
                     '.$this->script.'
                     sirioCustomObject.pageType = "checkout";
                     sirioCustomObject.locale = "'.$locale.'";
                     sirioCustomObject.currency = "'.$currency_code.'";
                     //]]>';
		}
		
		private function appendCheckoutSuccessJS() {
			$locale = explode('-', get_bloginfo('language'))[0];
			$currency_code = get_woocommerce_currency();
			
			if(isset($_COOKIE['cart_new'])){
				unset($_COOKIE['cart_new']);
			}
			
			$this->script = '//<![CDATA[
                     '.$this->script.'
                     sirioCustomObject.pageType = "checkout_success";
                     sirioCustomObject.locale = "'.$locale.'";
                     sirioCustomObject.currency = "'.$currency_code.'";
                     //]]>';
		}
		
	}
