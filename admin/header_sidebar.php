<style>
    /* Dark Mode */
    body[data-theme='dark'] {
        background-color: #000;
    }

    body[data-theme='dark'] header {
        box-shadow: 0 1px 1px rgb(255, 255, 255);
    }

    body[data-theme='dark'] .navbar {
        background: #000;
        color: #fff;
    }

    body[data-theme='dark'] .admin-header-profile-picture-text {
        color: #fff;
    }

    /* Dark Mode Ends */
    * {
        font-family: Montserrat;
    }

    .navbar {
        background: #fff;
        color: #000;
        padding: 0.5rem 1rem;

    }

    header {
        box-shadow: 0 1px 1px rgb(0, 0, 0, 0.2);
    }

    header>* {
        color: #000;
    }

    .navbar .logo h1 {
        color: #000;
        font-size: 1.5rem;
        margin: 0;
    }

    .nav-links {
        list-style: none;
        display: flex;
        gap: 1rem;
        margin: 0;
    }

    .nav-links li a {
        color: #000;
        text-decoration: none;
        font-weight: 500;
    }

    .sidebar {
        box-shadow: 0 1px 1px rgb(0, 0, 0, 0.2);
        background: #fff;
        color: #000;
        min-width: 220px;
        max-width: 220px;
        position: fixed;
        top: 56px;
        left: 0;
        padding-top: 1rem;
        transition: transform 0.3s ease;
        z-index: 1040;
        overflow-y: scroll;
        border-top: 1px solid;
        border-top-color: #000;
    }

    /* Dark Mode Sidebar */
    body[data-theme='dark'] .sidebar {
        background: #000;
        color: #fff;
        border-top-color: #fff;
        box-shadow: 0 2px 2px rgb(255, 255, 255);
    }

    body[data-theme='dark'] .sidebar ul li a {
        color: #fff;
    }

    /* Sidebar Dark Mode Ends */

    .sidebar ul {
        list-style: none;
        padding-left: 0;
    }

    .sidebar ul li a {
        font-size: 0.95rem;
        color: #000;
        display: block;
        padding: 0.75rem 1.5rem;
        text-decoration: none;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .sidebar ul li a:hover {
        background: rgb(0, 0, 0, 0.2);
    }

    .sidebar-header {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
    }

    .sidebar-toggler {
        display: none;
        background: none;
        border: none;
        color: #000;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .search-container {
        box-shadow: 0 1px 1px rgb(0, 0, 0, 0.2);
        border-radius: 10px;
        position: relative;
    }

    .search-results {
        top: 100%;
        left: 0;
        right: 0;
        margin-top: 2px;
    }

    .search-results .search-item {
        padding: 0.5rem 1rem;
        cursor: pointer;
        color: #000;
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s;
    }

    .search-results .search-item a {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .search-results .search-item:hover {
        background-color: #f8f9fa;
    }

    .search-results .search-item:last-child {
        border-bottom: none;
    }

    .search-results .search-category {
        font-weight: bold;
        color: #000000ff;
        padding: 0.25rem 1rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .admin-header-profile-picture-text {
        color: #000;
    }

    @media (max-width: 1251px) {
        .sidebar {
            transform: translateX(-100%);
            position: fixed;
            left: 0;
            top: 56px;
            height: calc(100vh - 56px);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .sidebar-toggler {
            display: inline-block;
        }


    }

    @media(max-width:1024px) {
        .search-container {
            display: none;
        }
    }
</style>
<header class="position-sticky top-0 w-100" style="z-index: 1030;">
    <nav class="navbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <button class="sidebar-toggler" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <div class="logo">
                <img src="assets/images/quest.jpg" width="40" alt="">
            </div>
        </div>
        <div class="search-container flex-grow-1 mx-3" style="max-width: 400px;">
            <div class="input-group">
                <input type="text" class="form-control" id="globalSearch"
                    placeholder="Search students, classes..." autocomplete="off">
                <button class="btn btn-outline-success" type="button" id="searchBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div id="searchResults" class="search-results position-absolute bg-white border rounded shadow-sm"
                style="display: none; z-index: 1050; max-height: 300px; overflow-y: auto; width: 100%;"></div>
        </div>
        <ul class="nav-links d-none d-md-flex">
            <li><i class="bi bi-envelope mx-4"></i><?php echo $adminData["email"] ?></li>
            <li class="nav-item dropdown">
                <button class="btn nav-link dropdown-toggle d-flex align-items-center gap-2 p-0" id="adminDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false" type="button">
                    <img src="<?php echo htmlspecialchars($adminData["picture"] ?? 'assets/images/quest.jpg'); ?>"
                        alt="Profile Picture" class="rounded-circle" width="25">
                    <span
                        class="d-none d-md-inline ms-2 admin-header-profile-picture-text"><?php echo htmlspecialchars($adminData["fullname"] ?? 'Admin'); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                    <li><a class="dropdown-item text-primary" href="profile.php"><i
                                class="bi bi-person me-2"></i>Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-primary" href="logout.php"><i
                                class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3 class="text-uppercase text-start fs-5">DASHBOARD</h3>
    </div>
    <ul>
        <li><a href="./"><i class="fas fa-home me-2"></i>Overview</a></li>
        <li data-bs-toggle="collapse" data-bs-target="#studentManagement"><a href="javascript:;"><i
                    class="fas fa-user-graduate me-2"></i>Students Management</a></li>
        <div class="collapse" id="studentManagement">
            <?php
            if ($adminData["staff_role"] == "head admin" || $adminData["staff_role"] == "admin") {
            ?>
                <li><a class="text-primary" href="students.php"><i class="bi bi-people me-2"></i>View all students</a></li>
            <?php
            }
            ?>
            <li><a class="text-primary" href="manage_results.php"><i class="bi bi-clipboard-check me-2"></i>Manage
                    Results</a></li>
            <li><a class="text-primary" href="support_requests.php"><i class="bi bi-headset me-2"></i>Support
                    Requests</a></li>
        </div>
        <?php
        if ($adminData["staff_role"] == "head admin" || $adminData["staff_role"] == "admin") {
        ?>
            <li data-bs-toggle="collapse" data-bs-target="#staffManagement"><a href="javascript:;"><i
                        class="bi bi-people-fill me-2"></i>Users Management</a></li>
            <div class="collapse" id="staffManagement">
                <li><a class="text-primary" href="staff_management.php"><i class="bi bi-people me-2"></i>View all users</a>
                </li>
                <li><a class="text-primary" href="add_staff.php"><i class="bi bi-plus me-2"></i>CREATE user</a></li>
            </div>
        <?php
        }
        ?>
        <?php
        if ($adminData["staff_role"] == "head admin" || $adminData["staff_role"] == "admin") {
        ?>
            <li data-bs-toggle="collapse" data-bs-target="#systemManagement"><a href="javascript:;"><i
                        class="fas fa-cogs me-2"></i>System Management</a></li>
            <div class="collapse" id="systemManagement">
                <li><a class="text-primary" href="add_notification.php"><i class="bi bi-bell me-2"></i>Add Notification</a>
                </li>
                <!-- <li><a class="text-primary" href="upload_material.php"><i class="bi bi-upload me-2"></i>Upload Materials</a>
            </li> -->
                <li><a class="text-primary" href="add_event.php"><i class="bi bi-calendar-event me-2"></i>Add Event</a></li>
            </div>
        <?php
        }
        ?>
    </ul>
    <div class="sidebar-header">
        <h3 class="text-uppercase text-start fs-5">PROFILE</h3>
    </div>
    <ul>
        <li data-bs-toggle="collapse" data-bs-target="#profileSettings"><a href="javascript:;"><i
                    class="bi bi-person-fill me-2"></i>Profile</a></li>
        <div class="collapse" id="profileSettings">
            <li><a class="text-primary" href="profile.php"><i class="bi bi-person-circle"></i>View profile</a></li>
            <li><a class="text-primary" href="login.php"><i class="bi bi-box-arrow-left"></i>Login</a></li>
            <li><a class="text-primary" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
        </div>
    </ul>
</aside>
<script>
    // Sidebar toggler functionality
    const header = document.querySelector('header');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    sidebar.style.height = `calc(100vh - ${header.offsetHeight}px)`;
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    // Optional: Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 1024) {
            if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        }
    });

    // Global Search Functionality
    const globalSearch = document.getElementById('globalSearch');
    const searchResults = document.getElementById('searchResults');
    const searchBtn = document.getElementById('searchBtn');
    let searchTimeout;

    function displaySearchResults(data, query) {
        searchResults.innerHTML = '';

        if (data.students && data.students.length > 0) {
            searchResults.innerHTML += '<div class="search-category">Students</div>';
            data.students.forEach(student => {
                const item = document.createElement('div');
                item.className = 'search-item';
                item.innerHTML = `<a href="view_student.php?id=${student.id}"><i class="bi bi-person me-2"></i>${highlightText(student.fullname, query)} <small class="text-muted">(${student.class})</small></a>`;
                searchResults.appendChild(item);
            });
        }

        /*if (data.staff && data.staff.length > 0) {
            searchResults.innerHTML += '<div class="search-category">Staff</div>';
            data.staff.forEach(staff => {
                const item = document.createElement('div');
                item.className = 'search-item';
                item.innerHTML = `<a href="view_staff.php?id=${staff.id}"><i class="bi bi-person-badge me-2"></i>${highlightText(staff.fullname, query)} <small class="text-muted">(${staff.staff_role})</small></a>`;
                searchResults.appendChild(item);
            });
        }*/

        if (data.classes && data.classes.length > 0) {
            searchResults.innerHTML += '<div class="search-category">Classes</div>';
            data.classes.forEach(cls => {
                const item = document.createElement('div');
                item.className = 'search-item';
                item.innerHTML = `<a href="view_class.php?id=${cls.id}"><i class="bi bi-journal-bookmark me-2"></i>${highlightText(cls.class_name, query)}</a>`;
                searchResults.appendChild(item);
            });
        }

        searchResults.style.display = searchResults.innerHTML ? 'block' : 'none';
    }

    // Function to perform search
    function performSearch(query) {
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        fetch('search_handler.php', {
                method: 'POST',
                credentials: 'same-origin', // send cookies so PHP session is available
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'query=' + encodeURIComponent(query)
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 401) {
                        // If session expired or not logged in, redirect to login page
                        window.location.href = 'login.php';
                    }
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                displaySearchResults(data, query);
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.style.display = 'none';
            });
    }

    // Function to highlight search text
    function highlightText(text, query) {
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    // Function to display search results




    // Search input event listeners
    globalSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(this.value);
        }, 300);
    });

    globalSearch.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            performSearch(this.value);
        }
    });

    searchBtn.addEventListener('click', function() {
        performSearch(globalSearch.value);
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(event) {
        if (!globalSearch.contains(event.target) && !searchResults.contains(event.target) && !searchBtn.contains(event.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Handle Enter key
    globalSearch.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            performSearch(this.value);
        }
    });

    // Programmatic fallback for dropdown toggles
    // In some environments the data-api may not initialize (or JS errors stop it). This ensures dropdowns still work.
    try {
        document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                // Prevent default anchor behavior
                if (e) e.preventDefault();
                // Use Bootstrap's Dropdown API if available
                if (window.bootstrap && window.bootstrap.Dropdown) {
                    window.bootstrap.Dropdown.getOrCreateInstance(toggle).toggle();
                }
            });
        });
    } catch (err) {
        // If this throws, log for debugging but don't break other scripts
        console.error('Dropdown init fallback error:', err);
    }
</script>