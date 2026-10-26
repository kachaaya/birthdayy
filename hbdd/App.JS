(function () {
  const $ = (id) => document.getElementById(id);

  // ---------- ดาว ----------
  const stars = $('stars');
  for (let i = 0; i < 90; i++) {
    const s = document.createElement('i');
    s.style.left = Math.random() * 100 + '%';
    s.style.top = Math.random() * 100 + '%';
    s.style.animationDelay = Math.random() * 3 + 's';
    s.style.transform = 'scale(' + (0.5 + Math.random()) + ')';
    stars.appendChild(s);
  }

  // ---------- สร้างทรงกระบอก 3D ----------
  function cylinder(parent, o) {
    const n = 40, w = (2 * Math.PI * o.r) / n + 1.5;
    const el = document.createElement('div');
    el.className = 'cyl';
    el.style.top = o.top + 'px';
    el.style.height = o.h + 'px';
    for (let i = 0; i < n; i++) {
      const s = document.createElement('div');
      s.className = 'side';
      s.style.width = w + 'px';
      s.style.height = o.h + 'px';
      s.style.left = -w / 2 + 'px';
      s.style.background = o.side;
      s.style.transform = 'rotateY(' + (i * 360 / n) + 'deg) translateZ(' + o.r + 'px)';
      if (i % 2) s.style.filter = 'brightness(.96)';
      el.appendChild(s);
    }
    const t = document.createElement('div');
    t.className = 'top';
    t.style.width = t.style.height = o.r * 2 + 'px';
    t.style.left = -o.r + 'px';
    t.style.top = o.h / 2 - o.r + 'px';
    t.style.background = o.topBg;
    t.style.transform = 'rotateX(90deg) translateZ(' + o.h / 2 + 'px)';
    el.appendChild(t);
    parent.appendChild(el);
  }

  const cake = $('cake');
  const sprinkle = 'radial-gradient(circle at 20% 70%,#ff5fa2 2px,transparent 3px),radial-gradient(circle at 60% 40%,#ffd36b 2px,transparent 3px),radial-gradient(circle at 85% 75%,#8fd3ff 2px,transparent 3px),';
  // ชั้นล่าง
  cylinder(cake, {
    r: 112, h: 84, top: -2,
    side: 'linear-gradient(#ffc2dc 0 22%,#fff3df 22% 100%)',
    topBg: 'radial-gradient(circle,#ffe0ee,#ffb6d3)'
  });
  // ชั้นบน
  cylinder(cake, {
    r: 76, h: 72, top: -74,
    side: sprinkle + 'linear-gradient(#ffb0d0 0 24%,#fff6e6 24% 100%)',
    topBg: 'radial-gradient(circle,#fff0f6,#ffc4dc)'
  });
  // เทียน
  cylinder(cake, {
    r: 5, h: 48, top: -122,
    side: 'repeating-linear-gradient(45deg,#fff 0 6px,#8fd3ff 6px 12px)',
    topBg: '#fff'
  });

  // ---------- ฉาก ----------
  const scenes = ['sceneCake', 'sceneEnv', 'sceneLetter'];
  function show(id) {
    scenes.forEach((s) => $(s).classList.toggle('active', s === id));
  }

  // ---------- เป่าเทียน ----------
  let blown = false;
  function blow() {
    if (blown) return;
    blown = true;
    $('flame').classList.add('out');
    $('flameWrap').querySelector('.smoke').classList.add('go');
    $('blowBtn').disabled = true;
    $('hint').textContent = 'เทียนดับแล้ว! กดที่ตัวเค้กเพื่อเปิดซองจดหมายนะ';
    $('cakeStage').title = 'กดเพื่อเปิดซองจดหมาย';
    confetti(14);
  }
  $('blowBtn').addEventListener('click', blow);
  $('cakeStage').addEventListener('click', () => {
    if (!blown) { blow(); return; }
    show('sceneEnv');
  });

  // ---------- เปิดซอง ----------
  $('seal').addEventListener('click', () => {
    $('envelope').classList.add('open');
    setTimeout(() => { show('sceneLetter'); confetti(18); }, 1300);
  });

  // ---------- ส่งต่อ / คัดลอกลิงก์ ----------
  function toast() {
    const t = $('toast');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 1800);
  }
  $('shareBtn').addEventListener('click', async () => {
    const url = location.href;
    if (navigator.share) {
      try { await navigator.share({ title: document.title, url }); return; } catch (e) {}
    }
    try { await navigator.clipboard.writeText(url); }
    catch (e) {
      const ta = document.createElement('textarea');
      ta.value = url; document.body.appendChild(ta); ta.select();
      document.execCommand('copy'); ta.remove();
    }
    toast();
  });

  $('replayBtn').addEventListener('click', () => location.reload());

  // ---------- หัวใจลอย ----------
  function confetti(n) {
    const set = ['💖', '🌸', '✨', '💗', '🎀'];
    for (let i = 0; i < n; i++) {
      const h = document.createElement('div');
      h.className = 'heart';
      h.textContent = set[Math.floor(Math.random() * set.length)];
      h.style.left = Math.random() * 100 + 'vw';
      h.style.animationDelay = Math.random() * 1.2 + 's';
      document.body.appendChild(h);
      setTimeout(() => h.remove(), 6500);
    }
  }
})();