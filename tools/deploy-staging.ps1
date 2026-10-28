# Deploys the esnad-next theme (and esnad-core plugin when present) to the STAGING site over SSH.
# Usage: powershell -File tools\deploy-staging.ps1
# Requires: an SSH key registered at Hostinger. Never point this at production.
param(
	[string]$Key = "$env:USERPROFILE\.ssh\esnad_staging_ed25519",
	[string]$Target = 'u433536276@91.108.101.100',
	[int]$Port = 65002,
	[string]$Site = 'domains/green-gorilla-647751.hostingersite.com/public_html'
)
$ErrorActionPreference = 'Stop'
$repo = Split-Path $PSScriptRoot
Add-Type -AssemblyName System.IO.Compression.FileSystem
$tmp = Join-Path $env:TEMP 'esnad-deploy'; if (Test-Path $tmp) { Remove-Item $tmp -Recurse -Force }; New-Item -ItemType Directory $tmp | Out-Null
$items = @('themes\esnad-next', 'plugins\esnad-core', 'mu-plugins') | Where-Object { Test-Path "$repo\wp-content\$_" }
foreach ($i in $items) {
	$name = ($i -replace '\\', '_') + '.zip'
	# Build the zip by hand so entry names use forward slashes (Windows PowerShell 5.1 writes backslashes otherwise).
	$src = "$repo\wp-content\$i"
	$zip = [IO.Compression.ZipFile]::Open("$tmp\$name", 'Create')
	foreach ($f in Get-ChildItem $src -Recurse -File) {
		$entry = $f.FullName.Substring($src.Length + 1) -replace '\\', '/'
		[void][IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $f.FullName, $entry, 'Optimal')
	}
	$zip.Dispose()
	scp -i $Key -P $Port -o BatchMode=yes "$tmp\$name" "${Target}:esnad-work/$name"
	$dest = "$Site/wp-content/" + ($i -replace '\\', '/')
	ssh -i $Key -p $Port -o BatchMode=yes $Target "rm -rf $dest.new && mkdir -p $dest.new && unzip -q -o ~/esnad-work/$name -d $dest.new && mkdir -p $dest && cp -a $dest.new/. $dest/ && rm -rf $dest.new ~/esnad-work/$name"
	"deployed $i"
}
