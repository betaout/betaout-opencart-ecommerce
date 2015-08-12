<?php
class ControllerModuleBetaout extends Controller {
	private $error = array(); 
	public $data=false;
        
     private $_fields = array(

        array(
            "label"=>"orderId",
        ),
        array(
            "label"=>"subtotalPrice",
        ),
        array(
            "label"=>"totalShippingPrice",
        ),
        array(
            "label"=>"totalPrice",
        ),
        array(
            "label"=>"totalDiscount",
        ),
        array(
            "label"=>"totalTaxes",
        ),
        array(
            "label"=>"email",
        ),
        array(
            "label"=>"paymentType",
        ),
        array(
            "label"=>"currency",
        ),
        array(
            "label"=>"createdTime",
        ),
        array(
            "label"=>"promoCode",
        ),
        array(
            "label"=>"productId",
        ),
        array(
            "label"=>"productTitle",
        ),
        array(
            "label"=>"productQty",
        ),
        array(
            "label"=>"categoryArray",
        ),
        array(
            "label"=>"categoryId",
        ),
        array(
            "label"=>"productPrice",
        ),
        array(
            "label"=>"discount",
        )
    );
    
	public function index() {   
		$this->load->language('module/betaout');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('betaout', $this->request->post);		
			
			//Write the settings to the betaout-proxy file
					
			$this->session->data['success'] = $this->language->get('text_success');
                       
						
			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$data['heading_title'] = $this->language->get('heading_title');
                
                $data['text_edit'] = $this->language->get('text_edit');
                $data['text_edit_csv'] = $this->language->get('text_edit_csv');
		$data['text_module'] = $this->language->get('text_module');
                $data['entry_api_key'] = $this->language->get('entry_api_key');
		$data['entry_tracker_location'] = $this->language->get('entry_tracker_location');
		$data['entry_site_id'] = $this->language->get('entry_site_id');
		
		$data['help_api_key'] = $this->language->get('help_api_key');
		$data['help_tracker_location2'] = $this->language->get('help_tracker_location2');
		$data['help_site_id2'] = $this->language->get('help_site_id2');
		$data['help_enable'] = $this->language->get('help_enable');
		
//		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
//		$data['button_download'] = $this->language->get('button_download');
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
                if (isset($this->error['api_key'])) {
			$data['error_api_key'] = $this->error['api_key'];
		} else {
			$data['error_api_key'] = '';
                }
		
 		if (isset($this->error['tracker_location'])) {
			$data['error_tracker_location'] = $this->error['tracker_location'];
		} else {
			$data['error_tracker_location'] = '';
		}

		if (isset($this->error['site_id'])) {
			$data['error_site_id'] = $this->error['site_id'];
		} else {
			$data['error_site_id'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/betaout', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$data['action'] = $this->url->link('module/betaout', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

       if (isset($this->request->post['betaout_title'])) {
			$data['betaout_title'] = $this->request->post['betaout_title'];
		} else {
			$data['betaout_title'] = $this->config->get('betaout_title');
		}
                
                if (isset($this->request->post['betaout_api_key'])) {
			$data['betaout_api_key'] = $this->request->post['betaout_api_key'];
		} else {
			$data['betaout_api_key'] = $this->config->get('betaout_api_key');
		}
                
           
		if (isset($this->request->post['betaout_site_id'])) {
			$data['betaout_site_id'] = $this->request->post['betaout_site_id'];
		} else {
			$data['betaout_site_id'] = $this->config->get('betaout_site_id');
		}	
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
                
                $this->data=$data;
		$this->response->setOutput($this->load->view('module/betaout.tpl', $data));	
	}
	
	public function validate() {
		
             if (isset($this->request->post['CSV_d']) )//&& !empty($this->request->post['end']) && !empty($this->request->post['start']))
            {
                
            $this->csv($this->request->post['start'],$this->request->post['end']);
            }
            
            if (strlen($this->request->post['betaout_api_key']) <= 31) {
			$this->error['warning'] = $this->language->get('error_api_key');
		}

                if (empty($this->request->post['betaout_site_id']) || !is_numeric($this->request->post['betaout_site_id']))
		{
			$this->error['site_id'] = $this->language->get('error_site_id');
		}
                
                if (empty($this->request->post['betaout_api_key']) || is_numeric($this->request->post['betaout_api_key']))
		{
			$this->error['api_key'] = $this->language->get('error_api_key');
		}

		if ($this->error) {
			return false;
		} else {
			return true;
		}
	}
        
        public function csv($start,$end)
        {
        $dat=array('start'=>$start,'limit'=>$end);
                header('Content-Description: File Transfer');
                     header('Content-Type: application/vnd.ms-excel');
                     header('Content-Disposition: attachment; filename='."order.csv");
                      header('Expires: 0');
                      header('Content-Transfer-Encoding: binary');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                    
                 echo $this->_getColumnLabels();
            
            $this->load->model('sale/order');
             $this->load->model('catalog/product');
           $orders=$this->model_sale_order->getOrders($dat);
           
           foreach ($orders as $order){
               
                    $orderd=$this->model_sale_order->getOrder($order['order_id']);
                    $order_info_totals = $this->model_sale_order->getOrderTotals($order['order_id']);
                    $totalproducts = $totaltaxes = null;
                    $products= $this->model_sale_order->getOrderProducts($order['order_id']);
          
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
                                                                    $promo_code .=$order_totals['title']. " ";
                                                                    break;
                                                                case "voucher":
                                                                    $order_discount += $order_totals['value'];
                                                                   $promo_code .=$order_totals['title']." ";
                                                                    break;
                                                                default:
                                                                    break;
                                                            }
                                                        }
          
          foreach ($products as $product)
          {
                                        $productdetail=$this->model_catalog_product->getProduct($product['product_id']);
                                        $productdis=$this->model_catalog_product->getProductDiscounts($product['product_id']);
                                        $abc = '"'.serialize($this->get_category_names_by_product($product['product_id'],TRUE)).'"';
                                        $abc1 = '"'.serialize($this->get_category_names_by_product($product['product_id'], FALSE)).'"';

                                        $totalproducts +=(int)$product['quantity'];
                                       $totaltaxes +=(int)$product['tax'];

              $line=array();//reset the line		
			
			$line[] = $order['order_id'];
                        $line[] = $order_subtotal;//subtotal price
                        $line[] = $order_shipping;
                        $line[] = $order['total'];
                        $line[] = $order_discount;
                        $line[] = $order_taxes;
                        $line[] = $orderd['email'];
                        $line[] = $orderd['payment_method'];
                        $line[] = $orderd['currency_code'];
                        $line[] = $orderd['date_added'];
                        $line[] = $promo_code;//promo code
			$line[] = $product['product_id'];//print
			$line[] = $product['name'];
			$line[] = $product['quantity'];//$product['product_quantity'];
			$line[] = $abc;//Categoty Array
			$line[] = $abc1;//Category Id Array
			$line[] = $product['price'];
			$line[] = 0;//json_encode($productdis); 
                    
                        echo $this->_getCsvLine($line);  
              
          }

		  }
       
           exit();
            
        }
        
        
    private function get_category_names_by_product($id, $flag=TRUE) {

       
         $this->load->model('catalog/product');
         $this->load->model('catalog/category');
          
        $_categories = $this->model_catalog_product->getProductCategories($id);
       
         
         if (!is_array($_categories)) {
           
                return array();
            
        }
          if ($flag) {
        $categories = array();
        foreach ($_categories as $key) {

            $category = $this->model_catalog_category->getCategory($key);
          
            if ($category['parent_id'] == 0) {


                $categories[$category['category_id']] = array('name' => $category['name'], 'pid' => 0);
            } else {
                $categories[$category['category_id']] = array('name' => $category['name'], 'pid' => $category['parent_id']);
            }
            }
          }
         else {
         $categories = array();
         foreach ($_categories as $key) {

            $category = $this->model_catalog_category->getCategory($key);

            if ($category['parent_id'] == 0) {

                $categories[$category['category_id']] = array('pid' => 0);
            } else {
                $categories[$category['category_id']] = array('pid' => $category['parent_id']);
            }
        }
     
        }
        return $categories;
    }
   
    private function _getColumnLabels() 
	{
	    $column_titles_array = array();
	    foreach($this->_fields as $f)
	    {
	        $column_titles_array[] = $f['label'];
	    }
	    return $this->_getCsvLine($column_titles_array);
	}

          private function _getCsvLine($list){
		foreach($list as $l){
			$l = '"' . addslashes($l) . '"';
		}
                
		return implode(",", $list)."\r\n";
	}  
 
}
?>