<div class="modal fade" id="modalDelete{{ $property->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Hapus ruangan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('properties.destroy', $property->id) }}" method="POST">
        @method('DELETE')
        @csrf
        <div class="modal-body">
          <p>Apakah anda yakin ingin menghapus data ini?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Close
          </button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>