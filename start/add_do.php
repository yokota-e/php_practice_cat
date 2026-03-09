<?php
require_once './inc/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: add.php');
  exit;
}

// POSTデータの受け取り
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

// 必須チェック（サーバーサイド実装）
if (empty($name) || empty($breed_id) || empty($gender) || $age === '') {
  $_SESSION['err_msg'] = '必須項目が入力されていません。';
  header('Location: add.php');
  exit;
}

// 【TODO】データベースに接続する関数を呼び出し、変数 $db に代入してください。
$db = db_connect();
try {
  // ★ユニーク制約チェック: 名前が既に使われていないか確認
  // 【TODO】名前が $name と一致するレコードの「件数」をcatsテーブルから取得するSQL文を作成し、変数 $sql_check に代入してください。
  $sql_check  = 'SELECT COUNT(*) FROM cats WHERE name =:name';

  // 【TODO】SQL文を準備し、プレースホルダに $name をバインドして実行してください。
  $stmt = $db->prepare($sql_check);
  $stmt->bindParam(':name', $name, PDO::PARAM_STR);
  $stmt->execute();


  // 【TODO】取得した件数を fetchColumn() などで取得し、変数 $count に代入してください。

  $count = $stmt->fetchColumn();


  if ($count > 0) {
    $_SESSION['err_msg'] = '指定された名前のキャストは既に登録されています。別の名前を指定してください。';
    header('Location: add.php');
    exit;
  }

  // 画像アップロード処理
  $image_name = null;
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['image']['tmp_name'];
    $file_type = exif_imagetype($tmp_name);
    $allowed_types = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP];

    // in_arrayで、$file_typeでとってきた値が $allowed_typesという配列の中にあるか調べている
    if (in_array($file_type, $allowed_types)) {
      // image_type_to_extension — 画像形式からファイルの拡張子を取得する
      $ext = image_type_to_extension($file_type);
      // 【TODO】uniqid()関数などを使ってユニークなファイル名を作成し、拡張子($ext)を結合して変数 $image_name に代入してください。
      $image_name = uniqid() . $ext;

      // 【TODO】保存先のパスを 'images/' フォルダの下に $image_name として指定し、変数 $save_path に代入してください。
      $save_path  = 'images/' .  $image_name;

      if (!move_uploaded_file($tmp_name, $save_path)) {
        $_SESSION['err_msg'] = '画像の保存に失敗しました。';
        header('Location: add.php');
        exit;
      }
    } else {
      $_SESSION['err_msg'] = '許可されていない画像形式です（JPG, PNG, WEBPのみ可）。';
      header('Location: add.php');
      exit;
    }
  }

  // データベースへの登録
  // 【TODO】catsテーブルに各データ（name, breed_id, gender, age, profile, image_name）をINSERTするSQL文を作成し、変数 $sql に代入してください。
  $sql = 'INSERT INTO cats (name, breed_id, gender, age, profile, image_name)
VALUES(:name, :breed_id, :gender, :age, :profile, :image_name)';

  // 【TODO】SQL文を準備し、実行してください。（すべての変数について bindValue を行うこと。profileやimage_nameはNULLが許可される点に注意）
  $stmt = $db->prepare($sql);

  $profile_for_db = ($profile === '') ? null : $profile;

  $stmt->bindParam(':name', $name, PDO::PARAM_STR);
  $stmt->bindParam(':breed_id', $breed_id, PDO::PARAM_INT);
  $stmt->bindParam(':gender', $gender, PDO::PARAM_INT);
  $stmt->bindParam(':age', $age, PDO::PARAM_INT);
  $stmt->bindParam(':profile', $profile_for_db, is_null($profile) ? PDO::PARAM_NULL : PDO::PARAM_STR);
  $stmt->bindParam(':image_name', $image_name, is_null($image_name) ? PDO::PARAM_NULL : PDO::PARAM_STR);

  $stmt->execute();



  // 【TODO】最後に挿入されたレコードのIDを取得し、変数 $new_id に代入してください。
  $new_id =  $db->lastInsertId();

  // 登録完了後、入力保持用のセッションを破棄
  unset($_SESSION['form_data']);
  $_SESSION['msg'] = '新しいキャストを登録しました。';

  header('Location: detail.php?id=' . $new_id);
  exit;
} catch (PDOException $e) {
  $_SESSION['err_msg'] = 'データベースエラー: ' . $e->getMessage();
  header('Location: add.php');
  exit;
}
