<?php

include __DIR__ . '/../__common__.php';

function tasks_prepare_data($data)
{
  $data['startDate'] = strtotime($data['startDate']);
  $data['closeDate'] = strtotime($data['closeDate']);

  if (empty($data['startDate']))
  {
    $data['startDate'] = time();
  }

  if (empty($data['closeDate']))
  {
    $data['closeDate'] = 0;
  }

  $data['doctor'] = empty($data['doctor']) ? null : (int)$data['doctor'];
  $data['patient'] = empty($data['patient']) ? null : (int)$data['patient'];
  $data['teeth'] = empty($data['teeth']) ? '' : implode(',', $data['teeth']);

  return $data;
}

function tasks_render_teeth($teeth, $readonly = false)
{
  static $TEETH = array(
    'upper' => array(17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27),
    'lower' => array(47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37)
  );

  if (is_string($teeth))
  {
    $teeth = array_flip(explode(',', $teeth));
  }

  include __DIR__ . '/__teeth__.php';
}
