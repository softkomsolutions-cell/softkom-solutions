param(
    [string]$Output = ".\softkom-live-production-bundle.zip"
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
$deploy = Join-Path $env:TEMP 'softkom-live-production-bundle'
$mu = Join-Path $deploy 'wp-content\mu-plugins'

Remove-Item $deploy -Recurse -Force -ErrorAction SilentlyContinue
New-Item $mu -ItemType Directory -Force | Out-Null

$files = @(
    'softkom-public-acquisition.php',
    'softkom-public-acquisition.js',
    'softkom-strategy-request.php',
    'softkom-sales-notifications.php',
    'softkom-industry-funnel.php',
    'softkom-industry-funnel.js'
)

foreach ($file in $files) {
    $source = Join-Path $root ("wp-content\mu-plugins\" + $file)
    if (-not (Test-Path $source)) {
        throw "Required deployment file is missing: $source"
    }
    Copy-Item $source -Destination $mu -Force
}

if (Test-Path $Output) {
    Remove-Item $Output -Force
}

Compress-Archive -Path (Join-Path $deploy '*') -DestinationPath $Output -Force

$zip = Get-Item $Output
Write-Host ''
Write-Host 'Softkom live production bundle created successfully.' -ForegroundColor Green
Write-Host ("Path: {0}" -f $zip.FullName)
Write-Host ("Size: {0:N0} bytes" -f $zip.Length)
Write-Host ("Updated: {0}" -f $zip.LastWriteTime)
Write-Host ''
Write-Host 'Deploy by uploading this ZIP to public_html and extracting with overwrite enabled.' -ForegroundColor Cyan
Write-Host 'Only wp-content/mu-plugins files in this bundle are changed.' -ForegroundColor Cyan
