@extends('layouts.app')

@section('title', 'Master Kategori & Dimensi - Adiprima Survey Center')

@section('styles')
<style>
    .dimensions-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .dimensions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .modal-dialog-box {
        background: #FFFFFF;
        border-radius: 16px;
        width: 95%;
        max-width: 500px;
        padding: 1.75rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
        margin: 1rem auto;
    }

    @media (max-width: 768px) {
        .dimensions-header {
            flex-direction: column;
            align-items: stretch;
        }

        .dimensions-header button {
            justify-content: center;
            width: 100%;
        }

        .dimensions-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .modal-dialog-box {
            padding: 1.25rem;
            border-radius: 12px;
        }
    }
</style>
@endsection

@section('content')
<div class="dimensions-header">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--color-navy-primary); letter-spacing: -0.02em;">
            Kategori & Dimensi Pertanyaan
        </h1>
        <p style="color: #64748B; font-size: 0.9rem; margin-top: 0.25rem;">
            Kelola pengelompokan dimensi survei (contoh: Basic Needs, Teamwork, dll.) untuk standardisasi kuesioner.
        </p>
    </div>
    <button onclick="openModal('createDimensionModal')" style="background: var(--color-navy-primary); color: #FFFFFF; border: none; padding: 0.75rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: background 0.2s ease; box-shadow: 0 4px 10px rgba(12, 43, 100, 0.2);">
        <i class="bi bi-plus-circle-fill"></i> Tambah Dimensi Baru
    </button>
</div>

<!-- Dimension Cards Grid -->
<div class="dimensions-grid">
    @forelse($dimensions as $dim)
    <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: {{ $dim->color ?? '#2563EB' }};"></div>
        
        <div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                <div>
                    <span style="display: inline-block; font-size: 0.725rem; font-weight: 800; padding: 0.15rem 0.55rem; border-radius: 6px; background: {{ $dim->color ?? '#2563EB' }}15; color: {{ $dim->color ?? '#2563EB' }}; margin-bottom: 0.35rem;">
                        {{ $dim->code ?? 'DIM' }} &bull; Urutan #{{ $dim->order }}
                    </span>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--color-navy-primary); margin: 0;">
                        {{ $dim->name }}
                    </h3>
                </div>
                <div style="width: 16px; height: 16px; border-radius: 50%; background: {{ $dim->color ?? '#2563EB' }}; flex-shrink: 0;" title="Warna: {{ $dim->color }}"></div>
            </div>

            <p style="color: #64748B; font-size: 0.85rem; line-height: 1.45; margin-bottom: 1.25rem;">
                {{ $dim->description ?: 'Tidak ada deskripsi.' }}
            </p>
        </div>

        <div style="border-top: 1px solid #F1F5F9; padding-top: 0.85rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; gap: 0.75rem; font-size: 0.775rem; color: #64748B; flex-wrap: wrap;">
                <span><i class="bi bi-file-earmark-text" style="color: #2563EB;"></i> <b>{{ $dim->questions_count }}</b> Soal</span>
                <span><i class="bi bi-collection" style="color: #7C3AED;"></i> <b>{{ $dim->question_templates_count }}</b> Template</span>
            </div>
            <div style="display: flex; gap: 0.35rem;">
                <button onclick="editDimension({{ json_encode($dim) }})" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #334155; padding: 0.35rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer;" title="Edit Dimensi">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <form action="{{ route('admin.dimensions.destroy', $dim->id) }}" method="POST" onsubmit="return confirm('Hapus dimensi {{ $dim->name }}?');" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 0.35rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer;" title="Hapus Dimensi">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; background: #FFFFFF; border: 1px dashed var(--color-border); border-radius: 12px; padding: 3rem; text-align: center; color: #64748B;">
        <i class="bi bi-tags" style="font-size: 2.5rem; color: #94A3B8; margin-bottom: 0.5rem; display: block;"></i>
        <p style="font-size: 1rem; font-weight: 600;">Belum ada kategori / dimensi pertanyaan.</p>
        <p style="font-size: 0.875rem;">Klik "Tambah Dimensi Baru" untuk mulai membuat.</p>
    </div>
    @endforelse
</div>

<!-- Modal Create Dimension -->
<div id="createDimensionModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 1rem;">
    <div class="modal-dialog-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-navy-primary); margin: 0;">
                Tambah Dimensi Baru
            </h3>
            <button onclick="closeModal('createDimensionModal')" style="background: none; border: none; font-size: 1.25rem; color: #94A3B8; cursor: pointer;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.dimensions.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Nama Dimensi / Kategori *</label>
                <input type="text" name="name" required placeholder="Contoh: Basic Needs" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.875rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Kode Singkat</label>
                    <input type="text" name="code" placeholder="BN, TW" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Urutan</label>
                    <input type="number" name="order" value="1" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.875rem;">
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Warna Identitas (Grafik)</label>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <input type="color" name="color" value="#2563EB" style="width: 44px; height: 38px; border: 1px solid var(--color-border); border-radius: 6px; cursor: pointer; padding: 2px;">
                    <span style="font-size: 0.8rem; color: #64748B;">Pilih warna representasi grafik.</span>
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Deskripsi Dimensi</label>
                <textarea name="description" rows="3" placeholder="Penjelasan singkat..." style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.875rem; font-family: inherit; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem;">
                <button type="button" onclick="closeModal('createDimensionModal')" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #64748B; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="background: var(--color-navy-primary); color: #FFFFFF; border: none; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    Simpan Dimensi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Dimension -->
<div id="editDimensionModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 1rem;">
    <div class="modal-dialog-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-navy-primary); margin: 0;">
                Edit Dimensi / Kategori
            </h3>
            <button onclick="closeModal('editDimensionModal')" style="background: none; border: none; font-size: 1.25rem; color: #94A3B8; cursor: pointer;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="editDimensionForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Nama Dimensi / Kategori *</label>
                <input type="text" id="editDimName" name="name" required style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.875rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Kode Singkat</label>
                    <input type="text" id="editDimCode" name="code" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Urutan</label>
                    <input type="number" id="editDimOrder" name="order" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.875rem;">
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Warna Identitas</label>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <input type="color" id="editDimColor" name="color" style="width: 44px; height: 38px; border: 1px solid var(--color-border); border-radius: 6px; cursor: pointer; padding: 2px;">
                    <span style="font-size: 0.8rem; color: #64748B;">Pilih warna chart.</span>
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Deskripsi Dimensi</label>
                <textarea id="editDimDesc" name="description" rows="3" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.875rem; font-family: inherit; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem;">
                <button type="button" onclick="closeModal('editDimensionModal')" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #64748B; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="background: var(--color-navy-primary); color: #FFFFFF; border: none; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function editDimension(dim) {
        document.getElementById('editDimensionForm').action = `/admin/dimensions/${dim.id}`;
        document.getElementById('editDimName').value = dim.name || '';
        document.getElementById('editDimCode').value = dim.code || '';
        document.getElementById('editDimOrder').value = dim.order || 0;
        document.getElementById('editDimColor').value = dim.color || '#2563EB';
        document.getElementById('editDimDesc').value = dim.description || '';
        openModal('editDimensionModal');
    }

    window.onclick = function(event) {
        if (event.target.id === 'createDimensionModal') closeModal('createDimensionModal');
        if (event.target.id === 'editDimensionModal') closeModal('editDimensionModal');
    }
</script>
@endsection
@endsection
