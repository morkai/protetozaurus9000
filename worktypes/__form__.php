<fieldset>
  <div class="control-group">
    <label for=worktype-name class="control-label">Nazwa:</label>
    <input id=worktype-name name=worktype[name] class="span6" type=text value="<?= $worktype->name ?>" <?= empty($doctor) ? 'autofocus' : 'readonly' ?>>
  </div>
  <div class="control-group">
    <label for=worktype-price class="control-label"><?= empty($doctor) ? 'Domyślna cena:' : 'Cena:' ?></label>
    <div class="input-append">
      <input id=worktype-price name=worktype[price] class="span2" type=text value="<?= $worktype->price ?>" <?= empty($doctor) ? '' : 'autofocus' ?>><span class="add-on">zł</span>
    </div>
  </div>
  <div class="form-actions">
    <input class="btn btn-primary" type="submit" value="Zapisz">
  </div>
</fieldset>
