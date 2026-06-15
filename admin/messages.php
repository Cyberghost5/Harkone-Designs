<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_db_connection();
$msg = '';

// Handle Delete request
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM `messages` WHERE `id` = ?");
        $stmt->execute([$deleteId]);
        header("Location: messages.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        $msg = 'Error deleting message: ' . $e->getMessage();
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $msg = 'Message deleted successfully.';
}

// Fetch all messages
$stmt = $pdo->query("SELECT * FROM `messages` ORDER BY `created_at` DESC");
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="dark h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Inquiries - Adebisi Covenant</title>
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
    [x-cloak] { display: none !important; }
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
        <a href="blogs.php" class="text-zinc-400 hover:text-white transition-colors">Blogs</a>
        <a href="projects.php" class="text-zinc-400 hover:text-white transition-colors">Works</a>
        <a href="messages.php" class="text-accent">Messages</a>
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
        <h1 class="text-3xl font-bold text-white tracking-tight">Contact Messages</h1>
        <p class="text-zinc-400 text-sm mt-1">View or delete incoming user contact submissions.</p>
      </div>
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
              <th class="py-4 px-6 w-1/4">Sender</th>
              <th class="py-4 px-6 w-1/4">Subject</th>
              <th class="py-4 px-6 w-1/3">Message</th>
              <th class="py-4 px-6 w-1/6">Date Received</th>
              <th class="py-4 px-6 text-right w-1/6">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-800/50 text-sm">
            <?php if (empty($messages)): ?>
              <tr>
                <td colspan="5" class="py-12 text-center text-zinc-500">
                  No messages found. Submissions will appear here when users submit the contact form.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($messages as $m): ?>
                <tr x-data="{ open: false }" class="hover:bg-zinc-900/35 transition-colors">
                  <td class="py-4 px-6 font-semibold text-white valign-top">
                    <?php echo htmlspecialchars($m['name']); ?>
                    <p class="text-zinc-500 text-xs font-normal mt-0.5"><?php echo htmlspecialchars($m['email']); ?></p>
                  </td>
                  <td class="py-4 px-6 text-zinc-300 valign-top">
                    <?php echo htmlspecialchars($m['subject'] ?: 'No Subject'); ?>
                  </td>
                  <td class="py-4 px-6 text-zinc-400 valign-top">
                    <div @click="open = !open" class="cursor-pointer hover:text-white transition-colors flex items-start gap-1.5 min-w-0">
                      <div class="min-w-0 flex-1">
                        <span class="block truncate" x-show="!open"><?php echo htmlspecialchars(substr($m['message'], 0, 70)) . (strlen($m['message']) > 70 ? '...' : ''); ?></span>
                        <span x-show="open" x-cloak class="whitespace-pre-wrap text-zinc-300 block font-mono text-xs max-w-lg leading-relaxed"><?php echo htmlspecialchars($m['message']); ?></span>
                      </div>
                      <svg class="w-4 h-4 text-zinc-600 group-hover:text-zinc-400 transition-colors shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" :class="open ? 'rotate-180' : ''">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </div>
                  </td>
                  <td class="py-4 px-6 text-zinc-400 valign-top">
                    <?php echo date("M j, Y H:i", strtotime($m['created_at'])); ?>
                  </td>
                  <td class="py-4 px-6 text-right space-x-3 whitespace-nowrap valign-top">
                    <a href="mailto:<?php echo htmlspecialchars($m['email']); ?>?subject=Re:%20<?php echo rawurlencode($m['subject']); ?>" class="text-accent hover:text-accent-light font-medium transition-colors">Reply</a>
                    <a href="messages.php?delete=<?php echo $m['id']; ?>" onclick="return confirm('Are you sure you want to delete this message? This action cannot be undone.')" class="text-red-400 hover:text-red-300 transition-colors">Delete</a>
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
