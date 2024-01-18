<?php
class ControllerExtensionModuleFeedbackForm extends Controller {
    const FEEDBACK_FORM_TAG = '[feedback_form]';
    const FEEDBACK_FORM_ACTION = '[feedback_form_action]';

    public function information_index_after(&$route, &$data, &$output) {
        if (isset($this->request->get['store_id'])) {
            $store_id = $this->request->get['store_id'];
        } else {
            $store_id = 0;
        }

        $this->load->model('setting/setting');

        $output = str_replace(self::FEEDBACK_FORM_TAG, html_entity_decode($this->model_setting_setting->getSettingValue('module_feedback_form_text', $store_id), ENT_QUOTES, 'UTF-8'), $output);
        $output = str_replace(self::FEEDBACK_FORM_ACTION, $this->url->link('extension/module/feedback_form/addNewFeedback', 'user_token=' . $this->session->data['user_token'], true), $output);
    }

    public function addNewFeedback() {
        $data = $this->request->post;

        $this->load->model('extension/module/feedback_form');

        $this->model_extension_module_feedback_form->createFeedback($data);

        if (isset($this->request->server['HTTP_REFERER'])) {
            $this->response->redirect($this->request->server['HTTP_REFERER']);
        } else {
            $this->response->redirect($this->url->link('common/home'));
        }
    }
}
