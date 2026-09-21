<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title') — Audit Platform</title>
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<header class="header">
  <div class="container header-inner">
    <a href="/" class="logo"><span class="logo-dot"></span> Audit Platform</a>
    <nav class="nav">
      <a href="/seo-audit">SEO Audit</a>
      <a href="/adsense-audit">AdSense Audit</a>
      <a href="/speed-audit">Speed Audit</a>
      <a href="/link-audit">Link Audit</a>
      <a href="/methodology">Methodology</a>
    </nav>
    <a href="/seo-audit" class="btn-primary">Run an audit</a>
  </div>
</header>
@yield('content')
<footer class="footer">
  <div class="container">
    <p>© 2026 Audit Platform · Independent analysis; no AI used.</p>
  </div>
</footer>
</body>
</html>
