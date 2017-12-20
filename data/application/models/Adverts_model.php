<?php

/**
 * Created by PhpStorm.
 * User: alexe
 * Date: 29.11.2017
 * Time: 22:14
 * @property CI_DB_query_builder $db
 * @property Telegraph $telegraph
 * @property MX_Loader $load
 * @property MX_Config $config
 */
class Adverts_model extends CI_Model
{


  function __construct() {
    parent::__construct();
  }

  /**
   * Поиск
   * @author Alexey
   */
  function find($params) {
    $where = [
      'deleted' => 0
    ];
    if ($params['limit']) {
      $this->db->limit($params['limit']);
    }
    if ($params['offset']) {
      $this->db->offset($params['offset']);
    }
    if ($params['search']) {
      $where['title LIKE'] = "%{$params['search']}%";
    }
    if ($params['active']) {
      $where['active'] = (int)$params['active'];
    }
    if ($params['category_id']) {
      $where['category_id'] = $params['category_id'];
    }
    if ($params['region_id']) {
      $where['region_id'] = $params['region_id'];
    }

    return $this->db
      ->where($where)
      ->order_by('tm DESC')
      ->get('advert')
      ->result();
  }

  /**
   * Количество
   * @author Alexey
   */
  function count() {
    return $this->db->where(['deleted' => 0])->count_all_results('advert');
  }

  /**
   * Получить юзера
   * @author Alexey
   */
  function get($id) {
    $id = (int)$id;
    $result = $this->db->where(['id' => $id])->get('advert')->row();
    $images = $this->db->where(['advert_id' => $id])->get('advert_file')->result();
    foreach ($images as $image) {
      $file = basename($image->link);
      $result->images[] = '/ad-images/' . $file;
    }
    return $result;
  }

  /**
   * Изменить
   * @author Alexey
   */
  function set($id, $params) {
    $id = (int)$id;
    $info = $this->get($id);
    $active = ($params['active'] == 'on');
    $this->db->update('advert', [
      'category_id' => (int)$params['category_id'],
      'region_id'   => (int)$params['region_id'],
      'city_id'     => (int)$params['city_id'],
      'title'       => $params['title'],
      'content'     => $params['content'],
      'active'      => $active
    ], ['id' => $id]);
    // неактивное объявление - изменение данных
    if ($info->active == $active && $active == 0) {
      //return;
    }
    // снять с публикации - нет такого функционала у telegraph
    if (!$active) {
      //return;
    }

    // make content
    $content = '[{"tag":"p","children":["' . $params['content'] . '"]},';

    foreach ($info->images as $key => $image) {
      $content .= '{"tag":"figure","children":[
        {"tag":"img","attrs":{"src":"' . $this->config->item('base_url') . $image . '"}},
        {"tag":"figcaption","children":["Изображение ' . ($key + 1) . '"]}
      ]},';
    }
    $content .= '{"tag":"p","children":[{"tag":"br"}]}]';

    $this->load->library('telegraph');

    // edit
    if ($info->tg_path) {
      $items = $this->telegraph->editPage([
        'access_token'  => Telegraph::ACCESS_TOKEN,
        'path'          => $info->tg_path,
        'title'         => $params['title'],
        'content'       => $content
      ]);
      return;
    }

    // add
    $items = $this->telegraph->createPage([
      'access_token'  => Telegraph::ACCESS_TOKEN,
      'title'         => $params['title'],
      'content'       => $content
    ]);

    $this->db->update('advert', [
      'tg_path' => $items->result->path,
      'tg_url'  => $items->result->url
    ], ['id' => $id]);
  }
}