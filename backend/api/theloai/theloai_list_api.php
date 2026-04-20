<?php
require_once(__DIR__ . '/../../model/TheLoaiModel.php');

header("Content-Type: application/json; charset=UTF-8");

$model = new TheLoaiModel();
$data = $model->getAll(); 

echo json_encode([
    "success" => true,
    "data" => $data
], JSON_UNESCAPED_UNICODE);