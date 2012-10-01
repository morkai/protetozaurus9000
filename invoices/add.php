<?php

include __DIR__ . '/__common__.php';

$errors = array();
$data = array(
  'nr' => '',
  'date' => time(),
  'seller' => 0,
  'buyer' => 0
);

if (!empty($_POST['invoice']))
{
  $data = array_merge($data, $_POST['invoice']);

  $data['date'] = strtotime($data['date']);

  if (empty($data['nr']))
  {
    $errors[] = 'Numer faktury jest wymagany.';
  }

  if (empty($data['seller']))
  {
    $errors[] = 'Sprzedawca jest wymagany.';
  }
  else
  {
    $seller = fetch_one('SELECT company, name, address FROM contacts WHERE id=:id LIMIT 1', array(':id' => $data['seller']));

    if (empty($seller))
    {
      $errors[] = 'Wybrany sprzedawca nie istnieje.';
    }
  }

  if (empty($data['buyer']))
  {
    $errors[] = 'Nabywca jest wymagany.';
  }
  else
  {
    $buyer = fetch_one('SELECT company, name, address FROM contacts WHERE id=:id LIMIT 1', array(':id' => $data['buyer']));

    if (empty($buyer))
    {
      $errors[] = 'Wybrany nabywca nie istnieje.';
    }
  }

  if (!empty($errors))
  {
    goto VIEW;
  }

  $data['sellerInfo'] = trim(str_replace("\n\n", "\n", "{$seller->company}\n{$seller->name}\n{$seller->address}"));
  $data['buyerInfo'] = trim(str_replace("\n\n", "\n", "{$buyer->company}\n{$buyer->name}\n{$buyer->address}"));

  try
  {
    exec_insert('invoices', $data);
  }
  catch (PDOException $x)
  {
    $errors[] = 'Numer faktury musi być unikalny.';

    goto VIEW;
  }

  $lastId = get_conn()->lastInsertId();

  go_to("invoices/edit.php?id={$lastId}");
}

VIEW:

$invoice = (object)$data;
$invoice->sellerName = empty($_POST['sellerName']) ? '' : $_POST['sellerName'];
$invoice->buyerName = empty($_POST['buyerName']) ? '' : $_POST['buyerName'];

?>

<? decorate('Dodawanie faktury') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("invoices") ?>">Faktury</a> \
    Dodawanie
  </h1>
</div>

<? if (!empty($errors)): ?>
<ul class="form-errors">
  <? foreach ($errors as $error): ?>
  <li class="form-error"><?= $error ?>
  <? endforeach ?>
</ul>
<? endif ?>

<form action="<?= url_for("invoices/add.php") ?>" method=post autocomplete=off>
  <fieldset>
    <div class="row">
      <div class="control-group span3">
        <label for=invoice-nr class="control-label">Numer faktury:</label>
        <input id=invoice-nr name=invoice[nr] class="span3" type=text value="<?= $invoice->nr ?>" autofocus>
      </div>
      <div class="control-group span3">
        <label for=invoice-date class="control-label">Data faktury:</label>
        <input id=invoice-date name=invoice[date] class="span3" type=date value="<?= date('Y-m-d', $invoice->date) ?>">
      </div>
    </div>
    <div class="control-group">
      <label for=invoice-sellerName class="control-label">Sprzedawca:</label>
      <input id=invoice-sellerName name=sellerName class="span6" type=text value="<?= e($invoice->sellerName) ?>">
      <input id=invoice-seller name=invoice[seller] type=hidden value="<?= $invoice->seller ?>">
    </div>
    <div class="control-group">
      <label for=invoice-buyerName class="control-label">Nabywca:</label>
      <input id=invoice-buyerName name=buyerName class="span6" type=text value="<?= e($invoice->buyerName) ?>">
      <input id=invoice-buyer name=invoice[buyer] type=hidden value="<?= $invoice->buyer ?>">
    </div>
    <div class="form-actions">
      <input class="btn btn-primary" type="submit" value="Zapisz">
    </div>
  </fieldset>
</form>

<? begin_slot('js') ?>
<script src="<?= url_for("__assets__/js/typeaheadContacts.js") ?>"></script>
<script>
$(function()
{
  var typeaheadOptions = {
    url: '<?= url_for('/contacts/index.php?perPage=15') ?>'
  };

  $('#invoice-buyerName').typeaheadContacts(typeaheadOptions);
  $('#invoice-sellerName').typeaheadContacts(typeaheadOptions);

  $(window).resize();
});
</script>
<? append_slot() ?>
