<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TrackFit</title>

  <style>
    :root{
      --bg:#000;
      --text:#fff;
      --muted:rgba(255,255,255,.7);
      --border:rgba(255,255,255,.12);
      --font: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    }

    *{ box-sizing:border-box; }
    html, body{ height:100%; }
    body{
      margin:0;
      background:var(--bg);
      color:var(--text);
      font-family:var(--font);
      overflow:hidden; /* splash limpio */
    }

    .hero{
      height:100vh;
      width:100vw;
      display:grid;
      place-items:center;
      position:relative;
      padding:24px;
      background:#000;
      cursor:pointer;
    }

    .center{
      text-align:center;
      display:flex;
      flex-direction:column;
      align-items:center;
      gap:12px;
      user-select:none;
    }

    .logo{
      width:min(300px, 70vw);
      height:auto;
      display:block;

      transform: translateY(-240px);
      opacity:0;
      animation: dropIn .9s cubic-bezier(.2,.9,.2,1) forwards;
    }

    @keyframes dropIn{
      to{ transform: translateY(0); opacity:1; }
    }

    .brand{
      font-size:42px;
      font-weight:800;
      letter-spacing:.5px;
    }

    .sub{
      color:var(--muted);
      font-size:14px;
      letter-spacing:.18em;
      text-transform:uppercase;
    }

    /* typing */
    .type{
      white-space:nowrap;
      overflow:hidden;
      width:0;
      opacity:0;
      display:block;
    }
    .type.typing{
      opacity:1;
      animation: typing var(--dur, 1.2s) steps(var(--steps, 10), end) forwards;
    }
    .type.typing::after{
      content:"";
      display:inline-block;
      width:1px;
      height:1em;
      margin-left:6px;
      background: rgba(255,255,255,.9);
      transform: translateY(2px);
      animation: blink .7s step-end infinite;
    }
    @keyframes typing{
      from{ width:0; }
      to{ width: var(--w, 240px); }
    }
    @keyframes blink{
      50%{ opacity:0; }
    }

    .enter{
      margin-top:14px;
      background:transparent;
      color:var(--text);
      border:1px solid var(--border);
      padding:12px 18px;
      border-radius:999px;
      cursor:pointer;
      font-weight:700;

      opacity:0;
      transform: translateY(8px);
      transition: opacity .35s ease, transform .35s ease;
    }
    .enter.show{ opacity:1; transform: translateY(0); }

    .hint{
      position:absolute;
      bottom:18px;
      left:0; right:0;
      text-align:center;
      color:var(--muted);
      font-size:12px;
      pointer-events:none;
    }
  </style>
</head>

<body>
  <section class="hero" id="hero" data-login-url="/login"
>
    <div class="center">
      <img class="logo" id="logo" src="{{ asset('img/trackfit.png') }}" alt="TrackFit logo">

      <div class="brand type" id="brandType" data-text="TrackFit"></div>
      <div class="sub type" id="subType" data-text="Entrenamientos • Alimentación • Logros"></div>

    </div>

    <div class="hint">Pulsa en cualquier parte</div>
  </section>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const hero = document.getElementById("hero");
      const btn = document.getElementById("enterBtn");
      const loginUrl = hero?.dataset?.loginUrl || "/login";

      // Click en cualquier parte => login
      hero?.addEventListener("click", () => window.location.href = loginUrl);
      btn?.addEventListener("click", (e) => { e.stopPropagation(); window.location.href = loginUrl; });

      // Typing cuando acaba la caída del logo
      const logo = document.getElementById("logo");
      const brand = document.getElementById("brandType");
      const sub = document.getElementById("subType");

      function setupTyping(el, durationMs){
        const text = el?.dataset?.text || "";
        if (!el) return;
        el.textContent = text;

        // medir ancho real en px para que no se corte
        const measurer = document.createElement("span");
        measurer.textContent = text;
        measurer.style.position = "absolute";
        measurer.style.visibility = "hidden";
        measurer.style.whiteSpace = "nowrap";

        const cs = window.getComputedStyle(el);
        measurer.style.font = cs.font;
        measurer.style.letterSpacing = cs.letterSpacing;
        measurer.style.textTransform = cs.textTransform;

        document.body.appendChild(measurer);
        const w = Math.ceil(measurer.getBoundingClientRect().width);
        measurer.remove();

        el.style.setProperty("--w", w + "px");
        el.style.setProperty("--steps", Math.max(8, text.length));
        el.style.setProperty("--dur", (durationMs/1000) + "s");
      }

      setupTyping(brand, 850);
      setupTyping(sub, 1100);

      logo?.addEventListener("animationend", () => {
        brand?.classList.add("typing");
        setTimeout(() => sub?.classList.add("typing"), 950);
        setTimeout(() => btn?.classList.add("show"), 2100);
      }, { once:true });
    });
  </script>
</body>
</html>
