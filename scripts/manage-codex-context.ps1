param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('read', 'append')]
    [string]$Mode,

    [string]$Summary = '',

    [string]$Path = 'codex_context.md',

    [int]$MaxChars = 80000
)

$startMarker = '<!-- CONTEXT_START -->'
$endMarker = '<!-- CONTEXT_END -->'

function New-Template {
    @"
# Codex Context

Arquivo de contexto operacional para continuidade entre tarefas.
Limite maximo: $MaxChars caracteres (janela deslizante).

$startMarker
$endMarker
"@
}

if (-not (Test-Path -LiteralPath $Path)) {
    New-Template | Set-Content -LiteralPath $Path -Encoding UTF8
}

$content = Get-Content -LiteralPath $Path -Raw

$startIndex = $content.IndexOf($startMarker)
$endIndex = $content.IndexOf($endMarker)

if ($startIndex -lt 0 -or $endIndex -lt 0 -or $endIndex -lt $startIndex) {
    $content = New-Template
    $content | Set-Content -LiteralPath $Path -Encoding UTF8
    $startIndex = $content.IndexOf($startMarker)
    $endIndex = $content.IndexOf($endMarker)
}

$prefixEnd = $startIndex + $startMarker.Length
$prefix = $content.Substring(0, $prefixEnd)
$middle = $content.Substring($prefixEnd, $endIndex - $prefixEnd)
$suffix = $content.Substring($endIndex)

if ($Mode -eq 'read') {
    $toShow = $middle.Trim()
    if ([string]::IsNullOrWhiteSpace($toShow)) {
        Write-Output '[codex_context] vazio'
    } else {
        Write-Output $toShow
    }
    exit 0
}

if ([string]::IsNullOrWhiteSpace($Summary)) {
    throw 'Para modo append, informe -Summary.'
}

$timestamp = (Get-Date).ToUniversalTime().ToString('yyyy-MM-ddTHH:mm:ssZ')
$newEntry = @"
### $timestamp

$Summary
"@.Trim()

$existing = @()
if (-not [string]::IsNullOrWhiteSpace($middle)) {
    $regexBlocks = [regex]::Matches($middle, '(?s)###\s.*?(?=(?:\r?\n)?###\s|$)') | ForEach-Object { $_.Value.Trim() } | Where-Object { -not [string]::IsNullOrWhiteSpace($_) }
    if ($regexBlocks.Count -gt 0) {
        $existing = $regexBlocks
    } else {
        $existing = ($middle.Trim() -split "`r?`n---`r?`n") | Where-Object { -not [string]::IsNullOrWhiteSpace($_) }
    }
}

$entries = @($existing + $newEntry)

while ($true) {
    $joined = ''
    if ($entries.Count -gt 0) {
        $joined = ($entries -join "`r`n---`r`n")
    }

    $final = "$prefix`r`n$joined`r`n$suffix"

    if ($final.Length -le $MaxChars) {
        $final | Set-Content -LiteralPath $Path -Encoding UTF8
        break
    }

    if ($entries.Count -gt 1) {
        $entries = $entries[1..($entries.Count - 1)]
        continue
    }

    $fixedOverhead = ("$prefix`r`n`r`n$suffix").Length
    $available = [Math]::Max(0, $MaxChars - $fixedOverhead)
    $single = $entries[0]
    if ($single.Length -gt $available) {
        $single = $single.Substring($single.Length - $available)
    }
    $entries = @($single)
}

Write-Output '[codex_context] atualizado com sucesso'
