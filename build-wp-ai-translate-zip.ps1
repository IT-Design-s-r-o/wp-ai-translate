$ErrorActionPreference = 'Stop'

$scriptRoot = $PSScriptRoot
$sourceRoot = $scriptRoot

if (-not (Test-Path (Join-Path $sourceRoot 'wp-ai-translate.php'))) {
    $nestedRoot = Join-Path $scriptRoot 'wp-ai-translate'
    if (Test-Path (Join-Path $nestedRoot 'wp-ai-translate.php')) {
        $sourceRoot = $nestedRoot
    }
}

if (-not (Test-Path (Join-Path $sourceRoot 'wp-ai-translate.php'))) {
    throw "Plugin source folder was not found: $sourceRoot"
}

$zipRoot = 'ai-translate-woocommerce-elementor'
$distRoot = Join-Path $scriptRoot 'dist'
$zipPath = Join-Path $distRoot ($zipRoot + '.zip')

if (-not (Test-Path $distRoot)) {
    New-Item -ItemType Directory -Path $distRoot | Out-Null
}

if (Test-Path $zipPath) {
    Remove-Item -LiteralPath $zipPath -Force
}

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$zip = [System.IO.Compression.ZipFile]::Open($zipPath, [System.IO.Compression.ZipArchiveMode]::Create)

try {
    Get-ChildItem -LiteralPath $sourceRoot -Recurse -File | ForEach-Object {
        $relative = $_.FullName.Substring($sourceRoot.Length).TrimStart('\', '/')
        $relativeUnix = $relative.Replace('\', '/')

        if ($relativeUnix -match '(^|/)(\.git|\.github|\.tools|dist|node_modules|vendor)(/|$)') {
            return
        }

        if ($relativeUnix -match '(^|/)(debug\.log|.*\.log|.*\.zip|\.DS_Store|Thumbs\.db)$') {
            return
        }

        if ($relativeUnix -match '(^|/)(\.gitignore|build-.*\.ps1|.*\.bak|.*\.tmp)$') {
            return
        }

        if ($relativeUnix -eq 'SUPPORT.md') {
            return
        }

        $entryName = $zipRoot + '/' + $relativeUnix
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
            $zip,
            $_.FullName,
            $entryName,
            [System.IO.Compression.CompressionLevel]::Optimal
        ) | Out-Null
    }
}
finally {
    $zip.Dispose()
}

Write-Host "Built $zipPath"
