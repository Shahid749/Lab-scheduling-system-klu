<?php

function showErrorMsg($msg = "Something went wrong!")
{
    $res['status'] = false;
    $res['errorMsg'] = $msg;
    die(json_encode($res, 1));
}
function showSuccessMsg($msg, $uploadId = 000)
{
    $res['status'] = true;
    $res['message'] = $msg;
    $res['uploadId'] = $uploadId;
    die(json_encode($res, 1));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadErrors = [];
    $allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    if (isset($_FILES['hall-data']) && isset($_FILES['internal-faculty-data']) && isset($_FILES['external-faculty-data'])) {
        $hallDataFile = $_FILES['hall-data'];
        $internalFacultyFile = $_FILES['internal-faculty-data'];
        $externalFacultyFile = $_FILES['external-faculty-data'];
        if ($hallDataFile['error'] === 0 && $internalFacultyFile['error'] === 0 && $externalFacultyFile['error'] === 0) {
            if (array_reduce([$hallDataFile, $internalFacultyFile, $externalFacultyFile], function ($carry, $item) use ($allowedTypes) {
                return $carry && in_array($item['type'], $allowedTypes);
            }, true)) {
                $uploadId = rand(111111, 999999);
                $hallDataFileName = "$uploadId-hall-data.xlsx";
                $internalFacultyFileName = "$uploadId-internal-faculty.xlsx";
                $externalFacultyFileName = "$uploadId-external-faculty.xlsx";
                array_map(function ($file, $fileName) use ($uploadId) {
                    move_uploaded_file($file['tmp_name'], "../../uploads/$fileName");
                }, [$hallDataFile, $internalFacultyFile, $externalFacultyFile], [$hallDataFileName, $internalFacultyFileName, $externalFacultyFileName]);
                showSuccessMsg("Files uploaded successfully!", $uploadId);
            } else showErrorMsg("Invalid file type!");
        } else showErrorMsg("File upload failed!");
    } else showErrorMsg("No files uploaded!");
} else showErrorMsg();
