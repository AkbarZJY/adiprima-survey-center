@extends('layouts.app')

@section('title', 'Buat Kuesioner Baru - Adiprima Survey Center')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('admin.surveys.index') }}" style="color: #64748B; text-decoration: none; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; margin-bottom: 0.5rem;">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kuesioner
        </a>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--color-navy-primary); letter-spacing: -0.02em;">
            Buat Instrumen Kuesioner Baru
        </h1>
        <p style="color: #64748B; font-size: 0.95rem; margin-top: 0.25rem;">
            Tentukan identitas survei dan atur rentang tanggal masa aktif pengisian oleh karyawan.
        </p>
    </div>

    <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: 14px; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <form action="{{ route('admin.surveys.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">
                    Judul Kuesioner *
                </label>
                <input type="text" name="title" required value="{{ old('title') }}" placeholder="Contoh: Survei Budaya Kerja Tahun 2026" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.95rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">
                        Kategori Survei *
                    </label>
                    <input type="text" name="category" required value="{{ old('category') }}" placeholder="Contoh: Engagement, Budaya Kerja, Kepuasan" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.95rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">
                        Ikon (Bootstrap Icons)
                    </label>
                    <input type="text" name="icon" value="{{ old('icon', 'bi-clipboard-data') }}" placeholder="bi-graph-up-arrow" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.95rem;">
                </div>
            </div>

            <!-- Active Dates Section -->
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
                <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--color-navy-primary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                    <i class="bi bi-calendar-check" style="color: #2563EB;"></i> Pengaturan Masa Aktif Kuesioner
                </h4>
                <p style="font-size: 0.8rem; color: #64748B; margin-bottom: 1rem;">
                    Tentukan tanggal buka dan batas akhir pengisian survei. Responden hanya dapat mengisi selama rentang waktu ini.
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">
                            Tanggal Mulai (Start Date)
                        </label>
                        <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.9rem; background: #FFFFFF;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">
                            Tanggal Selesai (End Date)
                        </label>
                        <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.9rem; background: #FFFFFF;">
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">
                    Deskripsi / Petunjuk Pengisian
                </label>
                <textarea name="description" rows="4" placeholder="Jelaskan tujuan survei ini dan petunjuk singkat bagi responden..." style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--color-border); border-radius: 8px; font-size: 0.95rem; font-family: inherit; resize: vertical;">{{ old('description') }}</textarea>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 700; color: #1E293B; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" checked style="width: 18px; height: 18px;">
                    Aktifkan Kuesioner Ini (Dapat Diakses oleh Karyawan)
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid #F1F5F9; padding-top: 1.5rem;">
                <a href="{{ route('admin.surveys.index') }}" style="background: #F1F5F9; color: #64748B; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem;">
                    Batal
                </a>
                <button type="submit" style="background: var(--color-navy-primary); color: #FFFFFF; border: none; padding: 0.75rem 1.75rem; border-radius: 8px; font-weight: 700; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="bi bi-check-circle-fill"></i> Simpan & Lanjut ke Pertanyaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
