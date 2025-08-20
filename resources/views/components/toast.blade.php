<div class="toast-container position-absolute" style="z-index: 999; bottom: 20px; right: 20px;">
    <div class="bs-toast toast fade show {{ $bgColor }}" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bx bx-bell me-2"></i>
            <div class="me-auto fw-semibold">{{ $title }}</div>
            <small>now</small>
            <button id="close" type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ $slot }}
        </div>

        <!-- Progress bar -->
        <div class="toast-progress"></div>
    </div>
</div>

<style>
    .toast-progress {
        height: 4px;
        background: rgba(255, 255, 255, 0.7);
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        transform: scaleX(0);
        transform-origin: left;
        animation: progressBar 3s linear forwards;
    }

    @keyframes progressBar {
        from {
            transform: scaleX(1);
        }

        to {
            transform: scaleX(0);
        }
    }
</style>

<script>
    setTimeout(function() {
        document.getElementById('close').click();
    }, 3000);
</script>
