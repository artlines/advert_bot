<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 03.12.2017
 * Time: 22:24
 * http://telegra.ph/api
 *  createAccount
    createPage
    editAccountInfo
    editPage
    getAccountInfo
    getPage
    getPageList
    getViews
    revokeAccessToken
 *              [access_token] => b21dabb8f5f8807baf8f67e217de685c3dafd0b2293d90ea70db73b8f42e
[auth_url] => https://edit.telegra.ph/auth/FWfPWsqqycHB92WJ9TXnChC5D5IHFwYlqb8nEyLaIs
 */
class Telegraph
{
  const API_URL = 'https://api.telegra.ph/';
  const ACCESS_TOKEN = 'b21dabb8f5f8807baf8f67e217de685c3dafd0b2293d90ea70db73b8f42e';

  /**
   * Магия методов
   * @author Alexey
   */
  function __call($name, $args) {
    return $this->request($name, $args[0]);
  }

  /**
   * Запрос к API
   * @author Alexey
   */
  protected function request($method, $params) {
    $url = self::API_URL . $method . ($params['path'] ? '/' . $params['path'] . '/' : '') . '?' . http_build_query($params);
    $result = file_get_contents($url);
    $result = json_decode($result);
    if (!$result->ok) {
      throw new Exception($result->error);
    }
    return $result;
  }
}