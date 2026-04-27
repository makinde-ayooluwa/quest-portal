<?php

session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mail Management</title>
    <?php include "head.php" ?>
</head>

<body>

    <?php include "settings.php" ?>
    <?php include "header_sidebar.php" ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-9 main-content">
                <!-- CONTENT -->
                <div class="p-3">
                    <div class="d-flex justify-content-between">
                        <h1>Mails</h1>
                        <button class="btn btn-primary py-0" data-bs-toggle="modal" data-bs-target="#new-mail-modal">Create new</button>
                        <!-- Modal -->
                        <div class="modal fade" id="new-mail-modal" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Create new mail</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="javascript:void(0);">
                                        <div class="modal-body">
                                            <label for="mail-for" class="form-label">Mail for</label>
                                            <select name="mail-for" id="mail-for" class="form-select">
                                                <option value="overall">Overall</option>
                                                <option value="student">Students</option>
                                                <option value="staff">Staffs</option>
                                            </select>
                                            <label for="mail-text">Mail text</label>
                                            <div class="text-editor"></div>
                                            <textarea name="mail-text"  id="mail-text" class="form-control">
                                            </textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Create</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3">
                        <!-- Tab -->
                        <ul class="nav nav-tabs mb-3" id="mailTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="overall-tab" data-bs-toggle="tab" href="#overall">Overall</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="student-tab" data-bs-toggle="tab" href="#student">Students</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="staff-tab" data-bs-toggle="tab" href="#staff">Staffs</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="overall" role="tabpanel">
                                No overall mail
                            </div>
                            <div class="tab-pane fade show" id="student" role="tabpanel">
                                No student mail
                            </div>
                            <div class="tab-pane fade show" id="staff" role="tabpanel">
                                No staff mail
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>