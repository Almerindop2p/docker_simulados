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
