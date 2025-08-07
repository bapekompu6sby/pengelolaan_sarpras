<div class="modal fade" id="roomsPaviliun" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalFullTitle">Paviliun</h5>
        @if (Auth::check())
          <button class="btn see">Lihat<i class="bx bx-show"></i></button>
          <button class="btn hide">Sembunyikan<i class="bx bx-hide"></i></button>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="seat-results">
          <div class="scroll-container">
            <table id="displaySeats" data-seats="seat">
              <tr>
                <td class="paviliun" id="seat-2" data-name="Paviliun 48CD - 3">48CD-3</td>
                <td class="paviliun invisible">&nbsp;</td>
                <td class="paviliun" id="seat-2" data-name="Paviliun 48EF - 3">48EF-3</td>
                <td class="paviliun invisible">&nbsp;</td>
                <td class="paviliun" id="seat-1" data-name="Paviliun 48G - 1">48G-1</td>
                <td class="paviliun" id="seat-1" data-name="Paviliun 48H - 1">48H-1</td>
                <td class="paviliun" id="seat-1" data-name="Paviliun 48I - 1">48I-1</td>
                <td class="paviliun" id="seat-1" data-name="Paviliun 48J - 1">48J-1</td>
              </tr>
              <tr>
                <td class="paviliun" id="seat-2" data-name="Paviliun 48CD - 1">48CD-1</td>
                <td class="paviliun" id="seat-1" data-name="Paviliun 48CD - 2">48CD-2</td>
                <td class="paviliun" id="seat-1" data-name="Paviliun 48EF - 1">48EF-1</td>
                <td class="paviliun" id="seat-2" data-name="Paviliun 48EF - 2">48EF-2</td>
                <td class="paviliun" id="seat-2" data-name="Paviliun 48G - 2">48G-2</td>
                <td class="paviliun" id="seat-2" data-name="Paviliun 48H - 2">48H-2</td>
                <td class="paviliun" id="seat-2" data-name="Paviliun 48I - 2">48I-2</td>
                <td class="paviliun" id="seat-2" data-name="Paviliun 48J - 2">48J-2</td>
              </tr>
            </table>
          </div>
          <div style="text-align: center; color: #9a031e; font-weight: bold;">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>