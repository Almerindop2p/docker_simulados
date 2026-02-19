Pacote de bandeiras por pais (ISO-3166 alpha-2)

- Pasta: `public/assets/flags`
- Nome de arquivo esperado: `<codigo>.svg` em minusculo
- Exemplo: `br.svg`, `us.svg`, `pt.svg`

Fallback:

- Quando o arquivo da bandeira nao existir para o pais, o sistema retorna automaticamente:
  - `public/assets/flags/_default.svg`

Campos usados:

- `country_code` salvo nas tabelas de metricas/consentimento (valor ISO-2, ex: `BR`, `US`).

Como renderizar na view:

```php
<img src="{{ $registro->country_flag_url }}" alt="Bandeira {{ $registro->country_code ?? 'N/A' }}">
```
