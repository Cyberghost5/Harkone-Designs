<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: ./");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $pdo = get_db_connection();
            $stmt = $pdo->prepare("SELECT * FROM `admins` WHERE `username` = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin['username'];
                header("Location: ./");
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Error connecting to database: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Adebisi Covenant</title>
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
    h1 {
      font-family: 'PT Sans', sans-serif;
    }
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: 0;
      opacity: .05;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E")
    }
  </style>
</head>
<body class="bg-zinc-950 text-zinc-100 flex items-center justify-center min-h-full overflow-hidden relative">

  <!-- Ambient light glow rings -->
  <div class="absolute w-[450px] h-[450px] bg-accent/20 rounded-full blur-[100px] top-[-100px] left-[-100px] pointer-events-none"></div>
  <div class="absolute w-[450px] h-[450px] bg-accent-light/15 rounded-full blur-[100px] bottom-[-150px] right-[-150px] pointer-events-none"></div>

  <div class="w-full max-w-md p-6 relative z-10">
    <div class="text-center mb-8">
      <a href="../" class="inline-block font-display font-bold text-3xl tracking-tight text-white mb-2">
        ade<span class="text-accent">bisi</span><span class="text-xs font-light text-zinc-500 uppercase tracking-widest ml-2">portal</span>
      </a>
      <p class="text-zinc-400 text-sm">Sign in to manage your projects and blogs.</p>
    </div>

    <!-- Glassmorphic Card -->
    <div class="bg-zinc-900/65 backdrop-blur-xl border border-zinc-800 rounded-3xl p-8 shadow-2xl">
      
      <?php if (!empty($error)): ?>
        <div class="mb-5 bg-red-950/40 border border-red-800 text-red-300 text-sm px-4 py-3 rounded-2xl flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <span><?php echo htmlspecialchars($error); ?></span>
        </div>
      <?php endif; ?>

      <form action="login" method="POST" class="space-y-6">
        <div>
          <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Username</label>
          <input type="text" id="username" name="username" required 
            class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
            placeholder="e.g. admin">
        </div>

        <div>
          <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Password</label>
          <input type="password" id="password" name="password" required 
            class="w-full bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent transition-colors"
            placeholder="••••••••">
        </div>

        <button type="submit" 
          class="w-full py-3.5 bg-accent text-white font-medium text-sm rounded-2xl hover:bg-accent-light hover:shadow-lg hover:shadow-accent/10 active:scale-[0.98] transition-all duration-200">
          Sign In
        </button>
      </form>
    </div>

    <div class="text-center mt-6">
      <a href="../" class="text-xs text-zinc-500 hover:text-accent transition-colors">&larr; Back to Portfolio</a>
    </div>
  </div>

</body>
</html>
