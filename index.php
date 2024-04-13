<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KARE - AUTOMATED EXAM SCHEDULE CREATOR</title>
    <link rel="stylesheet" href="./assets/style.css">
    <link rel="stylesheet" href="https://kit-pro.fontawesome.com/releases/v5.15.1/css/pro.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./assets/app.js"></script>
</head>
<?php include "includes/upload-handler.php"; ?>

<body>

    <form enctype="multipart/form-data" method="post" class="upload-container">
        <img class='banner' src="assets/banner.png" alt="">
        <div class="hall-data">
            <h1>Upload Hall data <span class="star">*</span><span>[XLSX]</span>
            </h1>
            <a href="./examples/hall-data.xlsx" class="example">Example Format <i class="fad fa-download"></i></a>
            <input accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" type="file" name="hall-data" id="hall-data" required>
        </div>
        <div class="internal-faculty-data">
            <h1>Upload Internal Faculty data <span class="star">*</span><span>[XLSX]</span>
            </h1>
            <a href="./examples/internal-faculty.xlsx" class="example">Example Format <i class="fad fa-download"></i></a>
            <input accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" type="file" name="internal-faculty-data" required id="internal-faculty-data">
        </div>
        <div class="external-faculty-data">
            <h1>Upload External Faculty data <span class="star">*</span><span>[XLSX]</span>
            </h1>
            <a href="./examples/external-faculty.xlsx" class="example">Example Format <i class="fad fa-download"></i></a>
            <input accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" type="file" name="external-faculty-data" required id="external-faculty-data">
        </div>
        <div class="note">Note: You must upload the exact format as provided in the example format.</div>
        <button type="submit" name="uploader">UPLOAD FILES <i class="fas fa-cloud-upload"></i></button>
    </form>
</body>

</html>