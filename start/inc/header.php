<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo h($page_title ?? '猫カフェ キャスト管理システム'); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #fce4ec;
      /* 猫カフェらしい少し暖かい背景色 */
    }

    .navbar {
      background-color: #e91e63 !important;
      /* メインカラーをピンク系に */
    }

    .avatar-img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
    }

    .avatar-img-large {
      width: 100%;
      height: auto;
      max-height: 300px;
      object-fit: contain;
      border-radius: 8px;
    }

    .card-img-top {
      height: 200px;
      object-fit: cover;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
      <a class="navbar-brand" href="index.php">猫カフェ キャスト管理</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="add.php">新規登録</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container mb-5">