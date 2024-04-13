<?php
error_reporting(0);

function showError($msg = "Something went wrong!")
{
    $res['status'] = false;
    $res['errorMsg'] = $msg;
    die(json_encode($res, 1));
}

function showSuccess($msg = "Successful!", $data = [])
{
    $res['status'] = true;
    $res['message'] = $msg;
    $res['data'] = $data;
    die(json_encode($res, 1));
}

$uploadId = $_GET['id'];
if ($uploadId == "") showError();

if (!is_numeric($uploadId) || strlen($uploadId) > 6) showError("Invalid Id Given!");

$uploads = scandir("../../uploads");
if (!in_array($uploadId . "-hall-data.xlsx", $uploads)) showError("Given Id didn't exist!");

$res = trim(shell_exec("python gen.py $uploadId"));
if ($res == "") showError("Generation Failed!");
[$status, $msg] = explode("|", $res);
if ($status == "failed") showError($msg);
else if ($status == "success") {
    $data['pdfLink'] = $msg;
    showSuccess("PDF Generated Successfully!", $data);
} else showError();
