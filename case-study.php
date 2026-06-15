<?php
require_once __DIR__ . '/includes/db.php';
$pdo = get_db_connection();

$slug = $_GET['slug'] ?? 'zerta-hq';

$stmt = $pdo->prepare("SELECT * FROM `projects` WHERE `slug` = ? AND `status` = 'published'");
$stmt->execute([$slug]);
$project = $stmt->fetch();

if (!$project) {
    header("Location: projects");
    exit;
}

// Fetch other projects for bottom section
$moreProjectsStmt = $pdo->prepare("SELECT * FROM `projects` WHERE `id` != ? AND `status` = 'published' LIMIT 3");
$moreProjectsStmt->execute([$project['id']]);
$moreProjects = $moreProjectsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" x-data="{
  dark: localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches),
  scrolled: false,
  mobileMenu: false,
  progress: 0
}" :class="{'dark':dark}" x-init="
  $watch('dark', v => localStorage.setItem('theme', v ? 'dark' : 'light'));
  window.addEventListener('scroll', () => {
    scrolled = window.scrollY > 20;
    progress = Math.min(100, Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100));
  }, { passive: true });
" class="scroll-smooth">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?php echo htmlspecialchars(substr($project['description'], 0, 150)); ?>..." />
  <meta name="author" content="Adebisi Covenant" />
  <meta name="language" content="English">
  <meta name="revisit-after" content="1 days">
  <meta name="author" content="Adebisi Covenant">
  <meta name="robots" content="index, follow">
  <meta name="geo.region" content="NG" />
  <meta name="geo.placename" content="Lagos" />
  <meta name="geo.position" content="6.5244;3.3792" />
  <meta name="ICBM" content="6.5244, 3.3792" />

  <meta property="og:title" content="<?php echo htmlspecialchars($project['title']); ?> · Case Study by Adebisi Covenant" />
  <meta property="og:type" content="article" />
  <meta property="og:locale" content="en_US"/>
  <meta property="og:url" content="https://harkone.com.ng"/>
  <meta property="og:description" content="<?php echo htmlspecialchars(substr($project['description'], 0, 150)); ?>..."/>
  <meta property="og:image" content="https://harkone.com.ng/images/sm.png"/>

  <meta property="twitter:card" content="summary"/>
  <meta property="twitter:title" content="<?php echo htmlspecialchars($project['title']); ?> · Case Study by Adebisi Covenant"/>
  <meta property="twitter:description" content="<?php echo htmlspecialchars(substr($project['description'], 0, 150)); ?>..."/>
  <meta property="twitter:url" content="https://harkone.com.ng"/>
  <meta property="twitter:image" content="https://harkone.com.ng/images/sm.png"/>

  <link rel="shortcut icon" type="image/x-icon" href="https://harkone.com.ng/images/favicon.png">
  <title><?php echo htmlspecialchars($project['title']); ?> · Case Study - Adebisi Covenant</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500&display=swap"
    rel="stylesheet" />
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: { display: ['PT Sans', 'sans-serif'], body: ['DM Sans', 'sans-serif'] },
          colors: { accent: '#0b94baff', 'accent-light': '#2dd902ff' }
        }
      }
    }
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

    /* Reading progress */
    .progress-bar {
      position: fixed;
      top: 0;
      left: 0;
      height: 3px;
      background: #0b94ba;
      z-index: 100;
      transition: width .1s linear;
    }

    /* Reveal */
    .reveal {
      opacity: 0;
      transform: translateY(22px);
      transition: opacity .55s cubic-bezier(.4, 0, .2, 1), transform .55s cubic-bezier(.4, 0, .2, 1);
    }

    .reveal.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .d1 {
      transition-delay: .07s
    }

    .d2 {
      transition-delay: .14s
    }

    .d3 {
      transition-delay: .21s
    }

    .d4 {
      transition-delay: .28s
    }

    /* Case study prose */
    .prose-cs h2 {
      font-family: 'PT Sans', sans-serif;
      font-size: 1.55rem;
      font-weight: 700;
      margin: 2.75rem 0 1rem;
      line-height: 1.25;
    }

    .prose-cs h3 {
      font-family: 'PT Sans', sans-serif;
      font-size: 1.15rem;
      font-weight: 700;
      margin: 2rem 0 .6rem;
      line-height: 1.3;
      color: #0b94ba;
    }

    .prose-cs p {
      margin: 0 0 1.4rem;
      line-height: 1.82;
      color: #52525b;
    }

    .dark .prose-cs p {
      color: #a1a1aa;
    }

    .prose-cs ul {
      margin: 0 0 1.4rem 1.5rem;
      list-style: disc;
    }

    .prose-cs li {
      margin-bottom: .45rem;
      line-height: 1.7;
      color: #52525b;
    }

    .dark .prose-cs li {
      color: #a1a1aa;
    }

    .prose-cs blockquote {
      border-left: 3px solid #0b94ba;
      padding: .75rem 1.25rem;
      margin: 0 0 1.8rem;
      background: #fff7f4;
      border-radius: 0 8px 8px 0;
    }

    .dark .prose-cs blockquote {
      background: #27272a;
    }

    .prose-cs blockquote p {
      color: #52525b;
      font-style: italic;
      margin: 0;
    }

    .dark .prose-cs blockquote p {
      color: #a1a1aa;
    }

    .prose-cs hr {
      border: none;
      border-top: 1px solid #e4e4e7;
      margin: 2.5rem 0;
    }

    .dark .prose-cs hr {
      border-top-color: #27272a;
    }

    .prose-cs a {
      color: #0b94ba;
      text-decoration: underline;
      text-underline-offset: 3px;
    }

    /* Stat card */
    .stat-card {
      border-left: 3px solid #0b94ba;
    }

    /* Image caption */
    .img-caption {
      font-size: .8rem;
      color: #a1a1aa;
      text-align: center;
      margin-top: .6rem;
    }

    /* Step badge */
    .step-badge {
      width: 2rem;
      height: 2rem;
      min-width: 2rem;
      border-radius: 50%;
      background: #0b94ba;
      color: #fff;
      font-family: 'PT Sans', sans-serif;
      font-weight: 700;
      font-size: .85rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* card hover */
    .card-h {
      transition: transform .28s cubic-bezier(.4, 0, .2, 1), border-color .18s;
    }

    .card-h:hover {
      transform: translateY(-4px);
    }
  </style>
</head>

<body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-body antialiased">

  <!-- Reading progress bar -->
  <div class="progress-bar" :style="`width:${progress}%`" role="progressbar" :aria-valuenow="progress" aria-valuemin="0"
    aria-valuemax="100" aria-label="Reading progress"></div>

  <!-- ═══ NAV ═══ -->
  <header class="fixed top-0.5 left-0 right-0 z-50 transition-all duration-300"
    :class="scrolled ? 'bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md shadow-sm shadow-black/5' : 'bg-transparent'">
    <nav class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="Main navigation">
      <a href="./" class="font-display font-bold text-xl tracking-tight z-10">
        <span class="text-zinc-900 dark:text-white">ade</span><span class="text-accent">bisi</span>
      </a>
      <ul class="hidden md:flex items-center gap-8 text-sm font-medium" role="list">
        <li><a href="./#services"
            class="nav-link text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Services</a>
        </li>
        <li><a href="projects" class="nav-link text-accent font-medium">Work</a></li>
        <li><a href="./#about"
            class="nav-link text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">About</a>
        </li>
        <li><a href="blog"
            class="nav-link text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Blog</a>
        </li>
        <li><a href="./#contact"
            class="nav-link text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Contact</a>
        </li>
      </ul>
      <div class="flex items-center gap-3">
        <button @click="dark=!dark"
          class="w-9 h-9 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
          :aria-label="dark ? 'Light mode' : 'Dark mode'">
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
        <li><a href="projects" @click="mobileMenu=false" class="block text-accent font-medium">Work</a></li>
        <li><a href="./#about" @click="mobileMenu=false" class="block text-zinc-700 dark:text-zinc-300">About</a></li>
        <li><a href="blog" @click="mobileMenu=false" class="block text-zinc-700 dark:text-zinc-300">Blog</a></li>
        <li><a href="./#contact" @click="mobileMenu=false" class="block text-zinc-700 dark:text-zinc-300">Contact</a>
        </li>
      </ul>
    </div>
  </header>

  <main>
    <?php //if ($project['slug'] !== 'zerta-hq'): ?>
      <!-- DYNAMIC CASE STUDY -->
      <article>
        <header class="pt-32 pb-10 max-w-4xl mx-auto px-6">
          <!-- Back link -->
          <a href="projects"
            class="inline-flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 hover:text-accent transition-colors mb-8">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
              aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
            </svg>
            Back to projects
          </a>

          <!-- Tags + date -->
          <div class="flex flex-wrap items-center gap-3 mb-6">
            <?php 
            $tags = array_map('trim', explode(',', $project['tags']));
            foreach ($tags as $tag): ?>
              <span class="text-xs bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 px-2.5 py-1 rounded-full"><?php echo htmlspecialchars($tag); ?></span>
            <?php endforeach; ?>
            <span class="text-xs text-zinc-400 ml-1"><?php echo htmlspecialchars($project['year']); ?></span>
          </div>

          <h1
            class="reveal font-display font-bold text-4xl md:text-5xl lg:text-6xl text-zinc-900 dark:text-white leading-tight mb-6">
            <?php echo htmlspecialchars($project['title']); ?>
          </h1>

          <p class="reveal d1 text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed mb-10 max-w-2xl">
            <?php echo htmlspecialchars($project['description']); ?>
          </p>

          <!-- Meta row -->
          <div
            class="reveal d2 grid grid-cols-2 md:grid-cols-4 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900">
            <div>
              <p class="text-xs text-zinc-400 uppercase tracking-widest mb-1">Project</p>
              <p class="font-medium text-zinc-900 dark:text-white text-sm"><?php echo htmlspecialchars($project['category_label']); ?></p>
            </div>
            <div>
              <p class="text-xs text-zinc-400 uppercase tracking-widest mb-1">Role</p>
              <p class="font-medium text-zinc-900 dark:text-white text-sm">Creator / Partner</p>
            </div>
            <div>
              <p class="text-xs text-zinc-400 uppercase tracking-widest mb-1">Year</p>
              <p class="font-medium text-zinc-900 dark:text-white text-sm"><?php echo htmlspecialchars($project['year']); ?></p>
            </div>
            <div>
              <p class="text-xs text-zinc-400 uppercase tracking-widest mb-1">Platform</p>
              <p class="font-medium text-zinc-900 dark:text-white text-sm">Web Application</p>
            </div>
          </div>
        </header>

        <!-- ═══ HERO IMAGE ═══ -->
        <div class="max-w-6xl mx-auto px-6 mb-16">
          <div class="reveal photo-frame w-full h-72 md:h-[480px] rounded-3xl">
            <img src="<?php echo htmlspecialchars($project['image_url']); ?>"
              alt="<?php echo htmlspecialchars($project['title']); ?>" loading="eager" />
          </div>
        </div>

        <!-- ═══ ARTICLE BODY ═══ -->
        <div class="max-w-3xl mx-auto px-6 pb-16 prose-cs">
          <?php if (!empty($project['content'])): ?>
            <?php echo $project['content']; ?>
          <?php else: ?>
            <h2>About the project</h2>
            <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
          <?php endif; ?>
          
          <?php if (!empty($project['project_url']) && strpos($project['project_url'], 'http') === 0): ?>
            <hr />
            <p>
              <a href="<?php echo htmlspecialchars($project['project_url']); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-accent font-medium hover:underline">
                Visit Live Project &rarr;
              </a>
            </p>
          <?php endif; ?>
        </div>
      </article>
    <?php //else: ?>
      
    <?php //endif; ?>

    <!-- ═══ MORE PROJECTS ═══ -->
    <section class="bg-zinc-50 dark:bg-zinc-900/50 py-16">
      <div class="max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between mb-8">
          <h2 class="font-display font-bold text-2xl text-zinc-900 dark:text-white">More projects</h2>
          <a href="projects" class="text-sm font-medium text-accent nav-link hidden md:block">View all →</a>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
          <?php foreach ($moreProjects as $relP): ?>
            <article
              class="reveal card-h group bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden border border-zinc-100 dark:border-zinc-800 hover:border-accent transition-colors">
              <div class="photo-frame w-full h-44">
                <img src="<?php echo htmlspecialchars($relP['image_url']); ?>" alt="<?php echo htmlspecialchars($relP['title']); ?>"
                  loading="lazy" />
              </div>
              <div class="p-5">
                <div class="flex gap-2 mb-2">
                  <span
                    class="text-xs bg-zinc-100 dark:bg-zinc-800 text-accent border dark:border-zinc-700 px-2.5 py-1 rounded-full"><?php echo htmlspecialchars($relP['category_label']); ?></span>
                </div>
                <a href="case-study?slug=<?php echo urlencode($relP['slug']); ?>">
                  <h3
                    class="font-display font-bold text-base text-zinc-900 dark:text-white mb-2 group-hover:text-accent transition-colors">
                    <?php echo htmlspecialchars($relP['title']); ?></h3>
                </a>
                <a href="case-study?slug=<?php echo urlencode($relP['slug']); ?>" class="text-sm font-medium text-accent nav-link">View project →</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
        <div class="text-center mt-8 md:hidden">
          <a href="projects" class="text-sm font-medium text-accent">View all projects →</a>
        </div>
      </div>
    </section>

    <!-- ═══ CTA ═══ -->
    <section class="py-24">
      <div class="max-w-6xl mx-auto px-6">
        <div class="bg-zinc-900 dark:bg-zinc-800 rounded-3xl p-10 md:p-14 text-center relative overflow-hidden">
          <div class="absolute top-0 right-0 w-64 h-64 bg-accent/20 rounded-full blur-3xl pointer-events-none"
            aria-hidden="true"></div>
          <div class="absolute bottom-0 left-0 w-40 h-40 bg-accent/10 rounded-full blur-2xl pointer-events-none"
            aria-hidden="true"></div>
          <div class="relative z-10">
            <p class="text-xs font-medium text-accent tracking-widest uppercase mb-4">Ready to start?</p>
            <h2 class="font-display font-bold text-3xl md:text-4xl text-white mb-4">Got a similar project?</h2>
            <p class="text-zinc-400 max-w-md mx-auto mb-8">I'm available for new projects. Tell me about what you're
              building and let's see if we're a good fit.</p>
            <a href="./#contact"
              class="inline-flex items-center gap-2 btn-primary bg-accent text-white font-medium px-8 py-3.5 rounded-full hover:bg-accent-light transition-colors">
              Start a project →
            </a>
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include 'includes/footer.php'; ?>

  <script>
    

    // Reveal on scroll
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); }
      });
    }, { threshold: .08, rootMargin: '0px 0px -30px 0px' });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
  </script>

</body>

</html>