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
    <label for=task-closeDate class="control-label">Czas oddania:</label>
    <input id=task-closeDate name=task[closeDate] class="span3" type=datetime value="<?= $task->closeDate ?>" placeholder="YYYY-MM-DD HH:mm">
  </div>
</div>
<div class="control-group">
  <label for=task-doctorName class="control-label">Lekarz:</label>
  <input id=task-doctorName class="span6" type=text value="<?= $task->doctorName ?>">
  <input id=task-doctor name=task[doctor] type=hidden value="<?= $task->doctor ?>">
</div>
<div class="control-group">
  <label for=task-patient class="control-label">Pacjent:</label>
  <textarea id=task-patient name=task[patient] class="span6" rows="4"><?= $task->patient ?></textarea>
</div>
<div class="row">
  <div class="span6">
    <div class="control-group">
      <label for=task-worktype class="control-label">Typ pracy:</label>
      <select id=task-worktype name=task[worktype] class="span6">
        <?= render_options($worktypes, $task->worktype) ?>
      </select>
    </div>
    <div class="row">
      <div class="control-group span2">
        <label for=task-quantity class="control-label">Ilość:</label>
        <input id=task-quantity name=task[quantity] class="span2" type=text value="<?= $task->quantity ?>">
      </div>
      <div class="control-group span2">
        <label for=task-unit class="control-label">Jednostka:</label>
        <input id=task-unit name=task[unit] class="span2" type=text value="<?= $task->unit ?>">
      </div>
      <div class="control-group span2">
        <label for=task-price class="control-label">Cena:</label>
        <input id=task-price name=task[price] class="span2" type=text value="<?= $task->price ?>">
      </div>
    </div>
  </div>
  <div class="span6">
    <? tasks_render_teeth($task->teeth) ?>
  </div>
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
<script src="<?= url_for("__assets__/js/typeaheadContacts.js") ?>"></script>
<script>
$(function()
{
  var chosenOptions = {
    no_results_text: 'Brak wyników.'
  };

  $('#task-worktype').chosen(chosenOptions);
  $('#task-color').chosen(chosenOptions);

  $('#task-doctorName').typeaheadContacts({
    url: '<?= url_for('/contacts/index.php?perPage=15') ?>'
  });

  $(window).resize();

  var priceRefresher = null;
  var $price = $('#task-price');

  function changePrice(newPrice)
  {
    $price.fadeOut(function()
    {
      $price.val(newPrice).fadeIn();
    });
  }

  function refreshPrice()
  {
    var doctor = parseInt($('#task-doctor').val());
    var worktype = parseInt($('#task-worktype').val());

    if (priceRefresher !== null)
    {
      priceRefresher.abort();
    }

    priceRefresher = $.ajax({
      type: 'GET',
      url: '<?= url_for('/worktypes/price.php') ?>',
      data: {doctor: doctor, worktype: worktype},
      success: function(data)
      {
        changePrice(data.price);
      },
      error: changePrice.bind(null, '0.00'),
      complete: function()
      {
        priceRefresher = null;
      }
    });
  }

  $('#task-doctor').change(refreshPrice);
  $('#task-worktype').change(refreshPrice);

});
</script>
<? append_slot() ?>
