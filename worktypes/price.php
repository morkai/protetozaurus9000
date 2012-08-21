<?php

include __DIR__ . '/../__common__.php';

bad_request_if(empty($_GET['doctor']) || empty($_GET['worktype']));

$q = <<<SQL
SELECT w.id, w.name, COALESCE(p.price, w.price) AS price
FROM worktypes w
LEFT JOIN worktype_prices p ON w.id=p.worktype
AND p.doctor=:doctor
WHERE w.id=:worktype
LIMIT 1
SQL;

$worktype = fetch_one($q, array(
  ':worktype' => (int)$_GET['worktype'],
  ':doctor' => (int)$_GET['doctor']
));

not_found_if(empty($worktype));

output_json($worktype);
