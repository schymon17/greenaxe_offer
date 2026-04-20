@extends('layouts.app')

@section('title', 'Klienci - Greenaxe CRM')
@section('page-title', 'Zarządzanie Klientami')

@section('content')
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.2s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-header h2 {
            font-size: 22px;
            color: #1f2937;
            font-weight: 700;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            color: #6b7280;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            color: #1f2937;
        }
    </style>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="font-size: 28px; color: #1f2937; font-weight: 700; margin-bottom: 5px;">Klienci</h2>
            <p style="color: #6b7280; font-size: 14px;">{{ $clients->total() }} klientów w systemie</p>
        </div>
        <a href="{{ route('clients.create') }}" class="btn btn-primary">+ Dodaj Nowego Klienta</a>
    </div>

    <!-- Wyszukiwarka -->
    <form method="GET" action="{{ route('clients.index') }}" style="margin-bottom: 30px;">
        <div style="display: flex; gap: 12px;">
            <input
                type="text"
                name="search"
                placeholder="Szukaj po nazwie, emailu, firmie lub kontakcie..."
                value="{{ $search ?? '' }}"
                style="flex: 1; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"
            />
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">Szukaj</button>
            @if($search)
                <a href="{{ route('clients.index') }}" class="btn btn-secondary" style="padding: 12px 24px;">Wyczyść</a>
            @endif
        </div>
    </form>

    @if($clients->isEmpty())
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 60px 40px; text-align: center;">
            <p style="color: #6b7280; font-size: 16px; margin-bottom: 20px; font-weight: 500;">{{ $search ? 'Brak wyników' : 'Brak klientów' }}</p>
            <p style="color: #9ca3af; font-size: 14px; margin-bottom: 30px;">
                @if($search)
                    Zmień kryteria wyszukiwania lub <a href="{{ route('clients.create') }}" style="color: #16a34a; text-decoration: none;">dodaj nowego klienta</a>
                @else
                    Zacznij od dodania pierwszego klienta do swojej bazy CRM.
                @endif
            </p>
            @if(!$search)
                <a href="{{ route('clients.create') }}" class="btn btn-primary">Utwórz Pierwszego Klienta</a>
            @endif
        </div>
    @else
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Nazwa</th>
                        <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Email</th>
                        <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Kontakt</th>
                        <th style="padding: 16px 20px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Firma</th>
                        <th style="padding: 16px 20px; text-align: center; font-weight: 600; color: #1f2937; font-size: 13px;">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background='white'">
                            <td style="padding: 16px 20px; color: #1f2937; font-size: 13px; font-weight: 600;">
                                <span style="color: #16a34a; cursor: pointer;" onclick="openEditModal({{ $client->id }})">{{ $client->name }}</span>
                            </td>
                            <td style="padding: 16px 20px; color: #6b7280; font-size: 13px;">
                                <a href="mailto:{{ $client->email }}" style="color: #16a34a; text-decoration: none;">{{ $client->email }}</a>
                            </td>
                            <td style="padding: 16px 20px; color: #6b7280; font-size: 13px;">
                                @if($client->contact_person)
                                    {{ $client->contact_person }}
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 16px 20px; color: #6b7280; font-size: 13px;">{{ $client->company ?? '-' }}</td>
                            <td style="padding: 16px 20px; font-size: 13px; text-align: center;">
                                <button type="button" onclick="openEditModal({{ $client->id }})" class="btn btn-secondary btn-small" style="margin-right: 4px;">Edytuj</button>
                                <a href="{{ route('clients.crm', $client) }}" class="btn btn-secondary btn-small" style="margin-right: 4px;">CRM</a>
                                <form method="POST" action="{{ route('clients.destroy', $client) }}" style="display: inline-block;" onsubmit="return confirm('Czy na pewno?')">
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

        @if($clients->hasPages())
            <div style="margin-top: 30px; display: flex; justify-content: center; gap: 5px;">
                @if($clients->onFirstPage())
                    <span style="padding: 8px 12px; color: #9ca3af; font-size: 13px;">← Poprzednia</span>
                @else
                    <a href="{{ $clients->previousPageUrl() }}" style="padding: 8px 12px; color: #16a34a; text-decoration: none; font-size: 13px;">← Poprzednia</a>
                @endif

                @foreach($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
                    @if($page == $clients->currentPage())
                        <span style="padding: 8px 12px; background: #16a34a; color: white; border-radius: 4px; font-size: 13px; font-weight: 600;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding: 8px 12px; color: #6b7280; text-decoration: none; border-radius: 4px; transition: all 0.2s; font-size: 13px;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background=''">{{ $page }}</a>
                    @endif
                @endforeach

                @if($clients->hasMorePages())
                    <a href="{{ $clients->nextPageUrl() }}" style="padding: 8px 12px; color: #16a34a; text-decoration: none; font-size: 13px;">Następna →</a>
                @else
                    <span style="padding: 8px 12px; color: #9ca3af; font-size: 13px;">Następna →</span>
                @endif
            </div>
        @endif
    @endif

    <!-- Modal do edycji -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edytuj Klienta</h2>
                <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="editForm" method="POST" onsubmit="submitEditForm(event)">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label for="modal_name">Nazwisko Klienta *</label>
                        <input
                            type="text"
                            id="modal_name"
                            name="name"
                            required
                            placeholder="np. Jan Kowalski"
                        />
                        <div id="modal_name_error" class="form-error" style="display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label for="modal_company">Nazwa Firmy</label>
                        <input
                            type="text"
                            id="modal_company"
                            name="company"
                            placeholder="np. ABC Sp. z o.o."
                        />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="modal_email">Email *</label>
                        <input
                            type="email"
                            id="modal_email"
                            name="email"
                            required
                            placeholder="jan@example.com"
                        />
                        <div id="modal_email_error" class="form-error" style="display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label for="modal_phone">Telefon</label>
                        <input
                            type="tel"
                            id="modal_phone"
                            name="phone"
                            placeholder="+48 123 456 789"
                        />
                    </div>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary">Zapisz Zmiany</button>
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Anuluj</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentClientId = null;

        function openEditModal(clientId) {
            currentClientId = clientId;
            
            // Fetch client data
            fetch(`/clients/${clientId}/edit`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(client => {
                document.getElementById('modal_name').value = client.name;
                document.getElementById('modal_email').value = client.email;
                document.getElementById('modal_phone').value = client.phone || '';
                document.getElementById('modal_company').value = client.company || '';
                document.getElementById('editForm').action = `/clients/${clientId}`;
                document.getElementById('editModal').classList.add('show');
            });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
            currentClientId = null;
        }

        function submitEditForm(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('editForm'));
            
            fetch(document.getElementById('editForm').action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditModal();
                    location.reload();
                } else {
                    alert('Błąd podczas zapisywania');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Błąd podczas zapisywania');
            });
        }

        // Zamknij modal po kliknięciu na tło
        document.getElementById('editModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeEditModal();
            }
        });
    </script>
@endsection

