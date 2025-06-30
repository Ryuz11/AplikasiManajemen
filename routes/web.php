<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Barang;
use App\Models\GoodsReceipt;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Redirect::route('login');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->hasRole('admin')) {
        return view('admin.dashboard');
    } elseif ($user->hasRole('warehouse')) {
        // Data untuk grafik
        $barangs = Barang::select('nama', 'stok')->get();
        $grnPerMonth = GoodsReceipt::selectRaw('DATE_TRUNC(\'month\', tanggal) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
        $prPerMonth = PurchaseRequest::selectRaw('DATE_TRUNC(\'month\', created_at) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
        return view('warehouse.dashboard', compact('barangs', 'grnPerMonth', 'prPerMonth'));
    } elseif ($user->hasRole('purchase')) {
        return view('purchase.dashboard');
    } elseif ($user->hasRole('finance')) {
        return view('finance.dashboard');
    }
    return view('dashboard'); // fallback
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('users', UserController::class);
    Route::resource('barangs', BarangController::class);
    Route::resource('purchase-requests', \App\Http\Controllers\PurchaseRequestController::class);
    Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class);
    Route::resource('goods-receipts', \App\Http\Controllers\GoodsReceiptController::class);
    Route::resource('payments', \App\Http\Controllers\PaymentController::class);
    Route::resource('stock-transactions', \App\Http\Controllers\StockTransactionController::class)->only(['index']);
    Route::post('purchase-orders/{id}/update-status', [\App\Http\Controllers\PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');
    Route::post('purchase-requests/{purchaseRequest}/approve', [\App\Http\Controllers\PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve');
    Route::post('payments/{payment}/update-status', [\App\Http\Controllers\PaymentController::class, 'updateStatus'])->name('payments.update-status');
});

require __DIR__.'/auth.php';
