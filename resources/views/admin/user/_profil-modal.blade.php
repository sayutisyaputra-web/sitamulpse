{{-- resources/views/admin/user/_profil-modal.blade.php --}}

<dialog
    id="profil-{{ $user->id }}"
    aria-labelledby="judul-profil-{{ $user->id }}"
    class="profil-dialog"
>
    <form
        method="POST"
        action="{{ route('admin.user.profil', $user) }}"
        enctype="multipart/form-data"
        class="profil-form"
    >
        @csrf

        {{-- HEADER --}}
        <header class="profil-header">
            <div>
                <span class="profil-header-label">PENGATURAN AKUN</span>

                <h2
                    id="judul-profil-{{ $user->id }}"
                    class="profil-title"
                >
                    Lengkapi Profil
                </h2>

                <p class="profil-subtitle">
                    Perbarui informasi profil {{ $user->name }}.
                </p>
            </div>

            <button
                type="button"
                class="profil-close"
                onclick="this.closest('dialog').close()"
                aria-label="Tutup dialog"
            >
                &times;
            </button>
        </header>

        {{-- KONTEN --}}
        <div class="profil-content">

            {{-- FOTO PROFIL --}}
            <section class="profil-section">
                <div class="profil-section-heading">
                    <span class="profil-section-icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="19"
                            height="19"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="12" cy="8" r="4"/>
                            <path
                                stroke-linecap="round"
                                d="M5 21a7 7 0 0 1 14 0"
                            />
                        </svg>
                    </span>

                    <div>
                        <h3>Foto profil</h3>
                        <p>Gunakan foto yang jelas untuk akun Anda.</p>
                    </div>
                </div>

                <div class="profil-photo-card">

                    <div class="profil-photo-preview">
                        <img
                            id="preview-foto-{{ $user->id }}"
                            src="{{ $user->foto_url }}"
                            alt="Foto {{ $user->name }}"
                        >
                    </div>

                    <div class="profil-photo-controls">

                        <label
                            for="foto-{{ $user->id }}"
                            class="profil-upload-button"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="19"
                                height="19"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 16V4m0 0L7 9m5-5 5 5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4"
                                />
                            </svg>

                            <span>Pilih Foto</span>
                        </label>

                        {{-- Input asli disembunyikan dengan CSS khusus --}}
                        <input
                            id="foto-{{ $user->id }}"
                            type="file"
                            name="foto"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            class="profil-file-input"
                            onchange="window.previewFotoProfil(this, 'preview-foto-{{ $user->id }}', 'nama-foto-{{ $user->id }}')"
                        >

                        <p
                            id="nama-foto-{{ $user->id }}"
                            class="profil-file-name"
                            aria-live="polite"
                        >
                            Belum ada foto baru dipilih
                        </p>

                        <p class="profil-file-hint">
                            JPG, JPEG, PNG, atau WEBP.
                            Maksimal 2 MB.
                        </p>
                    </div>
                </div>
            </section>

            {{-- INFORMASI PRIBADI --}}
            <section class="profil-section">
                <div class="profil-section-heading">
                    <span class="profil-section-icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="19"
                            height="19"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect
                                x="3"
                                y="4"
                                width="18"
                                height="16"
                                rx="2"
                            />
                            <path
                                stroke-linecap="round"
                                d="M8 9h8M8 13h5"
                            />
                        </svg>
                    </span>

                    <div>
                        <h3>Informasi pribadi</h3>
                        <p>Pastikan informasi yang dimasukkan benar.</p>
                    </div>
                </div>

                <div class="profil-fields">

                    {{-- NIK --}}
                    <div class="profil-field">
                        <label for="nik-{{ $user->id }}">
                            NIK
                        </label>

                        <input
                            id="nik-{{ $user->id }}"
                            type="text"
                            name="nik"
                            value="{{ $user->nik }}"
                            maxlength="16"
                            minlength="16"
                            inputmode="numeric"
                            autocomplete="off"
                            pattern="[0-9]{16}"
                            title="NIK harus terdiri dari 16 digit angka."
                            placeholder="Masukkan 16 digit NIK"
                        >

                        <small>
                            Terdiri dari 16 digit angka.
                        </small>
                    </div>

                    {{-- NIP --}}
                    <div class="profil-field">
                        <label for="nip-{{ $user->id }}">
                            NIP
                        </label>

                        <input
                            id="nip-{{ $user->id }}"
                            type="text"
                            name="nip"
                            value="{{ $user->nip }}"
                            maxlength="18"
                            minlength="18"
                            inputmode="numeric"
                            autocomplete="off"
                            pattern="[0-9]{18}"
                            title="NIP harus terdiri dari 18 digit angka."
                            placeholder="Masukkan 18 digit NIP"
                        >

                        <small>
                            Terdiri dari 18 digit angka jika diisi.
                        </small>
                    </div>

                    {{-- JABATAN --}}
                    <div class="profil-field profil-field-full">
                        <label for="jabatan-{{ $user->id }}">
                            Jabatan
                        </label>

                        <input
                            id="jabatan-{{ $user->id }}"
                            type="text"
                            name="jabatan"
                            value="{{ $user->jabatan }}"
                            maxlength="150"
                            placeholder="Contoh: Petugas Verifikator"
                        >
                    </div>

                </div>
            </section>
        </div>

        {{-- FOOTER --}}
        <footer class="profil-footer">

            <div class="profil-footer-left">
                @if ($user->foto)
                    <button
                        type="submit"
                        form="hapus-foto-{{ $user->id }}"
                        class="profil-delete-button"
                        onclick="return confirm('Hapus foto profil {{ addslashes($user->name) }}?')"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="17"
                            height="17"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 7h16M10 11v6m4-6v6M5 7l1 14h12l1-14M9 7V4h6v3"
                            />
                        </svg>

                        Hapus foto
                    </button>
                @endif
            </div>

            <div class="profil-footer-actions">
                <button
                    type="button"
                    class="profil-cancel-button"
                    onclick="this.closest('dialog').close()"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="profil-save-button"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5 12l4 4L19 6"
                        />
                    </svg>

                    Simpan perubahan
                </button>
            </div>

        </footer>
    </form>

    {{-- FORM HAPUS FOTO --}}
    @if ($user->foto)
        <form
            id="hapus-foto-{{ $user->id }}"
            method="POST"
            action="{{ route('admin.user.foto.hapus', $user) }}"
            hidden
        >
            @csrf
            @method('DELETE')
        </form>
    @endif
</dialog>

{{-- STYLE MODAL --}}
<style>
    .profil-dialog {
        width: calc(100% - 32px);
        max-width: 560px;
        max-height: 90vh;
        margin: auto;
        padding: 0;
        overflow-x: hidden;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #ffffff;
        color: #1e293b;
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.20);
    }

    .profil-dialog::backdrop {
        background: rgba(15, 23, 42, 0.58);
        backdrop-filter: blur(3px);
    }

    .profil-form {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .profil-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .profil-header-label {
        display: block;
        margin-bottom: 5px;
        color: #64748b;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1.1px;
    }

    .profil-title {
        margin: 0;
        color: #0f172a;
        font-size: 21px;
        font-weight: 750;
        line-height: 1.35;
    }

    .profil-subtitle {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .profil-close {
        display: inline-flex;
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        border-radius: 50%;
        background: #ffffff;
        color: #64748b;
        font-size: 23px;
        line-height: 1;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }

    .profil-close:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .profil-content {
        display: flex;
        flex-direction: column;
        gap: 27px;
        padding: 24px;
    }

    .profil-section {
        min-width: 0;
    }

    .profil-section-heading {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 15px;
    }

    .profil-section-icon {
        display: inline-flex;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        align-items: center;
        justify-content: center;
        border: 1px solid #e0e7ff;
        border-radius: 11px;
        background: #eef2ff;
        color: #1e2a78;
    }

    .profil-section-heading h3 {
        margin: 0;
        color: #1e293b;
        font-size: 14px;
        font-weight: 700;
    }

    .profil-section-heading p {
        margin: 3px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .profil-photo-card {
        display: flex;
        align-items: center;
        gap: 20px;
        min-width: 0;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
    }

    .profil-photo-preview {
        width: 86px;
        height: 86px;
        flex: 0 0 86px;
        overflow: hidden;
        border: 4px solid #ffffff;
        border-radius: 50%;
        background: #e2e8f0;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.10);
    }

    .profil-photo-preview img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profil-photo-controls {
        flex: 1;
        min-width: 0;
    }

    /*
     * Sembunyikan input asli secara visual.
     * Jangan menggunakan display:none agar input tetap dapat
     * menerima fokus keyboard melalui tombol label.
     */
    .profil-dialog .profil-file-input {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }

    .profil-upload-button {
        display: inline-flex !important;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 10px 17px;
        border: 1px solid #1e2a78 !important;
        border-radius: 9px;
        background: #1e2a78 !important;
        color: #ffffff !important;
        font-size: 13px;
        font-weight: 650;
        line-height: 1.4;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(30, 42, 120, 0.12);
        transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
    }

    .profil-upload-button:hover {
        border-color: #17215f !important;
        background: #17215f !important;
        color: #ffffff !important;
        box-shadow: 0 4px 9px rgba(30, 42, 120, 0.18);
    }

    .profil-upload-button:active {
        transform: translateY(1px);
    }

    .profil-file-input:focus-visible + .profil-upload-button {
        outline: 3px solid #c7d2fe;
        outline-offset: 3px;
    }

    .profil-file-name {
        margin: 10px 0 0;
        overflow-wrap: anywhere;
        color: #475569;
        font-size: 12px;
        line-height: 1.5;
    }

    .profil-file-hint {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 11px;
        line-height: 1.6;
    }

    .profil-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 19px 16px;
    }

    .profil-field {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .profil-field-full {
        grid-column: 1 / -1;
    }

    .profil-field label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 13px;
        font-weight: 650;
    }

    .profil-field input {
        display: block;
        box-sizing: border-box;
        width: 100%;
        min-width: 0;
        min-height: 43px;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        outline: none;
        background: #ffffff;
        color: #0f172a;
        font-family: inherit;
        font-size: 13px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .profil-field input::placeholder {
        color: #94a3b8;
    }

    .profil-field input:focus {
        border-color: #1e2a78;
        box-shadow: 0 0 0 3px rgba(30, 42, 120, 0.10);
    }

    .profil-field small {
        margin-top: 6px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.5;
    }

    .profil-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 17px 24px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .profil-footer-left,
    .profil-footer-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profil-footer-actions {
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .profil-cancel-button,
    .profil-save-button,
    .profil-delete-button {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 9px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 650;
        line-height: 1.4;
        white-space: nowrap;
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
    }

    .profil-cancel-button {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
    }

    .profil-cancel-button:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .profil-save-button {
        border: 1px solid #1e2a78 !important;
        background: #1e2a78 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(30, 42, 120, 0.15);
    }

    .profil-save-button:hover {
        border-color: #17215f !important;
        background: #17215f !important;
        color: #ffffff !important;
        box-shadow: 0 4px 9px rgba(30, 42, 120, 0.20);
    }

    .profil-delete-button {
        border: 1px solid #fecaca;
        background: #ffffff;
        color: #b91c1c;
    }

    .profil-delete-button:hover {
        border-color: #fca5a5;
        background: #fef2f2;
    }

    @media (max-width: 520px) {
        .profil-dialog {
            width: calc(100% - 20px);
            max-height: 92vh;
            border-radius: 15px;
        }

        .profil-header {
            padding: 18px;
        }

        .profil-title {
            font-size: 19px;
        }

        .profil-content {
            gap: 23px;
            padding: 18px;
        }

        .profil-photo-card {
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding: 18px;
            text-align: center;
        }

        .profil-photo-controls {
            width: 100%;
        }

        .profil-upload-button {
            width: 100%;
            box-sizing: border-box;
        }

        .profil-fields {
            gap: 17px 10px;
        }

        .profil-field input {
            padding: 10px;
            font-size: 12px;
        }

        .profil-footer {
            flex-direction: column-reverse;
            align-items: stretch;
            padding: 15px 18px;
        }

        .profil-footer-left {
            justify-content: flex-start;
        }

        .profil-footer-actions {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            width: 100%;
        }

        .profil-cancel-button,
        .profil-save-button {
            width: 100%;
            box-sizing: border-box;
            padding-right: 9px;
            padding-left: 9px;
            font-size: 12px;
        }

        .profil-delete-button {
            width: 100%;
        }
    }

    @media (max-width: 350px) {
        .profil-fields {
            grid-template-columns: 1fr;
        }

        .profil-field-full {
            grid-column: auto;
        }

        .profil-footer-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

{{-- JAVASCRIPT PREVIEW FOTO --}}
<script>
    if (typeof window.previewFotoProfil !== 'function') {
        window.previewFotoProfil = function (input, previewId, nameId) {
            const preview = document.getElementById(previewId);
            const name = document.getElementById(nameId);

            if (!preview || !name) {
                return;
            }

            const file = input.files && input.files.length
                ? input.files[0]
                : null;

            if (!file) {
                name.textContent = 'Belum ada foto baru dipilih';
                return;
            }

            name.textContent = file.name;

            if (!file.type.startsWith('image/')) {
                return;
            }

            const reader = new FileReader();

            reader.onload = function (event) {
                preview.src = event.target.result;
            };

            reader.readAsDataURL(file);
        };
    }
</script>