@extends('layouts.app')

@section('title', 'Manajemen Kuesioner Survei - Adiprima Survey Center')

@section('styles')
<style>
    .surveys-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .surveys-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .surveys-header {
            flex-direction: column;
            align-items: stretch;
        }

        .surveys-header a {
            justify-content: center;
            width: 100%;
        }

        .surveys-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="surveys-header">
    <div>
        <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--color-navy-primary); letter-spacing: -0.02em;">
            Manajemen Kuesioner & Survei
        </h1>
        <p style="color: #64748B; font-size: 0.9rem; margin-top: 0.25rem;">
            Kelola instrumen survei perusahaan, atur masa aktif pengisian, dan susun butir pertanyaan secara dinamis.
        </p>
    </div>
    <a href="{{ route('admin.surveys.create') }}" style="background: var(--color-navy-primary); color: #FFFFFF; text-decoration: none; padding: 0.75rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: background 0.2s ease; box-shadow: 0 4px 10px rgba(12, 43, 100, 0.2);">
        <i class="bi bi-plus-circle-fill"></i> Buat Kuesioner Baru
    </a>
</div>

<!-- Surveys Grid -->
<div class="surveys-grid">
    @forelse($surveys as $srv)
    @php
        $isActive = $srv->is_active;
        $isWithinDates = $srv->isWithinActiveDate();
    @endphp
    <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: 14px; padding: 1.25rem 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <!-- Header Card -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: linear-gradient(135deg, #0C2B64, #2563EB); color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; box-shadow: 0 4px 10px rgba(37,99,235,0.2); flex-shrink: 0;">
                        <i class="bi {{ $srv->icon ?: 'bi-clipboard-data' }}"></i>
                    </div>
                    <div>
                        <span style="font-size: 0.725rem; font-weight: 800; color: #2563EB; background: #EFF6FF; padding: 0.15rem 0.45rem; border-radius: 4px;">
                            {{ $srv->category }}
                        </span>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--color-navy-primary); margin-top: 0.2rem; line-height: 1.3;">
                            {{ $srv->title }}
                        </h3>
                    </div>
                </div>

                <!-- Status Badge -->
                <form action="{{ route('admin.surveys.toggle', $srv->id) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: {{ $isActive ? '#DEF7EC' : '#FEE2E2' }}; color: {{ $isActive ? '#03543F' : '#9B1C1C' }}; border: 1px solid {{ $isActive ? '#84E1BC' : '#FCA5A5' }}; padding: 0.25rem 0.55rem; border-radius: 9999px; font-size: 0.725rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.35rem;" title="Klik untuk mengubah status aktif">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $isActive ? '#10B981' : '#EF4444' }};"></span>
                        {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                    </button>
                </form>
            </div>

            <p style="color: #64748B; font-size: 0.85rem; line-height: 1.45; margin-bottom: 1.25rem;">
                {{ $srv->description ?: 'Tidak ada deskripsi kuesioner.' }}
            </p>

            <!-- Validity Range Card -->
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.8rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <span style="color: #64748B; display: flex; align-items: center; gap: 0.35rem;">
                        <i class="bi bi-calendar-range"></i> Masa Aktif:
                    </span>
                    <span style="font-weight: 700; color: #1E293B;">
                        @if($srv->start_date && $srv->end_date)
                            {{ $srv->start_date->format('d M') }} - {{ $srv->end_date->format('d M Y') }}
                        @elseif($srv->start_date)
                            Mulai {{ $srv->start_date->format('d M Y') }}
                        @elseif($srv->end_date)
                            Hingga {{ $srv->end_date->format('d M Y') }}
                        @else
                            <span style="color: #94A3B8;">Tidak dibatasi</span>
                        @endif
                    </span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748B; display: flex; align-items: center; gap: 0.35rem;">
                        <i class="bi bi-clock-history"></i> Status Periode:
                    </span>
                    @if($isWithinDates)
                        <span style="color: #16A34A; font-weight: 700;">Sedang Dibuka</span>
                    @elseif($srv->start_date && now()->lt($srv->start_date))
                        <span style="color: #D97706; font-weight: 700;">Belum Dibuka</span>
                    @else
                        <span style="color: #DC2626; font-weight: 700;">Telah Berakhir</span>
                    @endif
                </div>
            </div>

            <!-- Stats -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div style="background: #EFF6FF; border-radius: 8px; padding: 0.65rem; text-align: center;">
                    <div style="font-size: 1.2rem; font-weight: 800; color: #1E40AF;">{{ $srv->questions_count }}</div>
                    <div style="font-size: 0.725rem; color: #3B82F6; font-weight: 600;">Butir Pertanyaan</div>
                </div>
                <div style="background: #F0FDF4; border-radius: 8px; padding: 0.65rem; text-align: center;">
                    <div style="font-size: 1.2rem; font-weight: 800; color: #166534;">{{ $srv->responses_count }}</div>
                    <div style="font-size: 0.725rem; color: #16A34A; font-weight: 600;">Responden Masuk</div>
                </div>
            </div>
        </div>

        <!-- Action Footer -->
        <div style="border-top: 1px solid #F1F5F9; padding-top: 1rem; display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.surveys.questions', $srv->id) }}" style="background: var(--color-navy-primary); color: #FFFFFF; text-decoration: none; padding: 0.5rem 0.85rem; border-radius: 6px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                <i class="bi bi-ui-checks"></i> Kelola Pertanyaan
            </a>

            <div style="display: flex; gap: 0.35rem;">
                <a href="{{ route('survey.form', $srv->slug) }}" target="_blank" style="background: #F1F5F9; color: #334155; border: 1px solid #CBD5E1; padding: 0.45rem 0.65rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; text-decoration: none;" title="Buka Form Kuesioner">
                    <i class="bi bi-box-arrow-up-right"></i>
                </a>
                <a href="{{ route('admin.surveys.edit', $srv->id) }}" style="background: #F1F5F9; color: #334155; border: 1px solid #CBD5E1; padding: 0.45rem 0.65rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; text-decoration: none;" title="Edit Pengaturan & Tanggal">
                    <i class="bi bi-gear-fill"></i>
                </a>
                <form action="{{ route('admin.surveys.destroy', $srv->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus survei {{ $srv->title }}?');" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5; padding: 0.45rem 0.65rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer;" title="Hapus Survei">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; background: #FFFFFF; border: 1px dashed var(--color-border); border-radius: 12px; padding: 3rem; text-align: center; color: #64748B;">
        <i class="bi bi-kanban" style="font-size: 2.5rem; color: #94A3B8; display: block; margin-bottom: 0.5rem;"></i>
        <p style="font-size: 1rem; font-weight: 600;">Belum ada instrumen kuesioner survei.</p>
        <p style="font-size: 0.875rem;">Klik "Buat Kuesioner Baru" untuk mulai membuat instrumen survei pertama Anda.</p>
    </div>
    @endforelse
</div>
@endsection
