@extends('layouts.app')

@section('title', 'Bank Template Pertanyaan - Adiprima Survey Center')

@section('styles')
<style>
    .qbank-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .filter-form-wrapper {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .table-responsive-box {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .modal-box {
        background: #FFFFFF;
        border-radius: 16px;
        width: 95%;
        max-width: 620px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 1.75rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
        margin: 1rem auto;
    }

    @media (max-width: 768px) {
        .qbank-header {
            flex-direction: column;
            align-items: stretch;
        }

        .qbank-header button {
            justify-content: center;
            width: 100%;
        }

        .filter-form-wrapper {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-form-wrapper > div,
        .filter-form-wrapper button {
            width: 100% !important;
        }

        .modal-box {
            padding: 1.25rem;
            border-radius: 12px;
        }
    }
</style>
@endsection

@section('content')
<div class="qbank-header">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--color-navy-primary); letter-spacing: -0.02em;">
            Bank Template Pertanyaan (Question Bank)
        </h1>
        <p style="color: #64748B; font-size: 0.9rem; margin-top: 0.25rem;">
            Daftar master pertanyaan yang dapat digunakan kembali untuk berbagai macam kuesioner perusahaan.
        </p>
    </div>
    <button onclick="openModal('createTemplateModal')" style="background: var(--color-navy-primary); color: #FFFFFF; border: none; padding: 0.75rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: background 0.2s ease; box-shadow: 0 4px 10px rgba(12, 43, 100, 0.2);">
        <i class="bi bi-plus-circle-fill"></i> Tambah Template Pertanyaan
    </button>
</div>

<!-- Filters Bar -->
<div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <form method="GET" action="{{ route('admin.question-bank.index') }}" class="filter-form-wrapper">
        <div style="flex: 1; min-width: 240px;">
            <div style="position: relative;">
                <i class="bi bi-search" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari teks pertanyaan atau indikator..." style="width: 100%; padding: 0.6rem 0.85rem 0.6rem 2.4rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem;">
            </div>
        </div>

        <div style="min-width: 190px;">
            <select name="dimension_id" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                <option value="">Semua Kategori/Dimensi</option>
                @foreach($dimensions as $dim)
                    <option value="{{ $dim->id }}" {{ $dimensionFilter == $dim->id ? 'selected' : '' }}>
                        {{ $dim->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="min-width: 180px;">
            <select name="question_type" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                <option value="">Semua Tipe Soal</option>
                <option value="dual_rating" {{ $typeFilter == 'dual_rating' ? 'selected' : '' }}>Dual Rating (Harapan & Kenyataan)</option>
                <option value="single_rating" {{ $typeFilter == 'single_rating' ? 'selected' : '' }}>Single Rating Skala</option>
                <option value="essay" {{ $typeFilter == 'essay' ? 'selected' : '' }}>Uraian / Deskriptif</option>
                <option value="multiple_choice" {{ $typeFilter == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
            </select>
        </div>

        <button type="submit" style="background: #F1F5F9; border: 1px solid #CBD5E1; color: #334155; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
            <i class="bi bi-funnel-fill"></i> Filter
        </button>

        @if($search || $dimensionFilter || $typeFilter)
            <a href="{{ route('admin.question-bank.index') }}" style="color: #DC2626; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
                <i class="bi bi-x-circle"></i> Reset
            </a>
        @endif
    </form>
</div>

<!-- Question Templates Table -->
<div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <div class="table-responsive-box">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
            <thead>
                <tr style="background: #F8FAFC; border-bottom: 1px solid var(--color-border); color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">
                    <th style="padding: 0.85rem 1rem; width: 60px;">No</th>
                    <th style="padding: 0.85rem 1rem; width: 160px;">Dimensi</th>
                    <th style="padding: 0.85rem 1rem;">Indikator & Teks Pertanyaan</th>
                    <th style="padding: 0.85rem 1rem; width: 150px;">Tipe & Skala</th>
                    <th style="padding: 0.85rem 1rem; width: 180px;">Logic Deskriptif</th>
                    <th style="padding: 0.85rem 1rem; width: 90px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $index => $tpl)
                <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                    <td style="padding: 0.85rem 1rem; color: #64748B; font-weight: 600;">
                        {{ $templates->firstItem() + $index }}
                    </td>
                    <td style="padding: 0.85rem 1rem;">
                        @if($tpl->dimension)
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.775rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 6px; background: {{ $tpl->dimension->color ?? '#2563EB' }}15; color: {{ $tpl->dimension->color ?? '#2563EB' }};">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $tpl->dimension->color ?? '#2563EB' }};"></span>
                                {{ $tpl->dimension->name }}
                            </span>
                        @else
                            <span style="color: #94A3B8; font-size: 0.775rem;">(Tanpa Dimensi)</span>
                        @endif
                    </td>
                    <td style="padding: 0.85rem 1rem;">
                        @if($tpl->indicator_title)
                            <div style="font-weight: 700; color: var(--color-navy-primary); font-size: 0.825rem; margin-bottom: 0.2rem;">
                                {{ $tpl->indicator_title }}
                            </div>
                        @endif
                        <div style="color: #1E293B; line-height: 1.45;">
                            {{ $tpl->question_text }}
                        </div>
                        @if($tpl->applies_to_employment_status || $tpl->applies_to_positions)
                            <div style="margin-top: 0.25rem; font-size: 0.725rem; color: #D97706; display: flex; align-items: center; gap: 0.25rem;">
                                <i class="bi bi-shield-exclamation"></i>
                                Khusus: {{ $tpl->applies_to_employment_status ?? '-' }} / {{ $tpl->applies_to_positions ?? '-' }}
                            </div>
                        @endif
                    </td>
                    <td style="padding: 0.85rem 1rem;">
                        @if($tpl->question_type === 'dual_rating')
                            <span style="background: #EFF6FF; color: #2563EB; font-weight: 700; font-size: 0.725rem; padding: 0.15rem 0.45rem; border-radius: 4px; display: inline-block; margin-bottom: 0.2rem;">
                                Dual Rating (1-{{ $tpl->rating_scale }})
                            </span>
                        @elseif($tpl->question_type === 'single_rating')
                            <span style="background: #F0FDF4; color: #16A34A; font-weight: 700; font-size: 0.725rem; padding: 0.15rem 0.45rem; border-radius: 4px; display: inline-block;">
                                Skala 1-{{ $tpl->rating_scale }}
                            </span>
                        @elseif($tpl->question_type === 'essay')
                            <span style="background: #FAF5FF; color: #9333EA; font-weight: 700; font-size: 0.725rem; padding: 0.15rem 0.45rem; border-radius: 4px; display: inline-block;">
                                Uraian
                            </span>
                        @elseif($tpl->question_type === 'multiple_choice')
                            <span style="background: #FFF7ED; color: #EA580C; font-weight: 700; font-size: 0.725rem; padding: 0.15rem 0.45rem; border-radius: 4px; display: inline-block;">
                                Pilihan Ganda
                            </span>
                        @endif
                    </td>
                    <td style="padding: 0.85rem 1rem;">
                        @if($tpl->require_reason_on_low_score)
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; background: #FEF3C7; color: #92400E; font-size: 0.725rem; font-weight: 700; padding: 0.2rem 0.45rem; border-radius: 6px;">
                                <i class="bi bi-exclamation-circle-fill" style="color: #D97706;"></i>
                                Wajib Alasan jika &le; {{ $tpl->low_score_threshold }}
                            </span>
                        @else
                            <span style="color: #94A3B8; font-size: 0.75rem;">Nonaktif</span>
                        @endif
                    </td>
                    <td style="padding: 0.85rem 1rem; text-align: center;">
                        <div style="display: flex; justify-content: center; gap: 0.35rem;">
                            <button onclick="editTemplate({{ json_encode($tpl) }})" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #334155; padding: 0.3rem 0.5rem; border-radius: 6px; font-size: 0.8rem; cursor: pointer;" title="Edit Template">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.question-bank.destroy', $tpl->id) }}" method="POST" onsubmit="return confirm('Hapus template pertanyaan ini?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 0.3rem 0.5rem; border-radius: 6px; font-size: 0.8rem; cursor: pointer;" title="Hapus Template">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 3rem; text-align: center; color: #64748B;">
                        <i class="bi bi-journal-x" style="font-size: 2rem; color: #94A3B8; display: block; margin-bottom: 0.5rem;"></i>
                        Tidak ada template pertanyaan yang sesuai filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($templates->hasPages())
    <div style="padding: 0.85rem 1rem; border-top: 1px solid var(--color-border); background: #F8FAFC; display: flex; justify-content: center; align-items: center;">
        {{ $templates->links() }}
    </div>
    @endif
</div>

<!-- Modal Create Template -->
<div id="createTemplateModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 1rem;">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-navy-primary); margin: 0;">
                Tambah Template ke Bank Soal
            </h3>
            <button onclick="closeModal('createTemplateModal')" style="background: none; border: none; font-size: 1.25rem; color: #94A3B8; cursor: pointer;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.question-bank.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Kategori / Dimensi</label>
                    <select name="dimension_id" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="">-- Pilih Dimensi --</option>
                        @foreach($dimensions as $dim)
                            <option value="{{ $dim->id }}">{{ $dim->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Indikator / Judul</label>
                    <input type="text" name="indicator_title" placeholder="Contoh: Gaji" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem;">
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Teks Pertanyaan *</label>
                <textarea name="question_text" required rows="3" placeholder="Tuliskan butir pertanyaan secara jelas..." style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; font-family: inherit; resize: vertical;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Tipe Pertanyaan *</label>
                    <select name="question_type" id="createQType" onchange="toggleTypeFields('create')" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="dual_rating">Dual Rating (Harapan & Kenyataan)</option>
                        <option value="single_rating">Single Rating (Skala Nilai)</option>
                        <option value="essay">Uraian / Deskriptif</option>
                        <option value="multiple_choice">Pilihan Ganda</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Skala Rating</label>
                    <select name="rating_scale" id="createScale" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="4">Skala 1 - 4</option>
                        <option value="5">Skala 1 - 5</option>
                        <option value="10">Skala 1 - 10</option>
                    </select>
                </div>
            </div>

            <!-- Conditional Logic Box -->
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.825rem; font-weight: 700; color: var(--color-navy-primary); cursor: pointer;">
                    <input type="checkbox" name="require_reason_on_low_score" value="1" checked style="width: 16px; height: 16px;">
                    Aktifkan Logic Kolom Alasan Wajib Diisi Jika Jawaban &le; 2
                </label>
            </div>

            <div id="createOptionsBlock" style="display: none; margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Pilihan Jawaban (Satu baris per opsi)</label>
                <textarea name="options_text" rows="3" placeholder="Opsi 1&#10;Opsi 2&#10;Opsi 3" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; font-family: inherit;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Khusus Status Pegawai</label>
                    <input type="text" name="applies_to_employment_status" placeholder="Contoh: Tetap" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Khusus Jabatan</label>
                    <input type="text" name="applies_to_positions" placeholder="Contoh: Karu ke atas" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem;">
                <button type="button" onclick="closeModal('createTemplateModal')" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #64748B; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="background: var(--color-navy-primary); color: #FFFFFF; border: none; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    Simpan ke Bank
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Template -->
<div id="editTemplateModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 1rem;">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-navy-primary); margin: 0;">
                Edit Template Pertanyaan
            </h3>
            <button onclick="closeModal('editTemplateModal')" style="background: none; border: none; font-size: 1.25rem; color: #94A3B8; cursor: pointer;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="editTemplateForm" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Kategori / Dimensi</label>
                    <select name="dimension_id" id="editDimId" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="">-- Pilih Dimensi --</option>
                        @foreach($dimensions as $dim)
                            <option value="{{ $dim->id }}">{{ $dim->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Indikator</label>
                    <input type="text" name="indicator_title" id="editIndTitle" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem;">
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Teks Pertanyaan *</label>
                <textarea name="question_text" id="editQText" required rows="3" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; font-family: inherit; resize: vertical;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Tipe Pertanyaan *</label>
                    <select name="question_type" id="editQType" onchange="toggleTypeFields('edit')" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="dual_rating">Dual Rating (Harapan & Kenyataan)</option>
                        <option value="single_rating">Single Rating (Skala Nilai)</option>
                        <option value="essay">Uraian / Deskriptif</option>
                        <option value="multiple_choice">Pilihan Ganda</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Skala Rating</label>
                    <select name="rating_scale" id="editScale" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="4">Skala 1 - 4</option>
                        <option value="5">Skala 1 - 5</option>
                        <option value="10">Skala 1 - 10</option>
                    </select>
                </div>
            </div>

            <!-- Conditional Logic Box -->
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.825rem; font-weight: 700; color: var(--color-navy-primary); cursor: pointer;">
                    <input type="checkbox" name="require_reason_on_low_score" id="editRequireReason" value="1" style="width: 16px; height: 16px;">
                    Aktifkan Logic Kolom Alasan Wajib Diisi Jika Jawaban &le; 2
                </label>
            </div>

            <div id="editOptionsBlock" style="display: none; margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Pilihan Jawaban (Satu baris per opsi)</label>
                <textarea name="options_text" id="editOptionsText" rows="3" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; font-family: inherit;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Khusus Status Pegawai</label>
                    <input type="text" name="applies_to_employment_status" id="editEmpStatus" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Khusus Jabatan</label>
                    <input type="text" name="applies_to_positions" id="editPos" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem;">
                <button type="button" onclick="closeModal('editTemplateModal')" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #64748B; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
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

    function toggleTypeFields(mode) {
        const typeSelect = document.getElementById(mode === 'create' ? 'createQType' : 'editQType');
        const optBlock = document.getElementById(mode === 'create' ? 'createOptionsBlock' : 'editOptionsBlock');
        const scaleSelect = document.getElementById(mode === 'create' ? 'createScale' : 'editScale');

        if (typeSelect.value === 'multiple_choice') {
            optBlock.style.display = 'block';
            scaleSelect.disabled = true;
        } else if (typeSelect.value === 'essay') {
            optBlock.style.display = 'none';
            scaleSelect.disabled = true;
        } else {
            optBlock.style.display = 'none';
            scaleSelect.disabled = false;
        }
    }

    function editTemplate(tpl) {
        document.getElementById('editTemplateForm').action = `/admin/question-bank/${tpl.id}`;
        document.getElementById('editDimId').value = tpl.dimension_id || '';
        document.getElementById('editIndTitle').value = tpl.indicator_title || '';
        document.getElementById('editQText').value = tpl.question_text || '';
        document.getElementById('editQType').value = tpl.question_type || 'dual_rating';
        document.getElementById('editScale').value = tpl.rating_scale || 4;
        document.getElementById('editRequireReason').checked = !!tpl.require_reason_on_low_score;
        document.getElementById('editEmpStatus').value = tpl.applies_to_employment_status || '';
        document.getElementById('editPos').value = tpl.applies_to_positions || '';

        if (tpl.options_json && Array.isArray(tpl.options_json)) {
            document.getElementById('editOptionsText').value = tpl.options_json.join("\n");
        } else {
            document.getElementById('editOptionsText').value = '';
        }

        toggleTypeFields('edit');
        openModal('editTemplateModal');
    }

    window.onclick = function(event) {
        if (event.target.id === 'createTemplateModal') closeModal('createTemplateModal');
        if (event.target.id === 'editTemplateModal') closeModal('editTemplateModal');
    }
</script>
@endsection
@endsection
