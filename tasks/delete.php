<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

$task = fetch_one('SELECT id, closed FROM tasks WHERE id=? LIMIT 1', array(1 => $_GET['id']));

not_found_if(empty($task));

bad_request_if($task->closed);

exec_stmt('DELETE FROM tasks WHERE id=?', array(1 => $_GET['id']));

set_flash('Zadanie zostało usunięte pomyślnie!');
go_to("/tasks/");
