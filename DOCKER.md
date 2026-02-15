# Docker Compose - Laravel simulados

Este setup sobe:

- Laravel em PHP + Apache
- MySQL
- PHPMyAdmin

Sem alterar os arquivos do projeto `simulados`.

## Subir os containers

No diretorio raiz (`Docker simulados`):

```powershell
docker compose up -d --build
```

Para garantir migrations sempre apos o `up` (inclusive quando a app ja estava em execucao):

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\docker-up.ps1
```

## Endpoints

- Laravel: `http://localhost:8080`
- PHPMyAdmin: `http://localhost:8081`
- MySQL host para ferramentas externas: `127.0.0.1:3307`

## Credenciais padrao do banco

- Banco: `simulados`
- Usuario: `simulados`
- Senha: `simulados`
- Root senha: `root`

## Comandos uteis

Rodar migrations manualmente:

```powershell
docker compose exec app php artisan migrate
```

Rodar subida sem rebuild e aplicando migrations no final:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\docker-up.ps1 -NoBuild
```

Criar migration para nova tabela (padrao recomendado):

```powershell
docker compose exec app php artisan make:migration create_nome_tabela_table --create=nome_tabela
```

Criar migration para alterar tabela existente:

```powershell
docker compose exec app php artisan make:migration add_campo_to_nome_tabela_table --table=nome_tabela
```

Entrar no container da app:

```powershell
docker compose exec app sh
```

Parar tudo:

```powershell
docker compose down
```

Parar e remover volume do MySQL:

```powershell
docker compose down -v
```

Ver logs do MySQL:

```powershell
docker compose logs mysql --tail=200
```

Ver status e logs da aplicacao:

```powershell
docker compose ps
docker compose logs app --tail=200
```

Recriar tudo do zero (apos ajuste de compose):

```powershell
docker compose down -v
docker compose up -d --build
```

Se aparecer erro `Table 'simulados.sessions' doesn't exist`, recrie o container da app para aplicar os overrides de ambiente do Docker:

```powershell
docker compose up -d --build app
docker compose exec app php artisan optimize:clear
```

## Limite de memoria

O compose foi configurado com limite total de 3GB:

- app: 1536MB
- mysql: 1024MB
- phpmyadmin: 512MB

## Observacao sobre o projeto original

- O arquivo `simulados/.env` nao foi alterado.
- Fora do Docker, o projeto continua funcionando com sua configuracao atual.
- Dentro do Docker, as variaveis de ambiente do service `app` forcam conexao MySQL apenas no container.
- Build de imagem (`docker compose build`) nao executa migration, porque nao existe conexao com banco no build.
- No Docker, migrations pendentes executam no startup da app (`RUN_MIGRATIONS=true`).
- Como o codigo Laravel esta em volume (`./simulados:/var/www/html`), criar uma migration nova nao recria automaticamente o container app.
- Para garantir aplicacao da migration nova em qualquer cenario, use `scripts/docker-up.ps1` ou rode `docker compose exec app php artisan migrate --force` apos o `up`.
- Fora do Docker, voce pode continuar usando o fluxo normal do Laravel com `php artisan` local.
