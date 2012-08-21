<?php

$menu = array(
  'tasks' => 'Zadania',
  'contacts' => 'Kontakty',
  'worktypes' => 'Typy prac',
  'colors' => 'Kolory'
);

if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['HTTP_X_REWRITE_URL']))
{
  $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
}

function get_menu_item_class($item)
{
  static $chosen = false;

  if ($chosen) return '';

  $len = strlen(APP_BASE_URL) - 1;

  $uri  = substr($_SERVER['REQUEST_URI'], $len);
  $item = substr($item, $len);

  if (strpos($uri, $item) === 0)
  {
    $chosen = true;

    return 'active';
  }

  return '';
}

if (empty($_SESSION['flash']))
{
  $flash = '';
}
else
{
  $flash = $_SESSION['flash'];
  $flash = render_message($flash['message'], $flash['type'], $flash['title']);

  unset($_SESSION['flash']);
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset=utf-8>
  <title><?= e($title) ?>Pracownia protetyczna</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?= url_for('/__assets__/css/main.css') ?>">
  <?= render_slot('head') ?>
</head>
<body>

<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container-fluid">
      <a class="brand" href="<?= url_for('/') ?>">Protetozaurus 9000</a>
      <ul class="nav">
        <? foreach ($menu as $url => $label): ?>
        <? if ($label === '-'): ?>
        <li class="divider-vertical">
        <? else: ?>
        <li class="<?= get_menu_item_class(url_for($url)) ?>"><a href="<?= url_for($url) ?>"><?= $label ?></a>
        <? endif ?>
        <? endforeach ?>
      </ul>
    </div>
  </div>
</div>

<div class="container-fluid">
  <?= $flash ?>
  <?= $contents ?>
</div>

<script src="<?= url_for('/__assets__/js/jquery.min.js') ?>"></script>
<script src="<?= url_for('/__assets__/bootstrap/js/bootstrap.min.js') ?>"></script>
<script src="<?= url_for('/__assets__/bootstrap/js/typeahead.js') ?>"></script>
<script src="<?= url_for('/__assets__/chosen/chosen.jquery.min.js') ?>"></script>
<script src="<?= url_for('/__assets__/js/main.js') ?>"></script>
<?= render_slot('js') ?>
</body>
</html>
