<style>
    .settingsPage {
        position: fixed;
        top: 56px;
        right: 0;
        height: calc(100vh - 56px);
        width: 100%;
        z-index: 1040;
        transform: translateX(100%);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: grid;
        grid-template-columns: 1fr 380px;
    }

    .settingsCloser {
        background: rgba(0,0,0,0.3);
        backdrop-filter: blur(4px);
    }

    .settings {
        background: #fff;
        box-shadow: -4px 0 20px rgba(0,0,0,0.1);
        border-left: 1px solid var(--slate-200);
        display: flex;
        flex-direction: column;
    }

    .settingsHeader {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--slate-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .settingsHeader h3 {
        margin: 0;
        font-weight: 700;
        font-size: 1.25rem;
    }

    .settings-close-btn {
        border: none;
        background: transparent;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
        cursor: pointer;
    }

    .settings-close-btn:hover {
        background: var(--slate-100);
    }

    .settingsBody {
        flex: 1;
        overflow-y: auto;
        padding: 1rem 1.5rem;
    }

    @media(max-width: 576px) {
        .settingsPage {
            grid-template-columns: 0fr 100%;
        }
        .settingsCloser {
            display: none;
        }
    }
</style>

<div class="settingsPage" id="settingsPage">
    <div class="settingsCloser" id="settings-closer-bg"></div>
    <div class="settings">
        <div class="settingsHeader">
            <h3><i class="bi bi-sliders me-2 text-green"></i>Settings</h3>
            <button id="settings-close-btn" class="settings-close-btn">
                <i class="bi bi-x-lg text-danger fw-bold"></i>
            </button>
        </div>
        <div class="settingsBody">
            <ul class="nav nav-tabs" id="settingsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="mode-tab" data-bs-toggle="tab" data-bs-target="#mode-panel" type="button" role="tab">Appearance</button>
                </li>
            </ul>
            <div class="tab-content mt-3" id="settingsTabContent">
                <div class="tab-pane fade show active" id="mode-panel" role="tabpanel">
                    <div class="mb-4">
                        <label for="mode-select" class="form-label fw-semibold">Theme Mode</label>
                        <select id="mode-select" class="form-select">
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                        <div class="form-text mt-2">Choose your preferred theme for the portal.</div>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-grad" id="apply-btn">
                            <i class="bi bi-check-lg me-1"></i> Apply Theme
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        // Initialize settings storage
        let settings = JSON.parse(localStorage.getItem("settings"));
        if (!settings || typeof settings !== "object") {
            settings = { mode: "light" };
            localStorage.setItem("settings", JSON.stringify(settings));
        }
        if (!settings.mode) settings.mode = "light";

        // Apply saved theme immediately
        document.body.setAttribute("data-theme", settings.mode);

        // Setup select value
        const modeSelect = document.getElementById("mode-select");
        if (modeSelect) modeSelect.value = settings.mode;

        // Panel controls
        const settingsPage = document.getElementById("settingsPage");
        const opener = document.getElementById("studentSettingsBtn");
        const closerBg = document.getElementById("settings-closer-bg");
        const closerBtn = document.getElementById("settings-close-btn");

        function openSettings() {
            if (settingsPage) settingsPage.style.transform = "translateX(0%)";
        }
        function closeSettings() {
            if (settingsPage) settingsPage.style.transform = "translateX(100%)";
        }

        if (opener) opener.addEventListener("click", openSettings);
        if (closerBg) closerBg.addEventListener("click", closeSettings);
        if (closerBtn) closerBtn.addEventListener("click", closeSettings);

        // Apply button
        const applyBtn = document.getElementById("apply-btn");
        if (applyBtn && modeSelect) {
            applyBtn.addEventListener("click", function() {
                settings.mode = modeSelect.value;
                localStorage.setItem("settings", JSON.stringify(settings));
                document.body.setAttribute("data-theme", settings.mode);
                closeSettings();
            });
        }
    })();
</script>

