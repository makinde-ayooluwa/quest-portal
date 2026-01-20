<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Support & Help Desk - Quest Schools</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .support-header {
            background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-yellow) 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .support-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            margin-bottom: 2rem;
            border: none;
        }

        .form-control:focus {
            border-color: var(--quest-green);
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }

        .btn-grad {
            background: linear-gradient(90deg, var(--quest-green) 60%, var(--quest-yellow) 100%);
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-grad:hover {
            background: linear-gradient(90deg, var(--quest-yellow) 60%, var(--quest-green) 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .faq-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--quest-green);
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .faq-item strong {
            color: var(--quest-green);
            font-size: 1.1rem;
        }

        .contact-info {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
            border: 1px solid #dee2e6;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .contact-item i {
            color: var(--quest-green);
            font-size: 1.5rem;
            margin-right: 1rem;
            width: 30px;
        }

        .support-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .option-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        .option-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .option-card i {
            font-size: 2rem;
            color: var(--quest-green);
            margin-bottom: 1rem;
        }

        .option-card h5 {
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .option-card p {
            color: #6c757d;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .support-card {
                padding: 1.5rem;
            }

            .support-header {
                padding: 2rem 0;
            }
        }
    </style>
</head>

<body>
    <?php
    if (isset($_SESSION["error"])) {
        ?>
        <script>
            toastr.error("<?php echo $_SESSION["error"] ?>", "Error!");
        </script>
        <?php
        unset($_SESSION["error"]);
    } elseif (isset($_SESSION["success"])) {
        ?>
        <script>
            toastr.success("<?php echo $_SESSION["success"] ?>", "Success!");
        </script>
        <?php
        unset($_SESSION["success"]);
    }
    ?>
    <!-- Support Header -->
    <div class="support-header">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-4 mb-3"><i class="bi bi-headset me-3"></i>Support & Help Desk</h1>
                    <p class="lead mb-0">We're here to help! Get assistance with any issues or questions you may have.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Support Options -->
        <div class="support-options">
            <div class="option-card">
                <i class="bi bi-envelope-paper"></i>
                <h5>Submit a Request</h5>
                <p>Fill out our support form below to get help with your specific issue.</p>
            </div>
            <div class="option-card">
                <i class="bi bi-question-circle"></i>
                <h5>FAQ</h5>
                <p>Check our frequently asked questions for quick answers.</p>
            </div>
            <div class="option-card">
                <i class="bi bi-telephone"></i>
                <h5>Contact Info</h5>
                <p>Find our contact details and support hours below.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Support Form -->
                <div class="support-card">
                    <h3 class="mb-4"><i class="bi bi-send me-2"></i>Submit Support Request</h3>
                    <form action="support_request.php" method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="topic" class="form-label fw-bold">Topic <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="topic" name="topic" required>
                                    <option value="">Select Topic</option>
                                    <option value="assignment">📝 Assignment Issue</option>
                                    <option value="result">📊 Result/Grade Query</option>
                                    <!-- <option value="attendance">📅 Attendance Concern</option> -->
                                    <!-- <option value="scholarship">🎓 Scholarship/Program</option> -->
                                    <option value="profile">👤 Profile Update</option>
                                    <option value="technical">💻 Technical Problem</option>
                                    <option value="other">❓ Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label fw-bold">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-bold">Subject <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                placeholder="Brief description of your issue" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">Describe Your Issue <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="4"
                                placeholder="Please provide detailed information about your issue..."
                                required></textarea>
                        </div>
                        <div class="row">
                            <!-- <div class="col-md-6 mb-3">
                                <label for="contact" class="form-label fw-bold">Contact Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="contact" name="contact" placeholder="your.email@example.com" required>
                            </div> -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-bold">Phone Number (Optional)</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    placeholder="+1 (555) 123-4567">
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-outline-secondary me-md-2 align-middle">
                                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-grad">
                                <input type="hidden" name="submit">
                                <i class="bi bi-send me-1"></i>Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- FAQ Section -->
                <div class="support-card">
                    <h4 class="mb-4"><i class="bi bi-info-circle me-2"></i>Frequently Asked Questions</h4>
                    <div class="faq-item">
                        <strong>How do I download my assignments?</strong>
                        <div class="mt-2">Go to the Assignments page and click the download button next to each
                            assignment.</div>
                    </div>
                    <div class="faq-item">
                        <strong>How can I update my profile information?</strong>
                        <div class="mt-2">Visit your Profile page and click the "Blue Pencil" button to update your
                            details.</div>
                    </div>
                    <!-- <div class="faq-item">
                        <strong>Who do I contact for scholarship queries?</strong>
                        <div class="mt-2">Use this support form and select "Scholarship/Program" as your topic, or contact your school admin.</div>
                    </div> -->
                    <div class="faq-item">
                        <strong>How do I reset my password?</strong>
                        <div class="mt-2">Click the "forgot password" link in the login page and follow the other steps.
                        </div>
                    </div>
                    <!-- <div class="faq-item">
                        <strong>How can I view my attendance records?</strong>
                        <div class="mt-2">Go to your Profile page and navigate to the Attendance section.</div>
                    </div> -->
                </div>

                <!-- Contact Information -->
                <div class="contact-info">
                    <h5 class="mb-3"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
                    <div class="contact-item">
                        <i class="bi bi-envelope"></i>
                        <div>
                            <strong>Email:</strong><br>
                            <a href="mailto:info@questschools.org">info@questschools.org</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-telephone"></i>
                        <div>
                            <strong>Phone:</strong><br>
                            +234 704 761 8656
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-clock"></i>
                        <div>
                            <strong>Support Hours:</strong><br>
                            Mon-Fri: 8:00 AM - 6:00 PM<br>
                            Sat: 9:00 AM - 2:00 PM
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-geo-alt"></i>
                        <div>
                            <strong>Response Time:</strong><br>
                            Within 24 hours for normal requests<br>
                            Within 4 hours for urgent issues
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prevent right-click context menu
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        // Form validation enhancement
        document.querySelector('form').addEventListener('submit', function (e) {
            const topic = document.getElementById('topic').value;
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            const contact = document.getElementById('contact').value.trim();

            if (!topic || !subject || !message || !contact) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>

</html>