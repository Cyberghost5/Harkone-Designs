<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db_connection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$project = null;
$error = '';
$success = '';

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM `projects` WHERE `id` = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if (!$project) {
        header("Location: projects.php");
        exit;
    }
}

// Slugify helper
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    if (function_exists('iconv')) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? 'saas');
    $category_label = trim($_POST['category_label'] ?? 'SaaS');
    $tags = trim($_POST['tags'] ?? '');
    $year = trim($_POST['year'] ?? date('Y'));
    $project_url = trim($_POST['project_url'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'published';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    if (empty($slug)) {
        $slug = slugify($title);
    } else {
        $slug = slugify($slug);
    }
    
    if (empty($project_url)) {
        $project_url = 'case-study?slug=' . $slug;
    }
    
    if (empty($title) || empty($description)) {
        $error = 'Title and Short Description are required.';
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
                    if ($project && $project['image_url'] && strpos($project['image_url'], 'images/uploads/') !== false && file_exists(__DIR__ . '/../' . $project['image_url'])) {
                        @unlink(__DIR__ . '/../' . $project['image_url']);
                    }
                }
            } else {
                $error = 'Invalid file extension. Allowed: jpg, jpeg, png, gif, webp.';
            }
        }
        
        if (empty($error)) {
            // Default placeholder if none exists
            $finalImageUrl = !empty($uploadedImageUrl) ? $uploadedImageUrl : ($project ? $project['image_url'] : 'images/works/harkone_1776950258.gif');
            
            try {
                if ($project) {
                    $upStmt = $pdo->prepare("UPDATE `projects` SET `title` = ?, `slug` = ?, `description` = ?, `category` = ?, `category_label` = ?, `tags` = ?, `year` = ?, `image_url` = ?, `project_url` = ?, `content` = ?, `status` = ?, `is_featured` = ? WHERE `id` = ?");
                    $upStmt->execute([$title, $slug, $description, $category, $category_label, $tags, $year, $finalImageUrl, $project_url, $content, $status, $is_featured, $id]);
                    $success = 'Project updated successfully.';
                    
                    // Refresh local data
                    $stmt = $pdo->prepare("SELECT * FROM `projects` WHERE `id` = ?");
                    $stmt->execute([$id]);
                    $project = $stmt->fetch();
                } else {
                    $insStmt = $pdo->prepare("INSERT INTO `projects` (`title`, `slug`, `description`, `category`, `category_label`, `tags`, `year`, `image_url`, `project_url`, `content`, `status`, `is_featured`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insStmt->execute([$title, $slug, $description, $category, $category_label, $tags, $year, $finalImageUrl, $project_url, $content, $status, $is_featured]);
                    header("Location: projects.php?msg=created");
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
  <title><?php echo $project ? 'Edit' : 'Create'; ?> Project - Adebisi Covenant</title>
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
      <a href="index.php" class="font-display font-bold text-xl tracking-tight text-white flex items-center gap-2">
        ade<span class="text-accent">bisi</span>
        <span class="bg-zinc-900 text-zinc-500 border border-zinc-800 text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full">admin</span>
      </a>
      
      <nav class="flex items-center gap-6 text-sm font-medium">
        <a href="index.php" class="text-zinc-400 hover:text-white transition-colors">Dashboard</a>
        <a href="blogs.php" class="text-zinc-400 hover:text-white transition-colors">Blogs</a>
        <a href="projects.php" class="text-accent">Works</a>
        <a href="messages.php" class="text-zinc-400 hover:text-white transition-colors">Messages</a>
        <a href="../" target="_blank" class="text-zinc-500 hover:text-zinc-300 text-xs transition-colors flex items-center gap-1 border-l border-zinc-800 pl-6">
          View Site
        </a>
      </nav>
    </div>
  </header>

  <!-- FORM CONTAINER -->
  <main class="max-w-6xl mx-auto px-6 pt-10 relative z-10" x-data="{ 
    title: '<?php echo $project ? addslashes($project['title']) : ''; ?>', 
    slug: '<?php echo $project ? addslashes($project['slug']) : ''; ?>',
    autoSlug: <?php echo $project ? 'false' : 'true'; ?>,
    category: '<?php echo $project ? addslashes($project['category']) : 'saas'; ?>',
    category_label: '<?php echo $project ? addslashes($project['category_label']) : 'SaaS'; ?>',
    project_url: '<?php echo $project ? addslashes($project['project_url']) : ''; ?>',
    autoProjectUrl: <?php echo (!$project || strpos($project['project_url'], 'case-study?slug=') === 0) ? 'true' : 'false'; ?>,
    slugify(text) {
      return text.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_]+/g, '-')
        .replace(/^-+|-+$/g, '');
    },
    updateCategoryLabel() {
      const mapping = {
        'saas': 'SaaS',
        'landing': 'Landing page',
        'agency': 'Agency',
        'ecommerce': 'E-commerce'
      };
      this.category_label = mapping[this.category] || '';
    },
    updateProjectUrl() {
      if (this.autoProjectUrl) {
        this.project_url = 'case-study?slug=' + this.slug;
      }
    }
  }" x-init="
    $watch('title', v => { if (autoSlug) { slug = slugify(v); updateProjectUrl(); } });
    $watch('slug', v => { updateProjectUrl(); });
    $watch('autoProjectUrl', v => { updateProjectUrl(); });
  ">
    <div class="flex items-center gap-4 mb-8">
      <a href="projects.php" class="w-10 h-10 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white hover:border-zinc-700 transition-colors">
        &larr;
      </a>
      <div>
        <h1 class="text-3xl font-bold text-white tracking-tight"><?php echo $project ? 'Edit Project' : 'Create New Project'; ?></h1>
        <p class="text-zinc-400 text-sm mt-1"><?php echo $project ? 'Updating: ' . htmlspecialchars($project['title']) : 'Add a new work to your portfolio.'; ?></p>
      </div>
    </div>

    <!-- Alert notes -->
    <?php if (!empty($error)): ?>
      <div class="mb-6 bg-red-950/40 border border-red-800 text-red-300 text-sm px-4 py-3 rounded-2xl flex items-center gap-2">
        <span><?php echo htmlspecialchars($error); ?></span>
      </div>
    <?php elseif (!empty($success)): ?>
      <div class="mb-6 bg-green-950/20 border border-green-800 text-green-300 text-sm px-4 py-3 rounded-2xl flex items-center gap-2">
        <span><?php echo htmlspecialchars($success); ?></span>
      </div>
    <?php endif; ?>

    <form action="edit-project<?php echo $project ? '?id=' . $project['id'] : ''; ?>" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Primary Editor Column (2/3 width) -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800 rounded-3xl p-6 space-y-6 shadow-xl">
          
          <!-- Title -->
          <div>
            <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Project Title</label>
            <input type="text" id="title" name="title" required x-model="title"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
              placeholder="e.g. Zeeroh - Event Finder">
          </div>

          <!-- Slug -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="slug" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400">URL Slug</label>
              <div class="flex items-center gap-1.5 text-xs text-zinc-500">
                <input type="checkbox" id="auto-slug" x-model="autoSlug">
                <label for="auto-slug" class="cursor-pointer select-none">Auto-generate</label>
              </div>
            </div>
            <input type="text" id="slug" name="slug" x-model="slug" @input="autoSlug = false"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
              placeholder="e.g. zeeroh">
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Short Description</label>
            <textarea id="description" name="description" rows="3" required
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors resize-y"
              placeholder="A brief summary of the project shown on the listings page..."><?php echo $project ? htmlspecialchars($project['description']) : ''; ?></textarea>
          </div>

          <!-- Case Study Detailed Content -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="content" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400">Detailed Case Study Body (HTML/Markdown)</label>
              <span class="text-[10px] text-zinc-500 bg-zinc-950 px-2 py-0.5 border border-zinc-800 rounded-md">Raw HTML supported</span>
            </div>
            <textarea id="content" name="content" rows="15"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 font-mono rounded-2xl px-4 py-4 text-xs focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors resize-y"
              placeholder="<h2>Challenges & Solutions</h2><p>Provide details on how you solved the problem...</p>"><?php echo $project ? htmlspecialchars($project['content']) : ''; ?></textarea>
            <p class="text-[10px] text-zinc-500 mt-2">If left blank, the details page falls back to display the short description automatically.</p>
          </div>

        </div>
      </div>

      <!-- Sidebar Column (1/3 width) -->
      <div class="space-y-6">
        
        <!-- Project Details -->
        <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800 rounded-3xl p-6 space-y-6 shadow-xl">
          <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 border-b border-zinc-800 pb-3">Project Details</h3>
          
          <!-- Status -->
          <div>
            <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-2">Visibility Status</label>
            <select id="status" name="status"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors">
              <option value="published" <?php echo (!$project || $project['status'] === 'published') ? 'selected' : ''; ?>>Visible (Published)</option>
              <option value="draft" <?php echo ($project && $project['status'] === 'draft') ? 'selected' : ''; ?>>Hidden (Draft)</option>
            </select>
          </div>

          <!-- Featured Toggle -->
          <div class="flex items-center justify-between p-3 bg-zinc-950/40 rounded-xl border border-zinc-800/60">
            <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Featured Work</span>
            <div class="flex items-center">
              <input type="checkbox" id="is_featured" name="is_featured" value="1" <?php echo ($project && $project['is_featured']) ? 'checked' : ''; ?>
                class="w-4 h-4 text-accent bg-zinc-950 border-zinc-800 rounded focus:ring-accent focus:ring-1 focus:ring-offset-zinc-950">
            </div>
          </div>

          <!-- Category -->
          <div>
            <label for="category" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-2">Category Filter</label>
            <select id="category" name="category" x-model="category" @change="updateCategoryLabel()"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors">
              <option value="saas">SaaS</option>
              <option value="landing">Landing page</option>
              <option value="agency">Agency</option>
              <option value="ecommerce">E-commerce</option>
            </select>
          </div>

          <!-- Category Label Override -->
          <div>
            <label for="category_label" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-2">Category Label (Display)</label>
            <input type="text" id="category_label" name="category_label" required x-model="category_label"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors"
              placeholder="e.g. SaaS">
          </div>

          <!-- Year -->
          <div>
            <label for="year" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-2">Year / Timeline</label>
            <input type="text" id="year" name="year" required
              value="<?php echo $project ? htmlspecialchars($project['year']) : date('Y'); ?>"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors"
              placeholder="e.g. 2024">
          </div>

          <!-- Tags -->
          <div>
            <label for="tags" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-2">Tags (Comma-separated)</label>
            <input type="text" id="tags" name="tags"
              value="<?php echo $project ? htmlspecialchars($project['tags']) : ''; ?>"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors"
              placeholder="e.g. Landing page, Events, Web Design">
          </div>

          <!-- Project URL -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="project_url" class="block text-xs font-semibold uppercase tracking-wider text-zinc-500">Project Action Link</label>
              <div class="flex items-center gap-1.5 text-xs text-zinc-500">
                <input type="checkbox" id="auto-project-url" x-model="autoProjectUrl">
                <label for="auto-project-url" class="cursor-pointer select-none">Auto case-study</label>
              </div>
            </div>
            <input type="text" id="project_url" name="project_url" x-model="project_url" @input="autoProjectUrl = false"
              class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-colors"
              placeholder="e.g. case-study?slug=zeeroh">
            <p class="text-[10px] text-zinc-500 mt-2">Use the default case-study link to use the built-in layout, or insert an external web address (e.g. https://domain.com).</p>
          </div>
        </div>

        <!-- Image Upload -->
        <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800 rounded-3xl p-6 space-y-6 shadow-xl">
          <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 border-b border-zinc-800 pb-3">Cover Image</h3>
          
          <!-- Image preview -->
          <?php if ($project && !empty($project['image_url'])): ?>
            <div class="relative w-full h-40 bg-zinc-950 rounded-2xl overflow-hidden border border-zinc-800">
              <img src="../<?php echo htmlspecialchars($project['image_url']); ?>" class="w-full h-full object-cover">
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
            <?php echo $project ? 'Save Updates' : 'Add Project'; ?>
          </button>
          
          <a href="projects.php" 
            class="block w-full py-3 text-center bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 text-zinc-400 hover:text-white font-medium text-sm rounded-2xl active:scale-[0.99] transition-all">
            Cancel
          </a>
        </div>

      </div>

    </form>
  </main>

</body>
</html>
