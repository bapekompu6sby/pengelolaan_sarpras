<!-- Customer Service Widget -->
<div class="chat-widget position-fixed bottom-0 end-0 m-4" style="z-index: 1050;">
    <!-- Floating Button -->
    <button class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
        type="button" data-bs-toggle="collapse" data-bs-target="#chatBox" aria-expanded="false" aria-controls="chatBox"
        style="width: 56px; height: 56px; background-color: #1C3168; color: #fff; border: none;">
        <i class="bx bx-message-rounded-dots fs-3"></i>
    </button>

    <!-- Chat Box -->
    <div class="collapse mt-2" id="chatBox">
        <div class="card shadow-lg border-0 rounded-3 overflow-hidden w-100" style="max-width: 340px; height: 480px;">

            <!-- Header -->
            <div class="card-header text-white d-flex justify-content-between align-items-center py-2 px-3"
                style="background-color: #1C3168;">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 36px; height: 36px;">
                        <i class="bx bx-headphone text-primary fs-5" style="color:#1C3168;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-white">Admin bapekom VI</h6>
                        <small class="text-white">Online</small>
                    </div>
                </div>
                <button class="btn btn-light rounded-circle d-flex align-items-center justify-content-center p-0"
                    style="width: 36px; height: 36px;" data-bs-toggle="collapse" data-bs-target="#chatBox">
                    <i class="bx bx-x fs-5"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="card-body d-flex flex-column bg-light px-3 py-2" style="height: 320px; overflow-y: auto;">
                <!-- Bubble pesan -->
                <div class="align-self-start bg-white shadow-sm p-2 px-3 rounded-3 mb-2" style="max-width: 85%;">
                    👋 Selamat datang! <br>Silahkan isi form berikut<br>untuk berkomunikasi dengan Admin bapekom VI.
                </div>
            </div>

            <!-- Footer / Form -->
            <div class="card-footer bg-white border-0 p-3">
                <form action="{{ route('customer_service.send') }}" method="POST" class="d-flex flex-column gap-2">
                    @csrf

                    <div class="form-floating">
                        <input type="text" id="name" name="name" class="form-control rounded-3"
                            placeholder="Nama Anda" required>
                        <label for="name">Nama Anda</label>
                    </div>

                    <div class="form-floating">
                        <input type="text" id="phone" name="phone" class="form-control rounded-3"
                            placeholder="Nomor WhatsApp" required pattern="[0-9]+"
                            title="Nomor WhatsApp hanya boleh angka">
                        <label for="phone">Nomor WhatsApp</label>
                    </div>

                    <div class="form-floating">
                        <textarea id="message" name="message" class="form-control rounded-3" placeholder="Tulis pesan..." rows="2"
                            style="height: 80px;" required></textarea>
                        <label for="message">Pesan</label>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill shadow-sm">
                        <i class="bx bx-send"></i> Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
