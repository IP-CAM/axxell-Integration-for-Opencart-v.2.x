<?php

class ControllerModuleAxxell extends Controller
{
    private $error = array();

    public function install()
    {
        $this->load->model('extension/event');
        $this->model_extension_event->addEvent('axxell', 'catalog/controller/product/product/after', 'module/axxell/event_view_product');
        $this->model_extension_event->addEvent('axxell', 'catalog/model/checkout/order/addOrder/after', 'module/axxell/event_add_order');
        $this->model_extension_event->addEvent('axxell', 'admin/model/catalog/product/addProduct/after', 'module/axxell/event_add_product');
        $this->model_extension_event->addEvent('axxell', 'admin/model/catalog/product/deleteProduct/before', 'module/axxell/event_delete_product');
        $this->model_extension_event->addEvent('axxell', 'admin/model/catalog/product/editProduct/after', 'module/axxell/event_edit_product');
    }
    public function uninstall()
    {
        $this->load->model('extension/event');
        $this->model_extension_event->deleteEvent('axxell');
    }

    public function event_delete_product($route, $product_id)
    {
        $this->load->model('catalog/product');
        $product = $this->model_catalog_product->getProduct($product_id);
        $axxell = $this->getAxxellClient();
        $log = $this->getLogger();
        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting('axxell');

        try {
            $axxell->deleteItem($settings['axxell_accesskey'], $product_id);
            $log->write('INFO: Delete product ' . $product_id);
        } catch (\Axxell\ApiException $e) {
            $log->write('ERROR: ' . $e->getMessage());
            $log->write($e->getResponseBody());
        }
    }

    public function event_add_product($route, $product_id)
    {
        $this->event_edit_product($route, null, $product_id);
    }

    public function event_edit_product($route, $data, $product_id)
    {
        $this->load->model('catalog/product');
        $product = $this->model_catalog_product->getProduct($product_id);
        $categories = $this->getProductCategories($product);
        $axxell = $this->getAxxellClient();
        $log = $this->getLogger();
        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting('axxell');

        $item = new \Axxell\Model\Item();
        $item->setTitle($product['name']);
        $item->setItemId($product['product_id']);
        $item->setCategories($categories);
        try {
            $log->write($product);
            $axxell->registerItem($settings['axxell_accesskey'], $item);
            $log->write('INFO: Registered new product ' . $item);
        } catch (\Axxell\ApiException $e) {
            $log->write('ERROR: ' . $e->getMessage());
            $log->write($e->getResponseBody());
        }
    }

    public function index()
    {
        $this->load->language('module/axxell');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        $log = $this->getLogger();
        $settings = $this->model_setting_setting->getSetting('axxell');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('axxell', $this->request->post);
            if (isset($this->request->post['axxell_push_catalog'])) {
		        $settings = $this->model_setting_setting->getSetting('axxell');
                $axxell = $this->getAxxellClient();
                try {
                    $axxell->deleteAllItems($settings['axxell_accesskey']);
                    $this->load->model('catalog/product');
                    $products = $this->model_catalog_product->getProducts();
                    foreach ($products as $product) {
                        $this->event_add_product(null, $product['product_id']);
                    }
                } catch (\Axxell\ApiException $e) {
                    $log->write('ERROR: ' . $e->getMessage());
                    $log->write($e->getResponseBody());
                    $this->session->data['error'] = $this->language->get('error_push_catalog');
                    $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
                }
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['entry_accesskey'] = $this->language->get('entry_accesskey');
        $data['entry_secretkey'] = $this->language->get('entry_secretkey');
        $data['entry_apiurl'] = $this->language->get('entry_apiurl');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_push_catalog'] = $this->language->get('entry_push_catalog');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/axxell', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action'] = $this->url->link('module/axxell', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        $data['token'] = $this->session->data['token'];

        if (isset($this->request->post['axxell_accesskey'])) {
            $data['axxell_accesskey'] = $this->request->post['axxell_accesskey'];
        } else {
            $data['axxell_accesskey'] = $this->config->get('axxell_accesskey');
        }

        if (isset($this->request->post['axxell_secretkey'])) {
            $data['axxell_secretkey'] = $this->request->post['axxell_secretkey'];
        } else {
            $data['axxell_secretkey'] = $this->config->get('axxell_secretkey');
        }

        if (isset($this->request->post['axxell_apiurl'])) {
            $data['axxell_apiurl'] = $this->request->post['axxell_apiurl'];
        } else {
            $data['axxell_apiurl'] = $this->config->get('axxell_apiurl');
        }

        if (isset($this->request->post['axxell_status'])) {
            $data['axxell_status'] = $this->request->post['axxell_status'];
        } else {
            $data['axxell_status'] = $this->config->get('axxell_status');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('module/axxell.tpl', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'module/axxell')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['axxell_secretkey']) {
            $this->error['warning'] = $this->language->get('error_secretkey');
        }
        if (!$this->request->post['axxell_accesskey']) {
            $this->error['warning'] = $this->language->get('error_accesskey');
        }
        if (!$this->request->post['axxell_apiurl']) {
            $this->error['warning'] = $this->language->get('error_apiurl');
        }
        return !$this->error;
    }

    private function getLogger()
    {
        return new Log('axxell.log');
    }

    private function getProductCategories($product)
    {
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $categories = array();
        $product_categories = $this->model_catalog_product->getProductCategories($product['product_id']);
        foreach ($product_categories as $product_category) {
            $category = $this->model_catalog_category->getCategory($product_category);
            array_push($categories, $category['name']);
        }
        $tags = array_filter(explode(",", $product['tag']));
        $result = array_merge($tags, $categories);
        return $result;
    }

    private function getAxxellClient()
    {
        $log = $this->getLogger();
        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting('axxell');
        if (isset($settings['axxell_status'])) {
            $status = $settings['axxell_status'];
        } else {
            $status = false;
        }
        if ( $status == false || $status == 'false' || $status == 'disabled') {
            $log->write("WARN: Not sending product data because Axxell main module is disabled");
            return;
        }
        require_once(DIR_SYSTEM . '../vendor/axxell-client-php/autoload.php');
        $config = new \Axxell\Configuration();
        $config->setHost($settings['axxell_apiurl']);
        $config->setApiKey('x-api-key', $settings['axxell_secretkey']);
        $client = new \Axxell\ApiClient($config);
        $axxell = new \Axxell\Api\DefaultApi($client);
        return $axxell;
    }
}