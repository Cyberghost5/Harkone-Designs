<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db_connection();
$msg = '';

// Handle Delete request
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    try {
        // Fetch image url to delete from storage if wanted, or keep for other logs
        $imgStmt = $pdo->prepare("SELECT `image_url` FROM `blogs` WHERE `id` = ?");
        $imgStmt->execute([$deleteId]);
        $imgUrl = $imgStmt->fetchColumn();
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM `blogs` WHERE `id` = ?");
        $stmt->execute([$deleteId]);
        
        // Clean up file if it is in uploads directory
        if ($imgUrl && strpos($imgUrl, 'images/uploads/') !== false && file_exists(__DIR__ . '/../' . $imgUrl)) {
            @unlink(__DIR__ . '/../' . $imgUrl);
        }
        
        header("Location: blogs.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        $msg = 'Error deleting post: ' . $e->getMessage();
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $msg = 'Blog post deleted successfully.';
}

// Fetch all blogs
$stmt = $pdo->query("SELECT * FROM `blogs` ORDER BY `created_at` DESC");
$blogs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="dark h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Blogs - Adebisi Covenant</title>
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

  <!-- Ambient background glow -->
  <div class="absolute w-[450px] h-[450px] bg-accent/10 rounded-full blur-[100px] top-[-100px] right-[-100px] pointer-events-none"></div>

  <!-- HEADER -->
  <header class="border-b border-zinc-900 bg-zinc-950/80 backdrop-blur-md sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
      <a href="index.php" class="font-display font-bold text-xl tracking-tight text-white flex items-center gap-2">
        ade<span class="text-accent">bisi</span>
        <span class="bg-zinc-900 text-zinc-500 border border-zinc-800 text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full">admin</span>
      </a>
      
      <nav class="flex items-center gap-6 text-sm font-medium">
        <a href="index.php" class="text-zinc-400 hover:text-white transition-colors">Dashboard</a>
        <a href="blogs.php" class="text-accent">Blogs</a>
        <a href="projects.php" class="text-zinc-400 hover:text-white transition-colors">Works</a>
        <a href="messages.php" class="text-zinc-400 hover:text-white transition-colors">Messages</a>
        <a href="../" target="_blank" class="text-zinc-500 hover:text-zinc-300 text-xs transition-colors flex items-center gap-1 border-l border-zinc-800 pl-6">
          View Site
        </a>
        <a href="logout.php" class="text-red-400 hover:text-red-300 text-xs transition-colors bg-red-950/20 border border-red-900/30 px-3 py-1 rounded-full">
          Logout
        </a>
      </nav>
    </div>
  </header>

  <!-- CONTENT -->
  <main class="max-w-6xl mx-auto px-6 pt-10 relative z-10">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-white tracking-tight">Manage Blogs</h1>
        <p class="text-zinc-400 text-sm mt-1">Write, edit, publish, or remove blog posts.</p>
      </div>
      <a href="edit-blog.php" class="bg-accent text-white hover:bg-accent-light text-sm font-medium px-5 py-2.5 rounded-2xl flex items-center gap-2 hover:shadow-lg hover:shadow-accent/10 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Post
      </a>
    </div>

    <!-- Feedback alert -->
    <?php if (!empty($msg)): ?>
      <div class="mb-6 bg-zinc-900 border border-zinc-800 text-zinc-300 text-sm px-4 py-3 rounded-2xl flex items-center justify-between">
        <span><?php echo htmlspecialchars($msg); ?></span>
        <button onclick="this.parentElement.remove()" class="text-zinc-500 hover:text-white">&times;</button>
      </div>
    <?php endif; ?>

    <!-- Table Container -->
    <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800 rounded-3xl overflow-hidden shadow-xl">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-zinc-800 text-zinc-500 text-xs uppercase tracking-wider font-semibold">
              <th class="py-4 px-6">Article</th>
              <th class="py-4 px-6">Category</th>
              <th class="py-4 px-6">Reading Time</th>
              <th class="py-4 px-6">Date Created</th>
              <th class="py-4 px-6">Status</th>
              <th class="py-4 px-6 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-800/50 text-sm">
            <?php if (empty($blogs)): ?>
              <tr>
                <td colspan="6" class="py-12 text-center text-zinc-500">
                  No articles found. Click "New Post" to get started.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($blogs as $blog): ?>
                <tr class="hover:bg-zinc-900/35 transition-colors">
                  <td class="py-4 px-6 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-zinc-950 shrink-0 border border-zinc-800">
                      <img src="../<?php echo htmlspecialchars($blog['image_url']); ?>" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=100&q=80'">
                    </div>
                    <div class="min-w-0">
                      <p class="font-semibold text-white truncate max-w-xs md:max-w-md"><?php echo htmlspecialchars($blog['title']); ?></p>
                      <p class="text-zinc-500 text-xs truncate max-w-xs mt-0.5"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                    </div>
                  </td>
                  <td class="py-4 px-6 text-zinc-300">
                    <?php echo htmlspecialchars($blog['category']); ?>
                  </td>
                  <td class="py-4 px-6 text-zinc-400">
                    <?php echo intval($blog['reading_time']); ?> min read
                  </td>
                  <td class="py-4 px-6 text-zinc-400">
                    <?php echo date("M j, Y", strtotime($blog['created_at'])); ?>
                  </td>
                  <td class="py-4 px-6">
                    <span class="text-[10px] px-2 py-0.5 rounded-full border <?php echo $blog['status'] === 'published' ? 'bg-green-950/20 border-green-800/40 text-green-400' : 'bg-zinc-800 border-zinc-700 text-zinc-400'; ?>">
                      <?php echo ucfirst($blog['status']); ?>
                    </span>
                  </td>
                  <td class="py-4 px-6 text-right space-x-3">
                    <a href="edit-blog.php?id=<?php echo $blog['id']; ?>" class="text-accent hover:text-accent-light font-medium transition-colors">Edit</a>
                    <a href="blogs.php?delete=<?php echo $blog['id']; ?>" onclick="return confirm('Are you sure you want to delete this blog post? This action cannot be undone.')" class="text-red-400 hover:text-red-300 transition-colors">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

</body>
</html>
