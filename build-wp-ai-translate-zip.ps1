$ErrorActionPreference = 'Stop'

$root = Join-Path $PSScriptRoot 'wp-ai-translate'
$zipRoot = 'ai-translate-woocommerce-elementor'
$zipPath = Join-Path $PSScriptRoot ($zipRoot + '.zip')

if (-not (Test-Path (Join-Path $root 'wp-ai-translate.php'))) {
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

        if ($relativeUnix -match '(^|/)(\.git|\.github|node_modules|vendor)(/|$)') {
            return
        }

        if ($relativeUnix -match '(^|/)(debug\.log|.*\.log|\.DS_Store|Thumbs\.db)$') {
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
