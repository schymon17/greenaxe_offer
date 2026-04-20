@extends('layouts.editor')

@section('title', $gardenSection->name . ' — Edytor Sekcji')

@section('topbar-breadcrumb')
<div class="topbar-breadcrumb">
    <a href="{{ route('garden-projects.index') }}">Projekty</a>
    <span style="color:#4b5563;">›</span>
    <a href="{{ route('garden-projects.show', $gardenProject) }}">{{ $gardenProject->name }}</a>
    <span style="color:#4b5563;">›</span>
    <span class="current">{{ $gardenSection->name }}</span>
</div>
@endsection

@section('topbar-actions')
<button id="save-btn" onclick="saveCanvas()" class="topbar-btn topbar-btn-primary">Zapisz mape</button>
<a href="{{ route('garden-projects.show', $gardenProject) }}" class="topbar-btn topbar-btn-ghost">Wroc do projektu</a>
@endsection

@section('editor-body')

@php
$typeLabels = [
    'trawnik' => 'Trawnik',
    'rabata' => 'Rabata kwiatowa',
    'drzewa_krzewy' => 'Drzewa / Krzewy',
    'zywoplot' => 'Żywopłot',
    'kostka_brukowa' => 'Kostka brukowa',
    'nawierzchnia_zwirowa' => 'Nawierzchnia żwirowa',
    'nawierzchnia_kamienna' => 'Nawierzchnia kamienna',
    'taras' => 'Taras',
    'sciezka' => 'Ścieżka',
    'ogrodzenie' => 'Ogrodzenie / Płot',
    'brama' => 'Brama / Furtka',
    'pergola' => 'Pergola / Altana',
    'murki' => 'Murki / Obrzeża',
    'nawadnianie' => 'Nawadnianie',
    'oswietlenie' => 'Oświetlenie',
    'drenaz' => 'Drenaż',
    'robocizna' => 'Robocizna',
    'inne' => 'Inne',
];

$totalCost = $gardenSection->elements->sum(fn($el) => $el->quantity * $el->unit_price);
@endphp

<div class="canvas-panel">
    <div id="canvas-wrap">
    <div class="canvas-toolbar">
        <div class="tool-group-label">Widok</div>
        <button class="tool-btn active" id="tool-pan" onclick="setTool('pan')">Przesuwanie</button>
        <button class="tool-btn" id="tool-select" onclick="setTool('select')">Zaznacz</button>

        <div class="tool-sep"></div>
        <div class="tool-group-label">Rysowanie</div>
        <button class="tool-btn" id="tool-polygon" onclick="setTool('polygon')">Rysuj strefe</button>
        <button class="tool-btn" id="tool-fence" onclick="setTool('fence')">Ogrodzenie</button>
        <button class="tool-btn" id="tool-marker" onclick="setTool('marker')">Zaznacznik</button>
        <button class="tool-btn active" id="mode-length" onclick="toggleLengthMode()">Pytaj o dlugosc</button>
        <div style="font-size:10px;color:#94a3b8;line-height:1.35;padding:2px 4px 0;">
            Kat: <strong style="color:#e2e8f0;">90</strong> albo wzgledny <strong style="color:#e2e8f0;">+90</strong> / <strong style="color:#e2e8f0;">-45</strong>
        </div>

        <div class="tool-sep"></div>
        <div class="tool-group-label">Akcje</div>
        <button class="tool-btn" id="tool-lock-zone" onclick="toggleSelectedZoneLock()">Blokuj strefe</button>
        <button class="tool-btn" id="tool-main-zone" onclick="setSelectedAsMainZone()">Ustaw glowna</button>
        <button class="tool-btn" onclick="undoLast()">Cofnij</button>
        <button class="tool-btn" onclick="clearCanvas()">Wyczysc</button>

        <div class="tool-sep"></div>
        <div class="tool-group-label">Uklad</div>
        <button class="tool-btn active" id="layout-current" onclick="setLayoutMode('current')">Stan aktualny</button>
        <button class="tool-btn" id="layout-modified" onclick="setLayoutMode('modified')">Po modyfikacjach</button>
        <button class="tool-btn" onclick="copyCurrentToModified()">Kopiuj -> modyfikacje</button>

        <div class="tool-sep"></div>
        <div class="tool-group-label">Skala</div>
        <button class="tool-btn" id="tool-measure" onclick="toggleMeasureMode()">Skaluj odcinkiem</button>
        <div class="scale-row">
            <input id="meters-per-pixel" type="number" min="0.0001" step="0.0001" value="0.1000" onchange="setScaleFromInput()">
            <span>m/px</span>
        </div>
        <span id="layout-area-info" style="font-size:10px;color:#94a3b8;font-weight:600;padding:2px 4px;">Pow.: 0.00 m2</span>

        <div class="tool-sep"></div>
        <button class="tool-btn" id="tool-open-panel" onclick="openPanel()">Elementy / Koszty</button>

        <div class="tool-sep mobile-gps-controls" style="display:none;"></div>
        <div class="tool-group-label mobile-gps-controls" style="display:none;">Telefon GPS</div>
        <button class="tool-btn mobile-gps-controls" id="gps-walk-start" onclick="startGpsWalkMode()" style="display:none;">Start spacer GPS</button>
        <button class="tool-btn mobile-gps-controls" id="gps-walk-stop" onclick="stopGpsWalkMode()" style="display:none;">Pauza spaceru</button>
        <button class="tool-btn mobile-gps-controls" id="gps-walk-finish" onclick="finishGpsWalkMode()" style="display:none;">Zamknij strefe z GPS</button>

        <div class="tool-sep"></div>
        <div class="tool-group-label">Publiczny Link</div>
        <button class="tool-btn" id="tool-share-link" onclick="showPublicLink()">Pokaż link do rysowania</button>

        <div class="tool-sep"></div>
        <div class="tool-group-label">Kolor strefy</div>
        <div class="color-row">
            @foreach(['#16a34a','#2563eb','#dc2626','#d97706','#7c3aed','#0891b2','#be185d','#e2e8f0'] as $c)
                <div class="color-swatch {{ $c === '#16a34a' ? 'active' : '' }}" style="background:{{ $c }};" onclick="setColor('{{ $c }}', this)"></div>
            @endforeach
        </div>
    </div>

        <canvas id="garden-canvas"></canvas>

        <div style="position:absolute;right:14px;bottom:14px;display:flex;flex-direction:column;align-items:stretch;z-index:20;box-shadow:0 1px 4px rgba(0,0,0,0.35);border-radius:4px;overflow:hidden;">
            <button onclick="zoomIn()" title="Przybliz" style="width:38px;height:38px;border:none;background:#fff;color:#111827;font-size:22px;line-height:1;cursor:pointer;border-bottom:1px solid #e5e7eb;">+</button>
            <button onclick="zoomOut()" title="Oddal" style="width:38px;height:38px;border:none;background:#fff;color:#111827;font-size:22px;line-height:1;cursor:pointer;">-</button>
            <button onclick="resetZoom()" title="Reset zoom" style="height:24px;border:none;background:#f3f4f6;color:#111827;font-size:10px;font-weight:700;cursor:pointer;border-top:1px solid #e5e7eb;">RST</button>
        </div>

        <div id="zoom-level-info" style="position:absolute;right:14px;bottom:124px;z-index:20;background:rgba(15,23,42,0.88);color:#e2e8f0;font-size:11px;font-weight:600;padding:4px 8px;border-radius:4px;min-width:48px;text-align:center;">100%</div>
    </div>

    <div class="canvas-hint" id="canvas-hint">
        Kliknij aby dodac wierzcholek strefy. Tryb "Pytaj o dlugosc" ulatwia precyzyjne rysowanie.
    </div>
</div>

<div class="right-panel" id="right-panel">
    <div class="right-panel-header">
        <span class="right-panel-title" id="right-panel-title">Elementy i koszty</span>
        <button class="right-panel-close" onclick="closePanel()" title="Zamknij">&#x2715;</button>
    </div>
    <div class="right-panel-tabs">
        <button class="tab-btn active" onclick="switchTab('elements')">Elementy</button>
        <button class="tab-btn" onclick="switchTab('list')">Lista ({{ $gardenSection->elements->count() }})</button>
        <button class="tab-btn" onclick="switchTab('settings')">Sekcja</button>
    </div>

    <div class="tab-panel active" id="tab-elements">
        <form id="element-form" method="POST" action="{{ route('garden-sections.elements.store', [$gardenProject, $gardenSection]) }}">
            @csrf

            <div class="fp-row">
                <label>Typ Pracy *</label>
                <select id="element-type" name="type" required>
                    <option value="">Wybierz typ...</option>
                    <optgroup label="Zielen">
                        <option value="trawnik">Trawnik</option>
                        <option value="rabata">Rabata kwiatowa</option>
                        <option value="drzewa_krzewy">Drzewa / Krzewy</option>
                        <option value="zywoplot">Zywoplot</option>
                    </optgroup>
                    <optgroup label="Nawierzchnie">
                        <option value="kostka_brukowa">Kostka brukowa</option>
                        <option value="nawierzchnia_zwirowa">Nawierzchnia zwirowa</option>
                        <option value="nawierzchnia_kamienna">Nawierzchnia kamienna</option>
                        <option value="taras">Taras</option>
                        <option value="sciezka">Sciezka</option>
                    </optgroup>
                    <optgroup label="Konstrukcje">
                        <option value="ogrodzenie">Ogrodzenie / Plot</option>
                        <option value="brama">Brama / Furtka</option>
                        <option value="pergola">Pergola / Altana</option>
                        <option value="murki">Murki / Obrzeza</option>
                    </optgroup>
                    <optgroup label="Instalacje">
                        <option value="nawadnianie">Nawadnianie</option>
                        <option value="oswietlenie">Oswietlenie</option>
                        <option value="drenaz">Drenaz</option>
                    </optgroup>
                    <optgroup label="Inne">
                        <option value="robocizna">Robocizna</option>
                        <option value="inne">Inne</option>
                    </optgroup>
                </select>
            </div>

            <div class="fp-row">
                <label>Przypnij do strefy</label>
                <select id="zone-ref-select">
                    <option value="">Brak powiazania (recznie)</option>
                </select>
            </div>

            <div class="fp-row" style="display:flex;align-items:center;gap:8px;">
                <input id="auto-quantity-from-zone" type="checkbox" checked style="width:auto;">
                <label for="auto-quantity-from-zone" style="margin:0;font-size:12px;color:#4b5563;font-weight:600;">Automatycznie licz ilosc z geometrii strefy</label>
            </div>

            <input type="hidden" id="zone-ref-input" name="zone_ref">
            <input type="hidden" id="zone-label-input" name="zone_label">

            <div class="fp-row">
                <label>Nazwa / Opis *</label>
                <input type="text" name="name" placeholder="np. Wymiana trawnika" required>
            </div>

            <div class="fp-row">
                <label>Material</label>
                <input type="text" name="material" placeholder="np. Trawa rolowana Premium">
            </div>

            <div class="fp-grid-2">
                <div class="fp-row">
                    <label>Ilosc *</label>
                    <input id="quantity-input" type="number" name="quantity" min="0" step="0.01" placeholder="0.00" required>
                </div>
                <div class="fp-row">
                    <label>Jednostka</label>
                    <select id="unit-input" name="unit">
                        <option value="m2">m2</option>
                        <option value="mb">mb</option>
                        <option value="szt">szt</option>
                        <option value="h">h</option>
                        <option value="kpl">kpl</option>
                    </select>
                </div>
            </div>

            <div class="fp-row">
                <label>Cena jedn. (PLN) *</label>
                <input id="unit-price-input" type="number" name="unit_price" min="0" step="0.01" placeholder="0.00" required>
                <div id="live-line-total" style="margin-top:6px;font-size:12px;color:#14532d;font-weight:700;">Szacowany koszt pozycji: 0,00 PLN</div>
            </div>

            <div class="fp-row">
                <label>Notatki</label>
                <textarea name="notes" rows="2" placeholder="Dodatkowe uwagi..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Dodaj Element</button>
        </form>
    </div>

    <div class="tab-panel" id="tab-list">
        @php
            $zoneTotals = [];
            foreach ($gardenSection->elements as $el) {
                $zoneKey = $el->zone_label ?: 'Bez przypisania';
                if (!isset($zoneTotals[$zoneKey])) {
                    $zoneTotals[$zoneKey] = 0;
                }
                $zoneTotals[$zoneKey] += $el->quantity * $el->unit_price;
            }
        @endphp

        @if(count($zoneTotals) > 0)
            <div style="margin-bottom:12px;border:1px solid #d1d5db;border-radius:6px;background:#f8fafc;padding:10px;">
                <div style="font-size:12px;font-weight:700;color:#111827;margin-bottom:6px;">Koszty wg stref</div>
                @foreach($zoneTotals as $zoneName => $zoneValue)
                    <div style="display:flex;justify-content:space-between;gap:8px;font-size:12px;color:#374151;padding:3px 0;">
                        <span>{{ $zoneName }}</span>
                        <strong style="color:#14532d;">{{ number_format($zoneValue, 2, ',', '') }} PLN</strong>
                    </div>
                @endforeach
            </div>
        @endif

        @forelse($gardenSection->elements as $element)
            @php $elTotal = $element->quantity * $element->unit_price; @endphp
            <div class="el-row">
                <div class="el-name">{{ $element->name }}</div>
                <div class="el-meta">
                    {{ $typeLabels[$element->type] ?? $element->type }}
                    @if($element->material)
                        | {{ $element->material }}
                    @endif
                    @if($element->zone_label)
                        | Strefa: {{ $element->zone_label }}
                    @endif
                </div>
                <div class="el-row-footer">
                    <span class="el-qty">{{ number_format($element->quantity, 2, ',', '') }} {{ $element->unit }} x {{ number_format($element->unit_price, 2, ',', '') }} PLN</span>
                    <span class="el-total">{{ number_format($elTotal, 2, ',', '') }} PLN</span>
                </div>
                @if($element->notes)
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">{{ $element->notes }}</div>
                @endif
                <div style="margin-top:8px;">
                    <form method="POST" action="{{ route('garden-sections.elements.destroy', [$gardenProject, $gardenSection, $element]) }}" class="js-confirm-form" data-confirm-title="Usun element" data-confirm-subtitle="Ta operacja usunie wybrany element kosztowy." data-confirm-submit="Usun" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-del">Usun</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                Brak elementow. Przejdz do zakladki "Elementy" aby dodac.
            </div>
        @endforelse

        @if($gardenSection->elements->count() > 0)
            <div class="total-bar">
                <span>Suma sekcji:</span>
                <strong>{{ number_format($totalCost, 2, ',', '') }} PLN</strong>
            </div>
        @endif
    </div>

    <div class="tab-panel" id="tab-settings">
        <form method="POST" action="{{ route('garden-sections.update', [$gardenProject, $gardenSection]) }}">
            @csrf
            @method('PUT')

            <div class="fp-row">
                <label>Nazwa Sekcji</label>
                <input type="text" name="name" value="{{ $gardenSection->name }}" required>
            </div>

            <div class="fp-row">
                <label>Opis</label>
                <textarea name="description" rows="3">{{ $gardenSection->description }}</textarea>
            </div>

            <button type="submit" class="btn-submit">Zapisz Zmiany</button>
        </form>

        <div style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e7eb;">
            <form method="POST" action="{{ route('garden-sections.destroy', [$gardenProject, $gardenSection]) }}" class="js-confirm-form" data-confirm-title="Usun sekcje" data-confirm-subtitle="Ta operacja usunie cala sekcje razem z elementami i mapa." data-confirm-submit="Usun sekcje">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:none;border:1px solid #fca5a5;color:#dc2626;border-radius:6px;padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer;width:100%;font-family:inherit;">
                    Usun cala sekcje
                </button>
            </form>
        </div>
    </div>
</div>

<div id="editor-modal" style="position:fixed;inset:0;background:rgba(15,23,42,0.52);display:none;align-items:center;justify-content:center;z-index:120;padding:24px;">
    <div style="width:min(460px, 100%);background:#f8fafc;border:1px solid #d1d5db;border-radius:14px;box-shadow:0 24px 80px rgba(15,23,42,0.35);overflow:hidden;">
        <div style="padding:14px 16px;border-bottom:1px solid #e5e7eb;background:#ffffff;display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div>
                <div id="editor-modal-title" style="font-size:14px;font-weight:800;color:#0f172a;">Ustawienia odcinka</div>
                <div id="editor-modal-subtitle" style="margin-top:3px;font-size:11px;color:#64748b;">Wprowadz dane bez popupu systemowego.</div>
            </div>
            <button type="button" id="editor-modal-close" style="border:none;background:none;color:#94a3b8;font-size:20px;line-height:1;cursor:pointer;padding:0 4px;">×</button>
        </div>
        <form id="editor-modal-form" style="padding:16px;display:flex;flex-direction:column;gap:12px;">
            <div id="editor-modal-fields" style="display:flex;flex-direction:column;gap:12px;"></div>
            <div id="editor-modal-help" style="font-size:11px;line-height:1.45;color:#64748b;"></div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:4px;">
                <button type="button" id="editor-modal-cancel" style="padding:8px 14px;border:1px solid #cbd5e1;background:#ffffff;color:#334155;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">Anuluj</button>
                <button type="submit" id="editor-modal-submit" style="padding:8px 14px;border:1px solid #15803d;background:#16a34a;color:#ffffff;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">Zapisz</button>
            </div>
        </form>
    </div>
</div>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
function switchTab(name) {
    document.querySelectorAll('.tab-btn').forEach((button, idx) => {
        button.classList.toggle('active', ['elements', 'list', 'settings'][idx] === name);
    });

    document.querySelectorAll('.tab-panel').forEach((panel) => panel.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
}

(function () {
    const canvas = document.getElementById('garden-canvas');
    const ctx = canvas.getContext('2d');
    const wrap = document.getElementById('canvas-wrap');
    const hint = document.getElementById('canvas-hint');

    const scaleInput = document.getElementById('meters-per-pixel');
    const areaInfo = document.getElementById('layout-area-info');
    const zoomInfo = document.getElementById('zoom-level-info');
    const editorModal = document.getElementById('editor-modal');
    const editorModalTitle = document.getElementById('editor-modal-title');
    const editorModalSubtitle = document.getElementById('editor-modal-subtitle');
    const editorModalForm = document.getElementById('editor-modal-form');
    const editorModalFields = document.getElementById('editor-modal-fields');
    const editorModalHelp = document.getElementById('editor-modal-help');
    const editorModalSubmit = document.getElementById('editor-modal-submit');
    const editorModalCancel = document.getElementById('editor-modal-cancel');
    const editorModalClose = document.getElementById('editor-modal-close');
    const lockZoneButton = document.getElementById('tool-lock-zone');
    const mainZoneButton = document.getElementById('tool-main-zone');
    const lengthModeButton = document.getElementById('mode-length');
    const layoutModes = ['current', 'modified'];
    const elementForm = document.getElementById('element-form');
    const confirmForms = document.querySelectorAll('.js-confirm-form');
    const zoneRefSelect = document.getElementById('zone-ref-select');
    const zoneRefInput = document.getElementById('zone-ref-input');
    const zoneLabelInput = document.getElementById('zone-label-input');
    const autoQuantityCheckbox = document.getElementById('auto-quantity-from-zone');
    const quantityInput = document.getElementById('quantity-input');
    const unitInput = document.getElementById('unit-input');
    const elementTypeInput = document.getElementById('element-type');
    const unitPriceInput = document.getElementById('unit-price-input');
    const liveLineTotal = document.getElementById('live-line-total');
    const gpsWalkStartBtn = document.getElementById('gps-walk-start');
    const gpsWalkStopBtn = document.getElementById('gps-walk-stop');
    const gpsWalkFinishBtn = document.getElementById('gps-walk-finish');
    const mobileGpsControls = document.querySelectorAll('.mobile-gps-controls');
    const toolbar = document.querySelector('.canvas-toolbar');
    let allowTypeSuggestion = true;
    let isMobileUiMode = false;
    let color = '#16a34a';
    let activeLayout = 'current';
    let layouts = {
        current: [],
        modified: [],
    };
    let metersPerPixel = 0.1;
    let currentPoly = [];
    let hoverPoint = null;
    let selectedIdx = null;
    let selectedVertexIndex = null;
    let dragging = false;
    let draggingVertex = false;
    let dragOffset = { x: 0, y: 0 };
    let measureMode = false;
    let measurePoints = [];
    let currentFence = [];
    let zoom = 1;
    let panX = 0;
    let panY = 0;
    let spacePressed = false;
    let shiftPressed = false;
    let altPressed = false;
    let panning = false;
    let panStart = null;
    let promptLengthOnClick = true;
    let modalResolve = null;
    let modalEscapeHandler = null;
    let autosaveTimer = null;
    let autosaveInFlight = false;
    let autosavePending = false;
    let lastSavedCanvasHash = '';
    const sessionClientId = (window.crypto && typeof window.crypto.randomUUID === 'function')
        ? window.crypto.randomUUID()
        : ('client-' + Math.random().toString(36).slice(2, 12));
    let realtimePusher = null;
    let realtimeChannel = null;
    let gpsWatchId = null;
    let gpsWalkMode = false;
    let gpsTrackPoints = [];
    let gpsOrigin = null;
    let gpsBaseCenter = null;
    const isMobileClient = window.matchMedia('(max-width: 900px)').matches
        || /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent || '');
    const reverbConfig = {
        key: @json(env('REVERB_APP_KEY')),
        host: (function (rawHost) {
            const h = String(rawHost || '').trim();
            if (!h || h === 'localhost' || h === '127.0.0.1') {
                return window.location.hostname;
            }
            return h;
        })(@json(env('REVERB_HOST'))),
        port: (function (raw) {
            const num = Number(raw);
            return Number.isFinite(num) && num > 0 ? num : 8080;
        })(@json(env('REVERB_PORT', 8080))),
        scheme: @json(env('REVERB_SCHEME', 'http')),
        channel: @json('garden-section.'.$gardenSection->id),
    };

    const MIN_ZOOM = 0.5;
    const MAX_ZOOM = 3.5;

    const FENCE_PANEL_WIDTH_M = 2.5;
    const FENCE_SYSTEM_NAME = 'Panel ogrodzeniowy Vera 3D 173x250 cm antracytowy Wisniowski';

    const savedData = @json($gardenSection->canvas_data ?? null);
    if (savedData) {
        if (savedData.layouts && Array.isArray(savedData.layouts.current) && Array.isArray(savedData.layouts.modified)) {
            layouts = savedData.layouts;
        } else if (Array.isArray(savedData.shapes)) {
            // Backward compatibility with older canvas format.
            layouts.current = savedData.shapes;
            layouts.modified = JSON.parse(JSON.stringify(savedData.shapes));
        }

        if (savedData.active_layout && layoutModes.includes(savedData.active_layout)) {
            activeLayout = savedData.active_layout;
        }

        if (typeof savedData.meters_per_pixel === 'number' && savedData.meters_per_pixel > 0) {
            metersPerPixel = savedData.meters_per_pixel;
        }

        if (typeof savedData.zoom === 'number' && savedData.zoom > 0) {
            zoom = clampZoom(savedData.zoom);
        }

        if (typeof savedData.pan_x === 'number' && Number.isFinite(savedData.pan_x)) {
            panX = savedData.pan_x;
        }

        if (typeof savedData.pan_y === 'number' && Number.isFinite(savedData.pan_y)) {
            panY = savedData.pan_y;
        }
    }

    normalizeLayoutShapes('current');
    normalizeLayoutShapes('modified');

    scaleInput.value = metersPerPixel.toFixed(4);

    function getShapes() {
        return layouts[activeLayout];
    }

    function getCanvasSize() {
        const dpr = window.devicePixelRatio || 1;
        return {
            w: canvas.width / dpr,
            h: canvas.height / dpr,
        };
    }

    function clampZoom(value) {
        return Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, value));
    }

    function setZoomAtScreenPoint(nextZoom, screenPoint) {
        const size = getCanvasSize();
        const cx = size.w / 2;
        const cy = size.h / 2;
        const world = screenToWorld(screenPoint);

        zoom = clampZoom(nextZoom);
        panX = screenPoint.x - (world.x - cx) * zoom - cx;
        panY = screenPoint.y - (world.y - cy) * zoom - cy;
    }

    function updateZoomInfo() {
        zoomInfo.textContent = Math.round(zoom * 100) + '%';
    }

    function screenToWorld(point) {
        const size = getCanvasSize();
        const cx = size.w / 2;
        const cy = size.h / 2;
        return {
            x: (point.x - (cx + panX)) / zoom + cx,
            y: (point.y - (cy + panY)) / zoom + cy,
        };
    }

    function getRawPos(e) {
        const rect = canvas.getBoundingClientRect();
        const src = e.touches ? e.touches[0] : e;
        return {
            x: src.clientX - rect.left,
            y: src.clientY - rect.top,
        };
    }

    function getVisibleWorldBounds() {
        const size = getCanvasSize();
        const topLeft = screenToWorld({ x: 0, y: 0 });
        const bottomRight = screenToWorld({ x: size.w, y: size.h });

        return {
            left: Math.min(topLeft.x, bottomRight.x),
            right: Math.max(topLeft.x, bottomRight.x),
            top: Math.min(topLeft.y, bottomRight.y),
            bottom: Math.max(topLeft.y, bottomRight.y),
        };
    }

    function getFirstPolygonIndex(layoutName = activeLayout) {
        const shapes = layouts[layoutName] || [];
        return shapes.findIndex((shape) => shape.type === 'polygon');
    }

    function enforceFirstMainZone(layoutName = activeLayout) {
        const shapes = layouts[layoutName] || [];
        const firstPolygonIndex = getFirstPolygonIndex(layoutName);

        shapes.forEach((shape, idx) => {
            if (shape.type !== 'polygon') {
                return;
            }

            const isFirst = idx === firstPolygonIndex;
            shape.is_main = isFirst;
            if (isFirst) {
                shape.locked = true;
                shape.main_fixed = true;
            } else if (shape.main_fixed) {
                shape.main_fixed = false;
            }
        });
    }

    function getMainPolygonPoints(layoutName = activeLayout) {
        const shapes = layouts[layoutName] || [];
        const firstIdx = getFirstPolygonIndex(layoutName);
        if (firstIdx < 0) {
            return null;
        }
        return shapes[firstIdx].points || null;
    }

    function normalizeLayoutShapes(layoutName) {
        layouts[layoutName] = (layouts[layoutName] || []).map((shape, idx) => {
            const baseId = shape.id || (layoutName + '-shape-' + idx + '-' + Math.random().toString(36).slice(2, 7));

            if (shape.type === 'polygon') {
                return {
                    ...shape,
                    id: baseId,
                    locked: Boolean(shape.locked),
                    is_main: Boolean(shape.is_main),
                    main_fixed: Boolean(shape.main_fixed),
                };
            }

            if (shape.type === 'fence') {
                return {
                    ...shape,
                    id: baseId,
                };
            }

            return {
                ...shape,
                id: baseId,
            };
        });

        enforceFirstMainZone(layoutName);
    }

    function isPointInMainZone(pos, layoutName = activeLayout) {
        const mainPoints = getMainPolygonPoints(layoutName);
        if (!mainPoints || mainPoints.length < 3) {
            return false;
        }
        return pointInPolygon(pos, mainPoints);
    }

    function fenceLengthMeters(points) {
        if (!points || points.length < 2) {
            return 0;
        }

        let totalPx = 0;
        for (let i = 1; i < points.length; i++) {
            totalPx += dist(points[i - 1], points[i]);
        }
        return totalPx * metersPerPixel;
    }

    function fenceRequirements(points) {
        const lengthM = fenceLengthMeters(points);
        const panels = Math.max(1, Math.ceil(lengthM / FENCE_PANEL_WIDTH_M));
        const posts = panels + 1;
        return { lengthM, panels, posts };
    }

    function polygonZoneAreaM2(shape) {
        return polygonAreaM2(shape.points || []);
    }

    function buildZoneOptions() {
        const shapes = getShapes();
        const options = [];

        shapes.forEach((shape, idx) => {
            if (shape.type === 'polygon') {
                const area = polygonZoneAreaM2(shape);
                const labelBase = shape.is_main ? 'Strefa glowna' : (shape.label || ('Strefa ' + (idx + 1)));
                options.push({
                    ref: activeLayout + ':polygon:' + shape.id,
                    label: labelBase + ' [' + area.toFixed(2) + ' m2]',
                    quantity: area,
                    suggestedUnit: 'm2',
                });
            }

            if (shape.type === 'fence') {
                const req = fenceRequirements(shape.points || []);
                options.push({
                    ref: activeLayout + ':fence:' + shape.id,
                    label: 'Ogrodzenie [' + req.lengthM.toFixed(2) + ' mb]',
                    quantity: req.lengthM,
                    suggestedUnit: 'mb',
                });
            }
        });

        return options;
    }

    function getZoneMetrics(zoneRef) {
        if (!zoneRef) {
            return null;
        }

        const parts = zoneRef.split(':');
        if (parts.length !== 3) {
            return null;
        }

        const [layoutName, shapeType, shapeId] = parts;
        const shape = (layouts[layoutName] || []).find((item) => item.id === shapeId && item.type === shapeType);
        if (!shape) {
            return null;
        }

        if (shapeType === 'polygon') {
            const area = polygonZoneAreaM2(shape);
            const labelBase = shape.is_main ? 'Strefa glowna' : (shape.label || 'Strefa');
            return {
                label: labelBase + ' [' + area.toFixed(2) + ' m2]',
                quantity: area,
                unit: 'm2',
            };
        }

        if (shapeType === 'fence') {
            const req = fenceRequirements(shape.points || []);
            return {
                label: 'Ogrodzenie [' + req.lengthM.toFixed(2) + ' mb]',
                quantity: req.lengthM,
                unit: 'mb',
            };
        }

        return null;
    }

    function refreshZoneBindingUi() {
        if (!zoneRefSelect) {
            return;
        }

        const currentValue = zoneRefSelect.value;
        const options = buildZoneOptions();
        zoneRefSelect.innerHTML = '<option value="">Brak powiazania (recznie)</option>';

        options.forEach((opt) => {
            const el = document.createElement('option');
            el.value = opt.ref;
            el.textContent = opt.label;
            zoneRefSelect.appendChild(el);
        });

        if (options.some((opt) => opt.ref === currentValue)) {
            zoneRefSelect.value = currentValue;
        }

        applyZoneSelection();
    }

    function shouldAutoForType() {
        const type = elementTypeInput ? elementTypeInput.value : '';
        return ['trawnik', 'rabata', 'ogrodzenie', 'zywoplot', 'sciezka', 'nawierzchnia_zwirowa', 'nawierzchnia_kamienna', 'kostka_brukowa'].includes(type);
    }

    function getZoneKind(ref) {
        if (!ref) {
            return null;
        }

        const parts = ref.split(':');
        if (parts.length !== 3) {
            return null;
        }

        return parts[1];
    }

    function suggestTypeForZone(ref) {
        const zoneKind = getZoneKind(ref);
        if (zoneKind === 'fence') {
            return 'ogrodzenie';
        }
        if (zoneKind === 'polygon') {
            return 'trawnik';
        }
        return '';
    }

    function updateLiveLineTotal() {
        if (!liveLineTotal) {
            return;
        }

        const qty = Number(quantityInput && quantityInput.value ? quantityInput.value : 0);
        const price = Number(unitPriceInput && unitPriceInput.value ? unitPriceInput.value : 0);
        const total = (Number.isFinite(qty) ? qty : 0) * (Number.isFinite(price) ? price : 0);
        liveLineTotal.textContent = 'Szacowany koszt pozycji: ' + total.toFixed(2).replace('.', ',') + ' PLN';
    }

    function applyZoneSelection() {
        if (!zoneRefSelect) {
            return;
        }

        const ref = zoneRefSelect.value;
        zoneRefInput.value = ref || '';

        if (!ref) {
            zoneLabelInput.value = '';
            quantityInput.readOnly = false;
            return;
        }

        const metrics = getZoneMetrics(ref);
        if (!metrics) {
            zoneLabelInput.value = '';
            quantityInput.readOnly = false;
            return;
        }

        zoneLabelInput.value = metrics.label;

        const suggestedType = suggestTypeForZone(ref);

        if (allowTypeSuggestion && elementTypeInput && suggestedType && elementTypeInput.value !== suggestedType) {
            elementTypeInput.value = suggestedType;
        }

        const autoEnabled = autoQuantityCheckbox && autoQuantityCheckbox.checked && shouldAutoForType();

        if (autoEnabled) {
            quantityInput.value = metrics.quantity.toFixed(2);
            quantityInput.readOnly = true;
            unitInput.value = metrics.unit;
        } else {
            quantityInput.readOnly = false;
        }

        updateLiveLineTotal();
    }

    function pointToSegmentDistance(point, a, b) {
        const dx = b.x - a.x;
        const dy = b.y - a.y;
        if (dx === 0 && dy === 0) {
            return dist(point, a);
        }

        const t = Math.max(0, Math.min(1, ((point.x - a.x) * dx + (point.y - a.y) * dy) / (dx * dx + dy * dy)));
        const proj = { x: a.x + t * dx, y: a.y + t * dy };
        return dist(point, proj);
    }

    function projectPointToSegment(point, a, b) {
        const dx = b.x - a.x;
        const dy = b.y - a.y;
        if (dx === 0 && dy === 0) {
            return { x: a.x, y: a.y, t: 0 };
        }

        const t = Math.max(0, Math.min(1, ((point.x - a.x) * dx + (point.y - a.y) * dy) / (dx * dx + dy * dy)));
        return {
            x: a.x + t * dx,
            y: a.y + t * dy,
            t,
        };
    }

    function constrainAxisPoint(base, point) {
        const dx = Math.abs(point.x - base.x);
        const dy = Math.abs(point.y - base.y);

        if (dx >= dy) {
            return { x: point.x, y: base.y };
        }

        return { x: base.x, y: point.y };
    }

    function segmentMidpoint(a, b) {
        return {
            x: (a.x + b.x) / 2,
            y: (a.y + b.y) / 2,
        };
    }

    function drawLengthLabel(a, b, text, color = '#f8fafc') {
        const mid = segmentMidpoint(a, b);
        ctx.font = 'bold 11px system-ui, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        const tm = ctx.measureText(text);
        ctx.fillStyle = 'rgba(2, 6, 23, 0.85)';
        ctx.fillRect(mid.x - tm.width / 2 - 6, mid.y - 8, tm.width + 12, 16);
        ctx.fillStyle = color;
        ctx.fillText(text, mid.x, mid.y + 1);
    }

    function drawPolygonSegmentLengths(shape) {
        if (!shape.points || shape.points.length < 2) {
            return;
        }

        for (let i = 0; i < shape.points.length; i++) {
            const j = (i + 1) % shape.points.length;
            const a = shape.points[i];
            const b = shape.points[j];
            const lenM = (dist(a, b) * metersPerPixel).toFixed(2) + ' m';
            drawLengthLabel(a, b, lenM, '#f8fafc');
        }
    }

    function findVertexAt(pos) {
        const shape = getSelectedShape();
        if (!shape || !shape.points || !Array.isArray(shape.points)) {
            return null;
        }

        for (let i = shape.points.length - 1; i >= 0; i--) {
            if (dist(pos, shape.points[i]) < 10) {
                return { shapeIndex: selectedIdx, vertexIndex: i };
            }
        }

        return null;
    }

    function canEditShapePoints(shape) {
        if (!shape) {
            return false;
        }

        if (shape.type === 'polygon' && (shape.main_fixed || shape.locked)) {
            return false;
        }

        return shape.type === 'polygon' || shape.type === 'fence';
    }

    function drawVertexHandles(shape, isSelected) {
        if (!isSelected || !shape.points || !Array.isArray(shape.points)) {
            return;
        }

        shape.points.forEach((point, index) => {
            ctx.beginPath();
            ctx.arc(point.x, point.y, selectedVertexIndex === index ? 6 : 5, 0, Math.PI * 2);
            ctx.fillStyle = selectedVertexIndex === index ? '#f8fafc' : '#0f172a';
            ctx.fill();
            ctx.strokeStyle = selectedVertexIndex === index ? '#38bdf8' : '#f8fafc';
            ctx.lineWidth = 2;
            ctx.stroke();
        });
    }

    function removeSelectedVertex() {
        const shape = getSelectedShape();
        if (!shape || selectedVertexIndex === null || !canEditShapePoints(shape)) {
            return false;
        }

        const minPoints = shape.type === 'polygon' ? 3 : 2;
        if ((shape.points || []).length <= minPoints) {
            hint.textContent = shape.type === 'polygon'
                ? 'Strefa musi miec co najmniej 3 punkty.'
                : 'Ogrodzenie musi miec co najmniej 2 punkty.';
            return false;
        }

        shape.points.splice(selectedVertexIndex, 1);
        selectedVertexIndex = null;
        refreshZoneBindingUi();
        redraw();
        hint.textContent = 'Usunieto punkt.';
        return true;
    }

    function insertVertexOnSegment(shape, edgeIndex, closed, pos) {
        if (!shape || !shape.points || !canEditShapePoints(shape)) {
            return false;
        }

        const nextIndex = closed ? (edgeIndex + 1) % shape.points.length : edgeIndex + 1;
        if (nextIndex >= shape.points.length) {
            return false;
        }

        const projected = projectPointToSegment(pos, shape.points[edgeIndex], shape.points[nextIndex]);

        if (shape.type === 'polygon') {
            const mainPoints = getMainPolygonPoints(activeLayout);
            if (mainPoints && !pointInPolygon(projected, mainPoints)) {
                hint.textContent = 'Nowy punkt strefy musi pozostac wewnatrz glownej strefy.';
                return false;
            }
        }

        if (shape.type === 'fence' && !isPointInMainZone(projected, activeLayout)) {
            hint.textContent = 'Punkt ogrodzenia musi pozostac wewnatrz glownej strefy.';
            return false;
        }

        shape.points.splice(nextIndex, 0, { x: projected.x, y: projected.y });
        selectedVertexIndex = nextIndex;
        refreshZoneBindingUi();
        redraw();
        hint.textContent = 'Dodano nowy punkt na krawedzi.';
        return true;
    }

    function findEditableSegmentAt(pos) {
        const shapes = getShapes();
        for (let i = shapes.length - 1; i >= 0; i--) {
            const shape = shapes[i];
            if (shape.type === 'polygon' && shape.points && shape.points.length >= 2) {
                for (let p = 0; p < shape.points.length; p++) {
                    const n = (p + 1) % shape.points.length;
                    if (pointToSegmentDistance(pos, shape.points[p], shape.points[n]) < 10) {
                        return { shapeIndex: i, edgeIndex: p, closed: true };
                    }
                }
            }

            if (shape.type === 'fence' && shape.points && shape.points.length >= 2) {
                for (let p = 0; p < shape.points.length - 1; p++) {
                    if (pointToSegmentDistance(pos, shape.points[p], shape.points[p + 1]) < 10) {
                        return { shapeIndex: i, edgeIndex: p, closed: false };
                    }
                }
            }
        }

        return null;
    }

    function setSegmentLength(shape, edgeIndex, desiredMeters, closed) {
        const points = shape.points;
        if (!points || points.length < 2 || desiredMeters <= 0) {
            return false;
        }

        const nextIndex = closed ? (edgeIndex + 1) % points.length : edgeIndex + 1;
        if (nextIndex >= points.length) {
            return false;
        }

        const a = points[edgeIndex];
        const b = points[nextIndex];
        const currentPx = dist(a, b);
        if (currentPx <= 0) {
            return false;
        }

        if (shape.type === 'polygon' && shape.main_fixed) {
            metersPerPixel = desiredMeters / currentPx;
            scaleInput.value = metersPerPixel.toFixed(4);
            return true;
        }

        const targetPx = desiredMeters / metersPerPixel;
        const ux = (b.x - a.x) / currentPx;
        const uy = (b.y - a.y) / currentPx;
        const newPoint = {
            x: a.x + ux * targetPx,
            y: a.y + uy * targetPx,
        };

        if (shape.type === 'polygon' && !shape.main_fixed) {
            const mainPoints = getMainPolygonPoints(activeLayout);
            if (mainPoints && !pointInPolygon(newPoint, mainPoints)) {
                return false;
            }
        }

        points[nextIndex] = newPoint;
        return true;
    }

    function getSelectedShape() {
        const shapes = getShapes();
        if (selectedIdx === null || selectedIdx < 0 || selectedIdx >= shapes.length) {
            return null;
        }
        return shapes[selectedIdx];
    }

    function updateZoneButtons() {
        const selectedShape = getSelectedShape();
        const hasPolygon = selectedShape && selectedShape.type === 'polygon';

        lockZoneButton.disabled = !hasPolygon;
        mainZoneButton.disabled = !hasPolygon;

        if (!hasPolygon) {
            lockZoneButton.textContent = 'Blokuj strefe';
            mainZoneButton.textContent = 'Ustaw glowna';
            return;
        }

        lockZoneButton.textContent = selectedShape.locked ? 'Odblokuj strefe' : 'Blokuj strefe';
        mainZoneButton.textContent = selectedShape.is_main ? 'Strefa glowna' : 'Ustaw glowna';
    }

    function polygonAreaPx2(points) {
        if (!points || points.length < 3) {
            return 0;
        }

        let area = 0;
        for (let i = 0, j = points.length - 1; i < points.length; j = i++) {
            area += (points[j].x + points[i].x) * (points[j].y - points[i].y);
        }

        return Math.abs(area / 2);
    }

    function polygonAreaM2(points) {
        return polygonAreaPx2(points) * metersPerPixel * metersPerPixel;
    }

    function totalLayoutAreaM2(layoutName) {
        return (layouts[layoutName] || [])
            .filter((shape) => shape.type === 'polygon')
            .reduce((sum, shape) => sum + polygonAreaM2(shape.points), 0);
    }

    function updateAreaInfo() {
        const total = totalLayoutAreaM2(activeLayout);
        const label = activeLayout === 'current' ? 'aktualny' : 'po modyfikacjach';
        areaInfo.textContent = 'Powierzchnia (' + label + '): ' + total.toFixed(2) + ' m2';
    }

    function updateLayoutButtons() {
        document.getElementById('layout-current').classList.toggle('active', activeLayout === 'current');
        document.getElementById('layout-modified').classList.toggle('active', activeLayout === 'modified');
    }

    function resize() {
        const dpr = window.devicePixelRatio || 1;
        const rect = wrap.getBoundingClientRect();
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        canvas.style.width = rect.width + 'px';
        canvas.style.height = rect.height + 'px';
        redraw();
    }

    function getPos(e) {
        return screenToWorld(getRawPos(e));
    }

    function dist(a, b) {
        return Math.sqrt((a.x - b.x) ** 2 + (a.y - b.y) ** 2);
    }

    function drawGrid() {
        const bounds = getVisibleWorldBounds();
        const minorStep = 50;
        const majorStep = 250;

        const startX = Math.floor(bounds.left / minorStep) * minorStep;
        const endX = Math.ceil(bounds.right / minorStep) * minorStep;
        const startY = Math.floor(bounds.top / minorStep) * minorStep;
        const endY = Math.ceil(bounds.bottom / minorStep) * minorStep;

        for (let x = startX; x <= endX; x += minorStep) {
            const isMajor = Math.abs(x % majorStep) < 0.001;
            ctx.beginPath();
            ctx.moveTo(x, startY);
            ctx.lineTo(x, endY);
            ctx.strokeStyle = isMajor ? 'rgba(148,163,184,0.22)' : 'rgba(148,163,184,0.10)';
            ctx.lineWidth = isMajor ? 1.2 : 1;
            ctx.stroke();
        }

        for (let y = startY; y <= endY; y += minorStep) {
            const isMajor = Math.abs(y % majorStep) < 0.001;
            ctx.beginPath();
            ctx.moveTo(startX, y);
            ctx.lineTo(endX, y);
            ctx.strokeStyle = isMajor ? 'rgba(148,163,184,0.22)' : 'rgba(148,163,184,0.10)';
            ctx.lineWidth = isMajor ? 1.2 : 1;
            ctx.stroke();
        }
    }

    function closeEditorModal(result = null) {
        if (!editorModal || editorModal.style.display === 'none') {
            return;
        }

        editorModal.style.display = 'none';
        editorModalFields.innerHTML = '';
        editorModalHelp.textContent = '';

        if (modalEscapeHandler) {
            document.removeEventListener('keydown', modalEscapeHandler);
            modalEscapeHandler = null;
        }

        const resolver = modalResolve;
        modalResolve = null;
        if (resolver) {
            resolver(result);
        }
    }

    function openEditorModal(config) {
        return new Promise((resolve) => {
            modalResolve = resolve;
            editorModalTitle.textContent = config.title || 'Edycja';
            editorModalSubtitle.textContent = config.subtitle || '';
            editorModalHelp.textContent = config.help || '';
            editorModalSubmit.textContent = config.submitLabel || 'Zapisz';

            editorModalFields.innerHTML = (config.fields || []).map((field) => {
                const value = field.value ?? '';
                const placeholder = field.placeholder ?? '';
                const presets = Array.isArray(field.presets) && field.presets.length > 0
                    ? '<div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px;">'
                        + field.presets.map((preset) => '<button type="button" data-modal-preset-for="' + field.name + '" data-modal-preset-value="' + preset.value + '" style="padding:5px 8px;border:1px solid #cbd5e1;background:#fff;color:#334155;border-radius:999px;font-size:11px;font-weight:700;cursor:pointer;">' + preset.label + '</button>').join('')
                        + '</div>'
                    : '';
                return '<label style="display:block;">'
                    + '<span style="display:block;margin-bottom:4px;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;">' + field.label + '</span>'
                    + '<input data-modal-field="' + field.name + '" type="' + (field.type || 'text') + '" value="' + String(value).replace(/"/g, '&quot;') + '" placeholder="' + String(placeholder).replace(/"/g, '&quot;') + '" '
                    + (field.step ? 'step="' + field.step + '" ' : '')
                    + (field.min !== undefined ? 'min="' + field.min + '" ' : '')
                    + 'style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#0f172a;font-size:13px;font-family:inherit;">'
                    + presets
                    + '</label>';
            }).join('');

            editorModalFields.querySelectorAll('[data-modal-preset-for]').forEach((button) => {
                button.addEventListener('click', () => {
                    const input = editorModalFields.querySelector('[data-modal-field="' + button.dataset.modalPresetFor + '"]');
                    if (!input) {
                        return;
                    }
                    input.value = button.dataset.modalPresetValue || '';
                    input.focus();
                });
            });

            editorModal.style.display = 'flex';

            const firstField = editorModalFields.querySelector('[data-modal-field]');
            if (firstField) {
                queueMicrotask(() => {
                    firstField.focus();
                    if (typeof firstField.select === 'function') {
                        firstField.select();
                    }
                });
            }

            modalEscapeHandler = (event) => {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeEditorModal(null);
                }
            };
            document.addEventListener('keydown', modalEscapeHandler);
        });
    }

    async function confirmEditorAction(config) {
        const result = await openEditorModal({
            title: config.title || 'Potwierdz operacje',
            subtitle: config.subtitle || '',
            help: config.help || 'Tej operacji nie da sie cofnac automatycznie.',
            submitLabel: config.submitLabel || 'Potwierdz',
            fields: [],
        });

        return result !== null;
    }

    editorModalForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const values = {};
        editorModalFields.querySelectorAll('[data-modal-field]').forEach((input) => {
            values[input.dataset.modalField] = input.value;
        });
        closeEditorModal(values);
    });

    editorModalCancel.addEventListener('click', () => closeEditorModal(null));
    editorModalClose.addEventListener('click', () => closeEditorModal(null));
    editorModal.addEventListener('click', (event) => {
        if (event.target === editorModal) {
            closeEditorModal(null);
        }
    });

    async function resolvePointWithPromptLength(basePoint, targetPoint, referenceAngleDeg = null) {
        if (!promptLengthOnClick) {
            return targetPoint;
        }

        const defaultMeters = (dist(basePoint, targetPoint) * metersPerPixel).toFixed(2);
        const dx = targetPoint.x - basePoint.x;
        const dy = targetPoint.y - basePoint.y;
        const len = Math.sqrt(dx * dx + dy * dy);
        if (len < 0.0001) {
            hint.textContent = 'Kliknij w kierunku nowego odcinka, aby wyznaczyc kierunek.';
            return null;
        }

        const clickAngleDeg = (Math.atan2(dy, dx) * 180 / Math.PI + 360) % 360;
        const hasReferenceAngle = Number.isFinite(referenceAngleDeg);
        const modalValues = await openEditorModal({
            title: 'Nowy odcinek',
            subtitle: 'Ustaw dlugosc i kierunek bez popupu systemowego.',
            help: hasReferenceAngle
                ? 'Kat domyslnie liczony jest wzgledem ostatniego odcinka. Przyklady: +90, -45 lub wartosc bezwzgledna 180.'
                : 'Mozesz wpisac kat bezwzgledny, np. 90 lub zostawic puste, aby uzyc kierunku klikniecia.',
            submitLabel: 'Dodaj odcinek',
            fields: [
                { name: 'length', label: 'Dlugosc [m]', type: 'number', value: defaultMeters, min: '0.01', step: '0.01' },
                {
                    name: 'angle',
                    label: hasReferenceAngle ? 'Kat [+/-deg lub deg]' : 'Kat [deg]',
                    type: 'text',
                    value: hasReferenceAngle
                        ? signedAngleDeltaDeg(referenceAngleDeg, clickAngleDeg).toFixed(1).replace(/^([^\-])/, '+$1')
                        : clickAngleDeg.toFixed(1),
                    placeholder: hasReferenceAngle ? '+90 albo -45' : 'np. 90',
                    presets: hasReferenceAngle
                        ? [
                            { label: '+0', value: '+0' },
                            { label: '+45', value: '+45' },
                            { label: '+90', value: '+90' },
                            { label: '-45', value: '-45' },
                            { label: '-90', value: '-90' },
                            { label: '180', value: '180' },
                        ]
                        : [
                            { label: '0', value: '0' },
                            { label: '45', value: '45' },
                            { label: '90', value: '90' },
                            { label: '135', value: '135' },
                            { label: '180', value: '180' },
                        ],
                },
            ],
        });

        if (!modalValues) {
            return null;
        }

        const desiredMeters = Number(String(modalValues.length || '').replace(',', '.'));
        if (!Number.isFinite(desiredMeters) || desiredMeters <= 0) {
            hint.textContent = 'Podaj poprawna dodatnia dlugosc odcinka.';
            return null;
        }

        const desiredPx = desiredMeters / metersPerPixel;
        const baseAngleDeg = Number.isFinite(referenceAngleDeg) ? referenceAngleDeg : clickAngleDeg;
        const angleInput = String(modalValues.angle || '').trim();

        if (angleInput === '') {
            return {
                x: basePoint.x + (dx / len) * desiredPx,
                y: basePoint.y + (dy / len) * desiredPx,
            };
        }

        const normalized = angleInput.replace(',', '.');
        let angleDeg;

        if ((normalized.startsWith('+') || normalized.startsWith('-')) && normalized.length > 1) {
            const delta = Number(normalized);
            if (!Number.isFinite(delta)) {
                hint.textContent = 'Podaj poprawny kat. Przyklady: 90, +90, -45.';
                return null;
            }
            angleDeg = baseAngleDeg + delta;
        } else {
            angleDeg = Number(normalized);
        }

        if (!Number.isFinite(angleDeg)) {
            hint.textContent = 'Podaj poprawny kat w stopniach.';
            return null;
        }

        const angleRad = angleDeg * Math.PI / 180;
        return {
            x: basePoint.x + Math.cos(angleRad) * desiredPx,
            y: basePoint.y + Math.sin(angleRad) * desiredPx,
        };
    }

    function updateLengthModeButton() {
        if (!lengthModeButton) {
            return;
        }

        lengthModeButton.classList.toggle('active', promptLengthOnClick);
    }

    function normalizeAngleDeg(angle) {
        return ((angle % 360) + 360) % 360;
    }

    function signedAngleDeltaDeg(baseAngle, nextAngle) {
        let delta = normalizeAngleDeg(nextAngle) - normalizeAngleDeg(baseAngle);
        if (delta > 180) {
            delta -= 360;
        }
        if (delta < -180) {
            delta += 360;
        }
        return delta;
    }

    function snapAnglePoint(anchor, point, previousPoint = null) {
        if (!anchor || !point) {
            return point;
        }

        const length = dist(anchor, point);
        if (length <= 0.0001) {
            return point;
        }

        const currentAngle = normalizeAngleDeg(Math.atan2(point.y - anchor.y, point.x - anchor.x) * 180 / Math.PI);
        let snappedAngle = Math.round(currentAngle / 45) * 45;

        if (previousPoint) {
            const baseAngle = normalizeAngleDeg(Math.atan2(anchor.y - previousPoint.y, anchor.x - previousPoint.x) * 180 / Math.PI);
            const delta = signedAngleDeltaDeg(baseAngle, currentAngle);
            snappedAngle = baseAngle + Math.round(delta / 45) * 45;
        }

        const angleRad = snappedAngle * Math.PI / 180;
        return {
            x: anchor.x + Math.cos(angleRad) * length,
            y: anchor.y + Math.sin(angleRad) * length,
        };
    }

    function drawAnglePreview(anchor, previewPoint, previousPoint = null) {
        if (!anchor || !previewPoint) {
            return;
        }

        const currentAngle = normalizeAngleDeg(Math.atan2(previewPoint.y - anchor.y, previewPoint.x - anchor.x) * 180 / Math.PI);
        const text = previousPoint
            ? ('kat ' + signedAngleDeltaDeg(normalizeAngleDeg(Math.atan2(anchor.y - previousPoint.y, anchor.x - previousPoint.x) * 180 / Math.PI), currentAngle).toFixed(1) + ' deg')
            : ('kat ' + currentAngle.toFixed(1) + ' deg');

        ctx.font = 'bold 11px system-ui, sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        const tm = ctx.measureText(text);
        ctx.fillStyle = 'rgba(15,23,42,0.92)';
        ctx.fillRect(previewPoint.x + 10, previewPoint.y - 10, tm.width + 12, 20);
        ctx.strokeStyle = 'rgba(148,163,184,0.55)';
        ctx.lineWidth = 1;
        ctx.strokeRect(previewPoint.x + 10, previewPoint.y - 10, tm.width + 12, 20);
        ctx.fillStyle = '#e2e8f0';
        ctx.fillText(text, previewPoint.x + 16, previewPoint.y + 1);
    }

    function drawAngleCompass(anchor, previewPoint) {
        if (!anchor || !previewPoint) {
            return;
        }

        const centerX = previewPoint.x + 22;
        const centerY = previewPoint.y + 22;
        const radius = 18;
        const angleRad = Math.atan2(previewPoint.y - anchor.y, previewPoint.x - anchor.x);

        ctx.save();
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(15,23,42,0.88)';
        ctx.fill();
        ctx.strokeStyle = 'rgba(148,163,184,0.55)';
        ctx.lineWidth = 1;
        ctx.stroke();

        ctx.beginPath();
        ctx.moveTo(centerX - radius + 4, centerY);
        ctx.lineTo(centerX + radius - 4, centerY);
        ctx.moveTo(centerX, centerY - radius + 4);
        ctx.lineTo(centerX, centerY + radius - 4);
        ctx.strokeStyle = 'rgba(148,163,184,0.25)';
        ctx.stroke();

        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(centerX + Math.cos(angleRad) * (radius - 4), centerY + Math.sin(angleRad) * (radius - 4));
        ctx.strokeStyle = altPressed ? '#fbbf24' : '#38bdf8';
        ctx.lineWidth = 2;
        ctx.stroke();
        ctx.restore();
    }

    function drawSnapStatusLabel(point) {
        if (!altPressed || !point) {
            return;
        }

        const text = 'ALT SNAP 45 deg';
        ctx.font = 'bold 10px system-ui, sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        const tm = ctx.measureText(text);
        ctx.fillStyle = 'rgba(251,191,36,0.92)';
        ctx.fillRect(point.x + 10, point.y + 20, tm.width + 12, 18);
        ctx.fillStyle = '#111827';
        ctx.fillText(text, point.x + 16, point.y + 29);
    }

    function drawPolygon(shape, isSelected) {
        if (shape.points.length < 2) {
            return;
        }

        ctx.beginPath();
        ctx.moveTo(shape.points[0].x, shape.points[0].y);
        shape.points.forEach((p) => ctx.lineTo(p.x, p.y));
        ctx.closePath();

        ctx.fillStyle = shape.color + '40';
        ctx.fill();

        if (shape.is_main) {
            ctx.strokeStyle = isSelected ? '#ffffff' : '#facc15';
            ctx.lineWidth = isSelected ? 3.5 : 3;
        } else {
            ctx.strokeStyle = isSelected ? '#ffffff' : shape.color;
            ctx.lineWidth = isSelected ? 2.5 : 2;
        }

        if (shape.locked) {
            ctx.setLineDash([8, 4]);
        }
        ctx.stroke();
        ctx.setLineDash([]);
        drawVertexHandles(shape, isSelected);

        const area = polygonAreaM2(shape.points);
        const areaText = area > 0 ? area.toFixed(2) + ' m2' : '';

        if (shape.label || areaText || shape.is_main || shape.locked) {
            let titleText = shape.label ? shape.label : 'Strefa';
            if (shape.is_main) {
                titleText = 'GLOWNA: ' + titleText;
            }
            if (shape.locked) {
                titleText = titleText + ' [BLOKADA]';
            }
            const displayText = areaText ? (titleText + ' (' + areaText + ')') : titleText;
            const cx = shape.points.reduce((sum, p) => sum + p.x, 0) / shape.points.length;
            const cy = shape.points.reduce((sum, p) => sum + p.y, 0) / shape.points.length;
            ctx.font = 'bold 12px system-ui, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            const tm = ctx.measureText(displayText);
            ctx.fillStyle = 'rgba(15,23,42,0.8)';
            ctx.fillRect(cx - tm.width / 2 - 6, cy - 10, tm.width + 12, 20);
            ctx.fillStyle = shape.color;
            ctx.fillText(displayText, cx, cy);
        }

        drawPolygonSegmentLengths(shape);
    }

    function drawMarker(shape, isSelected) {
        const r = 10;
        const x = shape.x;
        const y = shape.y;

        ctx.beginPath();
        ctx.arc(x, y - r, r, 0, Math.PI * 2);
        ctx.fillStyle = isSelected ? '#ffffff' : shape.color;
        ctx.fill();

        ctx.beginPath();
        ctx.moveTo(x - 5, y - r + 2);
        ctx.lineTo(x, y + 4);
        ctx.lineTo(x + 5, y - r + 2);
        ctx.fillStyle = isSelected ? '#ffffff' : shape.color;
        ctx.fill();

        if (shape.label) {
            ctx.font = 'bold 12px system-ui, sans-serif';
            ctx.textAlign = 'left';
            ctx.textBaseline = 'middle';

            const tm = ctx.measureText(shape.label);
            ctx.fillStyle = 'rgba(15,23,42,0.9)';
            ctx.fillRect(x + 14, y - r - 8, tm.width + 10, 18);
            ctx.strokeStyle = shape.color;
            ctx.lineWidth = 1;
            ctx.strokeRect(x + 14, y - r - 8, tm.width + 10, 18);
            ctx.fillStyle = '#f1f5f9';
            ctx.fillText(shape.label, x + 19, y - r + 1);
        }
    }

    function drawFence(shape, isSelected) {
        if (!shape.points || shape.points.length < 2) {
            return;
        }

        ctx.beginPath();
        ctx.moveTo(shape.points[0].x, shape.points[0].y);
        for (let i = 1; i < shape.points.length; i++) {
            ctx.lineTo(shape.points[i].x, shape.points[i].y);
        }

        ctx.strokeStyle = isSelected ? '#ffffff' : (shape.color || '#f97316');
        ctx.lineWidth = isSelected ? 4 : 3;
        ctx.stroke();

        const req = fenceRequirements(shape.points);
        const text = 'Ogrodzenie ' + req.lengthM.toFixed(2) + ' m | panele: ' + req.panels + ' | slupki: ' + req.posts;

        const midIndex = Math.floor(shape.points.length / 2);
        const anchor = shape.points[midIndex];
        ctx.font = 'bold 12px system-ui, sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        const tm = ctx.measureText(text);
        ctx.fillStyle = 'rgba(15,23,42,0.9)';
        ctx.fillRect(anchor.x + 10, anchor.y - 10, tm.width + 12, 20);
        ctx.strokeStyle = shape.color || '#f97316';
        ctx.lineWidth = 1;
        ctx.strokeRect(anchor.x + 10, anchor.y - 10, tm.width + 12, 20);
        ctx.fillStyle = '#f8fafc';
        ctx.fillText(text, anchor.x + 16, anchor.y + 1);
        drawVertexHandles(shape, isSelected);

        for (let i = 1; i < shape.points.length; i++) {
            const a = shape.points[i - 1];
            const b = shape.points[i];
            const lenM = (dist(a, b) * metersPerPixel).toFixed(2) + ' m';
            drawLengthLabel(a, b, lenM, '#f8fafc');
        }
    }

    function drawCurrentPoly() {
        if (!currentPoly.length) {
            return;
        }

        if (hoverPoint) {
            const bounds = getVisibleWorldBounds();
            const lastPoint = currentPoly[currentPoly.length - 1];

            // Crosshair guide on cursor point for easier alignment.
            ctx.beginPath();
            ctx.moveTo(bounds.left, hoverPoint.y);
            ctx.lineTo(bounds.right, hoverPoint.y);
            ctx.moveTo(hoverPoint.x, bounds.top);
            ctx.lineTo(hoverPoint.x, bounds.bottom);
            ctx.strokeStyle = shiftPressed ? 'rgba(56,189,248,0.65)' : 'rgba(148,163,184,0.40)';
            ctx.lineWidth = shiftPressed ? 1.4 : 1;
            ctx.setLineDash([4, 6]);
            ctx.stroke();

            // Reference guides from the last polygon vertex.
            ctx.beginPath();
            ctx.moveTo(bounds.left, lastPoint.y);
            ctx.lineTo(bounds.right, lastPoint.y);
            ctx.moveTo(lastPoint.x, bounds.top);
            ctx.lineTo(lastPoint.x, bounds.bottom);
            ctx.strokeStyle = 'rgba(34,197,94,0.35)';
            ctx.lineWidth = 1;
            ctx.setLineDash([2, 8]);
            ctx.stroke();
            ctx.setLineDash([]);

            // Preview length label for current segment.
            const segLen = (dist(lastPoint, hoverPoint) * metersPerPixel).toFixed(2) + ' m';
            drawLengthLabel(lastPoint, hoverPoint, segLen, '#93c5fd');
            drawAnglePreview(lastPoint, hoverPoint, currentPoly.length >= 2 ? currentPoly[currentPoly.length - 2] : null);
            drawAngleCompass(lastPoint, hoverPoint);
            drawSnapStatusLabel(hoverPoint);
        }

        ctx.beginPath();
        ctx.moveTo(currentPoly[0].x, currentPoly[0].y);
        currentPoly.forEach((p) => ctx.lineTo(p.x, p.y));
        if (hoverPoint) {
            ctx.lineTo(hoverPoint.x, hoverPoint.y);
        }

        ctx.strokeStyle = color;
        ctx.lineWidth = 2;
        ctx.setLineDash([6, 4]);
        ctx.stroke();
        ctx.setLineDash([]);
    }

    function drawCurrentFence() {
        if (!currentFence.length) {
            return;
        }

        ctx.beginPath();
        ctx.moveTo(currentFence[0].x, currentFence[0].y);
        currentFence.forEach((p) => ctx.lineTo(p.x, p.y));
        if (hoverPoint) {
            ctx.lineTo(hoverPoint.x, hoverPoint.y);
        }
        ctx.strokeStyle = '#f97316';
        ctx.lineWidth = 3;
        ctx.setLineDash([6, 4]);
        ctx.stroke();
        ctx.setLineDash([]);

        if (hoverPoint && currentFence.length > 0) {
            const lastPoint = currentFence[currentFence.length - 1];
            const segLen = (dist(lastPoint, hoverPoint) * metersPerPixel).toFixed(2) + ' m';
            drawLengthLabel(lastPoint, hoverPoint, segLen, '#f8fafc');
            drawAnglePreview(lastPoint, hoverPoint, currentFence.length >= 2 ? currentFence[currentFence.length - 2] : null);
            drawAngleCompass(lastPoint, hoverPoint);
            drawSnapStatusLabel(hoverPoint);
        }
    }

    function redraw() {
        const dpr = window.devicePixelRatio || 1;
        const size = getCanvasSize();
        const w = size.w;
        const h = size.h;
        const cx = w / 2;
        const cy = h / 2;

        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, w, h);

        ctx.fillStyle = '#0d1117';
        ctx.fillRect(0, 0, w, h);

        ctx.save();
    ctx.translate(cx + panX, cy + panY);
        ctx.scale(zoom, zoom);
        ctx.translate(-cx, -cy);

        drawGrid();

        const shapes = getShapes();
        shapes.forEach((shape, idx) => {
            if (shape.type === 'polygon') {
                drawPolygon(shape, idx === selectedIdx);
            } else if (shape.type === 'fence') {
                drawFence(shape, idx === selectedIdx);
            } else {
                drawMarker(shape, idx === selectedIdx);
            }
        });

        if (measurePoints.length === 1 && hoverPoint) {
            ctx.beginPath();
            ctx.moveTo(measurePoints[0].x, measurePoints[0].y);
            ctx.lineTo(hoverPoint.x, hoverPoint.y);
            ctx.strokeStyle = '#fbbf24';
            ctx.lineWidth = 2;
            ctx.setLineDash([4, 4]);
            ctx.stroke();
            ctx.setLineDash([]);
        }

        drawCurrentPoly();
        drawCurrentFence();
        drawGpsWalkTrack();
        ctx.restore();

        updateAreaInfo();
        updateZoneButtons();
        updateZoomInfo();
        scheduleAutoSave();
    }

    function pointInPolygon(point, polygon) {
        let inside = false;

        for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
            const xi = polygon[i].x;
            const yi = polygon[i].y;
            const xj = polygon[j].x;
            const yj = polygon[j].y;

            const intersect = ((yi > point.y) !== (yj > point.y))
                && (point.x < (xj - xi) * (point.y - yi) / (yj - yi) + xi);

            if (intersect) {
                inside = !inside;
            }
        }

        return inside;
    }

    function findShapeAt(pos) {
        const shapes = getShapes();
        for (let i = shapes.length - 1; i >= 0; i--) {
            const shape = shapes[i];
            if (shape.type === 'fence' && shape.points && shape.points.length >= 2) {
                for (let p = 1; p < shape.points.length; p++) {
                    if (pointToSegmentDistance(pos, shape.points[p - 1], shape.points[p]) < 8) {
                        return i;
                    }
                }
            }
            if (shape.type === 'marker' && dist(pos, { x: shape.x, y: shape.y - 10 }) < 16) {
                return i;
            }
            if (shape.type === 'polygon' && pointInPolygon(pos, shape.points)) {
                return i;
            }
        }

        return -1;
    }

    async function closePolygon() {
        if (currentPoly.length < 3) {
            currentPoly = [];
            redraw();
            return;
        }

        const modalValues = await openEditorModal({
            title: 'Nowa strefa',
            subtitle: 'Mozesz od razu nazwac strefe.',
            help: 'Pole jest opcjonalne. Nazwe mozna pozniej zmienic w kosztach i przypisaniach.',
            submitLabel: 'Zapisz strefe',
            fields: [
                { name: 'label', label: 'Nazwa strefy', type: 'text', value: '', placeholder: 'np. Trawnik frontowy' },
            ],
        });

        if (!modalValues) {
            return;
        }

        const label = String(modalValues.label || '').trim();
        getShapes().push({
            type: 'polygon',
            id: activeLayout + '-polygon-' + Date.now() + '-' + Math.random().toString(36).slice(2, 7),
            color,
            points: [...currentPoly],
            label,
            locked: false,
            is_main: false,
            main_fixed: false,
        });

        currentPoly = [];
        enforceFirstMainZone(activeLayout);
        refreshZoneBindingUi();
        redraw();
    }

    function closeFence() {
        if (currentFence.length < 2) {
            currentFence = [];
            redraw();
            return;
        }

        const req = fenceRequirements(currentFence);

        getShapes().push({
            type: 'fence',
            id: activeLayout + '-fence-' + Date.now() + '-' + Math.random().toString(36).slice(2, 7),
            color: '#f97316',
            system: FENCE_SYSTEM_NAME,
            points: [...currentFence],
            panels: req.panels,
            posts: req.posts,
            length_m: Number(req.lengthM.toFixed(2)),
        });
        currentFence = [];
        hint.textContent = 'Ogrodzenie: ' + req.lengthM.toFixed(2) + ' m, panele: ' + req.panels + ', slupki: ' + req.posts + '.';
        refreshZoneBindingUi();
        redraw();
    }

    canvas.addEventListener('click', async (e) => {
        if (dragging) {
            return;
        }

        const pos = getPos(e);

        if (measureMode) {
            measurePoints.push(pos);
            if (measurePoints.length === 2) {
                const pxDistance = dist(measurePoints[0], measurePoints[1]);
                const modalValues = await openEditorModal({
                    title: 'Kalibracja skali',
                    subtitle: 'Podaj rzeczywista dlugosc zaznaczonego odcinka.',
                    help: 'Ta wartosc ustali przelicznik m/px dla calej sekcji.',
                    submitLabel: 'Ustaw skale',
                    fields: [
                        { name: 'distance', label: 'Dlugosc [m]', type: 'number', value: '10', min: '0.01', step: '0.01' },
                    ],
                });
                const realDistance = modalValues ? Number(String(modalValues.distance || '').replace(',', '.')) : 0;
                if (pxDistance > 0 && realDistance > 0) {
                    metersPerPixel = realDistance / pxDistance;
                    scaleInput.value = metersPerPixel.toFixed(4);
                }
                measureMode = false;
                measurePoints = [];
                document.getElementById('tool-measure').classList.remove('active');
                hint.textContent = 'Skala ustawiona. Rysuj strefy i sprawdzaj powierzchnie.';
                redraw();
            }
            return;
        }

        if (tool === 'polygon') {
            let drawPos = pos;
            if (shiftPressed && currentPoly.length > 0) {
                drawPos = constrainAxisPoint(currentPoly[currentPoly.length - 1], pos);
            }

            if (!currentPoly.length) {
                if (getFirstPolygonIndex(activeLayout) >= 0 && !isPointInMainZone(drawPos, activeLayout)) {
                    hint.textContent = 'Nowe elementy strefy musza byc wewnatrz glownej strefy.';
                    return;
                }
                currentPoly.push(drawPos);
                return;
            }

            if (currentPoly.length >= 2 && dist(drawPos, currentPoly[0]) < 14) {
                await closePolygon();
                return;
            }

            let referenceAngle = null;
            if (currentPoly.length >= 2) {
                const prev = currentPoly[currentPoly.length - 2];
                const last = currentPoly[currentPoly.length - 1];
                referenceAngle = (Math.atan2(last.y - prev.y, last.x - prev.x) * 180 / Math.PI + 360) % 360;
            }

            const precisePoint = await resolvePointWithPromptLength(currentPoly[currentPoly.length - 1], drawPos, referenceAngle);
            if (!precisePoint) {
                return;
            }

            if (getFirstPolygonIndex(activeLayout) >= 0 && !isPointInMainZone(precisePoint, activeLayout)) {
                hint.textContent = 'Nowe elementy strefy musza byc wewnatrz glownej strefy.';
                return;
            }

            currentPoly.push(precisePoint);
            redraw();
            return;
        }

        if (tool === 'fence') {
            const hasMainZone = getFirstPolygonIndex(activeLayout) >= 0;
            if (!hasMainZone) {
                hint.textContent = 'Najpierw narysuj strefe glowna. Ogrodzenie liczymy wewnatrz strefy.';
                return;
            }

            let fencePoint = pos;
            if (currentFence.length > 0) {
                let referenceAngle = null;
                if (currentFence.length >= 2) {
                    const prev = currentFence[currentFence.length - 2];
                    const last = currentFence[currentFence.length - 1];
                    referenceAngle = (Math.atan2(last.y - prev.y, last.x - prev.x) * 180 / Math.PI + 360) % 360;
                }

                const preciseFencePoint = await resolvePointWithPromptLength(currentFence[currentFence.length - 1], pos, referenceAngle);
                if (!preciseFencePoint) {
                    return;
                }
                fencePoint = preciseFencePoint;
            }

            if (!isPointInMainZone(fencePoint, activeLayout)) {
                hint.textContent = 'Punkt ogrodzenia jest poza strefa glowna. Zmien kierunek lub dlugosc.';
                return;
            }

            currentFence.push(fencePoint);
            redraw();
            return;
        }

        if (tool === 'marker') {
            if (!isPointInMainZone(pos, activeLayout)) {
                hint.textContent = 'Elementy nie moga wychodzic poza glowna strefe.';
                return;
            }
            const modalValues = await openEditorModal({
                title: 'Nowy zaznacznik',
                subtitle: 'Dodaj opis punktu na mapie.',
                help: 'To pole jest opcjonalne.',
                submitLabel: 'Dodaj zaznacznik',
                fields: [
                    { name: 'label', label: 'Opis', type: 'text', value: '', placeholder: 'np. Lampa ogrodowa' },
                ],
            });
            if (!modalValues) {
                return;
            }
            const label = String(modalValues.label || '').trim();
            getShapes().push({ type: 'marker', color, x: pos.x, y: pos.y, label });
            redraw();
            return;
        }

    });

    canvas.addEventListener('dblclick', async (e) => {
        if (tool === 'polygon' && currentPoly.length >= 3) {
            await closePolygon();
            return;
        }

        if (tool === 'fence' && currentFence.length >= 2) {
            closeFence();
            return;
        }

        const pos = getPos(e);

        // In select tool: try segment length edit first, then fall through to shape select
        if (tool === 'select') {
            const vertex = findVertexAt(pos);
            if (vertex) {
                selectedVertexIndex = vertex.vertexIndex;
                removeSelectedVertex();
                return;
            }

            const seg = findEditableSegmentAt(pos);
            if (seg) {
                const shape = getShapes()[seg.shapeIndex];
                selectedIdx = seg.shapeIndex;
                if (!e.altKey) {
                    insertVertexOnSegment(shape, seg.edgeIndex, seg.closed, pos);
                    return;
                }
                const points = shape.points || [];
                const nextIndex = seg.closed ? (seg.edgeIndex + 1) % points.length : seg.edgeIndex + 1;
                if (points[seg.edgeIndex] && points[nextIndex]) {
                    const currentLen = dist(points[seg.edgeIndex], points[nextIndex]) * metersPerPixel;
                    const modalValues = await openEditorModal({
                        title: 'Dlugosc odcinka',
                        subtitle: 'Zmien dlugosc wybranego odcinka.',
                        help: 'W przypadku glownej strefy zmiana przeliczy skale calego rysunku.',
                        submitLabel: 'Zapisz dlugosc',
                        fields: [
                            { name: 'length', label: 'Dlugosc [m]', type: 'number', value: currentLen.toFixed(2), min: '0.01', step: '0.01' },
                        ],
                    });
                    const desired = modalValues ? Number(String(modalValues.length || '').replace(',', '.')) : 0;
                    if (Number.isFinite(desired) && desired > 0) {
                        const ok = setSegmentLength(shape, seg.edgeIndex, desired, seg.closed);
                        if (!ok) {
                            hint.textContent = 'Nie mozna ustawic takiej dlugosci (poza strefa glowna).';
                        } else if (shape.main_fixed) {
                            hint.textContent = 'Zmieniono realny wymiar krawedzi glownej strefy. Skala zostala przeliczona.';
                        } else {
                            hint.textContent = 'Zmieniono dlugosc odcinka.';
                        }
                        redraw();
                    }
                    return;
                }
            }
        }

        // Double-click on any non-drawing tool: select shape + open panel
        if (tool === 'pan' || tool === 'select') {
            const idx = findShapeAt(pos);
            if (idx >= 0) {
                selectedIdx = idx;
                selectedVertexIndex = null;
                redraw();
                const shape = getShapes()[idx];
                const labelMap = { polygon: 'Strefa', fence: 'Ogrodzenie', marker: 'Zaznacznik' };
                const shapeName = shape.label || (labelMap[shape.type] || 'Element');
                openPanel('Strefa: ' + shapeName);
                // Pre-select zone in the element form
                const zoneRef = (activeLayout + ':' + shape.type + ':' + shape.id);
                if (zoneRefSelect) {
                    zoneRefSelect.value = zoneRef;
                    allowTypeSuggestion = true;
                    applyZoneSelection();
                }
            }
        }
    });

    canvas.addEventListener('mousemove', (e) => {
        const raw = getRawPos(e);
        if (panning) {
            panX += raw.x - panStart.x;
            panY += raw.y - panStart.y;
            panStart = raw;
            redraw();
            return;
        }

        const pos = getPos(e);

        if (measureMode) {
            hoverPoint = pos;
            redraw();
            return;
        }

        if (tool === 'polygon' && currentPoly.length > 0) {
            let nextHoverPoint = shiftPressed
                ? constrainAxisPoint(currentPoly[currentPoly.length - 1], pos)
                : pos;

            if (altPressed && !shiftPressed) {
                nextHoverPoint = snapAnglePoint(
                    currentPoly[currentPoly.length - 1],
                    nextHoverPoint,
                    currentPoly.length >= 2 ? currentPoly[currentPoly.length - 2] : null
                );
            }

            hoverPoint = nextHoverPoint;
            redraw();
        }

        if (tool === 'fence' && currentFence.length > 0) {
            hoverPoint = altPressed
                ? snapAnglePoint(
                    currentFence[currentFence.length - 1],
                    pos,
                    currentFence.length >= 2 ? currentFence[currentFence.length - 2] : null
                )
                : pos;
            redraw();
        }

        if (tool === 'select' && dragging && selectedIdx >= 0) {
            const shapes = getShapes();
            const shape = shapes[selectedIdx];

            if (draggingVertex && selectedVertexIndex !== null && shape.points && shape.points[selectedVertexIndex]) {
                const nextPoint = { x: pos.x, y: pos.y };

                if (shape.type === 'polygon') {
                    const mainPoints = getMainPolygonPoints(activeLayout);
                    const nextPoints = shape.points.map((point, index) => index === selectedVertexIndex ? nextPoint : point);

                    if (!mainPoints || nextPoints.every((point) => pointInPolygon(point, mainPoints))) {
                        shape.points[selectedVertexIndex] = nextPoint;
                        refreshZoneBindingUi();
                        redraw();
                    }
                } else if (shape.type === 'fence') {
                    if (isPointInMainZone(nextPoint, activeLayout)) {
                        shape.points[selectedVertexIndex] = nextPoint;
                        refreshZoneBindingUi();
                        redraw();
                    }
                }

                return;
            }

            if (shape.type === 'marker') {
                shape.x = pos.x - dragOffset.x;
                shape.y = pos.y - dragOffset.y;
            }

            if (shape.type === 'polygon') {
                const dx = pos.x - dragOffset.x - shape._dragStart.x;
                const dy = pos.y - dragOffset.y - shape._dragStart.y;
                const moved = shape._origPoints.map((p) => ({ x: p.x + dx, y: p.y + dy }));

                if (shape.main_fixed) {
                    shape.points = shape._origPoints;
                } else {
                    const mainPoints = getMainPolygonPoints(activeLayout);
                    if (!mainPoints || moved.every((p) => pointInPolygon(p, mainPoints))) {
                        shape.points = moved;
                    }
                }
            }

            redraw();
        }

        if (spacePressed || tool === 'pan') {
            canvas.style.cursor = panning ? 'grabbing' : 'grab';
        } else {
            canvas.style.cursor = tool === 'select' ? (findShapeAt(pos) >= 0 ? 'move' : 'default') : 'crosshair';
        }
    });

    canvas.addEventListener('mousedown', (e) => {
        if (spacePressed || tool === 'pan') {
            panning = true;
            panStart = getRawPos(e);
            canvas.style.cursor = 'grabbing';
            return;
        }

        if (tool !== 'select') {
            return;
        }

        const pos = getPos(e);
        const vertex = findVertexAt(pos);
        if (vertex) {
            const shape = getSelectedShape();
            if (canEditShapePoints(shape)) {
                selectedVertexIndex = vertex.vertexIndex;
                dragging = true;
                draggingVertex = true;
                redraw();
                return;
            }
        }

        const idx = findShapeAt(pos);
        if (idx < 0) {
            return;
        }

        selectedIdx = idx;
        selectedVertexIndex = null;
        dragging = true;

        const shapes = getShapes();
        const shape = shapes[idx];
        if (shape.type === 'marker') {
            dragOffset = { x: pos.x - shape.x, y: pos.y - shape.y };
        }

        if (shape.type === 'polygon') {
            if (shape.main_fixed) {
                dragging = false;
                hint.textContent = 'Pierwsza strefa jest glowna i nie mozna jej przesuwac.';
                redraw();
                return;
            }

            if (shape.locked) {
                dragging = false;
                hint.textContent = 'Ta strefa jest zablokowana. Odblokuj ja, aby przesuwac.';
                redraw();
                return;
            }

            const cx = shape.points.reduce((sum, p) => sum + p.x, 0) / shape.points.length;
            const cy = shape.points.reduce((sum, p) => sum + p.y, 0) / shape.points.length;
            dragOffset = { x: pos.x - cx, y: pos.y - cy };
            shape._dragStart = { x: cx, y: cy };
            shape._origPoints = shape.points.map((p) => ({ ...p }));
        }

        redraw();
    });

    canvas.addEventListener('mouseup', () => {
        dragging = false;
        draggingVertex = false;
        panning = false;
    });

    canvas.addEventListener('mouseleave', () => {
        hoverPoint = null;
        dragging = false;
        draggingVertex = false;
        panning = false;
        redraw();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Shift') {
            shiftPressed = true;
            return;
        }

        if (e.key === 'Alt') {
            altPressed = true;
            return;
        }

        if ((e.ctrlKey || e.metaKey) && !e.shiftKey && !e.altKey && e.key.toLowerCase() === 'z') {
            const tag = document.activeElement && document.activeElement.tagName;
            if (tag !== 'INPUT' && tag !== 'TEXTAREA' && tag !== 'SELECT') {
                e.preventDefault();
                undoLast();
            }
            return;
        }

        if ((e.key === 'l' || e.key === 'L') && !e.metaKey && !e.ctrlKey && !e.altKey) {
            const tag = document.activeElement && document.activeElement.tagName;
            if (tag !== 'INPUT' && tag !== 'TEXTAREA' && tag !== 'SELECT') {
                e.preventDefault();
                toggleLengthMode();
            }
            return;
        }

        if (e.code === 'Space') {
            const tag = document.activeElement && document.activeElement.tagName;
            if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') {
                return;
            }
            spacePressed = true;
            e.preventDefault();
            return;
        }

        if ((e.key === 'Delete' || e.key === 'Backspace') && selectedIdx >= 0) {
            if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                if (selectedVertexIndex !== null && removeSelectedVertex()) {
                    return;
                }
                const shapes = getShapes();
                shapes.splice(selectedIdx, 1);
                selectedIdx = null;
                selectedVertexIndex = null;
                enforceFirstMainZone(activeLayout);
                refreshZoneBindingUi();
                redraw();
            }
        }

        if (e.key === 'Escape' && tool === 'polygon') {
            currentPoly = [];
            redraw();
        }
    });

    document.addEventListener('keyup', (e) => {
        if (e.key === 'Shift') {
            shiftPressed = false;
            return;
        }

        if (e.key === 'Alt') {
            altPressed = false;
            return;
        }

        if (e.code === 'Space') {
            spacePressed = false;
            panning = false;
        }
    });

    window.setTool = function setTool(nextTool) {
        tool = nextTool;
        currentPoly = [];
        hoverPoint = null;
        currentFence = [];

        document.querySelectorAll('.tool-btn[id^="tool-"]').forEach((button) => button.classList.remove('active'));
        document.getElementById('tool-' + nextTool)?.classList.add('active');

        const hints = {
            pan: 'Przeciagnij aby przesunac widok. Uzyj kola myszy aby zoom.',
            polygon: 'Klikaj aby dodac wierzcholki. Przy kazdym kliknieciu mozesz podac dlugosc odcinka.',
            fence: 'Klikaj kolejne punkty linii. Przy kazdym kliknieciu mozesz podac dlugosc odcinka.',
            marker: 'Kliknij na mapie, aby dodac zaznacznik.',
            select: 'Kliknij element i przeciagnij, aby przesunac. Delete usuwa zaznaczony element.',
        };

        hint.textContent = hints[nextTool] || '';
        canvas.style.cursor = nextTool === 'pan' ? 'grab' : (nextTool === 'select' ? 'default' : 'crosshair');
        redraw();
    };

    window.toggleLengthMode = function toggleLengthMode() {
        promptLengthOnClick = !promptLengthOnClick;
        updateLengthModeButton();
        hint.textContent = promptLengthOnClick
            ? 'Tryb precyzyjny wlaczony: podajesz dlugosc i kat (np. 90, +90, -45). Skrot L.'
            : 'Tryb precyzyjny wylaczony: klik dodaje punkt bez pytania o dlugosc.';
    };

    window.zoomIn = function zoomIn() {
        const size = getCanvasSize();
        setZoomAtScreenPoint(zoom * 1.15, { x: size.w / 2, y: size.h / 2 });
        redraw();
    };

    window.zoomOut = function zoomOut() {
        const size = getCanvasSize();
        setZoomAtScreenPoint(zoom / 1.15, { x: size.w / 2, y: size.h / 2 });
        redraw();
    };

    window.resetZoom = function resetZoom() {
        zoom = 1;
        redraw();
    };

    window.setColor = function setColor(nextColor, element) {
        color = nextColor;
        document.querySelectorAll('.color-swatch').forEach((swatch) => swatch.classList.remove('active'));
        element.classList.add('active');
    };

    window.undoLast = function undoLast() {
        if (currentFence.length > 0) {
            currentFence.pop();
            hint.textContent = currentFence.length > 0
                ? 'Cofnieto ostatni punkt ogrodzenia.'
                : 'Cofnieto rozpoczecie ogrodzenia.';
            redraw();
            return;
        }

        if (currentPoly.length > 0) {
            currentPoly.pop();
            hint.textContent = currentPoly.length > 0
                ? 'Cofnieto ostatni wierzcholek strefy.'
                : 'Cofnieto rozpoczecie rysowania strefy.';
            redraw();
            return;
        }

        const shapes = getShapes();
        if (shapes.length > 0) {
            shapes.pop();
            hint.textContent = 'Cofnieto ostatnio dodany element mapy.';
            redraw();
        }
    };

    window.clearCanvas = async function clearCanvas() {
        const confirmed = await confirmEditorAction({
            title: 'Wyczysc mape',
            subtitle: 'Usuniesz wszystkie elementy z aktywnego ukladu.',
            submitLabel: 'Wyczysc',
        });
        if (!confirmed) {
            return;
        }

        layouts[activeLayout] = [];
        currentPoly = [];
        currentFence = [];
        selectedIdx = null;
        enforceFirstMainZone(activeLayout);
        refreshZoneBindingUi();
        redraw();
    };

    window.setScaleFromInput = function setScaleFromInput() {
        const value = Number(scaleInput.value);
        if (!Number.isNaN(value) && value > 0) {
            metersPerPixel = value;
            redraw();
        }
    };

    window.toggleMeasureMode = function toggleMeasureMode() {
        measureMode = !measureMode;
        measurePoints = [];
        document.getElementById('tool-measure').classList.toggle('active', measureMode);
        hint.textContent = measureMode
            ? 'Kliknij 2 punkty znanej odleglosci. Potem podaj metry, aby ustawic skale.'
            : 'Klikaj aby dodac wierzcholki. Dwuklik lub klik na start zamyka strefe.';
    };

    window.setLayoutMode = function setLayoutMode(nextLayout) {
        if (!layoutModes.includes(nextLayout)) {
            return;
        }

        activeLayout = nextLayout;
        selectedIdx = null;
        currentPoly = [];
        currentFence = [];
        measureMode = false;
        measurePoints = [];
        document.getElementById('tool-measure').classList.remove('active');
        enforceFirstMainZone(activeLayout);
        refreshZoneBindingUi();
        updateLayoutButtons();
        redraw();
    };

    window.copyCurrentToModified = async function copyCurrentToModified() {
        const confirmed = await confirmEditorAction({
            title: 'Nadpisz uklad po modyfikacjach',
            subtitle: 'Stan aktualny zostanie skopiowany do ukladu po modyfikacjach.',
            submitLabel: 'Kopiuj',
        });
        if (!confirmed) {
            return;
        }
        layouts.modified = JSON.parse(JSON.stringify(layouts.current));
        normalizeLayoutShapes('modified');
        enforceFirstMainZone('modified');
        refreshZoneBindingUi();
        if (activeLayout === 'modified') {
            redraw();
        }
    };

    window.toggleSelectedZoneLock = function toggleSelectedZoneLock() {
        const selectedShape = getSelectedShape();
        if (!selectedShape || selectedShape.type !== 'polygon') {
            hint.textContent = 'Najpierw zaznacz strefe, aby ustawic blokade.';
            return;
        }

        if (selectedShape.main_fixed) {
            hint.textContent = 'Pierwsza strefa jest glowna i zawsze zablokowana.';
            return;
        }

        selectedShape.locked = !selectedShape.locked;
        hint.textContent = selectedShape.locked
            ? 'Strefa zablokowana. Nie mozna jej przesuwac.'
            : 'Strefa odblokowana. Mozna ja przesuwac.';
        redraw();
    };

    window.setSelectedAsMainZone = function setSelectedAsMainZone() {
        const selectedShape = getSelectedShape();
        if (!selectedShape || selectedShape.type !== 'polygon') {
            hint.textContent = 'Najpierw zaznacz strefe, aby ustawic glowna.';
            return;
        }

        const firstPolygonIndex = getFirstPolygonIndex(activeLayout);
        if (firstPolygonIndex >= 0 && getShapes()[firstPolygonIndex] !== selectedShape) {
            hint.textContent = 'Pierwsza strefa jest zawsze glowna. Nie mozna jej zmienic.';
            return;
        }

        enforceFirstMainZone(activeLayout);
        hint.textContent = 'Pierwsza strefa jest glowna i zablokowana.';
        redraw();
    };

    function buildCanvasDataPayload() {
        return {
            layouts,
            active_layout: activeLayout,
            meters_per_pixel: metersPerPixel,
            zoom,
            pan_x: panX,
            pan_y: panY,
        };
    }

    function getCanvasStateHash() {
        return JSON.stringify(buildCanvasDataPayload());
    }

    function scheduleAutoSave() {
        const currentHash = getCanvasStateHash();
        if (currentHash === lastSavedCanvasHash) {
            return;
        }

        if (autosaveTimer) {
            clearTimeout(autosaveTimer);
        }

        autosaveTimer = setTimeout(async () => {
            autosaveTimer = null;

            const nextHash = getCanvasStateHash();
            if (nextHash === lastSavedCanvasHash) {
                return;
            }

            if (autosaveInFlight) {
                autosavePending = true;
                return;
            }

            autosaveInFlight = true;
            try {
                await persistCanvasData();
            } catch {
                // Zachowaj cisze - autosave nie powinien przeszkadzac w pracy.
            } finally {
                autosaveInFlight = false;
                if (autosavePending) {
                    autosavePending = false;
                    scheduleAutoSave();
                }
            }
        }, 700);
    }

    function latLngToMeters(lat, lng, originLat, originLng) {
        const earthRadius = 6378137;
        const dLat = (lat - originLat) * Math.PI / 180;
        const dLng = (lng - originLng) * Math.PI / 180;
        const avgLat = ((lat + originLat) / 2) * Math.PI / 180;

        return {
            x: dLng * earthRadius * Math.cos(avgLat),
            y: dLat * earthRadius,
        };
    }

    function updateGpsButtons() {
        if (!isMobileClient) {
            return;
        }

        if (gpsWalkStartBtn) {
            gpsWalkStartBtn.textContent = gpsWalkMode ? 'Spacer GPS aktywny' : 'Start spacer GPS';
            gpsWalkStartBtn.disabled = gpsWalkMode;
        }

        if (gpsWalkStopBtn) {
            gpsWalkStopBtn.disabled = !gpsWalkMode;
        }

        if (gpsWalkFinishBtn) {
            gpsWalkFinishBtn.disabled = gpsTrackPoints.length < 3;
        }
    }

    function drawGpsWalkTrack() {
        if (!gpsWalkMode || gpsTrackPoints.length < 1) {
            return;
        }

        ctx.beginPath();
        ctx.moveTo(gpsTrackPoints[0].x, gpsTrackPoints[0].y);
        for (let i = 1; i < gpsTrackPoints.length; i++) {
            ctx.lineTo(gpsTrackPoints[i].x, gpsTrackPoints[i].y);
        }

        ctx.strokeStyle = 'rgba(251,191,36,0.95)';
        ctx.lineWidth = 2.5;
        ctx.setLineDash([8, 6]);
        ctx.stroke();
        ctx.setLineDash([]);

        const last = gpsTrackPoints[gpsTrackPoints.length - 1];
        ctx.beginPath();
        ctx.arc(last.x, last.y, 6, 0, Math.PI * 2);
        ctx.fillStyle = '#fbbf24';
        ctx.fill();

        ctx.font = 'bold 11px system-ui, sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        const text = 'GPS: ' + gpsTrackPoints.length + ' pkt';
        const tm = ctx.measureText(text);
        ctx.fillStyle = 'rgba(15,23,42,0.9)';
        ctx.fillRect(last.x + 10, last.y - 10, tm.width + 12, 20);
        ctx.fillStyle = '#f8fafc';
        ctx.fillText(text, last.x + 16, last.y + 1);
    }

    window.startGpsWalkMode = function startGpsWalkMode() {
        if (!isMobileClient) {
            hint.textContent = 'Tryb GPS jest dostepny na telefonie.';
            return;
        }

        if (!navigator.geolocation) {
            hint.textContent = 'Twoja przegladarka nie obsluguje GPS.';
            return;
        }

        if (gpsWatchId) {
            navigator.geolocation.clearWatch(gpsWatchId);
        }

        gpsWalkMode = true;
        gpsTrackPoints = [];
        gpsOrigin = null;
        gpsBaseCenter = null;

        const size = getCanvasSize();
        const centerScreen = { x: size.w / 2, y: size.h / 2 };
        gpsBaseCenter = screenToWorld(centerScreen);

        gpsWatchId = navigator.geolocation.watchPosition((position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            if (!gpsOrigin) {
                gpsOrigin = { lat, lng };
            }

            const meters = latLngToMeters(lat, lng, gpsOrigin.lat, gpsOrigin.lng);
            const worldPoint = {
                x: gpsBaseCenter.x + (meters.x / metersPerPixel),
                y: gpsBaseCenter.y - (meters.y / metersPerPixel),
            };

            const last = gpsTrackPoints[gpsTrackPoints.length - 1];
            if (!last || dist(last, worldPoint) >= (0.8 / metersPerPixel)) {
                gpsTrackPoints.push(worldPoint);
                updateGpsButtons();
                redraw();
            }
        }, () => {
            hint.textContent = 'Brak dostepu do GPS. Sprawdz uprawnienia lokalizacji.';
        }, {
            enableHighAccuracy: true,
            maximumAge: 1000,
            timeout: 10000,
        });

        hint.textContent = 'Spacer GPS aktywny. Obejdz granice ogrodu i kliknij "Zamknij strefe z GPS".';
        updateGpsButtons();
        if (isMobileClient) {
            switchToMobileMode(true);
        }
        redraw();
    };

    window.stopGpsWalkMode = function stopGpsWalkMode() {
        if (gpsWatchId) {
            navigator.geolocation.clearWatch(gpsWatchId);
            gpsWatchId = null;
        }
        gpsWalkMode = false;
        if (isMobileClient) {
            switchToMobileMode(false);
        }
        updateGpsButtons();
        redraw();
    };

    window.finishGpsWalkMode = function finishGpsWalkMode() {
        if (gpsTrackPoints.length < 3) {
            hint.textContent = 'Zbyt malo punktow GPS. Potrzeba co najmniej 3.';
            return;
        }

        const firstPolygonIndex = getFirstPolygonIndex(activeLayout);
        const isMain = firstPolygonIndex < 0;

        getShapes().push({
            type: 'polygon',
            id: activeLayout + '-polygon-gps-' + Date.now() + '-' + Math.random().toString(36).slice(2, 7),
            color: '#16a34a',
            points: gpsTrackPoints.map((point) => ({ x: point.x, y: point.y })),
            label: isMain ? 'Strefa glowna (GPS)' : 'Strefa GPS',
            locked: false,
            is_main: false,
            main_fixed: false,
        });

        gpsTrackPoints = [];
        gpsOrigin = null;
        gpsBaseCenter = null;
        stopGpsWalkMode();
        enforceFirstMainZone(activeLayout);
        refreshZoneBindingUi();
        hint.textContent = 'Dodano strefe z trasy GPS.';
        redraw();
    };

    function initMobileGpsUi() {
        if (!isMobileClient) {
            return;
        }

        mobileGpsControls.forEach((el) => {
            el.style.display = '';
        });

        updateGpsButtons();
    }

    function switchToMobileMode(enable = true) {
        if (enable) {
            document.body.classList.add('mobile-ui-mode');
            isMobileUiMode = true;
        } else {
            document.body.classList.remove('mobile-ui-mode');
            isMobileUiMode = false;
        }
        redraw();
    }

    function initRealtimeSync() {
        if (!window.Pusher || !reverbConfig.key || !reverbConfig.host) {
            return;
        }

        try {
            realtimePusher = new window.Pusher(reverbConfig.key, {
                wsHost: reverbConfig.host,
                wsPort: reverbConfig.port,
                wssPort: reverbConfig.port,
                forceTLS: reverbConfig.scheme === 'https',
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
                cluster: 'mt1',
            });

            realtimeChannel = realtimePusher.subscribe(reverbConfig.channel);
            realtimeChannel.bind('canvas.updated', (payload) => {
                if (!payload || payload.client_id === sessionClientId || !payload.canvas_data) {
                    return;
                }

                const incoming = payload.canvas_data;
                if (!incoming.layouts || !Array.isArray(incoming.layouts.current) || !Array.isArray(incoming.layouts.modified)) {
                    return;
                }

                layouts = incoming.layouts;
                activeLayout = layoutModes.includes(incoming.active_layout) ? incoming.active_layout : activeLayout;
                metersPerPixel = (typeof incoming.meters_per_pixel === 'number' && incoming.meters_per_pixel > 0)
                    ? incoming.meters_per_pixel
                    : metersPerPixel;
                zoom = (typeof incoming.zoom === 'number' && incoming.zoom > 0)
                    ? clampZoom(incoming.zoom)
                    : zoom;
                panX = Number.isFinite(incoming.pan_x) ? incoming.pan_x : panX;
                panY = Number.isFinite(incoming.pan_y) ? incoming.pan_y : panY;

                normalizeLayoutShapes('current');
                normalizeLayoutShapes('modified');
                enforceFirstMainZone('current');
                enforceFirstMainZone('modified');
                scaleInput.value = metersPerPixel.toFixed(4);
                updateLayoutButtons();
                refreshZoneBindingUi();

                lastSavedCanvasHash = JSON.stringify(buildCanvasDataPayload());
                hint.textContent = 'Odebrano zmiany w czasie rzeczywistym.';
                redraw();
            });
        } catch {
            // Realtime optional - ignore setup errors.
        }
    }

    async function persistCanvasData() {
        const canvasPayload = buildCanvasDataPayload();
        const payloadHash = JSON.stringify(canvasPayload);

        const res = await fetch('{{ route("garden-sections.save-canvas", [$gardenProject, $gardenSection]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                canvas_data: canvasPayload,
                client_id: sessionClientId,
            }),
        });

        const json = await res.json();
        if (json && json.ok) {
            lastSavedCanvasHash = payloadHash;
        }

        return json;
    }

    window.openPanel = function openPanel(title) {
        const panel = document.getElementById('right-panel');
        const titleEl = document.getElementById('right-panel-title');
        if (title && titleEl) {
            titleEl.textContent = title;
        }
        panel.classList.add('open');
    };

    window.closePanel = function closePanel() {
        document.getElementById('right-panel').classList.remove('open');
    };

    window.saveCanvas = async function saveCanvas() {
        const button = document.getElementById('save-btn');
        const originalLabel = button.textContent;
        button.textContent = 'Zapisywanie...';
        button.disabled = true;

        try {
            const json = await persistCanvasData();
            button.textContent = json.ok ? 'Zapisano' : 'Blad';
        } catch {
            button.textContent = 'Blad';
        }

        setTimeout(() => {
            button.textContent = originalLabel;
            button.disabled = false;
        }, 1500);
    };

    updateLayoutButtons();
    updateZoneButtons();
    updateAreaInfo();
    enforceFirstMainZone('current');
    enforceFirstMainZone('modified');
    refreshZoneBindingUi();
    updateLengthModeButton();
    initMobileGpsUi();
    initRealtimeSync();
    lastSavedCanvasHash = getCanvasStateHash();

    if (zoneRefSelect) {
        zoneRefSelect.addEventListener('change', () => {
            allowTypeSuggestion = true;
            applyZoneSelection();
        });
    }

    if (autoQuantityCheckbox) {
        autoQuantityCheckbox.addEventListener('change', applyZoneSelection);
    }

    if (elementTypeInput) {
        elementTypeInput.addEventListener('change', () => {
            allowTypeSuggestion = false;
            applyZoneSelection();
        });
    }

    if (quantityInput) {
        quantityInput.addEventListener('input', updateLiveLineTotal);
        quantityInput.addEventListener('change', updateLiveLineTotal);
    }

    if (unitPriceInput) {
        unitPriceInput.addEventListener('input', updateLiveLineTotal);
        unitPriceInput.addEventListener('change', updateLiveLineTotal);
    }

    if (elementForm) {
        elementForm.addEventListener('submit', async (e) => {
            if (elementForm.dataset.autosaveSubmitting === '1') {
                return;
            }

            e.preventDefault();
            applyZoneSelection();

            elementForm.dataset.autosaveSubmitting = '1';
            try {
                await persistCanvasData();
            } catch {
                // Nie blokuj zapisu elementu, nawet jesli autosave mapy chwilowo sie nie powiedzie.
            }

            elementForm.submit();
        });
    }

    confirmForms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            if (form.dataset.confirming === '1') {
                return;
            }

            event.preventDefault();
            const confirmed = await confirmEditorAction({
                title: form.dataset.confirmTitle || 'Potwierdz operacje',
                subtitle: form.dataset.confirmSubtitle || '',
                submitLabel: form.dataset.confirmSubmit || 'Potwierdz',
            });

            if (!confirmed) {
                return;
            }

            form.dataset.confirming = '1';
            form.submit();
        });
    });

    updateLiveLineTotal();

    canvas.addEventListener('wheel', (e) => {
        e.preventDefault();
        const factor = e.deltaY < 0 ? 1.1 : 0.9;
        setZoomAtScreenPoint(zoom * factor, getRawPos(e));
        redraw();
    }, { passive: false });

    new ResizeObserver(resize).observe(wrap);
    resize();

    window.addEventListener('beforeunload', () => {
        if (gpsWatchId) {
            navigator.geolocation.clearWatch(gpsWatchId);
        }
    });

    window.showPublicLink = function() {
        const publicToken = '{{ $gardenSection->public_token }}';
        const link = window.location.protocol + '//' + window.location.host + '/draw/' + publicToken;
        const message = 'Link do rysowania głównej strefy (bez logowania):\n\n' + link + '\n\nLink skopiowany do schowka!';
        alert(message);
        navigator.clipboard.writeText(link).catch(e => console.error('Copy failed:', e));
    };
})();
</script>
@endsection
