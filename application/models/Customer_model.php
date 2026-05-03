<?php
class Customer_model extends CI_Model {

    private $table = "cd_customer";

    public function get_data($limit, $offset, $search, $order, $dir) {
        if ($search) {
            $this->db->group_start()
                ->like('CUST', $search)
                ->or_like('SHORT_NAME', $search)
                ->or_like('FULL_NAME', $search)
                ->or_like('CUST_CLASS', $search)
                ->group_end();
        }
        $this->db->order_by($order, $dir);
        return $this->db->get($this->table, $limit, $offset)->result_array();
    }

    public function count_data($search) {
        if ($search) {
            $this->db->group_start()
                ->like('CUST', $search)
                ->or_like('SHORT_NAME', $search)
                ->or_like('FULL_NAME', $search)
                ->or_like('CUST_CLASS', $search)
                ->group_end();
        }
        return $this->db->count_all_results($this->table);
    }

    public function generate_cust($cust_kind, $cust_class)
    {
        if ($cust_kind == 'CUSTOMER' || $cust_class == 'CUSTOMER') {
            $prefix = 'CS';
        } elseif ($cust_kind == 'SUPPLIER' || $cust_class == 'SUPPLIER') {
            $prefix = 'SP';
        } else {
            $prefix = 'CS';
        }

        $this->db->like('CUST', $prefix, 'after');
        $this->db->order_by('CUST', 'DESC');
        $this->db->limit(1);
        $q = $this->db->get('cd_customer'); // ganti nama tabel

        if ($q->num_rows() > 0) {
            $last = $q->row()->CUST;
            $num = (int) substr($last, 2);
            $num++;
        } else {
            $num = 1;
        }

        return $prefix . str_pad($num, 6, '0', STR_PAD_LEFT);
    }

    public function get_by_pk($cust) {
        return $this->db->get_where($this->table, ['CUST' => $cust])->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($cust, $data) {
        return $this->db->where('CUST', $cust)->update($this->table, $data);
    }

    public function delete($cust) {
        return $this->db->where('CUST', $cust)->delete($this->table);
    }
}
