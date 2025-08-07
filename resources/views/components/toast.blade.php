<div class="toast-container position-absolute" style="z-index: 999; bottom: 20px; right: 20px;">
  <div class="bs-toast toast fade show {{ $bgColor }}" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i class="bx bx-bell me-2"></i>
      <div class="me-auto fw-semibold">{{ $title }}</div>
      <small>1 mins ago</small>
      <button id="close" type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      {{ $slot }}
    </div>
  </div>
</div>

<script>
  setTimeout(function () {
    document.getElementById('close').click();
  }, 15000);
</script>