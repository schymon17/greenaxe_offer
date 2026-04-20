<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\GardenProjectController;
use App\Http\Controllers\GardenSectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $stats = [
            'clients_count' => \App\Models\Client::count(),
            'active_projects' => \App\Models\GardenProject::where('status', 'active')->count(),
            'open_offers' => 0,
        ];
        $recent_clients = \App\Models\Client::latest()->take(5)->get();
        return view('dashboard', compact('stats', 'recent_clients'));
    })->name('dashboard');

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::get('/clients/{client}/crm', [ClientController::class, 'crm'])->name('clients.crm');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    Route::get('/projects', [GardenProjectController::class, 'index'])->name('garden-projects.index');
    Route::get('/projects/create', [GardenProjectController::class, 'create'])->name('garden-projects.create');
    Route::post('/projects', [GardenProjectController::class, 'store'])->name('garden-projects.store');
    Route::get('/projects/{gardenProject}', [GardenProjectController::class, 'show'])->name('garden-projects.show');
    Route::get('/projects/{gardenProject}/edit', [GardenProjectController::class, 'edit'])->name('garden-projects.edit');
    Route::put('/projects/{gardenProject}', [GardenProjectController::class, 'update'])->name('garden-projects.update');
    Route::delete('/projects/{gardenProject}', [GardenProjectController::class, 'destroy'])->name('garden-projects.destroy');

    // Garden Sections
    Route::post('/projects/{gardenProject}/sections', [GardenSectionController::class, 'store'])->name('garden-sections.store');
    Route::get('/projects/{gardenProject}/sections/{gardenSection}', [GardenSectionController::class, 'editor'])->name('garden-sections.editor');
    Route::put('/projects/{gardenProject}/sections/{gardenSection}', [GardenSectionController::class, 'update'])->name('garden-sections.update');
    Route::post('/projects/{gardenProject}/sections/{gardenSection}/canvas', [GardenSectionController::class, 'saveCanvas'])->name('garden-sections.save-canvas');
    Route::delete('/projects/{gardenProject}/sections/{gardenSection}', [GardenSectionController::class, 'destroy'])->name('garden-sections.destroy');

    // Section Elements
    Route::post('/projects/{gardenProject}/sections/{gardenSection}/elements', [GardenSectionController::class, 'storeElement'])->name('garden-sections.elements.store');
    Route::put('/projects/{gardenProject}/sections/{gardenSection}/elements/{element}', [GardenSectionController::class, 'updateElement'])->name('garden-sections.elements.update');
    Route::delete('/projects/{gardenProject}/sections/{gardenSection}/elements/{element}', [GardenSectionController::class, 'destroyElement'])->name('garden-sections.elements.destroy');
});
