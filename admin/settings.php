<style>
    .settingsButton {
        position: fixed;
        left: 95%;
        top: 60%;
        border-radius: 50%;
        border: none;
        box-shadow: 0 2px 2px rgb(0, 0, 0, 0.2);
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
</style>
<button class="settingsButton">
    <i class="bi bi-gear"></i>
</button>