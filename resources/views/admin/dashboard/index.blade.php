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

    .category-nav-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .category-nav-card {
        background: #FFFFFF;
        border: 2px solid #E2E8F0;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        position: relative;
        overflow: hidden;
    }

    .category-nav-card:hover {
        border-color: #3B82F6;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.12);
    }

    .category-nav-card.active {
        border-color: var(--color-navy-primary);
        background: linear-gradient(145deg, #FFFFFF, #EFF6FF);
        box-shadow: 0 8px 20px -4px rgba(12, 43, 100, 0.15);
    }

    .category-nav-card.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: var(--color-navy-primary);
    }

    .cat-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .category-nav-card.active .cat-icon-box {
        background: var(--color-navy-primary);
        color: #FFFFFF;
    }

    .category-nav-card:not(.active) .cat-icon-box {
        background: #F1F5F9;
        color: #475569;
    }

    .cat-title-text {
        font-weight: 800;
        font-size: 0.95rem;
        color: #0F172A;
        line-height: 1.3;
    }

    .category-nav-card.active .cat-title-text {
        color: var(--color-navy-primary);
    }

    .cat-badge-info {
        font-size: 0.75rem;
        color: #64748B;
        margin-top: 0.2rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .survey-selector-card {
        background: linear-gradient(135deg, #0C2B64 0%, #1E40AF 100%);
        color: #FFFFFF;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
        box-shadow: 0 10px 25px -5px rgba(12, 43, 100, 0.25);
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
        .category-nav-grid {
            grid-template-columns: 1fr;
        }

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

    .feedback-q-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .feedback-q-header {
        background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .feedback-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .feedback-table th {
        background: #F1F5F9;
        color: #334155;
        font-weight: 700;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #E2E8F0;
        font-size: 0.8rem;
    }

    .feedback-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: top;
    }

    .badge-count-total {
        background: #EFF6FF;
        color: #1D4ED8;
        border: 1px solid #BFDBFE;
        font-weight: 800;
        font-size: 0.825rem;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        white-space: nowrap;
    }

    .badge-count-total.high-freq {
        background: #FEF3C7;
        color: #92400E;
        border-color: #FDE68A;
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
    </div>

    <!-- 1. Kategori Survey Selector Nav -->
    <div style="margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.8rem; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em;">
            <i class="bi bi-grid-fill" style="color: var(--color-navy-primary);"></i> Pilih Kategori Survei:
        </span>
        <span style="font-size: 0.75rem; color: #94A3B8;">
            Klik kategori untuk beralih topik kuesioner
        </span>
    </div>

    <div class="category-nav-grid">
        @foreach($allCategories as $catName)
            @php
                $catSurveys = $surveysByCategory->get($catName, collect());
                $catCount = $catSurveys->count();
                $isActiveCat = ($selectedCategory === $catName);
                
                $catIcon = match(trim($catName)) {
                    'Survey Budaya Kerja', 'Budaya Kerja' => 'bi-people-fill',
                    'Employee Engagement Survey', 'Engagement Survey' => 'bi-graph-up-arrow',
                    'Customer Satisfaction Survey', 'Kepuasan Pelanggan' => 'bi-award-fill',
                    'Internal Satisfaction Survey', 'Kepuasan Internal' => 'bi-chat-left-quote-fill',
                    default => 'bi-clipboard-check-fill'
                };
            @endphp
            <a href="{{ route('admin.dashboard', ['category' => $catName, 'tab' => $tab]) }}" 
               class="category-nav-card {{ $isActiveCat ? 'active' : '' }}"
               title="Pilih kategori {{ $catName }}">
                <div class="cat-icon-box">
                    <i class="bi {{ $catIcon }}"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div class="cat-title-text">{{ $catName }}</div>
                    <div class="cat-badge-info">
                        <span><i class="bi bi-collection"></i> {{ $catCount }} Edisi Survei</span>
                        @if($isActiveCat)
                            <span style="background: #DEF7EC; color: #03543F; font-size: 0.65rem; font-weight: 800; padding: 0.1rem 0.4rem; border-radius: 4px;">AKTIF</span>
                        @endif
                    </div>
                </div>
                @if($isActiveCat)
                    <div style="color: var(--color-navy-primary); font-size: 1.1rem;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                @endif
            </a>
        @endforeach
    </div>

    <!-- 2. Active Survey Selector Banner (With Category Dropdown for inner surveys) -->
    <div class="survey-selector-card">
        <div style="flex: 1; min-width: 280px;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem; flex-wrap: wrap;">
                <span style="font-size: 0.725rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #93C5FD; background: rgba(255,255,255,0.12); padding: 0.2rem 0.55rem; border-radius: 6px;">
                    <i class="bi bi-tag-fill"></i> Kategori: {{ $selectedCategory }}
                </span>
                <span style="font-size: 0.75rem; color: #BFDBFE;">
                    &bull; Pilih Edisi Kuesioner:
                </span>
            </div>

            <form action="{{ route('admin.dashboard') }}" method="GET" id="surveySwitcherForm">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="category" value="{{ $selectedCategory }}">

                <div style="position: relative;">
                    <select name="survey_id" onchange="this.form.requestSubmit ? this.form.requestSubmit() : this.form.dispatchEvent(new Event('submit', {bubbles: true, cancelable: true}))" style="background: #FFFFFF; color: #0F172A; font-weight: 700; font-size: 0.975rem; padding: 0.75rem 1rem; border-radius: 10px; border: none; width: 100%; box-shadow: 0 4px 14px rgba(0,0,0,0.18); cursor: pointer; appearance: auto;">
                        @php
                            $currentCatSurveys = $surveysByCategory->get($selectedCategory, collect());
                        @endphp
                        
                        @if($currentCatSurveys->count() > 0)
                            <optgroup label="Daftar Survei dalam Kategori: {{ $selectedCategory }}">
                                @foreach($currentCatSurveys as $s)
                                    <option value="{{ $s->id }}" {{ $activeSurvey->id == $s->id ? 'selected' : '' }}>
                                        {{ $s->title }} {{ $s->is_active ? '● (Aktif)' : '○ (Nonaktif / Arsip)' }} &bull; {{ $s->responses_count }} Responden
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($allSurveys->count() > $currentCatSurveys->count())
                            <optgroup label="── Kategori Lainnya ──">
                                @foreach($allSurveys as $otherS)
                                    @if($otherS->category !== $selectedCategory)
                                        <option value="{{ $otherS->id }}">
                                            [{{ $otherS->category }}] {{ $otherS->title }}
                                        </option>
                                    @endif
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>
            </form>
        </div>

        <div class="stats-stat-row">
            <div class="stat-badge-item">
                <div style="font-size: 0.7rem; color: #BFDBFE;">Masa Aktif:</div>
                <div style="font-size: 0.85rem; font-weight: 700;">
                    @if($activeSurvey->start_date && $activeSurvey->end_date)
                        {{ $activeSurvey->start_date->format('d M') }} - {{ $activeSurvey->end_date->format('d M Y') }}
                    @elseif($activeSurvey->start_date)
                        Mulai {{ $activeSurvey->start_date->format('d M Y') }}
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
        <a href="{{ route('admin.dashboard', ['survey_id' => $activeSurvey->id, 'category' => $selectedCategory, 'tab' => 'raw_data']) }}" class="tab-item {{ $tab == 'raw_data' ? 'active' : '' }}">
            <i class="bi bi-table"></i> 1. Raw Data
        </a>
        <a href="{{ route('admin.dashboard', ['survey_id' => $activeSurvey->id, 'category' => $selectedCategory, 'tab' => 'rekap_perolehan']) }}" class="tab-item {{ $tab == 'rekap_perolehan' ? 'active' : '' }}">
            <i class="bi bi-bar-chart-steps"></i> 2. Monitoring
        </a>
        <a href="{{ route('admin.dashboard', ['survey_id' => $activeSurvey->id, 'category' => $selectedCategory, 'tab' => 'analisa']) }}" class="tab-item {{ $tab == 'analisa' ? 'active' : '' }}">
            <i class="bi bi-pie-chart-fill"></i> 3. Analisa Dimensi
        </a>
        <a href="{{ route('admin.dashboard', ['survey_id' => $activeSurvey->id, 'category' => $selectedCategory, 'tab' => 'descriptive_reasons']) }}" class="tab-item {{ $tab == 'descriptive_reasons' ? 'active' : '' }}">
            <i class="bi bi-chat-left-text-fill"></i> 4. Feedback Uraian
        </a>
    </div>

    <!-- SUBMENU 1: DATA SURVEI (RAW DATA) -->
    @if($tab == 'raw_data')
    <div class="card-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0;">
                    1. Data Survei Mentah (Raw Data) &ndash; {{ $activeSurvey->title }}
                </h3>
                <p style="color: #64748B; font-size: 0.825rem; margin: 0.25rem 0 0 0;">
                    Daftar seluruh respon butir pertanyaan beserta waktu pengerjaan kuesioner.
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ route('admin.surveys.questions', $activeSurvey->id) }}" style="background: #FFFFFF; border: 1px solid #CBD5E1; color: #334155; text-decoration: none; padding: 0.55rem 0.95rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                    <i class="bi bi-ui-checks"></i> Soal
                </a>
                <a href="{{ route('admin.dashboard.export', ['survey_id' => $activeSurvey->id]) }}" style="background: #10B981; color: #FFFFFF; text-decoration: none; padding: 0.55rem 1.1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25); transition: all 0.15s ease;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
                    <i class="bi bi-file-earmark-excel-fill" style="font-size: 1rem;"></i> Unduh Excel (.xlsx)
                </a>
            </div>
        </div>
        
        <!-- Filter & Search Bar -->
        <form action="{{ route('admin.dashboard') }}" method="GET" style="display: flex; gap: 0.6rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
            <input type="hidden" name="survey_id" value="{{ $activeSurvey->id }}">
            <input type="hidden" name="category" value="{{ $selectedCategory }}">
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
                <a href="{{ route('admin.dashboard', ['survey_id' => $activeSurvey->id, 'category' => $selectedCategory, 'tab' => 'raw_data']) }}" style="background: #F1F5F9; color: #64748B; text-decoration: none; padding: 0.6rem 0.85rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center;">
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
                        <th style="min-width: 140px;">Mulai Dikerjakan</th>
                        <th style="min-width: 140px;">Selesai Dikerjakan</th>
                        <th style="min-width: 110px;">Lama Pengerjaan</th>
                        @foreach($questions as $q)
                            @if($q->question_type === 'dual_rating' || (empty($q->question_type) && $q->section === 'B'))
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
                    @php 
                        $answersMap = $resp->answers->keyBy('question_id');
                        $startedAt = $resp->started_at ?? $resp->created_at;
                        $submittedAt = $resp->submitted_at ?? $resp->updated_at;
                        $durationStr = '-';
                        if ($startedAt && $submittedAt) {
                            $diffSec = $startedAt->diffInSeconds($submittedAt);
                            $minVal = round($diffSec / 60, 1);
                            $durationStr = ($minVal < 1 ? '< 1' : $minVal) . ' Menit';
                        }
                    @endphp
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
                        <td style="font-size: 0.775rem; color: #475569; white-space: nowrap;">
                            <i class="bi bi-clock-history" style="color: #3B82F6;"></i> {{ $startedAt ? $startedAt->format('d/m/Y ; H:i') : '-' }}
                        </td>
                        <td style="font-size: 0.775rem; color: #475569; white-space: nowrap;">
                            <i class="bi bi-check-circle" style="color: #10B981;"></i> {{ $submittedAt ? $submittedAt->format('d/m/Y ; H:i') : '-' }}
                        </td>
                        <td style="text-align: center; white-space: nowrap;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: #0369A1; background: #E0F2FE; padding: 0.2rem 0.5rem; border-radius: 6px; display: inline-block;">
                                {{ $durationStr }}
                            </span>
                        </td>

                        @foreach($questions as $q)
                            @php $ans = $answersMap->get($q->id); @endphp
                            @if($q->question_type === 'dual_rating' || (empty($q->question_type) && $q->section === 'B'))
                                <td style="text-align: center; color: #2563EB; font-weight: 700;">
                                    {{ $ans ? $ans->expectation_score : '-' }}
                                </td>
                                <td style="text-align: center; font-weight: 700; color: {{ ($ans && $ans->reality_score <= 2) ? '#DC2626' : '#16A34A' }}; background: {{ ($ans && $ans->reality_score <= 2) ? '#FEE2E2' : 'transparent' }};">
                                    {{ $ans ? $ans->reality_score : '-' }}
                                </td>
                            @elseif($q->question_type === 'multiple_choice')
                                <td style="text-align: center; font-size: 0.825rem; font-weight: 600; color: #1E293B;" title="{{ $ans ? $ans->text_answer : '' }}">
                                    {{ $ans ? ($ans->text_answer ?? '-') : '-' }}
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
                        <td colspan="{{ 13 + $questions->count() }}" style="text-align: center; padding: 2rem; color: #64748B;">
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0;">
                    4. Rekapitulasi Feedback & Uraian Per Butir Pertanyaan
                </h3>
                <p style="color: #64748B; font-size: 0.825rem; margin: 0.25rem 0 0 0;">
                    Daftar masukan, alasan skor rendah, dan uraian terbuka responden yang dikelompokkan per butir pertanyaan serta dihitung frekuensi kesamaan keyword/poin masukan.
                </p>
            </div>
            <div style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; padding: 0.45rem 0.85rem; border-radius: 8px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="bi bi-chat-square-text-fill"></i> {{ count($feedbackByQuestion) }} Butir Pertanyaan Memiliki Feedback
            </div>
        </div>

        @forelse($feedbackByQuestion as $index => $fb)
        @php
            $q = $fb['question'];
            $indicator = $q->indicator_title ?? $q->dimensionName ?? ('Bagian ' . ($q->section ?? 'B'));
        @endphp
        <div class="feedback-q-card">
            <!-- Question Header Card -->
            <div class="feedback-q-header">
                <div style="flex: 1; min-width: 260px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
                        <span style="background: var(--color-navy-primary); color: #FFFFFF; font-weight: 800; font-size: 0.75rem; padding: 0.2rem 0.55rem; border-radius: 6px;">
                            Q{{ $q->question_number }}
                        </span>
                        <span style="background: #F1F5F9; color: #475569; font-weight: 700; font-size: 0.75rem; padding: 0.2rem 0.55rem; border-radius: 6px; border: 1px solid #E2E8F0;">
                            {{ $indicator }}
                        </span>
                        <span style="font-size: 0.75rem; color: #64748B;">
                            Tipe: <b>{{ ucfirst(str_replace('_', ' ', $q->question_type ?? 'Rating')) }}</b>
                        </span>
                    </div>
                    <div style="font-size: 0.925rem; font-weight: 700; color: #1E293B; line-height: 1.4;">
                        {{ $q->question_text }}
                    </div>
                </div>

                <div style="text-align: right; display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0;">
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 0.45rem 0.75rem; border-radius: 8px; text-align: center;">
                        <span style="display: block; font-size: 0.675rem; color: #64748B; font-weight: 600; text-transform: uppercase;">Total Masukan</span>
                        <span style="font-size: 1.05rem; font-weight: 800; color: var(--color-navy-primary);">{{ $fb['total_feedback'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Keywords Summary Bar (if available) -->
            @if(!empty($fb['top_keywords']))
            <div style="background: #FDFEFE; border-bottom: 1px dashed #E2E8F0; padding: 0.6rem 1.25rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; display: inline-flex; align-items: center; gap: 0.3rem;">
                    <i class="bi bi-tags-fill" style="color: #3B82F6;"></i> Keyword Terbanyak:
                </span>
                @foreach($fb['top_keywords'] as $kw => $kwCount)
                    <span style="font-size: 0.75rem; background: #EFF6FF; color: #1E40AF; border: 1px solid #DBEAFE; padding: 0.15rem 0.5rem; border-radius: 12px; font-weight: 600;">
                        {{ $kw }} <b style="color: #2563EB;">({{ $kwCount }}x)</b>
                    </span>
                @endforeach
            </div>
            @endif

            <!-- Table of Grouped Feedback with Total Column -->
            <div style="overflow-x: auto;">
                <table class="feedback-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">No</th>
                            <th>Poin Masukan / Uraian Responden</th>
                            <th style="min-width: 170px;">Departemen Terkait</th>
                            <th style="width: 120px; text-align: center; white-space: nowrap;">Total (Jumlah)</th>
                            <th style="min-width: 190px;">Rincian Responden</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fb['grouped_items'] as $itemIdx => $item)
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: #64748B;">
                                {{ $itemIdx + 1 }}
                            </td>
                            <td>
                                <div style="font-size: 0.875rem; font-weight: 600; color: #1E293B; line-height: 1.45;">
                                    &ldquo;{{ $item['sample_text'] }}&rdquo;
                                </div>
                                @if(!empty($item['scores']))
                                    <div style="margin-top: 0.35rem; font-size: 0.725rem; color: #64748B;">
                                        Skor Kenyataan Terkait: 
                                        @foreach($item['scores'] as $sc)
                                            <span style="background: {{ $sc <= 2 ? '#FEE2E2' : '#DCFCE7' }}; color: {{ $sc <= 2 ? '#B91C1C' : '#15803D' }}; padding: 0.1rem 0.35rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem; margin-right: 2px;">
                                                {{ $sc }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; flex-wrap: wrap; gap: 0.3rem;">
                                    @foreach($item['departments'] as $dept)
                                        <span style="background: #F1F5F9; color: #334155; border: 1px solid #E2E8F0; font-size: 0.75rem; font-weight: 600; padding: 0.15rem 0.45rem; border-radius: 4px;">
                                            {{ $dept }}
                                        </span>
                                    @endforeach
                                    @if(empty($item['departments']))
                                        <span style="color: #94A3B8; font-size: 0.75rem;">-</span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <span class="badge-count-total {{ $item['total'] > 1 ? 'high-freq' : '' }}" title="{{ $item['total'] }} responden menyampaikan poin/keyword serupa">
                                    <i class="bi {{ $item['total'] > 1 ? 'bi-fire' : 'bi-person-fill' }}"></i>
                                    {{ $item['total'] }}x
                                </span>
                            </td>
                            <td>
                                <div style="font-size: 0.775rem; color: #475569; display: flex; flex-direction: column; gap: 0.25rem;">
                                    @foreach(array_slice($item['responses'], 0, 3) as $respDetail)
                                        <div>
                                            <b style="color: #1E293B;">{{ $respDetail['name'] }}</b> 
                                            <span style="color: #64748B;">({{ $respDetail['dept'] }} &bull; {{ $respDetail['pos'] }})</span>
                                        </div>
                                    @endforeach
                                    @if(count($item['responses']) > 3)
                                        <div style="font-size: 0.725rem; color: #2563EB; font-weight: 600;">
                                            +{{ count($item['responses']) - 3 }} responden lainnya
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <div style="background: #FFFFFF; border: 1px dashed #CBD5E1; border-radius: 12px; padding: 3rem 1.5rem; text-align: center; color: #64748B;">
            <i class="bi bi-chat-left-dots" style="font-size: 2.5rem; color: #94A3B8; display: block; margin-bottom: 0.75rem;"></i>
            <h4 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">
                Belum Ada Masukan Feedback atau Uraian
            </h4>
            <p style="font-size: 0.85rem; margin: 0;">
                Responden belum mengisi uraian atau catatan alasan nilai pada edisi survei ini.
            </p>
        </div>
        @endforelse
    </div>
    @endif

</div>
@endsection

@section('scripts')
@if($tab == 'analisa')
<script>
    (function() {
        const dimLabels = {!! json_encode(array_keys($dimensionScores)) !!};
        const realScores = {!! json_encode(array_values($dimensionScores)) !!};
        const expScores = {!! json_encode(array_values($dimensionExpectationScores)) !!};

        // Radar Chart
        const radarCanvas = document.getElementById('dimensionRadarChart');
        if (radarCanvas) {
            const radarCtx = radarCanvas.getContext('2d');
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
                    animation: { duration: 300 },
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
        }

        // Bar Chart
        const barCanvas = document.getElementById('dimensionBarChart');
        if (barCanvas) {
            const barCtx = barCanvas.getContext('2d');
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
                    animation: { duration: 300 },
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
        }
    })();
</script>
@endif
@endsection
