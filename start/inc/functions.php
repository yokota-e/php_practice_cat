<?php
// セッションの開始
session_start();

// DB接続情報
define('DB_HOST', 'localhost');
define('DB_USER', 'catcafeuser');
define('DB_PASS', 'password');
define('DB_NAME', 'cat_cafe');

// データベース接続用の関数
function db_connect()
{
  try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    // エラーモードを例外に設定
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // フェッチモードを連想配列形式に設定
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $db;
  } catch (PDOException $e) {
    exit('DB接続エラー: ' . $e->getMessage());
  }
}

// XSS対策用のエスケープ関数
function h($string)
{
  if (is_null($string)) {
    return '';
  }
  return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// 日付のフォーマット用関数
function format_date($datetime, $type)
{
  $format_types = [
    1 => 'Y年m月d日 H:i:s',
    2 => 'Y年m月d日',
    3 => 'Y/n/j',
    4 => 'Y/m/d H:i',
  ];
  return date($format_types[$type], strtotime($datetime));
}

// 組み合わせIDからメニュー名とオプション名を返す
function get_menuname()
{
  $menu_name_array = array();
  try {
    //組み合わせテーブルから全レコードを取得
    $db = db_connect();
    $sql = 'SELECT menu_options.id,menus.name AS menu_name,menus.price,options.name AS option_name FROM menu_options INNER JOIN menus ON menu_options.menus_id = menus.id INNER JOIN options ON menu_options.options_id = options.id';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
      $menu_name_array[$row['id']] = $row['menu_name'];
    }
    return $menu_name_array;
  } catch (PDOException $e) {
    exit('エラー: ' . $e->getMessage());
  }
}

// 組み合わせIDからオプション名を返す
function get_option()
{
  $option_name_array = array();
  try {
    //組み合わせテーブルから全レコードを取得
    $db = db_connect();
    $sql = 'SELECT menu_options.id,menus.name AS menu_name,menus.price,options.name AS option_name FROM menu_options INNER JOIN menus ON menu_options.menus_id = menus.id INNER JOIN options ON menu_options.options_id = options.id';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
      $option_name_array[$row['id']] = $row['option_name'];
    }
    return $option_name_array;
  } catch (PDOException $e) {
    exit('エラー: ' . $e->getMessage());
  }
}
// 組み合わせIDからprice_arrayを返す
function get_price()
{
  $price_array = array();
  try {
    //組み合わせテーブルから全レコードを取得
    $db = db_connect();
    $sql = 'SELECT menu_options.id,menus.name AS menu_name,menus.price,options.name AS option_name FROM menu_options INNER JOIN menus ON menu_options.menus_id = menus.id INNER JOIN options ON menu_options.options_id = options.id';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
      $price_array[$row['id']] = $row['price'];
    }
    return $price_array;
  } catch (PDOException $e) {
    exit('エラー: ' . $e->getMessage());
  }
}
