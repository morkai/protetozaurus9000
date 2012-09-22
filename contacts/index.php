<?php

include __DIR__ . '/../__common__.php';

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$perPage = empty($_GET['perPage']) ? 10 : (int)$_GET['perPage'];

$pagedContacts = new PagedData($page, $perPage);

$query = get_search_query();
$where = '';

if (!empty($query))
{
  $where = "WHERE name LIKE '%{$query}%' OR company LIKE '%{$query}%' OR address LIKE '%{$query}%'";
}

$totalItems = fetch_one("SELECT COUNT(*) AS total FROM contacts {$where}")->total;

$contacts = fetch_all(sprintf(
  "SELECT * FROM contacts %s ORDER BY company, name LIMIT %d,%d",
  $where,
  $pagedContacts->getOffset(),
  $perPage
));
$contacts = array_map(function($contact)
{
  escape($contact);

  $contact->company = dash_if_empty($contact->company);
  $contact->address = empty($contact->address) ? '-' : nl2br($contact->address);
  $contact->tel = '';

  if (!empty($contact->telHome)) $contact->tel .= "Domowy: {$contact->telHome}<br>";
  if (!empty($contact->telWork)) $contact->tel .= "Służbowy: {$contact->telWork}<br>";
  if (!empty($contact->telMobile)) $contact->tel .= "Komórkowy: {$contact->telMobile}<br>";

  if (empty($contact->tel))
  {
    $contact->tel = '-';
  }

  return $contact;
}, $contacts);

if (is_ajax())
{
  output_json($contacts);
}

$pagedContacts->fill($totalItems, $contacts);

?>

<? decorate('Kontakty') ?>

<div class="page-header">
  <ul class="page-actions">
    <li>
      <form class="form-search" action="<?= url_for("contacts") ?>">
        <div class="input-append">
          <input class="span3" name="query" type="search" value="<?= $query ?>" results=5 autofocus><input class="btn" type="submit" value="Szukaj">
        </div>
      </form>
    <li><a class="btn" href="<?= url_for('/contacts/add.php') ?>"><i class="icon-plus"></i> Dodaj nowy kontakt</a>
  </ul>
  <h1>Kontakty</h1>
</div>

<table class="table table-bordered table-condensed">
  <thead>
    <tr>
      <th>Imię i nazwisko
      <th>Firma
      <th>Telefony
      <th>E-mail
      <th>Adres
      <th class="actions">Akcje
    </tr>
  </thead>
  <tbody>
    <? foreach ($pagedContacts AS $contact): ?>
    <tr>
      <td><?= $contact->name ?>
      <td><?= $contact->company ?>
      <td><?= $contact->tel ?>
      <td>
        <? if (empty($contact->email)): ?>
        -
        <? else: ?>
        <a href="mailto:<?= $contact->email ?>"><?= $contact->email ?></a>
        <? endif ?>
      <td><?= $contact->address ?>
      <td class="actions">
        <a class="btn" title="Wyświetl szczegóły kontaktu" href="<?= url_for("contacts/view.php?id={$contact->id}") ?>"><i class="icon-list-alt"></i></a>
        <a class="btn" title="Edytuj kontakt" href="<?= url_for("contacts/edit.php?id={$contact->id}") ?>"><i class="icon-pencil"></i></a>
        <a class="btn btn-danger" title="Usuń kontakt" href="<?= url_for("contacts/delete.php?id={$contact->id}") ?>"><i class="icon-remove icon-white"></i></a>
    </tr>
    <? endforeach ?>
  </tbody>
</table>

<?= $pagedContacts->render(url_for("contacts/?perPage={$perPage}&amp;query={$query}")) ?>
