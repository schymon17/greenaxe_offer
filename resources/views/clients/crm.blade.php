@extends('layouts.app')

@section('title', $client->name . ' - CRM - Greenaxe')
@section('page-title', $client->name)

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="font-size: 28px; color: #1f2937; font-weight: 700; margin-bottom: 5px;">{{ $client->name }}</h2>
            <p style="color: #6b7280; font-size: 14px;">{{ $client->company ?? 'Brak firmy' }} · {{ $client->email }}</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Powrót do listy</a>
            <button onclick="window.history.back()" class="btn btn-secondary">Wróć</button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- Sekcja: Informacje Podstawowe -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Informacje Podstawowe</h3>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">NAZWA</p>
                <p style="color: #1f2937; font-size: 14px; font-weight: 600;">{{ $client->name }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">EMAIL</p>
                <a href="mailto:{{ $client->email }}" style="color: #16a34a; font-size: 14px; text-decoration: none;">{{ $client->email }}</a>
            </div>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">TELEFON</p>
                <p style="color: #1f2937; font-size: 14px;">
                    @if($client->phone)
                        <a href="tel:{{ $client->phone }}" style="color: #16a34a; text-decoration: none;">{{ $client->phone }}</a>
                    @else
                        <span style="color: #d1d5db;">-</span>
                    @endif
                </p>
            </div>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">FIRMA</p>
                <p style="color: #1f2937; font-size: 14px;">{{ $client->company ?? '-' }}</p>
            </div>
        </div>

        <!-- Sekcja: Kontakt Osoby -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Osoba do Kontaktu</h3>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">IMIĘ I NAZWISKO</p>
                <p style="color: #1f2937; font-size: 14px;">{{ $client->contact_person ?? '-' }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">STANOWISKO</p>
                <p style="color: #1f2937; font-size: 14px;">{{ $client->contact_position ?? '-' }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">TELEFON</p>
                <p style="color: #1f2937; font-size: 14px;">
                    @if($client->contact_phone)
                        <a href="tel:{{ $client->contact_phone }}" style="color: #16a34a; text-decoration: none;">{{ $client->contact_phone }}</a>
                    @else
                        <span style="color: #d1d5db;">-</span>
                    @endif
                </p>
            </div>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">PREFEROWANA METODA KONTAKTU</p>
                <p style="color: #1f2937; font-size: 14px;">
                    @if($client->preferred_contact_method === 'email')
                        Email
                    @elseif($client->preferred_contact_method === 'phone')
                        Telefon
                    @elseif($client->preferred_contact_method === 'sms')
                        SMS
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Sekcja: Adres -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); margin-top: 30px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Adres</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">ULICA</p>
                <p style="color: #1f2937; font-size: 14px;">{{ $client->address ?? '-' }}</p>
            </div>

            <div>
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">MIASTO</p>
                <p style="color: #1f2937; font-size: 14px;">{{ $client->city ?? '-' }}</p>
            </div>

            <div>
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">KOD POCZTOWY</p>
                <p style="color: #1f2937; font-size: 14px;">{{ $client->postal_code ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Sekcja: CRM -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); margin-top: 30px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Informacje CRM</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 30px;">
            <div>
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">STATUS</p>
                <p style="color: #1f2937; font-size: 14px; font-weight: 600;">
                    @if($client->status === 'active')
                        <span style="background: #dbeafe; color: #0c4a6e; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Aktywny</span>
                    @elseif($client->status === 'prospect')
                        <span style="background: #fef3c7; color: #78350f; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Prospect</span>
                    @else
                        <span style="background: #f3f4f6; color: #6b7280; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Nieaktywny</span>
                    @endif
                </p>
            </div>

            <div>
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">ŹRÓDŁO</p>
                <p style="color: #1f2937; font-size: 14px;">
                    @switch($client->source)
                        @case('web')
                            Strona Internetowa
                            @break
                        @case('referral')
                            Rekomendacja
                            @break
                        @case('call')
                            Telefon
                            @break
                        @case('social')
                            Media Społeczne
                            @break
                        @case('other')
                            Inne
                            @break
                        @default
                            -
                    @endswitch
                </p>
            </div>

            <div>
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">OSTATNI KONTAKT</p>
                <p style="color: #1f2937; font-size: 14px;">
                    @if($client->last_contact_date)
                        {{ $client->last_contact_date->format('d.m.Y H:i') }}
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Sekcja: Historia Kontaktów -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); margin-top: 30px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Historia Kontaktów</h3>

        @if($client->contact_history)
            <div style="background: #f9fafb; padding: 20px; border-radius: 6px; border-left: 4px solid #16a34a;">
                <p style="color: #1f2937; font-size: 14px; white-space: pre-wrap; line-height: 1.6;">{{ $client->contact_history }}</p>
            </div>
        @else
            <p style="color: #9ca3af; font-size: 14px; italic;">Brak notatek z historii kontaktów</p>
        @endif
    </div>

    <!-- Sekcja: Notatki -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); margin-top: 30px; margin-bottom: 30px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Notatki</h3>

        @if($client->notes)
            <div style="background: #f9fafb; padding: 20px; border-radius: 6px; border-left: 4px solid #16a34a;">
                <p style="color: #1f2937; font-size: 14px; white-space: pre-wrap; line-height: 1.6;">{{ $client->notes }}</p>
            </div>
        @else
            <p style="color: #9ca3af; font-size: 14px; italic;">Brak notatek</p>
        @endif
    </div>

    <!-- Przyciski akcji -->
    <div style="display: flex; gap: 12px; margin-bottom: 30px;">
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary">Edytuj Dane Podstawowe</a>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">Powrót do listy</a>
    </div>
@endsection
