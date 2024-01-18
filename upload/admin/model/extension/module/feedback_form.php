<?php
class ModelExtensionModuleFeedbackForm extends Model {
    public function install() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "feedback_form` (
              `feedback_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `email` varchar(255) NOT NULL,
              `phone` varchar(255) NOT NULL,
              `created_at` datetime NOT NULL,
              `page` varchar(255) NOT NULL,
              PRIMARY KEY (`feedback_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
    }

    public function getTotalFeedbacks() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "feedback_form");

        return $query->row['total'];
    }

    public function getFeedbacks($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "feedback_form`";

        $sort_data = array(
            'name',
            'email',
            'phone',
            'created_at',
            'page'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "feedback_form`");
    }
}
