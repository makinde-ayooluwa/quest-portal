<style>
    * {
        font-family: Montserrat;
    }

    .navbar {
        background: #343a40;
        color: #fff;
        padding: 0.5rem 1rem;
    }

    .navbar .logo h1 {
        color: #fff;
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
        color: #fff;
        text-decoration: none;
        font-weight: 500;
    }

    .sidebar {
        background: #212529;
        color: #fff;
        min-width: 220px;
        max-width: 220px;
        position: fixed;
        top: 56px;
        left: 0;
        padding-top: 1rem;
        transition: transform 0.3s ease;
        z-index: 1040;
        overflow-y: scroll;
    }

    .sidebar ul {
        list-style: none;
        padding-left: 0;
    }

    .sidebar ul li a {
        font-size: 0.95rem;
        color: #fff;
        display: block;
        padding: 0.75rem 1.5rem;
        text-decoration: none;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .sidebar ul li a:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .sidebar-header {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
    }

    .sidebar-toggler {
        display: none;
        background: none;
        border: none;
        color: #fff;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .search-container {
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

    .main-content {
        margin-left: 220px;
        padding: 2rem 1rem 1rem 1rem;
        transition: margin-left 0.3s;
    }

    @media (max-width: 991px) {
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
        <!-- <div class="search-container flex-grow-1 mx-3" style="max-width: 400px;">
            <div class="input-group">
                <input type="text" class="form-control" id="globalSearch" placeholder="Search students" autocomplete="off">
                <button class="btn btn-outline-light" type="button" id="searchBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div id="searchResults" class="search-results position-absolute bg-white border rounded shadow-sm" style="display: none; z-index: 1050; max-height: 300px; overflow-y: auto; width: 100%;"></div>
        </div> -->
        <ul class="nav-links d-none d-md-flex">
            <li><i class="bi bi-envelope mx-4"></i><?php echo htmlspecialchars($teacherData['email'] ?? 'teacher@example.com'); ?></li>
            <li class="nav-item dropdown">
                <button class="btn nav-link dropdown-toggle d-flex align-items-center gap-2 p-0" id="teacherDropdown" data-bs-toggle="dropdown" aria-expanded="false" type="button">
                    <img src="<?php echo htmlspecialchars($teacherData['picture'] ?? 'assets/images/quest.jpg'); ?>" alt="Profile Picture" class="rounded-circle" width="40">
                    <span class="d-none d-md-inline text-white ms-2"><?php echo htmlspecialchars($teacherData['fullname'] ?? 'Teacher'); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="teacherDropdown">
                    <li><a class="dropdown-item text-primary" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-primary" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3 class="text-uppercase text-start fs-5">TEACHER DASHBOARD</h3>
    </div>
    <ul>
        <li><a href="index.php"><i class="fas fa-home me-2"></i>Overview</a></li>
        <li data-bs-toggle="collapse" data-bs-target="#classManagement"><a href="javascript:;"><i class="fas fa-book me-2"></i>My Classes</a></li>
        <div class="collapse" id="classManagement">
            <li><a class="text-primary" href="view_class.php"><i class="bi bi-journal-bookmark me-2"></i>View Classes</a></li>
            <li><a class="text-primary" href="post_assignment.php"><i class="bi bi-plus me-2"></i>Post Assignment</a></li>
        </div>
        <li data-bs-toggle="collapse" data-bs-target="#assignmentManagement"><a href="javascript:;"><i class="fas fa-pencil me-2"></i>Assignments</a></li>
        <div class="collapse" id="assignmentManagement">
            <li><a class="text-primary" href="view_assignments.php"><i class="bi bi-list me-2"></i>View Assignments</a></li>
            <li><a class="text-primary" href="post_assignment.php"><i class="bi bi-plus me-2"></i>Post New</a></li>
        </div>
        <!-- <li><a href="manage_results.php"><i class="fas fa-file-earmark-text me-2"></i>Results</a></li> -->
        <li data-bs-toggle="collapse" data-bs-target="#studentManagement"><a href="javascript:;"><i class="fas fa-users me-2"></i>Students</a></li>
        <div class="collapse" id="studentManagement">
            <li><a class="text-primary" href="view_students.php"><i class="bi bi-people me-2"></i>View Students</a></li>
            <!-- <li><a class="text-primary" href="manage_results.php"><i class="bi bi-upload me-2"></i>Upload Result</a></li> -->
        </div>
    </ul>
    <div class="sidebar-header">
        <h3 class="text-uppercase text-start fs-5">PROFILE</h3>
    </div>
    <ul>
        <li data-bs-toggle="collapse" data-bs-target="#profileSettings"><a href="javascript:;"><i class="bi bi-person-fill me-2"></i>Profile</a></li>
        <div class="collapse" id="profileSettings">
            <li><a class="text-primary" href="profile.php"><i class="bi bi-person-circle me-2"></i>View Profile</a></li>
            <li><a class="text-primary" href="login.php"><i class="bi bi-box-arrow-left me-2"></i>Login</a></li>
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
        if (window.innerWidth <= 991) {
            if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        }
    });

    // Global Search Functionality (adapted for teacher - search students and classes)
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
                item.innerHTML = `<a href="view_student.php?student_id=${student.id}"><i class="bi bi-person me-2"></i>${highlightText(student.fullname, query)} <small class="text-muted">(${student.class})</small></a>`;
                searchResults.appendChild(item);
            });
        }

        // if (data.classes && data.classes.length > 0) {
        //     searchResults.innerHTML += '<div class="search-category">Classes</div>';
        //     data.classes.forEach(cls => {
        //         const item = document.createElement('div');
        //         item.className = 'search-item';
        //         item.innerHTML = `<a href="view_class.php?"><i class="bi bi-journal-bookmark me-2"></i>${highlightText(cls.class_name, query)}</a>`;
        //         searchResults.appendChild(item);
        //     });
        // }

        searchResults.style.display = searchResults.innerHTML ? 'block' : 'none';
    }

    // Function to perform search (placeholder - implement teacher search handler if needed)
    function performSearch(query) {
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        // Placeholder: For now, just hide results. Implement actual search if required.
        searchResults.style.display = 'none';
        // Uncomment and implement fetch to teacher/search_handler.php when available
        fetch('search_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'query=' + encodeURIComponent(query)
        })
        .then(response => response.json())
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
    try {
        document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                if (e) e.preventDefault();
                if (window.bootstrap && window.bootstrap.Dropdown) {
                    window.bootstrap.Dropdown.getOrCreateInstance(toggle).toggle();
                }
            });
        });
    } catch (err) {
        console.error('Dropdown init fallback error:', err);
    }
</script>
