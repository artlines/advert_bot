<?php

class Bot_model extends CI_Model
{

  function __construct()
  {
    parent::__construct();
  }

  public function changeUserData($data)
  {
    $previous = $this->db->where('user_id', $data['user_id'])->get('user_state')->row();

    if ($previous) {
      $this->db->update(
        'user_state',
        [
          'previous_action' => $data['previous_action'] ?: $previous->previous_action,
          'category_id' => $data['category_id'] ?: $previous->category_id,
          'region_id' => $data['region_id'] ?: $previous->region_id,
          'user_type' => $data['user_type'] ?: $previous->user_type,
          'advert_id' => $data['advert_id'] ?: $previous->advert_id
        ],
        ['user_id' => $data['user_id']]);
    }else {
      $this->db->insert('user_state', [
        'previous_action' => $data['previous_action'] ?: '',
        'user_id' => $data['user_id'],
      ]);
    }

    $current = $this->db->where('user_id', $data['user_id'])->get('user_state')->row();
    $current->previous_action = $previous->previous_action;

    return $current;
  }

  public function getRegion($id)
  {
    return $this->db->where('id', $id)->get('region')->row();
  }

  public function getRegionLike($like)
  {
    return $this->db
      ->like('name', $like, 'both')
      ->or_like('alias', $like, 'both')
      ->get('region')
      ->row();
  }

  public function getCategory($id)
  {
    return $this->db->where('id', $id)->get('category')->row();
  }

  public function getRegions()
  {
    return $this->db->get('region')->result();
  }

  public function getCategories()
  {
    return $this->db->get('category')->result();
  }

  public function editAdvertText($data, $advert_id)
  {
    if($advert_id){
      $advert = $this->db->where('id', $advert_id)->get('advert')->row();

      return $this->db->update('advert',
        [
          'title' => $data['title'] ?: $advert->title,
          'content' => $data['content'] ?: $advert->content,
        ],
        ['id' => $advert_id]
      );
    }else{
      $this->db->insert('advert', $data);
      $d = [
        'user_id' => $data['user_id'],
        'advert_id' => $this->db->insert_id(),
      ];
      return $this->changeUserData($d);
    }
  }

  public function getAdvertText($id)
  {
    return $this->db->where('id', $id)->get('advert')->row();
  }

  public function getAdvertFiles($options)
  {
    //при выдаче добавить условия $this->db->where для типов файлов и т.д.
    return $this->db->where('advert_id', $options->advert_id)->get('advert_file')->result();
  }

  public function setAdvertFiles($file)
  {
    return $this->db->insert('advert_file', $file);
  }

  public function cleanUserState($id)
  {
    return $this->db->update('user_state',
      [
        'category_id' => '',
        'region_id' => '',
        'advert_id' => '',
        'user_type' => '',
        'previous_action' => '',
      ],
      ['user_id' => $id]);
  }
}

?>
