<table class=teeth>
  <tbody>
    <tr class="teeth-upper-values">
      <? foreach ($TEETH['upper'] as $tooth): ?>
      <td><input id=teeth-<?= $tooth ?> type="checkbox" name="task[teeth][]" value="<?= $tooth ?>" <?= checked_if(isset($teeth[$tooth])) ?> <?= $readonly ? 'disabled' : '' ?>>
      <? endforeach ?>
    <tr class="teeth-upper-labels">
      <? foreach ($TEETH['upper'] as $tooth): ?>
      <th><label for=teeth-<?= $tooth ?>><?= $tooth ?></label>
      <? endforeach ?>
    <tr class="teeth-lower-labels">
      <? foreach ($TEETH['lower'] as $tooth): ?>
      <th><label for=teeth-<?= $tooth ?>><?= $tooth ?></label>
      <? endforeach ?>
    <tr class="teeth-lower-values">
      <? foreach ($TEETH['lower'] as $tooth): ?>
      <td><input id=teeth-<?= $tooth ?> type="checkbox" name="task[teeth][]" value="<?= $tooth ?>" <?= checked_if(isset($teeth[$tooth])) ?> <?= $readonly ? 'disabled' : '' ?>>
      <? endforeach ?>
  </tbody>
</table>
