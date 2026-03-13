<?php
require_once './inc/functions.php';

// 合計計算用
$total_amount = 0;
$total = 0;

$id = $_GET['id'] ?? '';
if (empty($id) || !is_numeric($id)) {
  header('Location: index.php');
  // exit;
}

$db = db_connect();
$order = null;

try {


  $sql = 'SELECT sales.id,sales.receipt_id,shops.name AS shops_name,staffs.name AS staffs_name,sales.sale_date,receipts.created_at,receipts.updated_at,receipts.menu_options_id,receipts.amount FROM sales INNER JOIN shops ON sales.shops_id = shops.id INNER JOIN staffs ON sales.staffs_id = staffs.id INNER JOIN receipts ON sales.receipt_id = receipts.receipt_id WHERE sales.id = :id';
  $stmt = $db->prepare($sql);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $order = $stmt->fetch(PDO::FETCH_ASSOC);
  var_dump($order);

  $receipt_id = $order['receipt_id'];

  $sql = 'SELECT menu_options_id FROM receipts WHERE receipts.receipt_id = :receipt_id';
  $stmt = $db->prepare($sql);
  $stmt->bindParam(':receipt_id', $receipt_id, PDO::PARAM_STR);
  $stmt->execute();
  // 注文されたメニューとオプションの組み合わせID（主キー）
  $menu_option_count = $stmt->fetchALL(PDO::FETCH_ASSOC);

  foreach ($menu_option_count as $id) {
    $menuname = get_menuname();

    // var_dump($menuname[$id['menu_options_id']]);

    $optionname = get_option();
    // var_dump($optionname[$id['menu_options_id']]);

    $price = get_price();
    // var_dump($price[$id['menu_options_id']]);
  }

  if (!$order) {
    // header('Location: index.php');
    exit;
  }
} catch (PDOException $e) {
  exit('データの取得に失敗しました: ' . h($e->getMessage()));
}


$page_title = '注文詳細';
require_once 'inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="mb-0">注文詳細</h2>
  <div>
    <a href="edit.php?id=<?php echo $order['id']; ?>" class="btn btn-primary me-2">編集</a>
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
      削除
    </button>
  </div>
</div>

<div class="row">
  <div class="col-md-7">
    <div class="card shadow-sm h-100">
      <div class="card-body">

        <table class="table">
          <thead>
            <tr>
              <th>レシートNO.</th>
              <th>レジ日時</th>
              <th>店舗</th>
              <th>対応スタッフ</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?php echo h($order['receipt_id']) ?></td>
              <td><?php echo h(format_date($order['sale_date'], 4)) ?></td>
              <td><?php echo h($order['shops_name']) ?></td>
              <td><?php echo h($order['staffs_name']) ?></td>
            </tr>
          </tbody>
        </table>

        <dl class="row">

          <?php foreach ($menu_option_count as $id) : ?>

            <!-- メニュー情報 -->

            <dt class="col-8"><?php echo $menuname[$id['menu_options_id']] ?>(<?php echo $optionname[$id['menu_options_id']] ?>)</dt>

            <?php $subtotal_amount = $order['amount'] ?>
            <dd class="col-2"><?php echo $order['amount'] ?>個</dd>


            <?php $subtotal = (int) $price[$id['menu_options_id']] * $order['amount'] ?>
            <dd class="col-2"><?php echo $subtotal ?>円</dd>

            <?php $total_amount = $total_amount + $subtotal_amount  ?>
            <?php $total = $total + $subtotal  ?>
          <?php endforeach; ?>
          <!-- 合計 -->
          <dt class="col-10">点数</dt>
          <dd class="col-2"><?php echo $total_amount ?>点</dd>
          <dt class="col-10">合計</dt>
          <dd class="col-2"><?php echo $total ?>円</dd>
        </dl>

      </div>
    </div>
  </div>
</div>

<div class="mt-4 text-center">
  <a href="order.php" class="btn btn-secondary">一覧へ戻る</a>
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
        <p><strong><?php echo h($order['name']); ?></strong> の情報を完全に削除しますか？</p>
        <p class="text-danger mb-0 small">※この操作は取り消せません。設定されている画像も同時に削除されます。</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
        <form action="delete_do.php" method="POST">
          <!-- 【TODO】削除対象のキャストIDをPOST送信するための、hiddenタイプのinput要素を作成してください。（name属性をid、value属性にキャストのidを出力してください） -->
          <input type="hidden" name="id" value="<?php echo h($order['id']); ?>">
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