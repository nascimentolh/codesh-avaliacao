# Open Food Facts REST API

REST API para gerenciar dados nutricionais do Open Food Facts, desenvolvida em PHP puro seguindo princípios de Domain-Driven Design (DDD), SOLID e Design Patterns.

> This is a challenge by [Coodesh](https://coodesh.com/)

## Sobre o Projeto

Esta API oferece suporte à equipe de nutricionistas da Fitness Foods LC, permitindo revisar rapidamente informações nutricionais de alimentos importados do projeto [Open Food Facts](https://world.openfoodfacts.org/).

## Tecnologias Utilizadas

- **Linguagem:** PHP 8.2+
- **Banco de Dados:** SQLite
- **Arquitetura:** Domain-Driven Design (DDD)
- **Princípios:** SOLID, Clean Architecture
- **Testes:** PHPUnit
- **Containerização:** Docker & Docker Compose
- **Documentação:** OpenAPI 3.0
- **Dependências:**
  - vlucas/phpdotenv - Gerenciamento de variáveis de ambiente
  - PHPUnit - Framework de testes unitários

## Arquitetura do Projeto

O projeto segue uma arquitetura em camadas baseada em DDD:

```
src/
├── Domain/              # Camada de Domínio (Regras de negócio)
│   ├── Entity/         # Entidades (Product, ImportHistory)
│   ├── ValueObject/    # Value Objects (ProductCode, ProductStatus)
│   └── Repository/     # Interfaces de repositórios
├── Application/         # Camada de Aplicação (Casos de uso)
│   ├── UseCase/        # Use Cases (GetProduct, UpdateProduct, etc)
│   └── DTO/            # Data Transfer Objects
├── Infrastructure/      # Camada de Infraestrutura (Implementações técnicas)
│   ├── Database/       # Conexão e migrações
│   ├── Repository/     # Implementações dos repositórios
│   ├── ExternalService/ # Cliente Open Food Facts
│   └── Cron/           # Sistema de importação CRON
└── Presentation/        # Camada de Apresentação (Interface HTTP)
    ├── Controller/     # Controllers (Health, Product)
    ├── Middleware/     # Middlewares (ApiKey, JsonResponse)
    └── Router.php      # Roteador HTTP
```

## Funcionalidades

- ✅ CRUD completo de produtos
- ✅ Importação automática via CRON do Open Food Facts
- ✅ Paginação de resultados
- ✅ Health check com informações do sistema
- ✅ Autenticação via API Key
- ✅ Soft delete (status trash)
- ✅ Histórico de importações
- ✅ Testes unitários
- ✅ Docker para facilitar deploy
- ✅ Documentação OpenAPI 3.0

## Instalação

### Pré-requisitos

- PHP 8.2 ou superior
- Composer
- Extensões PHP: pdo, pdo_sqlite, json, mbstring
- Docker e Docker Compose (opcional)

### Instalação Local

1. Clone o repositório:
```bash
git clone <url-do-repositorio>
cd codesh-avaliacao
```

2. Instale as dependências:
```bash
composer install
```

3. Configure as variáveis de ambiente:
```bash
cp .env.example .env
# Edite o arquivo .env e configure sua API_KEY
```

4. Execute as migrações do banco de dados:
```bash
php bin/migrate.php
```

5. Inicie o servidor de desenvolvimento:
```bash
php -S localhost:8000 -t public/
```

A API estará disponível em `http://localhost:8000`

### Instalação com Docker

1. Configure as variáveis de ambiente:
```bash
cp .env.example .env
# Edite o arquivo .env conforme necessário
```

2. Construa e inicie os containers:
```bash
docker-compose up -d
```

3. Execute as migrações dentro do container:
```bash
docker-compose exec web php bin/migrate.php
```

A API estará disponível em `http://localhost:8080`

## Uso da API

### Autenticação

Todas as requisições (exceto `GET /`) requerem autenticação via API Key no header:

```bash
X-API-KEY: sua-api-key-aqui
```

### Endpoints Disponíveis

#### Health Check
```bash
GET /
```

Retorna informações sobre o status da API, conexão com banco de dados, última execução do CRON, uptime e uso de memória.

**Exemplo de resposta:**
```json
{
  "status": "OK",
  "database": {
    "connection": "OK",
    "read": "OK",
    "write": "OK"
  },
  "last_cron_run": "2026-01-19 02:00:00",
  "uptime_seconds": 3600.50,
  "memory_usage": {
    "bytes": 2097152,
    "human": "2.00 MB"
  },
  "timestamp": "2026-01-19 14:30:00"
}
```

#### Listar Produtos
```bash
GET /products?page=1&limit=20
```

**Parâmetros:**
- `page` (opcional): Número da página (padrão: 1)
- `limit` (opcional): Itens por página (padrão: 20, máximo: 100)

#### Obter Produto
```bash
GET /products/:code
```

**Exemplo:**
```bash
curl -H "X-API-KEY: dev-api-key-12345" http://localhost:8000/products/20221126
```

#### Atualizar Produto
```bash
PUT /products/:code
Content-Type: application/json

{
  "product_name": "Novo nome do produto",
  "quantity": "400g",
  "brands": "Nova marca"
}
```

**Exemplo:**
```bash
curl -X PUT \
  -H "X-API-KEY: dev-api-key-12345" \
  -H "Content-Type: application/json" \
  -d '{"product_name":"Madalenas Premium"}' \
  http://localhost:8000/products/20221126
```

#### Deletar Produto (Soft Delete)
```bash
DELETE /products/:code
```

**Exemplo:**
```bash
curl -X DELETE \
  -H "X-API-KEY: dev-api-key-12345" \
  http://localhost:8000/products/20221126
```

## Sistema CRON

O sistema de importação está configurado para executar diariamente às 02:00 AM.

### Executar Importação Manualmente

```bash
php cron/import.php
```

Com Docker:
```bash
docker-compose exec web php cron/import.php
```

### Configurar CRON no Sistema

Adicione ao crontab:
```bash
0 2 * * * /usr/bin/php /caminho/para/projeto/cron/import.php >> /caminho/para/projeto/storage/logs/cron.log 2>&1
```

## Testes

Execute os testes unitários:

```bash
./vendor/bin/phpunit
```

Com Docker:
```bash
docker-compose exec web ./vendor/bin/phpunit
```

## Documentação da API

A documentação completa da API está disponível em formato OpenAPI 3.0:

- **Arquivo:** `docs/openapi.yaml`
- **Visualizar:** Use [Swagger Editor](https://editor.swagger.io/) ou [Swagger UI](https://swagger.io/tools/swagger-ui/)

Para visualizar localmente, você pode usar:
```bash
npx @redocly/cli preview-docs docs/openapi.yaml
```

## Estrutura do Banco de Dados

### Tabela: products
Armazena informações dos produtos alimentícios.

**Campos principais:**
- `code` (INTEGER PRIMARY KEY): Código único do produto
- `status` (TEXT): Status do produto (draft, trash, published)
- `imported_t` (TEXT): Data/hora de importação
- `product_name`, `quantity`, `brands`, `categories`, etc.

### Tabela: import_history
Registra o histórico de importações do CRON.

**Campos:**
- `id` (INTEGER PRIMARY KEY AUTOINCREMENT)
- `filename` (TEXT): Nome do arquivo importado
- `products_imported` (INTEGER): Quantidade de produtos importados
- `started_at` (TEXT): Início da importação
- `completed_at` (TEXT): Fim da importação
- `status` (TEXT): Status (running, completed, failed)
- `error_message` (TEXT): Mensagem de erro (se houver)

## Design Patterns Utilizados

- **Repository Pattern:** Abstração do acesso a dados
- **Dependency Injection:** Inversão de controle via construtores
- **DTO (Data Transfer Object):** Transferência de dados entre camadas
- **Value Object:** Encapsulamento de valores com validação
- **Singleton:** Conexão única com banco de dados
- **Front Controller:** public/index.php como ponto de entrada
- **Middleware:** Cadeia de processamento de requisições

## Princípios SOLID Aplicados

- **S (Single Responsibility):** Cada classe tem uma única responsabilidade
- **O (Open/Closed):** Extensível via interfaces, fechado para modificação
- **L (Liskov Substitution):** Implementações de repositório são intercambiáveis
- **I (Interface Segregation):** Interfaces específicas e coesas
- **D (Dependency Inversion):** Dependência de abstrações, não de implementações concretas

## Diferenciais Implementados

- ✅ **Diferencial 2:** Docker configurado com docker-compose
- ✅ **Diferencial 4:** Documentação OpenAPI 3.0 completa
- ✅ **Diferencial 5:** Testes unitários para GET e PUT
- ✅ **Diferencial 6:** Autenticação via API Key

## Segurança

- Prepared statements para prevenir SQL Injection
- Validação de entrada via Value Objects
- API Key obrigatória (exceto health check)
- Headers de segurança (X-Content-Type-Options)
- CORS configurável
- Não exposição de detalhes internos em produção

## Performance

- Índices de banco de dados para queries otimizadas
- Paginação para evitar sobrecarga
- Conexão singleton com PDO
- Lazy loading de dados
- Limite de 100 produtos por arquivo na importação

## Estrutura de Logs

Logs do CRON são armazenados em:
```
storage/logs/cron.log
```

## Autor

**Luiz Nascimento**
- Email: luizh.nnh@gmail.com

## Licença

MIT

---

> This is a challenge by [Coodesh](https://coodesh.com/)
