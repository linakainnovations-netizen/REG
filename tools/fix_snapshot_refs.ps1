# Post-fix snapshot HTML: manifest link + absolute api form action
$ErrorActionPreference = "Stop"
$docs = Join-Path $PSScriptRoot ".." | Join-Path -ChildPath "docs"
Get-ChildItem (Join-Path $docs "*.html") | ForEach-Object {
  $p = $_.FullName
  $h = [System.IO.File]::ReadAllText($p)
  $h = $h -replace '/St_Charles_Lwanga_Regiment_Portal/manifest\.json', 'manifest.json'
  $h = $h -replace '<form action="/St_Charles_Lwanga_Regiment_Portal/api/auth"', '<form action="#" data-demo-login'
  [System.IO.File]::WriteAllText($p, $h, (New-Object System.Text.UTF8Encoding $false))
}
$left = (Select-String -Path (Join-Path $docs "*.html") -Pattern 'St_Charles_Lwanga_Regiment_Portal/').Count
Write-Host "remaining old-base refs in docs html: $left"
