<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Edytor Sekcji — Greenaxe')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #111827;
            color: #1f2937;
            overflow: hidden;
            height: 100vh;
        }
        /* Editor top bar */
        .editor-topbar {
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
        .editor-topbar-logo {
            font-size: 16px;
            font-weight: 800;
            color: #16a34a;
            letter-spacing: -0.3px;
            margin-right: 4px;
        }
        .topbar-sep { width: 1px; height: 28px; background: #374151; }
        .topbar-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #9ca3af;
        }
        .topbar-breadcrumb a { color: #6b7280; text-decoration: none; transition: color 0.15s; }
        .topbar-breadcrumb a:hover { color: #d1d5db; }
        .topbar-breadcrumb .current { color: #f9fafb; font-weight: 600; }
        .topbar-spacer { flex: 1; }
        .topbar-btn {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600;
            text-decoration: none; border: none; cursor: pointer; transition: all 0.15s;
        }
        .topbar-btn-ghost { background: transparent; color: #9ca3af; border: 1px solid #374151; }
        .topbar-btn-ghost:hover { background: #374151; color: #f9fafb; }
        .topbar-btn-primary { background: #16a34a; color: white; border: 1px solid #15803d; }
        .topbar-btn-primary:hover { background: #15803d; }

        /* Alert inside editor */
        .editor-alert {
            position: fixed;
            top: 60px; left: 50%; transform: translateX(-50%);
            background: #14532d; color: #bbf7d0;
            border: 1px solid #16a34a;
            padding: 8px 20px; border-radius: 8px;
            font-size: 13px; font-weight: 600;
            z-index: 200;
            opacity: 1;
        }

        /* Main editor body */
        .editor-body {
            display: flex;
            height: calc(100vh - 52px);
            margin-top: 52px;
        }

        /* Right panel */
        .right-panel {
            width: 400px;
            flex-shrink: 0;
            background: #f9fafb;
            border-left: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .right-panel-tabs {
            display: flex;
            border-bottom: 1px solid #e5e7eb;
            background: white;
            flex-shrink: 0;
        }
        .tab-btn {
            flex: 1;
            padding: 12px 8px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            border: none;
            background: transparent;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.15s;
        }
        .tab-btn.active { color: #16a34a; border-bottom-color: #16a34a; background: #f0fdf4; }
        .tab-btn:hover:not(.active) { background: #f9fafb; color: #374151; }
        .tab-panel { display: none; flex: 1; overflow-y: auto; padding: 16px; }
        .tab-panel.active { display: block; }

        /* Form inside right panel */
        .fp-row { margin-bottom: 12px; }
        .fp-row label { display: block; font-size: 11px; font-weight: 600; color: #6b7280; margin-bottom: 4px; text-transform: uppercase; }
        .fp-row input, .fp-row select, .fp-row textarea {
            width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px;
            font-size: 13px; color: #1f2937; background: white; box-sizing: border-box;
            font-family: inherit;
        }
        .fp-row input:focus, .fp-row select:focus, .fp-row textarea:focus { outline: none; border-color: #16a34a; }
        .fp-grid-2 { display: grid; grid-template-columns: 1fr 90px; gap: 8px; }
        .btn-submit {
            background: #16a34a; color: white; border: none; border-radius: 6px;
            padding: 9px 16px; font-size: 13px; font-weight: 600; cursor: pointer;
            width: 100%; transition: background 0.15s; font-family: inherit;
        }
        .btn-submit:hover { background: #15803d; }

        /* Element list */
        .el-row {
            border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 12px;
            margin-bottom: 8px; background: white;
        }
        .el-name { font-size: 13px; font-weight: 600; color: #1f2937; }
        .el-meta { font-size: 11px; color: #6b7280; margin: 2px 0 4px; }
        .el-row-footer { display: flex; justify-content: space-between; align-items: center; }
        .el-qty { font-size: 12px; color: #374151; }
        .el-total { font-size: 13px; font-weight: 700; color: #16a34a; }
        .btn-del { background: #fee2e2; color: #dc2626; border: none; border-radius: 4px; padding: 3px 9px; font-size: 11px; font-weight: 600; cursor: pointer; }
        .btn-del:hover { background: #fca5a5; }
        .total-bar { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; margin-top: 12px; }
        .total-bar span { font-size: 12px; color: #15803d; font-weight: 600; }
        .total-bar strong { font-size: 18px; color: #14532d; font-weight: 800; }
        .empty-state { text-align: center; padding: 32px 16px; color: #9ca3af; font-size: 13px; }

        /* Canvas panel (fills remaining space) */
        .canvas-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #1a1a2e;
            overflow: hidden;
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
            min-width: 148px;
            max-height: calc(100vh - 80px);
            overflow-y: auto;
        }
        .tool-btn {
            padding: 7px 12px; border: 1px solid #334155; border-radius: 6px;
            background: #1e293b; cursor: pointer; font-size: 12px; font-weight: 600;
            color: #94a3b8; transition: all 0.15s; white-space: nowrap;
            text-align: left; width: 100%;
        }
        .tool-btn:hover { border-color: #16a34a; color: #4ade80; }
        .tool-btn.active { border-color: #16a34a; background: #14532d; color: #4ade80; }
        .tool-group-label {
            font-size: 9px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 4px 6px 2px;
        }
        .tool-sep { height: 1px; background: #1e293b; margin: 3px 0; }
        .color-swatch { width: 22px; height: 22px; border-radius: 4px; cursor: pointer; border: 2px solid transparent; }
        .color-swatch.active { border-color: white !important; }
        .color-row { display: flex; flex-wrap: wrap; gap: 4px; padding: 2px 4px 4px; }
        .scale-row { display: flex; align-items: center; gap: 4px; padding: 2px 4px 4px; }
        .scale-row input { flex: 1; min-width: 0; padding: 4px 6px; border: 1px solid #334155; border-radius: 5px; background: #0f172a; color: #e2e8f0; font-size: 11px; }
        .scale-row span { font-size: 10px; color: #64748b; flex-shrink: 0; }
        #canvas-wrap { flex: 1; overflow: hidden; position: relative; cursor: grab; }
        canvas#garden-canvas { display: block; }
        .canvas-hint { font-size: 11px; color: #64748b; padding: 5px 12px; background: #0f172a; border-top: 1px solid #1e293b; flex-shrink: 0; }

        /* Mobile UI Mode - Larger buttons, simplified toolbar */
        body.mobile-ui-mode .canvas-toolbar {
            min-width: 200px;
            max-height: calc(100vh - 100px);
        }
        body.mobile-ui-mode .tool-btn {
            padding: 12px 14px;
            font-size: 14px;
            font-weight: 700;
            min-height: 48px;
            line-height: 1.2;
        }
        body.mobile-ui-mode .tool-group-label {
            font-size: 11px;
            padding: 8px 6px 3px;
            margin-top: 8px;
        }
        body.mobile-ui-mode .tool-sep {
            margin: 8px 0;
            height: 2px;
            background: #334155;
        }
        body.mobile-ui-mode #tool-measure,
        body.mobile-ui-mode #tool-lock-zone,
        body.mobile-ui-mode #tool-main-zone,
        body.mobile-ui-mode #mode-length,
        body.mobile-ui-mode .scale-row,
        body.mobile-ui-mode #layout-area-info,
        body.mobile-ui-mode #tool-open-panel,
        body.mobile-ui-mode #layout-current,
        body.mobile-ui-mode #layout-modified {
            display: none !important;
        }
        body.mobile-ui-mode .mobile-gps-controls {
            display: block !important;
        }
        body.mobile-ui-mode .tool-btn.mobile-gps-controls {
            min-height: 48px;
            font-size: 14px;
            font-weight: 700;
        }
        body.mobile-ui-mode .right-panel {
            display: none !important;
        }
        body.mobile-ui-mode .color-swatch {
            width: 36px;
            height: 36px;
        }
    </style>
</head>
<body>

    <!-- Top Bar -->
    <div class="editor-topbar">
        <span class="editor-topbar-logo">🌿 Greenaxe</span>
        <div class="topbar-sep"></div>
        @yield('topbar-breadcrumb')
        <div class="topbar-spacer"></div>
        @yield('topbar-actions')
    </div>

    @if(session('success'))
        <div class="editor-alert">✓ {{ session('success') }}</div>
    @endif

    <!-- Editor Body -->
    <div class="editor-body">
        @yield('editor-body')
    </div>

</body>
</html>
