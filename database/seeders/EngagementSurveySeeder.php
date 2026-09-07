<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyPeriod;
use App\Models\SurveyDimension;
use App\Models\QuestionTemplate;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveyAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EngagementSurveySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nik' => 'ADM001',
                'name' => 'Administrator HRGA',
                'email' => 'admin@adiprima.co.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department' => 'HRGA',
                'position' => 'Manager',
                'employment_status' => 'Tetap',
                'tenure' => '11 - 15 tahun',
                'gender' => 'Laki-laki',
                'age' => 38,
                'education' => 'S1',
            ]
        );

        // 2. Create Dummy Employees for Testing Login
        $dummyEmployees = [
            [
                'nik' => 'ADP00123',
                'username' => 'andi.pratama',
                'name' => 'Andi Pratama',
                'email' => 'andi.pratama@adiprima.co.id',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'department' => 'PM1',
                'position' => 'Operator',
                'employment_status' => 'Tetap',
                'tenure' => '1 - 5 tahun',
                'gender' => 'Laki-laki',
                'age' => 28,
                'education' => 'SMA/ SMK Sederjat',
            ],
            [
                'nik' => 'ADP00124',
                'username' => 'siti.aisyah',
                'name' => 'Siti Aisyah',
                'email' => 'siti.aisyah@adiprima.co.id',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'department' => 'MRK',
                'position' => 'Staf',
                'employment_status' => 'Tetap',
                'tenure' => '1 - 5 tahun',
                'gender' => 'Perempuan',
                'age' => 26,
                'education' => 'S1',
            ],
            [
                'nik' => 'ADP00125',
                'username' => 'budi.santoso',
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@adiprima.co.id',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'department' => 'FA',
                'position' => 'Ast. Manager',
                'employment_status' => 'Tetap',
                'tenure' => '6 - 10 tahun',
                'gender' => 'Laki-laki',
                'age' => 34,
                'education' => 'S1',
            ],
            [
                'nik' => 'ADP00126',
                'username' => 'dewi.lestari',
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@adiprima.co.id',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'department' => 'HRGA',
                'position' => 'Karu',
                'employment_status' => 'Tetap',
                'tenure' => '6 - 10 tahun',
                'gender' => 'Perempuan',
                'age' => 31,
                'education' => 'S1',
            ],
            [
                'nik' => 'ADP00127',
                'username' => 'rizky.maulana',
                'name' => 'Rizky Maulana',
                'email' => 'rizky.maulana@adiprima.co.id',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'department' => 'IT',
                'position' => 'Staf',
                'employment_status' => 'Kontrak',
                'tenure' => 'kurang dari 1 tahun',
                'gender' => 'Laki-laki',
                'age' => 24,
                'education' => 'D1 - D2 - D3 - D4',
            ]
        ];

        $employeeModels = [];
        foreach ($dummyEmployees as $empData) {
            $employeeModels[] = User::updateOrCreate(['username' => $empData['username']], $empData);
        }

        // 3. Create Master Dimensions / Categories
        $dimBasicNeeds = SurveyDimension::updateOrCreate(
            ['name' => 'Basic Needs'],
            [
                'code' => 'BN',
                'description' => 'Kebutuhan dasar karyawan mencakup rasa aman, gaji, kompensasi, dan fasilitas kerja.',
                'color' => '#2563EB',
                'order' => 1,
            ]
        );

        $dimIndividual = SurveyDimension::updateOrCreate(
            ['name' => 'Individual Contribution'],
            [
                'code' => 'IC',
                'description' => 'Kontribusi individu, pengakuan, pengembangan diri, dan tanggung jawab kerja.',
                'color' => '#7C3AED',
                'order' => 2,
            ]
        );

        $dimTeamwork = SurveyDimension::updateOrCreate(
            ['name' => 'Teamwork'],
            [
                'code' => 'TW',
                'description' => 'Kerjasama tim, komunikasi, kepedulian antar rekan kerja, dan keselarasan tujuan.',
                'color' => '#059669',
                'order' => 3,
            ]
        );

        $dimGrowth = SurveyDimension::updateOrCreate(
            ['name' => 'Growth'],
            [
                'code' => 'GW',
                'description' => 'Peluang tumbuh, pengembangan karir, kepemimpinan dan supervisi atasan.',
                'color' => '#D97706',
                'order' => 4,
            ]
        );

        $dimGeneral = SurveyDimension::updateOrCreate(
            ['name' => 'General / Umum'],
            [
                'code' => 'GN',
                'description' => 'Pertanyaan umum mengenai retensi, kepuasan menyeluruh, dan saran perbaikan.',
                'color' => '#4B5563',
                'order' => 5,
            ]
        );

        $dimBudaya = SurveyDimension::updateOrCreate(
            ['name' => 'Budaya & Nilai Kerja'],
            [
                'code' => 'BK',
                'description' => 'Penerapan nilai-nilai inti dan budaya kerja perusahaan.',
                'color' => '#EC4899',
                'order' => 6,
            ]
        );

        // 4. Create Surveys
        $surveyBudaya = Survey::updateOrCreate(
            ['slug' => 'survey-budaya-kerja'],
            [
                'title' => 'Survey Budaya Kerja',
                'category' => 'Budaya Kerja',
                'description' => 'Mengukur nilai-nilai budaya kerja dan lingkungan kerja di perusahaan.',
                'icon' => 'bi-people-fill',
                'start_date' => Carbon::today()->toDateString(),
                'end_date' => Carbon::today()->addMonths(2)->toDateString(),
                'is_active' => true,
            ]
        );

        $engagementSurvey = Survey::updateOrCreate(
            ['slug' => 'engagement-survey'],
            [
                'title' => 'Employee Engagement Survey',
                'category' => 'Engagement Survey',
                'description' => 'Mengukur tingkat keterlibatan dan komitmen karyawan terhadap perusahaan.',
                'icon' => 'bi-graph-up-arrow',
                'start_date' => Carbon::today()->subDays(7)->toDateString(),
                'end_date' => Carbon::today()->addMonths(1)->toDateString(),
                'is_active' => true,
            ]
        );

        $internalSat = Survey::updateOrCreate(
            ['slug' => 'internal-satisfaction-survey'],
            [
                'title' => 'Internal Satisfaction Survey',
                'category' => 'Kepuasan Internal',
                'description' => 'Mengukur tingkat kepuasan antar unit/departemen di perusahaan.',
                'icon' => 'bi-chat-left-quote-fill',
                'start_date' => Carbon::today()->toDateString(),
                'end_date' => Carbon::today()->addMonths(2)->toDateString(),
                'is_active' => true,
            ]
        );

        $customerSat = Survey::updateOrCreate(
            ['slug' => 'customer-satisfaction-survey'],
            [
                'title' => 'Customer Satisfaction Survey',
                'category' => 'Kepuasan Pelanggan',
                'description' => 'Mengukur kepuasan pelanggan terhadap produk dan layanan perusahaan.',
                'icon' => 'bi-award-fill',
                'start_date' => Carbon::today()->toDateString(),
                'end_date' => Carbon::today()->addMonths(2)->toDateString(),
                'is_active' => true,
            ]
        );

        // Create Active Periods
        $period = SurveyPeriod::updateOrCreate(
            ['survey_id' => $engagementSurvey->id, 'period_name' => 'Tahun 2026'],
            [
                'start_date' => $engagementSurvey->start_date,
                'end_date' => $engagementSurvey->end_date,
                'is_active' => true,
            ]
        );

        SurveyPeriod::updateOrCreate(
            ['survey_id' => $surveyBudaya->id, 'period_name' => 'Periode 2026'],
            [
                'start_date' => $surveyBudaya->start_date,
                'end_date' => $surveyBudaya->end_date,
                'is_active' => true,
            ]
        );

        // 5. Question Bank (Templates) and Survey Questions Definition
        $bankQuestions = [
            // Basic Needs
            [
                'dimension_id' => $dimBasicNeeds->id,
                'dimension' => 'Basic Needs',
                'section' => 'B',
                'question_number' => 1,
                'indicator_title' => 'Individual Accountability',
                'question_text' => 'Tugas, tanggung jawab, serta target kinerja yang harus dicapai dalam pekerjaan saya telah disampaikan dengan jelas oleh perusahaan',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 1,
            ],
            [
                'dimension_id' => $dimBasicNeeds->id,
                'dimension' => 'Basic Needs',
                'section' => 'B',
                'question_number' => 2,
                'indicator_title' => 'Kebutuhan Rasa Aman (Safety Needs)',
                'question_text' => 'Peralatan kerja dan perlengkapan keselamatan (seperti APD, rambu bahaya, instalasi proteksi kebakaran, dan lain-lain) yang disediakan oleh perusahaan telah sesuai dengan kebutuhan saya dalam melaksanakan pekerjaan',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 2,
            ],
            [
                'dimension_id' => $dimBasicNeeds->id,
                'dimension' => 'Basic Needs',
                'section' => 'B',
                'question_number' => 3,
                'indicator_title' => 'Kebutuhan Fisik (Physiological Needs)',
                'question_text' => 'Saya mendapatkan hak sebagai karyawan yang meliputi waktu istirahat, izin sakit, dan cuti yang diperhitungkan dalam sistem penggajian berdasarkan ketentuan perusahaan yang berlaku',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 3,
            ],
            [
                'dimension_id' => $dimBasicNeeds->id,
                'dimension' => 'Basic Needs',
                'section' => 'B',
                'question_number' => 4,
                'indicator_title' => 'Kebutuhan Rasa Aman (Safety Needs)',
                'question_text' => 'Perusahaan memberikan jaminan kesehatan dan keselamatan melalui BPJS Kesehatan dan BPJS Ketenagakerjaan sehingga membuat saya merasa lebih aman dalam melaksanakan pekerjaan',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 4,
            ],
            [
                'dimension_id' => $dimBasicNeeds->id,
                'dimension' => 'Basic Needs',
                'section' => 'B',
                'question_number' => 5,
                'indicator_title' => 'Gaji (Salary)',
                'question_text' => 'Perusahaan memberikan nominal gaji yang sesuai dengan beban kerja dan tanggung jawab pekerjaan',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 5,
            ],
            [
                'dimension_id' => $dimBasicNeeds->id,
                'dimension' => 'Basic Needs',
                'section' => 'B',
                'question_number' => 6,
                'indicator_title' => 'Bonus (Salary)',
                'question_text' => 'Bonus yang diberikan oleh perusahaan sesuai dengan performa kinerja saya',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => 'Tetap',
                'applies_to_positions' => 'Karu ke atas',
                'order' => 6,
            ],
            [
                'dimension_id' => $dimBasicNeeds->id,
                'dimension' => 'Basic Needs',
                'section' => 'B',
                'question_number' => 7,
                'indicator_title' => 'Gaji (Salary)',
                'question_text' => 'Kenaikan gaji di perusahaan diberikan berdasarkan kinerja dan kontribusi saya terhadap perusahaan serta sesuai dengan peraturan pemerintah yang berlaku',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 7,
            ],
            [
                'dimension_id' => $dimBasicNeeds->id,
                'dimension' => 'Basic Needs',
                'section' => 'B',
                'question_number' => 8,
                'indicator_title' => 'Kebijakan Perusahaan (Policies and Administration)',
                'question_text' => 'Kebijakan yang diterapkan perusahaan selaras dengan peraturan perusahaan, prosedur, dan pedoman yang berlaku dalam perusahaan',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 8,
            ],
            [
                'dimension_id' => $dimBasicNeeds->id,
                'dimension' => 'Basic Needs',
                'section' => 'B',
                'question_number' => 9,
                'indicator_title' => 'Kondisi Kerja (Working Conditions)',
                'question_text' => 'Kondisi lingkungan kerja serta fasilitas yang disediakan oleh perusahaan mendukung saya dalam melaksanakan pekerjaan',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 9,
            ],

            // Individual Contribution
            [
                'dimension_id' => $dimIndividual->id,
                'dimension' => 'Individual Contribution',
                'section' => 'B',
                'question_number' => 10,
                'indicator_title' => 'Pekerjaan itu Sendiri (Work Itself); Kompetensi (Competence)',
                'question_text' => 'Pekerjaan yang diberikan kepada saya telah sesuai dengan kemampuan dan keahlian yang saya miliki',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 10,
            ],
            [
                'dimension_id' => $dimIndividual->id,
                'dimension' => 'Individual Contribution',
                'section' => 'B',
                'question_number' => 11,
                'indicator_title' => 'Kebutuhan Pengakuan (Esteem Needs); Pengakuan (Recognition)',
                'question_text' => 'Saya mendapatkan apresiasi atas kualitas hasil pekerjaan yang saya tunjukkan',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 11,
            ],
            [
                'dimension_id' => $dimIndividual->id,
                'dimension' => 'Individual Contribution',
                'section' => 'B',
                'question_number' => 12,
                'indicator_title' => 'Hubungan Interpersonal (Interpersonal relationship)',
                'question_text' => 'Atasan dan/atau rekan kerja menunjukkan kepedulian terhadap saya sebagai individu serta memberikan dukungan kepada saya untuk bisa menyelesaikan pekerjaan secara optimal',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 12,
            ],
            [
                'dimension_id' => $dimIndividual->id,
                'dimension' => 'Individual Contribution',
                'section' => 'B',
                'question_number' => 13,
                'indicator_title' => 'Kebutuhan Aktualisasi (Self Actualization)',
                'question_text' => 'Perusahaan memberikan dukungan kepada saya untuk terus mengembangkan diri, baik dalam bentuk training atau coaching',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 13,
            ],
            [
                'dimension_id' => $dimIndividual->id,
                'dimension' => 'Individual Contribution',
                'section' => 'B',
                'question_number' => 14,
                'indicator_title' => 'Tanggung Jawab (Responsibility); Autonomi (Autonomy)',
                'question_text' => 'Atasan memberikan kepercayaan penuh kepada saya dalam melaksanakan pekerjaan serta kewenangan dalam pengambilan keputusan sesuai dengan tanggung jawab pekerjaan yang diberikan kepada saya',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 14,
            ],
            [
                'dimension_id' => $dimIndividual->id,
                'dimension' => 'Individual Contribution',
                'section' => 'B',
                'question_number' => 15,
                'indicator_title' => 'Pencapaian (Achievement)',
                'question_text' => 'Pencapaian atas target pekerjaan yang saya lakukan mendorong saya untuk terus melakukan peningkatan kinerja',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 15,
            ],

            // Teamwork
            [
                'dimension_id' => $dimTeamwork->id,
                'dimension' => 'Teamwork',
                'section' => 'B',
                'question_number' => 16,
                'indicator_title' => 'Group Processing',
                'question_text' => 'Saya diberi kesempatan untuk menyampaikan pendapat dan setiap masukan saya dipertimbangkan oleh atasan maupun rekan kerja',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 16,
            ],
            [
                'dimension_id' => $dimTeamwork->id,
                'dimension' => 'Teamwork',
                'section' => 'B',
                'question_number' => 17,
                'indicator_title' => 'Individual Accountability',
                'question_text' => 'Pekerjaan yang saya lakukan selaras dengan misi atau tujuan perusahaan',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 17,
            ],
            [
                'dimension_id' => $dimTeamwork->id,
                'dimension' => 'Teamwork',
                'section' => 'B',
                'question_number' => 18,
                'indicator_title' => 'Positive Interdependence',
                'question_text' => 'Rekan kerja saya berkomitmen untuk menghasilkan pekerjaan yang berkualitas dan tepat waktu',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 18,
            ],
            [
                'dimension_id' => $dimTeamwork->id,
                'dimension' => 'Teamwork',
                'section' => 'B',
                'question_number' => 19,
                'indicator_title' => 'Kebutuhan Sosial; Social Skills; Keterkaitan',
                'question_text' => 'Hubungan dengan atasan dan rekan kerja di perusahaan terjalin dengan harmonis sehingga dapat mendukung kerja sama tim dan komunikasi yang efektif',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 19,
            ],
            [
                'dimension_id' => $dimTeamwork->id,
                'dimension' => 'Teamwork',
                'section' => 'B',
                'question_number' => 20,
                'indicator_title' => 'Individual Accountability',
                'question_text' => 'Setiap orang dalam tim memiliki tanggung jawab atas pencapaian target dan penyelesaian tugas dalam pekerjaan',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 20,
            ],

            // Growth
            [
                'dimension_id' => $dimGrowth->id,
                'dimension' => 'Growth',
                'section' => 'B',
                'question_number' => 21,
                'indicator_title' => 'Pengawasan (Supervision)',
                'question_text' => 'Atasan saya memiliki kapabilitas dan kompetensi yang sesuai serta dapat memberikan masukan atau arahan yang mendukung pengembangan diri dan kinerja saya',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 21,
            ],
            [
                'dimension_id' => $dimGrowth->id,
                'dimension' => 'Growth',
                'section' => 'B',
                'question_number' => 22,
                'indicator_title' => 'Pengembangan Karir (Advancement); Peluang untuk Berkembang',
                'question_text' => 'Perusahaan memberikan kesempatan yang luas dan fasilitas yang memadai kepada saya untuk belajar dan berkembang sebagai upaya untuk menunjang peningkatan kinerja dan pengembangan karir di perusahaan',
                'question_type' => 'dual_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => true,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 22,
            ],

            // General Questions (Section C)
            [
                'dimension_id' => $dimGeneral->id,
                'dimension' => 'General',
                'section' => 'C',
                'question_number' => 1,
                'indicator_title' => 'Kemungkinan Bertahan (Retention Likelihood)',
                'question_text' => 'Dari angka 1-4, seberapa besar kemungkinan Anda bertahan dan tetap bekerja di PT Adiprima Suraprinta, dengan mempertimbangkan manfaat, lingkungan kerja, dan peluang pengembangan yang tersedia?',
                'question_type' => 'single_rating',
                'rating_scale' => 4,
                'require_reason_on_low_score' => false,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 23,
            ],
            [
                'dimension_id' => $dimGeneral->id,
                'dimension' => 'General',
                'section' => 'C',
                'question_number' => 2,
                'indicator_title' => 'Alasan Kemungkinan Bertahan',
                'question_text' => 'Berikan alasan mengapa Anda menjawab dengan penilaian terkait kemungkinan Anda bertahan dan tetap bekerja di PT Adiprima Suraprinta?',
                'question_type' => 'essay',
                'rating_scale' => 4,
                'require_reason_on_low_score' => false,
                'low_score_threshold' => 2,
                'applies_to_employment_status' => null,
                'applies_to_positions' => null,
                'order' => 24,
            ],
        ];

        // Seed templates into question_templates table and survey_questions for Engagement Survey
        $insertedQuestions = [];
        foreach ($bankQuestions as $q) {
            // Create in Question Bank Template
            $template = QuestionTemplate::updateOrCreate(
                [
                    'question_text' => $q['question_text'],
                    'indicator_title' => $q['indicator_title'],
                ],
                [
                    'dimension_id' => $q['dimension_id'],
                    'question_type' => $q['question_type'],
                    'rating_scale' => $q['rating_scale'],
                    'require_reason_on_low_score' => $q['require_reason_on_low_score'],
                    'low_score_threshold' => $q['low_score_threshold'],
                    'applies_to_employment_status' => $q['applies_to_employment_status'],
                    'applies_to_positions' => $q['applies_to_positions'],
                    'order' => $q['order'],
                ]
            );

            // Create or update in Engagement Survey
            $surveyQData = $q;
            $surveyQData['survey_id'] = $engagementSurvey->id;
            $surveyQData['question_template_id'] = $template->id;

            $insertedQuestions[] = SurveyQuestion::updateOrCreate(
                [
                    'survey_id' => $engagementSurvey->id,
                    'section' => $q['section'],
                    'question_number' => $q['question_number'],
                ],
                $surveyQData
            );
        }

        // 6. Seed Sample Responses for Dashboard Testing
        $departments = [
            'PM1' => ['total' => 45, 'filled' => 36],
            'MRK' => ['total' => 30, 'filled' => 25],
            'FA' => ['total' => 20, 'filled' => 18],
            'HRGA' => ['total' => 15, 'filled' => 12],
            'IT' => ['total' => 10, 'filled' => 8],
            'PPIC' => ['total' => 25, 'filled' => 20],
            'QC' => ['total' => 20, 'filled' => 16],
            'UTILITY' => ['total' => 20, 'filled' => 15],
            'TEKNIK' => ['total' => 25, 'filled' => 22],
            'PCH' => ['total' => 15, 'filled' => 11],
        ];

        $sampleIndex = 1;
        foreach ($employeeModels as $emp) {
            $resp = SurveyResponse::updateOrCreate(
                [
                    'survey_id' => $engagementSurvey->id,
                    'survey_period_id' => $period->id,
                    'user_id' => $emp->id,
                ],
                [
                    'nik' => $emp->nik,
                    'name' => $emp->name,
                    'gender' => $emp->gender,
                    'age' => $emp->age,
                    'education' => $emp->education,
                    'employment_status' => $emp->employment_status,
                    'tenure' => $emp->tenure,
                    'department' => $emp->department,
                    'position' => $emp->position,
                    'submitted_at' => Carbon::now()->subHours($sampleIndex * 2),
                    'ip_address' => '127.0.0.1',
                ]
            );

            // Clear old answers for clean re-seed
            SurveyAnswer::where('survey_response_id', $resp->id)->delete();

            foreach ($insertedQuestions as $q) {
                if ($q->section === 'B') {
                    // Check conditional for bonus question
                    if ($q->question_number === 6 && (!$emp->isPermanent() || !$emp->isKaruOrAbove())) {
                        continue;
                    }

                    $exp = rand(3, 4);
                    $real = rand(3, 4);
                    // Occasionally add lower score to test reasons
                    if ($sampleIndex === 1 && in_array($q->question_number, [5, 7])) {
                        $real = 2;
                    }

                    $reason = null;
                    if ($real <= 2) {
                        $reason = 'Perlu penyesuaian evaluasi gaji dan kompensasi berkala agar lebih proporsional dengan beban kerja.';
                    }

                    SurveyAnswer::create([
                        'survey_response_id' => $resp->id,
                        'question_id' => $q->id,
                        'expectation_score' => $exp,
                        'reality_score' => $real,
                        'reason_text' => $reason,
                    ]);
                } elseif ($q->section === 'C') {
                    if ($q->question_number === 1) {
                        SurveyAnswer::create([
                            'survey_response_id' => $resp->id,
                            'question_id' => $q->id,
                            'reality_score' => 4,
                        ]);
                    } elseif ($q->question_number === 2) {
                        SurveyAnswer::create([
                            'survey_response_id' => $resp->id,
                            'question_id' => $q->id,
                            'text_answer' => 'Lingkungan kerja sangat kondusif, rasa kekeluargaan tinggi, dan perusahaan terus berkembang memberikan prospek karir yang baik.',
                        ]);
                    }
                }
            }
            $sampleIndex++;
        }
    }
}
