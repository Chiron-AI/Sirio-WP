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
			//add_action( 'woocommerce_cart_updated', array( $this, 'on_action_cart_updated'), 20, 1 );
			//add_action( 'woocommerce_cart_contents_changed', array( $this, 'on_action_cart_updated'), 20, 1 );
			add_action( 'woocommerce_add_to_cart', array( $this, 'on_action_cart_updated'), 20, 1 );
			add_action( 'oocommerce_after_cart_item_quantity_update', array( $this, 'on_action_cart_updated'), 20, 1 );
			add_action( 'woocommerce_cart_item_removed', array( $this, 'on_action_cart_updated'), 20, 1 );
			add_action( 'woocommerce_calculated_shipping', array( $this, 'on_action_cart_updated'), 20, 1 );
			add_action( 'woocommerce_cart_calculate_shipping_address', array( $this, 'on_action_cart_updated'), 20, 1 );
			add_action( 'woocommerce_removed_coupon', array( $this, 'on_action_cart_updated'), 20, 1 );
			add_action( 'woocommerce_applied_coupon', array( $this, 'on_action_cart_updated'), 20, 1 );
		}
		
		public function on_action_cart_updated( $cart_updated ){
			$applied_coupons = WC()->cart->get_applied_coupons();
			$subtotal        = number_format((float)WC()->cart->get_subtotal() + (float)WC()->cart->get_subtotal_tax(),2);
			$discounted       = WC()->cart->coupon_discount_totals;
			$shipping = number_format((float)WC()->cart->get_shipping_total(),2);
			$discount = (float)0;
			foreach(array_values($discounted) as $item){
				$discount+=(float)$item[0];
			}
			$discount = number_format((float)0,2);
			$total = number_format((float)WC()->cart->get_total('edit'),2);
			
			//TODO debug
			$coupon=array();
			foreach ($applied_coupons as $coupon) {
				$coupon[] = $coupon->code;
			}
			$coupon = implode(",", $coupon);

			/*
					quando questa funzione viene chiamata:
					metto in cart_new il carrello attuale
			*/
			$products = array();
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				$sku = $cart_item['data']->get_slug();
				$item_name = $cart_item['data']->get_title();
				$quantity = $cart_item['quantity'];
				$price = $cart_item['data']->get_price();
				$regular_price      = number_format((float)$cart_item['data']->get_regular_price(),2);
				$sale_price         = number_format((float)$cart_item['data']->get_sale_price(),2);
				$product = array(
					"sku" => $sku,
					"price" => $price,
					"qty" => $quantity,
					"name" => $item_name,
					"discount_amount" => number_format((float)$sale_price > 0?(float)$regular_price - (float)$sale_price:(float)0, 2),
				);
				$products[]=$product;
			}

			$cart_full = '{"cart_total":'.$total.', "cart_subtotal":'.$subtotal.', "shipping":'.$shipping.', "coupon_code":"'.$coupon.'", "discount_amount":'.$discount.', "cart_products":'.json_encode($products).'}';
			//print_r($cart_full);//exit;
			if(isset($_COOKIE['cart_new'])){
				@setcookie('cart_new', "", 1);
			}
			@setcookie('cart_new', base64_encode($cart_full), time() + (86400 * 30), "/");
			return $cart_updated;
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
			$limit = get_option( 'posts_per_page' );
			$products_count = $limit;
			$currency_code = get_woocommerce_currency();
			$search_string = get_search_query();
			$max_product_count=0;
			while(have_posts()){
				the_post();
				$max_product_count++;
			}
			
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
