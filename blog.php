<?php
require_once __DIR__ . '/includes/db.php';
$pdo = get_db_connection();

// Fetch all published blogs
$stmt = $pdo->prepare("SELECT * FROM `blogs` WHERE `status` = 'published' ORDER BY `created_at` DESC");
$stmt->execute();
$allBlogs = $stmt->fetchAll();

$featuredBlog = !empty($allBlogs) ? $allBlogs[0] : null;
$otherBlogs = array_slice($allBlogs, 1);

function getFilterSlug($category) {
    $catLower = strtolower($category);
    if (strpos($catLower, 'dev') !== false) {
        return 'dev';
    } elseif (strpos($catLower, 'design') !== false || strpos($catLower, 'review') !== false) {
        return 'design';
    } elseif (strpos($catLower, 'freelance') !== false) {
        return 'freelance';
    }
    return 'other';
}
?>
<!DOCTYPE html>
<html lang="en"
  x-data="{ dark: localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches), scrolled:false, mobileMenu:false }"
  :class="{'dark':dark}"
  x-init="$watch('dark',v=>localStorage.setItem('theme',v?'dark':'light'));window.addEventListener('scroll',()=>scrolled=window.scrollY>20,{passive:true})"
  class="scroll-smooth">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Adebisi Covenant's blog - thoughts on web design, development, and Harkone Designs." />
  <meta name="author" content="Adebisi Covenant" />
  <meta name="language" content="English">
  <meta name="revisit-after" content="1 days">
  <meta name="author" content="Adebisi Covenant">
  <meta name="robots" content="index, follow">
  <meta name="geo.region" content="NG" />
  <meta name="geo.placename" content="Lagos" />
  <meta name="geo.position" content="6.5244;3.3792" />
  <meta name="ICBM" content="6.5244, 3.3792" />

  <meta property="og:title" content="Blog - Adebisi Covenant" />
  <meta property="og:type" content="website" />
  <meta property="og:locale" content="en_US"/>
  <meta property="og:url" content="https://harkone.com.ng"/>
  <meta property="og:description" content="Adebisi Covenant's blog - thoughts on web design, development, and Harkone Designs."/>
  <meta property="og:image" content="https://harkone.com.ng/images/sm.png"/>

  <meta property="twitter:card" content="summary"/>
  <meta property="twitter:title" content="Blog - Adebisi Covenant"/>
  <meta property="twitter:description" content="Adebisi Covenant's blog - thoughts on web design, development, and Harkone Designs."/>
  <meta property="twitter:url" content="https://harkone.com.ng"/>
  <meta property="twitter:image" content="https://harkone.com.ng/images/sm.png"/>

  <link rel="shortcut icon" type="image/x-icon" href="https://harkone.com.ng/images/favicon.png">
  <title>Blog - Adebisi Covenant</title>
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

    .reveal {
      opacity: 0;
      transform: translateY(24px);
      transition: opacity .6s cubic-bezier(.4, 0, .2, 1), transform .6s cubic-bezier(.4, 0, .2, 1);
    }

    .reveal.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .reveal-delay-1 {
      transition-delay: .08s;
    }

    .reveal-delay-2 {
      transition-delay: .16s;
    }

    .reveal-delay-3 {
      transition-delay: .24s;
    }

    .reveal-delay-4 {
      transition-delay: .32s;
    }

    .reveal-delay-5 {
      transition-delay: .4s;
    }

    .reveal-delay-6 {
      transition-delay: .48s;
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

    .card {
      transition: transform .3s cubic-bezier(.4, 0, .2, 1), border-color .2s;
    }

    .card:hover {
      transform: translateY(-3px);
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
  </style>
</head>

<body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-body antialiased">

  <!-- NAV -->
  <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    :class="scrolled?'bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md shadow-sm shadow-black/5':'bg-transparent'">
    <nav class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="Main navigation">
      <a href="./" class="font-display font-bold text-xl tracking-tight z-10" aria-label="Back to home">
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
        <li><a href="blog" class="nav-link text-accent font-medium" style="--tw-text-opacity:1">Blog</a></li>
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
          class="hidden md:inline-flex items-center gap-2 btn-primary bg-accent text-white text-sm font-medium px-5 py-2 rounded-full hover:bg-accent-light transition-colors">
          Hire me <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </a>
        <button @click="mobileMenu=!mobileMenu"
          class="md:hidden w-9 h-9 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800"
          :aria-expanded="mobileMenu" aria-label="Toggle menu">
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

    <!-- HERO BLOG -->
    <section class="pt-36 pb-16 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-80 h-80 bg-accent/10 rounded-full blur-3xl pointer-events-none"
        aria-hidden="true"></div>
      <div class="max-w-6xl mx-auto px-6 relative z-10">
        <p class="reveal text-xs font-medium text-accent tracking-widest uppercase mb-3">Writing</p>
        <h1
          class="reveal reveal-delay-1 font-display font-bold text-5xl md:text-6xl text-zinc-900 dark:text-white leading-tight mb-4">
          The Blog</h1>
        <p class="reveal reveal-delay-2 text-lg text-zinc-500 dark:text-zinc-400 max-w-xl leading-relaxed">Thoughts on
          UI/UX design, frontend development, and the realities of full-time freelancing. No fluff.</p>

        <!-- Filter tags -->
        <div class="reveal reveal-delay-3 flex flex-wrap gap-2 mt-8" x-data="{active:'all'}">
          <button @click="active='all'"
            :class="active==='all'?'bg-accent text-white border-accent':'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700 hover:border-accent'"
            class="text-sm px-4 py-1.5 rounded-full border transition-colors">All</button>
          <button @click="active='design'"
            :class="active==='design'?'bg-accent text-white border-accent':'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700 hover:border-accent'"
            class="text-sm px-4 py-1.5 rounded-full border transition-colors">Design</button>
          <button @click="active='dev'"
            :class="active==='dev'?'bg-accent text-white border-accent':'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700 hover:border-accent'"
            class="text-sm px-4 py-1.5 rounded-full border transition-colors">Dev</button>
          <button @click="active='freelance'"
            :class="active==='freelance'?'bg-accent text-white border-accent':'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700 hover:border-accent'"
            class="text-sm px-4 py-1.5 rounded-full border transition-colors">Freelance</button>
        </div>
      </div>
    </section>

    <!-- FEATURED ARTICLE -->
    <?php if ($featuredBlog): 
      $featuredSlug = getFilterSlug($featuredBlog['category']); ?>
      <section class="pb-16" x-show="active === 'all' || active === '<?php echo $featuredSlug; ?>'">
        <div class="max-w-6xl mx-auto px-6">
          <a href="blog-article?slug=<?php echo urlencode($featuredBlog['slug']); ?>"
            class="reveal group block rounded-3xl overflow-hidden bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 hover:border-accent transition-colors">
            <div class="grid md:grid-cols-2">
              <div class="photo-frame h-64 md:h-auto">
                <img src="<?php echo htmlspecialchars($featuredBlog['image_url']); ?>"
                  alt="<?php echo htmlspecialchars($featuredBlog['title']); ?>" loading="lazy" />
              </div>
              <div class="p-8 md:p-12 flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-4">
                  <span
                    class="text-xs dark:bg-zinc-800 text-accent border dark:border-zinc-700 px-2.5 py-1 rounded-full"><?php echo htmlspecialchars($featuredBlog['category']); ?></span>
                  <span class="text-xs text-zinc-400"><?php echo date("M j, Y", strtotime($featuredBlog['created_at'])); ?> · <?php echo intval($featuredBlog['reading_time']); ?> min read</span>
                </div>
                <h2
                  class="font-display font-bold text-2xl md:text-3xl text-zinc-900 dark:text-white mb-4 group-hover:text-accent transition-colors">
                  <?php echo htmlspecialchars($featuredBlog['title']); ?></h2>
                <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed mb-6"><?php echo htmlspecialchars($featuredBlog['excerpt']); ?></p>
                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-accent nav-link">Read article
                  →</span>
              </div>
            </div>
          </a>
        </div>
      </section>
    <?php endif; ?>

    <!-- ALL ARTICLES GRID -->
    <section class="pb-24">
      <div class="max-w-6xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
          <?php if (empty($otherBlogs) && !$featuredBlog): ?>
            <div class="col-span-2 text-center py-12">
              <p class="text-zinc-400 text-sm">No articles published yet.</p>
            </div>
          <?php else: ?>
            <?php foreach ($otherBlogs as $idx => $blog): 
              $blogSlug = getFilterSlug($blog['category']); ?>
              <article
                x-show="active === 'all' || active === '<?php echo $blogSlug; ?>'"
                class="reveal reveal-delay-<?php echo (($idx % 3) + 1); ?> card group bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-zinc-100 dark:border-zinc-800 hover:border-accent">
                <div class="photo-frame w-full h-44">
                  <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>"
                    loading="lazy" />
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
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white nav-link">Read
                    more →</a>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
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