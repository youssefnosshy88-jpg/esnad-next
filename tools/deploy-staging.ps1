# Deploys the esnad-next theme, esnad-core plugin and mu-plugins to the STAGING site over SSH.
# One zip, one scp, one ssh: Hostinger rate-limits repeated SSH connections.
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
$base = "$repo\wp-content"
$zipPath = "$tmp\wp-content.zip"
$zip = [IO.Compression.ZipFile]::Open($zipPath, 'Create')
foreach ($i in @('themes\esnad-next', 'plugins\esnad-core', 'mu-plugins')) {
	if (-not (Test-Path "$base\$i")) { continue }
	foreach ($f in Get-ChildItem "$base\$i" -Recurse -File) {
		# Forward slashes: Windows PowerShell 5.1 would otherwise write backslashes.
		$entry = $f.FullName.Substring($base.Length + 1) -replace '\\', '/'
		[void][IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $f.FullName, $entry, 'Optimal')
	}
}
$zip.Dispose()
$sshOpts = @('-i', $Key, '-o', 'BatchMode=yes', '-o', 'ConnectTimeout=30')
scp @sshOpts -P $Port $zipPath "${Target}:esnad-work/wp-content.zip"
ssh @sshOpts -p $Port $Target "unzip -q -o ~/esnad-work/wp-content.zip -d $Site/wp-content/ ; rm -f ~/esnad-work/wp-content.zip ; echo deployed"
