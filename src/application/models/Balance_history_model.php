<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Balance_history_model extends CI_Model
{
    private $table = 'balance_history';

    public function create_event($record)
    {
        $record['created_at'] = date('Y-m-d H:i:s');

        if (!$this->db->insert($this->table, $record)) {
            return false;
        }

        return $this->db->insert_id();
    }
}
