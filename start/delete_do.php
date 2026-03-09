<?php
require_once './inc/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}

$id = $_POST['id'] ?? '';

if (empty($id) || !is_numeric($id)) {
  header('Location: index.php');
  exit;
}

// 【TODO】データベースに接続する関数を呼び出し、変数 $db に代入してください。
$db = db_connect();

try {
  // まずは削除対象の猫情報を取得して、画像ファイル名を確認する
  $sql = "SELECT image_name FROM cats WHERE id = :id";
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $cat = $stmt->fetch();

  if ($cat) {
    // 画像の物理削除処理
    if (!empty($cat['image_name'])) {
      $file_path = 'images/' . $cat['image_name'];
      if (file_exists($file_path)) {
        unlink($file_path);
      }
    }

    // レコードの削除
    // 【TODO】catsテーブルから、指定された $id のレコードを削除するSQL文を変数 $sql_delete に代入してください。
    $sql_delete = 'DELETE FROM cats WHERE id = :id ';

    // 【TODO】SQL文を準備し、プレースホルダ `:id` に変数 `$id` をバインドして実行してください。
    $stmt = $db->prepare($sql_delete);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    $stmt->execute();


    $_SESSION['msg'] = 'キャストの情報を削除しました。';
  }
} catch (PDOException $e) {
  // エラー処理（本来はログに記録する等）
  $_SESSION['err_msg'] = '削除中にエラーが発生しました。';
}

header('Location: index.php');
exit;
