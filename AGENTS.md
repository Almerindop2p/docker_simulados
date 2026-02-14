# Workspace Instructions

## Contexto obrigatorio

Antes de iniciar qualquer tarefa, ler o contexto atual:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\\scripts\\manage-codex-context.ps1 -Mode read
```

Ao finalizar qualquer tarefa, atualizar o contexto com resumo curto:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\\scripts\\manage-codex-context.ps1 -Mode append -Summary "<resumo da tarefa e resultado>"
```

## Regras de retencao

- O arquivo de contexto eh `codex_context.md`.
- Limite maximo de armazenamento: `80000` caracteres.
- Quando exceder o limite, remover entradas mais antigas automaticamente.
- Manter resumos curtos e objetivos para reduzir consumo de contexto.

## Regras Laravel + Docker

- Sempre criar migration para qualquer nova tabela ou alteracao de esquema.
- Em ambiente Docker Compose, executar migrations apos subir MySQL/app (automatico no startup e manual quando necessario).
- Nao alterar a configuracao original do projeto para uso local sem Docker; manter compatibilidade com `php artisan` local.

