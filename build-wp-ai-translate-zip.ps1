$ErrorActionPreference = 'Stop'

$root = $PSScriptRoot
$zipRoot = 'ait-multilingual-translate'
$zipPath = Join-Path $PSScriptRoot ($zipRoot + '.zip')

if (-not (Test-Path (Join-Path $root 'ait-multilingual-translate.php'))) {
    $nestedRoot = Join-Path $PSScriptRoot 'wp-ai-translate'
    if (Test-Path (Join-Path $nestedRoot 'ait-multilingual-translate.php')) {
        $root = $nestedRoot
    }
}

if (-not (Test-Path (Join-Path $root 'ait-multilingual-translate.php'))) {
    throw "Plugin source folder was not found: $root"
}

if (Test-Path $zipPath) {
    Remove-Item -LiteralPath $zipPath -Force
}

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$zip = [System.IO.Compression.ZipFile]::Open($zipPath, [System.IO.Compression.ZipArchiveMode]::Create)

try {
    Get-ChildItem -LiteralPath $root -Recurse -File | ForEach-Object {
        $relative = $_.FullName.Substring($root.Length).TrimStart('\', '/')
        $relativeUnix = $relative -replace '\\', '/'

        if ($relativeUnix -match '(^|/)(\.git|\.github|\.tools|dist|docs|wordpress-org-assets|node_modules|vendor)(/|$)') {
            return
        }

        if ($relativeUnix -match '(^|/)(debug\.log|.*\.log|.*\.zip|\.DS_Store|Thumbs\.db|\.gitkeep)$') {
            return
        }

        if ($relativeUnix -match '(^|/)(\.gitignore|build-.*\.ps1|ROADMAP\.md|.*\.bak|.*\.tmp)$') {
            return
        }

        if ($relativeUnix -eq 'SUPPORT.md') {
            return
        }

        $entryName = $zipRoot + '/' + ($relative -replace '\\', '/')
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
