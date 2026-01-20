$email = (git config user.email)
if (-not $email) { $email = 'your-email@example.com' }
$pub = "$env:USERPROFILE\.ssh\id_ed25519.pub"
if (Test-Path $pub) {
    Write-Output '=== Existing public key ==='
    Get-Content $pub
} else {
    New-Item -ItemType Directory -Force -Path "$env:USERPROFILE\.ssh" | Out-Null
    ssh-keygen -t ed25519 -C $email -f "$env:USERPROFILE\.ssh\id_ed25519" -N '' | Out-Null
    Write-Output '=== New public key ==='
    Get-Content $pub
}