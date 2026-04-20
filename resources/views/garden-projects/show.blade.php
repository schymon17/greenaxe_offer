@extends('layouts.app')

@section('title', $gardenProject->name . ' - Greenaxe')
@section('page-title', $gardenProject->name)

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="font-size: 28px; color: #1f2937; font-weight: 700; margin-bottom: 5px;">{{ $gardenProject->name }}</h2>
            <p style="color: #6b7280; font-size: 14px;">
                Klient: <a href="{{ route('clients.crm', $gardenProject->client) }}" style="color: #16a34a; text-decoration: none;">{{ $gardenProject->client->name }}</a>
            </p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('garden-projects.edit', $gardenProject) }}" class="btn btn-primary">Edytuj Projekt</a>
            <a href="{{ route('garden-projects.index') }}" class="btn btn-secondary">Powrót do listy</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- Informacje Podstawowe -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Informacje Podstawowe</h3>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">NAZWA PROJEKTU</p>
                <p style="color: #1f2937; font-size: 14px; font-weight: 600;">{{ $gardenProject->name }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">KLIENT</p>
                <a href="{{ route('clients.crm', $gardenProject->client) }}" style="color: #16a34a; font-size: 14px; text-decoration: none; font-weight: 600;">{{ $gardenProject->client->name }}</a>
                @if($gardenProject->client->company)
                    <p style="color: #6b7280; font-size: 13px; margin-top: 2px;">{{ $gardenProject->client->company }}</p>
                @endif
            </div>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">STATUS</p>
                @if($gardenProject->status === 'active')
                    <span style="background: #dcfce7; color: #15803d; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Aktywny</span>
                @elseif($gardenProject->status === 'completed')
                    <span style="background: #dbeafe; color: #0c4a6e; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Ukończony</span>
                @elseif($gardenProject->status === 'on_hold')
                    <span style="background: #fef3c7; color: #78350f; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Wstrzymany</span>
                @elseif($gardenProject->status === 'cancelled')
                    <span style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Anulowany</span>
                @else
                    <span style="background: #f3f4f6; color: #6b7280; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Projekt</span>
                @endif
            </div>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">POWIERZCHNIA</p>
                <p style="color: #1f2937; font-size: 14px;">{{ $gardenProject->area_m2 ? $gardenProject->area_m2 . ' m²' : '-' }}</p>
            </div>
        </div>

        <!-- Adres -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Lokalizacja Projektu</h3>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">ULICA I NUMER</p>
                <p style="color: #1f2937; font-size: 14px;">{{ $gardenProject->street ?? '-' }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">KOD POCZTOWY</p>
                <p style="color: #1f2937; font-size: 14px;">{{ $gardenProject->postal_code ?? '-' }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 4px;">MIASTO</p>
                <p style="color: #1f2937; font-size: 14px;">{{ $gardenProject->city ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Opis -->
    @if($gardenProject->description)
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-top: 30px; margin-bottom: 30px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Opis Projektu</h3>
        <p style="color: #1f2937; font-size: 14px; white-space: pre-wrap; line-height: 1.6;">{{ $gardenProject->description }}</p>
    </div>
    @endif

    <!-- Akcje -->
    <div style="display: flex; gap: 12px; margin-top: 30px; margin-bottom: 30px;">
        <a href="{{ route('garden-projects.edit', $gardenProject) }}" class="btn btn-primary">Edytuj Projekt</a>
        <form method="POST" action="{{ route('garden-projects.destroy', $gardenProject) }}" onsubmit="return confirm('Czy na pewno usunąć projekt?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Usuń Projekt</button>
        </form>
        <a href="{{ route('garden-projects.index') }}" class="btn btn-secondary">Powrót do listy</a>
    </div>

    <!-- Sekcje Ogrodu -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">
            <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin: 0;">🌿 Sekcje Ogrodu</h3>
            <button onclick="document.getElementById('new-section-form').classList.toggle('hidden')"
                style="background: #16a34a; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-size: 13px; font-weight: 600; cursor: pointer;">
                + Dodaj Sekcję
            </button>
        </div>

        <!-- New Section Form -->
        <div id="new-section-form" class="hidden" style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
            <form method="POST" action="{{ route('garden-sections.store', $gardenProject) }}" style="display: grid; grid-template-columns: 1fr 2fr auto; gap: 12px; align-items: end;">
                @csrf
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#6b7280;margin-bottom:4px;">NAZWA SEKCJI *</label>
                    <input type="text" name="name" placeholder="np. Strefa frontowa" required
                        style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#6b7280;margin-bottom:4px;">OPIS</label>
                    <input type="text" name="description" placeholder="Krótki opis sekcji"
                        style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;box-sizing:border-box;">
                </div>
                <button type="submit" style="background:#16a34a;color:white;border:none;border-radius:6px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">
                    Utwórz i Edytuj
                </button>
            </form>
        </div>

        @php
            $project = $gardenProject->load('sections.elements');
            $grandTotal = 0;
        @endphp

        @forelse($gardenProject->sections as $section)
            @php
                $sectionTotal = $section->elements->sum(fn($el) => $el->quantity * $el->unit_price);
                $grandTotal += $sectionTotal;
            @endphp
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 12px; background: #fafafa;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <div>
                        <h4 style="font-size: 15px; font-weight: 700; color: #1f2937; margin: 0 0 4px 0;">{{ $section->name }}</h4>
                        @if($section->description)
                            <p style="font-size: 13px; color: #6b7280; margin: 0;">{{ $section->description }}</p>
                        @endif
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 15px; font-weight: 700; color: #16a34a;">{{ number_format($sectionTotal, 2, ',', ' ') }} PLN</span>
                        <a href="{{ route('garden-sections.editor', [$gardenProject, $section]) }}"
                            style="background: #2563eb; color: white; text-decoration: none; border-radius: 6px; padding: 7px 14px; font-size: 12px; font-weight: 600;">
                            Edytuj
                        </a>
                    </div>
                </div>
                @if($section->elements->count() > 0)
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#f3f4f6;">
                                <th style="text-align:left;padding:6px 10px;color:#6b7280;font-weight:600;">Nazwa</th>
                                <th style="text-align:left;padding:6px 10px;color:#6b7280;font-weight:600;">Materiał</th>
                                <th style="text-align:right;padding:6px 10px;color:#6b7280;font-weight:600;">Ilość</th>
                                <th style="text-align:right;padding:6px 10px;color:#6b7280;font-weight:600;">Cena jedn.</th>
                                <th style="text-align:right;padding:6px 10px;color:#6b7280;font-weight:600;">Razem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($section->elements as $el)
                            <tr style="border-top:1px solid #e5e7eb;">
                                <td style="padding:8px 10px;color:#1f2937;font-weight:600;">{{ $el->name }}</td>
                                <td style="padding:8px 10px;color:#6b7280;">{{ $el->material ?? '-' }}</td>
                                <td style="padding:8px 10px;text-align:right;color:#374151;">{{ number_format($el->quantity,2,',','') }} {{ $el->unit }}</td>
                                <td style="padding:8px 10px;text-align:right;color:#374151;">{{ number_format($el->unit_price,2,',','') }} PLN</td>
                                <td style="padding:8px 10px;text-align:right;color:#16a34a;font-weight:700;">{{ number_format($el->quantity * $el->unit_price,2,',','') }} PLN</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="font-size:13px;color:#9ca3af;margin:0;">Brak elementów — kliknij Edytuj aby dodać prace i materiały.</p>
                @endif
            </div>
        @empty
            <div style="text-align:center;padding:40px;color:#9ca3af;">
                <div style="font-size:48px;margin-bottom:12px;">🌱</div>
                <p style="font-size:14px;font-weight:600;">Brak sekcji ogrodu</p>
                <p style="font-size:13px;">Kliknij „Dodaj Sekcję" aby podzielić ogród na strefy i wycenić prace.</p>
            </div>
        @endforelse

        @if($gardenProject->sections->count() > 0)
            <div style="background: #f0fdf4; border: 2px solid #16a34a; border-radius: 8px; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                <span style="font-size: 14px; color: #15803d; font-weight: 600;">Łączny kosztorys projektu:</span>
                <strong style="font-size: 22px; color: #14532d; font-weight: 800;">{{ number_format($grandTotal, 2, ',', ' ') }} PLN</strong>
            </div>
        @endif
    </div>

<style>
.hidden { display: none !important; }
</style>
@endsection
