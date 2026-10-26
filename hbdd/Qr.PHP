<?php
// ---- เดา URL ของหน้าเว็บให้อัตโนมัติ (แก้ในช่องกรอกได้) ----
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$name = explode(':', $host)[0];
if (in_array($name, ['localhost', '127.0.0.1', '::1', '[::1]'], true)) {
    $ip = gethostbyname(gethostname());           // IP ในวง Wi-Fi เดียวกัน เช่น 192.168.1.45
    if ($ip && $ip !== gethostname()) $host = $ip;
}
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$defaultUrl = $scheme . '://' . $host . $dir . '/index.php';
?><!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>QR Code วันเกิดอุ้ม 💖</title>
<style>
  *{box-sizing:border-box}
  body{margin:0;min-height:100vh;display:flex;flex-direction:column;align-items:center;gap:16px;padding:24px 16px;
    font-family:'Segoe UI',Tahoma,sans-serif;background:linear-gradient(160deg,#ffe3ef,#ffc2dc);color:#7a2850}
  h1{margin:0;font-size:22px}
  .row{display:flex;gap:8px;width:min(560px,100%);flex-wrap:wrap}
  input{flex:1;min-width:220px;padding:12px 14px;border:2px solid #ff8fc0;border-radius:12px;font-size:15px}
  button{border:0;border-radius:12px;padding:12px 18px;font-size:15px;font-weight:600;color:#fff;cursor:pointer;
    background:linear-gradient(135deg,#ff7eb6,#ff4f9a)}
  button.alt{background:#fff;color:#d2457f;border:2px solid #ff8fc0}
  canvas{width:min(460px,92vw);height:auto;border-radius:24px;box-shadow:0 14px 34px rgba(200,40,110,.35);background:#fff}
  small{max-width:560px;text-align:center;line-height:1.6;opacity:.85}
</style>
</head>
<body>
<h1>💖 QR Code สำหรับสแกนดูการ์ดวันเกิด</h1>
<div class="row">
  <input id="url" value="<?= htmlspecialchars($defaultUrl, ENT_QUOTES, 'UTF-8') ?>" placeholder="ใส่ลิงก์เว็บที่นี่">
  <button id="gen">สร้าง QR</button>
</div>
<canvas id="cv" width="900" height="900"></canvas>
<div class="row" style="justify-content:center">
  <button id="style" class="alt">สลับสไตล์: หัวใจ / จุดกลม</button>
  <button id="dl">⬇️ ดาวน์โหลดรูป</button>
</div>
<small>
  เปิดหน้านี้ผ่าน IP ของเครื่อง (เช่น http://192.168.x.x/birthday/qr.php) แล้วสแกนด้วยมือถือที่ต่อ Wi-Fi เดียวกัน<br>
  ถ้าจะให้แฟนสแกนจากที่อื่น ต้องใช้ลิงก์ออนไลน์ (โฮสต์จริงหรือ ngrok) แล้ววางลงในช่องด้านบน
</small>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
<script>
const cv = document.getElementById('cv'), ctx = cv.getContext('2d');
let heartStyle = true;

function heartPath(c, cx, cy, s) {            // วาดหัวใจกว้าง s จุดกึ่งกลาง (cx,cy)
  const x = cx, y = cy - s * 0.45;
  c.beginPath();
  c.moveTo(x, y + s * 0.3);
  c.bezierCurveTo(x, y, x - s / 2, y, x - s / 2, y + s * 0.3);
  c.bezierCurveTo(x - s / 2, y + s * 0.6, x, y + s * 0.75, x, y + s * 0.95);
  c.bezierCurveTo(x, y + s * 0.75, x + s / 2, y + s * 0.6, x + s / 2, y + s * 0.3);
  c.bezierCurveTo(x + s / 2, y, x, y, x, y + s * 0.3);
  c.closePath();
}
function rr(c, x, y, w, h, r) {
  c.beginPath(); c.moveTo(x + r, y);
  c.arcTo(x + w, y, x + w, y + h, r); c.arcTo(x + w, y + h, x, y + h, r);
  c.arcTo(x, y + h, x, y, r); c.arcTo(x, y, x + w, y, r); c.closePath();
}

function draw() {
  const url = document.getElementById('url').value.trim();
  const W = cv.width;
  // พื้นหลังชมพู + หัวใจจางๆ
  const g = ctx.createLinearGradient(0, 0, W, W);
  g.addColorStop(0, '#ffd9ea'); g.addColorStop(1, '#ff9fc9');
  ctx.fillStyle = g; ctx.fillRect(0, 0, W, W);
  ctx.fillStyle = 'rgba(255,255,255,.35)';
  [[120,120,110],[790,160,80],[110,780,90],[800,790,120],[450,70,50],[450,840,50]].forEach(([x,y,s]) => { heartPath(ctx,x,y,s); ctx.fill(); });

  if (!url) return;
  const qr = qrcode(0, 'H'); qr.addData(url); qr.make();
  const n = qr.getModuleCount(), quiet = 2;
  const card = 640, cx = W / 2, cy = W / 2;
  const cell = card / (n + quiet * 2);
  const ox = cx - card / 2, oy = cy - card / 2;

  ctx.save(); ctx.shadowColor = 'rgba(190,40,100,.4)'; ctx.shadowBlur = 40; ctx.shadowOffsetY = 12;
  ctx.fillStyle = '#fff'; rr(ctx, ox, oy, card, card, 44); ctx.fill(); ctx.restore();

  const inFinder = (r, c) => (r < 7 && c < 7) || (r < 7 && c >= n - 7) || (r >= n - 7 && c < 7);
  const pink = '#d6296f';
  ctx.fillStyle = pink;
  const mid = n / 2, skip = Math.round(n * 0.2);      // เว้นช่องกลางไว้วางโลโก้หัวใจ
  for (let r = 0; r < n; r++) for (let c = 0; c < n; c++) {
    if (!qr.isDark(r, c) || inFinder(r, c)) continue;
    if (Math.abs(r - mid + .5) < skip / 2 && Math.abs(c - mid + .5) < skip / 2) continue;
    const x = ox + (c + quiet) * cell, y = oy + (r + quiet) * cell;
    if (heartStyle) { heartPath(ctx, x + cell / 2, y + cell / 2 + cell * 0.02, cell * 1.12); ctx.fill(); }
    else { ctx.beginPath(); ctx.arc(x + cell / 2, y + cell / 2, cell * 0.46, 0, 7); ctx.fill(); }
  }
  // ตาหลัก 3 มุม (ทำให้ชัดเพื่อให้สแกนติดง่าย)
  [[0,0],[0,n-7],[n-7,0]].forEach(([r, c]) => {
    const x = ox + (c + quiet) * cell, y = oy + (r + quiet) * cell;
    ctx.fillStyle = pink; rr(ctx, x, y, cell * 7, cell * 7, cell * 2); ctx.fill();
    ctx.fillStyle = '#fff'; rr(ctx, x + cell, y + cell, cell * 5, cell * 5, cell * 1.3); ctx.fill();
    ctx.fillStyle = pink; heartPath(ctx, x + cell * 3.5, y + cell * 3.55, cell * 3.6); ctx.fill();
  });
  // โลโก้หัวใจตรงกลาง
  ctx.fillStyle = '#fff'; rr(ctx, cx - cell * skip / 2, cy - cell * skip / 2, cell * skip, cell * skip, cell * 2); ctx.fill();
  const lg = ctx.createLinearGradient(cx - 60, cy - 60, cx + 60, cy + 60);
  lg.addColorStop(0, '#ff7eb6'); lg.addColorStop(1, '#d6296f');
  ctx.fillStyle = lg; heartPath(ctx, cx, cy + 4, cell * skip * 0.72); ctx.fill();
}

document.getElementById('gen').onclick = draw;
document.getElementById('url').addEventListener('keydown', e => e.key === 'Enter' && draw());
document.getElementById('style').onclick = () => { heartStyle = !heartStyle; draw(); };
document.getElementById('dl').onclick = () => {
  const a = document.createElement('a'); a.download = 'birthday-qr.png'; a.href = cv.toDataURL('image/png'); a.click();
};
draw();
</script>
</body>
</html>