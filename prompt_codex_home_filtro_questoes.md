# Prompt Codex - Home de Filtro de Questoes

Use este prompt no Codex quando precisar recriar ou evoluir a pagina inicial de pesquisa de questoes deste projeto Laravel.

## Objetivo
Criar ou atualizar a home (`/`) com visual moderno e claro, seguindo o mesmo padrao de cores, tipografia, bordas e sombras das rotas `login`, `cadastro`, `area_aluno` e `adm`.

## Requisitos funcionais
- Centralizar um formulario de pesquisa horizontal com filtros:
  - `banca_id`
  - `cargo_id`
  - `materia_id`
- Alimentar os selects com dados das tabelas `bancas`, `cargos` e `materias`.
- A pesquisa deve listar questoes filtradas da tabela `questoes` (com relacionamento de cargos via `cargo_questao`).
- Exibir quantidade de resultados e lista resumida de questoes.
- Manter comportamento responsivo (mobile/tablet/desktop).

## Requisitos visuais
- Usar `@include('partials.edu-theme-head')` para manter tokens visuais do sistema.
- Paleta clara, cards brancos, bordas suaves e sombra leve.
- Formulario alinhado no centro e organizado horizontalmente no desktop.
- Incluir secao explicativa abaixo da pesquisa sobre simulados e resolucao de questoes.

## Requisitos tecnicos
- Criar/ajustar controller para processar filtros via query string (`GET`).
- Rotear `/` para o controller da home.
- Criar migration de suporte de performance para filtros (indice composto em `questoes`).
- Garantir compatibilidade com Docker Compose e execucao local do Laravel.
- Rodar validacoes finais:
  - `php -l` nos arquivos PHP alterados
  - `docker compose exec app php artisan migrate --force`
  - `docker compose exec app php artisan view:cache`
  - `docker compose exec app php artisan route:list`

## Importante
- Nao quebrar as rotas existentes de autenticacao e painel.
- Nao alterar a forma original de execucao local sem Docker.
- Mensagens e textos em portugues (pt-BR).
