<?php

class Tovar_model extends CI_Model {
  
  static $categoryCache = array();
  
  function __construct() {
    parent::__construct();
    $this->user_id = (int)$_SESSION['user_id'];
  }
  
  /**
   * Поиск товаров
   */
  function Find($params, $countOnly = false) {
    $suffix = $params['suffix'];
    // фильтры
    if (!empty($params['filter'])) {
      foreach ($params['filter'] as $key => $value) {
        switch ($key) {
          case 'anylike':
            $filter[] = "(
              lower(t1.name) like '%{$value}%' or 
              lower(t1.code) like '%{$value}%' or 
              lower(t1.code_1c) like '%{$value}%' or 
              lower(t2.manufacturer_name) like '%{$value}%' or
              t1.id in (select tovar_id from shop_tovar_values where lower(value) like '%{$value}%')
            )";
          break;
          case 'category_id':
            $this->category_list = array();
            $this->_categoryFindChild((int)$value);
            $category = implode(", ", $this->category_list);
            $filter[] = "t1.id in (select distinct tovar_id from shop_tovar_category where category_id in ({$category}))";
          break;
          case 'sales_main':
            $filter[] = "t1.id in (select tovar_id from shop_tovar_values where var_id=".TOVAR_SALES_MAIN_FIELD." and value>0)";
          break;
          case 'sales':
            $filter[] = "(t4.value>0 AND t1.price<>t4.value)";
          break;
          case 'with_category':
            $filter[] = "t1.id in (select tovar_id from shop_tovar_category)";
          break;
          case 'search':
            $value = htmlspecialchars_decode(trim($value), ENT_QUOTES);
            $value = mb_strtolower($value);
            $value = preg_replace("/[^0-9A-Za-zА-Яа-я]+/u", "%", $value);
            $filter[] = "(t1.string like '%{$value}%' OR t1.name like '%{$value}%' OR t1.description like '%{$value}%')";
          break;
          case 'vars':
            foreach ((array)$value as $val_id => $val_item) {
              if ($val_item=='') {
                continue;
              }
              $filter[] = "t1.id in (
                select tovar_id from shop_tovar_values 
                where var_id = " . ((int)$val_id) . " 
                  and " . ($val_item===true ? "value > 0" : "value = '{$val_item}'") . "
              )";
            }
          break;
          case 'with_photo':
            $filter[] = "t1.id " . ($value ? "" : "NOT") . " in (select tovar_id from shop_tovar_photo WHERE status = 1)";
          break;
          case 'brand':
            if (is_array($value) && ! empty($value) ) {
              $list = implode(",", $value);
              $filter[] = "t1.manufacturer_id IN ({$list})";
            }
            elseif (is_numeric($value) && $value > 0) {
              $filter[] = "t1.manufacturer_id = " . (int)$value;
            }
          break;
          case 'price_from':
            $filter[] = "t1.price >= " . (float)$value;
          break;
          case 'price_to':
            if ($value > 0) {
              $filter[] = "t1.price <= " . (float)$value;
            }
          break;
          case 'filters':
            foreach ((array)$value as $filter_id => $v_item) {
              $v_ids = [];
              foreach ($v_item as $v_id => $v_name) {
                if ($filter_id == 'brands') {
                  $filter[] = "t1.manufacturer_id = '{$v_id}'";
                  continue 2;
                }
                else {
                  $v_ids[] = $v_id;
                }
              }
              $filter[] = "t1.id IN (SELECT product_id FROM shop_product_filter_values WHERE value_id IN (" . implode(",", $v_ids) . "))";
            }
          break;
          default:
            $parts = explode(" ", $key);
            if (count($parts)==2) {
              $key      = $parts[0];
              $operator = $parts[1];
              $operator=='like'
                ? $filter[] = "{$key} {$operator} '%{$value}%'"
                : $filter[] = "{$key} {$operator} '{$value}'";
            }
            else {
              $filter[] = "{$key}='{$value}'";
            }
        }
      }
    }
    
    // сортировка
    $valid_sort = $this->config->item('valid_sort');
    $sort_field  = ($valid_sort[$params['sort']] ? $params['sort'] : $this->config->item('default_sort'));
    $sort_direct = (in_array($params['sdir'], $this->config->item('valid_sdir')) ? $params['sdir'] : $this->config->item('default_sdir'));
    
    // количество на страницу
    $limit = ($params['limit'] > 0 ? (int)$params['limit'] : $this->config->item('default_limit'));
    $page = max((int)$params['page'], 1);
    $offset = ($page - 1) * $limit; 
    
    $and = implode(" and ", (array)$filter);
    
    // посчитаем сколько 
    $query_count = 
       "select count(*) as cnt, group_concat(DISTINCT t1.id ORDER BY {$sort_field} {$sort_direct}, t1.id ASC SEPARATOR ',') AS list
        from 
          shop_tovar{$suffix}         t1 left join
          shop_tovar_manufacturer     t2 on t1.manufacturer_id=t2.id
          LEFT JOIN shop_tovar_values t5 ON t1.id = t5.tovar_id AND t5.var_id = " . PRODUCT_FIELD_MIN_ORDER . "
        where 1=1" . ($and<>'' ? " and " : "") . $and;
    $count_res = $this->db->query($query_count)->row();
    $data['count'] = $count_res->cnt;
    $data['list']  = $count_res->list;
    if ($countOnly) {
      return $data;
    }
    
    // выберем данные
    $query_select = "
     SELECT  t1.id, t1.id as tovar_id, t1.code_1c, t1.code, t1.name, 
              round(t1.price) as price,
              t1.manufacturer_id,
              t1.description,
              t1.type,
              t1.translite,
              t1.url,
              t2.manufacturer_name as manufacturer,
              t2.manufacturer_name as brand,
              t3.thumb_file as photo_main, t3.big_file as photo_main_big,
              group_concat(DISTINCT t4.name ORDER BY t4.price ASC SEPARATOR ' / ') as variants,
              CASE WHEN MAX(t4.price) > 0 THEN MAX(t4.price) ELSE MIN(t4.price) END as price_var,
              t5.value as weight,
              t6.value as latest,
              t7.value as old_price,
              MAX(t9.value) as size
      FROM 
        shop_tovar{$suffix}                   t1
        LEFT JOIN shop_tovar_manufacturer     t2 ON t1.manufacturer_id = t2.id
        LEFT JOIN shop_tovar_photo            t3 ON (t1.id = t3.tovar_id and t3.is_main = 1 AND t3.status = 1)
        LEFT JOIN shop_product_variants       t4 ON t1.id = t4.product_id
        LEFT JOIN shop_tovar_values           t5 ON t1.id = t5.tovar_id AND t5.var_id = " . PRODUCT_FIELD_WEIGHT . "
        LEFT JOIN shop_tovar_values           t6 ON t1.id = t6.tovar_id AND t6.var_id = " . PRODUCT_FIELD_LATEST . "
        LEFT JOIN shop_tovar_values           t7 ON t1.id = t7.tovar_id AND t7.var_id = " . PRODUCT_FIELD_SALE_PRICE . "
        LEFT JOIN shop_product_filter_values  t8 ON t1.id = t8.product_id AND t8.filter_id = " . PRODUCT_FILTER_SIZE . "
        LEFT JOIN shop_filter_values          t9 ON t9.id = t8.value_id
      WHERE 1 = 1" . ($and <> '' ? " AND " : "") . $and . "
      GROUP BY t1.id
      ORDER BY {$sort_field} {$sort_direct}, t1.id DESC
      LIMIT {$limit} offset {$offset}
    ";
    // adebug($query_select);
    $data['search'] = $this->db->query($query_select)->result();
    
    // доп параметры посмотрим
    $data['page']   = $page;
    $data['sort']   = $sort_field;
    $data['sdir']   = $sort_direct;
    $data['limit']  = $limit;
    $data['pages']  = ceil($data['count']/$limit);
    return $data;
  }
  
  /**
   * Загрузить массив дочерних категорий
   */
  function _categoryFindChild($category_id) {
    $category_id = (int)$category_id;
    if ( !empty(self::$categoryCache[$category_id]) ) {
      return ($this->category_list = self::$categoryCache[$category_id]);
    }
    array_push($this->category_list, $category_id);
    $res = $this->categoryFind($category_id);
    foreach ($res as $item) {
      array_push($this->category_list, $item->id);
      if ($item->cnt>0) {
        $this->_categoryFindChild($item->id);
      }
    }
    self::$categoryCache[$category_id] = $this->category_list;
    return;
  }
  
  /**
   * Поиск производителей в категории
   */
  function findCatergoryManufacturers($category_id, $filter = array()) {
    $category_id = (int)$category_id;
    $this->category_list = array();
    $this->_categoryFindChild($category_id);
    $category = implode(", ", $this->category_list);
    if ( !$category ) {
      return;
    }
    $where = "";
    foreach ((array)$filter['vars'] as $key => $item) {
      $key  = (int)$key;
      $item = trim($item);
      if (!$item) {
        continue;
      }
      $where .= " and t2.id in (select tovar_id from shop_tovar_values where var_id = {$key} and value = '{$item}')";
    }
    $result = $this->db->query("
      SELECT t1.id, t1.manufacturer_name
      FROM shop_tovar_manufacturer  t1
        INNER JOIN shop_tovar t2 ON t1.id = t2.manufacturer_id
      where t2.active = 1 AND t2.price > 0
        AND t2.id IN (
          select distinct tovar_id from shop_tovar_category where category_id in ({$category})
        )
        {$where}
        " . ($filter['tovar_id'] <> '' ? "AND t2.id IN ({$filter['tovar_id']})" : "") . "
      GROUP BY t1.id, t1.manufacturer_name
      ORDER BY t1.manufacturer_name
    ")->result();
    return $result;
  }
  
  /**
   * Найти максимальную цену на товар в категории
   */
  function getMaxPrice($category_id) {
    $category_id = (int)$category_id;
    $price = $this->db->query("
      SELECT max(price) as price
      FROM shop_tovar
      WHERE active = 1 AND price > 0
        AND id IN (SELECT tovar_id FROM shop_tovar_category WHERE category_id = {$category_id})
    ")->row()->price;
    return $price;
  }
  
  /**
   * Получить список похожих товаров
   */
  function findSimilarProducts($tovar_id) {
    $tovar = $this->Get($tovar_id);
    $filter = array(
      'active'        => 1,
      'category_id'   => $tovar->category[0]->category_id,
      'price >'       => $tovar->price * 0.8,
      'price <'       => $tovar->price * 1.2,
      't1.id <>'      => $tovar_id,
      'with_ostatok'  => true,
      'with_photo'    => true
    );
    // поиск товаров
    $params['tovars'] = $this->tovar_model->Find(array(
      'filter' => $filter,
      'limit'  => 4,
      'sort'   => 'rand()',
      'suffix' => '_list'
    ));
    return $params['tovars'];
  }
  
  /**
   * Получить список товаров в комплект
   */
  function findKitProducts($tovar_id) {
    $tovar = $this->Get($tovar_id);
    $filter = array(
      'active'        => 1,
      'with_ostatok'  => true,
      'with_photo'    => true,
      'kit_for'       => $tovar_id
    );
    // поиск товаров
    $params['tovars'] = $this->tovar_model->Find(array(
      'filter' => $filter,
      'limit'  => 24,
      'sort'   => 'rand()',
      'suffix' => '_list'
    ));
    return $params['tovars'];
  }
  
  /**
   * Получить список типов доставки
   */
  function typeMoveList() {
    return $this->db->query("select * from shop_type_move order by name")->result_array();
  }
  
  /**
   * Получить тип доставки по id
   */
  function typeMoveGet($id) {
    $id = (int)$id;
    return $this->db->query("select * from shop_type_move where id={$id}")->row()->name;
  }
  
  
  /**
   * Получить список типов оплаты
   */
  function typePayList() {
    return $this->db->query("select * from shop_type_pay order by priority")->result_array();
  }
  
  /**
   * Получить тип оплаты по id
   */
  function typePayGet($id, $full = false) {
    $id = (int)$id;
    $row = $this->db->query("select * from shop_type_pay where id={$id}")->row();
    if ($full) {
      return $row;
    }
    return $row->name;
  }
  
  /**
   * Список статусов заказов
   */
  function getStatus($id = '') {
    $id = (int)$id;
    if($id>0) {
      $query = "select name from zakaz_status where id={$id}";
      $res = $this->db->query($query)->row()->name;
    }
    else {
      $query = "select * from zakaz_status order by name";
      $res = $this->db->query($query)->result_array();
    }
    return $res;
  }
  
  /**
   * Получить товар по его коду в 1с
   */
  function GetByCode($code, $field = 'code_1c') {
    $code = trim($code);
    if (!$code) {
      return false;
    }
    $query = $this->db->where($field, $code)->get('shop_tovar');
    $this->rows = $query->num_rows();
    $ret = $query->row();
    $query->free_result();
    return $ret;
  }
  
  /**
   * Получить товар по его id
   */
  function Get($id, $params = []) {
    $id = (int)$id;
    $query = $this->db->where('id', $id)->get('shop_tovar');
    $res = $query->row();
    $query->free_result();
    if (!$res) {
      return false;
    }
    $res->description = htmlspecialchars_decode($res->description);
    $res->old_price = $res->def_price = round($res->price, 2);
    $res->brand = $this->manufacturerGet($res->manufacturer_id);
    $res->manufacturer_name = $res->brand->manufacturer_name;
    // dop params
    $query = $this->db->query("
      SELECT t1.id, t1.name, t1.type, t2.value
      FROM shop_tovar_vars t1
        LEFT JOIN shop_tovar_values t2 ON t1.id=t2.var_id AND t2.tovar_id={$id}
      WHERE t1.category_id is null OR t1.category_id in (
        select category_id from shop_tovar_category where tovar_id={$id}
      )
    ");
    foreach ($query->result() as $item) {
      $res->dop[$item->id] = $item;
    }
    $query->free_result();
    // common dop params
    //$res->count = (int)$res->dop[TOVAR_COUNT_FIELD]->value;
    
    // variants
    if ($res->type == 'variant') {
      $res->variants = $this->db
        ->where('product_id', $id)
        ->order_by('price')
        ->get('shop_product_variants')
        ->result();
      $res->varParams = [];
      foreach ($res->variants as $item) {
        $item->price_format = number_format($item->price, 0, '.', ' ');
        $res->varParams[$item->name] = $item;
      }
      $selected = ($params['variant'] ? $res->varParams[$params['variant']] : current($res->varParams));
      $res->price = $selected->price;
      $res->code  = $selected->vendor_code;
      $res->old_price = $res->def_price = $res->price;
    }
    else {
      $variant = new stdClass();
      $variant->name  = $res->dop[PRODUCT_FIELD_WEIGHT]->value;
      $variant->price = $res->price;
      $res->variants[] = $variant;
      $res->varParams[$variant->name] = $variant;
    }
    
    // discount
    //$res->discount_size = $res->dop[TOVAR_FIELD_ACTION]->discount_size;
    $res->price = round($res->def_price * (1 - $res->discount_size / 100));
    // category
    $query = $this->db->query("
      select t1.category_id, t2.name 
      from shop_tovar_category t1 
        inner join shop_category t2 on t1.category_id=t2.id
      where t1.tovar_id={$id}
    ");
    $res->category = $query->result();
    $res->category_id = current($res->category)->category_id;
    $query->free_result();
    // photo
    $query = $this->db->query("
      SELECT DISTINCT t1.id, t1.big_file, t1.thumb_file, t1.is_main, t1.variant
      FROM shop_tovar_photo t1 
      WHERE t1.tovar_id={$id} AND t1.status = 1
      ORDER BY t1.is_main DESC
    ");
    $res->photo = $query->result();
    $query->free_result();
    foreach ($res->photo as $value) {
      if ($value->variant) {
        $res->varParams[$value->variant]->pic = $value->big_file;
      }
      if ($value->is_main) {
        $res->photo_main = $value;
      }
    }
    //adebug($res->varParams);
    $res->filters = $this->getProductFilters($id);
    foreach ($res->filters as $item) {
      if ($item->id == PRODUCT_FILTER_SIZE) {
        $res->size = $item->value;
      }
    }
    return $res;
  }
  
  /**
   * Фильтры товара
   * @author Alexey
   */
  function getProductFilters($id) {
    $id = (int)$id;
    $query = $this->db->query("
      SELECT sf.id, sf.name, sfv.id as value_id, sfv.value
      FROM shop_filters sf
        INNER JOIN shop_filter_values         sfv ON sf.id = sfv.filter_id
        INNER JOIN shop_product_filter_values pfv ON sfv.id = pfv.value_id
      WHERE pfv.product_id = {$id}
      ORDER BY sf.name, sfv.value
    ");
    $result = $query->result();
    $query->free_result();
    return $result;
  }
  
  /**
   * 
   * @author Alexey
   */
  function setProductFilters($id, $data) {
    $id = (int)$id;
    $this->db->trans_start();
    $this->db->delete('shop_product_filter_values', ['product_id' => $id]);
    foreach ($data as $filter_id => $values) {
      foreach ($values as $value) {
        $this->db->insert('shop_product_filter_values', [
          'product_id'  => $id,
          'filter_id'   => $filter_id,
          'value_id'    => $value
        ]);
      }
    }
    $res = $this->db->trans_complete();
    return $res;
  }
  
  /**
   * Добавление товара
   */
  function Add($params) {
    $valid = array(
      'code'            => '',
      'code_1c'         => '',
      'name'            => 'Название',
      'price'           => '',
      'manufacturer_id' => 'Производитель',
      'description'     => '',
      'active'          => 0
    );
    $insert = array_intersect_key($params, $valid);
    foreach ($valid as $key => $item) {
      if ($item && !$insert[$key]) {
        return "Не определено поле ".$item;
      }
    }
    $insert['translite'] = translite($insert['name']);
    $ret = $this->db->insert('shop_tovar', $insert);
    if (!$ret) {
      return "Ошибка БД!";
    }
    $this->new_id = $this->db->insert_id();
    $this->resetSearchString($this->new_id);
    return;
  }
  
  /**
   * Изменение произвольного свойства товара
   */
  function setParam($params) {
    $params['tovar_id'] = (int)$params['tovar_id'];
    $params['var_id']   = (int)$params['var_id'];
    if (!$params['tovar_id'] || !$params['var_id']) {
      return "Не определены параметры";
    }
    $where = array_intersect_key($params, array('tovar_id' => '', 'var_id'   => ''));
    $query = $this->db->where($where)->get("shop_tovar_values");
    $id = $query->row()->id;
    $query->free_result();
    if ($id) {
      $set = array_intersect_key($params, array('value' => ''));
      $ret = $this->db->update("shop_tovar_values", $set, $where);
    }
    else {
      $ret = $this->db->insert("shop_tovar_values", $params);
    }
    if (!$ret) {
      return "Ошибка БД";
    }
    return;
  }  
  
  /**
   * Изменение информации о товаре
   */
  function Set($id, $set) {
    $id = (int)$id;
    if (!$id)
      return "Товар не найден!";
    $main = array(
      'code_1c'         => '',
      'parent_code'     => '',
      'code'            => '',
      'name'            => '',
      'price'           => '',
      'manufacturer_id' => '',
      'description'     => '',
      'active'          => '',
      'type'            => '',
      'url'             => ''
    );
    $set['price'] = ($set['type'] == 'weight' ? $set['price-w'] : $set['price']);
    $set['active'] = ($set['active']=='true' || $set['active']==1  ?  1  :  0);
    $update = array_intersect_key($set, $main);
    if ($update['name']) {
      $update['translite'] = translite($update['name']);
    }
    $ret = $this->db->update('shop_tovar', $update, array("id" => $id));
    if (!$ret) {
      return "Ошибка БД при изменении общей информации";
    }
    foreach ((array)$set['dop'] as $key => $item) {
      $key = (int)$key;
      $query = $this->db->query("select id from shop_tovar_values where tovar_id={$id} and var_id={$key}");
      $value_id = $query->row()->id;
      $dop = array(
        'tovar_id' => $id,
        'var_id'   => $key,
        'value'    => ($item=='true' ? 1 : $item)
      );
      $query->free_result();
      $value_id
        ? $ret = $this->db->update("shop_tovar_values", $dop, array('id' => $value_id))
        : $ret = $this->db->insert("shop_tovar_values", $dop);
      if (!$ret) {
        echo "Ошибка БД при изменении дополнительных параметров товара.";
        return;
      }
    }
    $this->resetSearchString($id);
    if ($set['type'] == 'variant') {
      $this->db->delete('shop_product_variants', ['product_id' => $id]);
      foreach ($set['variants'] as $key => $item) {
        $this->db->insert('shop_product_variants', [
          'product_id'  => $id,
          'name'        => $item,
          'price'       => $set['prices'][$key],
          'vendor_code' => $set['vendor_code'][$key]
        ]);
      }
      $this->db->query("
        UPDATE shop_tovar SET price = (
          SELECT price
          FROM shop_product_variants
          WHERE product_id = {$id}
          ORDER BY price > 0 DESC, price ASC
          LIMIT 1
        )
        WHERE id = {$id}
      ");
    }
    return;
  }
  
  /**
   * Назначение категорий товара
   */
  function SetCategory($tovar_id, $category) {
    $tovar = $this->Get($tovar_id);
    foreach ($tovar->category as $key => $item) {
      $tovar_category[$item->category_id] = $item->category_id;
    }
    $for_insert = array_diff((array)$category, (array)$tovar_category);
    $for_delete = array_diff((array)$tovar_category, (array)$category);
    
    $this->db->trans_start();
    if (!empty($for_insert)) {
      foreach ($for_insert as $category_id) {
        $this->db->insert("shop_tovar_category", array('tovar_id' => $tovar_id, 'category_id' => $category_id));
      }
    }
    if (!empty($for_delete)) {
      $delete = implode(",", $for_delete);
      $this->db->query("delete from shop_tovar_category where tovar_id={$tovar_id} and category_id in ({$delete})");
    }
    // обновление url
    $tovar = $this->Get($tovar_id);
    $url = $this->getCategoryUrl($tovar->category_id) . $tovar_id . '-' . $tovar->translite;
    $this->Set($tovar_id, ['url' => $url]);
    
    $ret = $this->db->trans_complete();
    if (!$ret) {
      return "Ошибка БД.";
    }
    $this->resetSearchString($id);
    return;
  }
  
  /**
   * Получить url для категории
   * @author Alexey
   */
  function getCategoryUrl($category_id) {
    $url = '';
    while ($i < 100) {
      $category = $this->categoryGet($category_id);
      $url = $category->translite_name . '/' . $url;
      if ($category->parent_id == 0) {
        break;
      }
      $category_id = $category->parent_id;
      $i++;
    }
    $url = "/" . $url;
    return $url;
  }
  
  /**
   * Удаление товара
   */
  function Del($id) {
    $id = (int)$id;
    $this->db->trans_start();
    $this->db->delete("shop_tovar_category", array('tovar_id' => $id));
    $this->db->delete("shop_tovar_photo", array('tovar_id' => $id));
    $this->db->delete("shop_tovar_values", array('tovar_id' => $id));
    $this->db->delete("shop_tovar", array('id' => $id));
    $ret = $this->db->trans_complete();
    if (!$ret) {
      return "Ошибка БД";
    }
    return;
  }
  
  /**
   * Пересобрать поисковую строку для товара
   */
  function resetSearchString($tovar_id, $suffix = '') {
    $tovar_id = (int)$tovar_id;
    $tovar = $this->Get($tovar_id);
    $string = mb_strtolower(
      "{$tovar->category[0]->name} ".
      ($tovar->manufacturer_id<>TOVAR_NA_MANUFACTURER ? $tovar->manufacturer_name: '').
      " {$tovar->name} {$tovar->code} {$tovar->price} {$tovar->parent_code} " . 
      ($suffix <> '' ? implode(" ", $tovar->codes1c) : $tovar->code_1c)
    );
    return $this->db->update("shop_tovar" . $suffix, array('string' => $string), array('id' => $tovar_id));
  }
  
  
  /**
   * Image lib reinit
   */
  function reloadImageLib($config) {
    if ($this->image_lib) {
      $this->image_lib->clear();
      unset($this->image_lib);
    }
    $this->load->library('image_lib', $config); // загружаем библиотеку
    if (!$this->image_lib) {
      $this->image_lib = new CI_Image_lib($config);
    }
  }
  
  ##########################################################################
  ###         TOVAR PHOTOS
  ##########################################################################
  /**
   * Добавление фото
   */
  function photoAdd($params, $file) {
    if (!$file['size']) {
      return "Нулевой размер файла!";
    }
    //adebug($file);adebug($params);
    $ext = getExt($file['name']);
    $filebasis  = md5_file($file['tmp_name']);
    $fileweb    = PHOTO_DIR . 'big/' . $filebasis.".".$ext;
    $filewebth  = PHOTO_DIR . 'thumb/'.$filebasis.".".$ext;
    $filethumba = ROOT . PHOTO_DIR . 'big/' . $filebasis."_thumb.".$ext;
    $filename   = ROOT.$fileweb;
    $filethumb  = ROOT.$filewebth;

    // если файл не существует, либо чексумма не совпадает
    if (!is_file($filename) || !is_file($filethumb) || $filebasis <> md5_file($filename)) {
      if (is_file($filename)) {
        unlink($filename);
      }
      $method = ($file['name'] == $file['tmp_name'] ? "copy" : "rename");
      $res = $method($file['tmp_name'], $filename);
      
      if (!$res) {
        return "Не получилось переместить временный файл.";
      }

      // thumb file
      $config = array();
      $config['image_library']  = 'gd2';  // выбираем библиотеку
      $config['source_image']   = $filename;
      $config['create_thumb']   = TRUE;   // ставим флаг создания эскиза
      $config['maintain_ratio'] = TRUE;   // сохранять пропорции
      $config['height']         = 249;
      $config['width']          = 249;    // и задаем размеры
      
      $this->reloadImageLib($config);
      $res = $this->image_lib->resize(); // и вызываем функцию
      if (!$res) {
        return "Не получилось создать изображение предпросмотра.";
      }
      $res = rename($filethumba, $filethumb);
      if (!$res) {
        return "Не получилось сохранить изображение предпросмотра.";
      }
      chmod($filename,  0755);
      chmod($filethumb, 0755);
      
      if (PHOTO_MARKER) {
        $config = array();
        $config['source_image']     = $filename;
        $config['wm_text']          = config('photo_marker_text');
        $config['wm_type']          = 'text';
        $config['wm_font_path']     = ROOT . '/MISTRAL.TTF';
        $config['wm_font_size']     = '60';
        $config['wm_font_color']    = 'c2c2c2';
        $config['wm_vrt_alignment'] = 'middle';
        $config['wm_hor_alignment'] = 'center';
        /*
        config['wm_type']          = 'overlay';
        $config['wm_overlay_path'] = ROOT . '/images/main/stamp.png';
        */
        $this->reloadImageLib($config);
        $this->image_lib->watermark();
      }
    }
    
    $query = $this->db->query("
      SELECT id, is_main FROM shop_tovar_photo 
      WHERE tovar_id = {$params['id']} AND big_file = '$fileweb'
    ");
    $check = $query->row();
    $query->free_result();
    
    // проверим - если нет основных фоток у товаров, то присваиваем признак
    $query = $this->db->query("
      select id from shop_tovar_photo where is_main=1 and tovar_id={$params['id']} and status = 1
    ");
    $isset_main = $query->row()->id;
    
    if ($params['is_main']) {
      $this->db->query("update shop_tovar_photo set is_main=0 where tovar_id={$params['id']}");
    }
    
    if ($check->id) {
      $res = $this->db->update(
        'shop_tovar_photo', 
        array(
          'is_main' => ((int)$params['is_main'] || (int)$check->is_main) || (int)(!$isset_main),
          'status'  => 1
        ),
        array(
          'tovar_id' => (int)$params['id'],
          'big_file' => $fileweb,
        )
      );
    }
    else {
      $res = $this->db->insert('shop_tovar_photo', array(
        'tovar_id'    => (int)$params['id'],
        'variant'     => (string)$params['variant'],
        'big_file'    => $fileweb,
        'thumb_file'  => $filewebth,
        'comment'     => $params['comment'],
        'is_main'     => ((int)$params['is_main'] || (int)(!$isset_main))
      ));
    }
    if (!$res) {
      return "Ошибка БД.";
    }
    return;
  }
  
  /**
   * Удаление фотки
   */
  function photoDel($id) {
    $photo = $this->photoGet($id);
    @unlink(ROOT.$photo->big_file);
    @unlink(ROOT.$photo->thumb_file);
    if (is_file(ROOT.$photo->avg_file)) {
      @unlink(ROOT.$photo->avg_file);
    }
    $this->db->where('id', $id);
    $res = $this->db->delete('shop_tovar_photo'); 
    return $res;
  }
  
  /**
   * Получение фотки
   */
  function photoGet($id) {
    $res = $this->db->where('id', $id)->get('shop_tovar_photo')->row();
    return $res;
  }
  
  ##########################################################################
  ###         TOVAR MANUFACTURERS
  ##########################################################################

  /**
   * Поиск производителей по имени
   */
  function manufacturerGetByName($name) {
    $id = $this->db->query("select id from shop_tovar_manufacturer where lower(manufacturer_name) like lower('{$name}')")->row()->id;
    if (!$id) {
      $name = str_replace("&#039;", "\'", $name);
      $id = $this->db->query("select id from shop_tovar_manufacturer where lower(manufacturer_name) like lower('{$name}')")->row()->id;
    }
    return $id;
  }

  /**
   * Поиск производителей
   */
  function manufacturerFind($params = array()) {
    $where = 'WHERE true';
    $where .= ($params['pic']       ? " AND pic <> ''"     : "");
    $where .= ($params['main_page'] ? " AND main_page = 1" : "");
    $res = $this->db->query("
      select * 
      from shop_tovar_manufacturer 
      {$where}
      order by manufacturer_name asc
    ")->result();
    return $res;
  }

  /**
   * Поиск коллекций производителей
   */
  function brandCollectionFind() {
    $res = $this->db->query("select * from shop_brand_collection order by id asc")->result();
    return $res;
  }
  
  /**
   * Добавление производителей
   */
  function manufacturerAdd($params) {
    $valid = array('id'=> 'Код', 'manufacturer_name' => 'Название', 'description' => 'Описание');
    $params = array_intersect_key($params, $valid);
    $ret = $this->db->insert('shop_tovar_manufacturer', $params);
    return $ret;
  }
  
  /**
   * Добавление коллекции
   */
  function brandCollectionAdd($params) {
    $valid = array('id'=> 'Код', 'name' => 'Название', 'brand_id' => 'Код производителя');
    $params = array_intersect_key($params, $valid);
    $ret = $this->db->insert('shop_brand_collection', $params);
    return $ret;
  }
  
  /**
   * Удаление производителя
   */
  function manufacturerDel($id) {
    $id = (int)$id;
    $tovar = $this->db->query("select count(*) as cnt from shop_tovar where manufacturer_id={$id}")->row()->cnt;
    if ($tovar) {
      return "Удаление производителя невозможно. Есть связанные товары.";
    }
    $ret = $this->db->delete('shop_tovar_manufacturer', array('id' => $id));
    if (!$ret) {
      return "Ошибка БД";
    }
    return;
  }
  
  /**
   * Получение информации о производителе
   */
  function manufacturerGet($id) {
    $id = (int)$id;
    $query = $this->db->where('id', $id)->get('shop_tovar_manufacturer');
    $info = $query->row();
    $query->free_result();
    return $info;
  }
  
  /**
   * Получение информации о коллекции
   */
  function brandCollectionGet($id) {
    $id = (int)$id;
    $info = $this->db->where('id', $id)->get('shop_brand_collection')->row();
    return $info;
  }
  
  /**
   * Изменение иформации о производителе
   */
  function manufacturerSet($id, $params) {
    $id = (int)$id;
    $valid = array(
      'manufacturer_name' => 'Название',
      'description'       => 'Описание',
      'title'             => 'title',
      'main_page'         => 'На главной'
    );
    if ($params['pic']['size'] > 0) {
      $filename['pic']  = BRANDS_DIR . $id . '.' .getExt($params['pic']['name']);
      $upload['pic']    = move_uploaded_file($params['pic']['tmp_name'], ROOT . $filename['pic']);
    }
    
    $params = array_intersect_key($params, $valid);
    
    if ($upload['pic']) {
      $params['pic'] = $filename['pic'];
    }
    
    $this->db->where('id', $id);
    $ret = $this->db->update('shop_tovar_manufacturer', $params);
    return $ret;
  }
  
  /**
   * Изменение иформации о коллекции
   */
  function brandCollectionSet($id, $params) {
    $id = (int)$id;
    $valid = array('name' => 'Название', 'brand_id' => 'Код производителя');
    $params = array_intersect_key($params, $valid);
    $this->db->where('id', $id);
    $ret = $this->db->update('shop_brand_collection', $params);
    return $ret;
  }
  
  ##########################################################################
  ###         TOVAR CATEGORY
  ##########################################################################
  /**
   * Поиск категорий
   */
  function categoryFind($parent_id, $main_menu = null) {
    $parent_id = (int)$parent_id;
    $parent_id
      ? $where = "t1.parent_id={$parent_id}"
      : $where = "t1.parent_id is null";
    $where .= ($main_menu === null ? '' : " AND t1.main_menu = {$main_menu}");
    $res = $this->db->query(
      "select t1.id, t1.name, t1.level, count(t2.id) as cnt, t1.main_menu, t1.icon_code,
        t1.translite_name, t1.url, t1.pic
      from  shop_category t1 left join 
            shop_category t2 on t1.id=t2.parent_id
      where {$where}
      group by t1.id, t1.name, t1.level
      order by t1.priority asc, t1.id asc, t1.name asc"
    )->result();
    $result = [];
    foreach ($res as $item) {
      $result[$item->id] = $item;
    }
    return $result;
  }
  
  /**
   * вывод всех категорий
   */
  function categoryFindAll() {
    return $this->db->get('shop_category')->result();
  }
  
  /**
   * Получение категории
   */
  function categoryGet($id) {
    $where = [];
    if (is_numeric($id)) {
      $where = ['id' => $id];      
    }
    else {
      $where = ['translite_name' => preg_replace('/[^A-Za-z\-]/', '', $id)];
    }
    $res = $this->db->where($where)->get('shop_category')->row();
    return $res;
  }
  
  /**
   * Добавление категории
   */
  function categoryAdd($parent_id, $params) {
    $parent = $this->categoryGet($parent_id);
    $valid = array(
      'name'      => 'Название', 
      'title'     => 'title',
      'priority'  => 'Приоритет'
    );
    $params = array_intersect_key($params, $valid);
    $prio = (int)$this->db->query("
      SELECT MAX(priority) + 1 as id
      FROM shop_category
    ")->row()->id;
    $params['level']          = (int)$parent->level + 1;
    $params['parent_id']      = ($parent_id ? $parent_id : null);
    $params['priority']       = $prio;
    $params['translite_name'] = strtolower(translite($params['name']));
    $params['title']          = ($params['title'] ? $params['title'] : $params['name']);
    $this->db->trans_start();
    
    $this->db->insert('shop_category', $params);
    
    $id  = $this->db->insert_id();
    $url = $this->getCategoryUrl($id);
    $this->db->update('shop_category', ['url' => $url], ['id' => $id]);
    
    $ret = $this->db->trans_complete();
    return $ret;
  }
  
  /**
   * Изменение иформации о категории
   */
  function categorySet($id, $params, $file = []) {
    $id = (int)$id;
    $valid = array(
      'name'     => 'Название', 
      'priority' => 'Приоритет', 
      'title'    => 'title',
      'products' => 'Товарная категория',
      'text'     => 'Описание'
    );
    $this->db->trans_start();
    $params = array_intersect_key($params, $valid);
    if ($params['name']) {
      $params['translite_name'] = strtolower(translite($params['name']));
    }
    if ($file['size'] > 0) {
      $params['pic'] = CATEGORY_DIR . $id . '.' . getExt($file['name']);
      $filename      = ROOT . $params['pic'];
      move_uploaded_file($file['tmp_name'], $filename);
    }
    $this->db->where('id', $id);
    $this->db->update('shop_category', $params);
    if ($params['name']) {
      $url = $this->getCategoryUrl($id);
      $this->db->update('shop_category', ['url' => $url], ['id' => $id]);
      $old_translite_name = $this->categoryGet($id)->translite_name;
      $this->db->query("
        UPDATE shop_category
        SET url = replace(url, '/{$old_translite_name}/', '/{$params['translite_name']}/')
        WHERE id = {$id}
      ");
    }
    $ret = $this->db->trans_complete();
    return $ret;
  }
  
  /**
   * Удаление категории
   */
  function categoryDel($id) {
    $id = (int)$id;
    $check = $this->db->query("select count(*) as cnt from shop_tovar_category where category_id={$id}")->row()->cnt;
    if ($check) {
      return "Удаление категории невозможно. Есть связанные товары.";
    }
    $check = $this->db->query("select count(*) as cnt from shop_category where parent_id={$id}")->row()->cnt;
    if ($check) {
      return "Удаление категории невозможно. Есть дочерние категории.";
    }
    $ret = $this->db->delete('shop_category', array('id' => $id));
    if (!$ret) {
      return "Ошибка БД";
    }
    return;
  }
  
  ##########################################################################
  ###         ZAKAZ
  ##########################################################################
  /**
   * Поиск заказов
   */
  function zakazFind($params) {
    $filters = array('user_id' => '', 'type_pay_id' => '', 'type_move_id' => '', 'date1' => '', 'date2' => '', 'status' => '');
    $params = array_intersect_key($params, $filters); 
    foreach ($params as $key => $value) {
      switch($key) {
        case 'date1':
          if ($value<>'') {
            $and["date(tm)>='{$value}'"] = null;
          }
        break;
        case 'date2':
          if ($value<>'') {
            $and["date(tm)<='{$value}'"] = null;
          }
        break;
        default:
          if ($value>0) {
            $and[$key] = $value;
          }
      }
    }
    $this->db->select('*, date(tm) as date');
    $this->db->from('zakaz');
    if ($and) {
      $this->db->where($and);
    }
    $this->db->order_by("id", "desc");
    $res = $this->db->get()->result();
    return $res;
  }
  
  /**
   * Загрузка заказа
   */
  function zakazGet($id) {
    $id = (int)$id;
    $this->db->select('*, date(tm)');
    $this->db->from('zakaz');
    $this->db->where('id', $id);
    $res = $this->db->get()->row();
    $res->number = $this->zakazNumber($id);
    return $res;
  }
  
  /**
   * Номер заказа
   */
  function zakazNumber($id) {
    $str = 'Z-3' . str_pad($id, 7, '0', STR_PAD_LEFT);
    return $str;
  }
  
  /**
   * Изменение информации о заказе
   */
  function zakazSet($id, $params) {
    $id = (int)$id;
    $valid = array(
      'type_pay_id'   => '',
      'type_move_id'  => '',
      'status'        => '',
      'count'         => '',
      'summa_tovar'   => '',
      'summa_move'    => '',
      'summa'         => ''
    );
    if ($params['summa_move'] > 0) {
      $zakaz_info = $this->zakazGet($id);
      $params['summa'] = $zakaz_info->summa_tovar + $params['summa_move'];
    }
    $params = array_intersect_key($params, $valid);
    $params['changed'] = 1;
    $ret = $this->db->update('zakaz', $params, array('id' => $id));
    return $ret;
  }
  
  /**
   * Загрузка детализации заказа
   */
  function zakazGetDetail($id, $user_id = 0) {
    $zakaz = $this->zakazGet($id);
    if (!$zakaz->id) {
      return false;
    }
    $ret = array();
    $res = $this->db->where('zakaz_id', $id)->get('zakaz_detail')->result();
    return $res;
  }
  
  /**
   * Отмена заказа
   */
  function zakazCancel($user_id, $zakaz_id) {
    $user_id  = (int)$user_id;
    $zakaz_id = (int)$zakaz_id;
    if (!$user_id || !$zakaz_id)
      return false;
    $update = array(
      'status'  => ZAKAZ_STATUS_NULL, 
      'changed' => 1
    );
    $valid_status = array(ZAKAZ_STATUS_WAIT_PAY, ZAKAZ_STATUS_WAIT_PROCESS);
    $zakaz_info = $this->zakazGet($zakaz_id);
    if (!in_array($zakaz_info->status, $valid_status))
      return false;
    $ret = $this->db->update('zakaz', $update, array('id' => $zakaz_id, 'user_id' => $user_id));
    //mail_admin("Заказ #{$zakaz_id} отменен.");
    return $ret;
  }
  
  /**
   * Удаление заказа
   */
  function zakazDel($id) {
    $id = (int)$id;
    $this->db->trans_start();
    $this->db->query("delete from zakaz_detail where zakaz_id={$id}");
    $ret = $this->db->query("delete from zakaz where id={$id}");
    $this->db->trans_complete();
    //mail_admin("Заказ #{$zakaz_id} удален.");
    return $ret;
  }
  
  /**
   * Изменение детализации заказа
   */
  function zakazItemSet($zakaz_id, $params) {
    $zakaz_id  = (int)$zakaz_id;
    $item_id   = (int)$params['item_id'];
    $tovar_id  = (int)$params['tovar_id'];
    $tovar_cnt = (int)$params['tovar_cnt'];
    $comment   = trim($params['comment']);
    return;
    if (!$zakaz_id) {
      return "Неверные параметры";
    }
    
    if ($tovar_id) {
      $tovar_info = $this->Get($tovar_id);
      if (!$tovar_info->id) {
        return "Товар не найден";
      }
    }
    
    
    $this->db->trans_start();
    
    $where = array(
      'zakaz_id' => $zakaz_id, 
      'tovar_id' => $tovar_id
    );
    
    $zakaz_tovar = $this->db->where($where)->get('zakaz_detail')->row();
    // если надо удалить
    if ($tovar_cnt<=0) {
      $this->db->delete('zakaz_detail', $where);
    }
    // Если товар уже заказывался - обновляем количество и комментарий
    elseif ($zakaz_tovar->id) {
      $update = array(
        'cnt'     => $tovar_cnt,
        'comment' => $comment
      );
      $this->db->update('zakaz_detail', $update, $where);
    }
    // если решили дозаказать еще товаров - вставляем позицию
    else {
      $insert = array(
        'zakaz_id'     => $zakaz_id,
        'tovar_id'     => $tovar_id,
        'cnt'          => $tovar_cnt,
        'name'         => $tovar_info->name,
        'code'         => $tovar_info->code,
        'code_1c'      => $tovar_info->code_1c,
        'manufacturer' => $this->manufacturerGet($tovar_info->manufacturer_id)->manufacturer_name,
        'price'        => $tovar_info->price,
        'comment'      => $comment,
      );
      $this->db->insert('zakaz_detail', $insert);
    }
    // обновим инфу про заказ
    $zakaz_info = $this->zakazGet($zakaz_id);
    $count = $this->db->query("select sum(cnt)         as cnt from zakaz_detail where zakaz_id={$zakaz_id}")->row()->cnt;
    $summa = $this->db->query("select sum(cnt * price) as sum from zakaz_detail where zakaz_id={$zakaz_id}")->row()->sum;
    $update = array(
      'count'       => $count,
      'summa_tovar' => $summa,
      'summa'       => $zakaz_info->summa_move + $summa
    );
    $this->zakazSet($zakaz_id, $update);
    // отправим инфу админу
    //mail_admin("Заказ #{$zakaz_id} был отредактирован.");
    $ret = $this->db->trans_complete();
    return $ret;
  }
  
  
  /**
   * Создание заказа
   */
  function zakazSend($detail, $comments, $contacts) {
    $this->user_id = (int)$_SESSION['user_id'];
    $type_pay = $this->typePayGet($contacts['type_pay_id'], true);
    $insert = $contacts;
    
    $type_pay->prepay > 0
      ? $insert['status'] = ZAKAZ_STATUS_WAIT_PAY
      : $insert['status'] = ZAKAZ_STATUS_WAIT_PROCESS;
    
    $insert['user_id'] = ($this->user_id ? $this->user_id : null);
    
    $summa_move = ($insert['type_move_id']==ZAKAZ_TYPE_MOVE_COURIER ? config('courier_cost') : 0);
    
    $this->db->trans_start();
    
    $this->db->insert('zakaz', $insert);
    $zakaz_id = (int)$this->db->insert_id();
    if (!$zakaz_id) {
      return false;
    }
    
    $itog_count = 0;
    $itog_summa = 0;
    if (empty($detail)) {
      return false;
    }
    foreach ($detail as $tovar_id => $item) {
      foreach ($item as $variant => $count) {
        $tovar_info = $this->Get($tovar_id, ['variant' => $variant]);
        
        $insert = array(
          'zakaz_id'      => $zakaz_id,
          'tovar_id'      => $tovar_id,
          'cnt'           => $count,
          'name'          => $tovar_info->name . ($variant ? ', ' : '') . $variant,
          'code'          => $tovar_info->code,
          'code_1c'       => $tovar_info->code_1c,
          'manufacturer'  => $tovar_info->manufacturer_name,
          'price'         => $tovar_info->price,
          'comment'       => @$comments[encode("{$tovar_id}_{$variant}")]
        );
        $this->db->insert('zakaz_detail', $insert);
        $itog_count += $count;
        $itog_summa += $tovar_info->price * $count;
      }
    }
    
    $delivery_flag = ($itog_summa < config('courier_free_limit'));
    
    $update = array(
      'count'       => $itog_count,
      'summa_tovar' => $itog_summa,
      'summa_move'  => $summa_move * $delivery_flag,
      'summa'       => $itog_summa + $summa_move * $delivery_flag
    );
    $this->db->update('zakaz', $update, array('id'=>$zakaz_id));
    $ret = $this->db->trans_complete();
    
    if ($ret) {
      $_SESSION['zakaz'] = array();
      $this->zakazSendMessage($zakaz_id);
      return $zakaz_id;
    }
    return false;
  }
  
  /**
   * Отправка сообщения после заказа
   */
  function zakazSendMessage($zakaz_id) {
    $this->load->library('email');
    $zakaz = $this->zakazGet($zakaz_id);
    $zakaz->type_move = $this->typeMoveGet($zakaz->type_move_id);
    $zakaz->type_pay  = $this->typePayGet($zakaz->type_pay_id);
    $zakaz->detail = $this->zakazGetDetail($zakaz_id);
    $params['zakaz'] = $zakaz;
    $user = $this->user_model->Get($zakaz->user_id);
    $params['username'] = $user['username'];
    
    // пишем админу
    $admin_message = $this->load->view('/common/zakaz_new_message_admin', $params, true);
    mail_admin(
      "Заказ #{$zakaz_id} оформлен в интернет-магазине " . NAME . $admin_message,
      "Заказ #{$zakaz_id} оформлен в интернет-магазине"
    );
    
    // пишем юзеру
    $message = $this->load->view('/common/zakaz_new_message', $params, true);
    $this->email->clear();
    $this->email->to($user['username']);
    $this->email->from(EMAIL, NAME);
    $this->email->subject('Заказ в интернет-магазине '.NAME);
    $this->email->message($message);
    $this->email->send();
  }
  
  /**
   * Получить значение конфига
   */
  function getZakazConfig($key) {
    return $this->db->where(array('key' => $key))->get('zakaz_config')->row()->value;
  }
  
  /**
   * Изменить значение конфига
   */
  function setZakazConfig($key, $value) {
    return $this->db->update('zakaz_config', array('value' => $value), array('key' => $key));
  }
  
  /**
   * Получить уникальные значения параметра товаров
   */
  function getTovarUniqField($var_id, $filter = array()) {
    $var_id = (int)$var_id;
    $category_id = $this->db->where('id', $var_id)->get('shop_tovar_vars')->row()->category_id;
    $where = "";
    if ($category_id) {
      $where .= "and tovar_id in (select tovar_id from shop_tovar_category where category_id = '{$category_id}')";
    }
    $shop_tovar_where[] = "active = 1";
    foreach ((array)$filter['main'] as $key => $item) {
      if (!$item) {
        continue;
      }
      $shop_tovar_where[] = "{$key} = '{$item}'";
    }
    foreach ((array)$filter['vars'] as $key => $item) {
      $key  = (int)$key;
      $item = trim($item);
      if (!$item) {
        continue;
      }
      $where .= " and tovar_id in (select tovar_id from shop_tovar_values where var_id = {$key} and value = '{$item}')";
    }
    $result = $this->db->query(
     "select distinct value 
      from shop_tovar_values 
      where var_id = {$var_id} {$where}
        and tovar_id in (select id from shop_tovar where " . implode(" AND ", $shop_tovar_where) . ")
      order by value"
    )->result();
    $values = array();
    foreach ($result as $item) {
      $values[] = array(
        'id'   => $item->value,
        'name' => $item->value 
      );
    }
    return $values;
  }
  
  /**
   * Сохранение товара к юзеру в ЛК
   */
  function saveUserTovar($params) {
    $save = array(
      'user_id'   => (int)$params['user_id'],
      'tovar_id'  => (int)$params['tovar_id']
    );
    $isset = $this->db->where($save)->get("user_saved_tovar")->num_rows();
    if (!$isset) {
      $this->db->insert("user_saved_tovar", $save);
    }
    else {
      $this->db->update("user_saved_tovar", array('tm' => 'now()'), $save);
    }
  }
  
  /**
   * Получение сохраненных товаров
   */
  function getUserTovar($user_id) {
    $res = $this->db->where(array('user_id' => $user_id))->get("user_saved_tovar")->result();
    return $res;
  }
  
  /**
   * Получение города
   */
  function getCity($id) {
    return $this->db->where('id', (int)$id)->get('city')->row();
  }
  
  /**
   * Список городов
   */
  function findCity() {
    $res = $this->db->order_by('id')->get('city')->result();
    foreach ($res as $item) {
      $result[$item->id] = $item->name;
    }
    return $result;
  }
  
  /**
   * Очистка каталога
   */
  function clearCatalog() {
    $this->db->trans_start();
    $this->db->query("DELETE FROM shop_tovar_category");
    $this->db->query("DELETE FROM shop_tovar_photo");
    $this->db->query("DELETE FROM shop_tovar_values");
    $this->db->query("DELETE FROM shop_tovar");
    $this->db->trans_complete();
    $file = array_merge(glob(ROOT . PHOTO_DIR . 'big/*'), glob(ROOT . PHOTO_DIR . 'thumb/*'));
    array_map("unlink", (array)$file);
  }
  
    /**
   * Удаление города
   */
  function delCity($id) {
    return $this->db->delete('city', array(
      'id'        => (int)$id,
      'self_city' => 0
    ));
  }

  /**
   * сохранение города
   */
  function setCity($id, $data) {
    $id = (int)$id;
    $this->db->update('city', array(
      'name'          => $data['name'],
      'priority'      => $data['priority'],
      'region'        => $data['region'],
      'post_delivery' => (int)$data['post_delivery'],
      'tel'           => $data['tel'],
    ), array('id' => $id));
    return;
  }

  /**
   * добавление города
   */
  function addCity($data) {
    $this->db->insert('city', array(
      'name'          => $data['name'],
      'priority'      => $data['priority'],
      'region'        => $data['region'],
      'post_delivery' => (int)$data['post_delivery'],
      'tel'           => $data['tel'],
    ));
    return;
  }

  /**
   * Поиск магазинов
   */
  function findShop($filter = array()) {
    $result = $this->db->query("
      SELECT t1.name as city_name, t1.translite as city_translite,
        t2.shop_tel, t2.id, t2.addr, t2.city_id, t2.lat, t2.lon, t2.description, t2.is_shop, t2.name, t2.dgis_firm_id
      FROM city t1
        INNER JOIN shop_addr t2 ON t1.id = t2.city_id
      WHERE true
        " . ($filter['city_id'] ? "AND t2.city_id = " . (int)$filter['city_id'] : "") . "
    ")->result();
    return $result;
  }

  /**
   * Добавление магазина
   */
  function addShop($params) {
    $valid = array(
      'addr'        => '',
      'city_id'     => '',
      'lat'         => '',
      'lon'         => '',
      'description' => ''
    );
    $addr = $this->getCity($params['city_id'])->name . ", " . $params['addr'];
    $coord = getCoord($addr);
    $params['lat'] = $coord->lat;
    $params['lon'] = $coord->lon;
    if (!$coord->lat) {
      return "Невозможно определить координаты";
    }
    $save = array_intersect_key($params, $valid);
    $ret = $this->db->insert('shop_addr', $save);
    if (!$ret) {
      return "Ошибка БД";
    }
  }

  /**
   * Редактирование магазина
   */
  function editShop($id, $params) {
    $valid = array(
      'addr'        => '',
      'name'        => '',
      'city_id'     => '',
      'lat'         => '',
      'lon'         => '',
      'description' => '',
      'shop_tel'    => '',
      'is_shop'     => ''
    );
    $addr = $this->getCity($params['city_id'])->name . ", " . $params['addr'];
    $coord = getCoord($addr);
    $params['lat'] = $coord->lat;
    $params['lon'] = $coord->lon;
    if (!$coord->lat) {
      return "Невозможно определить координаты";
    }
    $save = array_intersect_key($params, $valid);
    $ret = $this->db->update('shop_addr', $save, array('id' => $id));
    if (!$ret) {
      return "Ошибка БД";
    }
  }

  /**
   * Получение магазина
   */
  function getShop($id) {
    return $this->db->where('id', (int)$id)->get('shop_addr')->row();
  }

  /**
   * Удаление магазина
   */
  function delShop($id) {
    return $this->db->delete('shop_addr', array('id' => (int)$id));
  }

}
?>