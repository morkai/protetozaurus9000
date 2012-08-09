<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

exec_stmt('DELETE FROM quantities WHERE id=?', array(1 => $_GET['id']));

set_flash('Ilość została usunięta pomyślnie!');
go_to("/quantities/");
