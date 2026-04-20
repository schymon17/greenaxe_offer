@extends('layouts.app')

@section('title', $client ? 'Edytuj Klienta - Greenaxe CRM' : 'Dodaj Klienta - Greenaxe CRM')
@section('page-title', $client ? 'Edytuj Klienta' : 'Nowy Klient')

@section('content')
    <div style="max-width: 900px;">
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 40px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <form method="POST" action="{{ $client ? route('clients.update', $client) : route('clients.store') }}">
                @csrf
                @if($client)
                    @method('PUT')
                @endif

                <!-- Sekcja: Informacje Podstawowe -->
                <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Informacje Podstawowe</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nazwisko Klienta *</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $client->name ?? '') }}"
                            required
                            placeholder="np. Jan Kowalski"
                        />
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="company">Nazwa Firmy</label>
                        <input
                            type="text"
                            id="company"
                            name="company"
                            value="{{ old('company', $client->company ?? '') }}"
                            placeholder="np. ABC Sp. z o.o."
                        />
                        @error('company')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $client->email ?? '') }}"
                            required
                            placeholder="jan@example.com"
                        />
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefon</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $client->phone ?? '') }}"
                            placeholder="+48 123 456 789"
                        />
                        @error('phone')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Sekcja: Adres -->
                <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-top: 35px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Adres</h3>

                <div class="form-group">
                    <label for="address">Ulica i Numer</label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        value="{{ old('address', $client->address ?? '') }}"
                        placeholder="np. ul. Główna 123"
                    />
                    @error('address')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="postal_code">Kod Pocztowy</label>
                        <input
                            type="text"
                            id="postal_code"
                            name="postal_code"
                            value="{{ old('postal_code', $client->postal_code ?? '') }}"
                            placeholder="00-000"
                        />
                        @error('postal_code')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="city">Miasto</label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            value="{{ old('city', $client->city ?? '') }}"
                            placeholder="Warszawa"
                        />
                        @error('city')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if($client)
                <!-- Sekcja: Kontakt -->
                <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-top: 35px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Osoba do Kontaktu</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_person">Imię i Nazwisko</label>
                        <input
                            type="text"
                            id="contact_person"
                            name="contact_person"
                            value="{{ old('contact_person', $client->contact_person ?? '') }}"
                            placeholder="np. Maria Nowak"
                        />
                        @error('contact_person')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_position">Stanowisko</label>
                        <input
                            type="text"
                            id="contact_position"
                            name="contact_position"
                            value="{{ old('contact_position', $client->contact_position ?? '') }}"
                            placeholder="np. Kierownik Projektów"
                        />
                        @error('contact_position')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_phone">Telefon Kontaktu</label>
                        <input
                            type="tel"
                            id="contact_phone"
                            name="contact_phone"
                            value="{{ old('contact_phone', $client->contact_phone ?? '') }}"
                            placeholder="+48 555 123 456"
                        />
                        @error('contact_phone')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="preferred_contact_method">Preferowana Metoda Kontaktu</label>
                        <select id="preferred_contact_method" name="preferred_contact_method">
                            <option value="email" @if(old('preferred_contact_method', $client->preferred_contact_method ?? 'email') === 'email') selected @endif>Email</option>
                            <option value="phone" @if(old('preferred_contact_method', $client->preferred_contact_method ?? 'email') === 'phone') selected @endif>Telefon</option>
                            <option value="sms" @if(old('preferred_contact_method', $client->preferred_contact_method ?? 'email') === 'sms') selected @endif>SMS</option>
                        </select>
                        @error('preferred_contact_method')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @endif

                @if($client)
                <!-- Sekcja: CRM -->
                <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-top: 35px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Informacje CRM</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="prospect" @if(old('status', $client->status ?? 'prospect') === 'prospect') selected @endif>Prospect</option>
                            <option value="active" @if(old('status', $client->status ?? 'prospect') === 'active') selected @endif>Aktywny</option>
                            <option value="inactive" @if(old('status', $client->status ?? 'prospect') === 'inactive') selected @endif>Nieaktywny</option>
                        </select>
                        @error('status')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="source">Źródło Pozyskania</label>
                        <select id="source" name="source">
                            <option value="">-- Wybierz --</option>
                            <option value="web" @if(old('source', $client->source ?? '') === 'web') selected @endif>Strona Internetowa</option>
                            <option value="referral" @if(old('source', $client->source ?? '') === 'referral') selected @endif>Rekomendacja</option>
                            <option value="call" @if(old('source', $client->source ?? '') === 'call') selected @endif>Telefon</option>
                            <option value="social" @if(old('source', $client->source ?? '') === 'social') selected @endif>Media Społeczne</option>
                            <option value="other" @if(old('source', $client->source ?? '') === 'other') selected @endif>Inne</option>
                        </select>
                        @error('source')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="last_contact_date">Ostatni Kontakt</label>
                    <input
                        type="datetime-local"
                        id="last_contact_date"
                        name="last_contact_date"
                        value="{{ old('last_contact_date', $client && $client->last_contact_date ? $client->last_contact_date->format('Y-m-d\TH:i') : '') }}"
                    />
                    @error('last_contact_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                @if($client)
                <!-- Sekcja: Notatki -->
                <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-top: 35px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Notatki</h3>

                <div class="form-group">
                    <label for="notes">Notatki Ogólne</label>
                    <textarea
                        id="notes"
                        name="notes"
                        placeholder="Dodaj notatki o kliencie..."
                    >{{ old('notes', $client->notes ?? '') }}</textarea>
                    @error('notes')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                @if($client)
                <div class="form-group">
                    <label for="contact_history">Historia Kontaktów</label>
                    <textarea
                        id="contact_history"
                        name="contact_history"
                        placeholder="Rejestr wszystkich kontaktów z klientem..."
                    >{{ old('contact_history', $client->contact_history ?? '') }}</textarea>
                    @error('contact_history')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                <!-- Przyciski akcji -->
                <div style="display: flex; gap: 12px; margin-top: 35px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">{{ $client ? 'Aktualizuj Klienta' : 'Utwórz Klienta' }}</button>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">Anuluj</a>
                </div>
            </form>
        </div>
    </div>
@endsection
