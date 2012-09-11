<?php

include __DIR__ . '/__common__.php';

bad_request_if(empty($_GET['id']));

$bindings = array(':id' => (int)$_GET['id']);

$invoice = fetch_one('SELECT id, closed FROM invoices WHERE id=:id LIMIT 1', $bindings);

not_found_if(empty($invoice));

bad_request_if($invoice->closed);

$taskCount = fetch_one('SELECT COUNT(*) AS total FROM invoice_tasks WHERE invoice=:invoice', array(':invoice' => $invoice->id))->total;

if ($taskCount == 0)
{
  set_flash('Nie można zamknąć faktury, która nie ma przypisanych żadnych zadań.', 'warning');
  go_to(get_referer("invoices/view.php?id={$invoice->id}"));
}

exec_stmt('UPDATE invoices SET closed=1 WHERE id=:id LIMIT 1', $bindings);

go_to("invoices/print.php?id={$invoice->id}");
