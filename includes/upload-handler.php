<?php
function showErrorMsg($msg)
{
    echo "<script>window.onload = () => showError(`$msg`)</script>";
}

function showSuccessMsg($msg, $uploadId)
{
    echo "<script>window.onload = () => generatePDF(`$msg`, `$uploadId`);</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["uploader"])) {
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
                    move_uploaded_file($file['tmp_name'], "./uploads/$fileName");
                }, [$hallDataFile, $internalFacultyFile, $externalFacultyFile], [$hallDataFileName, $internalFacultyFileName, $externalFacultyFileName]);
                showSuccessMsg("[#$uploadId] Files uploaded successfully!", $uploadId);
            } else showErrorMsg("Invalid file type!");
        } else showErrorMsg("File upload failed!");
    } else showErrorMsg("No files uploaded!");
}
