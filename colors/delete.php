<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['id']));

exec_stmt('DELETE FROM colors WHERE id=?', array(1 => $_GET['id']));

set_flash('Kolor został usunięty pomyślnie!');
go_to("/colors/");
