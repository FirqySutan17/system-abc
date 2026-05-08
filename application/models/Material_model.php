<?php
class Material_model extends CI_Model {

    private $table = "abc_cd_material";

    public function get_data($limit, $offset, $search, $order, $dir)
    {
        $this->db->select('
            m.*,
            c.CODE_NAME AS material_class_name
        ');
        $this->db->from('abc_cd_material m');

        // JOIN ke cd_code berdasarkan material_class
        $this->db->join('cd_code c', 'c.CODE = m.material_class AND c.HEAD_CODE = "AE"', 'left');

        if ($search) {
            $this->db->group_start()
                ->like('m.material', $search)
                ->or_like('m.material_name', $search)
                ->or_like('m.material_class', $search)
                ->or_like('m.grade', $search)
                ->or_like('c.CODE_NAME', $search) // bisa search nama class juga
                ->group_end();
        }

        // Hindari SQL error kalau kolom order dari table alias
        $allowed_order = ['material','material_name','material_class','grade','material_class_name'];
        if (!in_array($order, $allowed_order)) {
            $order = 'material';
        }

        if ($order == 'material_class_name') {
            $this->db->order_by('c.CODE_NAME', $dir);
        } else {
            $this->db->order_by('m.'.$order, $dir);
        }

        return $this->db->get()->result_array();
    }

    public function count_data($search)
    {
        $this->db->from('abc_cd_material m');
        $this->db->join('cd_code c', 'c.CODE = m.material_class AND c.HEAD_CODE = "AE"', 'left');

        if ($search) {
            $this->db->group_start()
                ->like('m.material', $search)
                ->or_like('m.material_name', $search)
                ->or_like('m.material_class', $search)
                ->or_like('m.grade', $search)
                ->or_like('c.CODE_NAME', $search)
                ->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_by_pk($material)
    {
        return $this->db->get_where($this->table, ['material' => $material])->row();
    }

    public function detail($id) {
        return $this->db->where('material', $id)->get($this->table)->row_array();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        return $this->db->where('material', $id)->update($this->table, $data);
    }

    public function delete($id) {
        return $this->db->where('material', $id)->delete($this->table);
    }

    public function export($search, $order, $dir) {

        if ($search) {
            $this->db->group_start()
                ->like('material', $search)
                ->or_like('material_name', $search)
                ->or_like('material_class', $search)
                ->or_like('grade', $search)
                ->group_end();
        }

        $this->db->order_by($order, $dir);
        return $this->db->get($this->table)->result_array();
    }
}
