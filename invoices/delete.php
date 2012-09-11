<?php

include __DIR__ . '/__common__.php';

bad_request_if(empty($_GET['id']));

$bindings = array(':id' => (int)$_GET['id']);

$invoice = fetch_one('SELECT id, closed FROM invoices WHERE id=:id LIMIT 1', $bindings);

not_found_if(empty($invoice));

bad_request_if($invoice->closed);

$db = get_conn();

try
{
  $db->beginTransaction();

  exec_stmt('DELETE FROM invoices WHERE id=:id LIMIT 1', $bindings);

  exec_stmt('UPDATE tasks t SET t.closed=0 WHERE t.id IN(SELECT it.task FROM invoice_tasks it WHERE it.invoice=:invoice)', array(':invoice' => $invoice->id));

  $db->commit();
}
catch (PDOException $x)
{
  $db->rollBack();

  set_flash($x->getMessage(), 'error');
  go_to(get_referer('invoices/'));
}

set_flash('Faktura została usunięta pomyślnie!');
go_to("invoices/");
