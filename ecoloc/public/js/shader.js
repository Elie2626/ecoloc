/* ECO'LOC — Hex-sphere WebGL shader
   Palette  : vert forêt  #1a3324 / #4a9e6a / teal
   GLSL ES 1.00 (WebGL 1) — aucune dépendance externe
   Fixes vs v1 :
     - float i,z,t → déclarations séparées (GLSL ES 1.00 strict)
     - boucle float → boucle int (garantie statique)
     - tanh() → implémenté via exp() (absent en GLSL ES 1.00)      */
(function () {
  'use strict';

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  /* ── Vertex shader ─────────────────────────────────── */
  var VERT = [
    'attribute vec2 aPos;',
    'void main(){ gl_Position = vec4(aPos, 0.0, 1.0); }'
  ].join('\n');

  /* ── Fragment shader (GLSL ES 1.00 compliant) ──────── */
  var FRAG = [
    'precision highp float;',
    'uniform vec2  iResolution;',
    'uniform float iTime;',

    /* tanh manuelle via exp() — absent en GLSL ES 1.00 */
    'vec4 eTanh(vec4 x) {',
    '  vec4 e2 = exp(clamp(2.0 * x, -20.0, 20.0));',
    '  return (e2 - 1.0) / (e2 + 1.0);',
    '}',

    'void main(){',
    '  vec2  I = gl_FragCoord.xy;',
    '  vec4  O = vec4(0.0);',
    '  float t = iTime * 0.55;',   /* déclarations séparées obligatoires */
    '  float z = 0.0;',

    /*  Boucle entière (GLSL ES 1.00 exige une borne constante)
        100 itérations = même résultat que le float i+=0.01          */
    '  for(int n = 0; n < 100; n++){',
    '    float fi = float(n) * 0.01;',
    '    vec2 v = iResolution;',
    '    vec2 p = (I + I - v) / v.y * fi;',
    /* distorsion sphère */
    '    p /= 0.2 + sqrt(z = max(1.0 - dot(p, p), 0.0)) * 0.3;',
    /* décalage hexagonal */
    '    p.y += fract(ceil(p.x = p.x / 0.9 + t) * 0.5) + t * 0.2;',
    '    v = abs(fract(p) - 0.5);',
    /*  Palette ECO'LOC : R bas, G haut, B moyen-teal              */
    '    O += vec4(0.8, 5.0, 2.2, 1.0) / 2000.0 * z /',
    '         (abs(max(v.x * 1.5 + v, v + v).y - 1.0) + 0.1 - fi * 0.09);',
    '  }',

    '  O = eTanh(O * O);',

    /* Base vert forêt dans les zones sombres */
    '  vec3 base = vec3(0.07, 0.16, 0.10);',
    '  float lum = dot(O.rgb, vec3(0.299, 0.587, 0.114));',
    '  O.rgb = mix(base, O.rgb, min(1.0, lum * 3.0 + 0.15));',

    '  gl_FragColor = vec4(O.rgb, 1.0);',
    '}'
  ].join('\n');

  /* ── Helpers WebGL ─────────────────────────────────── */
  function compile(gl, src, type) {
    var s = gl.createShader(type);
    gl.shaderSource(s, src);
    gl.compileShader(s);
    if (!gl.getShaderParameter(s, gl.COMPILE_STATUS)) {
      console.warn('[ECO shader]', gl.getShaderInfoLog(s));
      return null;
    }
    return s;
  }

  function buildProgram(gl) {
    var vs = compile(gl, VERT, gl.VERTEX_SHADER);
    var fs = compile(gl, FRAG, gl.FRAGMENT_SHADER);
    if (!vs || !fs) return null;
    var p = gl.createProgram();
    gl.attachShader(p, vs);
    gl.attachShader(p, fs);
    gl.linkProgram(p);
    if (!gl.getProgramParameter(p, gl.LINK_STATUS)) {
      console.warn('[ECO shader link]', gl.getProgramInfoLog(p));
      return null;
    }
    return p;
  }

  /* ── Init d'un canvas ──────────────────────────────── */
  function initCanvas(canvas) {
    var gl = canvas.getContext('webgl') ||
             canvas.getContext('experimental-webgl');
    if (!gl) return;

    var prog = buildProgram(gl);
    if (!prog) return;

    /* Quad plein écran (2 triangles en TRIANGLE_STRIP) */
    var buf = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, buf);
    gl.bufferData(gl.ARRAY_BUFFER,
      new Float32Array([-1, -1,  1, -1,  -1, 1,  1, 1]),
      gl.STATIC_DRAW);

    var aPos = gl.getAttribLocation(prog, 'aPos');
    gl.enableVertexAttribArray(aPos);
    gl.vertexAttribPointer(aPos, 2, gl.FLOAT, false, 0, 0);
    gl.useProgram(prog);

    var uTime = gl.getUniformLocation(prog, 'iTime');
    var uRes  = gl.getUniformLocation(prog, 'iResolution');

    var raf   = null;
    var start = performance.now();

    /* ── Resize ──────────────────────────────────────── */
    function resize() {
      var parent = canvas.parentElement || canvas;
      var w = parent.offsetWidth  || window.innerWidth;
      var h = parent.offsetHeight || window.innerHeight;
      var dpr = Math.min(window.devicePixelRatio || 1, 1.5);
      canvas.width  = Math.round(w * dpr);
      canvas.height = Math.round(h * dpr);
      gl.viewport(0, 0, canvas.width, canvas.height);
    }

    /* ── Boucle de rendu ─────────────────────────────── */
    function draw() {
      var t = (performance.now() - start) / 1000;
      gl.uniform1f(uTime, t);
      gl.uniform2f(uRes, canvas.width, canvas.height);
      gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
      raf = requestAnimationFrame(draw);
    }

    /* ── Pause quand hors champ ──────────────────────── */
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { if (!raf) draw(); }
        else { cancelAnimationFrame(raf); raf = null; }
      });
    }, { threshold: 0.01 });
    io.observe(canvas);

    /* ── Resize observer ─────────────────────────────── */
    var ro = new ResizeObserver(resize);
    ro.observe(canvas.parentElement || document.body);

    resize();
    draw();
  }

  /* ── Démarrage ─────────────────────────────────────── */
  function init() {
    document.querySelectorAll('canvas[data-shader]').forEach(initCanvas);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
