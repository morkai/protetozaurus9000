<?php

include __DIR__ . '/__common__.php';

bad_request_if(empty($_GET['id']));

$q = <<<SQL
SELECT i.*
FROM invoices i
WHERE i.id=:invoice
LIMIT 1
SQL;

$invoice = fetch_one($q, array(':invoice' => $_GET['id']));

not_found_if(empty($invoice));

$q = <<<SQL
SELECT t.*,
p.name AS patientName,
w.name AS worktypeName
FROM invoice_tasks it
INNER JOIN tasks t ON t.id=it.task
INNER JOIN contacts p ON p.id=t.patient
INNER JOIN worktypes w ON w.id=t.worktype
WHERE it.invoice=:invoice
ORDER BY t.id ASC
SQL;

$invoice->tasks = fetch_all($q, array(':invoice' => $invoice->id));

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Faktura <?= e($invoice->nr) ?></title>
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      font-size: 14px;
      background: #FFF;
      color: #000;
      text-align: left;
    }
    h1, h2, h3 {
      margin: 0;
    }
    .invoiceNr {
      float: right;
    }
    .companyName,
    .invoiceNr {
      text-transform: uppercase;
      font-variant: small-caps;
      font-size: 20px;
    }
    .companyName em,
    .invoiceNr em {
      font-style: normal;
      font-weight: normal;
      font-size: 12px;
    }
    #contacts {
      box-sizing: content-box;
      padding-top: 5mm;
      margin: 5mm 0;
      border-top: 1px solid #666;
    }
    #contacts p {
      margin-bottom: 0;
    }
    .contents {
      font-size: 12px;
      line-height: 1.4;
    }
    .contents p {
      text-align: justify;
    }
    .contents h1 {
      font-size: 1.4em;
    }
    .contents h2 {
      font-size: 1.2em;
    }
    .contents h3 {
      font-size: 1em;
    }
    .footer {
      padding-top: 5mm;
      margin-top: 5mm;
      border-top: 1px solid #666;
      font-size: 10px;
    }
    .address {
      margin: 0;
    }
    .contact {
      margin: 0;
      float: right;
      text-align: right;
    }
    #contacts > div {
      vertical-align: top;
      display: inline-block;
      width: 49%;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 4px 6px;
      border: 1px solid #666;
    }
    th {
      font-size: 14px;
    }
    @media print {
      html {
        -webkit-print-color-adjust: exact;
      }
      body {
        margin: 0;
      }
      .page {
        border: 0;
      }
    }
  </style>
</head>
<body>
<h1 class="invoiceNr"><em>data</em> <?= date('Y-m-d', $invoice->date) ?></h1>
<h1 class="companyName"><?= e($invoice->nr) ?> <em>faktura</em></h1>
<div id="contacts">
  <div id="seller">
    <h2>Sprzedawca</h2>
    <p><?= nl2br(trim($invoice->sellerInfo)) ?></p>
  </div>
  <div id="buyer">
    <h2>Nabywca</h2>
    <p><?= nl2br(trim($invoice->buyerInfo)) ?></p>
  </div>
</div>
<div class="contents">
  <table id="tasks">
    <thead>
      <tr>
        <th>Lp.</th>
        <th>Nr</th>
        <th>Pacjent</th>
        <th>Typ</th>
        <th>Ilość</th>
        <th>Wartość</th>
      </tr>
    </thead>
    <tbody>
      <? foreach ($invoice->tasks as $i => $task): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= e($task->nr) ?></td>
        <td><?= e($task->patientName) ?></td>
        <td><?= e($task->worktypeName) ?></td>
        <td><?= $task->quantity ?> <?= $task->unit ?></td>
        <td><?= $task->price ?> zł</td>
      </tr>
      <? endforeach ?>
    </tbody>
  </table>
</div>
<div class="footer">
  <p class="contact">
    Tel.: 012-345-678<br>
    E-mail: pp@walkner.pl<br>
    WWW: http://ppwalkner.pl/
  </p>
  <p class="address">
    Protetozaurus 9000 Łukasz Walukiewicz<br>
    Nowa Wieś Kętrzyńska 7, 11-400 Kętrzyn<br>
    NIP: 123-45-67-890 REGON: 123456789
  </p>
</div>
</body>
</html>
