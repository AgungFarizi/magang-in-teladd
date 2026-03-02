<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Manager\DashboardController as ManagerController;
use App\Http\Controllers\ManagerDep\DashboardController as ManagerDepController;
use App\Http\Controllers\Operator\DashboardController as OperatorController;
use App\Http\Controllers\Pembimbing\DashboardController as PembimbingController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaController;
use Illuminate\Support\Facades\Route;

// ── Halaman Utama ────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        $routeMap = [
            'manager'            => 'manager.dashboard',
            'manager_departemen' => 'manager-dep.dashboard',
            'operator'           => 'operator.dashboard',
            'pembimbing_lapang'  => 'pembimbing.dashboard',
            'mahasiswa'          => 'mahasiswa.dashboard',
        ];
        $route = $routeMap[$role] ?? 'login';
        return redirect()->route($route);
    }
    return view('welcome');
})->name('home');

// ── Autentikasi ───────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    // Registrasi
    Route::get('/register', [RegisterController::class, 'showPilihan'])->name('register');
    Route::get('/register/mahasiswa', [RegisterController::class, 'showFormMahasiswa'])->name('register.mahasiswa');
    Route::post('/register/mahasiswa', [RegisterController::class, 'registerMahasiswa'])->name('register.mahasiswa.post');
    Route::get('/register/admin', [RegisterController::class, 'showFormAdmin'])->name('register.admin');
    Route::post('/register/admin', [RegisterController::class, 'registerAdmin'])->name('register.admin.post');
});

// Verifikasi Email
Route::get('/verify-email', [RegisterController::class, 'showVerificationNotice'])->name('verification.notice');
Route::get('/verify-email/{token}', [RegisterController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/resend-verification', [RegisterController::class, 'resendVerification'])->name('verification.resend');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── MANAGER ───────────────────────────────────────────────────────────────────
Route::prefix('manager')->name('manager.')->middleware(['auth', 'verified', 'role:manager'])->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');

    // Proposal
    Route::get('/proposal', [ManagerController::class, 'indexProposal'])->name('proposal.index');
    Route::get('/proposal/{proposal}', [ManagerController::class, 'showProposal'])->name('proposal.show');
    Route::post('/proposal/{proposal}/approve', [ManagerController::class, 'approveProposal'])->name('proposal.approve');
    Route::post('/proposal/{proposal}/reject', [ManagerController::class, 'rejectProposal'])->name('proposal.reject');

    // Kelola Operator
    Route::get('/operator', [ManagerController::class, 'indexOperator'])->name('operator.index');
    Route::post('/operator', [ManagerController::class, 'storeOperator'])->name('operator.store');
    Route::delete('/operator/{pengguna}', [ManagerController::class, 'destroyOperator'])->name('operator.destroy');

    // Token Admin
    Route::get('/token', [ManagerController::class, 'indexToken'])->name('token.index');
    Route::post('/token', [ManagerController::class, 'storeToken'])->name('token.store');
    Route::delete('/token/{aksesTokenAdmin}', [ManagerController::class, 'destroyToken'])->name('token.destroy');

    // Laporan
    Route::get('/laporan', [ManagerController::class, 'laporan'])->name('laporan');
});

// ── MANAGER DEPARTEMEN ────────────────────────────────────────────────────────
Route::prefix('manager-dep')->name('manager-dep.')->middleware(['auth', 'verified', 'role:manager_departemen'])->group(function () {
    Route::get('/dashboard', [ManagerDepController::class, 'dashboard'])->name('dashboard');

    // Proposal
    Route::get('/proposal', [ManagerDepController::class, 'indexProposal'])->name('proposal.index');
    Route::get('/proposal/{proposal}', [ManagerDepController::class, 'showProposal'])->name('proposal.show');
    Route::post('/proposal/{proposal}/approve', [ManagerDepController::class, 'approveProposal'])->name('proposal.approve');
    Route::post('/proposal/{proposal}/reject', [ManagerDepController::class, 'rejectProposal'])->name('proposal.reject');

    // Pembimbing
    Route::get('/pembimbing', [ManagerDepController::class, 'indexPembimbing'])->name('pembimbing.index');
    Route::post('/pembimbing', [ManagerDepController::class, 'storePembimbing'])->name('pembimbing.store');
    Route::delete('/pembimbing/{pengguna}', [ManagerDepController::class, 'destroyPembimbing'])->name('pembimbing.destroy');

    // Mahasiswa
    Route::get('/mahasiswa', [ManagerDepController::class, 'mahasiswaDivisi'])->name('mahasiswa');
});

// ── OPERATOR ──────────────────────────────────────────────────────────────────
Route::prefix('operator')->name('operator.')->middleware(['auth', 'verified', 'role:operator'])->group(function () {
    Route::get('/dashboard', [OperatorController::class, 'dashboard'])->name('dashboard');

    // Proposal
    Route::get('/proposal', [OperatorController::class, 'indexProposal'])->name('proposal.index');
    Route::get('/proposal/{proposal}', [OperatorController::class, 'showProposal'])->name('proposal.show');
    Route::post('/proposal/{proposal}/review', [OperatorController::class, 'mulaiReview'])->name('proposal.review');
    Route::post('/proposal/{proposal}/teruskan', [OperatorController::class, 'teruskProposal'])->name('proposal.teruskan');
    Route::post('/proposal/{proposal}/tolak', [OperatorController::class, 'tolakProposal'])->name('proposal.tolak');

    // Surat Balasan
    Route::get('/surat', [OperatorController::class, 'indexSurat'])->name('surat.index');
    Route::get('/surat/buat/{proposal}', [OperatorController::class, 'createSurat'])->name('surat.create');
    Route::post('/surat/{proposal}', [OperatorController::class, 'storeSurat'])->name('surat.store');

    // Periode Magang
    Route::get('/periode', [OperatorController::class, 'indexPeriode'])->name('periode.index');
    Route::get('/periode/create', [OperatorController::class, 'createPeriode'])->name('periode.create');
    Route::post('/periode', [OperatorController::class, 'storePeriode'])->name('periode.store');
    Route::get('/periode/{periodeMagang}/edit', [OperatorController::class, 'editPeriode'])->name('periode.edit');
    Route::put('/periode/{periodeMagang}', [OperatorController::class, 'updatePeriode'])->name('periode.update');
});

// ── PEMBIMBING LAPANG ─────────────────────────────────────────────────────────
Route::prefix('pembimbing')->name('pembimbing.')->middleware(['auth', 'verified', 'role:pembimbing_lapang'])->group(function () {
    Route::get('/dashboard', [PembimbingController::class, 'dashboard'])->name('dashboard');

    // Kehadiran
    Route::get('/kehadiran', [PembimbingController::class, 'indexKehadiran'])->name('kehadiran.index');
    Route::post('/kehadiran/{kehadiran}/verifikasi', [PembimbingController::class, 'verifikasiKehadiran'])->name('kehadiran.verifikasi');
    Route::post('/kehadiran/bulk-verifikasi', [PembimbingController::class, 'bulkVerifikasiKehadiran'])->name('kehadiran.bulk-verifikasi');

    // Log Harian
    Route::get('/log', [PembimbingController::class, 'indexLog'])->name('log.index');
    Route::get('/log/{logHarian}', [PembimbingController::class, 'showLog'])->name('log.show');
    Route::post('/log/{logHarian}/verifikasi', [PembimbingController::class, 'verifikasiLog'])->name('log.verifikasi');

    // Laporan
    Route::get('/laporan/{proposal}', [PembimbingController::class, 'laporanMahasiswa'])->name('laporan.mahasiswa');
});

// ── MAHASISWA ─────────────────────────────────────────────────────────────────
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware(['auth', 'verified', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');

    // Proposal
    Route::get('/proposal', [MahasiswaController::class, 'indexProposal'])->name('proposal.index');
    Route::get('/proposal/ajukan', [MahasiswaController::class, 'createProposal'])->name('proposal.create');
    Route::post('/proposal', [MahasiswaController::class, 'storeProposal'])->name('proposal.store');
    Route::get('/proposal/{proposal}', [MahasiswaController::class, 'showProposal'])->name('proposal.show');

    // Kehadiran
    Route::get('/kehadiran', [MahasiswaController::class, 'indexKehadiran'])->name('kehadiran.index');
    Route::post('/kehadiran/absen', [MahasiswaController::class, 'absen'])->name('kehadiran.absen');
    Route::post('/kehadiran/{kehadiran}/keluar', [MahasiswaController::class, 'absenKeluar'])->name('kehadiran.keluar');

    // Log Harian
    Route::get('/log', [MahasiswaController::class, 'indexLog'])->name('log.index');
    Route::get('/log/buat/{kehadiran}', [MahasiswaController::class, 'createLog'])->name('log.create');
    Route::post('/log/{kehadiran}', [MahasiswaController::class, 'storeLog'])->name('log.store');

    // Surat Balasan
    Route::get('/surat', [MahasiswaController::class, 'indexSurat'])->name('surat.index');
    Route::get('/surat/{suratBalasan}', [MahasiswaController::class, 'showSurat'])->name('surat.show');
});