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
  ];
  return date($format_types[$type], strtotime($datetime));
}
