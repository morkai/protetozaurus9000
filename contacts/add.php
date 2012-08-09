<?php

include __DIR__ . '/../__common__.php';

if (!empty($_POST['contact']))
{
  $data = $_POST['contact'];

  exec_insert('contacts', $data);

  $lastId = get_conn()->lastInsertId();

  set_flash('Nowy kontakt został dodany pomyślnie!');
  go_to('/contacts/add.php');
}

$contact = (object)array(
  'name' => '',
  'address' => '',
  'company' => '',
  'position' => '',
  'email' => '',
  'website' => 'http://',
  'telHome' => '',
  'telWork' => '',
  'telMobile' => '',
  'comments' => '',
);

?>

<? decorate('Dodawanie kontaktu') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("/contacts") ?>">Kontakty</a> \
    Dodawanie
  </h1>
</div>

<form action="<?= url_for("/contacts/add.php") ?>" method=post>
  <? include __DIR__ . '/__form__.php' ?>
</form>
