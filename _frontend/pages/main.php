<?php
$v = "v9.5";
$version = 9;
$code = [
  "_frontend\pages\main.php",
  "_backend/_routes/test/sample.php",
  "_backend/auto/routing/api.php",
  "_backend/auto/routing/web.php",
  "_backend/core/command",
  "_backend/core/partials/classes/Routing.php",
  "_frontend/core/partials/fe.php"
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CodeTazer | The Most Basic PHP Framework</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" href="<?= assets('bfavicon.png') ?>" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;700;800&family=Orbitron:wght@400;500;700;900&display=swap');

    :root {
      --primary: #00ff88;
      --primary-glow: rgba(0, 255, 136, 0.4);
      --secondary: #0d0d0d;
      --accent: #00ccff;
    }

    body {
      background-color: var(--secondary);
      color: #fff;
      font-family: 'JetBrains Mono', monospace;
      background-image:
        radial-gradient(circle at 10% 20%, rgba(0, 255, 136, 0.05) 0%, transparent 20%),
        radial-gradient(circle at 90% 80%, rgba(0, 204, 255, 0.05) 0%, transparent 20%);
      overflow-x: hidden;
      min-height: 100vh;
    }

    .title-glow {
      text-shadow:
        0 0 5px var(--primary),
        0 0 15px var(--primary),
        0 0 25px var(--primary-glow),
        0 0 40px var(--primary-glow);
    }

    .pulse-glow {
      animation: pulse 3s infinite alternate;
    }

    @keyframes pulse {
      0% {
        box-shadow: 0 0 5px var(--primary);
      }

      100% {
        box-shadow: 0 0 15px var(--primary), 0 0 30px var(--primary-glow);
      }
    }

    .btn-primary {
      background: linear-gradient(135deg, rgba(0, 255, 136, 0.1) 0%, rgba(0, 255, 136, 0.05) 100%);
      border: 1px solid var(--primary);
      color: var(--primary);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, rgba(0, 255, 136, 0.2) 0%, rgba(0, 255, 136, 0.1) 100%);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 255, 136, 0.2);
    }

    .btn-primary::after {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.7s;
    }

    .btn-primary:hover::after {
      left: 100%;
    }

    .btn-secondary {
      background: linear-gradient(135deg, var(--primary) 0%, #00cc6a 100%);
      color: #000;
      font-weight: 700;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 20px rgba(0, 255, 136, 0.4);
    }

    .feature-card {
      background: rgba(13, 13, 13, 0.7);
      border: 1px solid rgba(0, 255, 136, 0.2);
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .feature-card:hover {
      border-color: var(--primary);
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 255, 136, 0.1);
    }

    .feature-icon {
      color: var(--primary);
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }

    .terminal-effect {
      position: relative;
      padding-left: 1rem;
      background: rgba(0, 0, 0, 0.2);
      padding: 1rem;
      border-radius: 0 5px 5px 0;
    }

    .scan-line {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, transparent, var(--primary), transparent);
      animation: scan 5s linear infinite;
    }

    @keyframes scan {
      0% {
        top: 0%;
      }

      100% {
        top: 100%;
      }
    }

    .footer-link {
      transition: all 0.3s ease;
    }

    .footer-link:hover {
      color: var(--primary);
      text-shadow: 0 0 5px var(--primary);
    }

    .logo {
      font-family: 'Orbitron', sans-serif;
      font-weight: 900;
    }

    /* Composer Installation Box */
    .composer-install {
      background: rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(0, 255, 136, 0.2);
      border-radius: 8px;
      padding: 1.5rem;
      margin-top: 2rem;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    .composer-install:hover {
      border-color: var(--primary);
      box-shadow: 0 0 15px rgba(0, 255, 136, 0.1);
    }

    .code-block {
      background: rgba(0, 0, 0, 0.5);
      border: 1px solid rgba(0, 255, 136, 0.1);
      border-radius: 6px;
      padding: 1rem;
      font-family: 'JetBrains Mono', monospace;
      font-size: 1.1rem;
      margin: 1rem 0;
      overflow-x: auto;
      position: relative;
    }

    .code-block::before {
      content: '$';
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--primary);
      font-weight: bold;
    }

    .code-block code {
      margin-left: 20px;
      color: #00ff88;
      display: block;
    }

    .copy-btn {
      background: rgba(0, 255, 136, 0.1);
      border: 1px solid rgba(0, 255, 136, 0.3);
      color: var(--primary);
      padding: 0.5rem 1rem;
      border-radius: 4px;
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-top: 0.5rem;
    }

    .copy-btn:hover {
      background: rgba(0, 255, 136, 0.2);
      transform: translateY(-2px);
    }

    .copy-btn.copied {
      background: rgba(0, 255, 136, 0.3);
      color: #fff;
    }

    /* Why Choose CodeTazer specific styles */
    .architecture-card {
      background: rgba(13, 13, 13, 0.8);
      border: 1px solid rgba(0, 255, 136, 0.3);
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .architecture-card:hover {
      border-color: var(--primary);
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 255, 136, 0.15);
    }

    .stack-badge {
      background: rgba(0, 255, 136, 0.1);
      border: 1px solid rgba(0, 255, 136, 0.3);
      color: var(--primary);
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .tech-stack {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-top: 1rem;
    }

    .deployment-badge {
      background: linear-gradient(135deg, rgba(0, 204, 255, 0.1) 0%, rgba(0, 255, 136, 0.1) 100%);
      border: 1px solid rgba(0, 204, 255, 0.3);
      color: #00ccff;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .section-title {
      position: relative;
      display: inline-block;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 100%;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--primary), transparent);
    }

    /* Core Benefits Grid */
    .benefit-card {
      background: rgba(13, 13, 13, 0.6);
      border: 1px solid rgba(0, 255, 136, 0.2);
      border-radius: 8px;
      padding: 1.5rem;
      transition: all 0.3s ease;
    }

    .benefit-card:hover {
      border-color: var(--primary);
      transform: translateY(-3px);
    }
  </style>
</head>

<body class="relative">
  <!-- Scan line animation -->
  <div class="scan-line"></div>

  <main class="container mx-auto px-4 py-12 flex flex-col justify-center min-h-screen">
    <div class="text-center mb-12">
      <div class="logo title-glow text-5xl md:text-7xl lg:text-8xl font-black mb-4 pulse-glow inline-block py-8 rounded-lg">
        ⚡ CodeTazer <?= $v ?> ⚡
      </div>
      <div class="terminal-effect max-w-2xl mx-auto mb-8">
        <p class="text-green-300 text-lg md:text-xl mb-4">The upgraded version of Basixs</p>
        <p class="text-xl md:text-2xl text-green-200">
          The most basic PHP framework <br><span class="text-cyan-300 font-bold">light</span>, <span class="text-cyan-300 font-bold">secure</span>, <span class="text-cyan-300 font-bold">fast</span> and <span class="text-cyan-300 font-bold">beginner friendly</span>.
        </p>
      </div>

      <div class="flex flex-wrap justify-center gap-6 mt-10">
        <a href="https://github.com/YroDevGit/basixs" class="btn-primary px-8 py-3 rounded-lg font-bold text-lg flex items-center gap-2">
          <i class="fab fa-github"></i> Visit Repository
        </a>
        <a href="https://github.com/YroDevGit/basixs/archive/refs/heads/main.zip" class="btn-secondary px-8 py-3 rounded-lg text-lg flex items-center gap-2">
          <i class="fas fa-download"></i> Download Now
        </a>
      </div>

      <!-- Composer Installation Section -->
      <div class="composer-install mt-8">
        <div class="text-center mb-4">
          <h3 class="text-xl font-bold text-green-300 flex items-center justify-center gap-2">
            <i class="fas fa-terminal"></i> Install via Composer
          </h3>
          <p class="text-gray-300 text-sm mt-2">Get started quickly with Composer installation</p>
        </div>

        <div class="code-block">
          <code>composer create-project yrodevgit/codetazer ctrapp</code>
        </div>

        <button class="copy-btn mx-auto" onclick="copyToClipboard()">
          <i class="far fa-copy"></i> Copy Command
        </button>
      </div>
    </div>

    <section class="max-w-6xl mx-auto mb-20">
      <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-green-300 title-glow">What's New in <?= $v ?>?</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="feature-card p-6">
          <div class="feature-icon">
            <i class="fas fa-box"></i>
          </div>
          <h3 class="text-xl font-bold mb-3 text-cyan-300">Composer</h3>
          <p class="text-gray-300">Integrated Composer support for easy dependency management and package installation.</p>
        </div>

        <div class="feature-card p-6">
          <div class="feature-icon">
            <i class="fas fa-layer-group"></i>
          </div>
          <h3 class="text-xl font-bold mb-3 text-cyan-300">Collection</h3>
          <p class="text-gray-300">Enhanced collection utilities for working with arrays and datasets more efficiently.</p>
        </div>

        <div class="feature-card p-6">
          <div class="feature-icon">
            <i class="fas fa-tools"></i>
          </div>
          <h3 class="text-xl font-bold mb-3 text-cyan-300">Built-in Tools</h3>
          <p class="text-gray-300">A suite of developer tools included out-of-the-box for faster development cycles.</p>
        </div>

        <div class="feature-card p-6">
          <div class="feature-icon">
            <i class="fas fa-database"></i>
          </div>
          <h3 class="text-xl font-bold mb-3 text-cyan-300">PostgreSQL</h3>
          <p class="text-gray-300">Native PostgreSQL support alongside MySQL for enterprise-grade database applications.</p>
        </div>

        <div class="feature-card p-6">
          <div class="feature-icon">
            <i class="fas fa-server"></i>
          </div>
          <h3 class="text-xl font-bold mb-3 text-cyan-300">MongoDB</h3>
          <p class="text-gray-300">NoSQL MongoDB integration for flexible, document-oriented data storage solutions.</p>
        </div>

        <div class="feature-card p-6">
          <div class="feature-icon">
            <i class="fas fa-bolt"></i>
          </div>
          <h3 class="text-xl font-bold mb-3 text-cyan-300">Performance</h3>
          <p class="text-gray-300">Optimized core for faster execution and lower memory footprint than previous versions.</p>
        </div>
      </div>
    </section>

    <!-- Enhanced Why Choose CodeTazer Section -->
    <section class="max-w-6xl mx-auto mb-20">
      <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-green-300 title-glow section-title">Why Choose CodeTazer?</h2>

      <!-- Core Benefits (Lightweight, Secure, Fast) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="benefit-card text-center">
          <div class="text-green-400 text-4xl mb-3">
            <i class="fas fa-feather-alt"></i>
          </div>
          <h3 class="text-xl font-bold mb-2 text-cyan-300">Lightweight</h3>
          <p class="text-gray-300">Minimal footprint with only essential components. No bloat, just pure performance.</p>
        </div>

        <div class="benefit-card text-center">
          <div class="text-green-400 text-4xl mb-3">
            <i class="fas fa-shield-alt"></i>
          </div>
          <h3 class="text-xl font-bold mb-2 text-cyan-300">Secure</h3>
          <p class="text-gray-300">Built-in security features and best practices. Protection against common vulnerabilities.</p>
        </div>

        <div class="benefit-card text-center">
          <div class="text-green-400 text-4xl mb-3">
            <i class="fas fa-rocket"></i>
          </div>
          <h3 class="text-xl font-bold mb-2 text-cyan-300">Fast</h3>
          <p class="text-gray-300">Optimized for performance and quick response times. Efficient code execution.</p>
        </div>
      </div>

      <!-- New Advanced Features -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
        <div class="architecture-card p-6">
          <div class="text-green-400 text-4xl mb-4">
            <i class="fas fa-sitemap"></i>
          </div>
          <h3 class="text-xl font-bold mb-3 text-cyan-300">Full-Stack Architecture</h3>
          <p class="text-gray-300 mb-4">Built-in fullstack features with hybrid PHP + JavaScript approach. Clean separation between frontend and backend.</p>
          <div class="tech-stack">
            <span class="stack-badge">PHP Backend</span>
            <span class="stack-badge">PHP Templates</span>
            <span class="stack-badge">JavaScript</span>
          </div>
        </div>

        <div class="architecture-card p-6">
          <div class="text-green-400 text-4xl mb-4">
            <i class="fas fa-code-branch"></i>
          </div>
          <h3 class="text-xl font-bold mb-3 text-cyan-300">Hybrid Development</h3>
          <p class="text-gray-300 mb-4">Separated folders for frontend and backend. Backend uses pure PHP while frontend combines PHP templates, CSS, and JavaScript.</p>
          <div class="tech-stack">
            <span class="stack-badge">Backend: PHP</span>
            <span class="stack-badge">Frontend: PHP+JS</span>
            <span class="stack-badge">Tyrax JS</span>
          </div>
        </div>

        <div class="architecture-card p-6">
          <div class="text-green-400 text-4xl mb-4">
            <i class="fas fa-bolt"></i>
          </div>
          <h3 class="text-xl font-bold mb-3 text-cyan-300">Powerful JavaScript</h3>
          <p class="text-gray-300 mb-4">Built-in JavaScript libraries with Tyrax - more powerful than AJAX with advanced features for modern web applications.</p>
          <div class="tech-stack">
            <span class="stack-badge">Tyrax JS</span>
            <span class="stack-badge">Api testing</span>
            <span class="stack-badge">Built-in Libs</span>
          </div>
        </div>
      </div>

      <!-- Deployment Features -->
      <div class="bg-black bg-opacity-50 border border-green-900 rounded-xl p-8">
        <div class="text-center mb-8">
          <h3 class="text-2xl font-bold mb-4 text-green-300">Easy Deployment & Modern Workflow</h3>
          <p class="text-gray-300 max-w-3xl mx-auto">CodeTazer is designed for modern deployment workflows with seamless Docker and Portainer integration.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="flex items-start space-x-4">
            <div class="text-green-400 text-2xl mt-1">
              <i class="fab fa-docker"></i>
            </div>
            <div>
              <h4 class="font-bold text-lg mb-2 text-cyan-300">Docker Ready</h4>
              <p class="text-gray-300">Pre-configured for Docker with optimized containers and easy setup for development and production environments.</p>
              <div class="tech-stack mt-3">
                <span class="deployment-badge">Docker</span>
                <span class="deployment-badge">Containers</span>
                <span class="deployment-badge">Optimized</span>
              </div>
            </div>
          </div>

          <div class="flex items-start space-x-4">
            <div class="text-green-400 text-2xl mt-1">
              <i class="fas fa-server"></i>
            </div>
            <div>
              <h4 class="font-bold text-lg mb-2 text-cyan-300">Portainer Integration</h4>
              <p class="text-gray-300">Easy deployment and management through Portainer with visual container management and monitoring.</p>
              <div class="tech-stack mt-3">
                <span class="deployment-badge">Portainer</span>
                <span class="deployment-badge">Easy Deploy</span>
                <span class="deployment-badge">Monitoring</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Beginner Friendly -->
        <div class="mt-8 pt-8 border-t border-gray-700">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="flex items-start space-x-4">
              <div class="text-green-400 text-2xl mt-1">
                <i class="fas fa-graduation-cap"></i>
              </div>
              <div>
                <h4 class="font-bold text-lg mb-2 text-cyan-300">Beginner Friendly</h4>
                <p class="text-gray-300">Easy to learn with comprehensive documentation, clear structure, and intuitive API design perfect for developers at any level.</p>
              </div>
            </div>

            <div class="flex items-start space-x-4">
              <div class="text-green-400 text-2xl mt-1">
                <i class="fas fa-cogs"></i>
              </div>
              <div>
                <h4 class="font-bold text-lg mb-2 text-cyan-300">Flexible & Scalable</h4>
                <p class="text-gray-300">Grow from small projects to enterprise applications with the same framework. Modular design allows easy scaling.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="border-t border-gray-800 pt-8 pb-6 text-center">
    <div class="container mx-auto px-4">
      <p class="text-green-500 mb-2">&copy; <?= date('Y') ?> CodeTazer Framework. Built with <span class="text-red-400">❤️</span> in PHP</p>
      <p class="text-gray-400 mb-6">by <a href="https://www.tiktok.com/@codebasixs" class="footer-link text-green-300 font-bold">CodeYro</a>.</p>

      <div class="flex justify-center gap-6 text-xl text-gray-500">
        <a href="https://github.com/YroDevGit/basixs" class="hover:text-green-300 transition-colors">
          <i class="fab fa-github"></i>
        </a>
        <a href="https://www.tiktok.com/@codebasixs" class="hover:text-green-300 transition-colors">
          <i class="fab fa-tiktok"></i>
        </a>
        <a href="#" class="hover:text-green-300 transition-colors">
          <i class="fas fa-globe"></i>
        </a>
        <a href="#" class="hover:text-green-300 transition-colors">
          <i class="fas fa-book"></i>
        </a>
      </div>
    </div>
  </footer>

  <script>
    function copyToClipboard() {
      const command = 'composer create-project yrodevgit/codetazer ctrapp';
      navigator.clipboard.writeText(command).then(() => {
        const btn = document.querySelector('.copy-btn');
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.add('copied');

        setTimeout(() => {
          btn.innerHTML = '<i class="far fa-copy"></i> Copy Command';
          btn.classList.remove('copied');
        }, 2000);
      });
    }
  </script>
</body>

</html>