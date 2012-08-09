<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

$contact = fetch_one('SELECT * FROM contacts WHERE id=?', array(1 => $_GET['id']));

not_found_if(empty($contact));

if (!empty($_POST['contact']))
{
  $data = $_POST['contact'];

  exec_update('contacts', $data, "id={$contact->id}");

  set_flash('Kontakt został zmodyfikowany pomyślnie!');
  go_to(get_referer("/contacts/view.php?id={$contact->id}"));
}

escape($contact);

?>

<? decorate('Edycja kontaktu') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("/contacts") ?>">Kontakty</a> \
    <a href="<?= url_for("/contacts/view.php?id={$contact->id}") ?>"><?= $contact->name ?></a> \
    Edycja
  </h1>
</div>

<form action="<?= url_for("/contacts/edit.php?id={$contact->id}") ?>" method=post>
  <? include __DIR__ . '/__form__.php' ?>
</form>
