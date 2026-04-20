@extends('layouts.app')

@section('title', 'Projekty - Greenaxe CRM')
@section('page-title', 'Zarządzanie Projektami')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="font-size: 28px; color: #1f2937; font-weight: 700; margin-bottom: 5px;">Projekty Ogrodów</h2>
            <p style="color: #6b7280; font-size: 14px;">{{ $projects->total() }} projektów w systemie</p>
        </div>
        <a href="{{ route('garden-projects.create') }}" class="btn btn-primary">+ Nowy Projekt</a>
    </div>

    @if($projects->isEmpty())
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 60px 40px; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 20px; color: #d1d5db;">🏗️</div>
            <p style="color: #6b7280; font-size: 16px; margin-bottom: 20px; font-weight: 500;">Brak projektów</p>
            <p style="color: #9ca3af; font-size: 14px; margin-bottom: 30px;">Zacznij od stworzenia pierwszego projektu ogrodniczego.</p>
            <a href="{{ route('garden-projects.create') }}" class="btn btn-primary">Utwórz Pierwszy Projekt</a>
        </div>
    @else
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Nazwa Projektu</th>
                        <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Klient</th>
                        <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Adres</th>
                        <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Powierzchnia</th>
                        <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Status</th>
                        <th style="padding: 16px 20px; text-align: center; font-weight: 600; color: #1f2937; font-size: 13px;">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background='white'">
                            <td style="padding: 16px 20px; color: #1f2937; font-size: 13px; font-weight: 600;">
                                <a href="{{ route('garden-projects.show', $project) }}" style="color: #16a34a; text-decoration: none; cursor: pointer;">{{ $project->name }}</a>
                            </td>
                            <td style="padding: 16px 20px; color: #6b7280; font-size: 13px;">
                                <a href="{{ route('clients.edit', $project->client) }}" style="color: #16a34a; text-decoration: none;">{{ $project->client->name }}</a>
                            </td>
                            <td style="padding: 16px 20px; color: #6b7280; font-size: 13px;">
                                @if($project->street)
                                    {{ $project->street }}<br>
                                    <span style="font-size: 11px;">{{ $project->postal_code }} {{ $project->city }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 16px 20px; color: #6b7280; font-size: 13px;">{{ $project->area_m2 ? $project->area_m2 . ' m²' : '-' }}</td>
                            <td style="padding: 16px 20px; font-size: 13px;">
                                @if($project->status === 'active')
                                    <span style="background: #dcfce7; color: #15803d; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600;">Aktywny</span>
                                @elseif($project->status === 'completed')
                                    <span style="background: #dbeafe; color: #0c4a6e; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600;">Ukończony</span>
                                @elseif($project->status === 'on_hold')
                                    <span style="background: #fef3c7; color: #78350f; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600;">Wstrzymany</span>
                                @elseif($project->status === 'cancelled')
                                    <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600;">Anulowany</span>
                                @else
                                    <span style="background: #f3f4f6; color: #6b7280; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600;">Projekt</span>
                                @endif
                            </td>
                            <td style="padding: 16px 20px; font-size: 13px; text-align: center;">
                                <a href="{{ route('garden-projects.show', $project) }}" class="btn btn-secondary btn-small" style="margin-right: 4px;">Podgląd</a>
                                <a href="{{ route('garden-projects.edit', $project) }}" class="btn btn-secondary btn-small" style="margin-right: 4px;">Edytuj</a>
                                <form method="POST" action="{{ route('garden-projects.destroy', $project) }}" style="display: inline-block;" onsubmit="return confirm('Czy na pewno?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-small">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
            <div style="margin-top: 30px; display: flex; justify-content: center; gap: 5px;">
                @if($projects->onFirstPage())
                    <span style="padding: 8px 12px; color: #9ca3af; font-size: 13px;">← Poprzednia</span>
                @else
                    <a href="{{ $projects->previousPageUrl() }}" style="padding: 8px 12px; color: #16a34a; text-decoration: none; font-size: 13px;">← Poprzednia</a>
                @endif

                @foreach($projects->getUrlRange(1, $projects->lastPage()) as $page => $url)
                    @if($page == $projects->currentPage())
                        <span style="padding: 8px 12px; background: #16a34a; color: white; border-radius: 4px; font-size: 13px; font-weight: 600;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding: 8px 12px; color: #6b7280; text-decoration: none; border-radius: 4px; transition: all 0.2s; font-size: 13px;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background=''">{{ $page }}</a>
                    @endif
                @endforeach

                @if($projects->hasMorePages())
                    <a href="{{ $projects->nextPageUrl() }}" style="padding: 8px 12px; color: #16a34a; text-decoration: none; font-size: 13px;">Następna →</a>
                @else
                    <span style="padding: 8px 12px; color: #9ca3af; font-size: 13px;">Następna →</span>
                @endif
            </div>
        @endif
    @endif
@endsection
