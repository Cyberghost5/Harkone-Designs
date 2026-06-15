<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db_connection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$blog = null;
$error = '';
$success = '';

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM `blogs` WHERE `id` = ?");
    $stmt->execute([$id]);
    $blog = $stmt->fetch();
    if (!$blog) {
        header("Location: blogs.php");
        exit;
    }
}

// Slugify helper
function slugify($text) {
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    if (function_exists('iconv')) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $category = trim($_POST['category'] ?? 'Web development');
    $reading_time = (int)($_POST['reading_time'] ?? 5);
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $created_at = $_POST['created_at'] ?? date('Y-m-d H:i:s');
    
    if (empty($slug)) {
        $slug = slugify($title);
    } else {
        $slug = slugify($slug);
    }
    
    if (empty($title) || empty($content)) {
        $error = 'Title and Content are required.';
    } else {
        $uploadedImageUrl = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $destPath = UPLOAD_DIR . $newFileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $uploadedImageUrl = 'images/uploads/' . $newFileName;
                    
                    // Delete old uploaded image if replacing
                    if ($blog && $blog['image_url'] && strpos($blog['image_url'], 'images/uploads/') !== false && file_exists(__DIR__ . '/../' . $blog['image_url'])) {
                        @unlink(__DIR__ . '/../' . $blog['image_url']);
                    }
                }
            } else {
                $error = 'Invalid file extension. Allowed: jpg, jpeg, png, gif, webp.';
            }
        }
        
        if (empty($error)) {
            $finalImageUrl = !empty($uploadedImageUrl) ? $uploadedImageUrl : ($blog ? $blog['image_url'] : 'images/blog/placeholder.png');
            
            try {
                if ($blog) {
                    $upStmt = $pdo->prepare("UPDATE `blogs` SET `title` = ?, `slug` = ?, `excerpt` = ?, `content` = ?, `image_url` = ?, `category` = ?, `reading_time` = ?, `status` = ?, `created_at` = ? WHERE `id` = ?");
                    $upStmt->execute([$title, $slug, $excerpt, $content, $finalImageUrl, $category, $reading_time, $status, $created_at, $id]);
                    $success = 'Blog post updated successfully.';
                    
                    // Refresh local data
                    $stmt = $pdo->prepare("SELECT * FROM `blogs` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    $blog = $stmt->fetch();
                } else {
                    $insStmt = $pdo->prepare("INSERT INTO `blogs` (`title`, `slug`, `excerpt`, `content`, `image_url`, `category`, `reading_time`, `status`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insStmt->execute([$title, $slug, $excerpt, $content, $finalImageUrl, $category, $reading_time, $status, $created_at]);
                    header("Location: blogs.php?msg=created");
                    exit;
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = 'Duplicate slug value. Please set a custom slug or change the title.';
                } else {
                    $error = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $blog ? 'Edit' : 'Create'; ?> Blog - Adebisi Covenant</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=PT+Sans:wght@700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet" />
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  
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
    body { font-family: 'DM Sans', sans-serif; }
    h1, h2, h3 { font-family: 'PT Sans', sans-serif; }
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

  <!-- Ambient glow -->
  <div class="absolute w-[450px] h-[450px] bg-accent/10 rounded-full blur-[100px] top-[-100px] right-[-100px] pointer-events-none"></div>

  <!-- HEADER -->
  <header class="border-b border-zinc-900 bg-zinc-950/80 backdrop-blur-md sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
      <a href="./" class="font-display font-bold text-xl tracking-tight text-white flex items-center">
        ade<span class="text-accent">bisi</span>
        <span class="bg-zinc-900 text-zinc-500 border border-zinc-800 text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full">admin</span>
      </a>
      
      <nav class="flex items-center gap-6 text-sm font-medium">
        <a href="./" class="text-zinc-400 hover:text-white transition-colors">Dashboard</a>
        <a href="blogs.php" class="text-accent">Blogs</a>
        <a href="projects.php" class="text-zinc-400 hover:text-white transition-colors">Works</a>
        <a href="messages.php" class="text-zinc-400 hover:text-white transition-colors">Messages</a>
        <a href="../" target="_blank" class="text-zinc-500 hover:text-zinc-300 text-xs transition-colors flex items-center gap-1 border-l border-zinc-800 pl-6">
          View Site
        </a>
      </nav>
    </div>
  </header>

  <!-- FORM CONTAINER -->
  <main class="max-w-6xl mx-auto px-6 pt-10 relative z-10" x-data="{ 
    title: '<?php echo $blog ? addslashes($blog['title']) : ''; ?>', 
    slug: '<?php echo $blog ? addslashes($blog['slug']) : ''; ?>',
    autoSlug: <?php echo $blog ? 'false' : 'true'; ?>,
    slugify(text) {
      return text.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_]+/g, '-')
        .replace(/^-+|-+$/g, '');
    }
  }">
    <div class="flex items-center gap-4 mb-8">
      <a href="blogs.php" class="w-10 h-10 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white hover:border-zinc-700 transition-colors">
        &larr;
      </a>
      <div>
        <h1 class="text-3xl font-bold text-white tracking-tight"><?php echo $blog ? 'Edit Post' : 'Create New Post'; ?></h1>
        <p class="text-zinc-400 text-sm mt-1"><?php echo $blog ? 'Updating: ' . htmlspecialchars($blog['title']) : 'Draft a new publication.'; ?></p>
      </div>
    </div>

    <!-- Alert notes -->
    <?php if (!empty($error)): ?>
      <div class="mb-6 bg-red-950/40 border border-red-800 text-red-300 text-sm px-4 py-3 rounded-2xl flex items-center">
        <span><?php echo htmlspecialchars($error); ?></span>
      </div>
    <?php elseif (!empty($success)): ?>
      <div class="mb-6 bg-green-950/20 border border-green-800 text-green-300 text-sm px-4 py-3 rounded-2xl flex items-center">
        <span><?php echo htmlspecialchars($success); ?></span>
      </div>
    <?php endif; ?>

    <form action="edit-blog<?php echo $blog ? '?id=' . $blog['id'] : ''; ?>" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Primary Editor Column (2/3 width) -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800 rounded-3xl p-6 space-y-6 shadow-xl">
          
          <!-- Title -->
          <div>
            <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Title</label>
            <input type="text" id="title" name="title" required x-model="title" @input="if (autoSlug) { slug = slugify(title) }"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
              placeholder="e.g. BetaLink | Connecting the world">
          </div>

          <!-- Slug -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="slug" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400">URL Slug</label>
              <div class="flex items-center gap-1.5 text-xs text-zinc-500">
                <input type="checkbox" id="auto-slug" x-model="autoSlug" @change="if (autoSlug) { slug = slugify(title) }">
                <label for="auto-slug" class="cursor-pointer select-none">Auto-generate</label>
              </div>
            </div>
            <input type="text" id="slug" name="slug" x-model="slug" @input="autoSlug = false"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
              placeholder="e.g. betalink-connecting-the-world">
          </div>

          <!-- Excerpt -->
          <div>
            <label for="excerpt" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Excerpt</label>
            <textarea id="excerpt" name="excerpt" rows="3"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors resize-y"
              placeholder="A short summary of the article shown in listings..."><?php echo $blog ? htmlspecialchars($blog['excerpt']) : ''; ?></textarea>
          </div>

          <!-- Content Body -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="content" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400">Content (HTML / Markdown)</label>
              <span class="text-[10px] text-zinc-500 bg-zinc-950 px-2 py-0.5 border border-zinc-800 rounded-md">Raw HTML supported</span>
            </div>
            <textarea id="content" name="content" required rows="15"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 font-mono rounded-2xl px-4 py-4 text-xs focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors resize-y"
              placeholder="<h2>Subheading</h2><p>Article body content here...</p>"><?php echo $blog ? htmlspecialchars($blog['content']) : ''; ?></textarea>
          </div>

        </div>
      </div>

      <!-- Sidebar Column (1/3 width) -->
      <div class="space-y-6">
        
        <!-- Publishing Parameters -->
        <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800 rounded-3xl p-6 space-y-6 shadow-xl">
          <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 border-b border-zinc-800 pb-3">Publishing Details</h3>
          
          <!-- Status -->
          <div>
            <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-2">Status</label>
            <select id="status" name="status"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors">
              <option value="draft" <?php echo ($blog && $blog['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
              <option value="published" <?php echo (!$blog || $blog['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
            </select>
          </div>

          <!-- Category -->
          <div>
            <label for="category" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-2">Category</label>
            <input type="text" id="category" name="category" required
              value="<?php echo $blog ? htmlspecialchars($blog['category']) : 'Web development'; ?>"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors"
              placeholder="e.g. Web development">
          </div>

          <!-- Reading Time -->
          <div>
            <label for="reading_time" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-2">Reading Time (mins)</label>
            <input type="number" id="reading_time" name="reading_time" required min="1" max="120"
              value="<?php echo $blog ? intval($blog['reading_time']) : 5; ?>"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors">
          </div>

          <!-- Created Date -->
          <div>
            <label for="created_at" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-2">Created Date</label>
            <input type="text" id="created_at" name="created_at" required
              value="<?php echo $blog ? htmlspecialchars($blog['created_at']) : date('Y-m-d H:i:s'); ?>"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors"
              placeholder="YYYY-MM-DD HH:MM:SS">
          </div>
        </div>

        <!-- Image Upload -->
        <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800 rounded-3xl p-6 space-y-6 shadow-xl">
          <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 border-b border-zinc-800 pb-3">Cover Image</h3>
          
          <!-- Image preview -->
          <?php if ($blog && !empty($blog['image_url'])): ?>
            <div class="relative w-full h-40 bg-zinc-950 rounded-2xl overflow-hidden border border-zinc-800">
              <img src="../<?php echo htmlspecialchars($blog['image_url']); ?>" class="w-full h-full object-cover">
              <span class="absolute top-2 left-2 text-[9px] uppercase bg-black/60 backdrop-blur-sm px-2 py-0.5 rounded border border-zinc-800">Current</span>
            </div>
          <?php endif; ?>

          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-2">Upload Image</label>
            <input type="file" name="image" accept="image/*"
              class="block w-full text-xs text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-zinc-950 file:text-zinc-300 hover:file:bg-zinc-800 file:cursor-pointer">
            <p class="text-[10px] text-zinc-500 mt-2">Recommended: 16:9 ratio (jpg, png, gif, webp)</p>
          </div>
        </div>

        <!-- Submit actions -->
        <div class="space-y-3">
          <button type="submit"
            class="w-full py-3.5 bg-accent text-white font-medium text-sm rounded-2xl hover:bg-accent-light hover:shadow-lg hover:shadow-accent/10 active:scale-[0.99] transition-all duration-200">
            <?php echo $blog ? 'Save Updates' : 'Publish Article'; ?>
          </button>
          
          <a href="blogs.php" 
            class="block w-full py-3 text-center bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 text-zinc-400 hover:text-white font-medium text-sm rounded-2xl active:scale-[0.99] transition-all">
            Cancel
          </a>
        </div>

      </div>

    </form>
  </main>

</body>
</html>
