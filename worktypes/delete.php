<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

try
{
  exec_stmt('DELETE FROM worktypes WHERE id=?', array(1 => $_GET['id']));

  set_flash('Typ prac został usunięty pomyślnie!');
  go_to("/worktypes/");
}
catch (PDOException $x)
{
  set_flash("Nie udało się usunąć typu prac: {$x->getMessage()}", 'error');
  go_to(get_referer("/worktypes/view.php?id={$_GET['id']}"));
}
