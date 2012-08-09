<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

exec_stmt('DELETE FROM tasks WHERE id=?', array(1 => $_GET['id']));

set_flash('Zadanie zostało usunięte pomyślnie!');
go_to("/tasks/");
