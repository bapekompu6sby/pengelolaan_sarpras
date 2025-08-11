<div class="modal fade" id="modalResponse{{ $t->id }}" tabindex="-1" data-bs-backdrop="static" role="dialog"
  aria-labelledby="addEventLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('transactions.ruangan.response', $t->id) }}" method="POST">
        @csrf
        <div class="col mb-0">
          <label for="affiliation" class="form-label">Respon</label>
          <select class="form-select" id="status" aria-label="Default select example"
            name="status">
            <option value="" selected disabled>Open this select menu</option>
            <option value="approved">Terima</option>
            <option value="rejected">Tolak</option>
          </select>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Respon</button>
        </div>
      </form>
    </div>
  </div>
</div>
