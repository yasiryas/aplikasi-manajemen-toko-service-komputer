<x-card title="Pengaturan Teknisi" class="mt-4">
    <form method="POST" action="{{ route('technicians.update') }}" class="space-y-4">
        @csrf

        <div>
            <label class="label">Teknisi Utama</label>
            <select id="technician-select" class="input w-full pr-10 appearance-none">
                <option value="">Pilih teknisi</option>
                @foreach ($technicians as $technician)
                    <option value="{{ $technician->id }}" {{ in_array($technician->id, $selected_ids) ? 'selected' : '' }}>
                        {{ $technician->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <input type="hidden" id="selected-technician-id" name="technician_id" value="{{ $selected_ids[0] ?? '' }}">

        <div id="technician-errors" class="hidden rounded-lg bg-rose-50 p-3 text-sm text-rose-700"></div>

        <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="btn-secondary" data-close-modal>Batal</button>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</x-card>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('technician-select');
        const selectedIds = <?= json_encode($selected_ids ?? []) ?>;
        
        // Set initial selection
        select.value = selectedIds[0] || '';
    });
</script>