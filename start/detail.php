<?php
require_once './inc/functions.php';

$id = $_GET['id'] ?? '';
if (empty($id) || !is_numeric($id)) {
  header('Location: index.php');
  // exit;
}

// 【TODO】データベースに接続する関数を呼び出し、変数 $db に代入してください。
$db = db_connect();
$cat = null;

try {
  // 【TODO】特定のキャストと品種情報を取得するSQL文を作成し、変数 $sql に代入してください。
  // （catsテーブルの全カラムと、breedsテーブルのnameカラムを breed_name として取得）
  // （条件：catsテーブルのid が、URLパラメータから取得したid に一致すること）

  $sql = 'SELECT cats.*,breeds.name AS breed_name FROM cats INNER JOIN breeds ON cats.breed_id = breeds.id WHERE cats.id = :id';




  // 【TODO】SQL文を準備し、実行してください。
  // （プレースホルダ `:id` に、変数 `$id` をバインドしてください。型は数値です）
  $stmt = $db->prepare($sql);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);


  $stmt->execute();


  // 【TODO】取得した1件のデータを連想配列として変数 $cat に代入してください。
  $cat = $stmt->fetch(PDO::FETCH_ASSOC);


  if (!$cat) {
    // header('Location: index.php');
    exit;
  }
} catch (PDOException $e) {
  exit('データの取得に失敗しました: ' . h($e->getMessage()));
}


$page_title = 'キャスト詳細';
require_once 'inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="mb-0">キャスト詳細</h2>
  <div>
    <a href="edit.php?id=<?php echo $cat['id']; ?>" class="btn btn-primary me-2">編集</a>
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
      削除
    </button>
  </div>
</div>

<div class="row">
  <div class="col-md-5 mb-4 mb-md-0">
    <!-- 画像 -->
    <!-- 【TODO】 もしキャストの「image_name」が空ではなく、かつファイルが /images/ の下にあるなら、<img> タグで画像を表示してください -->
    <!-- （画像のパスや alt 属性のエスケープを忘れないこと。クラスには 'img-fluid rounded shadow-sm w-100' 等を設定） -->
    <?php if (!empty($cat['image_name']) && file_exists('images/' . $cat['image_name'])): ?>
      <img src="images/<?php echo h($cat['image_name']); ?>" alt="<?php echo h($cat['name']); ?>" class="img-fluid rounded shadow-sm w-100">
    <?php endif; ?>
    <?php if (empty($cat['image_name'])): ?>
      <!-- 【TODO】 もし画像がなければ、以下のプレースホルダーを表示させてください -->
      <div class="bg-secondary text-white d-flex justify-content-center align-items-center rounded shadow-sm w-100" style="height: 400px;">
        <span class="fs-4">No Image</span>
      </div>
    <?php endif; ?>
  </div>

  <div class="col-md-7">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h3 class="card-title text-pink border-bottom pb-2 mb-4">
          <h3 class="card-title text-pink border-bottom pb-2 mb-4">
            <!-- 【TODO】 キャストの「name」をエスケープして表示してください -->
            <?php echo h($cat['name']); ?>
            <!-- 【TODO】 もし性別「gender」が 1(おとこのこ) なら、以下のHTML（青色の♂）を表示してください -->
            <?php if ($cat['gender'] == 1): ?>
              <span class="text-primary fs-4 ms-2">♂</span>

              <!-- 【TODO】 もし性別「gender」が 2(おんなのこ) なら、以下のHTML（赤色の♀）を表示してください -->
            <?php elseif ($cat['gender'] == 2): ?>
              <span class="text-danger fs-4 ms-2">♀</span>
            <?php endif; ?>
          </h3>

          <table class="table table-borderless">
            <tbody>
              <tr>
                <th class="w-25 text-muted">ID</th>
                <td><span class="badge bg-light text-dark border"><?php echo h($cat['id']); ?><!-- 【TODO】 そのキャストの「id」をエスケープして表示してください --></span></td>
              </tr>
              <tr>
                <th class="text-muted">種類</th>
                <td class="fs-5">
                  <?php echo h($cat['breed_name']); ?>
                  <!-- 【TODO】 品種名「breed_name」をエスケープして表示してください -->
                </td>
              </tr>
              <tr>
                <th class="text-muted">年齢</th>
                <td class="fs-5">
                  <?php echo h($cat['age']); ?>
                  <!-- 【TODO】 年齢「age」をエスケープして表示してください --> 歳
                </td>
              </tr>
              <tr>
                <th class="text-muted">紹介文</th>
                <td>
                  <div class="p-3 bg-light rounded mt-1">
                    <?php echo h($cat['profile']); ?>
                    <!-- 【TODO】 紹介文「profile」をエスケープし、さらに改行を <br> タグに変換(nl2br)して表示してください -->

                  </div>
                </td>
              </tr>
              <tr>
                <th class="text-muted border-top pt-3 mt-3">登録情報</th>
                <td class="border-top pt-3 mt-3 small text-muted">
                  登録:
                  <?php echo h(date('Y年m月d日 H:i:s', strtotime($cat['created_at']))); ?>

                  <br>

                  更新:
                  <?php echo h(format_date($cat['updated_at'], 3)); ?>

                </td>
              </tr>
            </tbody>
          </table>
      </div>
    </div>
  </div>
</div>

<div class="mt-4 text-center">
  <a href="index.php" class="btn btn-secondary">一覧へ戻る</a>
</div>

<!-- 削除確認モーダル -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">削除の確認</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong><?php echo h($cat['name']); ?></strong> の情報を完全に削除しますか？</p>
        <p class="text-danger mb-0 small">※この操作は取り消せません。設定されている画像も同時に削除されます。</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
        <form action="delete_do.php" method="POST">
          <!-- 【TODO】削除対象のキャストIDをPOST送信するための、hiddenタイプのinput要素を作成してください。（name属性をid、value属性にキャストのidを出力してください） -->
          <input type="hidden" name="id" value="<?php echo h($cat['id']); ?>">
          <button type="submit" class="btn btn-danger">削除を実行する</button>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .text-pink {
    color: #e91e63;
  }
</style>

<?php require_once 'inc/footer.php'; ?>