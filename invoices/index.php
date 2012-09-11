<?php

include __DIR__ . '/__common__.php';

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$perPage = empty($_GET['perPage']) ? 15 : (int)$_GET['perPage'];

$pagedInvoices = new PagedData($page, $perPage);

$buyer = empty($_GET['buyer']) || !is_numeric($_GET['buyer']) ? 0 : (int)$_GET['buyer'];
$query = empty($_GET['query']) ? '' : trim($_GET['query']);
$where = '';
$bindings = array();

if ($buyer)
{
  $buyer = fetch_one('SELECT id, name FROM contacts WHERE id=:id LIMIT 1', array(':id' => $buyer));

  bad_request_if(empty($buyer));

  $where = 'WHERE i.buyer=:buyer';
  $bindings = array(':buyer' => $buyer->id);
}
else
{
  $buyer = (object)array('id' => 0, 'name' => '');
}

if (!empty($query))
{
  if (preg_match('/[0-9]{4}-[0-9}+-[0-9]+/', $query))
  {
    $startDate = strtotime($query);
    $endDate = $startDate + 24 * 3600 - 1;
    $where = 'WHERE i.date BETWEEN :startDate AND :endDate';
    $bindings = array(':startDate' => $startDate, ':endDate' => $endDate);
  }
  else
  {
    $query = addslashes($query);
    $where = "WHERE i.nr LIKE '%{$query}%' OR b.company LIKE '%{$query}%' OR b.name LIKE '%{$query}%'";
  }
}

$q = <<<SQL
SELECT COUNT(*) AS total
FROM invoices i
INNER JOIN contacts b ON b.id=i.buyer
{$where}
SQL;

$totalItems = fetch_one($q, $bindings)->total;

$q = <<<SQL
SELECT i.*,
b.company AS buyerCompany,
b.name AS buyerName
FROM invoices i
INNER JOIN contacts b ON b.id=i.buyer
{$where}
ORDER BY i.date DESC
LIMIT {$pagedInvoices->getOffset()}, {$perPage}
SQL;

$invoices = fetch_all($q, $bindings);

$pagedInvoices->fill($totalItems, $invoices);

?>

<? decorate('Faktury') ?>

<div class="page-header">
  <ul class="page-actions">
    <li>
      <form class="form-search" action="<?= url_for("invoices") ?>">
        <input type="hidden" name="perPage" value="<?= $perPage ?>">
        <input type="hidden" name="buyer" value="<?= $buyer->id ?>">
        <div class="input-append">
          <input class="span3" name="query" type="search" value="<?= $query ?>" results=5 autofocus><input class="btn" type="submit" value="Szukaj">
        </div>
      </form>
    <li><a class="btn" href="<?= url_for('/invoices/add.php') ?>"><i class="icon-plus"></i> Dodaj nową fakturę</a>
  </ul>
  <h1>
    Faktury
    <? if ($buyer->id): ?>
    dla <a href="<?= url_for("contacts/view.php?id={$buyer->id}") ?>"><?= e($buyer->name) ?></a>
    <? endif ?>
  </h1>
</div>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Nr
      <th>Data
      <th>Nabywca
      <th class="actions">Akcje
    </tr>
  </thead>
  <tbody>
    <? foreach ($pagedInvoices AS $invoice): ?>
    <tr>
      <td><?= e($invoice->nr) ?>
      <td><?= date('Y-m-d', $invoice->date) ?>
      <td><a href="<?= url_for("contacts/view.php?id={$invoice->buyer}") ?>"><?= invoices_render_contact_id($invoice->buyerCompany, $invoice->buyerName) ?></a>
      <td class="actions">
        <a class="btn" href="<?= url_for("invoices/view.php?id={$invoice->id}") ?>"><i class="icon-list-alt"></i></a>
        <? if ($invoice->closed): ?>
        <a class="btn" href="<?= url_for("invoices/print.php?id={$invoice->id}") ?>"><i class="icon-print"></i></a>
        <? else: ?>
        <a class="btn" href="<?= url_for("invoices/close.php?id={$invoice->id}") ?>"><i class="icon-lock"></i></a>
        <a class="btn" href="<?= url_for("invoices/edit.php?id={$invoice->id}") ?>"><i class="icon-pencil"></i></a>
        <a class="btn btn-danger" href="<?= url_for("invoices/delete.php?id={$invoice->id}") ?>"><i class="icon-remove icon-white"></i></a>
        <? endif ?>
    </tr>
    <? endforeach ?>
  </tbody>
</table>

<?= $pagedInvoices->render(url_for("invoices/?perPage={$perPage}&amp;query={$query}&amp;buyer={$buyer->id}")) ?>
