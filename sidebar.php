<style>
    :root {
        --quest-yellow: #fec511;
        --quest-green: #5aac7b;
    }

    html {
        overflow-x: hidden;
    }

    .text-green {
        color: var(--quest-green);
    }

    .text-yellow {
        color: var(--quest-yellow);
    }

    .bg-grad {
        background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
    }

    .btn-grad {
        background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
    }

    .btn-grad:hover {
        background: linear-gradient(90deg, var(--quest-yellow), var(--quest-green));
    }

    .bg-yellow {
        background: var(--quest-yellow);
    }

    .bg-green {
        background: var(--quest-green);
    }

    .form-container {
        width: 482px;
        border-radius: 12px;
        margin-top: 20px;
    }

    .toggler {
        display: grid;
    }

    .toggler span {
        margin: 5px 5px;
        border-radius: 20px;
        padding: 2px 30px;
        background: #767676;
    }

    .toggler-parent {
        margin-top: 10px;
    }

    .sidebar {
        background: #fff;
        z-index: 20;
        position: fixed;
    }

    [closed-sidebar] {
        overflow: hidden;
        left: -100%;
    }

    .side-links a {
        text-decoration: none;
        color: black;
        font-weight: bolder;
        margin-bottom: 5px;
        border-radius: 5px;
        text-align: center;
        padding: 10px;
        transition: background 0.3s ease-in-out;
    }

    .side-links a:hover {
        background: rgba(115, 115, 115, 0.1);
    }

    @media(min-width:992px) {
        .toggler-parent {
            display: none;
        }

        .sidebar {
            width: 177.5px;
            left: 0%;
            position: fixed;
        }
    }
    @media(min-width:1205px){
        .sidebar{
            width: 250px;
        }
    }

    @media (max-width: 991px) {
        .sidebar {
            left: -100% !important;
        }
    }

    * {
        font-family: Montserrat;
    }
</style>

<!-- Update your sidebar JS for toggling -->
<div class="col-6 col-sm-4 col-xl-2 col-lg-3 sidebar shadow-sm">
    <div class="d-grid justify-content-center">
        <img src="assets/images/quest.jpg" width="150" class="p-3" alt="">
    </div>
    <div class="py-3">
        <div class="px-3">
            <div class="d-grid side-links">
                <a href="./"><i class="pe-3 bi bi-reception-3"></i>Dashboard</a>
                <a href="./profile.php"><i class="pe-3 bi bi-person-fill"></i>My Profile</a>
                <a href="./notifications.php" class="d-lg-none"><i class="pe-3 fa-solid fa-bell"></i>Notifications</a>
                <a href="./events.php" class="d-lg-none"><i class="pe-3 bi bi-calendar"></i>Events</a>
                <a href="./result.php"><i class="fa-solid fa-square-poll-vertical"></i>My Results / Reports</a>
                <a href="./assignments.php"><i class="pe-3 fa-solid fa-book"></i>Assignments</a>
                <a href="support.php"><i class="pe-3 fa-solid fa-question"></i>Help & Support</a>
                <!--<a href="javascript:;"><i class="pe-3 fa-solid fa-table-columns"></i>Sign out</a>-->
            </div>
        </div>
    </div>
</div>
<script>
    let sidebar = document.querySelector(".sidebar");
    const header = document.querySelector(".header");
    window.addEventListener("DOMContentLoaded", function() {
        sidebar.style.height = `calc(100vh - ${header.clientHeight}px)`;
    });

    function updateSidebarState() {
        if (window.innerWidth < 992) {
            sidebar.setAttribute("closed-sidebar", true);
        } else {
            sidebar.removeAttribute("closed-sidebar");
        }
    }
    window.addEventListener("resize", updateSidebarState);
    updateSidebarState();

    const button = document.querySelector(".toggler-parent button");
    button.addEventListener("click", function() {
        sidebar.toggleAttribute("closed-sidebar");
    });

    // Hide sidebar on window click for smaller screens
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 992) {
            if (!sidebar.contains(event.target) && !button.contains(event.target)) {
                sidebar.setAttribute("closed-sidebar", true);
            }
        }
    });
</script>