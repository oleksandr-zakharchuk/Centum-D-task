<?php
class ModelExtensionModuleFeedbackForm extends Model {
    public function createFeedback($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "feedback_form SET `name` = '" . $data['username'] . "', `email` = '" . $data['email'] . "', `phone` = '" . $data['phone'] . "', `created_at` = NOW(), `page` = '" . $this->request->server['HTTP_REFERER'] . "'");
    }
}
