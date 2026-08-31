param(
    [string]$Output = ".\softkom-live-production-bundle.zip"
)
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$deploy = Join-Path $env:TEMP 'softkom-live-production-bundle'
$mu = Join-Path $deploy 'wp-content\mu-plugins'
$runtime = Join-Path $deploy 'wp-content\softkom-assessment-runtime'
$runtimeData = Join-Path $runtime 'data'
Remove-Item $deploy -Recurse -Force -ErrorAction SilentlyContinue
New-Item $mu -ItemType Directory -Force | Out-Null
New-Item $runtimeData -ItemType Directory -Force | Out-Null
$muFiles = @(
    '000-softkom-organic-loader.php','softkom-public-acquisition.php','softkom-public-acquisition.js','softkom-strategy-request.php','softkom-sales-notifications.php','softkom-industry-funnel.php','softkom-industry-funnel.js','softkom-assessment-standalone.php','softkom-commercial-persistence.php','softkom-campaign-admin-redirect.php','softkom-organic-ai-discovery.php','softkom-organic-growth-pages.php','softkom-search-discovery.php','softkom-organic-attribution.php'
)
foreach ($file in $muFiles) {$source=Join-Path $root ("wp-content\mu-plugins\"+$file);if(-not(Test-Path $source)){throw "Required deployment file is missing: $source"};Copy-Item $source -Destination $mu -Force}
$sitemap=Join-Path $root 'softkom-sitemap.xml';if(-not(Test-Path $sitemap)){throw "Required physical sitemap is missing: $sitemap"};Copy-Item $sitemap -Destination (Join-Path $deploy 'softkom-sitemap.xml') -Force
$theme=Join-Path $root 'wp-content\themes\softkom-v3';$assessmentData=Join-Path $theme 'inc\data\assessment';if(-not(Test-Path $assessmentData)){throw "Assessment data directory is missing: $assessmentData"};Copy-Item (Join-Path $assessmentData '*.php') -Destination $runtimeData -Force
$runtimeFiles=@{
 (Join-Path $theme 'template-parts\page-assessment.php')=(Join-Path $runtime 'page-assessment.php')
 (Join-Path $theme 'assets\js\softkom-assessment.js')=(Join-Path $runtime 'softkom-assessment.js')
 (Join-Path $theme 'assets\css\softkom-assessment.css')=(Join-Path $runtime 'softkom-assessment.css')
 (Join-Path $root 'tests\run-live-readiness.php')=(Join-Path $runtime 'live-readiness.php')
 (Join-Path $root 'tests\run-organic-acquisition-readiness.php')=(Join-Path $runtime 'organic-acquisition-readiness.php')
}
foreach($source in $runtimeFiles.Keys){if(-not(Test-Path $source)){throw "Required assessment runtime file is missing: $source"};Copy-Item $source -Destination $runtimeFiles[$source] -Force}
if(Test-Path $Output){Remove-Item $Output -Force}
Compress-Archive -Path (Join-Path $deploy '*') -DestinationPath $Output -Force
$zip=Get-Item $Output
Write-Host ''
Write-Host 'Softkom Organic Acquisition Release bundle created successfully.' -ForegroundColor Green
Write-Host ("Path: {0}" -f $zip.FullName)
Write-Host ("Size: {0:N0} bytes" -f $zip.Length)
Write-Host ("Updated: {0}" -f $zip.LastWriteTime)
Write-Host ''
Write-Host 'Deploy by uploading this ZIP to the LIVE WORDPRESS ROOT (the directory containing wp-admin, wp-content and wp-includes) and extracting with overwrite enabled.' -ForegroundColor Cyan
Write-Host 'Bundle includes the physical acquisition sitemap, assessment runtime, organic/AI discovery and attribution, search discovery/internal linking, high-intent organic growth pages, acquisition, commercial persistence, campaign admin MU plugins and release QA.' -ForegroundColor Cyan
Write-Host 'It does not replace the active live theme or WordPress database.' -ForegroundColor Cyan
