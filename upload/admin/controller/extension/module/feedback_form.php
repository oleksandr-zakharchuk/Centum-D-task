<?php
class ControllerExtensionModuleFeedbackForm extends Controller {
    private $error = array();

    public function index() {
        $this->load->model('extension/module/feedback_form');

        $this->load->language('extension/module/feedback_form');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_feedback_form', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/feedback_form', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/feedback_form', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true);

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['module_feedback_form_text'])) {
            $data['module_feedback_form_text'] = $this->request->post['module_feedback_form_text'];
        } else {
            $data['module_feedback_form_text'] = $this->config->get('module_feedback_form_text');
        }

        if (isset($this->request->post['module_feedback_form_status'])) {
            $data['module_feedback_form_status'] = $this->request->post['module_feedback_form_status'];
        } else {
            $data['module_feedback_form_status'] = $this->config->get('module_feedback_form_status');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->getList($data);
    }

    protected function getList($data) {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['categories'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $category_total = $this->model_extension_module_feedback_form->getTotalFeedbacks();

        $results = $this->model_extension_module_feedback_form->getFeedbacks($filter_data);

        foreach ($results as $result) {
            $data['categories'][] = array(
                'name' => $result['name'],
                'email' => $result['email'],
                'phone' => $result['phone'],
                'created_at' => $result['created_at'],
                'page' => $result['page']
            );
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('extension/module/feedback_form', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
        $data['sort_email'] = $this->url->link('extension/module/feedback_form', 'user_token=' . $this->session->data['user_token'] . '&sort=email' . $url, true);
        $data['sort_phone'] = $this->url->link('extension/module/feedback_form', 'user_token=' . $this->session->data['user_token'] . '&sort=phone' . $url, true);
        $data['sort_created_at'] = $this->url->link('extension/module/feedback_form', 'user_token=' . $this->session->data['user_token'] . '&sort=created_at' . $url, true);
        $data['sort_page'] = $this->url->link('extension/module/feedback_form', 'user_token=' . $this->session->data['user_token'] . '&sort=page' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $category_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/feedback_form', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $this->response->setOutput($this->load->view('extension/module/feedback_form', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/feedback_form')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function install() {
        $this->load->model('extension/module/feedback_form');

        $this->model_extension_module_feedback_form->install();

        $this->load->model('setting/event');

        $this->model_setting_event->addEvent('feedback_form_replace_tag', 'catalog/view/information/information/after', 'extension/module/feedback_form/information_index_after');
    }

    public function uninstall() {
        $this->load->model('extension/module/feedback_form');

        $this->model_extension_module_feedback_form->uninstall();
    }
}
