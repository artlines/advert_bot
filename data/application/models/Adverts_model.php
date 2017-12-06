<?php

/**
 * Created by PhpStorm.
 * User: alexe
 * Date: 29.11.2017
 * Time: 22:14
 * @property CI_DB_query_builder $db
 * @property Telegraph $telegraph
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
    return $this->db->where(['id' => $id])->get('advert')->row();
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
      return;
    }
    $this->load->library('telegraph');
    // снять с публикации - нет такого функционала у telegraph
    if (!$active) {

      return;
    }

    // make content
    $images = [
      'https://advert.artline.me/ad-images/AgADAgAD36gxGzOs8EhTvnFUQOsCoP_oAw4ABIx3wQocFJ_1Xj8AAgI.jpg',
      'https://advert.artline.me/ad-images/AgADAgADl6gxGzn12EgSOsWBTWpoONrwAw4ABMtFAzCSYQZN5ScAAgI.jpg'
    ];
    $img_string = '';
    foreach ($images as $image) {
      $img_string .= ', {"tag": "img", "attrs":["src": "' . $image . '"]}';
    }
    $content = '[{"tag":"p","children":["' . $params['content'] . '"]} ' . $img_string . ']';

    // edit
    if ($info->tg_path) {
      $items = $this->telegraph->editPage([
        'access_token'  => Telegraph::ACCESS_TOKEN,
        'path'          => $info->tg_path,
        'title'         => $params['title'],
        'content'       => '<img src="https://advert.artline.me/ad-images/AgADAgAD36gxGzOs8EhTvnFUQOsCoP_oAw4ABIx3wQocFJ_1Xj8AAgI.jpg" />'//'[{"tag":"p","children":["' . $params['content'] . '"]}, {"tag": }]'
      ]);
      print_r($items);
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