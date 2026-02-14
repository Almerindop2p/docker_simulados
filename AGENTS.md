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

