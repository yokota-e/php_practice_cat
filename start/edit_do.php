<?php
require_once './inc/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}

$id                = (int)$_POST['id'] ?? '';
$name              = $_POST['name'] ?? '';
$breed_id          = (int)$_POST['breed_id'] ?? '';
$gender            = (int)$_POST['gender'] ?? '';
$age               = (int)$_POST['age'] ?? '';
$profile           = $_POST['profile'] ?? '';

// 入力データ保持用
$_SESSION['form_data'] = [
  'name'              => $name,
  'breed_id'          => $breed_id,
  'gender'            => $gender,
  'age'               => $age,
  'profile'           => $profile
];

// 必須チェック
if (empty($id) || empty($name) || empty($breed_id) || empty($gender) || $age === '') {
  $_SESSION['err_msg'] = '必須項目が入力されていません。';
  header('Location: edit.php?id=' . $id);
  exit;
}

// 【TODO】データベースに接続する関数を呼び出し、変数 $db に代入してください。
$db = db_connect();

try {
  // ★ユニーク制約チェック: 変更後の名前が、"自分以外の"他の猫ですでに使われていないかチェック
  // 【TODO】名前が $name と一致し、かつ id が $id と一致しないレコードの「件数」を取得するSQL文を変数 $sql_check に代入してください。
  $sql_check = 'SELECT count(name) FROM cats WHERE name = :name AND id = :id';

  // 【TODO】SQL文を準備し、プレースホルダに $name と $id をバインドして実行してください。
  $stmt = $db->prepare($sql_check);
  $stmt->bindParam('name', $name, PDO::PARAM_STR);
  $stmt->bindParam('id', $id, PDO::PARAM_INT);
  $stmt->execute();


  // 【TODO】取得した件数を fetchColumn() などで取得し、変数 $count に代入してください。
  $count = $stmt->fetchColumn();

  if ($count > 0) {
    $_SESSION['err_msg'] = '指定された名前のキャストは既に登録されています。';
    header('Location: edit.php?id=' . $id);
    exit;
  }

  // データベースの更新処理（画像は更新しない仕様）
  // 【TODO】catsテーブルの各データ（name, breed_id, gender, age, profile）を、指定された $id のレコードに対してUPDATEするSQL文を変数 $sql に代入してください。
  $sql = 'UPDATE cats SET name=:name, breed_id=:breed_id, gender=:gender, age=:age, profile=:profile WHERE id=:id';

  // 【TODO】SQL文を準備し、実行してください。（すべての変数について bindValue を行うこと。profileはNULLが許可される点に注意）

  $stmt = $db->prepare($sql);

  $profile_for_db = ($profile === "") ? null : $profile;


  $stmt->bindParam(':name', $name, PDO::PARAM_STR);
  $stmt->bindParam(':breed_id', $breed_id, PDO::PARAM_INT);
  $stmt->bindParam(':gender', $gender, PDO::PARAM_INT);
  $stmt->bindParam(':age', $age, PDO::PARAM_INT);
  $stmt->bindParam(':profile', $profile_for_db, is_null($profile_for_db) ? PDO::PARAM_NULL : PDO::PARAM_STR);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);

  $stmt->execute();

  unset($_SESSION['form_data']);
  $_SESSION['msg'] = '登録情報を更新しました。';

  header('Location: detail.php?id=' . $id);
  exit;
} catch (PDOException $e) {
  $_SESSION['err_msg'] = 'データベースエラー: ' . $e->getMessage();
  header('Location: edit.php?id=' . $id);
  exit;
}
