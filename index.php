<?php
require_once __DIR__ . '/includes/db.php';
$pdo = get_db_connection();

// Fetch latest 3 projects (featured first, ordered by newest)
$projectsStmt = $pdo->prepare("SELECT * FROM `projects` WHERE `status` = 'published' ORDER BY `is_featured` DESC, `id` DESC LIMIT 3");
$projectsStmt->execute();
$featuredProjects = $projectsStmt->fetchAll();

// Fetch latest 3 blog posts
$blogsStmt = $pdo->prepare("SELECT * FROM `blogs` WHERE `status` = 'published' ORDER BY `created_at` DESC LIMIT 3");
$blogsStmt->execute();
$latestBlogs = $blogsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" x-data="app()" :class="{'dark':dark}" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Adebisi Covenant - Freelance UI/UX Designer &amp; Full Stack Developer. CEO Harkone Designs.">
  <meta name="keywords" content="freelance developer, UI designer, portfolio, Tailwind CSS, Alpine.js, Harkone, Harkone Designs, Adebisi Covenant">
  <meta name="author" content="Adebisi Covenant">
  <meta name="language" content="English">
  <meta name="revisit-after" content="1 days">
  <meta name="author" content="Adebisi Covenant">
  <meta name="robots" content="index, follow">
  <meta name="geo.region" content="NG" />
  <meta name="geo.placename" content="Lagos" />
  <meta name="geo.position" content="6.5244;3.3792" />
  <meta name="ICBM" content="6.5244, 3.3792" />
  

  <meta property="og:title" content="Adebisi Covenant - Freelance UI/UX Designer">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="en_US"/>
  <meta property="og:url" content="https://harkone.com.ng">
  <meta property="og:description" content="I design and build digital products that people love to use - fast, clean, and accessible.">
  <meta property="og:image" content="https://harkone.com.ng/images/sm.png">

  <meta property="twitter:card" content="summary"/>
  <meta property="twitter:title" content="Adebisi Covenant - Freelance UI/UX Designer"/>
  <meta property="twitter:description" content="I design and build digital products that people love to use - fast, clean, and accessible.">
  <meta property="twitter:url" content="https://harkone.com.ng"/>
  <meta property="twitter:image" content="https://harkone.com.ng/images/sm.png">

  <link rel="shortcut icon" type="image/x-icon" href="https://harkone.com.ng/images/favicon.png">
  <title>Adebisi Covenant - Freelance UI/UX Designer &amp; Full Stack Dev</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap"
    rel="stylesheet">

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: { display: ['PT Sans', 'sans-serif'], body: ['DM Sans', 'sans-serif'] },
          colors: { accent: '#0b94baff', 'accent-light': '#2dd902ff' } // #2dd902ff #0b94baff
        }
      }
    }
  </script>
  <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-234405192-1">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-234405192-1');
</script>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1889394779762877"
     crossorigin="anonymous"></script>
    
    <!-- Amplitude JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/amplitudejs@5.3.2/dist/amplitude.js"></script>
<!-- Meta Pixel Code --><script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js'); fbq('init', '1183796817228500'); fbq('track', 'PageView');</script><noscript> <img height="1" width="1" src="https://www.facebook.com/tr?id=1183796817228500&ev=PageView&noscript=1"/></noscript><!-- End Meta Pixel Code -->

  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box
    }

    html,
    body {
      font-family: 'DM Sans', sans-serif
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      font-family: 'PT Sans', sans-serif
    }

    body {
      transition: background-color .3s, color .3s
    }

    /* noise overlay */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: 0;
      opacity: .35;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.05'/%3E%3C/svg%3E")
    }

    /* scrollbar */
    ::-webkit-scrollbar {
      width: 5px
    }

    ::-webkit-scrollbar-track {
      background: transparent
    }

    ::-webkit-scrollbar-thumb {
      background: #0b94ba;
      border-radius: 99px
    }

    /* reveal on scroll */
    .reveal {
      opacity: 0;
      transform: translateY(26px);
      transition: opacity .6s cubic-bezier(.4, 0, .2, 1), transform .6s cubic-bezier(.4, 0, .2, 1)
    }

    .reveal.in {
      opacity: 1;
      transform: none
    }

    .d1 {
      transition-delay: .08s
    }

    .d2 {
      transition-delay: .16s
    }

    .d3 {
      transition-delay: .24s
    }

    .d4 {
      transition-delay: .32s
    }

    /* nav underline animation */
    .nl {
      position: relative
    }

    .nl::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 1.5px;
      background: currentColor;
      transition: width .22s cubic-bezier(.4, 0, .2, 1)
    }

    .nl:hover::after,
    .nl.on::after {
      width: 100%
    }

    .nl.on {
      font-weight: 500
    }

    /* shimmer button */
    .shimmer {
      position: relative;
      overflow: hidden
    }

    .shimmer::after {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 60%;
      height: 100%;
      background: rgba(255, 255, 255, .18);
      transform: skewX(-20deg);
      transition: left .4s cubic-bezier(.4, 0, .2, 1)
    }

    .shimmer:hover::after {
      left: 160%
    }

    /* photo frames */
    .pf {
      overflow: hidden;
      background: #d4d4d8
    }

    .pf img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .pf1 {
      overflow: hidden;
      background: #d4d4d8
    }

    .pf1 img {
      width: 100%;
      /* height: 100%; */
      object-fit: cover;
      display: block;
    }

    /* card hover */
    .card-h {
      transition: transform .28s cubic-bezier(.4, 0, .2, 1), border-color .18s
    }

    .card-h:hover {
      transform: translateY(-4px)
    }

    /* skill tag */
    .stag {
      transition: border-color .18s
    }

    [x-cloak] {
      display: none !important
    }
  </style>
</head>

<body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">

  <!-- Floating Toast Notification -->
  <div x-show="toast.show" 
       x-transition:enter="transition ease-out duration-300 transform"
       x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
       x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="fixed top-6 right-6 z-[100] max-w-sm w-full bg-zinc-900/90 dark:bg-zinc-950/90 backdrop-blur-xl border rounded-2xl shadow-2xl p-4 overflow-hidden"
       :class="toast.type === 'success' ? 'border-green-500/30' : 'border-red-500/30'"
       x-cloak>
    <div class="flex items-start gap-3">
      <!-- Icon -->
      <div class="shrink-0 mt-0.5">
        <template x-if="toast.type === 'success'">
          <svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </template>
        <template x-if="toast.type === 'error'">
          <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </template>
      </div>
      <!-- Message -->
      <div class="flex-1">
        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400" x-text="toast.type === 'success' ? 'Success' : 'Error'"></p>
        <p class="text-sm text-zinc-200 mt-1 leading-relaxed" x-text="toast.message"></p>
      </div>
      <!-- Close button -->
      <button @click="toast.show = false" class="text-zinc-500 hover:text-zinc-300 transition-colors shrink-0">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </div>

  <!-- ═══ NAV ═══ -->
  <header class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
    :class="sc?'bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md shadow-sm shadow-black/5':''">
    <nav class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="Main navigation">

      <a href="#hero" class="font-display font-bold text-xl tracking-tight relative z-10">
        <span class="text-zinc-900 dark:text-white">ade</span><span class="text-accent">bisi</span>
        <!-- <img src="images/line-logo.png" alt="Logo" class="w-40"> -->
      </a>

      <ul class="hidden md:flex items-center gap-8 text-sm" role="list">
        <li><a href="#services"
            class="nl text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
            :class="s==='services'?'on !text-zinc-900 dark:!text-white':''">Services</a></li>
        <li><a href="#work"
            class="nl text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
            :class="s==='work'?'on !text-zinc-900 dark:!text-white':''">Work</a></li>
        <li><a href="#about"
            class="nl text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
            :class="s==='about'?'on !text-zinc-900 dark:!text-white':''">About</a></li>
        <li><a href="#reviews"
            class="nl text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
            :class="s==='reviews'?'on !text-zinc-900 dark:!text-white':''">Reviews</a></li>
        <li><a href="#blog"
            class="nl text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
            :class="s==='blog'?'on !text-zinc-900 dark:!text-white':''">Blog</a></li>
        <li><a href="#contact"
            class="nl text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
            :class="s==='contact'?'on !text-zinc-900 dark:!text-white':''">Contact</a></li>
      </ul>

      <div class="flex items-center gap-3">
        <!-- dark toggle -->
        <button @click="dark=!dark"
          class="w-9 h-9 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors"
          :aria-label="dark?'Light mode':'Dark mode'">
          <svg x-show="!dark" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
          </svg>
          <svg x-show="dark" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
          </svg>
        </button>
        <!-- hire me -->
        <a href="#contact"
          class="hidden md:inline-flex items-center gap-2 shimmer bg-accent text-white text-sm font-medium px-5 py-2 rounded-full hover:bg-accent-light transition-colors">
          Hire me <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </a>
        <!-- hamburger -->
        <button @click="mm=!mm"
          class="md:hidden w-9 h-9 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800"
          :aria-expanded="mm" aria-label="Toggle menu">
          <svg x-show="!mm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg x-show="mm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </nav>

    <!-- mobile menu -->
    <div x-show="mm" x-cloak x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
      x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
      x-transition:leave-end="opacity-0 -translate-y-2"
      class="md:hidden bg-white dark:bg-zinc-950 border-t border-zinc-100 dark:border-zinc-900">
      <ul class="flex flex-col px-6 py-5 gap-4 text-sm font-medium" role="list">
        <li><a href="#services" @click="mm=false"
            class="block text-zinc-700 dark:text-zinc-300 hover:text-accent transition-colors">Services</a></li>
        <li><a href="#work" @click="mm=false"
            class="block text-zinc-700 dark:text-zinc-300 hover:text-accent transition-colors">Work</a></li>
        <li><a href="#about" @click="mm=false"
            class="block text-zinc-700 dark:text-zinc-300 hover:text-accent transition-colors">About</a></li>
        <li><a href="#reviews" @click="mm=false"
            class="block text-zinc-700 dark:text-zinc-300 hover:text-accent transition-colors">Reviews</a></li>
        <li><a href="#blog" @click="mm=false"
            class="block text-zinc-700 dark:text-zinc-300 hover:text-accent transition-colors">Blog</a></li>
        <li><a href="#contact" @click="mm=false"
            class="block text-zinc-700 dark:text-zinc-300 hover:text-accent transition-colors">Contact</a></li>
        <li class="pt-2 border-t border-zinc-100 dark:border-zinc-900">
          <a href="#contact" @click="mm=false"
            class="inline-flex shimmer bg-accent text-white font-medium text-sm px-5 py-2.5 rounded-full">Hire me →</a>
        </li>
      </ul>
    </div>
  </header>

  <main>

    <!-- ═══ HERO ═══ -->
    <section id="hero" class="relative min-h-screen flex items-center pt-16 overflow-hidden">
      <div class="absolute top-1/4 right-0 w-96 h-96 bg-accent/10 rounded-full blur-3xl pointer-events-none"
        aria-hidden="true"></div>
      <div
        class="absolute bottom-1/4 left-0 w-64 h-64 bg-zinc-200/50 dark:bg-zinc-800/30 rounded-full blur-3xl pointer-events-none"
        aria-hidden="true"></div>

      <div class="relative z-10 max-w-6xl mx-auto px-6 py-24 w-full">
        <div class="grid md:grid-cols-2 gap-12 items-center">

          <div>
            <p class="reveal text-sm font-medium text-accent tracking-widest uppercase mb-4">Available for work</p>
            <h1
              class="reveal d1 font-display font-bold text-5xl md:text-6xl lg:text-7xl leading-[1.05] tracking-tight text-zinc-900 dark:text-white mb-6">
              Hi, I'm <span class="text-accent">Adebisi</span>
            </h1>
            <p
              class="reveal d2 text-lg md:text-xl text-zinc-500 dark:text-zinc-400 font-light leading-relaxed max-w-md mb-10">
              Freelance <strong class="font-medium text-zinc-700 dark:text-zinc-300">UI/UX Designer &amp; Full Stack
                Developer</strong>. I design and build premium digital products - websites, apps, and branding solutions - that are fast, clean, and accessible.
            </p>
            <div class="reveal d3 flex flex-wrap gap-4">
              <a href="#work"
                class="shimmer inline-flex items-center gap-2 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-medium px-7 py-3.5 rounded-full hover:bg-zinc-700 dark:hover:bg-zinc-200 transition-colors text-sm">
                View my work
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </a>
              <a href="#contact"
                class="inline-flex items-center gap-2 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-medium px-7 py-3.5 rounded-full hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors text-sm">Get
              in touch</a>
            </div>
            <div class="reveal d4 flex gap-8 mt-14 pt-8 border-t border-zinc-100 dark:border-zinc-900">
              <div>
                <p class="font-display font-bold text-3xl text-zinc-900 dark:text-white">550+</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Projects done</p>
              </div>
              <div>
                <p class="font-display font-bold text-3xl text-zinc-900 dark:text-white">500+</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Happy clients</p>
              </div>
              <div>
                <p class="font-display font-bold text-3xl text-zinc-900 dark:text-white">7y+</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Experience</p>
              </div>
            </div>
          </div>

          <div class="reveal d2 flex justify-center md:justify-end">
            <div class="relative w-72 h-72 md:w-80 md:h-80 lg:w-96 lg:h-96">
              <div class="pf1 w-full h-full rounded-3xl">
                <img src="images/adebisi.jpg"
                  alt="Adebisi Covenant - Freelance UI/UX Designer" loading="eager">
              </div>
              <div
                class="absolute -bottom-4 -left-4 bg-accent text-white font-display font-bold text-sm px-4 py-2.5 rounded-2xl shadow-lg">
                Open to projects</div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ═══ SERVICES ═══ -->
    <section id="services" class="py-24 bg-zinc-50 dark:bg-zinc-900/40">
      <div class="max-w-6xl mx-auto px-6">
        <div class="mb-14">
          <p class="reveal text-xs font-medium text-accent tracking-widest uppercase mb-3">What I do</p>
          <h2 class="reveal d1 font-display font-bold text-4xl md:text-5xl text-zinc-900 dark:text-white">Services</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">

          <article
            class="reveal d1 card-h group bg-white dark:bg-zinc-900 rounded-2xl p-8 border border-zinc-100 dark:border-zinc-800 hover:border-accent">
            <div
              class="w-12 h-12 flex items-center justify-center dark:bg-zinc-800 rounded-xl mb-6 group-hover:bg-accent/10 transition-colors">
              <svg class="w-6 h-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2" />
              </svg>
            </div>
            <h3 class="font-display font-bold text-xl text-zinc-900 dark:text-white mb-3">UI/UX Design</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">From wireframes to polished Figma
              prototypes. Intuitive, visually compelling interfaces that convert visitors into users and put usability
              first.</p>
          </article>

          <article
            class="reveal d2 card-h group bg-zinc-900 dark:bg-zinc-800 rounded-2xl p-8 border border-zinc-800 hover:border-accent">
            <div
              class="w-12 h-12 flex items-center justify-center bg-zinc-800 dark:bg-zinc-700 rounded-xl mb-6 group-hover:bg-accent/20 transition-colors">
              <svg class="w-6 h-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
              </svg>
            </div>
            <h3 class="font-display font-bold text-xl text-white mb-3">Full Stack Dev</h3>
            <p class="text-sm text-zinc-400 leading-relaxed">Production-grade code with Tailwind CSS and Alpine.js.
              Pixel-perfect, fully responsive, SEO-friendly and blazing fast - no bloat, no heavy frameworks.</p>
          </article>

          <article
            class="reveal d3 card-h group bg-white dark:bg-zinc-900 rounded-2xl p-8 border border-zinc-100 dark:border-zinc-800 hover:border-accent">
            <div
              class="w-12 h-12 flex items-center justify-center dark:bg-zinc-800 rounded-xl mb-6 group-hover:bg-accent/10 transition-colors">
              <svg class="w-6 h-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
              </svg>
            </div>
            <h3 class="font-display font-bold text-xl text-zinc-900 dark:text-white mb-3">Graphics Design</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">The art of turning imaginative ideas into real-life representations. From corporate branding and logo designs to professional marketing assets.</p>
          </article>

        </div>
      </div>
    </section>

    <!-- ═══ WORK ═══ -->
    <section id="work" class="py-24">
      <div class="max-w-6xl mx-auto px-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-14">
          <div>
            <p class="reveal text-xs font-medium text-accent tracking-widest uppercase mb-3">Portfolio</p>
            <h2 class="reveal d1 font-display font-bold text-4xl md:text-5xl text-zinc-900 dark:text-white">Selected
              work</h2>
          </div>
          <a href="projects"
            class="reveal d1 text-sm font-medium text-zinc-500 dark:text-zinc-400 hover:text-accent transition-colors self-start sm:self-auto nl">All
            projects →</a>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <?php foreach ($featuredProjects as $idx => $project): 
            $tags = array_map('trim', explode(',', $project['tags']));
            if ($idx === 0): ?>
              <!-- First project (covers 2 rows) -->
              <article
                class="card-h reveal d1 group rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 hover:border-accent md:row-span-2">
                <div class="pf w-full h-64 md:h-80">
                  <img src="<?php echo htmlspecialchars($project['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" loading="lazy">
                </div>
                <div class="p-7">
                  <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach ($tags as $tIdx => $tag): ?>
                      <?php if ($tIdx === 0): ?>
                        <span class="text-xs dark:bg-zinc-800 text-accent border dark:border-zinc-700 px-3 py-1 rounded-full"><?php echo htmlspecialchars($tag); ?></span>
                      <?php else: ?>
                        <span class="text-xs bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 px-3 py-1 rounded-full"><?php echo htmlspecialchars($tag); ?></span>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                  <a href="case-study?slug=<?php echo htmlspecialchars($project['slug']); ?>">
                    <h3 class="font-display font-bold text-2xl text-zinc-900 dark:text-white mb-2"><?php echo htmlspecialchars($project['title']); ?></h3>
                  </a>
                  <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-5"><?php echo htmlspecialchars($project['description']); ?></p>
                  <a href="case-study?slug=<?php echo htmlspecialchars($project['slug']); ?>"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white nl">View case
                    study <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg></a>
                </div>
              </article>
            <?php else: ?>
              <!-- Other projects -->
              <article
                class="card-h reveal d<?php echo ($idx + 1); ?> group rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 hover:border-accent">
                <div class="pf w-full h-48">
                  <img src="<?php echo htmlspecialchars($project['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" loading="lazy">
                </div>
                <div class="p-6">
                  <div class="flex flex-wrap gap-2 mb-3">
                    <?php foreach ($tags as $tIdx => $tag): ?>
                      <?php if ($tIdx === 0): ?>
                        <span class="text-xs dark:bg-zinc-800 text-accent border dark:border-zinc-700 px-3 py-1 rounded-full"><?php echo htmlspecialchars($tag); ?></span>
                      <?php else: ?>
                        <span class="text-xs bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 px-3 py-1 rounded-full"><?php echo htmlspecialchars($tag); ?></span>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                  <a href="<?php echo htmlspecialchars($project['project_url']); ?>" <?php echo (strpos($project['project_url'], 'http') === 0) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                    <h3 class="font-display font-bold text-xl text-zinc-900 dark:text-white mb-1.5"><?php echo htmlspecialchars($project['title']); ?></h3>
                  </a>
                  <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4"><?php echo htmlspecialchars($project['description']); ?></p>
                  <a href="<?php echo htmlspecialchars($project['project_url']); ?>" <?php echo (strpos($project['project_url'], 'http') === 0) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white nl">Explore site →</a>
                </div>
              </article>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ═══ ABOUT ═══ -->
    <section id="about" class="py-24 bg-zinc-50 dark:bg-zinc-900/40">
      <div class="max-w-6xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">

          <div class="reveal order-2 md:order-1">
            <div class="pf1 w-full aspect-square max-w-sm mx-auto rounded-3xl">
              <img src="images/adebisi.jpg" alt="Adebisi"
                loading="lazy">
            </div>
          </div>

          <div class="order-1 md:order-2">
            <p class="reveal text-xs font-medium text-accent tracking-widest uppercase mb-3">About me</p>
            <h2
              class="reveal d1 font-display font-bold text-4xl md:text-5xl text-zinc-900 dark:text-white leading-tight mb-6">
              A bit about<br>who I am</h2>
            <p class="reveal d2 text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4">
              I'm Covenant Adebisi, a versatile and experienced Full Stack Developer and UI/UX Designer with over 7 years of professional expertise in creating dynamic, responsive web applications. I thrive at the intersection of great design, clean code, and strategic web auditing.
            </p>
            <p class="reveal d3 text-zinc-500 dark:text-zinc-400 leading-relaxed mb-8">
              I believe in building interfaces that are not only visually premium but also intuitive, accessible, and high-performing. When I'm not writing code or prototyping in Figma, you can find me consulting for companies on SEO, designing graphic assets, or auditing websites.
            </p>
            <div class="reveal d4">
              <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mb-3">Stack &amp;
                tools</p>
              <div class="flex flex-wrap gap-2" role="list" aria-label="Skills">
                <span role="listitem"
                  class="stag text-sm bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 px-3.5 py-1.5 rounded-full hover:border-accent">Tailwind
                  CSS</span>
                <span role="listitem"
                  class="stag text-sm bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 px-3.5 py-1.5 rounded-full hover:border-accent">Alpine.js</span>
                <span role="listitem"
                  class="stag text-sm bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 px-3.5 py-1.5 rounded-full hover:border-accent">Figma</span>
                <span role="listitem"
                  class="stag text-sm bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 px-3.5 py-1.5 rounded-full hover:border-accent">HTML
                  / CSS</span>
                <span role="listitem"
                  class="stag text-sm bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 px-3.5 py-1.5 rounded-full hover:border-accent">JavaScript</span>
                <span role="listitem"
                  class="stag text-sm bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 px-3.5 py-1.5 rounded-full hover:border-accent">PHP / MySQL</span>
                <span role="listitem"
                  class="stag text-sm bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 px-3.5 py-1.5 rounded-full hover:border-accent">Graphics Design</span>
                <span role="listitem"
                  class="stag text-sm bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 px-3.5 py-1.5 rounded-full hover:border-accent">WordPress</span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ═══ TESTIMONIALS ═══ -->
    <section id="reviews" class="py-24">
      <div class="max-w-6xl mx-auto px-6">
        <div class="mb-14">
          <p class="reveal text-xs font-medium text-accent tracking-widest uppercase mb-3">Social proof</p>
          <h2 class="reveal d1 font-display font-bold text-4xl md:text-5xl text-zinc-900 dark:text-white">What clients
            say</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">

          <blockquote
            class="reveal d1 bg-zinc-50 dark:bg-zinc-900 rounded-2xl p-7 border border-zinc-100 dark:border-zinc-800">
            <div class="flex gap-0.5 mb-5" aria-label="5 stars">
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>
            <p class="text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed mb-6 italic">"Neat and perfect work done always. Very professional, responsive, and delivers high-quality coding within the deadline."</p>
            <footer class="flex items-center gap-3">
              <div class="pf w-10 h-10 rounded-full shrink-0"><img src="images/reviews/IMG-20220130-WA0030.jpg"
                  alt="Adebayo Precious Oguntolu" loading="lazy"></div>
              <div>
                <p class="font-medium text-sm text-zinc-900 dark:text-white">Adebayo Precious Oguntolu</p>
                <p class="text-xs text-zinc-500">CEO</p>
              </div>
            </footer>
          </blockquote>

          <blockquote class="reveal d2 bg-zinc-900 dark:bg-zinc-800 rounded-2xl p-7 border border-zinc-800">
            <div class="flex gap-0.5 mb-5" aria-label="5 stars">
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>
            <p class="text-zinc-400 text-sm leading-relaxed mb-6 italic">"Working with Harkone Designs has been awesome. They ask the right questions, pay close attention to detail, and deliver on time."
            </p>
            <footer class="flex items-center gap-3">
              <div class="pf w-10 h-10 rounded-full shrink-0"><img src="images/reviews/LYM_LOGO.png"
                  alt="David Avong" loading="lazy"></div>
              <div>
                <p class="font-medium text-sm text-white">David Avong</p>
                <p class="text-xs text-zinc-500">Lead, Lighthouse Youth Movement</p>
              </div>
            </footer>
          </blockquote>

          <blockquote
            class="reveal d3 bg-zinc-50 dark:bg-zinc-900 rounded-2xl p-7 border border-zinc-100 dark:border-zinc-800">
            <div class="flex gap-0.5 mb-5" aria-label="5 stars">
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 20 20" aria-hidden="true">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>
            <p class="text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed mb-6 italic">"Harkone Designs is the best. Their software capabilities and creative eye turned our projects into absolute successes."</p>
            <footer class="flex items-center gap-3">
              <div class="pf w-10 h-10 rounded-full shrink-0"><img src="images/reviews/agbc_1e8b9b979b98c7a0a77c1c70ad221f64.png"
                  alt="Rev. Julius Jerry" loading="lazy"></div>
              <div>
                <p class="font-medium text-sm text-zinc-900 dark:text-white">Rev. Julius Jerry</p>
                <p class="text-xs text-zinc-500">Church Leader</p>
              </div>
            </footer>
          </blockquote>

        </div>
      </div>
    </section>

    <!-- ═══ BLOG ═══ -->
    <section id="blog" class="py-24 bg-zinc-50 dark:bg-zinc-900/40">
      <div class="max-w-6xl mx-auto px-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-14">
          <div>
            <p class="reveal text-xs font-medium text-accent tracking-widest uppercase mb-3">Thoughts</p>
            <h2 class="reveal d1 font-display font-bold text-4xl md:text-5xl text-zinc-900 dark:text-white">From the
              blog</h2>
          </div>
          <a href="blog"
            class="reveal d1 text-sm font-medium text-zinc-500 dark:text-zinc-400 hover:text-accent transition-colors self-start sm:self-auto nl">All
            articles →</a>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
          <?php foreach ($latestBlogs as $idx => $blog): ?>
            <article
              class="reveal d<?php echo ($idx + 1); ?> card-h group bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-zinc-100 dark:border-zinc-800 hover:border-accent">
              <div class="pf w-full h-44">
                <img src="<?php echo htmlspecialchars($blog['image_url']); ?>"
                  alt="<?php echo htmlspecialchars($blog['title']); ?>" loading="lazy">
              </div>
              <div class="p-6">
                <div class="flex items-center gap-3 mb-3">
                  <span
                    class="text-xs dark:bg-zinc-800 text-accent border dark:border-zinc-700 px-2.5 py-1 rounded-full"><?php echo htmlspecialchars($blog['category']); ?></span>
                  <span class="text-xs text-zinc-400"><?php echo date("M j, Y", strtotime($blog['created_at'])); ?></span>
                </div>
                <a href="blog-article?slug=<?php echo urlencode($blog['slug']); ?>">
                  <h3
                    class="font-display font-bold text-lg text-zinc-900 dark:text-white mb-2 group-hover:text-accent transition-colors">
                    <?php echo htmlspecialchars($blog['title']); ?></h3>
                </a>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                <a href="blog-article?slug=<?php echo urlencode($blog['slug']); ?>"
                  class="inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white nl">Read more
                  →</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ═══ CONTACT ═══ -->
    <section id="contact" class="py-24">
      <div class="max-w-6xl mx-auto px-6">
        <div class="bg-zinc-900 dark:bg-zinc-800 rounded-3xl p-10 md:p-16 relative overflow-hidden">
          <div class="absolute top-0 right-0 w-64 h-64 bg-accent/20 rounded-full blur-3xl pointer-events-none"
            aria-hidden="true"></div>
          <div class="absolute bottom-0 left-0 w-40 h-40 bg-accent/10 rounded-full blur-2xl pointer-events-none"
            aria-hidden="true"></div>

          <div class="relative z-10 grid md:grid-cols-2 gap-12 items-start">

            <div>
              <p class="reveal text-xs font-medium text-accent tracking-widest uppercase mb-3">Get in touch</p>
              <h2 class="reveal d1 font-display font-bold text-4xl md:text-5xl text-white leading-tight mb-5">Let's
                work<br>together</h2>
              <p class="reveal d2 text-zinc-400 leading-relaxed mb-8">I'm open to UI/UX and frontend missions, short or
                long-term. Landing page, full product redesign, or just a second pair of eyes - let's talk.</p>

              <div class="reveal d3 flex flex-col gap-4">
                <a href="mailto:info@harkone.com.ng"
                   class="group flex items-center gap-3 text-zinc-400 hover:text-white transition-colors">
                  <span
                    class="w-9 h-9 flex items-center justify-center bg-zinc-800 rounded-lg group-hover:bg-accent/20 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </span>
                  <span class="text-sm">info@harkone.com.ng</span>
                </a>
                <a href="https://www.linkedin.com/in/adebisi-covenant-0b4b73163/" rel="noopener noreferrer" target="_blank"
                   class="group flex items-center gap-3 text-zinc-400 hover:text-white transition-colors">
                  <span
                    class="w-9 h-9 flex items-center justify-center bg-zinc-800 rounded-lg group-hover:bg-accent/20 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path
                        d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                    </svg>
                  </span>
                  <span class="text-sm">linkedin.com/in/adebisi-covenant</span>
                </a>
                <a href="https://api.whatsapp.com/send?phone=2349031704109" rel="noopener noreferrer" target="_blank"
                   class="group flex items-center gap-3 text-zinc-400 hover:text-white transition-colors">
                  <span
                    class="w-9 h-9 flex items-center justify-center bg-zinc-800 rounded-lg group-hover:bg-accent/20 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.727-1.458L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.963C16.59 2.016 14.12 1.01 11.492 1.01c-5.433 0-9.858 4.37-9.862 9.801 0 1.743.465 3.447 1.348 4.978l-.999 3.649 3.766-.975zm13.125-9.191c-.302-.15-1.78-.875-2.056-.976-.276-.101-.476-.15-.677.15-.201.301-.776.976-.951 1.176-.175.201-.35.226-.653.076-.301-.15-1.272-.469-2.422-1.496-.895-.797-1.5-1.782-1.675-2.083-.175-.301-.019-.464.132-.613.135-.134.301-.351.451-.527.15-.175.201-.301.301-.502.1-.201.05-.376-.025-.526-.075-.151-.677-1.629-.927-2.23-.244-.588-.492-.507-.677-.516-.175-.008-.375-.01-.576-.01-.201 0-.527.075-.802.376-.276.301-1.053 1.028-1.053 2.507 0 1.48 1.077 2.91 1.228 3.11 1.1 1.48 2.005 2.5 3.505 3.11 1.5.61 1.5.41 1.8.38.3-.03 1.78-.727 2.03-1.43.25-.702.25-1.303.176-1.43-.076-.124-.277-.2-.577-.35z"/>
                    </svg>
                  </span>
                  <span class="text-sm">WhatsApp Chat</span>
                </a>
              </div>
            </div>

            <div class="reveal d2">
              <form action="reserve.php" method="POST" novalidate>
                <div class="flex flex-col gap-4">
                  <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                      <label for="fname" class="block text-xs font-medium text-zinc-400 mb-1.5">Name <span
                          aria-hidden="true">*</span></label>
                      <input type="text" id="fname" name="name" placeholder="Full name" required autocomplete="name"
                        class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 placeholder-zinc-600 focus:outline-none focus:border-accent transition-colors">
                    </div>
                    <div>
                      <label for="femail" class="block text-xs font-medium text-zinc-400 mb-1.5">Email <span
                          aria-hidden="true">*</span></label>
                      <input type="email" id="femail" name="email" placeholder="Email address" required
                        autocomplete="email"
                        class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 placeholder-zinc-600 focus:outline-none focus:border-accent transition-colors">
                    </div>
                  </div>
                  <div>
                    <label for="fsubject" class="block text-xs font-medium text-zinc-400 mb-1.5">Subject</label>
                    <input type="text" id="fsubject" name="subject" placeholder="Project inquiry"
                      class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 placeholder-zinc-600 focus:outline-none focus:border-accent transition-colors">
                  </div>
                  <div>
                    <label for="fmessage" class="block text-xs font-medium text-zinc-400 mb-1.5">Message <span
                        aria-hidden="true">*</span></label>
                    <textarea id="fmessage" name="message" rows="4" placeholder="Tell me about your project..." required
                      class="w-full bg-zinc-800 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 placeholder-zinc-600 focus:outline-none focus:border-accent transition-colors resize-none"></textarea>
                  </div>
                  <button type="submit"
                    class="shimmer w-full bg-accent text-white font-display font-bold text-sm py-3.5 rounded-xl hover:bg-accent-light transition-colors">
                    Send message →
                  </button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include 'includes/footer.php'; ?>

  <script>
    function app() {
      return {
        dark: false,
        mm: false,
        sc: false,
        s: 'hero',
        toast: {
          show: false,
          type: '',
          message: ''
        },

        init() {
          // dark mode
          this.dark = localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
          this.$watch('dark', v => localStorage.setItem('theme', v ? 'dark' : 'light'));

          // scroll
          window.addEventListener('scroll', () => {
            this.sc = window.scrollY > 20;
            this.updateSection();
          }, { passive: true });

          // reveal
          const io = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
          }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
          document.querySelectorAll('.reveal').forEach(el => io.observe(el));

          // year
          document.getElementById('yr').textContent = new Date().getFullYear();

          // Check contact form redirect parameters
          const urlParams = new URLSearchParams(window.location.search);
          if (urlParams.has('contact_success')) {
            this.showToast('success', 'Message sent successfully! I will get back to you soon.');
            this.clearUrlParams();
          } else if (urlParams.has('contact_error')) {
            const err = urlParams.get('contact_error');
            let msg = 'Failed to send message. Please try again.';
            if (err === 'empty') msg = 'Please fill in all required fields.';
            if (err === 'invalid_email') msg = 'Please enter a valid email address.';
            this.showToast('error', msg);
            this.clearUrlParams();
          }
        },

        showToast(type, message) {
          this.toast.show = true;
          this.toast.type = type;
          this.toast.message = message;
          setTimeout(() => {
            this.toast.show = false;
          }, 5000);
        },

        clearUrlParams() {
          const url = new URL(window.location);
          url.searchParams.delete('contact_success');
          url.searchParams.delete('contact_error');
          window.history.replaceState({}, document.title, url.pathname + url.hash);
        },

        updateSection() {
          const atBottom = (window.innerHeight + window.scrollY) >= document.body.scrollHeight - 60;
          if (atBottom) { this.s = 'contact'; return; }
          const ids = ['contact', 'blog', 'reviews', 'about', 'work', 'services', 'hero'];
          for (const id of ids) {
            const el = document.getElementById(id);
            if (el && window.scrollY >= el.offsetTop - 130) { this.s = id; return; }
          }
        }
      }
    }
  </script>

</body>

</html>