<?php


class ModelToolBetaout extends Model {

    // Used to store Betaout Tracker object (don't touch!)
    private $t;

    /* Variables defined to be used later in the code */
    /* ---------------------------------------------------------------------------------------- */
    private $betaout_https_url; // Betaout installation URL (https).
    private $betaout_http_url; // Betaout installation URL.
    private $betaout_site_id;  // The Site ID for the site in Betaout.
    // False for basic page tracking.
    private $betaout_use_sku;  // True - Report Betaout SKU from Opencart 'SKU'.
    // False - Report Betaout SKU from Opencart 'Model'.
    private $betaout_api_key;  // True - to enable the use of the betaout proxy script to hide trhe betaout URL.
    private $baseurl;

	// False - for regular Betaout tracking.
//	private $betaout_tracker_location;	// The full path to the BetaoutTracker.php file
    /* ---------------------------------------------------------------------------------------- */

    // Function to set various things up
    // Not 100% certain where most efficient to run, so just blanket running before each big block of API code
    // Called internally by other functions
    private function init() {
        // Load config data
        $this->load->model('setting/setting');

        $this->model_setting_setting->getSetting('betaout');

        $this->betaout_http_url = $this->config->get('config_url') . 'betaout/';
        $this->betaout_https_url = $this->config->get('config_url') . 'betaout/';
        $this->betaout_site_id = $this->config->get('betaout_site_id');
        $this->betaout_api_key = $this->config->get('betaout_api_key');

        $this->session->data['betaout_visitorid'] = "gh"; // $this->t->getVisitorId();

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('account/order');
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
         $this->baseurl = $this->config->get('config_ssl');
      } else {
         $this->baseurl = $this->config->get('config_url');
      }
    }

    // Returns the text needed for the JAVASCRIPT method of setEcommerceView
    // (to be inserted in javascript footer as it occurs on every page)
    // Other ecommerce actions not on every page use PHP API.
    // Private as this is called internally to this class by getFooterText()
    private function setEcommerceView() {
        /* Get the Category info */
        // First, check the GET variable 'path' is set
        // Set to false - category reporting not fully supported in this version
		/* Get the Product info */
        // Read the product ID from the GET variable
        $product_id = $this->request->get['product_id'];
        $i = 0;
        $productarray = array();
        // Look up the product info using the product ID					
        // Uses function from the catalog/product model
        $product = $this->model_catalog_product->getProduct($product_id);
        $abc = $this->get_category_names_by_product($product['product_id']);
        // Get the individual pieces of info
        if ($this->betaout_use_sku) {
            $product_info_sku = '"' . $product['sku'] . '"';
        } else {
            $product_info_sku = '"' . $product['model'] . '"';
        }
        $betaout_product = '"' . $product['name'] . '"';
        $betaout_price = (string) $product['price'];
        
        
        $productarray[$i]['productTitle'] = $product['name'];
        $productarray[$i]['sku'] = $product_info_sku;
        $productarray[$i]['price'] = $product['price'];
        $productarray[$i]['productId'] = $product['product_id'];
        $productarray[$i]['currency'] = $this->session->data['currency'];
        $productarray[$i]['category'] = $abc;
        $productarray[$i]['specialPrice'] = $product['price'];
        $productarray[$i]['status'] = ($product['status'] == 1) ? "enabled" : "disabled";
        $productarray[$i]['productPictureUrl'] = $this->baseurl.'image/'.$product['image'];
        $productarray[$i]['pageUrl'] = $this->baseurl."?route=product/product&product_id=".$product['product_id'];
        $productarray[$i]['qty'] = $product['quantity'];
        $productarray[$i]['totalProductPrice'] = $product['price'];
        $productarray[$i]['discountPrice'] = FALSE;
        $productarray[$i]['stockAvailability'] = $product['status'];
        $productarray[$i]['size'] = false;
        $productarray[$i]['color'] = false;

			// If there is no 'product_id' GET variable, then we are not in a product
			// So set the appropriate 'false' text to use (see betaout JavaScript function)

        $parray = array('pd' => $productarray,
            'or' => "",
            'action' => "viewed");
        $default = $this->betaout_get_defaults();
        $betaoutparams = array_merge($default, $parray);
        $requesturl = 'betaout.in/v1/user/customer_activity/';
        $result = $this->betaout_make_request($requesturl, $betaoutparams);

		return $result;
    }
   
	public function track()
	{
         $this->init();
		 $pq=$this->session->data['pro_qty'];
		 $cart_info = $this->cart->getProducts();
		 foreach ($cart_info as $info)
		 {
			if ($info['quantity']!=$pq[$info['product_id']]){
				$this->cartUpdate($info['product_id'],$info['quantity']);
			 }
			 }
		 		 
    }
	
	public function cartUpdate($id,$qty)
	{
		 $product = $this->model_catalog_product->getProduct($id);
         $abc = $this->get_category_names_by_product($id);
		 $productd=$pro_qty=array();
                    if ($this->betaout_use_sku) {
                        $product_info_sku = $product['sku'];
                    } else {
                        $product_info_sku = $product['model'];
                    }
                    $i = $id;

			$cartinfo = array("subtotalPrice" => $this->cart->getSubTotal());
                    $productd[$i]['productTitle'] = $product['name'];
                    $productd[$i]['sku'] = $product_info_sku;
                    $productd[$i]['price'] = $product['price'];
                    $productd[$i]['productId'] = $product['product_id'];
                    $productd[$i]['currency'] = $this->session->data['currency'];
                    $productd[$i]['category'] = $abc;
                    $productd[$i]['specialPrice'] = $product['price'];
                    $productd[$i]['status'] = ($product['status'] == 1) ? "enabled" : "disabled";
                    $productd[$i]['productPictureUrl'] = $this->baseurl.'image/'.$product['image'];
                    $productd[$i]['pageUrl'] = $this->baseurl."?route=product/product&product_id=".$product['product_id'];
                    $productd[$i]['qty'] = $qty;
                    $productd[$i]['totalProductPrice'] = $product['price'];
                    $productd[$i]['discountPrice'] = FALSE;
                    $productd[$i]['stockAvailability'] = $product['status'];
                    $productd[$i]['size'] = false;
                    $productd[$i]['color'] = false;

                    $parray = array("pd" => $productd,
                        "or" => $cartinfo,
                        "action" => "update_cart"
                    );
                    $default = $this->betaout_get_defaults();
                    $betaoutparams = array_merge($default, $parray);
                    $requesturl = 'betaout.in/v1/user/customer_activity/';
                    $result = $this->betaout_make_request($requesturl, $betaoutparams);
					
					$this->session->data['pro_qty'][$i] = $qty;
					
					return;
	}
   
    // Tracks a cart update with Betaout PHP API
    // Calls BetaoutTracker 'addEcommerceItem' iteratively for each product in cart
    // Calls BetaoutTracker 'doTrackEcommerceCartUpdate' at the end to track the cart update
   
	public function trackEcommerceCartUpdate()
		{

        $this->init();
        $productd = $productIdArray = $pro_qty=$padd=$productarray = array();
        /* Get the Cart info */
        // First, check if the cart has items in
		
            // Read all the info about items in the cart
            $cart_info = $this->cart->getProducts();
            // For product in the cart...
            $i=null;
            foreach ($cart_info as $cart_item) {
                // Get the info for this product ID					
                // Uses function from the catalog/product model
                $product = $this->model_catalog_product->getProduct($cart_item['product_id']);

                $abc = $this->get_category_names_by_product($cart_item['product_id']);

                if ($this->betaout_use_sku) {
                    $product_info_sku = $product['sku'];
                } else {
                    $product_info_sku = $product['model'];
                }
                $i = $product['product_id'];

                $productarray[$i]['productTitle'] = $product['name'];
                $productarray[$i]['sku'] = $product_info_sku;
                $productarray[$i]['price'] = $product['price'];
                $productarray[$i]['productId'] = $product['product_id'];
                $productarray[$i]['currency'] = $this->session->data['currency'];
                $productarray[$i]['category'] = $abc;
                $productarray[$i]['specialPrice'] = $product['price'];
                $productarray[$i]['status'] = ($product['status'] == 1) ? "enabled" : "disabled";
                $productarray[$i]['productPictureUrl'] = $this->baseurl.'image/'.$product['image'];
                $productarray[$i]['pageUrl'] = $this->baseurl."?route=product/product&product_id=".$product['product_id'];
                $productarray[$i]['qty'] = $cart_item['quantity'];
                $productarray[$i]['totalProductPrice'] = $product['price'];
                $productarray[$i]['discountPrice'] = FALSE;
                $productarray[$i]['stockAvailability'] = $product['status'];
                $productarray[$i]['size'] = false;
                $productarray[$i]['color'] = false;
				
				$pro_qty[$i]= $cart_item['quantity'];
				
                array_push($productIdArray, $product['product_id']);
            }
			
			
            $prev_cookie = isset($this->session->data['cookie']) ? $this->session->data['cookie'] : FALSE;

            $this->session->data['cookie'] = implode(',', $productIdArray);
            $added = array_diff($productIdArray, explode(',', $prev_cookie));
            
 
            $carray=  $this->session->data['cookie']==NULL ? NULL : explode(',', $this->session->data['cookie']);
            $parray = explode(',', $prev_cookie);

            if ((count($parray))>(count($carray))) {

                $deleted = array_diff(explode(',', $prev_cookie), $productIdArray);
 
            } else {
                $deleted = array();
            }

            $adcount = count($added);
            $deletecount = count($deleted);
			
			$cartinfo = array("subtotalPrice" => $this->cart->getSubTotal());
            if ($adcount) {
				
                foreach ($added as $id) {
					$padd[$id]=$productarray[$id];
                    $parray = array("pd" => $padd,
                        "or" => $cartinfo,
                        "action" => "add_to_cart"
                    );
 
                    $default = $this->betaout_get_defaults();
                    $betaoutparams = array_merge($default, $parray);
                    $requesturl = 'betaout.in/v1/user/customer_activity/';
                    $result = $this->betaout_make_request($requesturl, $betaoutparams);
                }
            }

            if ($deletecount) {
                foreach ($deleted as $id) {
                    $product = $this->model_catalog_product->getProduct($id);
                    $abc = $this->get_category_names_by_product($id);

                    if ($this->betaout_use_sku) {
                        $product_info_sku = $product['sku'];
                    } else {
                        $product_info_sku = $product['model'];
                    }
                    $i = $id;

                    $productd[$i]['productTitle'] = $product['name'];
                    $productd[$i]['sku'] = $product_info_sku;
                    $productd[$i]['price'] = $product['price'];
                    $productd[$i]['productId'] = $product['product_id'];
                    $productd[$i]['currency'] = $this->session->data['currency'];
                    $productd[$i]['category'] = $abc;
                    $productd[$i]['specialPrice'] = $product['price'];
                    $productd[$i]['status'] = ($product['status'] == 1) ? "enabled" : "disabled";
                    $productd[$i]['productPictureUrl'] = $this->baseurl.'image/'.$product['image'];
                    $productd[$i]['pageUrl'] = $this->baseurl."?route=product/product&product_id=".$product['product_id'];
                    $productd[$i]['qty'] = 1;
                    $productd[$i]['totalProductPrice'] = $product['price'];
                    $productd[$i]['discountPrice'] = FALSE;
                    $productd[$i]['stockAvailability'] = $product['status'];
                    $productd[$i]['size'] = false;
                    $productd[$i]['color'] = false;

                    $parray = array("pd" => $productd,
                        "or" => $cartinfo,
                        "action" => "removed_from_cart"
                    );
                    $default = $this->betaout_get_defaults();
                    $betaoutparams = array_merge($default, $parray);
                    $requesturl = 'betaout.in/v1/user/customer_activity/';
                    $result = $this->betaout_make_request($requesturl, $betaoutparams);

                }
            }
            if ($adcount == 0 && $deletecount == 0 ) {

				$pq=$this->session->data['pro_qty'];

				foreach ($cart_info as $info)
			 {
			if ($info['quantity']!=$pq[$info['product_id']])
				{
				$productarray[$info['product_id']]['qty']=$info['quantity']-$pq[$info['product_id']];
				$padd[$info['product_id']]=$productarray[$info['product_id']];
				$parray = array("pd" => $padd,
                    "or" => $cartinfo,
                    "action" => "add_to_cart"
                );
                $default = $this->betaout_get_defaults();
                $betaoutparams = array_merge($default, $parray);
                $requesturl = 'betaout.in/v1/user/customer_activity/';
                $result = $this->betaout_make_request($requesturl, $betaoutparams);
			 }
			 }
                
            }
			$this->session->data['pro_qty'] = $pro_qty;
            return $result;
    }

    private function get_category_names_by_product($id, $array = true) {

        $_categories = $this->model_catalog_product->getCategories($id);
        if (!is_array($_categories)) {
            if ($array)
                return array();
            else
                return "[]";
        }

        $categories = array();
        foreach ($_categories as $key) {
            $category = $this->model_catalog_category->getCategory($key['category_id']);

            if ($category['parent_id'] == 0) {

                $categories[$category['category_id']] = array('n' => $category['name'], 'p' => 0);
            } else {
                $categories[$category['category_id']] = array('n' => $category['name'], 'p' => $category['parent_id']);
            }
        }
        return $categories;
    }

    // Tracks an order with Betaout PHP API
    // Calls BetaoutTracker 'addEcommerceItem' iteratively for each product in order
    // Calls BetaoutTracker 'doTrackEcommerceOrder' at the end to track order
    public function trackEcommerceOrder($order_id) {

        $this->init();
        $productarray = array();
        $order_info = $this->model_account_order->getOrder($order_id);
        $order_info_products = $this->model_account_order->getOrderProducts($order_id);
        $order_info_totals = $this->model_account_order->getOrderTotals($order_id);

        $i = $tax = 0;
        $currency = $this->session->data['currency'];
        // Add ecommerce items for each product in the order before tracking
        foreach ($order_info_products as $order_product) {
            // Get the info for this product ID	
           $product_info = $this->model_catalog_product->getProduct($order_product['product_id']);
            
            $abc = $this->get_category_names_by_product($order_product['product_id']);
            // Decide whether to use 'Model' or 'SKU' from product info
            if ($this->betaout_use_sku) {
                $product_info_sku = (string) $product_info['sku'];
            } else {
                $product_info_sku = (string) $product_info['model'];
            }
            $tax +=$order_product['tax'];

            $productarray[$i]['productTitle'] = $order_product['name'];
            $productarray[$i]['sku'] = $product_info_sku;
            $productarray[$i]['price'] = $order_product['price'];
            $productarray[$i]['productId'] = $order_product['product_id'];
            $productarray[$i]['currency'] = $currency;
            $productarray[$i]['category'] = $abc;
            $productarray[$i]['specialPrice'] = $order_product['price'] + $order_product['tax'];
            $productarray[$i]['status'] = ($product_info['status'] == 1) ? "enabled" : "disabled";
            $productarray[$i]['productPictureUrl'] = $this->baseurl.'image/'.$product_info['image'];
            $productarray[$i]['pageUrl'] = (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false) . "?route=product/product&product_id=" . $order_product['product_id'];
            $productarray[$i]['qty'] = $order_product['quantity'];
            $productarray[$i]['totalProductPrice'] = $order_info_products[$i]['total'];
            $productarray[$i]['discountPrice'] = FALSE;
            $productarray[$i]['stockAvailability'] = $product_info['stock_status'];
            $productarray[$i]['size'] = false;
            $productarray[$i]['color'] = false;
            $productarray[$i]['orderId'] = $order_product['order_id'];
            $productarray[$i]['orderStatus'] = $order_info['order_status_id'];

            $i++;
        }

        $order_shipping = 0;
        $order_subtotal = 0;
        $order_taxes = 0;
        $order_grandtotal = 0;
        $order_discount = 0;
        $promo_code = "";
        // Find out shipping / taxes / total values
        foreach ($order_info_totals as $order_totals) {
            switch ($order_totals['code']) {
                case "shipping":
                    $order_shipping += $order_totals['value'];
                    break;
                case "sub_total":
                    $order_subtotal += $order_totals['value'];
                    break;
                case "tax":
                    $order_taxes += $order_totals['value'];
                    break;
                case "total":
                    $order_grandtotal += $order_totals['value'];
                    break;
                case "coupon":
                    $order_discount += $order_totals['value'];
                    $promo_code+=$this->session->data['coupon'] + " ";
                    break;
                case "voucher":
                    $order_discount += $order_totals['value'];
                    $promo_code+=$this->session->data['coupon'] + " ";
                    break;
                default:
                    break;
            }
        }

        $cartinfo = array("orderId" => $order_info['order_id'],
            "subtotalPrice" => $order_subtotal,
            "totalShippingPrice" => $order_shipping,
            "totalTaxes" => $tax,
            "totalDiscount" => $order_discount,
            "totalPrice" => $order_grandtotal,
            "promocode" => $promo_code,
            "financialStatus" => false,
            "paymentType" => $order_info['payment_method'],
            "currency" => $currency
        );

        $parray = array('pd' => $productarray,
            'or' => $cartinfo,
            'action' => "purchased");
        
        if (!$this->customer->isLogged()) {
          if(isset($this->session->data['guest']))
          {
              $email=$this->session->data['guest']['email'];
              $this->betaoutidentify($email);
          }
        }
        
        $default = $this->betaout_get_defaults();
        $betaoutparams = array_merge($default, $parray);
        $requesturl = 'betaout.in/v1/user/customer_activity/';
        $result = $this->betaout_make_request($requesturl, $betaoutparams);
        
        $this->session->data['cookie'] =NULL;
        
        return $result;        
       
  }
            
  public function betaoutidentify($email)
		  { 
                 $default = $this->betaout_get_defaults();
                $requesturl = 'betaout.in/v1/user/identify/';
                $result = $this->betaout_make_request($requesturl, $default);
                if($email!=""){
                setcookie("_ampEm", base64_encode($email)) ;
                setcookie("_ampUITN","") ;
                }
          }

    // Returns the Betaout Javascript text to place at the page footer
    // Generates based on Betaout URLs and settings
    // Includes code for setEcommerceView, depending on whether this option is set
    public function getFooterText() {

        $this->init();

        $customer_fname = "";
        $email = "";
        if ($this->customer->isLogged()) {
            $customer_fname = $this->customer->getFirstName();
            $email = $this->customer->getEmail();            
        }
        if (isset($this->request->get['product_id'])) {
            $this->setEcommerceView();
        }

        $betaout_footer = '<script type="text/javascript">
var _bOut = _bOut || [];var 
_bOutAKEY = "' . $this->betaout_api_key . '", 
_bOutPID = ' . $this->betaout_site_id . ', 

_bOutCW = true, _bOutST = true;var d = document, f =d.getElementsByTagName("script")[0], _sc =d.createElement("script");_sc.type = "text/javascript";_sc.async = true;_sc.src = "//d22vyp49cxb9py.cloudfront.net/jal.min.js";f.parentNode.insertBefore(_sc, f);
_bOut.push(["identify", {email:"' . $email . '", name: "' . $customer_fname . '"}]);
</script>';

        return $betaout_footer;
    }

    public function betaout_get_defaults() {
        $token = "";
        if (isset($_COOKIE['_ampUITN'])) {
            $token = $_COOKIE['_ampUITN'];
        }
        $email = "";
        $cemail= "";
       
        if ($this->customer->isLogged()) {
            $email = $this->customer->getEmail();
         }
        elseif (isset ($this->session->data['guest'])) {
            $email = $this->session->data['guest']['email'];
            
        }elseif (isset($_COOKIE['_ampEm'])) {
            $email = base64_decode($_COOKIE['_ampEm']);
            $cemail = base64_decode($_COOKIE['_ampEm']);
        }
        if ($email != "" && !is_null($email) && (isset($_COOKIE['_ampEm']) && $email==$cemail)) {
            $token = "";
        }

        $http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : FALSE;
        $properties = array(
            'ip' => $_SERVER['REMOTE_ADDR'],
            'systemInfo' => $_SERVER['HTTP_USER_AGENT'],
            'referrer' => $http_referer,
            'referring_domain' => parse_url($http_referer, PHP_URL_HOST),
        );
        $properties = array_merge($properties, array(
            'email' => $email,
        ));

        $properties = array_merge($properties, array(
            'apiKey' => $this->betaout_api_key,
            'token' => $token,
        ));
        // Let other modules alter the defaults.
       
        return $properties;
    }

    public function betaout_make_request($requesturl, $params) {
        try {
            $projectid = $this->betaout_site_id;
            $requesturl = "http://" . $projectid . "." . $requesturl;
            $data_string = json_encode($params);
            $params = array("params" => $data_string);

            $ch = curl_init($requesturl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $result = curl_exec($ch);
        } catch (Exception $e) {
            
        }
        return $result;
    }

}
