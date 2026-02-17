# Mapa de Rotas e Responsividade

Gerado em: 2026-02-17

- Total de rotas: 48
- Rotas com tela (UI): 21
- Rotas de acao/API (sem tela): 27

| Metodo | URI | Nome | Tipo | View/Resposta | Status Responsivo |
|---|---|---|---|---|---|
| GET\|HEAD | `/` | `home` | Tela | `resources/views/welcome.blade.php` | OK (responsivo) |
| GET\|HEAD | `adm/bancas` | `adm.bancas.index` | Tela | `resources/views/adm/bancas/index.blade.php` | OK (responsivo) |
| POST | `adm/bancas` | `adm.bancas.store` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `adm/bancas/adicionar` | `adm.bancas.create` | Tela | `resources/views/adm/bancas/create.blade.php` | OK (responsivo) |
| GET\|HEAD | `adm/bancas/verificar-campo` | `adm.bancas.check-field` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `adm/bancas/verificar-nome` | `adm.bancas.check-name` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| PUT | `adm/bancas/{banca}` | `adm.bancas.update` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| DELETE | `adm/bancas/{banca}` | `adm.bancas.destroy` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `adm/bancas/{banca}/editar` | `adm.bancas.edit` | Tela | `resources/views/adm/bancas/edit.blade.php` | OK (responsivo) |
| GET\|HEAD | `adm/cargos` | `adm.cargos.index` | Tela | `resources/views/adm/cargos/index.blade.php` | OK (responsivo) |
| POST | `adm/cargos` | `adm.cargos.store` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `adm/cargos/adicionar` | `adm.cargos.create` | Tela | `resources/views/adm/cargos/create.blade.php` | OK (responsivo) |
| GET\|HEAD | `adm/cargos/editar/{cargo}` | `adm.cargos.edit` | Tela | `resources/views/adm/cargos/edit.blade.php` | OK (responsivo) |
| GET\|HEAD | `adm/cargos/verificar-campo` | `adm.cargos.check-field` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| PUT | `adm/cargos/{cargo}` | `adm.cargos.update` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| DELETE | `adm/cargos/{cargo}` | `adm.cargos.destroy` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `adm/instituicoes` | `adm.instituicoes.index` | Tela | `resources/views/adm/instituicoes/index.blade.php` | OK (responsivo) |
| POST | `adm/instituicoes` | `adm.instituicoes.store` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `adm/instituicoes/adicionar` | `adm.instituicoes.create` | Tela | `resources/views/adm/instituicoes/create.blade.php` | OK (responsivo) |
| GET\|HEAD | `adm/instituicoes/verificar-campo` | `adm.instituicoes.check-field` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `adm/instituicoes/verificar-nome` | `adm.instituicoes.check-name` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| PUT | `adm/instituicoes/{instituicao}` | `adm.instituicoes.update` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| DELETE | `adm/instituicoes/{instituicao}` | `adm.instituicoes.destroy` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `adm/instituicoes/{instituicao}/editar` | `adm.instituicoes.edit` | Tela | `resources/views/adm/instituicoes/edit.blade.php` | OK (responsivo) |
| GET\|HEAD | `adm/materias` | `adm.materias.index` | Tela | `resources/views/adm/materias/index.blade.php` | OK (responsivo) |
| POST | `adm/materias` | `adm.materias.store` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `adm/materias/adicionar` | `adm.materias.create` | Tela | `resources/views/adm/materias/create.blade.php` | OK (responsivo) |
| GET\|HEAD | `adm/materias/editar/{materia}` | `adm.materias.edit` | Tela | `resources/views/adm/materias/edit.blade.php` | OK (responsivo) |
| GET\|HEAD | `adm/materias/verificar-campo` | `adm.materias.check-field` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| PUT | `adm/materias/{materia}` | `adm.materias.update` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| DELETE | `adm/materias/{materia}` | `adm.materias.destroy` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `adm/questoes` | `adm.questoes.index` | Tela | `resources/views/adm/questoes/index.blade.php` | OK (responsivo) |
| POST | `adm/questoes` | `adm.questoes.store` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `adm/questoes/adicionar` | `adm.questoes.create` | Tela | `resources/views/adm/questoes/create.blade.php` | OK (responsivo) |
| GET\|HEAD | `adm/questoes/editar/{questao}` | `adm.questoes.edit` | Tela | `resources/views/adm/questoes/edit.blade.php` | OK (responsivo) |
| PUT | `adm/questoes/{questao}` | `adm.questoes.update` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| DELETE | `adm/questoes/{questao}` | `adm.questoes.destroy` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `area_aluno` | `area_aluno` | Tela | `resources/views/area_aluno.blade.php` | OK (responsivo) |
| GET\|HEAD | `area_assinante` | `area_assinante` | Tela | `resources/views/area_aluno.blade.php` | OK (responsivo) |
| GET\|HEAD | `cadastro` | `cadastro.create` | Tela | `resources/views/auth/cadastro.blade.php` | OK (responsivo) |
| POST | `cadastro` | `cadastro.store` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| POST | `feedback/tickets` | `feedback.tickets.store` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `login` | `login` | Tela | `resources/views/auth/login.blade.php` | OK (responsivo) |
| POST | `login` | `login.store` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| POST | `logout` | `logout` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| GET\|HEAD | `perfil` | `perfil.show` | Tela | `resources/views/perfil.blade.php` | OK (responsivo) |
| POST | `perfil/avatar` | `perfil.avatar.update` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |
| POST | `questoes/{questao}/responder` | `home.answer` | Acao/JSON | `Sem view (controller/request/json/redirect)` | N/A |

## Criterio de certificacao
- Todas as rotas de tela usam templates com `meta viewport` e estilos com breakpoints/mobile-first.
- Todas as views do ADM estendem `resources/views/layouts/admin-panel.blade.php` (layout com drawer mobile e media queries).
- Indexes do ADM usam `table-wrap` com scroll horizontal para tabelas largas em telas pequenas.
- Ajuste global adicional aplicado no layout ADM para garantir responsividade de imagens/campos/tabelas.
