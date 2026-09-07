@extends('layouts.app')

@section('title', 'Kelola Pertanyaan - ' . $survey->title)

@section('styles')
<style>
    .builder-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .builder-actions {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    .dimension-panel {
        background: #FFFFFF;
        border: 1px solid var(--color-border);
        border-radius: 14px;
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }

    .dimension-header {
        background: #F8FAFC;
        border-bottom: 1px solid var(--color-border);
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .modal-box {
        background: #FFFFFF;
        border-radius: 16px;
        width: 95%;
        max-width: 650px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 1.75rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
        margin: 1rem auto;
    }

    @media (max-width: 768px) {
        .builder-header {
            flex-direction: column;
            align-items: stretch;
        }

        .builder-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }

        .builder-actions button,
        .builder-actions a {
            justify-content: center;
            text-align: center;
        }

        .builder-actions button:last-child {
            grid-column: 1 / -1;
        }

        .modal-box {
            padding: 1.25rem;
            border-radius: 12px;
        }
    }
</style>
@endsection

@section('content')
<div style="width: 100%;">
    <div style="margin-bottom: 0.75rem;">
        <a href="{{ route('admin.surveys.index') }}" style="color: #64748B; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem;">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kuesioner
        </a>
    </div>
    
    <div class="builder-header">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                <span style="background: #EFF6FF; color: #2563EB; font-weight: 800; font-size: 0.75rem; padding: 0.2rem 0.6rem; border-radius: 4px;">
                    {{ $survey->category }}
                </span>
                <span style="color: #64748B; font-size: 0.85rem;">&bull; Total {{ $questions->count() }} Butir Pertanyaan</span>
            </div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--color-navy-primary); letter-spacing: -0.02em; line-height: 1.3;">
                {{ $survey->title }}
            </h1>
        </div>

        <!-- Action Buttons -->
        <div class="builder-actions">
            <a href="{{ route('survey.form', $survey->slug) }}" target="_blank" style="background: #FFFFFF; border: 1px solid #CBD5E1; color: #334155; text-decoration: none; padding: 0.6rem 0.9rem; border-radius: 8px; font-weight: 700; font-size: 0.825rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                <i class="bi bi-box-arrow-up-right"></i> Preview
            </a>
            <button onclick="openModal('importBankModal')" style="background: #7C3AED; color: #FFFFFF; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.825rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: 0 4px 10px rgba(124,58,237,0.2);">
                <i class="bi bi-collection-fill"></i> Import Bank Soal
            </button>
            <button onclick="openModal('createQuestionModal')" style="background: var(--color-navy-primary); color: #FFFFFF; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.825rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                <i class="bi bi-plus-circle-fill"></i> Tambah Soal Baru
            </button>
        </div>
    </div>
</div>

<!-- Dimension Groups and Questions -->
@php
    $groupedQuestions = $questions->groupBy(function($item) {
        return $item->dimensionModel ? $item->dimensionModel->name : ($item->dimension ?: 'General');
    });
@endphp

@forelse($groupedQuestions as $dimName => $qList)
@php
    $dimModel = $qList->first()->dimensionModel;
    $dimColor = $dimModel ? $dimModel->color : '#2563EB';
@endphp
<div class="dimension-panel">
    <!-- Dimension Header -->
    <div class="dimension-header">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <div style="width: 12px; height: 12px; border-radius: 50%; background: {{ $dimColor }}; flex-shrink: 0;"></div>
            <h3 style="font-size: 1rem; font-weight: 800; color: var(--color-navy-primary); margin: 0;">
                Dimensi: {{ $dimName }}
            </h3>
            <span style="font-size: 0.75rem; font-weight: 700; background: {{ $dimColor }}15; color: {{ $dimColor }}; padding: 0.15rem 0.5rem; border-radius: 4px;">
                {{ $qList->count() }} Butir
            </span>
        </div>
        @if($dimModel && $dimModel->description)
            <div style="font-size: 0.775rem; color: #64748B; max-width: 450px;">
                {{ $dimModel->description }}
            </div>
        @endif
    </div>

    <!-- Questions in Dimension Table -->
    <div class="table-container">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
            <thead>
                <tr style="background: #FFFFFF; border-bottom: 1px solid #F1F5F9; color: #64748B; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                    <th style="padding: 0.75rem 1rem; width: 60px;">No</th>
                    <th style="padding: 0.75rem 1rem; width: 160px;">Indikator</th>
                    <th style="padding: 0.75rem 1rem;">Teks Pertanyaan</th>
                    <th style="padding: 0.75rem 1rem; width: 150px;">Tipe & Skala</th>
                    <th style="padding: 0.75rem 1rem; width: 170px;">Logic Deskriptif</th>
                    <th style="padding: 0.75rem 1rem; width: 90px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($qList as $q)
                <tr style="border-bottom: 1px solid #F8FAFC;">
                    <td style="padding: 0.75rem 1rem; font-weight: 800; color: var(--color-navy-primary);">
                        <span style="background: #F1F5F9; padding: 0.2rem 0.45rem; border-radius: 4px; font-size: 0.8rem;">
                            {{ $q->section }}{{ $q->question_number }}
                        </span>
                    </td>
                    <td style="padding: 0.75rem 1rem; font-weight: 600; color: #334155; font-size: 0.8rem;">
                        {{ $q->indicator_title ?: '-' }}
                    </td>
                    <td style="padding: 0.75rem 1rem; color: #1E293B; line-height: 1.45;">
                        {{ $q->question_text }}
                        @if($q->applies_to_employment_status || $q->applies_to_positions)
                            <div style="margin-top: 0.25rem; font-size: 0.75rem; color: #D97706; font-weight: 600;">
                                <i class="bi bi-person-fill-lock"></i> Khusus: {{ $q->applies_to_employment_status ?? '-' }} / {{ $q->applies_to_positions ?? '-' }}
                            </div>
                        @endif
                    </td>
                    <td style="padding: 0.75rem 1rem;">
                        @if($q->question_type === 'dual_rating' || $q->section === 'B')
                            <span style="background: #EFF6FF; color: #2563EB; font-weight: 700; font-size: 0.725rem; padding: 0.15rem 0.4rem; border-radius: 4px; display: inline-block;">
                                Dual Rating (1-{{ $q->rating_scale ?? 4 }})
                            </span>
                        @elseif($q->question_type === 'single_rating')
                            <span style="background: #F0FDF4; color: #16A34A; font-weight: 700; font-size: 0.725rem; padding: 0.15rem 0.4rem; border-radius: 4px; display: inline-block;">
                                Skala 1-{{ $q->rating_scale ?? 4 }}
                            </span>
                        @elseif($q->question_type === 'essay')
                            <span style="background: #FAF5FF; color: #9333EA; font-weight: 700; font-size: 0.725rem; padding: 0.15rem 0.4rem; border-radius: 4px; display: inline-block;">
                                Uraian
                            </span>
                        @elseif($q->question_type === 'multiple_choice')
                            <span style="background: #FFF7ED; color: #EA580C; font-weight: 700; font-size: 0.725rem; padding: 0.15rem 0.4rem; border-radius: 4px; display: inline-block;">
                                Pilihan Ganda
                            </span>
                        @endif
                    </td>
                    <td style="padding: 0.75rem 1rem;">
                        @if($q->require_reason_on_low_score || $q->section === 'B')
                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; background: #FEF3C7; color: #92400E; font-size: 0.725rem; font-weight: 700; padding: 0.2rem 0.4rem; border-radius: 4px;">
                                <i class="bi bi-exclamation-circle-fill" style="color: #D97706;"></i> Wajib jika &le; {{ $q->low_score_threshold ?? 2 }}
                            </span>
                        @else
                            <span style="color: #94A3B8; font-size: 0.75rem;">-</span>
                        @endif
                    </td>
                    <td style="padding: 0.75rem 1rem; text-align: center;">
                        <div style="display: flex; justify-content: center; gap: 0.35rem;">
                            <button onclick="editSurveyQuestion({{ json_encode($q) }})" style="background: #F1F5F9; border: 1px solid #CBD5E1; color: #334155; padding: 0.3rem 0.5rem; border-radius: 6px; font-size: 0.8rem; cursor: pointer;" title="Edit Pertanyaan">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.surveys.questions.destroy', [$survey->id, $q->id]) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan ini dari kuesioner?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 0.3rem 0.5rem; border-radius: 6px; font-size: 0.8rem; cursor: pointer;" title="Hapus dari Kuesioner">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div style="background: #FFFFFF; border: 1px dashed var(--color-border); border-radius: 12px; padding: 3rem 1.5rem; text-align: center; color: #64748B; margin-bottom: 2rem;">
    <i class="bi bi-ui-checks-grid" style="font-size: 2.5rem; color: #94A3B8; display: block; margin-bottom: 0.75rem;"></i>
    <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--color-navy-primary); margin-bottom: 0.35rem;">
        Belum ada butir pertanyaan dalam kuesioner ini
    </h3>
    <p style="font-size: 0.875rem; margin-bottom: 1.25rem;">
        Pilih pertanyaan dari <b>Bank Soal</b> atau tambahkan pertanyaan kustom baru.
    </p>
    <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
        <button onclick="openModal('importBankModal')" style="background: #7C3AED; color: #FFFFFF; border: none; padding: 0.65rem 1.1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="bi bi-collection-fill"></i> Import Bank Soal
        </button>
        <button onclick="openModal('createQuestionModal')" style="background: var(--color-navy-primary); color: #FFFFFF; border: none; padding: 0.65rem 1.1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="bi bi-plus-circle-fill"></i> Tambah Soal Baru
        </button>
    </div>
</div>
@endforelse

<!-- Modal Import from Question Bank -->
<div id="importBankModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 1rem;">
    <div class="modal-box" style="max-width: 720px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-navy-primary); margin: 0;">
                    Import Template dari Bank Soal
                </h3>
                <p style="color: #64748B; font-size: 0.8rem; margin-top: 0.2rem;">
                    Pilih butir pertanyaan yang ingin dimasukkan ke kuesioner ini.
                </p>
            </div>
            <button onclick="closeModal('importBankModal')" style="background: none; border: none; font-size: 1.25rem; color: #94A3B8; cursor: pointer;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.surveys.questions.import', $survey->id) }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; background: #F8FAFC; padding: 0.65rem 0.85rem; border-radius: 8px; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #334155;">Bagian (Section):</label>
                    <select name="section" style="padding: 0.35rem 0.55rem; border: 1px solid var(--color-border); border-radius: 6px; font-size: 0.8rem; background: #FFFFFF;">
                        <option value="B">Bagian B (Dimensi Inti)</option>
                        <option value="C">Bagian C (Pertanyaan Umum)</option>
                        <option value="A">Bagian A (Tambahan)</option>
                    </select>
                </div>

                <button type="button" onclick="toggleSelectAllBank(this)" style="background: none; border: none; color: #2563EB; font-weight: 700; font-size: 0.8rem; cursor: pointer;">
                    Pilih Semua
                </button>
            </div>

            <!-- List of templates in Bank -->
            <div style="max-height: 360px; overflow-y: auto; border: 1px solid var(--color-border); border-radius: 8px; margin-bottom: 1.25rem; -webkit-overflow-scrolling: touch;">
                @forelse($bankTemplates as $tpl)
                <label style="display: flex; align-items: flex-start; gap: 0.65rem; padding: 0.75rem 0.85rem; border-bottom: 1px solid #F1F5F9; cursor: pointer; transition: background 0.15s ease;">
                    <input type="checkbox" name="template_ids[]" value="{{ $tpl->id }}" class="bank-checkbox" style="margin-top: 0.25rem; width: 16px; height: 16px; flex-shrink: 0;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.2rem; flex-wrap: wrap;">
                            @if($tpl->dimension)
                                <span style="font-size: 0.7rem; font-weight: 800; background: {{ $tpl->dimension->color ?? '#2563EB' }}15; color: {{ $tpl->dimension->color ?? '#2563EB' }}; padding: 0.1rem 0.35rem; border-radius: 4px;">
                                    {{ $tpl->dimension->name }}
                                </span>
                            @endif
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--color-navy-primary);">
                                {{ $tpl->indicator_title ?: 'Template #' . $tpl->id }}
                            </span>
                            <span style="font-size: 0.7rem; color: #64748B;">
                                ({{ $tpl->question_type }})
                            </span>
                        </div>
                        <div style="font-size: 0.825rem; color: #334155; line-height: 1.4;">
                            {{ $tpl->question_text }}
                        </div>
                    </div>
                </label>
                @empty
                <div style="padding: 2rem; text-align: center; color: #64748B; font-size: 0.85rem;">
                    Belum ada template di Bank Soal.
                </div>
                @endforelse
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeModal('importBankModal')" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #64748B; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="background: #7C3AED; color: #FFFFFF; border: none; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    Import Terpilih
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Create Custom Question -->
<div id="createQuestionModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 1rem;">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-navy-primary); margin: 0;">
                Tambah Pertanyaan Kustom
            </h3>
            <button onclick="closeModal('createQuestionModal')" style="background: none; border: none; font-size: 1.25rem; color: #94A3B8; cursor: pointer;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.surveys.questions.store', $survey->id) }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Bagian (Section) *</label>
                    <select name="section" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="B">Bagian B (Inti)</option>
                        <option value="C">Bagian C (Umum)</option>
                        <option value="A">Bagian A</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Dimensi / Kategori</label>
                    <select name="dimension_id" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="">-- Pilih Dimensi --</option>
                        @foreach($dimensions as $dim)
                            <option value="{{ $dim->id }}">{{ $dim->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Indikator</label>
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
                    <select name="question_type" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="dual_rating">Dual Rating (Harapan & Kenyataan)</option>
                        <option value="single_rating">Single Rating (Skala Nilai)</option>
                        <option value="essay">Uraian / Deskriptif</option>
                        <option value="multiple_choice">Pilihan Ganda</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Skala Rating</label>
                    <select name="rating_scale" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="4">Skala 1 - 4</option>
                        <option value="5">Skala 1 - 5</option>
                        <option value="10">Skala 1 - 10</option>
                    </select>
                </div>
            </div>

            <!-- Logic Low Score Box -->
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.825rem; font-weight: 700; color: var(--color-navy-primary); cursor: pointer;">
                    <input type="checkbox" name="require_reason_on_low_score" value="1" checked style="width: 16px; height: 16px;">
                    Aktifkan Logic Kolom Alasan Wajib Diisi Jika Jawaban &le; 2
                </label>
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
                <button type="button" onclick="closeModal('createQuestionModal')" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #64748B; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="background: var(--color-navy-primary); color: #FFFFFF; border: none; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    Tambahkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Question in Survey -->
<div id="editQuestionModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 1rem;">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-navy-primary); margin: 0;">
                Edit Butir Pertanyaan
            </h3>
            <button onclick="closeModal('editQuestionModal')" style="background: none; border: none; font-size: 1.25rem; color: #94A3B8; cursor: pointer;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="editQuestionForm" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Bagian (Section) *</label>
                    <select name="section" id="editQSec" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="B">Bagian B (Inti)</option>
                        <option value="C">Bagian C (Umum)</option>
                        <option value="A">Bagian A</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Nomor Soal</label>
                    <input type="number" name="question_number" id="editQNum" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Dimensi / Kategori</label>
                    <select name="dimension_id" id="editQDim" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="">-- Pilih Dimensi --</option>
                        @foreach($dimensions as $dim)
                            <option value="{{ $dim->id }}">{{ $dim->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Indikator</label>
                <input type="text" name="indicator_title" id="editQInd" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Teks Pertanyaan *</label>
                <textarea name="question_text" id="editQTextSurvey" required rows="3" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; font-family: inherit; resize: vertical;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Tipe Pertanyaan *</label>
                    <select name="question_type" id="editQTypeSurvey" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="dual_rating">Dual Rating (Harapan & Kenyataan)</option>
                        <option value="single_rating">Single Rating (Skala Nilai)</option>
                        <option value="essay">Uraian / Deskriptif</option>
                        <option value="multiple_choice">Pilihan Ganda</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Skala Rating</label>
                    <select name="rating_scale" id="editQScaleSurvey" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem; background: #FFFFFF;">
                        <option value="4">Skala 1 - 4</option>
                        <option value="5">Skala 1 - 5</option>
                        <option value="10">Skala 1 - 10</option>
                    </select>
                </div>
            </div>

            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.825rem; font-weight: 700; color: var(--color-navy-primary); cursor: pointer;">
                    <input type="checkbox" name="require_reason_on_low_score" id="editQReasonSurvey" value="1" style="width: 16px; height: 16px;">
                    Aktifkan Logic Kolom Alasan Wajib Diisi Jika Jawaban &le; 2
                </label>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Khusus Status Pegawai</label>
                    <input type="text" name="applies_to_employment_status" id="editQEmpSurvey" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">Khusus Jabatan</label>
                    <input type="text" name="applies_to_positions" id="editQPosSurvey" style="width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.85rem;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem;">
                <button type="button" onclick="closeModal('editQuestionModal')" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #64748B; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
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

    function toggleSelectAllBank(btn) {
        const checkboxes = document.querySelectorAll('.bank-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        btn.textContent = allChecked ? 'Pilih Semua' : 'Batal Pilih Semua';
    }

    function editSurveyQuestion(q) {
        document.getElementById('editQuestionForm').action = `/admin/surveys/{{ $survey->id }}/questions/${q.id}`;
        document.getElementById('editQSec').value = q.section || 'B';
        document.getElementById('editQNum').value = q.question_number || 1;
        document.getElementById('editQDim').value = q.dimension_id || '';
        document.getElementById('editQInd').value = q.indicator_title || '';
        document.getElementById('editQTextSurvey').value = q.question_text || '';
        document.getElementById('editQTypeSurvey').value = q.question_type || 'dual_rating';
        document.getElementById('editQScaleSurvey').value = q.rating_scale || 4;
        document.getElementById('editQReasonSurvey').checked = !!(q.require_reason_on_low_score || q.section === 'B');
        document.getElementById('editQEmpSurvey').value = q.applies_to_employment_status || '';
        document.getElementById('editQPosSurvey').value = q.applies_to_positions || '';
        openModal('editQuestionModal');
    }

    window.onclick = function(event) {
        if (event.target.id === 'importBankModal') closeModal('importBankModal');
        if (event.target.id === 'createQuestionModal') closeModal('createQuestionModal');
        if (event.target.id === 'editQuestionModal') closeModal('editQuestionModal');
    }
</script>
@endsection
@endsection
