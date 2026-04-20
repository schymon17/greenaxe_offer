@extends('layouts.app')

@section('title', 'Pulpit - Greenaxe CRM')
@section('page-title', 'Pulpit')

@section('content')
    <!-- Statystyki -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 28px; font-weight: 700; color: #16a34a;">{{ $stats['clients_count'] ?? 0 }}</div>
                    <div style="font-size: 13px; color: #6b7280; margin-top: 8px; font-weight: 500;">Razem Klientów</div>
                </div>
            </div>
            <a href="{{ route('clients.index') }}" style="display: inline-block; margin-top: 12px; color: #16a34a; text-decoration: none; font-size: 12px; font-weight: 600;">Przejdź do klientów →</a>
        </div>

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 28px; font-weight: 700; color: #16a34a;">{{ $stats['active_projects'] ?? 0 }}</div>
                    <div style="font-size: 13px; color: #6b7280; margin-top: 8px; font-weight: 500;">Aktywne Projekty</div>
                </div>
            </div>
            <a href="{{ route('garden-projects.index') }}" style="display: inline-block; margin-top: 12px; color: #16a34a; text-decoration: none; font-size: 12px; font-weight: 600;">Przejdź do projektów →</a>
        </div>

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 28px; font-weight: 700; color: #16a34a;">{{ $stats['open_offers'] ?? 0 }}</div>
                    <div style="font-size: 13px; color: #6b7280; margin-top: 8px; font-weight: 500;">Otwarte Oferty</div>
                </div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div style="font-size: 28px; font-weight: 700; color: #16a34a;">0 PLN</div>
                    <div style="font-size: 13px; color: #6b7280; margin-top: 8px; font-weight: 500;">Razem Przychodu</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sekcje Główne -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Sekcja: Ostatni Klienci -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <div style="padding: 24px; border-bottom: 1px solid #e5e7eb;">
                <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; display: flex; justify-content: space-between; align-items: center;">
                    Ostatni Klienci
                    <a href="{{ route('clients.index') }}" style="font-size: 12px; color: #16a34a; text-decoration: none; font-weight: 600;">Wszystko →</a>
                </h3>
            </div>
            <div>
                @if(isset($recent_clients) && count($recent_clients) > 0)
                    @foreach($recent_clients as $client)
                        <div style="padding: 16px 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-size: 13px; font-weight: 600; color: #1f2937;">{{ $client->name }}</div>
                                <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $client->email }}</div>
                            </div>
                            <span style="background: #dbeafe; color: #0c4a6e; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600;">{{ $client->status }}</span>
                        </div>
                    @endforeach
                @else
                    <div style="padding: 24px; text-align: center; color: #6b7280; font-size: 13px;">Brak klientów</div>
                @endif
            </div>
        </div>

        <!-- Sekcja: Szybkie Akcje -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <div style="padding: 24px; border-bottom: 1px solid #e5e7eb;">
                <h3 style="font-size: 16px; font-weight: 700; color: #1f2937;">Szybkie Akcje</h3>
            </div>
            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <a href="{{ route('clients.create') }}" style="background: #16a34a; color: white; padding: 12px; border-radius: 6px; text-decoration: none; text-align: center; font-size: 13px; font-weight: 600; transition: all 0.2s; display: block;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                        Dodaj Klienta
                    </a>
                    <a href="{{ route('garden-projects.create') }}" style="background: #16a34a; color: white; padding: 12px; border-radius: 6px; text-decoration: none; text-align: center; font-size: 13px; font-weight: 600; transition: all 0.2s; display: block;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                        Nowy Projekt
                    </a>
                    <a href="#" style="background: #16a34a; color: white; padding: 12px; border-radius: 6px; text-decoration: none; text-align: center; font-size: 13px; font-weight: 600; opacity: 0.5; cursor: not-allowed;">
                        Nowa Oferta
                    </a>
                    <a href="#" style="background: #16a34a; color: white; padding: 12px; border-radius: 6px; text-decoration: none; text-align: center; font-size: 13px; font-weight: 600; opacity: 0.5; cursor: not-allowed;">
                        Raporty
                    </a>
                </div>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                    <p style="color: #6b7280; font-size: 12px; line-height: 1.6;">
                        <strong>Greenaxe CRM</strong> to nowoczesny system zarządzania relacjami z klientami. Zarządzaj klientami, projektami ogrodów i generuj profesjonalne oferty w jednym miejscu.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
