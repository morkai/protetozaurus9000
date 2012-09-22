<?php

include __DIR__ . '/__common__.php';

bad_request_if(empty($_REQUEST['task']) || empty($_REQUEST['invoice']));

$invoice = fetch_one('SELECT id, closed FROM invoices WHERE id=:id LIMIT 1', array(':id' => $_REQUEST['invoice']));

not_found_if(empty($invoice));

bad_request_if($invoice->closed);

$task = fetch_one('SELECT id, closed FROM tasks WHERE id=:id LIMIT 1', array(':id' => $_REQUEST['task']));

not_found_if(empty($task));

bad_request_if(!$task->closed);

$referer = get_referer("invoices/tasks.php?invoice={$invoice->id}");
$db = get_conn();

try
{
  $db->beginTransaction();

  exec_stmt('DELETE FROM invoice_tasks WHERE invoice=:invoice AND task=:task LIMIT 1', array(
    ':invoice' => $invoice->id,
    ':task' => $task->id
  ));

  exec_update('tasks', array('closed' => 0), "id={$task->id}");

  $db->commit();
}
catch (PDOException $x)
{
  $db->rollBack();

  if (is_ajax())
  {
    internal_server_error();
  }
  else
  {
    set_flash('Nie udało się usunąć przypisania zadania do faktury :(', 'error');
    go_to($referer);
  }
}

if (!is_ajax())
{
  set_flash('Przypisanie zadanie do faktury zostało pomyślnie usunięte!');
  go_to($referer);
}
