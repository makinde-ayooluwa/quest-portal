<style>
    * {
        /* transition: all 0.5s ease-in-out; */
    }

    .settingsButton {
        z-index: 20;
        position: fixed;
        left: 95%;
        top: 60%;
        border-radius: 50%;
        border: none;
        box-shadow: 0 2px 2px rgb(0, 0, 0, 0.2);
    }

    body[data-theme='dark'] .settingsButton {
        box-shadow: 2px 2px 2px rgb(255, 255, 255, 0.5);
        background: #000;
        color: #fff;
    }

    body[data-theme='dark'] .settingsPage .settings {
        background: #000;
        color: #fff;
        box-shadow: 0 1px 1px rgb(255, 255, 255);
        border-color: #fff;
    }

    #mode-select {
        background: #fff;
        color: #000;
        border: 2px solid;
        border-color: #000;
    }

    body[data-theme='dark'] #mode-select {
        background: #000;
        color: #fff;
        border-color: #fff;
    }


    .settingsButton i {
        display: inline-block;
        padding: 3px;
        animation: rotate 1.25s linear infinite;
    }

    @keyframes rotate {
        to {
            transform: rotate(360deg);
        }
    }

    .settingsButton:hover>i {
        animation-play-state: paused;
    }

    .settingsPage {
        position: fixed;
        height: calc(100vh - 56px);
        width: 100%;
        /* background: rgb(0, 0, 0, 0.4); */
        /* backdrop-filter: blur(10px); */
        z-index: 20;
        transform: translateX(100%);
        transition: all 0.5s ease-in-out;
        display: grid;
        grid-template-columns: 70% 30%;
    }

    @media(max-width:765px) {
        .settingsPage {
            grid-template-columns: 30% 70%;
        }
    }
</style>
<!-- <button class="settingsButton">
    <i class="bi bi-gear"></i>
</button> -->
<div class="settingsPage">
    <style>
        .settings {
            background: #fff;
            box-shadow: 0 1px 1px rgb(0, 0, 0, 0.2);
            border-radius: 10px;
            border: 1px solid;
            border-color: #000;
        }

        .settingsHeader .p-2 {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid;
        }
    </style>
    <div class="settingsCloser"></div>
    <div class="settings">
        <div class="settingsHeader">
            <div class="p-2">
                <h3>Settings</h3>
                <style>
                    .button {
                        border: none;
                        background: transparent;
                    }
                </style>
                <button id="settings-close-btn" class="button"><i class="bi bi-x-lg text-danger fw-bold h5"></i></button>
            </div>
        </div>
        <div class="settingsBody">
            <!-- Tab -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="mode-tab" data-bs-toggle="tab" data-bs-target="#mode" type="button" role="tab" aria-controls="home" aria-selected="true">Mode</button>
                </li>
                <!-- <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Contact</button>
                </li> -->
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="mode" role="tabpanel" aria-labelledby="mode-tab">
                    <div class="p-3 mt-3">
                        <select name="mode" id="mode-select" class="form-select w-50">
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                        <div class="d-flex" style="justify-content: flex-end;">
                            <button class="btn btn-outline-success" id="apply-btn">Apply</button>
                        </div>
                        <script>
                            const mode = document.querySelector("select");
                            const button = document.querySelector("#apply-btn");
                            const settings = JSON.parse(localStorage.getItem("settings"));
                            document.body.setAttribute("data-theme", settings.mode);
                            button.addEventListener("click", function() {
                                const modeSetting = mode.value;
                                settings.mode = modeSetting;
                                localStorage.setItem("settings", JSON.stringify(settings));
                                document.body.setAttribute("data-theme", settings.mode);
                            })
                        </script>
                    </div>
                </div>
                <!-- <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">Hi, Profile</div>
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">Hi, Contact</div> -->
            </div>
            <!-- Tab Ends -->
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (!localStorage.getItem("settings")) {
            localStorage.setItem("settings", JSON.stringify({}));
        }
    })
</script>
<script>
    const settingsPage = document.querySelector(".settingsPage");
    settingsPage.style.top = 56 + "px";
    // Settings Close
    const closers = [document.querySelector(".settingsCloser"), document.querySelector("#settings-close-btn")];
    closers.map(closer => {
        closer.addEventListener("click", function() {
            settingsPage.style.transform = "translateX(100%)";
            console.log("CLOSED")
        })
    })
    const opener = document.querySelector(".settingsButton");
    opener.addEventListener("click", function() {
        settingsPage.style.transform = "translateX(0%)";
    })
</script>