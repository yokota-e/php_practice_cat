<?php
require_once './inc/functions.php';

$id = $_GET['id'] ?? '';
if (empty($id) || !is_numeric($id)) {
  header('Location: index.php');
  exit;
}

// 【TODO】データベースに接続する関数を呼び出し、変数 $db に代入してください。
$db = db_connect();
$err_msg = $_SESSION['err_msg'] ?? '';
unset($_SESSION['err_msg']);

$cat = null;
$breeds = [];

try {
  // キャスト情報の取得
  // 【TODO】catsテーブルから id が $id に一致するレコードを1件取得するSQL文を作成し、変数 $sql に代入してください。
  $sql = 'SELECT * FROM cats WHERE id =:id';

  // 【TODO】SQL文を準備し、プレースホルダ `:id` に変数 `$id` をバインドして実行してください。
  $stmt = $db->prepare($sql);
  $stmt->bindParam('id', $id, PDO::PARAM_INT);

  $stmt->execute();

  // 【TODO】取得した1件のデータを連想配列形式で取得し、変数 $cat に代入してください。
  $cat = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$cat) {
    header('Location: index.php');
    exit;
  }

  // 品種リストの取得
  // 【TODO】breedsテーブルからすべての品種データをidの昇順で取得するSQL文を作成し、変数 $sql_breeds に代入してください。
  $sql_breeds = 'SELECT * FROM breeds ORDER BY id ASC';

  // 【TODO】SQL文を準備し、実行してください。
  $stmt = $db->prepare($sql_breeds);

  $stmt->execute();

  // 【TODO】取得した全データを連想配列形式で取得し、変数 $breeds に代入してください。
  $breeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  exit('データの取得に失敗しました: ' . h($e->getMessage()));
}


// エラー復帰用のセッション値、なければDBの値をセット
$name              = $_SESSION['form_data']['name'] ?? $cat['name'];
$breed_id          = $_SESSION['form_data']['breed_id'] ?? $cat['breed_id'];
$gender            = $_SESSION['form_data']['gender'] ?? $cat['gender'];
$age               = $_SESSION['form_data']['age'] ?? $cat['age'];
$profile           = $_SESSION['form_data']['profile'] ?? $cat['profile'];

unset($_SESSION['form_data']);

$page_title = 'キャスト情報編集';
require_once 'inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="mb-0">キャスト情報編集</h2>
  <a href="detail.php?id=<?php echo h($cat['id']); ?>" class="btn btn-secondary">詳細へ戻る</a>
</div>

<?php if ($err_msg): ?>
  <div class="alert alert-danger">
    <?php echo h($err_msg); ?>
  </div>
<?php endif; ?>

<div class="card shadow-sm mb-5">
  <div class="card-body p-4">
    <form action="edit_do.php" method="POST">
      <!-- 更新対象のIDを隠しフィールドで送る -->
      <!-- 【TODO】編集対象のキャストの「id」をvalue属性に出力してください -->
      <input type="hidden" name="id" value="<?php echo h($cat['id']); ?>">

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
              <!-- （すでに選択されていた品種 $breed_id と一致する場合は 'selected' を出力する処理が書かれています） -->
              <option value="<?php echo h($breed['id']); ?>" <?php echo $breed_id == $breed['id'] ? 'selected' : ''; ?>>
                <?php echo h($breed['name']); ?>
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

      <div class="mb-4">
        <label for="profile" class="form-label fw-bold">紹介文</label>
        <textarea class="form-control" id="profile" name="profile" rows="4"><?php echo h($profile); ?></textarea>
      </div>

      <div class="mb-4">
        <label class="form-label d-block fw-bold">現在のプロフィール画像</label>
        <?php if (!empty($cat['image_name']) && file_exists('images/' . $cat['image_name'])): ?>
          <img src="images/<?php echo h($cat['image_name']); ?>" alt="現在のアバター" class="img-thumbnail" style="max-height: 150px;">
        <?php else: ?>
          <span class="text-muted">（未設定）</span>
        <?php endif; ?>
        <p class="text-muted small mt-2">※画像の変更機能は現在提供されていません。</p>
      </div>

      <div class="d-grid pt-3 border-top">
        <button type="submit" class="btn btn-success btn-lg">更新する</button>
      </div>
    </form>
  </div>
</div>

<?php require_once 'inc/footer.php'; ?>