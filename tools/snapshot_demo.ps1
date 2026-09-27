# Snapshot real localhost pages into docs/ (pixel-identical frontend, no backend)
# Run from the project root: powershell -ExecutionPolicy Bypass -File tools/snapshot_demo.ps1
$ErrorActionPreference = "Stop"
$base = "http://localhost/St_Charles_Lwanga_Regiment_Portal"
$docs = Join-Path $PSScriptRoot ".." | Join-Path -ChildPath "docs"
$docs = (Resolve-Path $docs).Path

$routes = @{
  "home" = "index.html"; "login" = "login.html"; "forgot_password" = "forgot_password.html";
  "register" = "register.html"; "join" = "join.html"; "contact" = "contact.html";
  "groups" = "groups.html"; "announcements" = "announcements.html"; "roq" = "roq.html";
  "ministries" = "ministries.html"; "pledges" = "pledges.html"; "offertory" = "offertory.html";
  "sunday_collection" = "sunday_collection.html"; "singing_cycle" = "singing_cycle.html";
  "youth" = "youth.html"; "submit_announcement" = "submit_announcement.html"; "rosters" = "rosters.html";
  "verify_event" = "verify_event.html"; "event_request" = "event_request.html"; "media" = "media.html";
  "giving" = "giving.html"; "giving_callback" = "giving_callback.html"; "live-updates" = "live-updates.html";
  "bulletins" = "bulletins.html"; "events" = "events.html"
}

$inject = @'
<!-- STATIC DEMO INJECTION (preview only: backend stripped, frontend unchanged) -->
<div id="demo-strip" style="background:#f59e0b;color:#0f172a;font-weight:700;font-size:.8rem;padding:.45rem 1rem;text-align:center;position:sticky;top:0;z-index:10000;">STATIC PREVIEW &middot; password for all accounts: <b>REGIMENT</b> &middot; <a href="#" id="demo-accts" style="text-decoration:underline;color:#0f172a;">demo accounts</a></div>
<div id="demo-panel" style="display:none;position:fixed;top:48px;right:12px;z-index:10001;background:#fff;border:1px solid #e2e8f0;border-radius:.8rem;box-shadow:0 20px 60px rgba(0,0,0,.25);padding:1rem;max-width:300px;max-height:70vh;overflow:auto;"></div>
<div id="demo-toast" style="display:none;position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:10002;background:#0f172a;color:#fff;padding:.8rem 1.2rem;border-radius:.6rem;font-size:.9rem;box-shadow:0 10px 30px rgba(0,0,0,.3);"></div>
<script src="js/demo-data.js"></script>
<script src="js/snapshot-demo.js"></script>
'@

foreach ($route in $routes.Keys) {
  $file = $routes[$route]
  Write-Host "Fetching $route -> $file"
  $url = if ($route -eq "home") { "$base/" } else { "$base/$route" }
  $res = Invoke-WebRequest -Uri $url -UseBasicParsing
  $html = $res.Content

  # 1. Strip service-worker registration (would cache stale demo on Pages)
  $html = [regex]::Replace($html, '<!-- PWA Service Worker Registration -->[\s\S]*?</script>', '', 'IgnoreCase')

  # 2a. Absolute asset/manifest URLs -> relative (MUST run before route rewrite)
  $html = $html -replace '/St_Charles_Lwanga_Regiment_Portal/assets/', 'assets/'
  $html = $html -replace '/St_Charles_Lwanga_Regiment_Portal/dashboard/', 'dashboard/'
  $html = $html -replace '/St_Charles_Lwanga_Regiment_Portal/manifest\.json', 'manifest.json'
  $html = $html -replace '<form action="/St_Charles_Lwanga_Regiment_Portal/api/auth"', '<form action="#" data-demo-login'

  # 2b. Absolute portal route links -> static files (only bare routes remain now)
  $html = [regex]::Replace($html, '/St_Charles_Lwanga_Regiment_Portal/([A-Za-z_\-]+)(?=["''\?#])', {
    param($m)
    $r = $m.Groups[1].Value
    if ($r -eq 'dashboard') { return 'dashboard.html' }
    if ($r -eq 'logout') { return 'login.html' }
    if ($r -eq 'home') { return 'index.html' }
    if ($routes.ContainsKey($r)) { return "$r.html" }
    return "$r.html"
  })

  # 2c. Bare relative route links in body markup: href="register" -> href="register.html"
  $routeNames = ((@($routes.Keys) + @('home','dashboard','logout')) -join '|')
  $html = [regex]::Replace($html, 'href="(' + $routeNames + ')"', {
    param($m)
    $r = $m.Groups[1].Value
    if ($r -eq 'dashboard') { return 'href="dashboard.html"' }
    if ($r -eq 'logout') { return 'href="login.html"' }
    if ($r -eq 'home') { return 'href="index.html"' }
    return 'href="' + $r + '.html"'
  })

  # 3. Login form -> demo handler hook
  $html = $html -replace '<form action="api/auth"', '<form action="#" data-demo-login'

  # 4. Inject demo strip + scripts before </body>
  $html = $html -replace '</body>', ($inject + '</body>')

  $out = Join-Path $docs $file
  # Preserve UTF8 (pages contain no BOM originally; keep as-is)
  [System.IO.File]::WriteAllText($out, $html, (New-Object System.Text.UTF8Encoding $false))
  Write-Host "  saved $out ($($html.Length) chars)"
}
Write-Host "DONE."
