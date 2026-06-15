<?php
require_once __DIR__ . '/includes/db.php';
$pdo = get_db_connection();

$slug = $_GET['slug'] ?? '';
$article = null;

if (!empty($slug)) {
    $stmt = $pdo->prepare("SELECT * FROM `blogs` WHERE `slug` = ? AND `status` = 'published'");
    $stmt->execute([$slug]);
    $article = $stmt->fetch();
}

if (!$article) {
    header("Location: blog");
    exit;
}

// Fetch related articles (excluding current)
$relatedStmt = $pdo->prepare("SELECT * FROM `blogs` WHERE `id` != ? AND `status` = 'published' ORDER BY `created_at` DESC LIMIT 3");
$relatedStmt->execute([$article['id']]);
$relatedArticles = $relatedStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"
  x-data="{ dark: localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches), scrolled:false, mobileMenu:false, progress:0 }"
  :class="{'dark':dark}"
  x-init="$watch('dark',v=>localStorage.setItem('theme',v?'dark':'light'));window.addEventListener('scroll',()=>{scrolled=window.scrollY>20;progress=Math.min(100,Math.round((window.scrollY/(document.body.scrollHeight-window.innerHeight))*100));},{passive:true})"
  class="scroll-smooth">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?php echo htmlspecialchars($article['excerpt']); ?>" />
  <meta name="author" content="Adebisi Covenant" />
  <meta name="language" content="English">
  <meta name="revisit-after" content="1 days">
  <meta name="author" content="Adebisi Covenant">
  <meta name="robots" content="index, follow">
  <meta name="geo.region" content="NG" />
  <meta name="geo.placename" content="Lagos" />
  <meta name="geo.position" content="6.5244;3.3792" />
  <meta name="ICBM" content="6.5244, 3.3792" />

  <meta property="og:title" content="<?php echo htmlspecialchars($article['title']); ?> - Adebisi Covenant" />
  <meta property="og:type" content="article" />
  <meta property="og:locale" content="en_US"/>
  <meta property="og:url" content="https://harkone.com.ng"/>
  <meta property="og:description" content="<?php echo htmlspecialchars($article['excerpt']); ?>"/>
  <meta property="og:image" content="https://harkone.com.ng/images/sm.png"/>

  <meta property="twitter:card" content="summary"/>
  <meta property="twitter:title" content="<?php echo htmlspecialchars($article['title']); ?> - Adebisi Covenant"/>
  <meta property="twitter:description" content="<?php echo htmlspecialchars($article['excerpt']); ?>"/>
  <meta property="twitter:url" content="https://harkone.com.ng"/>
  <meta property="twitter:image" content="https://harkone.com.ng/images/sm.png"/>

  <link rel="shortcut icon" type="image/x-icon" href="https://harkone.com.ng/images/favicon.png">
  <title><?php echo htmlspecialchars($article['title']); ?> - Adebisi Covenant</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500&display=swap"
    rel="stylesheet" />
  <script>
    tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { display: ['PT Sans', 'sans-serif'], body: ['DM Sans', 'sans-serif'] }, colors: { accent: '#0b94baff', 'accent-light': '#2dd902ff' } } } }
  </script>
  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
    }

    html {
      font-family: 'DM Sans', sans-serif;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      font-family: 'PT Sans', sans-serif;
    }

    ::-webkit-scrollbar {
      width: 5px;
    }

    ::-webkit-scrollbar-track {
      background: transparent;
    }

    ::-webkit-scrollbar-thumb {
      background: #0b94ba;
      border-radius: 99px;
    }

    body {
      transition: background-color .3s, color .3s;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
      pointer-events: none;
      z-index: 0;
      opacity: .4;
    }

    .nav-link {
      position: relative;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 1.5px;
      background: currentColor;
      transition: width .25s cubic-bezier(.4, 0, .2, 1);
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .btn-primary {
      position: relative;
      overflow: hidden;
    }

    .btn-primary::after {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 60%;
      height: 100%;
      background: rgba(255, 255, 255, .18);
      transform: skewX(-20deg);
      transition: left .4s cubic-bezier(.4, 0, .2, 1);
    }

    .btn-primary:hover::after {
      left: 160%;
    }

    [x-cloak] {
      display: none !important;
    }

    .photo-frame {
      position: relative;
      overflow: hidden;
      background: #e4e4e7;
    }

    .photo-frame img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    /* Article typography */
    .prose-article h2 {
      font-family: 'PT Sans', sans-serif;
      font-size: 1.6rem;
      font-weight: 700;
      color: inherit;
      margin: 2.5rem 0 1rem;
      line-height: 1.25;
    }

    .prose-article h3 {
      font-family: 'PT Sans', sans-serif;
      font-size: 1.2rem;
      font-weight: 700;
      margin: 2rem 0 .75rem;
      line-height: 1.3;
    }

    .prose-article p {
      margin: 0 0 1.4rem;
      line-height: 1.8;
      color: #52525b;
    }

    .dark .prose-article p {
      color: #a1a1aa;
    }

    .prose-article ul {
      margin: 0 0 1.4rem 1.5rem;
      list-style: disc;
    }

    .prose-article li {
      margin-bottom: .4rem;
      line-height: 1.7;
      color: #52525b;
    }

    .dark .prose-article li {
      color: #a1a1aa;
    }

    .prose-article pre {
      background: #18181b;
      color: #e4e4e7;
      border-radius: 12px;
      padding: 1.25rem 1.5rem;
      overflow-x: auto;
      font-size: .85rem;
      line-height: 1.7;
      margin: 0 0 1.6rem;
    }

    .prose-article code:not(pre code) {
      background: #f4f4f5;
      color: #0b94ba;
      padding: .15em .4em;
      border-radius: 4px;
      font-size: .875em;
    }

    .dark .prose-article code:not(pre code) {
      background: #27272a;
    }

    .prose-article blockquote {
      border-left: 3px solid #0b94ba;
      padding: .75rem 1.25rem;
      margin: 0 0 1.6rem;
      background: #fff7f4;
      border-radius: 0 8px 8px 0;
    }

    .dark .prose-article blockquote {
      background: #27272a;
    }

    .prose-article blockquote p {
      color: #52525b;
      font-style: italic;
      margin: 0;
    }

    .dark .prose-article blockquote p {
      color: #a1a1aa;
    }

    .prose-article a {
      color: #0b94ba;
      text-decoration: underline;
      text-underline-offset: 3px;
    }

    .prose-article hr {
      border: none;
      border-top: 1px solid #e4e4e7;
      margin: 2.5rem 0;
    }

    .dark .prose-article hr {
      border-top-color: #27272a;
    }

    /* Reading progress bar */
    .progress-bar {
      position: fixed;
      top: 0;
      left: 0;
      height: 3px;
      background: #0b94ba;
      z-index: 100;
      transition: width .1s;
    }

    .reveal {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity .5s cubic-bezier(.4, 0, .2, 1), transform .5s cubic-bezier(.4, 0, .2, 1);
    }

    .reveal.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .card {
      transition: transform .3s cubic-bezier(.4, 0, .2, 1), border-color .2s;
    }

    .card:hover {
      transform: translateY(-3px);
    }
  </style>
</head>

<body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-body antialiased">

  <!-- Reading progress bar -->
  <div class="progress-bar" :style="`width:${progress}%`" role="progressbar" :aria-valuenow="progress" aria-valuemin="0"
    aria-valuemax="100" aria-label="Reading progress"></div>

  <!-- NAV -->
  <header class="fixed top-0.5 left-0 right-0 z-50 transition-all duration-300"
    :class="scrolled?'bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md shadow-sm shadow-black/5':'bg-transparent'">
    <nav class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="Main navigation">
      <a href="./" class="font-display font-bold text-xl tracking-tight z-10">
        <span class="text-zinc-900 dark:text-white">ade</span><span class="text-accent">bisi</span>
      </a>
      <ul class="hidden md:flex items-center gap-8 text-sm font-medium" role="list">
        <li><a href="./#services"
            class="nav-link text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Services</a>
        </li>
        <li><a href="projects"
            class="nav-link text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Work</a>
        </li>
        <li><a href="./#about"
            class="nav-link text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">About</a>
        </li>
        <li><a href="blog" class="nav-link text-accent font-medium">Blog</a></li>
        <li><a href="./#contact"
            class="nav-link text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Contact</a>
        </li>
      </ul>
      <div class="flex items-center gap-3">
        <button @click="dark=!dark"
          class="w-9 h-9 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
          :aria-label="dark?'Light mode':'Dark mode'">
          <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
          </svg>
          <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
          </svg>
        </button>
        <a href="./#contact"
          class="hidden md:inline-flex items-center gap-2 btn-primary bg-accent text-white text-sm font-medium px-5 py-2 rounded-full hover:bg-accent-light transition-colors">Hire
          me →</a>
        <button @click="mobileMenu=!mobileMenu"
          class="md:hidden w-9 h-9 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800"
          aria-label="Toggle menu">
          <svg x-show="!mobileMenu" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg x-show="mobileMenu" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </nav>
    <div x-show="mobileMenu" x-cloak x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
      x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
      x-transition:leave-end="opacity-0 -translate-y-2"
      class="md:hidden bg-white dark:bg-zinc-950 border-t border-zinc-100 dark:border-zinc-900">
      <ul class="flex flex-col px-6 py-4 gap-4 text-sm font-medium" role="list">
        <li><a href="./#services" @click="mobileMenu=false" class="block text-zinc-700 dark:text-zinc-300">Services</a>
        </li>
        <li><a href="projects" @click="mobileMenu=false" class="block text-zinc-700 dark:text-zinc-300">Work</a>
        </li>
        <li><a href="./#about" @click="mobileMenu=false" class="block text-zinc-700 dark:text-zinc-300">About</a></li>
        <li><a href="blog" class="block text-accent font-medium">Blog</a></li>
        <li><a href="./#contact" @click="mobileMenu=false" class="block text-zinc-700 dark:text-zinc-300">Contact</a>
        </li>
      </ul>
    </div>
  </header>

  <main>

    <!-- ARTICLE HEADER -->
    <article>
      <header class="pt-32 pb-12 max-w-3xl mx-auto px-6">
        <a href="blog"
          class="inline-flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 hover:text-accent transition-colors mb-8">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
          </svg>
          Back to blog
        </a>

        <div class="flex items-center gap-3 mb-5">
          <span
            class="text-xs dark:bg-zinc-800 text-accent border dark:border-zinc-700 px-2.5 py-1 rounded-full"><?php echo htmlspecialchars($article['category']); ?></span>
          <span class="text-xs text-zinc-400"><?php echo date("F j, Y", strtotime($article['created_at'])); ?> · <?php echo intval($article['reading_time']); ?> min read</span>
        </div>

        <h1 class="font-display font-bold text-4xl md:text-5xl text-zinc-900 dark:text-white leading-tight mb-6"><?php echo htmlspecialchars($article['title']); ?></h1>

        <p class="text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed mb-8"><?php echo htmlspecialchars($article['excerpt']); ?></p>

        <!-- Author -->
        <div class="flex items-center gap-4 py-6 border-t border-b border-zinc-100 dark:border-zinc-900">
          <div class="photo-frame w-12 h-12 rounded-full shrink-0">
            <img src="images/adebisi.jpg?w=100&q=80" alt="Adebisi Covenant"
              loading="lazy" />
          </div>
          <div>
            <p class="font-medium text-zinc-900 dark:text-white text-sm">Adebisi Covenant</p>
            <p class="text-xs text-zinc-500">UI/UX Designer &amp; Full Stack Developer</p>
          </div>
          <div class="ml-auto flex gap-3">
            <a href="#"
              class="w-8 h-8 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-accent hover:border-accent transition-colors"
              aria-label="Share on Twitter">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"
                aria-hidden="true">
                <path
                  d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.259 5.632 5.905-5.632zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
              </svg>
            </a>
            <a href="#"
              class="w-8 h-8 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800 text-zinc-500 hover:text-accent hover:border-accent transition-colors"
              aria-label="Share on LinkedIn">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"
                aria-hidden="true">
                <path
                  d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
              </svg>
            </a>
          </div>
        </div>
      </header>

      <!-- Hero image -->
      <div class="max-w-4xl mx-auto px-6 mb-12">
        <div class="photo-frame w-full h-72 md:h-96 rounded-2xl">
          <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>"
            loading="lazy" />
        </div>
      </div>

      <!-- Article content -->
      <div class="max-w-3xl mx-auto px-6 pb-16 prose-article">
        <?php echo $article['content']; ?>
      </div>
    </article>

    <!-- RELATED ARTICLES -->
    <section class="bg-zinc-50 dark:bg-zinc-900/50 py-16">
      <div class="max-w-6xl mx-auto px-6">
        <h2 class="font-display font-bold text-2xl text-zinc-900 dark:text-white mb-8">More from the blog</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <?php foreach ($relatedArticles as $idx => $rel): ?>
            <article
              class="reveal card group bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-zinc-100 dark:border-zinc-800 hover:border-accent transition-colors">
              <div class="photo-frame w-full h-40">
                <img src="<?php echo htmlspecialchars($rel['image_url']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>"
                  loading="lazy" />
              </div>
              <div class="p-5">
                <span class="text-xs text-zinc-400 mb-2 block"><?php echo date("M j, Y", strtotime($rel['created_at'])); ?> · <?php echo htmlspecialchars($rel['category']); ?></span>
                <a href="blog-article?slug=<?php echo urlencode($rel['slug']); ?>">
                  <h3
                    class="font-display font-bold text-base text-zinc-900 dark:text-white mb-2 group-hover:text-accent transition-colors">
                    <?php echo htmlspecialchars($rel['title']); ?></h3>
                </a>
                <a href="blog-article?slug=<?php echo urlencode($rel['slug']); ?>" class="text-sm font-medium text-accent nav-link">Read →</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

  </main>

  <?php include 'includes/footer.php'; ?>

  <script>
    
    const observer = new IntersectionObserver(entries => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); } });
    }, { threshold: .1, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
  </script>

</body>

</html>