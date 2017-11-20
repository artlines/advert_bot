<?php

class Filters_model extends CI_Model {
  
  
  /**
   * Получить список
   * @author Alexey
   */
  function getFilters($data = []) {
    return $this->db
      ->where($data)
      ->order_by('name')
      ->get('shop_filters')
      ->result();
  }
  
  /**
   * Получить элемент
   * @author Alexey
   */
  function getFilter($id, $params = []) {
    $id = (int)$id;
    $result = $this->db
      ->where(['id' => $id])
      ->get('shop_filters')
      ->row();
    if ($params['values']) {
      $result->values = $this->getFilterValues($id);
    }
    return $result;
  }
  
  /**
   * Добавить
   * @author Alexey
   */
  function addFilter($data) {
    $valid = [
      'name' => 'Название'
    ];
    $data = array_intersect_key($data, $valid);
    $res = $this->db->insert('shop_filters', $data);
    return $res;
  }
  
  /**
   * Удалить
   * @author Alexey
   */
  function delFilter($id) {
    $id = (int)$id;
    $check = $this->db->where(['filter_id' => $id])->get('shop_product_filter_values')->num_rows();
    if ($check) {
      throw new Exception('Данный фильтр используется в ' . $check . ' товарах. Удаление невозможно.');
    }
    $this->db->trans_start();
    $this->db->delete('shop_filter_values', ['filter_id' => $id]);
    $this->db->delete('shop_filters', ['id' => $id]);
    $res = $this->db->trans_complete();
    return $res;
  }
  
  /**
   * Изменить фильтр
   * @author Alexey
   */
  function setFilter($id, $data) {
    $id = (int)$id;
    $this->db->trans_start();
    $this->db->update('shop_filters', ['name' => $data['name']], ['id' => $id]);
    $values = array_filter($data['values']);
    foreach ($values as $value) {
      $check = $this->db->where(['filter_id' => $id, 'value' => $value])->get('shop_filter_values')->num_rows();
      if (!$check) {
        $this->db->insert('shop_filter_values', [
          'filter_id' => $id,
          'value'     => $value
        ]);
      }
    }
    $this->db->query("
      DELETE FROM shop_filter_values
      WHERE filter_id = {$id}
        AND value NOT IN ('" . implode("','", $values) . "')
    ");
    $res = $this->db->trans_complete();
    return $res;
  }
  
  /**
   * Получить значения фильтра
   * @author Alexey
   */
  function getFilterValues($id) {
    $id = (int)$id;
    $result = $this->db->where(['filter_id' => $id])->get('shop_filter_values')->result();
    return $result;
  }
  
  /**
   * Получить фильтры, характерные для категории
   * @author Alexey
   */
  function getCategoryFilters($category_id) {
    $this->load->model('tovar_model');
    $this->tovar_model->_categoryFindChild($category_id);
    $category = implode(", ", array_merge([$category_id], (array)$this->category_list));
    $query = $this->db->query("
      SELECT sf.id, sf.name, sfv.id as value_id, sfv.value
      FROM shop_filters sf
        INNER JOIN shop_filter_values         sfv ON sf.id = sfv.filter_id
        INNER JOIN shop_product_filter_values pfv ON sfv.id = pfv.value_id
      WHERE pfv.product_id IN (
        SELECT id FROM shop_tovar
        WHERE active = 1
          AND id IN (
            SELECT tovar_id
            FROM shop_tovar_category
            WHERE category_id IN ({$category})
          )
      )
      ORDER BY sf.name, sfv.value
    ");
    $result = $query->result();
    $query->free_result();
    return $result;
  }
}
