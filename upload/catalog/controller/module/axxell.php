<?php
class ControllerModuleAxxell extends Controller
{
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

    private function sendEvent($type, $user, $product)
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

        $event = new \Axxell\Model\Event();
        $event->setEventType($type);
        $event->setEntityId($user);
        $event->setTargetEntityId($product);
        try {
            $axxell->registerEvent($settings['axxell_accesskey'], $event);
            $log->write('INFO: Registered new event ' . $event);
        } catch (\Axxell\ApiException $e) {
            $log->write('ERROR: ' . $e->getMessage());
            $log->write($e->getResponseBody());
        }
    }

    public function event_view_product()
    {
        if (isset($this->request->get['product_id'])) {
            $product_id = (int)$this->request->get['product_id'];
        } else {
            return;
        }
        $log = $this->getLogger();
        $this->load->model('catalog/product');
        $product_info = $this->model_catalog_product->getProduct($product_id);
        if (!$product_info) {
            $log->write('DEBUG: unknown product with id ' . $product_id );
            return;
        }
        $user = $this->getCurrentUser();
        $this->sendEvent("view", $user, $product_id);
    }

    public function event_add_order($route, $order_id)
    {
        $log = $this->getLogger();
        if (!isset($order_id)) {
            return;
        }
        $log->write('order id ' . $order_id);
        $this->load->model('account/order');
        $order_products = $this->model_account_order->getOrderProducts($order_id);
        if (!$order_products) {
            $log->write('DEBUG: unknown order with id ' . $order_id);
            return;
        }
        $user = $this->getCurrentUser();
        $this->load->model('catalog/product');
        foreach ($order_products as $product) {
            $this->sendEvent("purchase", $user, $product['product_id']);
        }
    }

}
