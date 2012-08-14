<div class="control-group">
  <label for=task-name class="control-label">Numer zadania:</label>
  <input id=task-name name=task[nr] class="span3" type=text value="<?= $task->nr ?>" autofocus>
</div>
<div class="row">
  <div class="control-group span3">
    <label for=task-startDate class="control-label">Data rozpoczęcia:</label>
    <input id=task-startDate name=task[startDate] class="span3" type=date value="<?= $task->startDate ?>">
  </div>
  <div class="control-group span3">
    <label for=task-closeDate class="control-label">Data oddania:</label>
    <input id=task-closeDate name=task[closeDate] class="span3" type=date value="<?= $task->closeDate ?>">
  </div>
</div>
<div class="control-group">
  <label for=task-doctorName class="control-label">Lekarz:</label>
  <input id=task-doctorName class="span6" type=text value="<?= $task->doctorName ?>">
  <input id=task-doctor name=task[doctor] type=hidden value="<?= $task->doctor ?>">
</div>
<div class="control-group">
  <label for=task-patientName class="control-label">Pacjent:</label>
  <input id=task-patientName class="span6" type=text value="<?= $task->patientName ?>">
  <input id=task-patient name=task[patient] type=hidden value="<?= $task->patient ?>">
</div>
<div class="control-group">
  <label for=task-worktype class="control-label">Typ pracy:</label>
  <select id=task-worktype name=task[worktype] class="span6">
    <?= render_options($worktypes, $task->worktype) ?>
  </select>
</div>
<div class="control-group">
  <label for=task-quantity class="control-label">Ilość:</label>
  <select id=task-quantity name=task[quantity] class="span6">
    <?= render_options($quantities, $task->quantity) ?>
  </select>
</div>
<div class="control-group">
  <label for=task-color class="control-label">Kolor:</label>
  <select id=task-color name=task[color] class="span6">
    <?= render_options($colors, $task->color) ?>
  </select>
</div>
<div class="control-group">
  <label for=task-notes class="control-label">Uwagi:</label>
  <textarea id=task-notes name=task[notes] class="span12" rows="5" cols="10"><?= $task->notes ?></textarea>
</div>
<div class="form-actions">
  <input class="btn btn-primary" type="submit" value="Zapisz">
</div>

<? begin_slot('js') ?>
<script>
$(function()
{
  var chosenOptions = {
    no_results_text: 'Brak wyników.'
  };

  $('#task-worktype').chosen(chosenOptions);
  $('#task-quantity').chosen(chosenOptions);
  $('#task-color').chosen(chosenOptions);

  var typeaheadOptions = {
    ajax: {
      method: 'get',
      url: '<?= url_for("/contacts/index.php?perPage=15") ?>'
    },
    matcher: function() { return true; },
    sorter: function(items) { return items; },
    updater: function(i)
    {
      var contact = this.ajax.data[i] || {
        id: 0,
        name: ''
      };

      this.$element.next().val(contact.id);

      return contact.name;
    },
    itemRenderer: function(i, contact)
    {
      var html = '<em>' + contact.name + '</em>';

      if (contact.company !== '')
      {
        html += '<br>' + contact.company;
      }

      i = $(this.options.item).attr('data-value', i);
      i.find('a').html(html);

      return i[0]
    }
  };

  var $doctor = $('#task-doctorName').typeahead(typeaheadOptions);
  var $patient = $('#task-patientName').typeahead(typeaheadOptions);

  $(window).resize(function()
  {
    $doctor.data('typeahead').$menu.css('min-width', $doctor.outerWidth());
    $patient.data('typeahead').$menu.css('min-width', $patient.outerWidth());
  }).resize();

});
</script>
<? append_slot() ?>
