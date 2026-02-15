param(
    [switch]$NoBuild
)

$ErrorActionPreference = "Stop"

$upArgs = @("compose", "up", "-d")
if (-not $NoBuild) {
    $upArgs += "--build"
}

Write-Host "Subindo stack Docker Compose..."
docker @upArgs
if ($LASTEXITCODE -ne 0) {
    exit $LASTEXITCODE
}

Write-Host "Executando migrations..."
for ($attempt = 1; $attempt -le 30; $attempt++) {
    docker compose exec -T app php artisan migrate --force
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Migrations aplicadas com sucesso."
        exit 0
    }

    Write-Host "Tentativa $attempt/30 falhou. Aguardando 2s..."
    Start-Sleep -Seconds 2
}

Write-Error "Falha ao executar migrations apos subir a stack."
exit 1
