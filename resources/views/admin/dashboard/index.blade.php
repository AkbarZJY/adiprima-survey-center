@extends('layouts.app')

@section('title', 'Dashboard Analytics - Adiprima Survey Center')

@section('styles')
<style>
    .dashboard-container {
        width: 100%;
        max-width: 1300px;
        margin: 0 auto;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .survey-selector-card {
        background: linear-gradient(135deg, #0C2B64, #1E40AF);
        color: #FFFFFF;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
        box-shadow: 0 10px 25px -5px rgba(12, 43, 100, 0.2);
    }

    .nav-tabs-wrapper {
        display: flex;
        gap: 0.5rem;
        border-bottom: 2px solid #E2E8F0;
        margin-bottom: 1.75rem;
        overflow-x: auto;
        padding-bottom: 2px;
        scrollbar-width: thin;
        -webkit-overflow-scrolling: touch;
    }

    .nav-tabs-wrapper::-webkit-scrollbar {
        height: 4px;
    }

    .tab-item {
        padding: 0.75rem 1.25rem;
        font-weight: 700;
        font-size: 0.9rem;
        color: #64748B;
        text-decoration: none;
        border-bottom: 3px solid transparent;
        margin-bottom: -4px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
        flex-shrink: 0;
        border-radius: 6px 6px 0 0;
    }

    .tab-item:hover {
        color: var(--color-navy-primary);
        background: #F8FAFC;
    }

    .tab-item.active {
        color: var(--color-navy-primary);
        border-bottom-color: var(--color-navy-primary);
        background: #EFF6FF;
    }

    .card-panel {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 2rem;
    }

    .table-container {
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        -webkit-overflow-scrolling: touch;
        background: #FFFFFF;
    }

    .raw-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        text-align: left;
    }

    .raw-table th {
        background: #F8FAFC;
        color: #334155;
        font-weight: 700;
        padding: 0.85rem 0.75rem;
        border-bottom: 2px solid #E2E8F0;
        white-space: nowrap;
    }

    .raw-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #F1F5F9;
        color: #1E293B;
        white-space: nowrap;
    }

    .raw-table tr:hover {
        background: #F8FAFC;
    }

    .progress-bar-bg {
        width: 100%;
        height: 10px;
        background: #E2E8F0;
        border-radius: 9999px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #10B981, #059669);
        border-radius: 9999px;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: 1fr 1.35fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stats-stat-row {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .stat-badge-item {
        border-left: 2px solid rgba(255,255,255,0.2);
        padding-left: 1.25rem;
    }

    @media (max-width: 992px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .dashboard-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        .dashboard-header > div:last-child {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
            gap: 0.5rem;
        }

        .dashboard-header > div:last-child a {
            justify-content: center;
        }

        .survey-selector-card {
            padding: 1.25rem;
            flex-direction: column;
            align-items: stretch;
            gap: 1.25rem;
        }

        .stats-stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            width: 100%;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 1rem;
        }

        .stat-badge-item {
            border-left: none;
            padding-left: 0;
            text-align: center;
        }

        .card-panel {
            padding: 1.25rem 1rem;
            border-radius: 14px;
        }
    }

    @media (max-width: 480px) {
        .stats-stat-row {
            grid-template-columns: 1fr 1fr;
        }
        .stats-stat-row > div:last-child {
            grid-column: 1 / -1;
        }
        .tab-item {
            padding: 0.65rem 0.95rem;
            font-size: 0.825rem;
        }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    
    <!-- Top Header -->
    <div class="dashboard-header">
        <div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--color-navy-primary); letter-spacing: -0.02em;">
                Dashboard Analytics & Monitoring
            </h1>
            <p style="color: #64748B; font-size: 0.875rem; margin-top: 0.25rem;">
                Analisa data kuesioner, pantau partisipasi responden, dan eksplorasi insight per dimensi.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <a href="{{ route('admin.surveys.questions', $activeSurvey->id) }}" style="background: #FFFFFF; border: 1px solid #CBD5E1; color: #334155; text-decoration: none; padding: 0.6rem 0.85rem; border-radius: 8px; font-size: 0.825rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                <i class="bi bi-ui-checks"></i> Soal
            </a>

            <a href="{{ route('admin.dashboard.export', ['survey_id' => $activeSurvey->id]) }}" style="background: #10B981; color: #FFF; text-decoration: none; padding: 0.6rem 0.95rem; border-radius: 8px; font-size: 0.825rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: 0 4px 10px rgba(16,185,129,0.25);">
                <i class="bi bi-file-earmark-excel-fill"></i> Download CSV
            </a>
        </div>
    </div>

    <!-- Active Survey Selector Banner -->
    <div class="survey-selector-card">
        <div style="flex: 1; min-width: 260px;">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #93C5FD; margin-bottom: 0.35rem;">
                Pilih Instrumen Kuesioner:
            </div>
            <form action="{{ route('admin.dashboard') }}" method="GET" id="surveySwitcherForm">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <select name="survey_id" onchange="document.getElementById('surveySwitcherForm').submit()" style="background: #FFFFFF; color: #0F172A; font-weight: 700; font-size: 1rem; padding: 0.65rem 0.85rem; border-radius: 8px; border: none; width: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.15); cursor: pointer;">
                    @foreach($allSurveys as $s)
                        <option value="{{ $s->id }}" {{ $activeSurvey->id == $s->id ? 'selected' : '' }}>
                            {{ $s->title }} ({{ $s->category }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="stats-stat-row">
            <div class="stat-badge-item">
                <div style="font-size: 0.7rem; color: #BFDBFE;">Masa Aktif:</div>
                <div style="font-size: 0.85rem; font-weight: 700;">
                    @if($activeSurvey->start_date && $activeSurvey->end_date)
                        {{ $activeSurvey->start_date->format('d M') }} - {{ $activeSurvey->end_date->format('d M Y') }}
                    @else
                        Terbuka
                    @endif
                </div>
            </div>

            <div class="stat-badge-item">
                <div style="font-size: 0.7rem; color: #BFDBFE;">Total Soal:</div>
                <div style="font-size: 1.1rem; font-weight: 800;">
                    {{ $questions->count() }} Butir
                </div>
            </div>

            <div class="stat-badge-item">
                <div style="font-size: 0.7rem; color: #BFDBFE;">Responden:</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #6EE7B7;">
                    {{ $totalFilled }} Orang
                </div>
            </div>
        </div>
    </div>

    <!-- Submenu Tabs -->
    <div class="nav-tabs-wrapper">
        <a href="{{ route('admin.dashboard', ['survey_id' => $activeSurvey->id, 'tab' => 'raw_data']) }}" class="tab-item {{ $tab == 'raw_data' ? 'active' : '' }}">
            <i class="bi bi-table"></i> 1. Raw Data
        </a>
        <a href="{{ route('admin.dashboard', ['survey_id' => $activeSurvey->id, 'tab' => 'rekap_perolehan']) }}" class="tab-item {{ $tab == 'rekap_perolehan' ? 'active' : '' }}">
            <i class="bi bi-bar-chart-steps"></i> 2. Monitoring
        </a>
        <a href="{{ route('admin.dashboard', ['survey_id' => $activeSurvey->id, 'tab' => 'analisa']) }}" class="tab-item {{ $tab == 'analisa' ? 'active' : '' }}">
            <i class="bi bi-pie-chart-fill"></i> 3. Analisa Dimensi
        </a>
        <a href="{{ route('admin.dashboard', ['survey_id' => $activeSurvey->id, 'tab' => 'descriptive_reasons']) }}" class="tab-item {{ $tab == 'descriptive_reasons' ? 'active' : '' }}">
            <i class="bi bi-chat-left-text-fill"></i> 4. Feedback Uraian
        </a>
    </div>

    <!-- SUBMENU 1: DATA SURVEI (RAW DATA) -->
    @if($tab == 'raw_data')
    <div class="card-panel">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: #0F172A; margin-bottom: 1rem;">
            1. Data Survei Mentah (Raw Data) &ndash; {{ $activeSurvey->title }}
        </h3>
        
        <!-- Filter & Search Bar -->
        <form action="{{ route('admin.dashboard') }}" method="GET" style="display: flex; gap: 0.6rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
            <input type="hidden" name="survey_id" value="{{ $activeSurvey->id }}">
            <input type="hidden" name="tab" value="raw_data">
            
            <div style="flex: 1; min-width: 200px; position: relative;">
                <i class="bi bi-search" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / NIK / departemen..." style="width: 100%; padding: 0.6rem 0.85rem 0.6rem 2.3rem; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.85rem;">
            </div>

            <select name="department" style="padding: 0.6rem 0.85rem; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.85rem; background: #FFF;">
                <option value="">Semua Departemen</option>
                @foreach($allDepartments as $dept)
                    <option value="{{ $dept }}" {{ $departmentFilter == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                @endforeach
            </select>

            <select name="position" style="padding: 0.6rem 0.85rem; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.85rem; background: #FFF;">
                <option value="">Semua Jabatan</option>
                @foreach($allPositions as $pos)
                    <option value="{{ $pos }}" {{ $positionFilter == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                @endforeach
            </select>

            <button type="submit" style="background: var(--color-navy-primary); color: #FFF; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">
                Filter
            </button>

            @if($search || $departmentFilter || $positionFilter)
                <a href="{{ route('admin.dashboard', ['survey_id' => $activeSurvey->id, 'tab' => 'raw_data']) }}" style="background: #F1F5F9; color: #64748B; text-decoration: none; padding: 0.6rem 0.85rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center;">
                    Reset
                </a>
            @endif
        </form>

        <!-- Datatable -->
        <div class="table-container">
            <div class="table-touch-hint">
                <i class="bi bi-arrows-expand"></i> Geser tabel ke samping untuk melihat detail responden & jawaban lengkap
            </div>
            <table class="raw-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>Usia</th>
                        <th>Pendidikan</th>
                        <th>Status</th>
                        <th>Tenure</th>
                        <th>Departemen</th>
                        <th>Jabatan</th>
                        @foreach($questions as $q)
                            @if($q->question_type === 'dual_rating' || $q->section === 'B')
                                <th title="{{ $q->question_text }}">Q{{ $q->question_number }} (H)</th>
                                <th title="{{ $q->question_text }}">Q{{ $q->question_number }} (K)</th>
                            @else
                                <th title="{{ $q->question_text }}">Q{{ $q->question_number }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($responses as $index => $resp)
                    @php $answersMap = $resp->answers->keyBy('question_id'); @endphp
                    <tr>
                        <td>{{ $responses->firstItem() + $index }}</td>
                        <td>{{ $resp->nik ?? '-' }}</td>
                        <td style="font-weight: 700;">{{ $resp->name }}</td>
                        <td>{{ $resp->gender ? substr($resp->gender, 0, 1) : '-' }}</td>
                        <td>{{ $resp->age ?? '-' }}</td>
                        <td>{{ $resp->education ?? '-' }}</td>
                        <td>{{ $resp->employment_status ?? '-' }}</td>
                        <td>{{ $resp->tenure ?? '-' }}</td>
                        <td><span style="font-weight: 600; background: #EFF6FF; color: #1D4ED8; padding: 0.15rem 0.4rem; border-radius: 4px;">{{ $resp->department ?? '-' }}</span></td>
                        <td>{{ $resp->position ?? '-' }}</td>

                        @foreach($questions as $q)
                            @php $ans = $answersMap->get($q->id); @endphp
                            @if($q->question_type === 'dual_rating' || $q->section === 'B')
                                <td style="text-align: center; color: #2563EB; font-weight: 700;">
                                    {{ $ans ? $ans->expectation_score : '-' }}
                                </td>
                                <td style="text-align: center; font-weight: 700; color: {{ ($ans && $ans->reality_score <= 2) ? '#DC2626' : '#16A34A' }}; background: {{ ($ans && $ans->reality_score <= 2) ? '#FEE2E2' : 'transparent' }};">
                                    {{ $ans ? $ans->reality_score : '-' }}
                                </td>
                            @elseif($q->question_type === 'essay')
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;" title="{{ $ans ? $ans->text_answer : '' }}">
                                    {{ $ans ? Str::limit($ans->text_answer, 25) : '-' }}
                                </td>
                            @else
                                <td style="text-align: center; font-weight: 700;">
                                    {{ $ans ? ($ans->reality_score ?? $ans->text_answer ?? '-') : '-' }}
                                </td>
                            @endif
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 10 + $questions->count() }}" style="text-align: center; padding: 2rem; color: #64748B;">
                            Belum ada respon data survei yang masuk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.25rem; display: flex; justify-content: center; align-items: center;">
            {{ $responses->links() }}
        </div>
    </div>
    @endif

    <!-- SUBMENU 2: REKAP PEROLEHAN (MONITORING) -->
    @if($tab == 'rekap_perolehan')
    <div class="card-panel">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: #0F172A; margin-bottom: 1.25rem;">
            2. Rekap Perolehan Survei (Department Monitoring)
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1.25rem;">
                <div style="font-size: 0.8rem; font-weight: 600; color: #64748B;">Total Responden Mengisi</div>
                <div style="font-size: 1.85rem; font-weight: 800; color: var(--color-navy-primary); margin: 0.25rem 0;">
                    {{ $totalFilled }} <small style="font-size: 0.95rem; color: #64748B; font-weight: 500;">/ {{ $totalTarget }} Target</small>
                </div>
                <div class="progress-bar-bg" style="margin-top: 0.75rem;">
                    <div class="progress-bar-fill" style="width: {{ min(100, $overallProgressPct) }}%;"></div>
                </div>
                <div style="font-size: 0.8rem; font-weight: 700; color: #059669; margin-top: 0.4rem;">
                    {{ $overallProgressPct }}% Tercapai
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-touch-hint">
                <i class="bi bi-arrows-expand"></i> Geser tabel ke samping untuk melihat rekap target & progress per unit
            </div>
            <table class="raw-table">
                <thead>
                    <tr>
                        <th>Grup Unit / Departemen</th>
                        <th style="text-align: center;">Target</th>
                        <th style="text-align: center;">Masuk</th>
                        <th style="width: 35%;">Progress</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapData as $rk)
                    <tr>
                        <td style="font-weight: 700; color: #0F172A;">{{ $rk['group'] }}</td>
                        <td style="text-align: center;">{{ $rk['target'] }}</td>
                        <td style="text-align: center; font-weight: 700; color: var(--color-navy-primary);">{{ $rk['filled'] }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="progress-bar-bg" style="flex: 1;">
                                    <div class="progress-bar-fill" style="width: {{ min(100, $rk['progress']) }}%;"></div>
                                </div>
                                <span style="font-size: 0.8rem; font-weight: 700; width: 40px;">{{ $rk['progress'] }}%</span>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            @if($rk['progress'] >= 100)
                                <span style="background: #DEF7EC; color: #03543F; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">Tuntas</span>
                            @elseif($rk['progress'] >= 50)
                                <span style="background: #FEF08A; color: #854D0E; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">Berjalan</span>
                            @else
                                <span style="background: #FEE2E2; color: #991B1B; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">Rendah</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- SUBMENU 3: ANALISA DIMENSI (GRAFIK) -->
    @if($tab == 'analisa')
    <div class="analytics-grid">
        <!-- Summary Dimension Cards -->
        <div>
            <div class="card-panel" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #0F172A; margin-bottom: 1rem;">
                    Skor Rata-rata per Dimensi
                </h3>

                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    @foreach($dimensionScores as $dim => $score)
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 0.85rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <span style="font-weight: 700; font-size: 0.875rem; color: #334155;">{{ $dim }}</span>
                            <span style="font-size: 1.15rem; font-weight: 800; color: var(--color-navy-primary);">
                                {{ number_format($score, 2) }} <small style="font-size: 0.75rem; color: #64748B;">/ 4.00</small>
                            </span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ ($score / 4) * 100 }}%;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 0.35rem; font-size: 0.75rem; color: #64748B;">
                            <span>Harapan: <b>{{ number_format($dimensionExpectationScores[$dim] ?? 0, 2) }}</b></span>
                            <span>Gap: <b style="color: {{ ($dimensionGaps[$dim] ?? 0) > 0.5 ? '#DC2626' : '#16A34A' }};">{{ number_format($dimensionGaps[$dim] ?? 0, 2) }}</b></span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div style="background: #DEF7EC; border: 1px solid #84E1BC; border-radius: 12px; padding: 1rem;">
                    <div style="font-size: 0.7rem; font-weight: 700; color: #03543F; text-transform: uppercase;">Dimensi Tertinggi</div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: #046C4E; margin: 0.25rem 0;">{{ $highestDimension }}</div>
                    <div style="font-size: 0.8rem; color: #03543F;">Skor: <b>{{ number_format($highestScore, 2) }}</b></div>
                </div>

                <div style="background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 12px; padding: 1rem;">
                    <div style="font-size: 0.7rem; font-weight: 700; color: #991B1B; text-transform: uppercase;">Dimensi Terendah</div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: #B91C1C; margin: 0.25rem 0;">{{ $lowestDimension }}</div>
                    <div style="font-size: 0.8rem; color: #991B1B;">Skor: <b>{{ number_format($lowestScore, 2) }}</b></div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card-panel">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #0F172A; margin-bottom: 1rem;">
                Visualisasi Radar & Bar Chart
            </h3>
            
            <div style="position: relative; min-height: 280px; max-height: 340px; margin-bottom: 1.5rem;">
                <canvas id="dimensionRadarChart"></canvas>
            </div>

            <div style="position: relative; min-height: 220px; max-height: 260px;">
                <canvas id="dimensionBarChart"></canvas>
            </div>
        </div>
    </div>
    @endif

    <!-- SUBMENU 4: FEEDBACK DESKRIPTIF & ALASAN -->
    @if($tab == 'descriptive_reasons')
    <div class="card-panel">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: #0F172A; margin-bottom: 0.75rem;">
            4. Rekapitulasi Alasan Nilai Rendah & Masukan Uraian
        </h3>
        <p style="color: #64748B; font-size: 0.85rem; margin-bottom: 1.5rem;">
            Catatan alasan yang wajib diisi responden saat memilih nilai kenyataan 1 atau 2, serta jawaban terbuka lainnya.
        </p>

        <!-- Low Score Reasons Feed -->
        <h4 style="font-size: 0.9rem; font-weight: 800; color: #B91C1C; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
            <i class="bi bi-exclamation-triangle-fill"></i> Alasan Khusus Nilai Rendah (Skor &le; 2)
        </h4>

        <div style="display: grid; gap: 0.75rem; margin-bottom: 1.75rem;">
            @forelse($lowScoreReasons as $item)
            <div style="background: #FFF5F5; border: 1px solid #FECACA; border-radius: 10px; padding: 0.85rem 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; flex-wrap: wrap; gap: 0.25rem;">
                    <div>
                        <span style="font-weight: 700; font-size: 0.85rem; color: #991B1B;">
                            {{ $item->question?->indicator_title ?? 'Q' . $item->question?->question_number }}
                        </span>
                        <span style="font-size: 0.75rem; color: #7F1D1D; margin-left: 0.35rem;">
                            (Kenyataan: <b>{{ $item->reality_score }}</b>, Harapan: <b>{{ $item->expectation_score }}</b>)
                        </span>
                    </div>
                    <span style="font-size: 0.75rem; color: #64748B;">
                        {{ $item->response?->department ?? 'Umum' }} &bull; {{ $item->response?->position ?? 'Pegawai' }}
                    </span>
                </div>
                <div style="font-size: 0.85rem; color: #1E293B; line-height: 1.45; font-style: italic;">
                    &ldquo;{{ $item->reason_text }}&rdquo;
                </div>
            </div>
            @empty
            <div style="padding: 1.5rem; text-align: center; color: #64748B; background: #F8FAFC; border-radius: 8px; font-size: 0.85rem;">
                Belum ada catatan alasan nilai rendah.
            </div>
            @endforelse
        </div>

        <!-- General Essay Feed -->
        <h4 style="font-size: 0.9rem; font-weight: 800; color: #4338CA; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
            <i class="bi bi-chat-quote-fill"></i> Masukan / Uraian Terbuka Responden
        </h4>

        <div style="display: grid; gap: 0.75rem;">
            @forelse($essayAnswers as $item)
            <div style="background: #EEF2FF; border: 1px solid #C7D2FE; border-radius: 10px; padding: 0.85rem 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; flex-wrap: wrap; gap: 0.25rem;">
                    <span style="font-weight: 700; font-size: 0.85rem; color: #3730A3;">
                        {{ $item->question?->indicator_title ?? 'Uraian Pertanyaan' }}
                    </span>
                    <span style="font-size: 0.75rem; color: #64748B;">
                        {{ $item->response?->department ?? 'Umum' }} &bull; {{ $item->response?->position ?? 'Pegawai' }}
                    </span>
                </div>
                <div style="font-size: 0.85rem; color: #1E293B; line-height: 1.45;">
                    &ldquo;{{ $item->text_answer }}&rdquo;
                </div>
            </div>
            @empty
            <div style="padding: 1.5rem; text-align: center; color: #64748B; background: #F8FAFC; border-radius: 8px; font-size: 0.85rem;">
                Belum ada jawaban uraian terbuka.
            </div>
            @endforelse
        </div>
    </div>
    @endif

</div>
@endsection

@section('scripts')
@if($tab == 'analisa')
<script>
    const dimLabels = {!! json_encode(array_keys($dimensionScores)) !!};
    const realScores = {!! json_encode(array_values($dimensionScores)) !!};
    const expScores = {!! json_encode(array_values($dimensionExpectationScores)) !!};

    // Radar Chart
    const radarCtx = document.getElementById('dimensionRadarChart').getContext('2d');
    new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: dimLabels,
            datasets: [
                {
                    label: 'Tingkat Kenyataan (Perception)',
                    data: realScores,
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    borderColor: '#2563EB',
                    pointBackgroundColor: '#2563EB',
                    borderWidth: 2
                },
                {
                    label: 'Tingkat Harapan (Expectation)',
                    data: expScores,
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: '#10B981',
                    pointBackgroundColor: '#10B981',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    min: 0,
                    max: 4,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 11 } }
                }
            }
        }
    });

    // Bar Chart
    const barCtx = document.getElementById('dimensionBarChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: dimLabels,
            datasets: [
                {
                    label: 'Kenyataan',
                    data: realScores,
                    backgroundColor: '#2563EB'
                },
                {
                    label: 'Harapan',
                    data: expScores,
                    backgroundColor: '#10B981'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    min: 0,
                    max: 4,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 11 } }
                }
            }
        }
    });
</script>
@endif
@endsection
