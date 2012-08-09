<fieldset>
  <div class="row">
    <div class="control-group span3">
      <label for=contact-name class="control-label">Imię i nazwisko:</label>
      <input id=contact-name name=contact[name] class="span3" type=text value="<?= $contact->name ?>" autofocus>
    </div>
    <div class="control-group span3">
      <label for=contact-position class="control-label">Stanowisko:</label>
      <input id=contact-position name=contact[position] class="span3" type=text value="<?= $contact->position ?>">
    </div>
  </div>
  <div class="control-group">
    <label for=contact-company class="control-label">Firma:</label>
    <input id=contact-company name=contact[company] class="span6" type=text value="<?= $contact->company ?>">
  </div>
  <div class="control-group">
    <label for=contact-address class="control-label">Adres:</label>
    <textarea id=contact-address name=contact[address] class="span6" rows=4 cols=10><?= $contact->address ?></textarea>
  </div>
  <div class="row">
    <div class="control-group span3">
      <label for=contact-email class="control-label">Adres e-mail:</label>
      <input id=contact-email name=contact[email] class="span3" type=text value="<?= $contact->email ?>">
    </div>
    <div class="control-group span3">
      <label for=contact-website class="control-label">Strona WWW:</label>
      <input id=contact-website name=contact[website] class="span3" type=text value="<?= $contact->website ?>">
    </div>
  </div>
  <div class="row">
    <div class="control-group span2">
      <label for=contact-telWork class="control-label">Telefon służbowy:</label>
      <input id=contact-telWork name=contact[telWork] class="span2" type=text value="<?= $contact->telWork ?>">
    </div>
    <div class="control-group span2">
      <label for=contact-telMobile class="control-label">Telefon komórkowy:</label>
      <input id=contact-telMobile name=contact[telMobile] class="span2" type=text value="<?= $contact->telMobile ?>">
    </div>
    <div class="control-group span2">
      <label for=contact-telHome class="control-label">Telefon domowy:</label>
      <input id=contact-telHome name=contact[telHome] class="span2" type=text value="<?= $contact->telHome ?>">
    </div>
  </div>
  <div class="row-fluid control-group">
    <label for=contact-comments class="control-label">Uwagi:</label>
    <textarea id=contact-comments name=contact[comments] class="span12" rows="5" cols="10"><?= $contact->comments ?></textarea>
  </div>
  <div class="form-actions">
    <input class="btn btn-primary" type="submit" value="Zapisz">
  </div>
</fieldset>
