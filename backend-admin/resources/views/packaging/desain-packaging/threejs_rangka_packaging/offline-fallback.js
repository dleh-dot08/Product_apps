(function () {
  'use strict';

  window.startOfflineFallback = function startOfflineFallback() {
    if (window.__offlineFallbackStarted) return;
    window.__offlineFallbackStarted = true;

    var FACE_CONFIG = [
      { id: 'front', name: 'Depan' },
      { id: 'back', name: 'Belakang' },
      { id: 'right', name: 'Kanan' },
      { id: 'left', name: 'Kiri' },
      { id: 'top', name: 'Atas' },
      { id: 'bottom', name: 'Bawah' }
    ];

    var DEFAULTS = {
      length: 2000,
      width: 1000,
      height: 1200,
      frameSize: 80,
      supportSize: 60,
      maxSpacing: 500,
      boardWidth: 150,
      boardThickness: 20,
      halfGap: 35,
      plywoodThickness: 9
    };

    var wrap = document.getElementById('canvasWrap');
    var faceGrid = document.getElementById('faceGrid');
    var coverGrid = document.getElementById('coverGrid');
    var errorOverlay = document.getElementById('errorOverlay');
    var totalSupportsEl = document.getElementById('totalSupports');
    var totalCoverPiecesEl = document.getElementById('totalCoverPieces');
    var coverAreaEl = document.getElementById('coverArea');
    var totalBeamsEl = document.getElementById('totalBeams');
    var totalLengthEl = document.getElementById('totalLength');
    var faceSummaryEl = document.getElementById('faceSummary');
    var modeBadgeEl = document.getElementById('modeBadge');
    var statusText = document.getElementById('statusText');

    if (errorOverlay) errorOverlay.style.display = 'none';
    if (!wrap) return;

    if (faceGrid && !faceGrid.children.length) {
      FACE_CONFIG.forEach(function (face) {
        var card = document.createElement('div');
        card.className = 'face-card';
        card.innerHTML =
          '<div class="face-head">' +
            '<span class="face-name">' + face.name + '</span>' +
            '<label class="switch" title="Aktif/nonaktif">' +
              '<input id="' + face.id + 'Enabled" type="checkbox" checked />' +
              '<span class="slider"></span>' +
            '</label>' +
          '</div>' +
          '<select id="' + face.id + 'Orientation" aria-label="Orientasi penyangga ' + face.name + '">' +
            '<option value="H">Horizontal</option>' +
            '<option value="V">Vertikal</option>' +
          '</select>' +
          '<select id="' + face.id + 'Count" aria-label="Jumlah penyangga ' + face.name + '">' +
            '<option value="auto">Otomatis (1–3)</option>' +
            '<option value="1">1 penyangga</option>' +
            '<option value="2">2 penyangga</option>' +
            '<option value="3">3 penyangga</option>' +
          '</select>';
        faceGrid.appendChild(card);
      });
    }

    if (coverGrid && !coverGrid.children.length) {
      FACE_CONFIG.forEach(function (face) {
        var card = document.createElement('div');
        card.className = 'cover-card';
        card.innerHTML =
          '<div class="face-head">' +
            '<span class="face-name">' + face.name + '</span>' +
            '<label class="switch" title="Aktif/nonaktif penutup">' +
              '<input id="' + face.id + 'CoverEnabled" type="checkbox" checked />' +
              '<span class="slider"></span>' +
            '</label>' +
          '</div>' +
          '<select id="' + face.id + 'CoverType" aria-label="Jenis penutup ' + face.name + '">' +
            '<option value="half">Papan Setengah</option>' +
            '<option value="full">Papan Full</option>' +
            '<option value="plywood">Triplex</option>' +
          '</select>' +
          '<select id="' + face.id + 'CoverOrientation" aria-label="Arah penutup ' + face.name + '">' +
            '<option value="H">Horizontal</option>' +
            '<option value="V">Vertikal</option>' +
          '</select>';
        coverGrid.appendChild(card);
      });
    }

    var canvas = document.createElement('canvas');
    canvas.setAttribute('aria-label', 'Visual 3D rangka packaging kayu');
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    canvas.style.cursor = 'grab';
    wrap.innerHTML = '';
    wrap.appendChild(canvas);
    var ctx = canvas.getContext('2d');

    var boxes = [];
    var yaw = -0.78;
    var pitch = 0.48;
    var zoom = 1;
    var currentMaxDimension = 2;
    var dimensionsVisible = true;
    var gridVisible = true;
    var dragging = false;
    var lastX = 0;
    var lastY = 0;

    function getNumber(id, fallback) {
      var el = document.getElementById(id);
      var value = el ? Number(el.value) : fallback;
      return Number.isFinite(value) && value > 0 ? value : fallback;
    }

    function readInputs() {
      return {
        length: getNumber('length', DEFAULTS.length),
        width: getNumber('width', DEFAULTS.width),
        height: getNumber('height', DEFAULTS.height),
        frameSize: getNumber('frameSize', DEFAULTS.frameSize),
        supportSize: getNumber('supportSize', DEFAULTS.supportSize),
        maxSpacing: getNumber('maxSpacing', DEFAULTS.maxSpacing),
        boardWidth: getNumber('boardWidth', DEFAULTS.boardWidth),
        boardThickness: getNumber('boardThickness', DEFAULTS.boardThickness),
        halfGap: getNumber('halfGap', DEFAULTS.halfGap),
        plywoodThickness: getNumber('plywoodThickness', DEFAULTS.plywoodThickness)
      };
    }

    function readFace(face) {
      var enabled = document.getElementById(face.id + 'Enabled');
      var orientation = document.getElementById(face.id + 'Orientation');
      var countMode = document.getElementById(face.id + 'Count');
      return {
        enabled: !enabled || enabled.checked,
        orientation: orientation ? orientation.value : 'H',
        countMode: countMode ? countMode.value : 'auto'
      };
    }

    function readCover(face) {
      var enabled = document.getElementById(face.id + 'CoverEnabled');
      var type = document.getElementById(face.id + 'CoverType');
      var orientation = document.getElementById(face.id + 'CoverOrientation');
      return {
        enabled: !enabled || enabled.checked,
        type: type ? type.value : 'half',
        orientation: orientation ? orientation.value : 'H'
      };
    }

    function autoCount(spanMm, spacingMm) {
      return Math.max(0, Math.ceil(spanMm / spacingMm) - 1);
    }

    function resolvedCount(faceState, crossSpanMm, spacingMm) {
      return faceState.countMode === 'auto' ? autoCount(crossSpanMm, spacingMm) : Number(faceState.countMode);
    }

    function evenlySpaced(count, min, max) {
      var positions = [];
      for (var i = 1; i <= count; i += 1) positions.push(min + (max - min) * (i / (count + 1)));
      return positions;
    }

    function addBox(sx, sy, sz, x, y, z, type, name) {
      boxes.push({ sx: sx, sy: sy, sz: sz, x: x, y: y, z: z, type: type || 'frame', name: name || 'Balok' });
    }

    function makeBoardLayout(crossSpan, boardWidth, gap, isFull) {
      var safeWidth = Math.max(0.01, Math.min(boardWidth, crossSpan));
      var count;
      var pieceCross;
      var positions = [];

      if (isFull) {
        count = Math.max(1, Math.min(80, Math.ceil(crossSpan / safeWidth)));
        pieceCross = crossSpan / count;
        for (var i = 0; i < count; i += 1) {
          positions.push(-crossSpan / 2 + pieceCross / 2 + i * pieceCross);
        }
      } else {
        count = Math.max(1, Math.min(80, Math.floor((crossSpan + gap) / (safeWidth + gap))));
        pieceCross = Math.min(safeWidth, crossSpan / count);
        var remaining = Math.max(0, crossSpan - count * pieceCross);
        var actualGap = remaining / (count + 1);
        for (var j = 0; j < count; j += 1) {
          positions.push(-crossSpan / 2 + actualGap + pieceCross / 2 + j * (pieceCross + actualGap));
        }
      }
      return { count: count, pieceCross: pieceCross, positions: positions };
    }

    function addCoverForFace(face, state, L, W, H, inputs) {
      if (!state.enabled) return { pieces: 0, area: 0, length: 0, summary: face.name + ': nonaktif' };

      var boardWidth = Math.max(0.04, inputs.boardWidth / 1000);
      var boardThickness = Math.max(0.005, inputs.boardThickness / 1000);
      var halfGap = Math.max(0.005, inputs.halfGap / 1000);
      var plywoodThickness = Math.max(0.003, inputs.plywoodThickness / 1000);
      var isPlywood = state.type === 'plywood';
      var isFull = state.type === 'full';
      var materialType = isPlywood ? 'plywood' : 'coverBoard';
      var pieces = 0;
      var area = 0;
      var length = 0;
      var label = state.type === 'half' ? 'Papan Setengah' : (state.type === 'full' ? 'Papan Full' : 'Triplex');

      if (isPlywood) {
        if (face.id === 'front' || face.id === 'back') {
          var pz = face.id === 'front' ? W / 2 + plywoodThickness / 2 : -W / 2 - plywoodThickness / 2;
          addBox(L, H, plywoodThickness, 0, H / 2, pz, materialType, face.name + ' triplex');
          area = L * H;
        } else if (face.id === 'right' || face.id === 'left') {
          var px = face.id === 'right' ? L / 2 + plywoodThickness / 2 : -L / 2 - plywoodThickness / 2;
          addBox(plywoodThickness, H, W, px, H / 2, 0, materialType, face.name + ' triplex');
          area = W * H;
        } else {
          var py = face.id === 'top' ? H + plywoodThickness / 2 : -plywoodThickness / 2;
          addBox(L, plywoodThickness, W, 0, py, 0, materialType, face.name + ' triplex');
          area = L * W;
        }
        pieces = 1;
        return { pieces: pieces, area: area, length: 0, summary: face.name + ': ' + label };
      }

      var crossSpan;
      var longSpan;
      if (face.id === 'front' || face.id === 'back') {
        crossSpan = state.orientation === 'H' ? H : L;
        longSpan = state.orientation === 'H' ? L : H;
      } else if (face.id === 'right' || face.id === 'left') {
        crossSpan = state.orientation === 'H' ? H : W;
        longSpan = state.orientation === 'H' ? W : H;
      } else {
        crossSpan = state.orientation === 'H' ? W : L;
        longSpan = state.orientation === 'H' ? L : W;
      }

      var layout = makeBoardLayout(crossSpan, boardWidth, halfGap, isFull);
      pieces = layout.count;
      area = pieces * layout.pieceCross * longSpan;
      length = pieces * longSpan;

      layout.positions.forEach(function (pos) {
        if (face.id === 'front' || face.id === 'back') {
          var z = face.id === 'front' ? W / 2 + boardThickness / 2 : -W / 2 - boardThickness / 2;
          if (state.orientation === 'H') addBox(L, layout.pieceCross, boardThickness, 0, H / 2 + pos, z, materialType, face.name + ' papan horizontal');
          else addBox(layout.pieceCross, H, boardThickness, pos, H / 2, z, materialType, face.name + ' papan vertikal');
        } else if (face.id === 'right' || face.id === 'left') {
          var x = face.id === 'right' ? L / 2 + boardThickness / 2 : -L / 2 - boardThickness / 2;
          if (state.orientation === 'H') addBox(boardThickness, layout.pieceCross, W, x, H / 2 + pos, 0, materialType, face.name + ' papan horizontal');
          else addBox(boardThickness, H, layout.pieceCross, x, H / 2, pos, materialType, face.name + ' papan vertikal');
        } else {
          var y = face.id === 'top' ? H + boardThickness / 2 : -boardThickness / 2;
          if (state.orientation === 'H') addBox(L, boardThickness, layout.pieceCross, 0, y, pos, materialType, face.name + ' papan arah panjang');
          else addBox(layout.pieceCross, boardThickness, W, pos, y, 0, materialType, face.name + ' papan arah lebar');
        }
      });

      return {
        pieces: pieces,
        area: area,
        length: length,
        summary: face.name + ': ' + label + ' ' + state.orientation + ' (' + pieces + ')'
      };
    }

    function buildCrate(resetView) {
      var inputs = readInputs();
      var L = inputs.length / 1000;
      var W = inputs.width / 1000;
      var H = inputs.height / 1000;
      var F = Math.min(inputs.frameSize / 1000, L * 0.22, W * 0.22, H * 0.22);
      var S = Math.min(inputs.supportSize / 1000, L * 0.20, W * 0.20, H * 0.20);
      var halfF = F / 2;
      var halfS = S / 2;

      currentMaxDimension = Math.max(L, W, H);
      boxes = [];

      [-W / 2 + halfF, W / 2 - halfF].forEach(function (z) {
        [halfF, H - halfF].forEach(function (y) {
          addBox(L, F, F, 0, y, z, 'frame', 'Rangka panjang');
        });
      });

      [-L / 2 + halfF, L / 2 - halfF].forEach(function (x) {
        [halfF, H - halfF].forEach(function (y) {
          addBox(F, F, W, x, y, 0, 'frame', 'Rangka lebar');
        });
      });

      [-L / 2 + halfF, L / 2 - halfF].forEach(function (x) {
        [-W / 2 + halfF, W / 2 - halfF].forEach(function (z) {
          addBox(F, H, F, x, H / 2, z, 'frame', 'Tiang sudut');
        });
      });

      var totalSupports = 0;
      var supportLengthMeters = 0;
      var activeFaces = 0;
      var summaries = [];

      FACE_CONFIG.forEach(function (face) {
        var state = readFace(face);
        if (!state.enabled) {
          summaries.push(face.name + ': nonaktif');
          return;
        }
        activeFaces += 1;
        var crossSpanMm;
        var supportLength;
        var positions;
        var count;

        if (face.id === 'front' || face.id === 'back') {
          var z = face.id === 'front' ? W / 2 - halfS : -W / 2 + halfS;
          if (state.orientation === 'H') {
            crossSpanMm = inputs.height;
            count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
            positions = evenlySpaced(count, F + halfS, H - F - halfS);
            positions.forEach(function (y) { addBox(Math.max(S, L - 2 * F), S, S, 0, y, z, 'support', face.name + ' horizontal'); });
            supportLength = Math.max(S, L - 2 * F);
          } else {
            crossSpanMm = inputs.length;
            count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
            positions = evenlySpaced(count, -L / 2 + F + halfS, L / 2 - F - halfS);
            positions.forEach(function (x) { addBox(S, Math.max(S, H - 2 * F), S, x, H / 2, z, 'support', face.name + ' vertikal'); });
            supportLength = Math.max(S, H - 2 * F);
          }
        } else if (face.id === 'right' || face.id === 'left') {
          var x = face.id === 'right' ? L / 2 - halfS : -L / 2 + halfS;
          if (state.orientation === 'H') {
            crossSpanMm = inputs.height;
            count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
            positions = evenlySpaced(count, F + halfS, H - F - halfS);
            positions.forEach(function (y) { addBox(S, S, Math.max(S, W - 2 * F), x, y, 0, 'support', face.name + ' horizontal'); });
            supportLength = Math.max(S, W - 2 * F);
          } else {
            crossSpanMm = inputs.width;
            count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
            positions = evenlySpaced(count, -W / 2 + F + halfS, W / 2 - F - halfS);
            positions.forEach(function (z) { addBox(S, Math.max(S, H - 2 * F), S, x, H / 2, z, 'support', face.name + ' vertikal'); });
            supportLength = Math.max(S, H - 2 * F);
          }
        } else {
          var y = face.id === 'top' ? H - halfS : halfS;
          if (state.orientation === 'H') {
            crossSpanMm = inputs.width;
            count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
            positions = evenlySpaced(count, -W / 2 + F + halfS, W / 2 - F - halfS);
            positions.forEach(function (z) { addBox(Math.max(S, L - 2 * F), S, S, 0, y, z, 'support', face.name + ' arah panjang'); });
            supportLength = Math.max(S, L - 2 * F);
          } else {
            crossSpanMm = inputs.length;
            count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
            positions = evenlySpaced(count, -L / 2 + F + halfS, L / 2 - F - halfS);
            positions.forEach(function (x) { addBox(S, S, Math.max(S, W - 2 * F), x, y, 0, 'support', face.name + ' arah lebar'); });
            supportLength = Math.max(S, W - 2 * F);
          }
        }

        totalSupports += count;
        supportLengthMeters += count * supportLength;
        summaries.push(face.name + ': ' + count + ' ' + (state.orientation === 'H' ? 'horizontal' : 'vertikal'));
      });

      var totalCoverPieces = 0;
      var totalCoverArea = 0;
      var coverLengthMeters = 0;
      var activeCovers = 0;
      var coverSummaries = [];

      FACE_CONFIG.forEach(function (face) {
        var coverState = readCover(face);
        if (coverState.enabled) activeCovers += 1;
        var coverResult = addCoverForFace(face, coverState, L, W, H, inputs);
        totalCoverPieces += coverResult.pieces;
        totalCoverArea += coverResult.area;
        coverLengthMeters += coverResult.length;
        coverSummaries.push(coverResult.summary);
      });

      var mainLengthMeters = (4 * L) + (4 * W) + (4 * H);
      if (totalSupportsEl) totalSupportsEl.textContent = String(totalSupports);
      if (totalCoverPiecesEl) totalCoverPiecesEl.textContent = String(totalCoverPieces);
      if (coverAreaEl) coverAreaEl.textContent = totalCoverArea.toFixed(2);
      if (totalBeamsEl) totalBeamsEl.textContent = String(12 + totalSupports + totalCoverPieces);
      if (totalLengthEl) totalLengthEl.textContent = (mainLengthMeters + supportLengthMeters + coverLengthMeters).toFixed(2);
      if (faceSummaryEl) faceSummaryEl.innerHTML = '<strong>Penyangga</strong><br>' + summaries.join('<br>') + '<br><br><strong>Penutup</strong><br>' + coverSummaries.join('<br>');
      if (modeBadgeEl) modeBadgeEl.textContent = activeFaces + ' penyangga • ' + activeCovers + ' penutup';
      if (statusText) statusText.textContent = 'Mode offline • ' + Math.round(inputs.length) + ' × ' + Math.round(inputs.width) + ' × ' + Math.round(inputs.height) + ' mm';

      if (resetView) {
        yaw = -0.78;
        pitch = 0.48;
        zoom = 1;
      }
      draw();
    }

    function rotatePoint(p) {
      var cy = Math.cos(yaw), sy = Math.sin(yaw);
      var cp = Math.cos(pitch), sp = Math.sin(pitch);
      var x1 = p.x * cy - p.z * sy;
      var z1 = p.x * sy + p.z * cy;
      var y1 = p.y;
      return {
        x: x1,
        y: y1 * cp - z1 * sp,
        z: y1 * sp + z1 * cp
      };
    }

    function project(p, w, h, scale) {
      var r = rotatePoint(p);
      return {
        x: w * 0.5 + r.x * scale,
        y: h * 0.57 - r.y * scale,
        depth: r.z
      };
    }

    function boxCorners(b) {
      var hx = b.sx / 2, hy = b.sy / 2, hz = b.sz / 2;
      return [
        { x: b.x - hx, y: b.y - hy, z: b.z - hz },
        { x: b.x + hx, y: b.y - hy, z: b.z - hz },
        { x: b.x + hx, y: b.y + hy, z: b.z - hz },
        { x: b.x - hx, y: b.y + hy, z: b.z - hz },
        { x: b.x - hx, y: b.y - hy, z: b.z + hz },
        { x: b.x + hx, y: b.y - hy, z: b.z + hz },
        { x: b.x + hx, y: b.y + hy, z: b.z + hz },
        { x: b.x - hx, y: b.y + hy, z: b.z + hz }
      ];
    }

    var FACE_INDICES = [
      [0, 1, 2, 3], [4, 7, 6, 5], [0, 4, 5, 1],
      [3, 2, 6, 7], [1, 5, 6, 2], [0, 3, 7, 4]
    ];

    function shade(base, amount) {
      function clamp(v) { return Math.max(0, Math.min(255, v)); }
      var r = parseInt(base.slice(1, 3), 16);
      var g = parseInt(base.slice(3, 5), 16);
      var b = parseInt(base.slice(5, 7), 16);
      return 'rgb(' + clamp(r + amount) + ',' + clamp(g + amount) + ',' + clamp(b + amount) + ')';
    }

    function drawGrid(w, h, scale) {
      if (!gridVisible) return;
      ctx.save();
      ctx.lineWidth = 1;
      var size = Math.max(currentMaxDimension * 1.7, 3);
      var step = Math.max(0.25, Math.pow(2, Math.floor(Math.log2(currentMaxDimension / 5))));
      for (var i = -Math.ceil(size / step); i <= Math.ceil(size / step); i += 1) {
        var a = project({ x: -size, y: 0, z: i * step }, w, h, scale);
        var b = project({ x: size, y: 0, z: i * step }, w, h, scale);
        ctx.strokeStyle = i === 0 ? 'rgba(72,99,132,.34)' : 'rgba(120,142,168,.18)';
        ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
        a = project({ x: i * step, y: 0, z: -size }, w, h, scale);
        b = project({ x: i * step, y: 0, z: size }, w, h, scale);
        ctx.strokeStyle = i === 0 ? 'rgba(72,99,132,.34)' : 'rgba(120,142,168,.18)';
        ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
      }
      ctx.restore();
    }

    function drawDimensions(w, h) {
      if (!dimensionsVisible) return;
      var inputs = readInputs();
      var labels = [
        'Panjang ' + Math.round(inputs.length) + ' mm',
        'Lebar ' + Math.round(inputs.width) + ' mm',
        'Tinggi ' + Math.round(inputs.height) + ' mm'
      ];
      ctx.save();
      ctx.font = '700 12px Arial';
      ctx.textBaseline = 'middle';
      var x = 18, y = h - 94;
      labels.forEach(function (label, index) {
        var tw = ctx.measureText(label).width + 22;
        ctx.fillStyle = 'rgba(255,255,255,.92)';
        ctx.strokeStyle = 'rgba(31,103,199,.35)';
        ctx.lineWidth = 1;
        roundRect(ctx, x, y + index * 26, tw, 21, 7);
        ctx.fill(); ctx.stroke();
        ctx.fillStyle = '#163d69';
        ctx.fillText(label, x + 11, y + index * 26 + 10.5);
      });
      ctx.restore();
    }

    function roundRect(context, x, y, width, height, radius) {
      var r = Math.min(radius, width / 2, height / 2);
      context.beginPath();
      context.moveTo(x + r, y);
      context.arcTo(x + width, y, x + width, y + height, r);
      context.arcTo(x + width, y + height, x, y + height, r);
      context.arcTo(x, y + height, x, y, r);
      context.arcTo(x, y, x + width, y, r);
      context.closePath();
    }

    function draw() {
      var rect = wrap.getBoundingClientRect();
      var dpr = Math.min(window.devicePixelRatio || 1, 2);
      var cssW = Math.max(1, Math.round(rect.width));
      var cssH = Math.max(1, Math.round(rect.height));
      if (canvas.width !== Math.round(cssW * dpr) || canvas.height !== Math.round(cssH * dpr)) {
        canvas.width = Math.round(cssW * dpr);
        canvas.height = Math.round(cssH * dpr);
      }
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      ctx.clearRect(0, 0, cssW, cssH);

      var bg = ctx.createRadialGradient(cssW * 0.5, cssH * 0.38, 20, cssW * 0.5, cssH * 0.42, Math.max(cssW, cssH) * 0.75);
      bg.addColorStop(0, '#ffffff');
      bg.addColorStop(0.56, '#f5f8fc');
      bg.addColorStop(1, '#e6edf6');
      ctx.fillStyle = bg;
      ctx.fillRect(0, 0, cssW, cssH);

      var scale = Math.min(cssW, cssH) / (currentMaxDimension * 3.05) * zoom;
      drawGrid(cssW, cssH, scale);

      var faces = [];
      boxes.forEach(function (box, boxIndex) {
        var corners = boxCorners(box);
        var rotated = corners.map(rotatePoint);
        var projected = corners.map(function (p) { return project(p, cssW, cssH, scale); });
        FACE_INDICES.forEach(function (indices, faceIndex) {
          var points = indices.map(function (idx) { return projected[idx]; });
          var depth = indices.reduce(function (sum, idx) { return sum + rotated[idx].z; }, 0) / 4;
          faces.push({ box: box, points: points, depth: depth, faceIndex: faceIndex, boxIndex: boxIndex });
        });
      });

      faces.sort(function (a, b) { return a.depth - b.depth; });
      faces.forEach(function (face) {
        var p = face.points;
        var cross = (p[1].x - p[0].x) * (p[2].y - p[0].y) - (p[1].y - p[0].y) * (p[2].x - p[0].x);
        if (Math.abs(cross) < 0.02) return;
        var baseMap = { frame: '#c98f4d', support: '#ddb67e', coverBoard: '#b9793e', plywood: '#d5ae79' };
        var base = baseMap[face.box.type] || '#c98f4d';
        var light = [4, 17, -17, 27, -7, 10][face.faceIndex];
        ctx.beginPath();
        ctx.moveTo(p[0].x, p[0].y);
        for (var i = 1; i < p.length; i += 1) ctx.lineTo(p[i].x, p[i].y);
        ctx.closePath();
        ctx.fillStyle = shade(base, light);
        ctx.fill();
        ctx.strokeStyle = 'rgba(91,55,23,.46)';
        ctx.lineWidth = 0.8;
        ctx.stroke();

        if (face.box.type !== 'plywood' && (face.faceIndex === 3 || face.faceIndex === 1)) {
          ctx.save();
          ctx.clip();
          ctx.strokeStyle = 'rgba(93,55,22,.12)';
          ctx.lineWidth = 0.65;
          var minX = Math.min.apply(null, p.map(function (pt) { return pt.x; }));
          var maxX = Math.max.apply(null, p.map(function (pt) { return pt.x; }));
          var minY = Math.min.apply(null, p.map(function (pt) { return pt.y; }));
          var maxY = Math.max.apply(null, p.map(function (pt) { return pt.y; }));
          for (var gx = minX - 30; gx < maxX + 30; gx += 18) {
            ctx.beginPath();
            ctx.moveTo(gx, minY - 20);
            ctx.bezierCurveTo(gx + 8, minY + (maxY - minY) * 0.3, gx - 7, minY + (maxY - minY) * 0.7, gx + 4, maxY + 20);
            ctx.stroke();
          }
          ctx.restore();
        }
      });

      drawDimensions(cssW, cssH);
    }

    function setView(view) {
      if (view === 'front') { yaw = 0; pitch = 0; }
      else if (view === 'right') { yaw = -Math.PI / 2; pitch = 0; }
      else if (view === 'top') { yaw = 0; pitch = Math.PI / 2 - 0.02; }
      else { yaw = -0.78; pitch = 0.48; }
      zoom = 1;
      draw();
    }

    function resetInputs() {
      Object.keys(DEFAULTS).forEach(function (key) {
        var el = document.getElementById(key);
        if (el) el.value = DEFAULTS[key];
      });
      FACE_CONFIG.forEach(function (face) {
        var enabled = document.getElementById(face.id + 'Enabled');
        var orientation = document.getElementById(face.id + 'Orientation');
        var count = document.getElementById(face.id + 'Count');
        var coverEnabled = document.getElementById(face.id + 'CoverEnabled');
        var coverType = document.getElementById(face.id + 'CoverType');
        var coverOrientation = document.getElementById(face.id + 'CoverOrientation');
        if (enabled) enabled.checked = true;
        if (orientation) orientation.value = 'H';
        if (count) count.value = 'auto';
        if (coverEnabled) coverEnabled.checked = true;
        if (coverType) coverType.value = 'half';
        if (coverOrientation) coverOrientation.value = 'H';
      });
      buildCrate(true);
    }

    function downloadScreenshot() {
      draw();
      var link = document.createElement('a');
      link.download = 'packaging-kayu-offline-' + Date.now() + '.png';
      link.href = canvas.toDataURL('image/png');
      link.click();
    }

    canvas.addEventListener('pointerdown', function (event) {
      dragging = true;
      lastX = event.clientX;
      lastY = event.clientY;
      canvas.setPointerCapture(event.pointerId);
      canvas.style.cursor = 'grabbing';
    });
    canvas.addEventListener('pointermove', function (event) {
      if (!dragging) return;
      var dx = event.clientX - lastX;
      var dy = event.clientY - lastY;
      lastX = event.clientX;
      lastY = event.clientY;
      yaw += dx * 0.008;
      pitch = Math.max(-1.25, Math.min(1.45, pitch + dy * 0.006));
      draw();
    });
    canvas.addEventListener('pointerup', function (event) {
      dragging = false;
      canvas.releasePointerCapture(event.pointerId);
      canvas.style.cursor = 'grab';
    });
    canvas.addEventListener('pointercancel', function () {
      dragging = false;
      canvas.style.cursor = 'grab';
    });
    canvas.addEventListener('wheel', function (event) {
      event.preventDefault();
      zoom *= event.deltaY > 0 ? 0.9 : 1.1;
      zoom = Math.max(0.45, Math.min(3.5, zoom));
      draw();
    }, { passive: false });

    var applyBtn = document.getElementById('applyBtn');
    var resetBtn = document.getElementById('resetBtn');
    var shotBtn = document.getElementById('shotBtn');
    var dimensionBtn = document.getElementById('dimensionBtn');
    var gridBtn = document.getElementById('gridBtn');
    if (applyBtn) applyBtn.addEventListener('click', function () { buildCrate(false); });
    if (resetBtn) resetBtn.addEventListener('click', resetInputs);
    if (shotBtn) shotBtn.addEventListener('click', downloadScreenshot);
    if (dimensionBtn) dimensionBtn.addEventListener('click', function (event) {
      dimensionsVisible = !dimensionsVisible;
      event.currentTarget.classList.toggle('active', dimensionsVisible);
      draw();
    });
    if (gridBtn) gridBtn.addEventListener('click', function (event) {
      gridVisible = !gridVisible;
      event.currentTarget.classList.toggle('active', gridVisible);
      draw();
    });

    document.querySelectorAll('[data-view]').forEach(function (button) {
      button.addEventListener('click', function () {
        document.querySelectorAll('[data-view]').forEach(function (btn) { btn.classList.remove('active'); });
        button.classList.add('active');
        setView(button.dataset.view);
      });
    });

    var debounceTimer;
    document.querySelectorAll('input, select').forEach(function (element) {
      element.addEventListener('change', function () { buildCrate(false); });
      if (element.type === 'number') {
        element.addEventListener('input', function () {
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(function () { buildCrate(false); }, 180);
        });
      }
    });

    if ('ResizeObserver' in window) {
      var observer = new ResizeObserver(draw);
      observer.observe(wrap);
    }
    window.addEventListener('resize', draw);

    buildCrate(true);
  };
}());
