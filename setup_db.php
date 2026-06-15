<?php
header('Content-Type: text/plain');

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

try {
    // 1. Establish connection to MySQL server (without specifying DB first to create if missing)
    $pdo = get_db_connection();
    
    echo "Connecting to database server...\n";
    
    // Create Database
    $dbName = DB_NAME;
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database `$dbName` verified/created successfully.\n";
    
    // Reconnect selecting the target database
    $pdo->exec("USE `$dbName`");
    
    // 2. Create tables
    echo "Creating tables...\n";
    
    // Admins Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admins` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password_hash` VARCHAR(255) NOT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "- Table 'admins' verified.\n";
    
    // Blogs Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `blogs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `excerpt` TEXT NOT NULL,
        `content` LONGTEXT NOT NULL,
        `image_url` VARCHAR(255) NOT NULL,
        `category` VARCHAR(100) NOT NULL,
        `reading_time` INT DEFAULT 5,
        `status` ENUM('draft', 'published') DEFAULT 'published',
        `created_at` DATETIME NOT NULL,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "- Table 'blogs' verified.\n";
    
    // Projects Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `projects` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `description` TEXT NOT NULL,
        `category` VARCHAR(100) NOT NULL,
        `category_label` VARCHAR(100) NOT NULL,
        `tags` TEXT NOT NULL, -- comma separated tags
        `year` VARCHAR(10) NOT NULL,
        `image_url` VARCHAR(255) NOT NULL,
        `project_url` VARCHAR(255) NOT NULL,
        `content` LONGTEXT NULL, -- Detailed content for case studies if dynamic
        `status` ENUM('draft', 'published') DEFAULT 'published',
        `is_featured` TINYINT(1) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "- Table 'projects' verified.\n";
    
    // Migration for existing tables: add is_featured if not present
    try {
        $pdo->exec("ALTER TABLE `projects` ADD COLUMN `is_featured` TINYINT(1) DEFAULT 0 AFTER `status`");
        echo "- Migration: Column 'is_featured' added to 'projects'.\n";
    } catch (PDOException $e) {
        // Ignored if column already exists
    }
    
    // Messages Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `subject` VARCHAR(255) NULL,
        `message` TEXT NOT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "- Table 'messages' verified.\n";
    
    // 3. Seed initial admin account
    $adminUser = ADMIN_USER;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `admins` WHERE `username` = ?");
    $stmt->execute([$adminUser]);
    if ($stmt->fetchColumn() == 0) {
        $passwordHash = password_hash(DEFAULT_ADMIN_PASS, PASSWORD_BCRYPT);
        $insertAdmin = $pdo->prepare("INSERT INTO `admins` (`username`, `password_hash`) VALUES (?, ?)");
        $insertAdmin->execute([$adminUser, $passwordHash]);
        echo "Seeded default admin user: '$adminUser' with password '" . DEFAULT_ADMIN_PASS . "'\n";
    } else {
        echo "Admin user '$adminUser' already exists.\n";
    }
    
    // 4. Seed initial projects
    $projects = [
        [
            'title' => 'Zerta HQ - Software Outsourcing & Dev',
            'slug' => 'zerta-hq',
            'description' => 'Zerta delivers end-to-end software projects and embeds senior engineers into your team - scoped clearly, built in sprints, and shipped on time. We build your product, you keep the focus. We developed a custom web interface that enables seamless communication, real-time scoping, and efficient resource allocation for software outsourcing teams worldwide.',
            'category' => 'saas',
            'category_label' => 'SaaS',
            'tags' => 'SaaS,Web Design,Outsourcing',
            'year' => '2024',
            'image_url' => 'images/projects/zerta.png',
            'project_url' => 'case-study?slug=zerta-hq',
            'content' => '' // We can populate this later or load static file
        ],
        [
            'title' => 'Zeeroh - Event Finder',
            'slug' => 'zeeroh',
            'description' => 'Discover Events in Nigeria. A clean, interactive portal designed to display and find popular social events.',
            'category' => 'landing',
            'category_label' => 'Landing page',
            'tags' => 'Landing page,Events,Web Design',
            'year' => '2024',
            'image_url' => 'images/works/harkone_1776950258.gif',
            'project_url' => 'https://harkone.com.ng',
            'content' => ''
        ],
        [
            'title' => 'Achaba - Ride Booking',
            'slug' => 'achaba',
            'description' => 'A proposed booking solution built for cities like Bauchi, where the road does not always reach you. Book verified motorcycle riders directly from your doorstep.',
            'category' => 'agency',
            'category_label' => 'Agency',
            'tags' => 'Agency,Logistics,Booking',
            'year' => '2024',
            'image_url' => 'images/works/harkone_1776950169.gif',
            'project_url' => 'https://achaba.ng',
            'content' => ''
        ],
        [
            'title' => 'Governor Crest Limited',
            'slug' => 'governor-crest',
            'description' => 'Governor Crest Limited is a multi-sector company driven by innovation and integrity, operating across real estate, agriculture, and logistics.',
            'category' => 'landing',
            'category_label' => 'Landing page',
            'tags' => 'Landing page,Corporate,Multi-Sector',
            'year' => '2024',
            'image_url' => 'images/works/harkone_1776950125.gif',
            'project_url' => 'https://governorcrestlimited.com/',
            'content' => ''
        ],
        [
            'title' => 'Bauchi Pearl Magazine',
            'slug' => 'bauchi-pearl',
            'description' => 'Bauchi Pearl Entertainment and Lifestyle Magazine is an apparatus for propagation of business ideas, entertainment, and lifestyle in Northern Nigeria.',
            'category' => 'agency',
            'category_label' => 'Agency',
            'tags' => 'Agency,Lifestyle,Business',
            'year' => '2024',
            'image_url' => 'images/works/harkone_1776950060.gif',
            'project_url' => 'https://harkone.com.ng',
            'content' => ''
        ],
        [
            'title' => 'GrowthEngine AI',
            'slug' => 'growthengine-ai',
            'description' => 'AI-Powered Business Automation portal. Accelerate your tech career with premium courses in Cybersecurity, DevOps, and Cloud Computing.',
            'category' => 'saas',
            'category_label' => 'SaaS',
            'tags' => 'SaaS,AI,Automation',
            'year' => '2024',
            'image_url' => 'images/works/harkone_1776949992.gif',
            'project_url' => 'https://growthengineai.org/',
            'content' => ''
        ],
        [
            'title' => 'Flexifybook',
            'slug' => 'flexifybook',
            'description' => 'Streamline project management, organize tasks, collaborate seamlessly, and track progress effortlessly with Flexifybook.',
            'category' => 'saas',
            'category_label' => 'SaaS',
            'tags' => 'SaaS,Management,Productivity',
            'year' => '2023',
            'image_url' => 'images/works/harkone_1764080112.png',
            'project_url' => 'https://flexifybook.com/',
            'content' => ''
        ],
        [
            'title' => 'NMillenium VTU',
            'slug' => 'nmillenium-vtu',
            'description' => 'VTU website and app making bills payment, airtime purchase, and data subscription easy and affordable for Nigerian users.',
            'category' => 'ecommerce',
            'category_label' => 'E-commerce',
            'tags' => 'E-commerce,VTU,Bills Payment',
            'year' => '2023',
            'image_url' => 'images/works/harkone_1764079984.png',
            'project_url' => 'https://nmilleniumresource.com.ng/',
            'content' => ''
        ]
    ];
    
    $checkProj = $pdo->prepare("SELECT COUNT(*) FROM `projects` WHERE `slug` = ?");
    $insertProj = $pdo->prepare("INSERT INTO `projects` (`title`, `slug`, `description`, `category`, `category_label`, `tags`, `year`, `image_url`, `project_url`, `content`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $seededProjectsCount = 0;
    foreach ($projects as $p) {
        $checkProj->execute([$p['slug']]);
        if ($checkProj->fetchColumn() == 0) {
            $insertProj->execute([
                $p['title'],
                $p['slug'],
                $p['description'],
                $p['category'],
                $p['category_label'],
                $p['tags'],
                $p['year'],
                $p['image_url'],
                $p['project_url'],
                $p['content']
            ]);
            $seededProjectsCount++;
        }
    }
    echo "Seeded $seededProjectsCount projects.\n";
    
    // 5. Seed initial blogs
    $blogs = [
        [
            'title' => 'BetaLink | Connecting the world',
            'slug' => 'betalink-connecting-the-world',
            'excerpt' => 'BetaLink is a custom web platform built to connect remote teams, clients, and partners seamlessly with instant messaging and video features. Here is how we built it and launched it successfully.',
            'content' => '<h2>Connecting remote teams worldwide</h2>
<p>In mid-2022, we recognized a significant gap in how small to medium enterprises handled remote communication. Most tools were either too expensive or too bloated for startup teams that simply needed quick, reliable, and secure messaging and video calls. That\'s when we conceptualized and built BetaLink.</p>

<blockquote>
  <p>"The goal of BetaLink was simple: strip away the bloat and build a lightning-fast, secure connection portal that teams actually enjoy using."</p>
</blockquote>

<h2>The technology behind BetaLink</h2>
<p>Building a real-time communications application required a highly optimized stack. We decided to leverage modern technologies to keep the client bundle lightweight and the connection latency minimal:</p>
<ul>
  <li><strong>WebSockets &amp; WebRTC:</strong> Used for peer-to-peer real-time communication, ensuring instant message delivery and low-latency audio/video feeds.</li>
  <li><strong>Tailwind CSS:</strong> Kept our style sheets small (less than 10KB) and allowed us to construct a fully responsive, clean layout in record time.</li>
  <li><strong>PHP Backend:</strong> Powered our API, authentication, and secure database management.</li>
</ul>

<h2>Impact and Reception</h2>
<p>After launching the beta version of BetaLink, the portal gained traction with several local and international teams. We recorded a 40% increase in daily active users within the first month and successfully connected teams across continents with zero downtime.</p>

<hr />
<p><em>Interested in custom web development? Reach out to me via the contact form to discuss your project!</em></p>',
            'image_url' => 'images/blog/BetaLink%20_%20Connecting%20the%20world_1656176516.png',
            'category' => 'Web development',
            'reading_time' => 5,
            'created_at' => '2022-05-31 10:00:00'
        ],
        [
            'title' => 'Two weeks web design & development class',
            'slug' => 'two-weeks-web-design-development-class',
            'excerpt' => 'Teaching student developers the core principles of responsive layouts, semantic markup, and deployment.',
            'content' => '<h2>Empowering the Next Generation of Developers</h2>
<p>Our recent two-week intensive class focused on taking students from basic HTML syntax to deploying fully functional, responsive websites. We emphasized semantic structures, accessible layouts, and version control with Git.</p>
<h2>Core Pillars of the Program</h2>
<ul>
  <li><strong>Semantic Markup:</strong> Understanding why elements like section, article, and nav are critical for accessibility and SEO.</li>
  <li><strong>Modern CSS:</strong> Moving beyond styling basics to flexbox, grid layouts, and clean visual structures.</li>
  <li><strong>Deployment:</strong> Demystifying hosting, domain routing, and the build pipeline.</li>
</ul>',
            'image_url' => 'images/blog/Two%20weeks%20web%20design%20and%20development%20class_1645257324.png',
            'category' => 'Web development',
            'reading_time' => 4,
            'created_at' => '2022-02-19 14:30:00'
        ],
        [
            'title' => 'Harkone Designs - A brand new start',
            'slug' => 'harkone-designs-brand-new-start',
            'excerpt' => 'Reflecting on our design agency\'s journey, rebranding efforts, and the new visual standards set for 2022.',
            'content' => '<h2>Reflecting on Our Journey</h2>
<p>As we entered 2022, Harkone Designs underwent a complete rebranding. Our focus shifted towards delivering highly responsive, glassmorphic interfaces that offer optimal performance alongside stunning visuals.</p>
<h2>New Visual Standards</h2>
<p>We established a design system centered around dark mode compatibility, crisp gradients, and modern typography to redefine our visual identity. We are excited about what lies ahead in the coming months.</p>',
            'image_url' => 'images/blog/Harkone%20Designs%20-%20A%20new%20start_1641955813.png',
            'category' => 'Review',
            'reading_time' => 3,
            'created_at' => '2022-01-12 09:15:00'
        ]
    ];
    
    $checkBlog = $pdo->prepare("SELECT COUNT(*) FROM `blogs` WHERE `slug` = ?");
    $insertBlog = $pdo->prepare("INSERT INTO `blogs` (`title`, `slug`, `excerpt`, `content`, `image_url`, `category`, `reading_time`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $seededBlogsCount = 0;
    foreach ($blogs as $b) {
        $checkBlog->execute([$b['slug']]);
        if ($checkBlog->fetchColumn() == 0) {
            $insertBlog->execute([
                $b['title'],
                $b['slug'],
                $b['excerpt'],
                $b['content'],
                $b['image_url'],
                $b['category'],
                $b['reading_time'],
                $b['created_at']
            ]);
            $seededBlogsCount++;
        }
    }
    echo "Seeded $seededBlogsCount blog posts.\n";
    
    // Create uploads directory if it doesn't exist
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
        echo "Uploads directory created successfully: " . UPLOAD_DIR . "\n";
    }
    
    echo "\nDatabase Setup Completed Successfully!\n";
    
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage() . "\n");
}
?>
