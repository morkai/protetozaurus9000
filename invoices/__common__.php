<?php

include __DIR__ . '/../__common__.php';

function invoices_render_contact_id($company, $name)
{
  if (!empty($company))
  {
    $html = e($company) . '<br>';
  }
  else
  {
    $html = '';
  }

  if (strpos($company, $name) === false)
  {
    $html .= e($name);
  }

  return $html;
}
