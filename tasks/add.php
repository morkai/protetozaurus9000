<?php

include __DIR__ . '/__common__.php';

if (!empty($_POST['task']))
{
  $data = tasks_prepare_data($_POST['task']);

  exec_insert('tasks', $data);

  $lastId = get_conn()->lastInsertId();

  set_flash('Nowe zadanie zostało dodane pomyślnie!');
  go_to('/tasks/add.php');
}

$task = (object)array(
  'nr' => '',
  'startDate' => date('Y-m-d'),
  'closeDate' => '',
  'doctor' => 0,
  'doctorName' => '',
  'patient' => 0,
  'patientName' => '',
  'worktype' => 0,
  'color' => 0,
  'quantity' => 1,
  'unit' => 'szt.',
  'price' => '0.00',
  'teeth' => array(),
  'notes' => ''
);

$worktypes = fetch_array('SELECT id AS `key`, name AS `value` FROM worktypes ORDER BY name ASC');
$colors = fetch_array('SELECT id AS `key`, name AS `value` FROM colors ORDER BY name ASC');

?>

<? decorate('Dodawanie zadania') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("/tasks") ?>">Zadania</a> \
    Dodawanie
  </h1>
</div>

<form action="<?= url_for("/tasks/add.php") ?>" method=post autocomplete=off>
  <? include __DIR__ . '/__form__.php' ?>
</form>
