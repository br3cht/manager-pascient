# Conecta SUS — Sistema de Cadastro de Pacientes

Sistema web de cadastro de pacientes para o setor de saúde. Backend em **Laravel** (API REST JSON) e frontend em **Vue.js 2** (SPA), integrados e containerizados com **Docker**.

## Stack

- **Backend:** PHP 8.5 / Laravel 13 / MySQL 8
- **Frontend:** Vue.js 2 / Vuex / Vuetify 2 / VeeValidate
- **Infra:** Docker / Docker Compose / Nginx

## Pré-requisitos

- Docker e Docker Compose instalados

## Como executar

```bash
# 1. Clonar o repositório
git clone <url-do-repositorio>
cd testDev

# 2. Copiar variáveis de ambiente
cp .env.example .env

# 3. Subir os containers
docker compose up -d --build

# 4. Instalar dependências PHP
docker compose exec app composer install

# 5. Gerar chave da aplicação
docker compose exec app php artisan key:generate

# 6. Rodar migrations e seeders
docker compose exec app php artisan migrate --seed

# 7. Build do frontend
docker compose exec frontend npm run build:standalone
```

## Acesso

| Serviço  | URL                       |
|----------|---------------------------|
| Aplicação | http://localhost:8080     |
| MySQL    | localhost:3306             |

## Credenciais padrão (seed)

| Campo | Valor              |
|-------|--------------------|
| Email | test@example.com   |
| Senha | password           |

## Comandos úteis

```bash
# Iniciar containers
docker compose up -d --build

# Migrations + seed
docker compose exec app php artisan migrate --seed

# Rodar testes
docker compose exec app php artisan test

# Instalar dependências PHP
docker compose exec app composer install

# Build do frontend
docker compose exec frontend npm run build:standalone
```

## Estrutura do projeto

### Backend (Laravel)

```
app/
  Http/
    Controllers/Api/
      AddressController.php
      PatientController.php
      DashboardController.php
    Requests/
      StoreAddressRequest.php
      UpdateAddressRequest.php
      StorePatientRequest.php
      UpdatePatientRequest.php
  Models/
    Address.php
    Patient.php
  Repositories/
    AddressRepository.php
    PatientRepository.php
  Services/
    AddressService.php
    PatientService.php
    DashboardService.php
database/
  migrations/
  seeders/
  factories/
routes/
  api.php
```

### Frontend (Vue.js 2)

```
resources/js/
  components/
    AppLayout.vue
    BaseInput.vue
    BaseTable.vue
    ConfirmModal.vue
    Pagination.vue
  views/
    Dashboard.vue
    Login.vue
    enderecos/  Index.vue · Form.vue
    pacientes/  Index.vue · Form.vue
  store/
    index.js
    modules/
      addresses.js
      patients.js
      auth.js
      dashboard.js
      feedback.js
  services/
    api.js
    address.service.js
    patient.service.js
    auth.service.js
    dashboard.service.js
  router/
    index.js
```

## API REST — Endpoints

Todos os endpoints requerem autenticação via Sanctum (Bearer Token), exceto login.

### Autenticação

| Método | Rota          | Descrição       |
|--------|---------------|-----------------|
| POST   | /api/login    | Autenticar      |
| POST   | /api/logout   | Desconectar     |

### Endereços

| Método | Rota                 | Descrição              |
|--------|----------------------|------------------------|
| GET    | /api/addresses       | Listar (paginado)      |
| POST   | /api/addresses       | Criar endereço         |
| GET    | /api/addresses/{id}  | Detalhar               |
| PUT    | /api/addresses/{id}  | Atualizar              |
| DELETE | /api/addresses/{id}  | Excluir (ver RN-03)    |

### Pacientes

| Método | Rota                | Descrição             |
|--------|---------------------|-----------------------|
| GET    | /api/patients       | Listar (paginado)     |
| POST   | /api/patients       | Criar paciente        |
| GET    | /api/patients/{id}  | Detalhar              |
| PUT    | /api/patients/{id}  | Atualizar             |
| DELETE | /api/patients/{id}  | Excluir               |

### Dashboard

| Método | Rota            | Descrição                        |
|--------|-----------------|----------------------------------|
| GET    | /api/dashboard  | Totais de pacientes e endereços  |

### Parâmetros de listagem

| Parâmetro | Tipo        | Exemplo            |
|-----------|-------------|---------------------|
| page      | integer     | ?page=2             |
| per_page  | integer     | ?per_page=20 (padrão: 15, máx: 100) |
| search    | string      | ?search=João        |
| sort_by   | string      | ?sort_by=name       |
| sort_dir  | asc / desc  | ?sort_dir=desc      |
| state     | UF (2 letras) | ?state=SP         |
| gender    | M / F / O   | ?gender=F           |

## Docker — Containers

| Serviço   | Porta          | Notas                            |
|-----------|----------------|----------------------------------|
| app       | 9000 (interno) | PHP-FPM + Composer + Artisan     |
| nginx     | 8080 → 80      | Proxy reverso para FPM           |
| db        | 3306           | MySQL 8 com volume persistente   |
| frontend  | —              | Node 20 — build da SPA Vue.js    |

## Variáveis de ambiente

| Variável       | Descrição                  |
|----------------|----------------------------|
| APP_KEY        | Chave da aplicação         |
| APP_ENV        | Ambiente (local/production)|
| APP_URL        | URL da aplicação           |
| FRONTEND_URL   | URL do frontend (CORS)     |
| DB_HOST        | Host do banco de dados     |
| DB_PORT        | Porta do MySQL             |
| DB_DATABASE    | Nome do banco              |
| DB_USERNAME    | Usuário do banco           |
| DB_PASSWORD    | Senha do banco             |
| LOG_CHANNEL    | Canal de log (daily)       |
