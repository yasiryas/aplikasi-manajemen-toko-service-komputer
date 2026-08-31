@extends('layouts.app', ['page' => 'settings'])

@section('page-title', 'Pengaturan')

@section('content')
    <div>
        <h1 class="font-heading text-2xl font-bold text-slate-900">Pengaturan</h1>
        <p class="mt-0.5 text-sm text-slate-500">Atur identitas toko dan informasi pada invoice.</p>
    </div>

    @if (session('status'))
        <div class="mt-4 flex items-center gap-2 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-700">
            <i class="fa-solid fa-circle-check"></i>{{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mt-4 flex items-center gap-2 rounded-lg bg-rose-50 p-3 text-sm text-rose-700">
            <i class="fa-solid fa-circle-exclamation"></i>{{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="card mt-5 max-w-2xl space-y-4 p-6">
        @csrf
        @method('PUT')

        <div class="flex flex-wrap items-end gap-5">
            <div>
                <label class="label">Logo / Ikon</label>
                <div id="logo-preview" class="flex h-16 w-16 items-center justify-center rounded-xl border border-slate-200 bg-slate-50">
                    <x-brand-logo boxClass="h-16 w-16 rounded-xl" />
                </div>
            </div>
            <div class="min-w-0 flex-1">
                <label class="label" for="logo">Unggah Logo</label>
                <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="input file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-slate-600 hover:file:bg-slate-200">
                <p class="mt-1 text-xs text-slate-400">PNG, JPG, WebP, atau SVG. Maks 2 MB. Kosongkan untuk mempertahankan logo saat ini.</p>
            </div>
        </div>

        <div>
            <label class="label" for="nama_toko">Nama Toko</label>
            <input type="text" id="nama_toko" name="nama_toko" class="input" value="{{ old('nama_toko', setting('nama_toko', 'Service Computer')) }}" required>
        </div>

        <div>
            <label class="label" for="tagline_toko">Tagline</label>
            <input type="text" id="tagline_toko" name="tagline_toko" class="input" value="{{ old('tagline_toko', setting('tagline_toko', 'Service Komputer')) }}" placeholder="Service Komputer">
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="telepon_toko">Telepon / WhatsApp</label>
                <input type="text" id="telepon_toko" name="telepon_toko" class="input" value="{{ old('telepon_toko', setting('telepon_toko')) }}" placeholder="08xxxxxxxxxx">
            </div>
            <div>
                <label class="label" for="alamat_toko">Alamat</label>
                <input type="text" id="alamat_toko" name="alamat_toko" class="input" value="{{ old('alamat_toko', setting('alamat_toko')) }}" placeholder="Jl. ...">
            </div>
        </div>

        <div>
            <label class="label" for="footer_invoice">Catatan Footer Invoice</label>
            <textarea id="footer_invoice" name="footer_invoice" rows="2" class="input" placeholder="Terima kasih...">{{ old('footer_invoice', setting('footer_invoice')) }}</textarea>
        </div>

        <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Pengaturan
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.getElementById('logo').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('logo-preview').innerHTML =
                    `<img src="${e.target.result}" alt="Preview logo" class="h-16 w-16 rounded-xl object-contain p-1">`;
            };
            reader.readAsDataURL(file);
        });
    </script>
@endpush