<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php" ?>
    <title>Upload Result | Quest Portal</title>
</head>

<body>
    <?php include "header_sidebar.php" ?>
    <div class="main container-fluid">
        <div class="row">
            <!-- Space Left -->
            <div class="col-md-3"></div>
            <!-- Main Content -->
            <div class="col-md-9">
                <style>
                    .uploadCard {
                        border-radius: 14px;
                        box-shadow: 0 1px 1px rgb(0, 0, 0, 0.2)
                    }
                </style>
                <div class="uploadCard my-5">
                    <div class="container p-3">
                        <h1 class="text-start text-primary h3">Upload Results</h1>
                        <div class="uploadForm">
                            <div class="form">
                                <form action="javascript:void(0);" method="post">
                                    <div class="container">
                                        <div class="row">
                                            <!-- 
                                        Database fields are [academic_term, student_admission_number, result_file]
                                            -->
                                            <div class="col-md-6">
                                                <!-- Student Select -->
                                                <div class="students rounded" style="border: 1px solid;">
                                                    <input type="hidden" id="student_admission_number"
                                                        name="student_admission">
                                                    <div class="studentsSearch">
                                                        <input type="text" placeholder="Type here to search students"
                                                            class="form-control">
                                                        <div class="table-responsive" style="overflow-y: scroll; height: 300px">
                                                            <div
                                                            class="studentResults p-3 border rounded text-center">

                                                        </div>
                                                        </div>
                                                        <script>
                                                            let studentSearch = document.querySelector(".studentsSearch");
                                                            let searchInput = studentSearch.querySelector("input");
                                                            let studentResults = studentSearch.querySelector(".studentResults");

                                                            fetch("ajax_data_for_students.php")
                                                                .then(res => res.json())
                                                                .then(data => {
                                                                    data.forEach(student => {
                                                                        studentResults.innerHTML += `
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <div class="d-grid">
                                                                                <img class="rounded-circle" width="70" src="../${student.picture}">
                                                                                <p class="p-0 m-0 fs-6 h6 fw-bold">${student.class.toUpperCase()}</p>
                                                                            </div>
                                                                            <div class="fw-bold text-center">${student.fullname}</div>
                                                                            <div class="d-grid">
                                                                                <h1 class="p-0 m-0 h6 fs-6">Adm. #</h1>
                                                                                <small>${student.admission_number}</small>
                                                                            </div>
                                                                        </div>`;
                                                                    })

                                                                    searchInput.addEventListener("input", function () {
                                                                        const query = searchInput.value.toLowerCase();
                                                                        studentResults.innerHTML = "";

                                                                        if (query === "") {
                                                                            studentResults.innerHTML = "";
                                                                        }

                                                                        data.forEach(student => {

                                                                            if (student.fullname.toLowerCase().includes(query)) {
                                                                                studentResults.innerHTML += `
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                            <div class="d-grid">
                                                                                <img class="rounded-circle" width="70" src="../${student.picture}">
                                                                                <p class="p-0 m-0 fs-6 h6 fw-bold">${student.class.toUpperCase()}</p>
                                                                            </div>
                                                                            <div class="fw-bold text-center">${student.fullname}</div>
                                                                            <div class="d-grid">
                                                                                <h1 class="p-0 m-0 h6 fs-6">Adm. #</h1>
                                                                                <small>${student.admission_number}</small>
                                                                            </div>
                                                                        </div>
                                                                                `;
                                                                            }
                                                                        });
                                                                    });

                                                                });

                                                        </script>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Academic Term -->
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text">Academic Term</span>
                                                    <input type="text" placeholder="eg. First Term 2025/2026"
                                                        class="form-control" name="academic_term">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="input-group">
                                                <span class="input-group-text">Result URL:</span>
                                                <input type="url"
                                                    placeholder="eg. https://docs.google.com/spreadsheets/d/17vy-_nifUOAGizuX_OdwlcKrjdZfBL0xO_eBhQ_JO6o/edit?usp=sharing"
                                                    class="form-control" name="result_file" id="result_url">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-outline-success">Upload
                                                    Result</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>