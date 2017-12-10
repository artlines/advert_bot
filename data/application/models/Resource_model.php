<?php

class Resource_model extends CI_Model {

  function __construct() {
    parent::__construct();
  }
  /**
   * Получить дерево ресурсов
   * После обновления каталога
   * дополнить: дерево каталога
   * формируется из object_id
   */
  function loadResourceTree(){
      $resources = $this->db->query("
        SELECT max(id) as id, parent_id, object_id, title, h1, url, priority, type, name
        FROM resource
        WHERE active=1
        GROUP BY parent_id, object_id, title, h1, url, priority, type, name
        ORDER BY priority DESC"
      )->result();

    $listById = [];
    $listByParentId = [];

    foreach ($resources as $item) {
      //каталог по object_id
      $id = $item->object_id ?: $item->id;
      $listById[$id] = $item;
      $listByParentId[$item->parent_id][$id] = $item;
    }

    $tree = [];
    foreach ($listByParentId[0] as $key => $item) {
      $id = $item->object_id ?: $item->id;
      $tree[$id] = $item;
      $tree[$id]->children = $listByParentId[$id];

      foreach ((array)$tree[$id]->children as $ckey => $citem) {
        $i = $citem->object_id ?: $citem->id;
        $tree[$id]->children[$ckey]->children = $listByParentId[$i];
        if($tree[$id]->children[$ckey]->children){
          foreach ($tree[$id]->children[$ckey]->children as $kt => $it){
            $tree[$id]->children[$ckey]->children[$kt]->children = $listByParentId[$it->object_id];
            if($tree[$id]->children[$ckey]->children[$kt]->children){
              foreach ($tree[$id]->children[$ckey]->children[$kt]->children as $k => $i){
                $tree[$id]->children[$ckey]->children[$kt]->children[$k]->children = $listByParentId[$i->object_id];

                if($tree[$id]->children[$ckey]->children[$kt]->children[$k]->children){
                  foreach ($tree[$id]->children[$ckey]->children[$kt]->children[$k]->children as $prod => $item_prod){
                    $tree[$id]->children[$ckey]->children[$kt]->children[$k]->children[$prod]->children = $listByParentId[$item_prod->object_id];
                  }
                }
              }
            }
          }
        }
      }
    }
    return $tree;
  }
  /**
   * Получить ресурс по id || object_id || url
   */
  function getResourceOne($data){
    $resource = $this->db->query("
        SELECT *
        FROM resource
        WHERE {$data['type']} = '{$data['value']}'"
    )->row();
    if($resource->id){
      $resource->content = html_entity_decode($resource->content);
      $resource->content_top = html_entity_decode($resource->content_top);
      $resource->content_bottom = html_entity_decode($resource->content_bottom);
      $resource->tv = $this->db->query("
        SELECT tt.type, tv.value, tp.name, tp.id, tp.type_resource, tp.template_id
        FROM tv_values tv
        LEFT JOIN tv_parameters tp ON (tv.tv_id = tp.id)
        LEFT JOIN tv_types tt ON (tp.type_id = tt.id)
        WHERE tv.resource_id = {$resource->id}"
      )->result_array();
    }

    return $resource;
  }

  /**
   * Получить родителя ресурса
   */
  function getParentById($data){
    $parent = $this->db->query("
        SELECT parent_id
        FROM resource
        WHERE object_id = '{$data['object_id']}'"
    )->row();

    return $this->db->query("
        SELECT object_id,name,title,url
        FROM resource
        WHERE object_id = '{$parent->parent_id}'"
    )->row();

  }

  /**
   * Получить типы ресурсов
   */
  function getResourceTypes(){
    return $this->db->select('type')->distinct()->from('resource')->get()->result();
  }

  /**
 * Получить модули ресурсов
 */
  function getResourceModules(){
    return $this->db->select(['id', 'name'])->from('razdel_modules')->get()->result();
  }

  /**
   * Получить ресурсы по параметрам
   */
  function getResources(){
    return $this->db->select(['id', 'name'])->from('resource')->where('url in ("main_page", "shadow", "news", "articles", "selections", "tags", "filters", "virtual")')->get()->result();
  }

  /**
   * Получить tv-параметры
   */
  function getTv($tv = []){

    $post = $this->input->post();
    $id = $tv['post']['tv_id'];
    $tr = $ti = $res = [];
    $type = $post['type'] ?: $post['type_resource'];
    $template_id = $tv['template_id'] ?: $post['template_id'];
    $type_resource = $tv['type_resource'] ?: $type;

    if($id){
      $this->db->reset_query();
      return $this->db
        ->select(['tp.id', 'tp.name', 'tp.alias', 'tp.description', 'tp.template_id', 'tp.type_resource', 'tt.type'])
        ->join('tv_types tt', 'tt.id = tp.type_id', 'left')
        ->join('razdel_templates rt', 'tt.id = tp.template_id', 'left')
        ->from('tv_parameters tp')
        ->where('tp.id', $id)
        ->get()
        ->row();
    }

    if($type_resource){
      $this->db->reset_query();
      $tr = $this->db
        ->select(['tp.id', 'tp.name', 'tt.type'])
        ->join('tv_types tt', 'tt.id = tp.type_id', 'left')
        ->from('tv_parameters tp')
        ->where('type_resource', $type_resource)
        ->get()
        ->result_array();
    }

    if($template_id){
      $this->db->reset_query();
      $ti = $this->db
        ->select(['tp.id', 'tp.name', 'tt.type'])
        ->join('tv_types tt', 'tt.id = tp.type_id', 'left')
        ->from('tv_parameters tp')
        ->where('template_id', $template_id)
        ->get()
        ->result_array();
    }

    $result = array_merge($tr, $ti);
    if ($result) {
      foreach ($result as $field) {
        $res[$field['id']] = '';
      }
      $result['fields'] = $res;
    }else{
      $this->db->reset_query();
      return $this->db->select(['id', 'name'])->from('tv_parameters')->get()->result();
    }

    return $result;
  }

  /**
   * Получить типы tv-параметров
   */
  function getTvTypes(){
    return $this->db->get('tv_types')->result();
  }

  /**
   * Вставить/обновить tv-параметры
   */
  function setTv($params){
    $data['table'] = 'tv_parameters';
    $fields = $this->getFields($data);
    $type = $params['post']['type_resource'];
    $template = $params['post']['template_id'];
    if($type){
      $data['type_resource'] = $type;
    }
    if($template){
      $data['template_id'] = $template;
    }

    if(count($data) > 0){
      $insert_tv = array_intersect_key($params['post'], $fields);
      if($insert_tv['id']) {
        $id = $insert_tv['id'];
        unset($insert_tv['id']);
        $this->db->update('tv_parameters', $insert_tv, ['id' => $id]);
      }else{
        $this->db->insert('tv_parameters', $insert_tv);
        $new_id = $this->db->insert_id();
        $id = '';
      }
      $id = $new_id ?: $id;
      return $id;
    }
  }
  /**
   * Вставить/обновить значения tv-параметров
   */
  function setTvValues($params){
    $data = [];
    $type = $params['post']['type_resource'] ?:$params['post']['type'];
    $template = $params['post']['tv_template_id'] ?: $params['post']['template_id'];
    if($type){
    $data['type_resource'] = $type;
    }
    if($template){
      $data['template_id'] = $template;
    }

    if(count($data) > 0){
      $fields_tv = $this->getTv($data)['fields'];
      $insert_tv = array_intersect_key($params['post'], $fields_tv);
      foreach ($insert_tv as $key => $item) {
        if($params['id']){
          $this->db->reset_query();
          $tv = $this->db->query("select id from tv_values where resource_id = {$params["id"]} AND tv_id = {$key}")->row();
          if(!$tv->id){
            $this->db->insert('tv_values', ['value' => $item, 'resource_id' => $params['id'], 'tv_id' => $key]);
          }else{
            $this->db->update('tv_values', ['value' => $item], ['resource_id' => $params['id'], 'tv_id' => $key]);
          }
        }elseif($params['new_id']){
          $this->db->insert('tv_values', ['value' => $item, 'resource_id' => $params['new_id'], 'tv_id' => $key]);
        }
      }
    }
    return;
  }

  /**
   * Вставить/обновить url
   */
  function setUrl($params){
    $alias = $params['alias'] ?: $this->getTranslite($params['name']);

    switch ($params['type']){
      case 'sales':
      case 'product':
      case 'catalog':
      case 'filter':
      case 'shadow':
        return;
      break;

      case 'news':
      case 'articles':
        $url = $params['type'].'/'.$alias;
        break;

      case 'selection':
        $url = 'catalog/'.$params['type'].'/'.$alias.'/';
        break;

      case 'virtual': //по списку id категорий
        $categories = implode('_', explode(',', $params['categories']));
        $url = 'catalog/?categories='.$categories;
        break;

      case 'static':
      case 'tag':
        $url = $alias;
        break;

      default:
        return;
        break;
    }

    return $url;
  }

  /**
   * Получить транслит
   */
  function getTranslite($name){
    $result = $this->db->query("select text_translite('{$name}') as translite")->row();
    return $result->translite;
  }

  /**
   * Получить поля таблицы
   * @return array массив с ключами-полями
   * для последующего array_intersect_key из post
   */
  function getFields($data){
    $result = [];
    $fields = $this->db->list_fields($data['table']);

    foreach ($fields as $field){
      $result[$field] = '';
    }
    return $result;
  }
  
  function resourceEdit($params){
    $fields = $this->getFields($params);
    $insert = array_intersect_key($params['post'], $fields);
    $insert['active'] = $insert['active'] == 'true' ? 1 : 0;

    //check alias и формирование url
    if(!$insert['url']){
      $insert['categories'] = $params['post'][4];
      $insert['url'] = str_replace('/?', '?', $this->setUrl($insert));
      unset($insert['categories']);
    }else{
      //слеш на конце url для фильтров
      $insert['url'] = str_replace('/?', '?', $insert['url']);
    }

    // ресурсы каталога обновляются из внешних источников
    if($params['post']['object_id'] > 0){
      $type = 'object_id';
      $value = $params['post']['object_id'];
    }else{
      $type = 'id';
      $value = $params['post']['id'];
    }
    $resource = $this->db->where($type, $value)->get('resource')->row();
    $resource_by_url = $this->db->where('url', $insert['url'])->order_by('id', 'DESC')->get('resource')->row();
    $id = $insert['id'];
    unset($insert['id']);
    unset($insert['object_id']);
    date_default_timezone_set('Asia/Yekaterinburg');

    //вставка записи или обновление
    if($resource_by_url->id) {
      $insert['tm_upd'] = date("Y-m-d H:i:s");
      $this->db->update('resource', $insert, ['id' => $resource_by_url->id]);
    }elseif($resource->id){
      $insert['tm_upd'] = date("Y-m-d H:i:s");
      $this->db->update('resource', $insert, [$type => $value]);
    }else{
      $insert['tm_add'] = date("Y-m-d H:i:s");
      $this->db->insert('resource', $insert);
      $id = '';
      $new_id = $this->db->insert_id();
    }

    //обработка tv-параметров, сохранение отдельно
    $params['id'] = $id;
    $params['new_id'] = $new_id;
    $this->setTvValues($params);

    $id = $new_id ?: $id;
    return $id;
  }

  /**
   * Удалить ресурс
   */
  function resourceDelete($params){
    return $this->db->delete('resource', ['id' => $params['post']['id']]);
  }

  /**
   * Удалить tv
   */
  function tvDelete($params){
    return $this->db->delete('tv_parameters', ['id' => $params['post']['id']]);
  }

  /**
   * Открепить tv-параметр от шаблона
   */
  function cancelTv($params){
    return $this->db->update('tv_parameters', ['template_id' => ''], ['id' => $params['post']['tv_id'], 'template_id' => $params['post']['template_id']]);
  }

  /**
   * Найти ресурс
   */
  function search($val){
    return $this->db->query("
      SELECT max(id) as id, url, object_id, name, type
      FROM resource
      WHERE 
        name LIKE '%{$val}%' OR
        title LIKE '%{$val}%' OR
        h1 LIKE '%{$val}%' OR
        url LIKE '%{$val}%' OR
        alias LIKE '%{$val}%'
      GROUP BY url, object_id, name, type
      ORDER BY id"
    )->result();
  }

  /**
   * Получить шаблоны страниц
   */
  function getTemplates($template){
    $this->db->reset_query();
    $get = $this->db->select(['id', 'name', 'file'])->from('razdel_templates');
    if($template['where']) $get->where('category', $template['where']);
    if($template['or_where']) $get->or_where('category', $template['or_where']);
    if($template['not_in']) $get->where_not_in('category', $template['not_in']);

    return $get->get()->result();
  }

  /**
   * Получить шаблон по id
   */
  function getTemplateOne($id){
    $template =  $this->db->where('id', $id)->get('razdel_templates')->row();
    if($template->id){
      $template->tv = $this->db->query("
        SELECT alias, name, id, template_id
        FROM tv_parameters
        WHERE template_id = {$template->id}"
      )->result_array();
    }
    return $template;

  }

  /**
   * Отредактировать шаблон по id
   */
  function templateEdit($params){
    $templates_root = ['Базовые', 'Чанк'];
    $id = $params['post']['id'];
    $insert['name'] = $params['post']['name'];
    $insert['description'] = $params['post']['description'];
    $insert['file'] = $params['post']['file'];
    $insert['category'] = $params['post']['category'];
    $code = $params['post']['code'];
    date_default_timezone_set('Asia/Yekaterinburg');
    $file = explode('/', $insert['file']);
    array_pop($file);
    $dir = implode('/', $file);

    if($id){
      $insert['tm_upd'] = date("Y-m-d H:i:s");
      $this->db->update('razdel_templates', $insert, ['id' => $id]);
    }else{
      $this->db->insert('razdel_templates', $insert);
      $id = $this->db->insert_id();
    }

    //сохраним файл шаблона или создадим новый
    if (in_array($insert['category'], $templates_root)){
      chmod(APP_ROOT.'/views/'.$dir,0775);
      file_put_contents(APP_ROOT.'/views/'.$insert['file'], $code);
    }else{
      chmod(MODULES_ROOT.'/'.$dir,0775);
      file_put_contents(MODULES_ROOT.'/'.$insert['file'], $code);
    }

    return $id;
  }



  /**
   * Обновить список категорий при импорте
   */
  function updateCategoryResource(){
    $data = [];
    $shop_category = $this->db->query('
      SELECT concat(id, COALESCE(summand,"")) as id, parent_id, url, name, active, title, description, keywords, befor_text
      FROM shop_category
    ')->result();
    $resource_category = $this->db->query('
      SELECT url
      FROM resource where type="catalog"
    ')->result();

    foreach ($resource_category as $cat) {
      $data['categories'][] = $cat->url;
    }

    foreach ($shop_category as $category) {
      $category->parent_id = $category->parent_id ?: 600;
      if($category->parent_id <= 5){
        $category->parent_id .= $category->parent_id.'0000000';
      }
      //parent_id должен быть уникален в пределах таблицы
      if(in_array($category->url, $data['categories'])){
        $this->db->update('resource',
          [
            'object_id' => $category->id,
            'parent_id' => $category->parent_id,
          ],
          ['url' => $category->url]);
        $data['update'][] = $category->id;
      }else{
        $this->db->insert('resource',
          [
            'name' => $category->name,
            'active' => $category->active,
            'object_id' => $category->id,
            'parent_id' => $category->parent_id,
            'url' => $category->url,
            'title' => $category->title,
            'content' => $category->description,
            'keywords' => $category->keywords,
            'content_top' => $category->befor_text,
            'type' => 'catalog',
            'module_id' => 5,
            'template_id' => 2,
          ]
        );
        $data['insert'][] = $this->db->insert_id();
      }
    }

    return $data;
  }


  /**
   * Обновить список Товаров при импорте
   */
  function updateProductResource(){
    $data = [];
    $shop_product = $this->db->query('
      SELECT id, category_id, name, description, tm_add, active
      FROM shop_tovar_list
    ')->result();
    $resource_product = $this->db->query('
      SELECT url
      FROM resource WHERE type="product"
    ')->result();

    foreach ($resource_product as $cat) {
      $data['products'][] = $cat->url;
    }

    foreach ($shop_product as $product) {
      $product->parent_id = $product->category_id;
      $product->url = $this->tovar_model->findGoodUrl($product->id);

      if(in_array($product->url, $data['products'])){
        $this->db->update('resource',
          [
            'object_id' => $product->id,
            'parent_id' => $product->parent_id,
          ],
          ['url' => $product->url]);
        $data['update'][] = $product->id;
      }else{
        $this->db->insert('resource',
          [
            'name' => $product->name,
            'active' => $product->active,
            'object_id' => $product->id,
            'parent_id' => $product->parent_id,
            'url' => $product->url,
            'content' => $product->description,
            'tm_add' => $product->tm_add,
            'type' => 'product',
            'module_id' => 5,
            'template_id' => 2,
          ]
        );
        $data['insert'][] = $this->db->insert_id();
      }
    }

    return $data;
  }

}
