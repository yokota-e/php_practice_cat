<?php
require_once './inc/functions.php';

// 【TODO】データベースに接続する関数を呼び出し、変数 $db に代入してください。
$db = db_connect();
$breeds = [];
$err_msg = $_SESSION['err_msg'] ?? '';
unset($_SESSION['err_msg']);

try {
  // 【TODO】breedsテーブルからすべての品種データをidの昇順で取得するSQL文を作成し、変数 $sql に代入してください。
  $sql = 'SELECT * FROM breeds ORDER BY id ASC';


  // 【TODO】SQL文を準備し、実行してください。
  $stmt = $db->prepare($sql);
  $stmt->execute();

  // 【TODO】取得した全データを連想配列形式で取得し、変数 $breeds に代入してください。

  $breeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  if (empty($err_msg)) {
    $err_msg = '品種一覧の取得に失敗しました: ' . $e->getMessage();
  }
}

// セッションから以前の入力値を復元
$name              = $_SESSION['form_data']['name'] ?? '';
$breed_id          = $_SESSION['form_data']['breed_id'] ?? '';
$gender            = $_SESSION['form_data']['gender'] ?? '1'; // デフォルトはおとこのこ
$age               = $_SESSION['form_data']['age'] ?? '';
$profile           = $_SESSION['form_data']['profile'] ?? '';


unset($_SESSION['form_data']);

$page_title = 'キャスト新規登録';
require_once 'inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="mb-0">キャスト新規登録</h2>
  <a href="index.php" class="btn btn-secondary">一覧へ戻る</a>
</div>

<?php if ($err_msg): ?>
  <div class="alert alert-danger">
    <?php echo h($err_msg); ?>
  </div>
<?php endif; ?>

<div class="card shadow-sm mb-5">
  <div class="card-body p-4">
    <form action="add_do.php" method="POST" enctype="multipart/form-data">

      <div class="row mb-3">
        <div class="col-md-12">
          <label for="name" class="form-label fw-bold">名前 <span class="badge bg-danger">必須</span></label>
          <input type="text" class="form-control" id="name" name="name" value="<?php echo h($name); ?>" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6 mb-3 mb-md-0">
          <label for="breed_id" class="form-label fw-bold">品種 <span class="badge bg-danger">必須</span></label>
          <select class="form-select" id="breed_id" name="breed_id" required>
            <option value="">選択してください</option>
            <?php foreach ($breeds as $breed): ?>
              <!-- 【TODO】 品種の「id」をvalue属性に、「name」をエスケープして表示テキストに出力してください -->
              <!-- （セッションから復元した選択状態 $breed_id と一致する場合は 'selected' を出力する処理が書かれています） -->
              <option value="<?php echo h($breed['id']) ?>" <?php echo $breed_id == $breed['id'] ? 'selected' : ''; ?>>
                <!-- nameを出力 -->
                <?php echo h($breed['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
          <label class="form-label d-block fw-bold">性別 <span class="badge bg-danger">必須</span></label>
          <div class="form-check form-check-inline mt-2">
            <input class="form-check-input" type="radio" name="gender" id="gender_male" value="1" <?php echo $gender == '1' ? 'checked' : ''; ?>>
            <label class="form-check-label text-primary" for="gender_male">おとこのこ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" id="gender_female" value="2" <?php echo $gender == '2' ? 'checked' : ''; ?>>
            <label class="form-check-label text-danger" for="gender_female">おんなのこ</label>
          </div>
        </div>
        <div class="col-md-3">
          <label for="age" class="form-label fw-bold">年齢 <span class="badge bg-danger">必須</span></label>
          <div class="input-group">
            <input type="number" class="form-control" id="age" name="age" min="0" max="30" value="<?php echo h($age); ?>" required>
            <span class="input-group-text">歳</span>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label for="profile" class="form-label fw-bold">紹介文</label>
        <textarea class="form-control" id="profile" name="profile" rows="4"><?php echo h($profile); ?></textarea>
      </div>

      <div class="mb-4">
        <label for="image" class="form-label fw-bold">プロフィール画像</label>
        <input type="file" class="form-control" id="image" name="image" accept=".webp, .png, .jpg, .jpeg">
        <div class="form-text">許可される形式: jpg, jpeg, png, webp</div>
      </div>

      <div class="d-grid pt-3 border-top">
        <button type="submit" class="btn btn-primary btn-lg" style="background-color: #e91e63; border-color: #e91e63;">登録する</button>
      </div>
    </form>
  </div>
</div>

<?php require_once 'inc/footer.php'; ?>