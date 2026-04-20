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
    <div class="canvas-toolbar">
        <button class="tool-btn active" id="layout-current" onclick="setLayoutMode('current')">Stan aktualny</button>
        <button class="tool-btn" id="layout-modified" onclick="setLayoutMode('modified')">Po modyfikacjach</button>
        <button class="tool-btn" onclick="copyCurrentToModified()">Kopiuj aktualny -> modyfikacje</button>
        <div class="tool-sep"></div>
        <button class="tool-btn active" id="tool-polygon" onclick="setTool('polygon')">Rysuj strefe</button>
        <button class="tool-btn" id="tool-fence" onclick="setTool('fence')">Ogrodzenie panelowe</button>
        <button class="tool-btn" id="tool-marker" onclick="setTool('marker')">Zaznacznik</button>
        <button class="tool-btn" id="tool-select" onclick="setTool('select')">Zaznacz</button>
        <button class="tool-btn" id="tool-lock-zone" onclick="toggleSelectedZoneLock()">Blokuj strefe</button>
        <button class="tool-btn" id="tool-main-zone" onclick="setSelectedAsMainZone()">Ustaw glowna</button>
        <div class="tool-sep"></div>
        <button class="tool-btn" onclick="undoLast()">Cofnij</button>
        <button class="tool-btn" onclick="clearCanvas()">Wyczysc</button>
        <div class="tool-sep"></div>
        <button class="tool-btn" id="tool-measure" onclick="toggleMeasureMode()">Skaluj odcinkiem</button>
        <input id="meters-per-pixel" type="number" min="0.0001" step="0.0001" value="0.1000" onchange="setScaleFromInput()" style="width:90px;padding:5px 8px;border:1px solid #334155;border-radius:5px;background:#0f172a;color:#e2e8f0;font-size:12px;">
        <span style="font-size:11px;color:#94a3b8;">m/px</span>
        <div class="tool-sep"></div>
        <span id="layout-area-info" style="font-size:11px;color:#94a3b8;font-weight:600;">Powierzchnia: 0.00 m2</span>
        <div class="tool-sep"></div>
        <span style="font-size:11px;color:#64748b;font-weight:600;">Kolor:</span>
        @foreach(['#16a34a','#2563eb','#dc2626','#d97706','#7c3aed','#0891b2','#be185d','#e2e8f0'] as $c)
            <div class="color-swatch {{ $c === '#16a34a' ? 'active' : '' }}" style="background:{{ $c }};" onclick="setColor('{{ $c }}', this)"></div>
        @endforeach
    </div>

    <div id="canvas-wrap">
        <canvas id="garden-canvas"></canvas>

        <div style="position:absolute;right:14px;bottom:14px;display:flex;flex-direction:column;align-items:stretch;z-index:20;box-shadow:0 1px 4px rgba(0,0,0,0.35);border-radius:4px;overflow:hidden;">
            <button onclick="zoomIn()" title="Przybliz" style="width:38px;height:38px;border:none;background:#fff;color:#111827;font-size:22px;line-height:1;cursor:pointer;border-bottom:1px solid #e5e7eb;">+</button>
            <button onclick="zoomOut()" title="Oddal" style="width:38px;height:38px;border:none;background:#fff;color:#111827;font-size:22px;line-height:1;cursor:pointer;">-</button>
            <button onclick="resetZoom()" title="Reset zoom" style="height:24px;border:none;background:#f3f4f6;color:#111827;font-size:10px;font-weight:700;cursor:pointer;border-top:1px solid #e5e7eb;">RST</button>
        </div>

        <div id="zoom-level-info" style="position:absolute;right:14px;bottom:124px;z-index:20;background:rgba(15,23,42,0.88);color:#e2e8f0;font-size:11px;font-weight:600;padding:4px 8px;border-radius:4px;min-width:48px;text-align:center;">100%</div>
    </div>

    <div class="canvas-hint" id="canvas-hint">
        Kliknij aby dodac wierzcholek strefy. Dwuklik lub klik na punkt startowy zamyka strefe.
    </div>
</div>

<div class="right-panel">
    <div class="right-panel-tabs">
        <button class="tab-btn active" onclick="switchTab('elements')">Elementy</button>
        <button class="tab-btn" onclick="switchTab('list')">Lista ({{ $gardenSection->elements->count() }})</button>
        <button class="tab-btn" onclick="switchTab('settings')">Sekcja</button>
    </div>

    <div class="tab-panel active" id="tab-elements">
        <form method="POST" action="{{ route('garden-sections.elements.store', [$gardenProject, $gardenSection]) }}">
            @csrf

            <div class="fp-row">
                <label>Typ Pracy *</label>
                <select name="type" required>
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
                    <input type="number" name="quantity" min="0" step="0.01" placeholder="0.00" required>
                </div>
                <div class="fp-row">
                    <label>Jednostka</label>
                    <select name="unit">
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
                <input type="number" name="unit_price" min="0" step="0.01" placeholder="0.00" required>
            </div>

            <div class="fp-row">
                <label>Notatki</label>
                <textarea name="notes" rows="2" placeholder="Dodatkowe uwagi..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Dodaj Element</button>
        </form>
    </div>

    <div class="tab-panel" id="tab-list">
        @forelse($gardenSection->elements as $element)
            @php $elTotal = $element->quantity * $element->unit_price; @endphp
            <div class="el-row">
                <div class="el-name">{{ $element->name }}</div>
                <div class="el-meta">
                    {{ $typeLabels[$element->type] ?? $element->type }}
                    @if($element->material)
                        | {{ $element->material }}
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
                    <form method="POST" action="{{ route('garden-sections.elements.destroy', [$gardenProject, $gardenSection, $element]) }}" onsubmit="return confirm('Usunac element?')" style="display:inline;">
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
            <form method="POST" action="{{ route('garden-sections.destroy', [$gardenProject, $gardenSection]) }}" onsubmit="return confirm('Usunac cala sekcje wraz z elementami?')">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:none;border:1px solid #fca5a5;color:#dc2626;border-radius:6px;padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer;width:100%;font-family:inherit;">
                    Usun cala sekcje
                </button>
            </form>
        </div>
    </div>
</div>

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
    const lockZoneButton = document.getElementById('tool-lock-zone');
    const mainZoneButton = document.getElementById('tool-main-zone');
    const layoutModes = ['current', 'modified'];

    let tool = 'polygon';
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
    let dragging = false;
    let dragOffset = { x: 0, y: 0 };
    let measureMode = false;
    let measurePoints = [];
    let currentFence = [];
    let zoom = 1;
    let panX = 0;
    let panY = 0;
    let spacePressed = false;
    let shiftPressed = false;
    let panning = false;
    let panStart = null;

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
        layouts[layoutName] = (layouts[layoutName] || []).map((shape) => {
            if (shape.type !== 'polygon') {
                return shape;
            }

            return {
                ...shape,
                locked: Boolean(shape.locked),
                is_main: Boolean(shape.is_main),
                main_fixed: Boolean(shape.main_fixed),
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
        const w = canvas.width / (window.devicePixelRatio || 1);
        const h = canvas.height / (window.devicePixelRatio || 1);
        ctx.strokeStyle = 'rgba(255,255,255,0.04)';
        ctx.lineWidth = 1;

        for (let x = 0; x <= w; x += 50) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, h);
            ctx.stroke();
        }

        for (let y = 0; y <= h; y += 50) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(w, y);
            ctx.stroke();
        }
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
        ctx.restore();

        updateAreaInfo();
        updateZoneButtons();
        updateZoomInfo();
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

    function closePolygon() {
        if (currentPoly.length < 3) {
            currentPoly = [];
            redraw();
            return;
        }

        const label = prompt('Nazwa strefy (opcjonalnie):') || '';
        getShapes().push({
            type: 'polygon',
            color,
            points: [...currentPoly],
            label,
            locked: false,
            is_main: false,
            main_fixed: false,
        });

        currentPoly = [];
        enforceFirstMainZone(activeLayout);
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
            color: '#f97316',
            system: FENCE_SYSTEM_NAME,
            points: [...currentFence],
            panels: req.panels,
            posts: req.posts,
            length_m: Number(req.lengthM.toFixed(2)),
        });
        currentFence = [];
        hint.textContent = 'Ogrodzenie: ' + req.lengthM.toFixed(2) + ' m, panele: ' + req.panels + ', slupki: ' + req.posts + '.';
        redraw();
    }

    canvas.addEventListener('click', (e) => {
        if (dragging) {
            return;
        }

        const pos = getPos(e);

        if (measureMode) {
            measurePoints.push(pos);
            if (measurePoints.length === 2) {
                const pxDistance = dist(measurePoints[0], measurePoints[1]);
                const realDistance = Number(prompt('Podaj rzeczywista dlugosc odcinka w metrach:', '10') || '0');
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

            if (getFirstPolygonIndex(activeLayout) >= 0 && !isPointInMainZone(drawPos, activeLayout)) {
                hint.textContent = 'Nowe elementy strefy musza byc wewnatrz glownej strefy.';
                return;
            }

            if (!currentPoly.length) {
                currentPoly.push(drawPos);
                return;
            }

            if (currentPoly.length >= 2 && dist(drawPos, currentPoly[0]) < 14) {
                closePolygon();
                return;
            }

            currentPoly.push(drawPos);
            redraw();
            return;
        }

        if (tool === 'fence') {
            if (!isPointInMainZone(pos, activeLayout)) {
                hint.textContent = 'Ogrodzenie musi byc rysowane wewnatrz glownej strefy.';
                return;
            }

            currentFence.push(pos);
            redraw();
            return;
        }

        if (tool === 'marker') {
            if (!isPointInMainZone(pos, activeLayout)) {
                hint.textContent = 'Elementy nie moga wychodzic poza glowna strefe.';
                return;
            }
            const label = prompt('Opis zaznacznika:') || '';
            getShapes().push({ type: 'marker', color, x: pos.x, y: pos.y, label });
            redraw();
            return;
        }

        if (tool === 'select') {
            selectedIdx = findShapeAt(pos);
            redraw();
        }
    });

    canvas.addEventListener('dblclick', (e) => {
        if (tool === 'polygon' && currentPoly.length >= 3) {
            closePolygon();
            return;
        }

        if (tool === 'fence' && currentFence.length >= 2) {
            closeFence();
            return;
        }

        if (tool === 'select') {
            const seg = findEditableSegmentAt(getPos(e));
            if (!seg) {
                return;
            }

            const shape = getShapes()[seg.shapeIndex];
            const points = shape.points || [];
            const nextIndex = seg.closed ? (seg.edgeIndex + 1) % points.length : seg.edgeIndex + 1;
            if (!points[seg.edgeIndex] || !points[nextIndex]) {
                return;
            }

            const currentLen = dist(points[seg.edgeIndex], points[nextIndex]) * metersPerPixel;
            const input = prompt('Podaj dlugosc odcinka [m]:', currentLen.toFixed(2));
            const desired = Number(input || '0');
            if (!Number.isFinite(desired) || desired <= 0) {
                return;
            }

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
            hoverPoint = shiftPressed
                ? constrainAxisPoint(currentPoly[currentPoly.length - 1], pos)
                : pos;
            redraw();
        }

        if (tool === 'fence' && currentFence.length > 0) {
            hoverPoint = pos;
            redraw();
        }

        if (tool === 'select' && dragging && selectedIdx >= 0) {
            const shapes = getShapes();
            const shape = shapes[selectedIdx];

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

        if (spacePressed) {
            canvas.style.cursor = 'grab';
        } else {
            canvas.style.cursor = tool === 'select' ? (findShapeAt(pos) >= 0 ? 'move' : 'default') : 'crosshair';
        }
    });

    canvas.addEventListener('mousedown', (e) => {
        if (spacePressed) {
            panning = true;
            panStart = getRawPos(e);
            canvas.style.cursor = 'grabbing';
            return;
        }

        if (tool !== 'select') {
            return;
        }

        const pos = getPos(e);
        const idx = findShapeAt(pos);
        if (idx < 0) {
            return;
        }

        selectedIdx = idx;
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
        panning = false;
    });

    canvas.addEventListener('mouseleave', () => {
        hoverPoint = null;
        dragging = false;
        panning = false;
        redraw();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Shift') {
            shiftPressed = true;
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
                const shapes = getShapes();
                shapes.splice(selectedIdx, 1);
                selectedIdx = null;
                enforceFirstMainZone(activeLayout);
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
            polygon: 'Klikaj aby dodac wierzcholki. Dwuklik lub klik na start zamyka strefe.',
            fence: 'Klikaj kolejne punkty linii ogrodzenia. Dwuklik konczy rysowanie i liczy panele/slupki.',
            marker: 'Kliknij na mapie, aby dodac zaznacznik.',
            select: 'Kliknij element i przeciagnij, aby przesunac. Delete usuwa zaznaczony element.',
        };

        hint.textContent = hints[nextTool] || '';
        redraw();
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
            redraw();
            return;
        }

        if (currentPoly.length > 0) {
            currentPoly.pop();
            redraw();
            return;
        }

        const shapes = getShapes();
        if (shapes.length > 0) {
            shapes.pop();
            redraw();
        }
    };

    window.clearCanvas = function clearCanvas() {
        if (!confirm('Wyczyscic cala mape?')) {
            return;
        }

        layouts[activeLayout] = [];
        currentPoly = [];
        currentFence = [];
        selectedIdx = null;
        enforceFirstMainZone(activeLayout);
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
        updateLayoutButtons();
        redraw();
    };

    window.copyCurrentToModified = function copyCurrentToModified() {
        if (!confirm('Nadpisac uklad "Po modyfikacjach" na podstawie stanu aktualnego?')) {
            return;
        }
        layouts.modified = JSON.parse(JSON.stringify(layouts.current));
        normalizeLayoutShapes('modified');
        enforceFirstMainZone('modified');
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

    window.saveCanvas = async function saveCanvas() {
        const button = document.getElementById('save-btn');
        const originalLabel = button.textContent;
        button.textContent = 'Zapisywanie...';
        button.disabled = true;

        try {
            const res = await fetch('{{ route("garden-sections.save-canvas", [$gardenProject, $gardenSection]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    canvas_data: {
                        layouts,
                        active_layout: activeLayout,
                        meters_per_pixel: metersPerPixel,
                        zoom,
                    },
                }),
            });

            const json = await res.json();
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

    canvas.addEventListener('wheel', (e) => {
        e.preventDefault();
        const factor = e.deltaY < 0 ? 1.1 : 0.9;
        setZoomAtScreenPoint(zoom * factor, getRawPos(e));
        redraw();
    }, { passive: false });

    new ResizeObserver(resize).observe(wrap);
    resize();
})();
</script>
@endsection
