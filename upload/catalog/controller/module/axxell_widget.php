<?php
class ControllerModuleAxxellWidget extends Controller
{

    public function index($setting)
    {
        $this->load->language('module/axxell');

        $data['heading_title'] = $setting['name'];

        $data['button_cart'] = $this->language->get('button_cart');
        $data['button_wishlist'] = $this->language->get('button_wishlist');
        $data['button_compare'] = $this->language->get('button_compare');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        if (file_exists('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/axxell.css')) {
            $this->document->addStyle('catalog/view/theme/' . $this->config->get('config_template') . '/stylesheet/axxell.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/axxell.css');
        }

        $data['products'] = array();

        $product_id = $this->request->get["product_id"];
        $product_info = $this->model_catalog_product->getProduct($product_id);
        $data['product_id'] = $product_id;
        $data['width'] = (string)((int)100 / (int)$setting['limit']) . '%';
        $this->load->model('setting/setting');
        $axxell_settings = $this->model_setting_setting->getSetting('axxell');

        require_once(DIR_SYSTEM . '../vendor/axxell-client-php/autoload.php');
        $config = new \Axxell\Configuration();
        $config->setHost($axxell_settings['axxell_apiurl']);
        $config->setApiKey('x-api-key', $axxell_settings['axxell_secretkey']);
        $client = new \Axxell\ApiClient($config);
        $axxell = new \Axxell\Api\DefaultApi($client);
        $store_id = $axxell_settings['axxell_accesskey'];
        $user = $this->getCurrentUser();
		$logger = $this->getLogger();
        if (isset($this->request->get['product_id'])) {
            $product_id = (int)$this->request->get['product_id'];
        } else {
            $product_id = null;
        }

        try {
			if ($setting['enginetype'] == "personalized") {
				$items = $axxell->recommendInteresting($store_id, $user);
			} else {
				$items = $axxell->recommendSimilar($store_id, $user, $product_id);
			}
        } catch (\Axxell\ApiException $e) {
			$logger->write("Failed to retrieve recommendations from Axxell");
			$logger->write($e->getMessage());
			$items = array();
        }

        $count = 0;
        $limit = $setting['limit'];
        if (count($items) > 0) {
			foreach ($items as $item) {
				if ($count >= $limit)
					break;
				// skip self
				if ($item->getItemId() == $product_id) {
					continue;
				}
				$product_info = $this->model_catalog_product->getProduct($item->getItemId());

				if ($product_info) {
					$data['products'][] = $this->getProduct($product_info, $setting);
					$count = $count + 1;
				}
			}
        }
        // fill in with popular products
        if ($count < $limit) {
        	$popular_products = $this->model_catalog_product->getPopularProducts($limit + 1);
        	foreach ($popular_products as $product_info) {
				if ($count >= $limit)
					break;
				// skip self
				if ($product_info['product_id'] == $product_id) {
					continue;
				}
				$logger->write("Filler item: " . $product_info['product_id']);

				$data['products'][] = $this->getProduct($product_info, $setting);
				$count = $count + 1;
        	}
        }

        //return $this->load->view($this->config->get('config_template') . '/module/axxell_widget.tpl', $data);
        return $this->load->view('/module/axxell_widget.tpl', $data);
    }

    private function getCurrentUser()
    {
        if ($this->customer->isLogged()) {
            $user = $this->customer->getEmail();
        } else {
            $user = session_id(); // guest
        }
        return $user;
    }

    private function getLogger()
    {
        return new Log('axxell.log');
    }
    
    private function getProduct($product_info, $setting)
    {
		if ($product_info['image']) {
			$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
		} else {
			$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
		}

		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
		} else {
			$price = false;
		}

		if ((float)$product_info['special']) {
			$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
		} else {
			$special = false;
		}

		if ($this->config->get('config_tax')) {
			$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
		} else {
			$tax = false;
		}


		if ($this->config->get('config_review_status')) {
			$rating = $product_info['rating'];
		} else {
			$rating = false;
		}

		return array(
			'product_id' => $product_info['product_id'],
			'thumb' => $image,
			'name' => $product_info['name'],
			'price' => $price,
			'tax' => $tax,
			'special' => $special,
			'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
			'rating' => $rating,
			'href' => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
		);
    }
}
