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
          'previous_action' => $data['previous_action'],
          'category_id' => $data['category_id'] ?: $previous->category_id,
          'region_id' => $data['region_id'] ?: $previous->region_id,
          'user_type' => $data['user_type'] ?: $previous->user_type,
          'advert_id' => $data['advert_id'] ?: $previous->advert_id
        ],
        ['user_id' => $data['user_id']]);
    } else {
      $this->db->insert('user_state', $data);
      //вставить юзера, указать поля
      //$this->db->insert('user', $data);
    }

    $current = $this->db->where('user_id', $data['user_id'])->get('user_state')->row();
    $current->previous_action = $previous->previous_action;

    return $current;
  }

  public function getRegions()
  {
    return $this->db->get('region')->result();
  }

  public function getCategories()
  {
    return $this->db->get('category')->result();
  }

  public function getRegionsById($ids)
  {
    $regions = $this->db->query("
      SELECT id, name 
      FROM region
      WHERE id IN ('{$ids}')
    ")->result();

    foreach ($regions as $region) {
      $regions['list'][] = $region->name;
      $regions['ids'][] = $region->id;
    }
    $regions['list'] = implode(',', $regions['list']);
    $regions['ids'] = implode(',', $regions['ids']);

    return $regions;

  }
  public function getCategoriesById($ids)
  {
    $categories = $this->db->query("
      SELECT id, name 
      FROM category
      WHERE id IN ('{$ids}')
    ")->result();

    foreach ($categories as $category) {
      $categories['list'][] = $category->name;
      $categories['ids'][] = $category->id;
    }
    $categories['list'] = implode(',', $categories['list']);
    $categories['ids'] = implode(',', $categories['ids']);

    return $categories;

  }

  public function getRegionsByName($data)
  {
    $names = explode(',', $data);
    foreach ($names as $name) {
      $name = trim($name);
      $regions[] = $this->db->query("
        SELECT id, name 
        FROM region
        WHERE name LIKE '%{$name}%'
          OR alias LIKE '%{$name}%'
          AND is_active = 1
      ")->row();
    }

    foreach ($regions as $region) {
      $regions['list'][] = $region->name;
      $regions['ids'][] = $region->id;
    }
    $regions['list'] = implode(',', $regions['list']);
    $regions['ids'] = implode(',', $regions['ids']);

    return $regions;
  }

  public function getCategoriesByName($data)
  {
    $names = explode(',', $data);
    foreach ($names as $name) {
      $name = trim($name);
      $categories[] = $this->db->query("
        SELECT id, parent_id, name 
        FROM category
        WHERE name LIKE '%{$name}%'
          AND is_active = 1
      ")->row();
    }

    foreach ($categories as $category) {
      $categories['list'][] = $category->name;
      $categories['ids'][] = $category->id;
    }
    $categories['list'] = implode(',', $categories['list']);
    $categories['ids'] = implode(',', $categories['ids']);

    return $categories;
  }

  public function editAdvertText($data, $advert_id)
  {
    if($advert_id){
      $advert = $this->db->where('id', $advert_id)->get('advert')->row();
      file_put_contents(LOG, print_r($advert, 1));

      return $this->db->update('advert',
        [
          'title' => $data['title'] ?: $advert->title,
          'content' => $data['content'] ?: $advert->content,
        ],
        ['id' => $advert_id]
      );
    }else{
      $this->db->insert('advert', $data);
      $data = [
        'user_id' => $data['user_id'],
        'advert_id' => $this->db->insert_id(),
      ];
      return $this->changeUserData($data);
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
    return $this->db->delete('user_state', ['user_id' => $id]);
  }
}

?>
