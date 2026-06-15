<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db_connection();

// Query platform metrics
$totalBlogs = $pdo->query("SELECT COUNT(*) FROM `blogs`")->fetchColumn();
$publishedBlogs = $pdo->query("SELECT COUNT(*) FROM `blogs` WHERE `status` = 'published'")->fetchColumn();

$totalProjects = $pdo->query("SELECT COUNT(*) FROM `projects`")->fetchColumn();
$publishedProjects = $pdo->query("SELECT COUNT(*) FROM `projects` WHERE `status` = 'published'")->fetchColumn();

// Fetch latest items for review
$stmtBlogs = $pdo->query("SELECT * FROM `blogs` ORDER BY `created_at` DESC LIMIT 3");
$recentBlogs = $stmtBlogs->fetchAll();

$stmtProj = $pdo->query("SELECT * FROM `projects` ORDER BY `id` DESC LIMIT 3");
$recentProjects = $stmtProj->fetchAll();

$totalMessages = $pdo->query("SELECT COUNT(*) FROM `messages`")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en" class="dark h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Adebisi Covenant</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=PT+Sans:wght@700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet" />
  
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
    body {
      font-family: 'DM Sans', sans-serif;
    }
    h1, h2, h3 {
      font-family: 'PT Sans', sans-serif;
    }
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: 0;
      opacity: .04;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E")
    }
  </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-full pb-12 relative">

  <!-- Ambient light glow rings -->
  <div class="absolute w-[500px] h-[500px] bg-accent/10 rounded-full blur-[120px] top-[-150px] right-[-100px] pointer-events-none"></div>
  <div class="absolute w-[400px] h-[400px] bg-accent-light/5 rounded-full blur-[100px] bottom-[50px] left-[-100px] pointer-events-none"></div>

  <!-- NAVIGATION HEADER -->
  <header class="border-b border-zinc-900 bg-zinc-950/80 backdrop-blur-md sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
      <a href="./" class="font-display font-bold text-xl tracking-tight text-white flex items-center">
        ade<span class="text-accent">bisi</span>
        <span class="bg-zinc-900 text-zinc-500 border border-zinc-800 text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full">admin</span>
      </a>
      
      <nav class="flex items-center gap-6 text-sm font-medium">
        <a href="./" class="text-accent">Dashboard</a>
        <a href="blogs.php" class="text-zinc-400 hover:text-white transition-colors">Blogs</a>
        <a href="projects.php" class="text-zinc-400 hover:text-white transition-colors">Works</a>
        <a href="messages.php" class="text-zinc-400 hover:text-white transition-colors">Messages</a>
        <a href="../" target="_blank" class="text-zinc-500 hover:text-zinc-300 text-xs transition-colors flex items-center gap-1 border-l border-zinc-800 pl-6">
          View Site
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
        </a>
        <a href="logout.php" class="text-red-400 hover:text-red-300 text-xs transition-colors bg-red-950/20 border border-red-900/30 px-3 py-1 rounded-full">
          Logout
        </a>
      </nav>
    </div>
  </header>

  <!-- DASHBOARD CONTENT -->
  <main class="max-w-6xl mx-auto px-6 pt-10 relative z-10">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-white tracking-tight">Overview</h1>
        <p class="text-zinc-400 text-sm mt-1">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
      </div>
      
      <!-- Quick Action Buttons -->
      <div class="flex gap-3">
        <a href="edit-blog.php" class="bg-zinc-900 border border-zinc-800 hover:border-accent text-zinc-100 hover:text-white text-sm font-medium px-5 py-2.5 rounded-2xl flex items-center gap-2 transition-all">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          New Blog
        </a>
        <a href="edit-project.php" class="bg-accent text-white hover:bg-accent-light text-sm font-medium px-5 py-2.5 rounded-2xl flex items-center gap-2 hover:shadow-lg hover:shadow-accent/10 transition-all">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          New Project
        </a>
      </div>
    </div>

    <!-- STATS GRID -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      <!-- Blog Widget -->
      <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-3xl p-6 shadow-xl flex items-center justify-between">
        <div>
          <span class="text-zinc-500 text-xs font-semibold uppercase tracking-wider">Blog Posts</span>
          <h2 class="text-5xl font-bold text-white mt-2"><?php echo $totalBlogs; ?></h2>
          <p class="text-xs text-zinc-400 mt-2">
            <span class="text-accent"><?php echo $publishedBlogs; ?></span> published · 
            <span class="text-zinc-500"><?php echo ($totalBlogs - $publishedBlogs); ?></span> drafts
          </p>
        </div>
        <div class="w-14 h-14 bg-accent/10 border border-accent/20 text-accent rounded-2xl flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-5M19 20a2 2 0 01-2-2v-1M15 4h3a2 2 0 012 2v2M8 12h4m-4 4h8" />
          </svg>
        </div>
      </div>

      <!-- Works Widget -->
      <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-3xl p-6 shadow-xl flex items-center justify-between">
        <div>
          <span class="text-zinc-500 text-xs font-semibold uppercase tracking-wider">Works & Projects</span>
          <h2 class="text-5xl font-bold text-white mt-2"><?php echo $totalProjects; ?></h2>
          <p class="text-xs text-zinc-400 mt-2">
            <span class="text-accent-light"><?php echo $publishedProjects; ?></span> visible · 
            <span class="text-zinc-500"><?php echo ($totalProjects - $publishedProjects); ?></span> hidden
          </p>
        </div>
        <div class="w-14 h-14 bg-accent-light/10 border border-accent-light/20 text-accent-light rounded-2xl flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2" />
          </svg>
        </div>
      </div>

      <!-- Messages Widget -->
      <a href="messages" class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 hover:border-accent/40 transition-colors rounded-3xl p-6 shadow-xl flex items-center justify-between group">
        <div>
          <span class="text-zinc-500 text-xs font-semibold uppercase tracking-wider group-hover:text-zinc-400 transition-colors">Messages & Inquiries</span>
          <h2 class="text-5xl font-bold text-white mt-2"><?php echo $totalMessages; ?></h2>
          <p class="text-xs text-zinc-400 mt-2">
            Click to view contact submissions
          </p>
        </div>
        <div class="w-14 h-14 bg-red-950/20 border border-red-900/30 group-hover:border-red-800/40 text-red-400 rounded-2xl flex items-center justify-center transition-all">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
      </a>
    </section>

    <!-- CONTENT MANAGEMENT SPLIT -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      
      <!-- Recent Blogs -->
      <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-3xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-bold text-white">Recent Blog Posts</h3>
          <a href="blogs.php" class="text-xs text-accent hover:text-accent-light font-medium transition-colors">Manage All &rarr;</a>
        </div>
        
        <div class="space-y-4">
          <?php if (empty($recentBlogs)): ?>
            <p class="text-zinc-500 text-sm py-4 text-center">No blogs created yet.</p>
          <?php else: ?>
            <?php foreach ($recentBlogs as $blog): ?>
              <div class="flex items-center justify-between p-4 bg-zinc-950 rounded-2xl border border-zinc-900/60 hover:border-zinc-800 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="w-10 h-10 rounded-xl overflow-hidden bg-zinc-900 shrink-0">
                    <img src="../<?php echo htmlspecialchars($blog['image_url']); ?>" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=100&q=80'">
                  </div>
                  <div class="min-w-0">
                    <h4 class="text-sm font-semibold text-white truncate"><?php echo htmlspecialchars($blog['title']); ?></h4>
                    <p class="text-zinc-500 text-xs mt-0.5"><?php echo date("M j, Y", strtotime($blog['created_at'])); ?></p>
                  </div>
                </div>
                
                <div class="flex items-center gap-3 shrink-0 ml-4">
                  <span class="text-[10px] px-2 py-0.5 rounded-full border <?php echo $blog['status'] === 'published' ? 'bg-green-950/20 border-green-800/40 text-green-400' : 'bg-zinc-900 border-zinc-800 text-zinc-400'; ?>">
                    <?php echo ucfirst($blog['status']); ?>
                  </span>
                  <a href="edit-blog.php?id=<?php echo $blog['id']; ?>" class="text-xs text-zinc-400 hover:text-accent font-medium transition-colors">Edit</a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Recent Projects -->
      <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-3xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-bold text-white">Recent Works</h3>
          <a href="projects.php" class="text-xs text-accent hover:text-accent-light font-medium transition-colors">Manage All &rarr;</a>
        </div>
        
        <div class="space-y-4">
          <?php if (empty($recentProjects)): ?>
            <p class="text-zinc-500 text-sm py-4 text-center">No projects added yet.</p>
          <?php else: ?>
            <?php foreach ($recentProjects as $proj): ?>
              <div class="flex items-center justify-between p-4 bg-zinc-950 rounded-2xl border border-zinc-900/60 hover:border-zinc-800 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="w-10 h-10 rounded-xl overflow-hidden bg-zinc-900 shrink-0">
                    <img src="../<?php echo htmlspecialchars($proj['image_url']); ?>" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1551650975-87deedd944c3?w=100&q=80'">
                  </div>
                  <div class="min-w-0">
                    <h4 class="text-sm font-semibold text-white truncate"><?php echo htmlspecialchars($proj['title']); ?></h4>
                    <p class="text-zinc-500 text-xs mt-0.5"><?php echo htmlspecialchars($proj['category_label']); ?> · <?php echo htmlspecialchars($proj['year']); ?></p>
                  </div>
                </div>
                
                <div class="flex items-center gap-3 shrink-0 ml-4">
                  <span class="text-[10px] px-2 py-0.5 rounded-full border <?php echo $proj['status'] === 'published' ? 'bg-green-950/20 border-green-800/40 text-green-400' : 'bg-zinc-900 border-zinc-800 text-zinc-400'; ?>">
                    <?php echo $proj['status'] === 'published' ? 'Visible' : 'Hidden'; ?>
                  </span>
                  <a href="edit-project.php?id=<?php echo $proj['id']; ?>" class="text-xs text-zinc-400 hover:text-accent font-medium transition-colors">Edit</a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

    </section>
  </main>

</body>
</html>
