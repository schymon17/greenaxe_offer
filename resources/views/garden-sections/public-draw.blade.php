<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $gardenSection->name }} — Rysuj Główną Strefę</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #111827;
            color: #1f2937;
            overflow: hidden;
            height: 100vh;
        }
        .topbar {
            height: 52px;
            background: #1f2937;
            display: flex;
            align-items: center;
            padding: 0 16px;
            gap: 12px;
            border-bottom: 1px solid #374151;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
        }
        .topbar-logo {
            font-size: 16px;
            font-weight: 800;
            color: #16a34a;
            letter-spacing: -0.3px;
            margin-right: 4px;
        }
        .topbar-sep { width: 1px; height: 28px; background: #374151; }
        .topbar-title {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #9ca3af;
            flex: 1;
        }
        .topbar-title strong { color: #f9fafb; }
        .topbar-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #9ca3af;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #16a34a;
        }
        .topbar-status.empty .status-dot { background: #ef4444; }
        
        .main-container {
            display: flex;
            height: calc(100vh - 52px);
            margin-top: 52px;
        }

        .canvas-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #1a1a2e;
            overflow: hidden;
            position: relative;
        }

        .canvas-toolbar {
            position: absolute;
            left: 12px;
            top: 12px;
            z-index: 50;
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 10px;
            padding: 8px 6px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.5);
            min-width: 160px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }

        .tool-group-label {
            font-size: 9px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 4px 6px 2px;
        }

        .tool-btn {
            padding: 10px 12px; border: 1px solid #334155; border-radius: 6px;
            background: #1e293b; cursor: pointer; font-size: 13px; font-weight: 600;
            color: #94a3b8; transition: all 0.15s; white-space: nowrap;
            text-align: left; width: 100%;
        }

        .tool-btn:hover { border-color: #16a34a; color: #4ade80; }
        .tool-btn.active { border-color: #16a34a; background: #14532d; color: #4ade80; }
        .tool-sep { height: 1px; background: #1e293b; margin: 3px 0; }

        #canvas-wrap { flex: 1; overflow: hidden; position: relative; cursor: grab; }
        canvas#garden-canvas { display: block; }

        .canvas-hint { 
            font-size: 11px; color: #64748b; padding: 5px 12px; 
            background: #0f172a; border-top: 1px solid #1e293b; flex-shrink: 0; 
        }

        .zoom-controls {
            position: absolute;
            right: 14px;
            bottom: 14px;
            display: flex;
            flex-direction: column;
            z-index: 20;
            box-shadow: 0 1px 4px rgba(0,0,0,0.35);
            border-radius: 4px;
            overflow: hidden;
        }

        .zoom-btn {
            width: 38px;
            height: 38px;
            border: none;
            background: #fff;
            color: #111827;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.15s;
        }

        .zoom-btn:hover { background: #f3f4f6; }
        .zoom-btn:last-child { border-bottom: none; }

        .zoom-reset {
            height: 24px;
            border: none;
            background: #f3f4f6;
            color: #111827;
            font-size: 10px;
            font-weight: 700;
            cursor: pointer;
            border-top: 1px solid #e5e7eb;
        }

        .zoom-reset:hover { background: #e5e7eb; }

        #zone-status {
            position: absolute;
            left: 12px;
            bottom: 60px;
            background: rgba(15,23,42,0.95);
            color: #e2e8f0;
            border: 1px solid #1e293b;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 600;
            z-index: 20;
            max-width: 160px;
        }

        .zone-status-empty { color: #fbbf24; }
        .zone-status-exists { color: #4ade80; }

        .reset-dialog {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .reset-dialog.show { display: flex; }

        .dialog-content {
            background: #f9fafb;
            border-radius: 12px;
            padding: 24px;
            max-width: 380px;
            box-shadow: 0 20px 48px rgba(0,0,0,0.3);
        }

        .dialog-title {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .dialog-desc {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .dialog-actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            flex: 1;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-primary {
            background: #16a34a;
            color: white;
            border: 1px solid #15803d;
        }

        .btn-primary:hover { background: #15803d; }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover { background: #d1d5db; }
    </style>
</head>
<body>

    <!-- Top Bar -->
    <div class="topbar">
        <span class="topbar-logo">🌿 Greenaxe</span>
        <div class="topbar-sep"></div>
        <span class="topbar-title">
            <strong>{{ $gardenSection->name }}</strong>
            <span style="color: #64748b;">— Główna Strefa</span>
        </span>
        <div style="flex: 1;"></div>
        <div class="topbar-status" id="topbar-status">
            <div class="status-dot"></div>
            <span>{{ $gardenSection->canvas_data && isset($gardenSection->canvas_data['layouts']['current']) && count($gardenSection->canvas_data['layouts']['current']) > 0 ? 'Strefa narysowana' : 'Brak strefy' }}</span>
        </div>
    </div>

    <!-- Main -->
    <div class="main-container">
        <div class="canvas-panel">
            <div id="canvas-wrap">
                <div class="canvas-toolbar">
                    <div class="tool-group-label">Widok</div>
                    <button class="tool-btn active" id="tool-pan" onclick="setTool('pan')">Przesuwanie</button>

                    <div class="tool-sep"></div>
                    <div class="tool-group-label">Rysowanie</div>
                    <button class="tool-btn" id="tool-polygon" onclick="setTool('polygon')">Rysuj strefę</button>

                    <div class="tool-sep"></div>
                    <div class="tool-group-label">Akcje</div>
                    <button class="tool-btn" onclick="undoLast()">Cofnij</button>
                    <button class="tool-btn" onclick="clearCanvas()">Wyczyść</button>
                </div>

                <canvas id="garden-canvas"></canvas>

                <div class="zoom-controls">
                    <button class="zoom-btn" onclick="zoomIn()" title="Przybliż">+</button>
                    <button class="zoom-btn" onclick="zoomOut()" title="Oddal">−</button>
                    <button class="zoom-reset" onclick="resetZoom()">RST</button>
                </div>

                <div id="zone-status" class="zone-status-empty">
                    ⚠ Brak głównej strefy
                </div>
            </div>

            <div class="canvas-hint" id="canvas-hint">
                Kliknij aby dodać wierzchołek strefy. Zamknij strefę naciskając Enter lub Shift+Click.
            </div>
        </div>
    </div>

    <!-- Reset Dialog -->
    <div class="reset-dialog" id="reset-dialog">
        <div class="dialog-content">
            <div class="dialog-title">Główna strefa już istnieje</div>
            <div class="dialog-desc">Czy chcesz ją zresetować i narysować nową?</div>
            <div class="dialog-actions">
                <button class="btn btn-secondary" onclick="cancelReset()">Nie, wróć</button>
                <button class="btn btn-primary" onclick="confirmReset()">Tak, reset</button>
            </div>
        </div>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        const canvas = document.getElementById('garden-canvas');
        const ctx = canvas.getContext('2d');
        const hint = document.getElementById('canvas-hint');
        const zoneStatus = document.getElementById('zone-status');
        const resetDialog = document.getElementById('reset-dialog');
        const sectionId = {{ $gardenSection->id }};
        const publicToken = '{{ $gardenSection->public_token }}';

        const sessionClientId = (window.crypto && typeof window.crypto.randomUUID === 'function')
            ? window.crypto.randomUUID()
            : ('client-' + Math.random().toString(36).slice(2, 12));

        const reverbConfig = {
            key: '{{ env("VITE_REVERB_APP_KEY") }}',
            host: (function (h) {
                h = '{{ env("VITE_REVERB_HOST", "localhost") }}';
                if (!h || h === 'localhost' || h === '127.0.0.1') {
                    return window.location.hostname;
                }
                return h;
            })(),
            port: {{ env("VITE_REVERB_PORT", 8080) }},
            scheme: '{{ env("VITE_REVERB_SCHEME", "http") }}',
            channel: 'garden-section.' + sectionId,
        };

        let tool = 'pan';
        let layouts = {
            current: [],
            modified: [],
        };
        let activeLayout = 'current';
        let metersPerPixel = 0.1;
        let currentPoly = [];
        let zoom = 1;
        let panX = 0;
        let panY = 0;
        let spacePressed = false;
        let panning = false;
        let panStart = null;
        let dragging = false;
        let dragOffset = { x: 0, y: 0 };
        let selectedIdx = null;
        let autosaveTimer = null;
        let autosaveInFlight = false;
        let lastSavedCanvasHash = '';
        let realtimePusher = null;
        let realtimeChannel = null;

        function getCanvasSize() {
            const wrap = document.getElementById('canvas-wrap');
            return { w: wrap.clientWidth, h: wrap.clientHeight };
        }

        function resizeCanvas() {
            const size = getCanvasSize();
            canvas.width = size.w;
            canvas.height = size.h;
            redraw();
        }

        function screenToWorld(p) {
            return {
                x: (p.x - panX) / zoom,
                y: (p.y - panY) / zoom,
            };
        }

        function worldToScreen(p) {
            return {
                x: p.x * zoom + panX,
                y: p.y * zoom + panY,
            };
        }

        function dist(a, b) {
            const dx = a.x - b.x, dy = a.y - b.y;
            return Math.sqrt(dx * dx + dy * dy);
        }

        function setTool(t) {
            tool = t;
            document.querySelectorAll('.tool-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('tool-' + t)?.classList.add('active');
            if (t !== 'polygon') {
                currentPoly = [];
            }
            redraw();
        }

        function zoomIn() {
            zoom *= 1.2;
            redraw();
        }

        function zoomOut() {
            zoom /= 1.2;
            redraw();
        }

        function resetZoom() {
            zoom = 1;
            panX = 0;
            panY = 0;
            redraw();
        }

        function undoLast() {
            if (currentPoly.length > 0) {
                currentPoly.pop();
                redraw();
            }
        }

        function clearCanvas() {
            if (confirm('Wyczyścić cały canvas?')) {
                currentPoly = [];
                layouts.current = [];
                layouts.modified = [];
                selectedIdx = null;
                scheduleAutoSave();
                redraw();
            }
        }

        function drawLayout(layout, isActive) {
            layout.forEach((zone, idx) => {
                const points = zone.points || [];
                if (points.length < 2) return;

                const screenPoints = points.map(p => worldToScreen(p));
                
                ctx.fillStyle = zone.color || '#16a34a';
                ctx.globalAlpha = isActive ? 0.4 : 0.2;
                ctx.beginPath();
                ctx.moveTo(screenPoints[0].x, screenPoints[0].y);
                for (let i = 1; i < screenPoints.length; i++) {
                    ctx.lineTo(screenPoints[i].x, screenPoints[i].y);
                }
                ctx.closePath();
                ctx.fill();

                ctx.globalAlpha = 1;
                ctx.strokeStyle = isActive ? zone.color || '#16a34a' : '#8b5cf6';
                ctx.lineWidth = 2 / zoom;
                ctx.stroke();

                if (zone.isMain) {
                    ctx.fillStyle = '#fbbf24';
                    ctx.font = (14 / zoom) + 'px sans-serif';
                    const centroid = screenPoints[0];
                    ctx.fillText('★ MAIN', centroid.x + 10, centroid.y + 10);
                }
            });
        }

        function drawCurrentPolygon() {
            if (currentPoly.length < 1) return;

            const screenPoints = currentPoly.map(p => worldToScreen(p));

            ctx.strokeStyle = '#16a34a';
            ctx.fillStyle = 'rgba(22, 163, 74, 0.2)';
            ctx.lineWidth = 2 / zoom;

            ctx.beginPath();
            ctx.moveTo(screenPoints[0].x, screenPoints[0].y);
            for (let i = 1; i < screenPoints.length; i++) {
                ctx.lineTo(screenPoints[i].x, screenPoints[i].y);
            }
            ctx.stroke();
            ctx.fill();

            screenPoints.forEach((p, i) => {
                ctx.fillStyle = '#00ff00';
                ctx.beginPath();
                ctx.arc(p.x, p.y, 4 / zoom, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = '#e2e8f0';
                ctx.font = (10 / zoom) + 'px sans-serif';
                ctx.fillText(i.toString(), p.x + 6, p.y + 4);
            });
        }

        function redraw() {
            ctx.fillStyle = '#1a1a2e';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            drawLayout(layouts.current, true);
            if (activeLayout === 'modified') {
                drawLayout(layouts.modified, true);
            }

            drawCurrentPolygon();

            updateZoneStatus();
        }

        function updateZoneStatus() {
            const hasMain = layouts.current.some(z => z.isMain);
            if (hasMain) {
                zoneStatus.textContent = '✓ Główna strefa: narysowana';
                zoneStatus.className = 'zone-status-exists';
            } else {
                zoneStatus.textContent = '⚠ Brak głównej strefy';
                zoneStatus.className = 'zone-status-empty';
            }
        }

        function getCanvasStateHash() {
            return JSON.stringify({
                layouts: layouts,
                currentPoly: currentPoly,
            });
        }

        function scheduleAutoSave() {
            if (autosaveTimer) clearTimeout(autosaveTimer);
            autosaveTimer = setTimeout(() => {
                persistCanvasData();
            }, 700);
        }

        function buildCanvasDataPayload() {
            return {
                layouts: layouts,
                active_layout: activeLayout,
                meters_per_pixel: metersPerPixel,
                zoom: zoom,
                pan_x: panX,
                pan_y: panY,
            };
        }

        function persistCanvasData() {
            const hash = getCanvasStateHash();
            if (hash === lastSavedCanvasHash) return;

            if (autosaveInFlight) {
                autosavePending = true;
                return;
            }

            autosaveInFlight = true;
            const payload = buildCanvasDataPayload();

            fetch('/projects/' + {{ $gardenProject->id }} + '/sections/' + sectionId + '/canvas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    canvas_data: payload,
                    client_id: sessionClientId,
                }),
            })
            .then(r => r.json())
            .then(() => {
                autosaveInFlight = false;
                lastSavedCanvasHash = hash;
                if (autosavePending) {
                    autosavePending = false;
                    persistCanvasData();
                }
            })
            .catch(e => {
                console.error('Autosave failed:', e);
                autosaveInFlight = false;
            });
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
                    if (!incoming.layouts) return;

                    layouts = incoming.layouts;
                    metersPerPixel = incoming.meters_per_pixel || metersPerPixel;
                    zoom = incoming.zoom || zoom;
                    panX = incoming.pan_x || panX;
                    panY = incoming.pan_y || panY;
                    redraw();
                });
            } catch (e) {
                console.warn('Realtime sync unavailable:', e.message);
            }
        }

        canvas.addEventListener('mousedown', (e) => {
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            if (e.button === 1 || (e.button === 0 && spacePressed)) {
                panning = true;
                panStart = { x, y };
                return;
            }

            if (tool === 'polygon') {
                const worldPos = screenToWorld({ x, y });
                currentPoly.push(worldPos);

                if (e.shiftKey || (currentPoly.length > 2 && dist(currentPoly[0], worldPos) < 15 / zoom)) {
                    closePolygon();
                }

                scheduleAutoSave();
                redraw();
            }
        });

        canvas.addEventListener('mousemove', (e) => {
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            if (panning && panStart) {
                panX += x - panStart.x;
                panY += y - panStart.y;
                panStart = { x, y };
                redraw();
            }
        });

        canvas.addEventListener('mouseup', () => {
            panning = false;
            panStart = null;
        });

        canvas.addEventListener('wheel', (e) => {
            e.preventDefault();
            zoom *= e.deltaY > 0 ? 0.8 : 1.2;
            redraw();
        });

        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                spacePressed = true;
                canvas.style.cursor = 'grab';
            }
            if (e.key === 'Enter' && tool === 'polygon' && currentPoly.length >= 3) {
                closePolygon();
            }
        });

        document.addEventListener('keyup', (e) => {
            if (e.code === 'Space') {
                spacePressed = false;
                canvas.style.cursor = tool === 'pan' ? 'grab' : 'crosshair';
            }
        });

        function closePolygon() {
            if (currentPoly.length < 3) {
                alert('Potrzeba co najmniej 3 punkty');
                return;
            }

            const checkExisting = layouts.current.filter(z => z.isMain);
            if (checkExisting.length > 0) {
                resetDialog.classList.add('show');
                return;
            }

            const newZone = {
                points: currentPoly,
                color: '#16a34a',
                isMain: true,
            };

            layouts.current.push(newZone);
            currentPoly = [];
            tool = 'pan';
            setTool('pan');
            scheduleAutoSave();
            hint.textContent = '✓ Główna strefa narysowana!';
            redraw();
        }

        function cancelReset() {
            resetDialog.classList.remove('show');
            currentPoly = [];
            tool = 'pan';
            setTool('pan');
            redraw();
        }

        function confirmReset() {
            resetDialog.classList.remove('show');
            layouts.current = layouts.current.filter(z => !z.isMain);
            layouts.modified = layouts.modified.filter(z => !z.isMain);
            
            const newZone = {
                points: currentPoly,
                color: '#16a34a',
                isMain: true,
            };

            layouts.current.push(newZone);
            currentPoly = [];
            tool = 'pan';
            setTool('pan');
            scheduleAutoSave();
            hint.textContent = '✓ Główna strefa zmieniona!';
            redraw();
        }

        window.addEventListener('resize', resizeCanvas);

        // Initialize
        const canvasData = @json($gardenSection->canvas_data ?? []);
        if (canvasData && canvasData.layouts) {
            layouts = canvasData.layouts;
            metersPerPixel = canvasData.meters_per_pixel || 0.1;
            zoom = canvasData.zoom || 1;
            panX = canvasData.pan_x || 0;
            panY = canvasData.pan_y || 0;
        }

        resizeCanvas();
        updateZoneStatus();
        initRealtimeSync();
        lastSavedCanvasHash = getCanvasStateHash();
        setTool('polygon');
    </script>
</body>
</html>
