<?php
require_once './inc/functions.php';

// 【TODO】データベースに接続する関数を呼び出し、変数 $db に代入してください。
$db = db_connect();

$cats = [];
$err_msg = '';

try {
  // 【TODO】catsテーブルとbreedsテーブルを結合して全件取得するSQL文を作成し、変数 $sql に代入してください。
  // （catsテーブルの全カラムと、breedsテーブルのnameカラムを breed_name として取得。cats.idの昇順で並び替え）


  // 【TODO】SQL文を準備し、実行してください。
  $sql = 'SELECT cats.*,breeds.name AS breed_name FROM cats INNER JOIN breeds ON cats.breed_id = breeds.id ORDER BY cats.id ASC';

  $stmt = $db->prepare($sql);

  $stmt->execute();




  // 【TODO】取得した全データを連想配列形式で取得し、変数 $cats に代入してください。

  $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $err_msg = 'データの取得に失敗しました: ' . $e->getMessage();
}

// 成功メッセージの表示用（リダイレクト時など）
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

$page_title = 'キャスト一覧';
require_once 'inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="mb-0">キャスト一覧</h2>
</div>

<?php if ($msg): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo h($msg); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if ($err_msg): ?>
  <div class="alert alert-danger">
    <?php echo h($err_msg); ?>
  </div>
<?php endif; ?>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
  <?php if (!empty($cats)): ?>
    <!-- 【TODO】 $cats の配列を foreach で回して、以下の <div class="col"> ～ </div> のブロックを繰り返し表示させてください -->
    <?php foreach ($cats as $cat): ?>
      <div class="col">
        <div class="card h-100 shadow-sm">
          <!-- 画像 -->
          <!-- 【TODO】 もしキャストの「image_name」が空ではなく、かつファイルが /images/ の下にあるなら、<img> タグで画像を表示してください -->
          <!-- （画像のパスや alt 属性のエスケープを忘れないこと。表示用のクラスは 'card-img-top' です） -->
          <?php if (!empty($cat['image_name']) && file_exists('images/' . $cat['image_name'])): ?>

            <img class="card-img-top" src="images/<?php echo h($cat['image_name']) ?>" alt="<?php echo h($cat['name']) ?>">

          <?php endif; ?>
          <?php if (empty($cat['image_name'])): ?>
            <!-- 【TODO】 もし画像がなければ、以下のプレースホルダーを表示させてください -->
            <div class="bg-secondary text-white d-flex justify-content-center align-items-center card-img-top">
              <span>No Image</span>
            </div>
          <?php endif; ?>

          <div class="card-body">
            <h5 class="card-title text-center text-pink mb-1">
              <!-- 【TODO】 キャストの「name」をエスケープして表示してください -->
              <?php echo h($cat['name']) ?>
              <!-- 【TODO】 もし性別「gender」が 1(おとこのこ) なら、以下のHTML（青色の♂）を表示してください -->
              <?php if ($cat['gender'] == 1): ?>
                <span class="text-primary fs-6">♂</span>
              <?php elseif ($cat['gender'] == 2): ?>
                <!-- 【TODO】 もし性別「gender」が 2(おんなのこ) なら、以下のHTML（赤色の♀）を表示してください -->
                <span class="text-danger fs-6">♀</span>
              <?php endif; ?>
            </h5>
            <p class="card-text text-center text-muted small mb-3">
              <!-- 【TODO】 品種名「breed_name」と年齢「age」を組み合わせてエスケープして表示してください（例: マンチカン / 2歳） -->
              <?php echo h($cat['breed_name']); ?> / <?php echo h($cat['age']); ?>歳
            </p>
            <div class="text-center">
              <span class="badge bg-light text-dark border mb-3">No.
                <?php echo h($cat['id']); ?>
                <!-- 【TODO】 そのキャストの「id」をエスケープして表示してください --></span>
            </div>

            <div class="d-grid mt-auto">
              <!-- 【TODO】 詳細ページのリンク（href=""）に、そのキャストの「id」をクエリパラメータとして付与してください（例: detail.php?id=1） -->
              <a href="detail.php?id=<?php echo h($cat['id']); ?>" class="btn btn-outline-danger">詳細ページへ</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="col-12">
      <div class="alert alert-info">
        キャストがまだ登録されていません。
      </div>
    </div>
  <?php endif; ?>

</div>

<style>
  .text-pink {
    color: #e91e63;
  }

  .btn-outline-danger {
    color: #e91e63;
    border-color: #e91e63;
  }

  .btn-outline-danger:hover {
    background-color: #e91e63;
    color: #fff;
  }
</style>

<?php require_once 'inc/footer.php'; ?>