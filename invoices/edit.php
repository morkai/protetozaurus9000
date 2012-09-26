<?php

include __DIR__ . '/__common__.php';

bad_request_if(empty($_GET['id']));

$q = <<<SQL
SELECT i.*,
s.company AS sellerCompany,
s.name AS sellerName,
b.company AS buyerCompany,
b.name AS buyerName
FROM invoices i
INNER JOIN contacts s ON s.id=i.seller
INNER JOIN contacts b ON b.id=i.buyer
WHERE i.id=:invoice
LIMIT 1
SQL;

$invoice = fetch_one($q, array(':invoice' => $_GET['id']));

not_found_if(empty($invoice));

bad_request_if($invoice->closed);

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

if (!empty($_POST['invoice']))
{
  $data = $_POST['invoice'];

  $data['date'] = strtotime($data['date']);

  $tasks = $data['tasks'];
  unset($data['tasks']);

  try
  {
    exec_update('invoices', $data, "id={$invoice->id}");

    exec_stmt('UPDATE tasks t SET t.closed=0 WHERE t.id IN(SELECT i.task FROM invoice_tasks i WHERE i.invoice=:invoice)', array(':invoice' => $invoice->id));

    exec_stmt('DELETE FROM invoice_tasks WHERE invoice=:invoice', array(':invoice' => $invoice->id));

    $stmt = prepare_stmt('INSERT INTO invoice_tasks SET invoice=:invoice, task=:task');

    foreach ($tasks as $task)
    {
      $stmt->execute(array(':invoice' => $invoice->id, ':task' => (int)$task));
    }

    exec_stmt('UPDATE tasks t SET t.closed=1 WHERE t.id IN(SELECT i.task FROM invoice_tasks i WHERE i.invoice=:invoice)', array(':invoice' => $invoice->id));
  }
  catch (PDOException $x)
  {
    set_flash('Nie udało się zamodyfikować faktury: ' . $x->getMessage(), 'error');
    go_to("invoices/edit.php?id={$invoice->id}");
  }

  set_flash('Faktura została zmodyfikowana pomyślnie!');
  go_to(get_referer("/invoices/view.php?id={$invoice->id}"));
}

escape($invoice);

?>

<? decorate('Edycja faktury') ?>

<div class="page-header" xmlns="http://www.w3.org/1999/html">
  <h1>
    <a href="<?= url_for("invoices") ?>">Faktury</a> \
    <a href="<?= url_for("invoices/view.php?id={$invoice->id}") ?>"><?= $invoice->nr ?></a> \
    Edycja
  </h1>
</div>

<form id=editInvoiceForm action="<?= url_for("invoices/edit.php?id={$invoice->id}") ?>" method=post>
  <fieldset>
    <div class="row">
      <div class="control-group span3">
        <label for=invoice-nr class="control-label">Numer faktury:</label>
        <span id=invoice-nr class="span3 uneditable-input"><?= $invoice->nr ?></span>
      </div>
      <div class="control-group span3">
        <label for=invoice-date class="control-label">Data faktury:</label>
        <input id=invoice-date name=invoice[date] class="span3" type=date value="<?= date('Y-m-d', $invoice->date) ?>">
      </div>
    </div>
    <div class="control-group">
      <label for=invoice-sellerInfo class="control-label">Sprzedawca:</label>
      <span class="help-block">
        <a href="<?= url_for("contacts/view.php?id={$invoice->seller}") ?>"><?= invoices_render_contact_id($invoice->sellerCompany, $invoice->sellerName) ?></a>
      </span>
      <textarea id=invoice-sellerInfo name=invoice[sellerInfo] class="span6" rows="6"><?= e($invoice->sellerInfo) ?></textarea>
    </div>
    <div class="control-group">
      <label for=invoice-buyerInfo class="control-label">Nabywca:</label>
      <span class="help-block">
        <a href="<?= url_for("contacts/view.php?id={$invoice->buyer}") ?>"><?= invoices_render_contact_id($invoice->buyerCompany, $invoice->buyerName) ?></a>
      </span>
      <textarea id=invoice-buyerInfo name=invoice[buyerInfo] class="span6" rows="6"><?= e($invoice->buyerInfo) ?></textarea>
    </div>
    <div class="control-group">
      <label for=newTaskNr class="control-label">Przypisz zadanie o numerze:</label>
      <input id=newTaskNr class="span6" type=text value="">
    </div>
    <table id=tasksTable class="table table-bordered table-condensed">
      <thead>
        <tr>
          <th>Nr
          <th>Pacjent
          <th>Typ pracy
          <th>Ilość
          <th>Cena
          <th class="actions">Akcje
        </tr>
      </thead>
      <tbody class="empty <?= empty($invoice->tasks) ? '' : 'hidden' ?>">
        <tr>
          <td colspan="6">Faktura nie ma jeszcze przypisanych żadnych zadań.
        </tr>
      </tbody>
      <tbody class="tasks">
        <tr id="task-template" class="task" data-id="{$id}">
          <td>{$nr}
          <td><a href="<?= url_for('contacts/view.php?id={$patient}') ?>">{$patientName}</a>
          <td>{$worktypeName}
          <td>{$quantity} {$unit}
          <td>{$price} zł
          <td>
            <input type="hidden" name="invoice[tasks][]" value="{$id}">
            <span class="btn remove-task" title="Usuń przypisanie"><i class="icon-remove"></i></span>
        </tr>
        <? foreach ($invoice->tasks as $task): ?>
        <tr class="task" data-id="<?= $task->id ?>">
          <td><?= e($task->nr) ?>
          <td><a href="<?= url_for("contacts/view.php?id={$task->patient}") ?>"><?= e($task->patientName) ?></a>
          <td><?= e($task->worktypeName) ?>
          <td><?= $task->quantity ?> <?= e($task->unit) ?>
          <td><?= $task->price ?> zł
          <td>
            <input type="hidden" name="invoice[tasks][]" value="<?= $task->id ?>">
            <span class="btn remove-task" title="Usuń przypisanie"><i class="icon-remove"></i></span>
        </tr>
        <? endforeach ?>
      </tbody>
    </table>
    <div class="form-actions">
      <input class="btn btn-primary" type="submit" value="Zapisz">
    </div>
  </fieldset>
</form>

<? begin_slot('js') ?>
<script>
$(function()
{
  var taskTemplate = $('#task-template').detach().removeAttr('id')[0].outerHTML;
  var templateVars = ['nr', 'patient', 'patientName', 'patient', 'worktypeName', 'quantity', 'unit', 'price', 'id'];

  var $tasks = $('#tasksTable .tasks');

  var $newTaskNr = $('#newTaskNr').typeahead({
    ajax: {
      method: 'get',
      url: '<?= url_for('/tasks/index.php?perPage=15') ?>',
      triggerLength: 1,
      preProcess: function(data)
      {
        return data.map(function(item, i)
        {
          item.index = i;

          return item;
        });
      }
    },
    matcher: function(item)
    {
      return $tasks.find('[data-id="' + item.id + '"]').length === 0;
    },
    sorter: function(items) { return items; },
    updater: function(i)
    {
      var task = this.ajax.data[i];
      var html = taskTemplate;

      templateVars.forEach(function(templateVar)
      {
        html = html.replace(new RegExp('\\{\\$' + templateVar + '\\}', 'g'), task[templateVar]);
      });

      $('#tasksTable .tasks').append(html);

      $('#tasksTable .empty').hide();

      return '';
    },
    itemRenderer: function(i, task)
    {
      var html = '<em>' + task.nr + '</em><br>' + task.patientName;

      var el = $(this.options.item).attr('data-value', task.index);
      el.find('a').html(html);

      return el[0];
    }
  });

  $newTaskNr.blur(function()
  {
    this.value = '';

    $newTaskNr.data('typeahead').query = '';
  });

  $(window).resize(function()
  {
    $newTaskNr.data('typeahead').$menu.css('min-width', $newTaskNr.outerWidth());
  }).resize();

  $('#tasksTable .tasks').on('click', '.remove-task', function()
  {
    $(this).closest('.task').fadeOut(function()
    {
      $(this).remove();

      if ($('#tasksTable .task').length === 0)
      {
        $('#tasksTable .empty').show().removeClass('hidden');
      }
    });
  });

  $('#editInvoiceForm').submit(function()
  {
    return $(this).find(':focus').attr('id') !== 'newTaskNr';
  });
});
</script>
<? append_slot() ?>
