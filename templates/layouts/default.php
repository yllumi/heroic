<!DOCTYPE html>

<html lang="en" data-bs-theme="auto">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Heroic</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    body {
      --bs-body-bg: #FDFDFB;

      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    :root {
      --heroic-orange: #ff6b00;
    }
    [data-bs-theme="dark"] body {
      --bs-body-bg: #121212;
      --bs-body-color: #e0e0e0;
      --bs-navbar-bg: #1e1e1e;
      --bs-navbar-color: #fff;
    }
    [data-bs-theme="dark"] .btn-outline-secondary {
      border-color: var(--bs-body-color);
      color: var(--bs-body-color);
    }
    .text-brand {
      color: var(--heroic-orange);
    }
    .container {
      max-width: 1024px;
    }
  </style>

</head>
<body>

  <?= $this->renderSection('content') ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= asset_url('vendor/heroic/heroic.min.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/pinecone-router@6.2.4/dist/router.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  <?= $this->renderSection('script') ?>

</body>
</html>