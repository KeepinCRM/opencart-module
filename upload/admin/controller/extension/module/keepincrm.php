<?php
class ControllerExtensionModuleKeepincrm extends Controller {
  private $error = array();
  private $token = 'token';

  public function __construct($registry){
    parent::__construct($registry);
    $this->token = (defined('VERSION') && version_compare(VERSION,'3.0.0.0','>=')) ? 'user_token' : $this->token;

    $this->load->model('catalog/attribute');
    $this->load->model('catalog/product');
    $this->load->language('extension/module/keepincrm');
    $this->load->model('catalog/category');
    $this->load->model('catalog/manufacturer');
    $this->load->model('localisation/length_class');
  }

  public function install() {
    $this->load->model('extension/module/keepincrm');
    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->load->model('setting/extension');
    } else {
      $this->load->model('extension/extension');
    }
    $this->load->model('user/user_group');

    $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/keepincrm');
    $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/keepincrm');

    $this->model_extension_module_keepincrm->install();
  }

  public function uninstall() {
    $this->load->model('extension/module/keepincrm');
    $this->load->model('setting/setting');

    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->load->model('setting/extension');
    } else {
      $this->load->model('extension/extension');
    }

    $this->model_extension_module_keepincrm->uninstall();
    if (version_compare(VERSION,'3.0.0.0','>=')) {
      $this->model_setting_extension->uninstall('keepincrm', $this->request->get['extension']);
    } else {
      $this->model_extension_extension->uninstall('keepincrm', $this->request->get['extension']);
    }

    $this->model_setting_setting->deleteSetting($this->request->get['extension']);
  }

  public function index() {
    $this->load->language('extension/module/keepincrm');
    $data['k_api_key'] = $this->language->get('k_api_key');
    $data['k_data_types'] = $this->language->get('k_data_types');
    $data['k_lead'] = $this->language->get('k_lead'); 
    $data['k_client'] = $this->language->get('k_client'); 
    $data['k_address'] = $this->language->get('k_address'); 
    $data['k_payment'] = $this->language->get('k_payment');
    $data['k_delivery'] = $this->language->get('k_delivery'); 
    $data['k_source'] = $this->language->get('k_source');
    $data['k_settings'] = $this->language->get('k_settings');
    $data['k_module'] = $this->language->get('k_module');
    $data['k_coupon'] = $this->language->get('k_coupon');
    $data['k_store_name'] = $this->language->get('k_store_name');
    $data['k_company_name'] = $this->language->get('k_company_name');
    $data['k_url_xml'] = $this->language->get('k_url_xml');
    $data['h_xml'] = $this->language->get('h_xml');
    $data['h_custom_fild'] = $this->language->get('h_custom_fild');
    $data['h_basic_settings'] = $this->language->get('h_basic_settings');
    $data['h_contact'] = $this->language->get('h_contact');
    $data['k_user_id'] = $this->language->get('k_user_id');
    $data['heading_title'] = $this->language->get('heading_title');
    $data['success'] = $this->language->get('success');
    $data['error_warning'] = $this->language->get('error_warning');
    $data['k_tab_general'] = $this->language->get('k_tab_general');
    $data['k_tab_log'] = $this->language->get('k_tab_log');
    $data['k_button_clear'] = $this->language->get('k_button_clear');
    $data['field_required'] = $this->language->get('field_required');
    $data['d_api_key'] = $this->language->get('d_api_key');
    $data['d_data_types'] = $this->language->get('d_data_types');
    $data['d_source'] = $this->language->get('d_source');
    $data['d_address'] = $this->language->get('d_address');
    $data['d_payment'] = $this->language->get('d_payment');
    $data['d_delivery'] = $this->language->get('d_delivery');
    $data['d_coupon'] = $this->language->get('d_coupon');
    $data['d_store_name'] = $this->language->get('d_store_name');
    $data['d_company_name'] = $this->language->get('d_company_name');
    $data['d_user_id'] = $this->language->get('d_user_id');
    $data['text_success'] = $this->language->get('text_success');

    $this->document->setTitle($this->language->get('heading_title'));
    $this->load->model('setting/setting');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->model_setting_setting->editSetting('keepincrm', $this->request->post);
      $this->session->data['success'] = $this->language->get('text_success');
      #$this->response->redirect($this->url->link('extension/extension', $this->token.'=' . $this->session->data[$this->token], $this->ssl));
      $this->response->redirect($this->url->link('extension/extension', $this->token.'=' . $this->session->data[$this->token] . '&type=module', true));
    }

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else if (isset($this->session->data['error'])) {
      $data['error_warning'] = $this->session->data['error'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->error['key'])) {
      $data['error_key'] = $this->error['key'];
    } else {
      $data['error_key'] = '';
    }

    if (isset($this->error['field_required'])) {
      $data['error_field_required'] = $this->error['field_required'];
    } else {
      $data['error_field_required'] = '';
    }

    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
    } else {
      $data['success'] = '';
    }

    $data['breadcrumbs'] = array();
    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'), 
      'href' => $this->url->link('common/dashboard', $this->token.'=' . $this->session->data[$this->token], true)
    );
    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('k_module'), 
      'href' => $this->url->link('extension/module', $this->token.'=' . $this->session->data[$this->token], true)
    );
    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'), 
      'href' => $this->url->link('extension/module/keepincrm', $this->token.'=' . $this->session->data[$this->token], true)
    );

    $site_url = $_SERVER['HTTP_HOST'];
    $data['keepincrm_url_xml'] = $site_url.'/index.php?route=extension/module/keepincrm/import_xml';

    if (isset($this->request->post['keepincrm_key'])) {
      $data['keepincrm_key'] = $this->request->post['keepincrm_key'];
    } else {
      $data['keepincrm_key'] = $this->config->get('keepincrm_key');
    }
    if (isset($this->request->post['keepincrm_address'])) {
      $data['keepincrm_address'] = $this->request->post['keepincrm_address'];
    } else {
      $data['keepincrm_address'] = $this->config->get('keepincrm_address');
    }
    if (isset($this->request->post['keepincrm_payment'])) {
      $data['keepincrm_payment'] = $this->request->post['keepincrm_payment'];
    } else {
      $data['keepincrm_payment'] = $this->config->get('keepincrm_payment');
    }
    if (isset($this->request->post['keepincrm_delivery'])) {
      $data['keepincrm_delivery'] = $this->request->post['keepincrm_delivery'];
    } else {
      $data['keepincrm_delivery'] = $this->config->get('keepincrm_delivery');
    }
    if (isset($this->request->post['keepincrm_source'])) {
      $data['keepincrm_source'] = $this->request->post['keepincrm_source'];
    } else {
      $data['keepincrm_source'] = $this->config->get('keepincrm_source');
    }
    if (isset($this->request->post['keepincrm_coupon'])) {
      $data['keepincrm_coupon'] = $this->request->post['keepincrm_coupon'];
    } else {
      $data['keepincrm_coupon'] = $this->config->get('keepincrm_coupon');
    }
    if (isset($this->request->post['keepincrm_status'])) {
      $data['keepincrm_status'] = $this->request->post['keepincrm_status'];
    } else {
      $data['keepincrm_status'] = $this->config->get('keepincrm_status') ? $this->config->get('keepincrm_status') : 1;
    }
    if (isset($this->request->post['keepincrm_company_name'])) {
      $data['keepincrm_company_name'] = $this->request->post['keepincrm_company_name'];
    } else {
      $data['keepincrm_company_name'] = $this->config->get('keepincrm_company_name');
    }
    if (isset($this->request->post['keepincrm_store_name'])) {
      $data['keepincrm_store_name'] = $this->request->post['keepincrm_store_name'];
    } else {
      $data['keepincrm_store_name'] = $this->config->get('keepincrm_store_name');
    }
    if (isset($this->request->post['keepincrm_user_id'])) {
      $data['keepincrm_user_id'] = $this->request->post['keepincrm_user_id'];
    } else {
      $data['keepincrm_user_id'] = $this->config->get('keepincrm_user_id');
    }

    $file = DIR_LOGS . 'keepincrm.log';
    if (file_exists($file)) {
      $lines = file($file);
      $data_value = '';
      foreach(array_reverse($lines) as $line) { 
        $data_value .= $line;
      }
      $data['log'] = $data_value;
    } else {
      $data['log'] = '';
    }

    $data['clear_log'] = $this->url->link('extension/module/keepincrm/clearlog', $this->token.'=' . $this->session->data[$this->token], 'SSL');
    $data['action'] = $this->url->link('extension/module/keepincrm', $this->token.'=' . $this->session->data[$this->token], 'SSL');
    $data['cancel'] = $this->url->link('extension/extension', $this->token.'=' . $this->session->data[$this->token], 'SSL');

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/keepincrm', $data));
  }

  public function clearlog() {
    //$this->load->language('extension/module/keepincrm');
    //$data['text_success'] = $this->language->get('text_success');
    $handle = fopen(DIR_LOGS . 'keepincrm.log', 'w+');
    fclose($handle);

    //$this->session->data['success'] = $this->language->get('text_success');
    $this->response->redirect($this->url->link('extension/module/keepincrm', $this->token.'=' . $this->session->data[$this->token], true));
  }

  protected function validate() {
    $key = $this->request->post['keepincrm_key'];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.keepincrm.com/v1/agreements');
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'X-Auth-Token: '.$key.' ','Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    $out = curl_exec($curl);
    $info = curl_getinfo($curl);
    curl_close($curl);

    if ($info["http_code"] != '200') {
      $this->error['key'] = $this->language->get('error_key');
    }
    if (!$this->request->post['keepincrm_store_name']) {
      //$this->error['field_required'] = $this->language->get('field_required');
    }
    if (!$this->request->post['keepincrm_company_name']) {
      //$this->error['field_required'] = $this->language->get('field_required');
    }
    if (!$this->request->post['keepincrm_user_id']) {
      //$this->error['field_required'] = $this->language->get('field_required');
    }
    if (!$this->request->post['keepincrm_source']) {
    //$this->error['field_required'] = $this->language->get('field_required');
    }
    return !$this->error;
  }
}
