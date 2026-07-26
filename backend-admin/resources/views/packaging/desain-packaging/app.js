(function () {
  'use strict';

  const errorOverlay = document.getElementById('errorOverlay');
  if (!window.THREE || !THREE.OrbitControls) {
    if (typeof window.startOfflineFallback === 'function') {
      window.startOfflineFallback();
    } else if (errorOverlay) {
      errorOverlay.style.display = 'grid';
    }
    return;
  }

  const FACE_CONFIG = [
    { id: 'front', name: 'Depan' },
    { id: 'back', name: 'Belakang' },
    { id: 'right', name: 'Kanan' },
    { id: 'left', name: 'Kiri' },
    { id: 'top', name: 'Atas' },
    { id: 'bottom', name: 'Bawah' }
  ];

  const DEFAULTS = {
    length: 2000,
    width: 1000,
    height: 1200,
    frameSize: 80,
    supportSize: 60,
    maxSpacing: 500
  };

  const wrap = document.getElementById('canvasWrap');
  const faceGrid = document.getElementById('faceGrid');
  const totalSupportsEl = document.getElementById('totalSupports');
  const totalBeamsEl = document.getElementById('totalBeams');
  const totalLengthEl = document.getElementById('totalLength');
  const faceSummaryEl = document.getElementById('faceSummary');
  const modeBadgeEl = document.getElementById('modeBadge');
  const statusText = document.getElementById('statusText');

  FACE_CONFIG.forEach(function (face) {
    const card = document.createElement('div');
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

  const scene = new THREE.Scene();
  scene.background = new THREE.Color(0xf3f7fc);

  const camera = new THREE.PerspectiveCamera(42, 1, 0.1, 1000);
  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false, preserveDrawingBuffer: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  renderer.shadowMap.enabled = true;
  renderer.shadowMap.type = THREE.PCFSoftShadowMap;
  renderer.outputEncoding = THREE.sRGBEncoding;
  wrap.appendChild(renderer.domElement);

  const controls = new THREE.OrbitControls(camera, renderer.domElement);
  controls.enableDamping = true;
  controls.dampingFactor = 0.08;
  controls.screenSpacePanning = true;
  controls.minDistance = 2;
  controls.maxDistance = 60;

  scene.add(new THREE.HemisphereLight(0xffffff, 0x637184, 1.25));

  const keyLight = new THREE.DirectionalLight(0xffffff, 1.35);
  keyLight.position.set(6, 9, 7);
  keyLight.castShadow = true;
  keyLight.shadow.mapSize.width = 2048;
  keyLight.shadow.mapSize.height = 2048;
  scene.add(keyLight);

  const fillLight = new THREE.DirectionalLight(0xbfd8ff, 0.55);
  fillLight.position.set(-7, 4, -5);
  scene.add(fillLight);

  const modelGroup = new THREE.Group();
  const dimensionGroup = new THREE.Group();
  scene.add(modelGroup);
  scene.add(dimensionGroup);

  const groundMaterial = new THREE.MeshStandardMaterial({ color: 0xe8eef6, roughness: 0.94, metalness: 0 });
  const ground = new THREE.Mesh(new THREE.PlaneGeometry(70, 70), groundMaterial);
  ground.rotation.x = -Math.PI / 2;
  ground.position.y = -0.02;
  ground.receiveShadow = true;
  scene.add(ground);

  const grid = new THREE.GridHelper(40, 40, 0xaab7c8, 0xd5dde8);
  grid.position.y = 0.002;
  grid.material.transparent = true;
  grid.material.opacity = 0.45;
  scene.add(grid);

  let dimensionsVisible = true;
  let currentView = 'iso';
  let currentMaxDimension = 2;

  function makeWoodTexture() {
    const canvas = document.createElement('canvas');
    canvas.width = 512;
    canvas.height = 128;
    const ctx = canvas.getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
    gradient.addColorStop(0, '#c99657');
    gradient.addColorStop(0.5, '#d9ad70');
    gradient.addColorStop(1, '#b77c3e');
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    let seed = 8128;
    function random() {
      seed = (seed * 9301 + 49297) % 233280;
      return seed / 233280;
    }

    for (let y = 8; y < canvas.height; y += 12) {
      ctx.beginPath();
      for (let x = 0; x <= canvas.width; x += 8) {
        const wave = Math.sin((x + y * 3) * 0.035) * 2.2;
        const noise = (random() - 0.5) * 2.1;
        const py = y + wave + noise;
        if (x === 0) ctx.moveTo(x, py); else ctx.lineTo(x, py);
      }
      ctx.strokeStyle = 'rgba(87,47,19,' + (0.12 + random() * 0.10) + ')';
      ctx.lineWidth = 1 + random();
      ctx.stroke();
    }

    for (let i = 0; i < 9; i += 1) {
      const x = random() * canvas.width;
      const y = random() * canvas.height;
      const r = 3 + random() * 8;
      ctx.beginPath();
      ctx.ellipse(x, y, r * 2, r, random() * Math.PI, 0, Math.PI * 2);
      ctx.strokeStyle = 'rgba(92,50,20,.24)';
      ctx.lineWidth = 1.4;
      ctx.stroke();
    }

    const texture = new THREE.CanvasTexture(canvas);
    texture.wrapS = THREE.RepeatWrapping;
    texture.wrapT = THREE.RepeatWrapping;
    texture.anisotropy = Math.min(renderer.capabilities.getMaxAnisotropy(), 8);
    return texture;
  }

  const woodTexture = makeWoodTexture();
  const frameMaterial = new THREE.MeshStandardMaterial({
    color: 0xffffff,
    map: woodTexture,
    roughness: 0.69,
    metalness: 0.0
  });
  const supportMaterial = frameMaterial.clone();
  supportMaterial.color = new THREE.Color(0xf0d1a4);

  function clearGroup(group) {
    while (group.children.length) {
      const child = group.children.pop();
      if (child.geometry) child.geometry.dispose();
      if (child.material && child.material.dispose && child.material !== frameMaterial && child.material !== supportMaterial) {
        if (child.material.map && child.material.map.dispose) child.material.map.dispose();
        child.material.dispose();
      }
      if (child.children && child.children.length) clearGroup(child);
    }
  }

  function getNumber(id, fallback) {
    const value = Number(document.getElementById(id).value);
    return Number.isFinite(value) && value > 0 ? value : fallback;
  }

  function readInputs() {
    return {
      length: getNumber('length', DEFAULTS.length),
      width: getNumber('width', DEFAULTS.width),
      height: getNumber('height', DEFAULTS.height),
      frameSize: getNumber('frameSize', DEFAULTS.frameSize),
      supportSize: getNumber('supportSize', DEFAULTS.supportSize),
      maxSpacing: getNumber('maxSpacing', DEFAULTS.maxSpacing)
    };
  }

  function readFace(face) {
    return {
      enabled: document.getElementById(face.id + 'Enabled').checked,
      orientation: document.getElementById(face.id + 'Orientation').value,
      countMode: document.getElementById(face.id + 'Count').value
    };
  }

  function autoCount(spanMm, spacingMm) {
    return Math.max(0, Math.ceil(spanMm / spacingMm) - 1);
  }

  function resolvedCount(faceState, crossSpanMm, spacingMm) {
    return faceState.countMode === 'auto' ? autoCount(crossSpanMm, spacingMm) : Number(faceState.countMode);
  }

  function evenlySpaced(count, min, max) {
    const positions = [];
    for (let i = 1; i <= count; i += 1) {
      positions.push(min + (max - min) * (i / (count + 1)));
    }
    return positions;
  }

  function addBeam(size, position, material, name) {
    const geometry = new THREE.BoxGeometry(size.x, size.y, size.z);
    const mesh = new THREE.Mesh(geometry, material);
    mesh.position.copy(position);
    mesh.castShadow = true;
    mesh.receiveShadow = true;
    mesh.name = name || 'Balok';
    modelGroup.add(mesh);
    return mesh;
  }

  function makeTextSprite(text) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const fontSize = 30;
    ctx.font = '700 ' + fontSize + 'px Arial';
    const width = Math.ceil(ctx.measureText(text).width + 30);
    canvas.width = width;
    canvas.height = 54;

    const context = canvas.getContext('2d');
    context.fillStyle = 'rgba(255,255,255,.94)';
    context.strokeStyle = 'rgba(31,103,199,.32)';
    context.lineWidth = 2;
    roundRect(context, 1, 1, canvas.width - 2, canvas.height - 2, 12);
    context.fill();
    context.stroke();
    context.font = '700 ' + fontSize + 'px Arial';
    context.fillStyle = '#163d69';
    context.textBaseline = 'middle';
    context.textAlign = 'center';
    context.fillText(text, canvas.width / 2, canvas.height / 2 + 1);

    const texture = new THREE.CanvasTexture(canvas);
    texture.minFilter = THREE.LinearFilter;
    const material = new THREE.SpriteMaterial({ map: texture, depthTest: false, transparent: true });
    const sprite = new THREE.Sprite(material);
    const scale = 0.0045;
    sprite.scale.set(canvas.width * scale, canvas.height * scale, 1);
    sprite.renderOrder = 999;
    return sprite;
  }

  function roundRect(ctx, x, y, w, h, radius) {
    const r = Math.min(radius, w / 2, h / 2);
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.arcTo(x + w, y, x + w, y + h, r);
    ctx.arcTo(x + w, y + h, x, y + h, r);
    ctx.arcTo(x, y + h, x, y, r);
    ctx.arcTo(x, y, x + w, y, r);
    ctx.closePath();
  }

  function addDimensionLine(start, end, label, labelOffset) {
    const material = new THREE.LineBasicMaterial({ color: 0x1f67c7, transparent: true, opacity: 0.88, depthTest: false });
    const points = [start.clone(), end.clone()];
    const geometry = new THREE.BufferGeometry().setFromPoints(points);
    const line = new THREE.Line(geometry, material);
    line.renderOrder = 990;
    dimensionGroup.add(line);

    const direction = end.clone().sub(start).normalize();
    const length = start.distanceTo(end);
    const tickSize = Math.max(0.06, length * 0.022);
    const upCandidate = Math.abs(direction.y) < 0.8 ? new THREE.Vector3(0, 1, 0) : new THREE.Vector3(1, 0, 0);
    const tickDir = new THREE.Vector3().crossVectors(direction, upCandidate).normalize().multiplyScalar(tickSize);

    [start, end].forEach(function (p) {
      const tickGeo = new THREE.BufferGeometry().setFromPoints([p.clone().sub(tickDir), p.clone().add(tickDir)]);
      const tick = new THREE.Line(tickGeo, material.clone());
      tick.renderOrder = 990;
      dimensionGroup.add(tick);
    });

    const sprite = makeTextSprite(label);
    sprite.position.copy(start.clone().add(end).multiplyScalar(0.5).add(labelOffset || new THREE.Vector3()));
    dimensionGroup.add(sprite);
  }

  function addDimensions(L, W, H, F) {
    const offset = F * 1.65 + 0.16;
    addDimensionLine(
      new THREE.Vector3(-L / 2, 0, W / 2 + offset),
      new THREE.Vector3(L / 2, 0, W / 2 + offset),
      'Panjang ' + Math.round(L * 1000) + ' mm',
      new THREE.Vector3(0, 0.15, 0)
    );
    addDimensionLine(
      new THREE.Vector3(L / 2 + offset, 0, -W / 2),
      new THREE.Vector3(L / 2 + offset, 0, W / 2),
      'Lebar ' + Math.round(W * 1000) + ' mm',
      new THREE.Vector3(0, 0.15, 0)
    );
    addDimensionLine(
      new THREE.Vector3(-L / 2 - offset, 0, -W / 2),
      new THREE.Vector3(-L / 2 - offset, H, -W / 2),
      'Tinggi ' + Math.round(H * 1000) + ' mm',
      new THREE.Vector3(-0.12, 0, 0)
    );
  }

  function buildCrate(resetCamera) {
    const inputs = readInputs();
    const L = inputs.length / 1000;
    const W = inputs.width / 1000;
    const H = inputs.height / 1000;
    const F = Math.min(inputs.frameSize / 1000, L * 0.22, W * 0.22, H * 0.22);
    const S = Math.min(inputs.supportSize / 1000, L * 0.20, W * 0.20, H * 0.20);
    const halfF = F / 2;
    const halfS = S / 2;

    currentMaxDimension = Math.max(L, W, H);
    clearGroup(modelGroup);
    clearGroup(dimensionGroup);

    woodTexture.repeat.set(Math.max(1, L * 2.2), 1);
    woodTexture.needsUpdate = true;

    // 4 balok arah panjang: bawah dan atas, depan dan belakang.
    [-W / 2 + halfF, W / 2 - halfF].forEach(function (z) {
      [halfF, H - halfF].forEach(function (y) {
        addBeam(new THREE.Vector3(L, F, F), new THREE.Vector3(0, y, z), frameMaterial, 'Rangka panjang');
      });
    });

    // 4 balok arah lebar: bawah dan atas, kiri dan kanan.
    [-L / 2 + halfF, L / 2 - halfF].forEach(function (x) {
      [halfF, H - halfF].forEach(function (y) {
        addBeam(new THREE.Vector3(F, F, W), new THREE.Vector3(x, y, 0), frameMaterial, 'Rangka lebar');
      });
    });

    // 4 tiang sudut penuh sehingga sambungan sudut terlihat menyatu.
    [-L / 2 + halfF, L / 2 - halfF].forEach(function (x) {
      [-W / 2 + halfF, W / 2 - halfF].forEach(function (z) {
        addBeam(new THREE.Vector3(F, H, F), new THREE.Vector3(x, H / 2, z), frameMaterial, 'Tiang sudut');
      });
    });

    let totalSupports = 0;
    let supportLengthMeters = 0;
    let activeFaces = 0;
    const summaries = [];

    FACE_CONFIG.forEach(function (face) {
      const state = readFace(face);
      if (!state.enabled) {
        summaries.push(face.name + ': nonaktif');
        return;
      }
      activeFaces += 1;

      let crossSpanMm;
      let supportLength;
      let positions;
      let count;

      if (face.id === 'front' || face.id === 'back') {
        const z = face.id === 'front' ? W / 2 - halfS : -W / 2 + halfS;
        if (state.orientation === 'H') {
          crossSpanMm = inputs.height;
          count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
          positions = evenlySpaced(count, F + halfS, H - F - halfS);
          positions.forEach(function (y) {
            addBeam(new THREE.Vector3(Math.max(S, L - 2 * F), S, S), new THREE.Vector3(0, y, z), supportMaterial, face.name + ' horizontal');
          });
          supportLength = Math.max(S, L - 2 * F);
        } else {
          crossSpanMm = inputs.length;
          count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
          positions = evenlySpaced(count, -L / 2 + F + halfS, L / 2 - F - halfS);
          positions.forEach(function (x) {
            addBeam(new THREE.Vector3(S, Math.max(S, H - 2 * F), S), new THREE.Vector3(x, H / 2, z), supportMaterial, face.name + ' vertikal');
          });
          supportLength = Math.max(S, H - 2 * F);
        }
      } else if (face.id === 'right' || face.id === 'left') {
        const x = face.id === 'right' ? L / 2 - halfS : -L / 2 + halfS;
        if (state.orientation === 'H') {
          crossSpanMm = inputs.height;
          count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
          positions = evenlySpaced(count, F + halfS, H - F - halfS);
          positions.forEach(function (y) {
            addBeam(new THREE.Vector3(S, S, Math.max(S, W - 2 * F)), new THREE.Vector3(x, y, 0), supportMaterial, face.name + ' horizontal');
          });
          supportLength = Math.max(S, W - 2 * F);
        } else {
          crossSpanMm = inputs.width;
          count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
          positions = evenlySpaced(count, -W / 2 + F + halfS, W / 2 - F - halfS);
          positions.forEach(function (z) {
            addBeam(new THREE.Vector3(S, Math.max(S, H - 2 * F), S), new THREE.Vector3(x, H / 2, z), supportMaterial, face.name + ' vertikal');
          });
          supportLength = Math.max(S, H - 2 * F);
        }
      } else {
        const y = face.id === 'top' ? H - halfS : halfS;
        if (state.orientation === 'H') {
          // Atas/Bawah horizontal = balok searah panjang.
          crossSpanMm = inputs.width;
          count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
          positions = evenlySpaced(count, -W / 2 + F + halfS, W / 2 - F - halfS);
          positions.forEach(function (z) {
            addBeam(new THREE.Vector3(Math.max(S, L - 2 * F), S, S), new THREE.Vector3(0, y, z), supportMaterial, face.name + ' arah panjang');
          });
          supportLength = Math.max(S, L - 2 * F);
        } else {
          // Atas/Bawah vertikal = balok searah lebar.
          crossSpanMm = inputs.length;
          count = resolvedCount(state, crossSpanMm, inputs.maxSpacing);
          positions = evenlySpaced(count, -L / 2 + F + halfS, L / 2 - F - halfS);
          positions.forEach(function (x) {
            addBeam(new THREE.Vector3(S, S, Math.max(S, W - 2 * F)), new THREE.Vector3(x, y, 0), supportMaterial, face.name + ' arah lebar');
          });
          supportLength = Math.max(S, W - 2 * F);
        }
      }

      totalSupports += count;
      supportLengthMeters += count * supportLength;
      summaries.push(face.name + ': ' + count + ' ' + (state.orientation === 'H' ? 'horizontal' : 'vertikal'));
    });

    addDimensions(L, W, H, F);
    dimensionGroup.visible = dimensionsVisible;

    const mainLengthMeters = (4 * L) + (4 * W) + (4 * H);
    totalSupportsEl.textContent = String(totalSupports);
    totalBeamsEl.textContent = String(12 + totalSupports);
    totalLengthEl.textContent = (mainLengthMeters + supportLengthMeters).toFixed(2);
    faceSummaryEl.innerHTML = summaries.join('<br>');
    modeBadgeEl.textContent = activeFaces + ' sisi aktif';
    statusText.textContent = 'Render ' + Math.round(inputs.length) + ' × ' + Math.round(inputs.width) + ' × ' + Math.round(inputs.height) + ' mm';

    ground.position.y = -0.025;
    grid.position.y = 0.003;

    if (resetCamera) setCameraView(currentView, true);
  }

  function setCameraView(view, immediate) {
    currentView = view;
    const d = currentMaxDimension * 2.15 + 1.2;
    let targetPosition;

    if (view === 'front') targetPosition = new THREE.Vector3(0, currentMaxDimension * 0.55, d);
    else if (view === 'right') targetPosition = new THREE.Vector3(d, currentMaxDimension * 0.55, 0);
    else if (view === 'top') targetPosition = new THREE.Vector3(0, d, 0.001);
    else targetPosition = new THREE.Vector3(d * 0.86, d * 0.66, d * 0.86);

    controls.target.set(0, currentMaxDimension * 0.38, 0);
    camera.position.copy(targetPosition);
    camera.near = 0.01;
    camera.far = Math.max(100, d * 20);
    camera.updateProjectionMatrix();
    controls.update();

    if (immediate) renderer.render(scene, camera);
  }

  function resetInputs() {
    Object.keys(DEFAULTS).forEach(function (key) {
      document.getElementById(key).value = DEFAULTS[key];
    });
    FACE_CONFIG.forEach(function (face) {
      document.getElementById(face.id + 'Enabled').checked = true;
      document.getElementById(face.id + 'Orientation').value = 'H';
      document.getElementById(face.id + 'Count').value = 'auto';
    });
    buildCrate(true);
  }

  function downloadScreenshot() {
    renderer.render(scene, camera);
    const link = document.createElement('a');
    link.download = 'packaging-kayu-' + Date.now() + '.png';
    link.href = renderer.domElement.toDataURL('image/png');
    link.click();
  }

  function resize() {
    const rect = wrap.getBoundingClientRect();
    renderer.setSize(Math.max(1, rect.width), Math.max(1, rect.height), false);
    camera.aspect = rect.width / Math.max(rect.height, 1);
    camera.updateProjectionMatrix();
  }

  function animate() {
    requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
  }

  document.getElementById('applyBtn').addEventListener('click', function () { buildCrate(false); });
  document.getElementById('resetBtn').addEventListener('click', resetInputs);
  document.getElementById('shotBtn').addEventListener('click', downloadScreenshot);

  document.getElementById('dimensionBtn').addEventListener('click', function (event) {
    dimensionsVisible = !dimensionsVisible;
    dimensionGroup.visible = dimensionsVisible;
    event.currentTarget.classList.toggle('active', dimensionsVisible);
  });

  document.getElementById('gridBtn').addEventListener('click', function (event) {
    grid.visible = !grid.visible;
    event.currentTarget.classList.toggle('active', grid.visible);
  });

  document.querySelectorAll('[data-view]').forEach(function (button) {
    button.addEventListener('click', function () {
      document.querySelectorAll('[data-view]').forEach(function (btn) { btn.classList.remove('active'); });
      button.classList.add('active');
      setCameraView(button.dataset.view, true);
    });
  });

  let debounceTimer;
  document.querySelectorAll('input, select').forEach(function (element) {
    element.addEventListener('change', function () { buildCrate(false); });
    if (element.type === 'number') {
      element.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () { buildCrate(false); }, 220);
      });
    }
  });

  if ('ResizeObserver' in window) {
    const observer = new ResizeObserver(resize);
    observer.observe(wrap);
  }
  window.addEventListener('resize', resize);

  resize();
  buildCrate(true);
  animate();
}());
