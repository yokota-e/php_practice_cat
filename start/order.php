<?php
require_once './inc/functions.php';

$db = db_connect();


$err_msg = '';

try {

    $sql = 'SELECT sales.id,sales.receipt_id,shops.name AS shops_name,staffs.name AS staffs_name,sales.sale_date FROM sales INNER JOIN shops ON sales.shops_id = shops.id INNER JOIN staffs ON sales.staffs_id = staffs.id ORDER BY sale_date DESC';

    $stmt = $db->prepare($sql);

    $stmt->execute();

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $err_msg = 'データの取得に失敗しました: ' . $e->getMessage();
}

// 成功メッセージの表示用（リダイレクト時など）
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

$page_title = '注文一覧';
require_once 'inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">注文一覧</h2>
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
    <?php if (!empty($orders)): ?>


        <table class="table">
            <thead>
                <tr>
                    <th>レシートNO.</th>
                    <th>レジ日時</th>
                    <th>店舗</th>
                    <th>対応スタッフ</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td> <?php echo h($order['receipt_id']); ?></td>
                        <td> <?php echo h(format_date($order['sale_date'], 4)); ?></td>
                        <td> <?php echo h($order['shops_name']); ?></td>
                        <td> <?php echo h($order['staffs_name']); ?></td>
                        <td> <a href="order_detail.php?id=<?php echo h($order['id']); ?>" class="btn btn-outline-danger">詳細</a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                注文がまだ登録されていません。
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