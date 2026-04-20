@extends('layouts.app')

@section('title', $gardenProject ? 'Edytuj Projekt - Greenaxe CRM' : 'Nowy Projekt - Greenaxe CRM')
@section('page-title', $gardenProject ? 'Edytuj Projekt' : 'Nowy Projekt')

@section('content')
    <div style="max-width: 900px;">
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 40px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <form method="POST" action="{{ $gardenProject ? route('garden-projects.update', $gardenProject) : route('garden-projects.store') }}">
                @csrf
                @if($gardenProject)
                    @method('PUT')
                @endif

                <!-- Sekcja: Informacje Podstawowe -->
                <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Informacje Podstawowe</h3>

                <div class="form-group">
                    <label for="client_id">Klient *</label>
                    <select id="client_id" name="client_id" required>
                        <option value="">-- Wybierz klienta --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" @if(old('client_id', $gardenProject->client_id ?? '') == $client->id) selected @endif>{{ $client->name }}</option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">Nazwa Projektu *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $gardenProject->name ?? '') }}"
                        required
                        placeholder="np. Ogród Przydomowy - Wiosna 2026"
                    />
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sekcja: Szczegóły Projektu -->
                <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-top: 35px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Szczegóły Projektu</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="street">Ulica i Numer</label>
                        <input
                            type="text"
                            id="street"
                            name="street"
                            value="{{ old('street', $gardenProject->street ?? '') }}"
                            placeholder="np. ul. Główna 123"
                        />
                        @error('street')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="city">Miasto</label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            value="{{ old('city', $gardenProject->city ?? '') }}"
                            placeholder="np. Warszawa"
                        />
                        @error('city')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="postal_code">Kod Pocztowy</label>
                        <input
                            type="text"
                            id="postal_code"
                            name="postal_code"
                            value="{{ old('postal_code', $gardenProject->postal_code ?? '') }}"
                            placeholder="00-000"
                        />
                        @error('postal_code')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="area_m2">Powierzchnia (m²)</label>
                        <input
                            type="number"
                            id="area_m2"
                            name="area_m2"
                            value="{{ old('area_m2', $gardenProject->area_m2 ?? '') }}"
                            step="0.01"
                            placeholder="np. 250"
                        />
                        @error('area_m2')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="draft" @if(old('status', $gardenProject->status ?? 'draft') === 'draft') selected @endif>Projekt</option>
                            <option value="active" @if(old('status', $gardenProject->status ?? 'draft') === 'active') selected @endif>Aktywny</option>
                            <option value="on_hold" @if(old('status', $gardenProject->status ?? 'draft') === 'on_hold') selected @endif>Wstrzymany</option>
                            <option value="completed" @if(old('status', $gardenProject->status ?? 'draft') === 'completed') selected @endif>Ukończony</option>
                            <option value="cancelled" @if(old('status', $gardenProject->status ?? 'draft') === 'cancelled') selected @endif>Anulowany</option>
                        </select>
                        @error('status')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Sekcja: Opis -->
                <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-top: 35px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Opis i Notatki</h3>

                <div class="form-group">
                    <label for="description">Opis Projektu</label>
                    <textarea
                        id="description"
                        name="description"
                        placeholder="Szczegółowy opis projektu, wymagania klienta, specjalne uwagi..."
                    >{{ old('description', $gardenProject->description ?? '') }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Przyciski akcji -->
                <div style="display: flex; gap: 12px; margin-top: 35px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">{{ $gardenProject ? 'Aktualizuj Projekt' : 'Utwórz Projekt' }}</button>
                    <a href="{{ route('garden-projects.index') }}" class="btn btn-secondary">Anuluj</a>
                </div>
            </form>
        </div>
    </div>
@endsection
