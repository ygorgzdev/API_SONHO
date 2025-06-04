# API Sistema de Sonhos

Uma API RESTful robusta para gerenciamento de sonhos, tags e interpretações, desenvolvida em PHP com arquitetura MVC limpa e bem estruturada.

## 🏗️ Arquitetura

A API segue o padrão MVC com separação clara de responsabilidades:

- **Controller**: Recebe requisições HTTP e delega operações para os services
- **Service**: Implementa regras de negócio, validações e coordena operações
- **DAO**: Gerencia acesso direto ao banco de dados MySQL
- **Interfaces**: Define contratos para implementações dos DAOs
- **Generic**: Classes utilitárias para roteamento, conexão e processamento

### Padrões Implementados
- **Singleton**: Para gerenciamento de conexão com banco de dados
- **Factory**: Para criação de instâncias de conexão
- **Dependency Injection**: Services injetam dependências necessárias
- **Interface Segregation**: DAOs implementam interfaces específicas

## 🗄️ Banco de Dados

### Estrutura das Tabelas

```sql
CREATE DATABASE banco_sonhos;
USE banco_sonhos;

-- Tabela principal de sonhos
CREATE TABLE sonhos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conteudo TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de tags para categorização
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    INDEX idx_nome (nome)
);

-- Tabela de relacionamento N:N entre sonhos e tags
CREATE TABLE sonho_tag (
    sonho_id INT,
    tag_id INT,
    PRIMARY KEY (sonho_id, tag_id),
    FOREIGN KEY (sonho_id) REFERENCES sonhos(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    INDEX idx_sonho_id (sonho_id),
    INDEX idx_tag_id (tag_id)
);

-- Tabela de interpretações dos sonhos
CREATE TABLE interpretacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sonho_id INT NOT NULL,
    interpretador VARCHAR(100) NOT NULL,
    texto TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sonho_id) REFERENCES sonhos(id) ON DELETE CASCADE,
    INDEX idx_sonho_id (sonho_id)
);
```

## ⚙️ Instalação e Configuração

### Pré-requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensão PDO MySQL habilitada

### Passos de Instalação

1. **Clone o repositório**
   ```bash
   git clone <url-do-repositorio>
   cd api-sistema-sonhos
   ```

2. **Configure o banco de dados**
   - Execute o script SQL fornecido para criar as tabelas
   - Ajuste as credenciais em `generic/MysqlSingleton.php`:
   ```php
   private $dsn = 'mysql:host=localhost;dbname=banco_sonhos';
   private $usuario = 'seu_usuario';
   private $senha = 'sua_senha';
   ```

3. **Configure o servidor web**
   - Aponte o DocumentRoot para o diretório da API
   - Certifique-se de que o arquivo `.htaccess` está funcionando
   - Teste a URL: `http://localhost/api/?param=sonhos`

4. **Verificação da instalação**
   ```bash
   curl -X GET "http://localhost/api/?param=sonhos"
   ```

## 📚 Documentação da API

### Formato de Resposta Padrão

Todas as respostas seguem o formato JSON:

```json
{
    "erro": null,
    "dados": [...]
}
```

**Códigos de Status HTTP:**
- `200`: Operação realizada com sucesso
- `201`: Recurso criado com sucesso
- `400`: Dados inválidos ou erro de validação
- `404`: Recurso não encontrado
- `500`: Erro interno do servidor

---

## 🌙 Endpoints - Sonhos

### Listar Todos os Sonhos
```http
GET /api/?param=sonhos
```

**Resposta:**
```json
{
    "erro": null,
    "dados": [
        {
            "id": 1,
            "conteudo": "Sonhei que estava voando sobre a cidade...",
            "criado_em": "2024-01-15 10:30:00",
            "tags": "voar, liberdade, cidade"
        }
    ]
}
```

### Buscar Sonho por ID
```http
GET /api/?param=sonho&id=1
```

### Criar Novo Sonho
```http
POST /api/?param=sonhos
Content-Type: application/json

{
    "conteudo": "Descrição detalhada do sonho...",
    "tags": "tag1,tag2,tag3"
}
```

**Validações:**
- `conteudo`: Mínimo 10 caracteres (obrigatório)
- `tags`: Opcional, separadas por vírgula

**Resposta de Sucesso:**
```json
{
    "erro": null,
    "dados": {
        "sucesso": "Sonho salvo com sucesso!",
        "id": 1
    }
}
```

### Atualizar Sonho
```http
PUT /api/?param=sonho
Content-Type: application/json

{
    "id": 1,
    "conteudo": "Conteúdo atualizado do sonho...",
    "tags": "nova_tag1,nova_tag2"
}
```

### Deletar Sonho
```http
DELETE /api/?param=sonho&id=1
```

### Buscar Sonhos por Tag
```http
GET /api/?param=sonhos/tag&tag=voar
```

---

## 🏷️ Endpoints - Tags

### Listar Todas as Tags
```http
GET /api/?param=tags
```

### Buscar Tag por ID
```http
GET /api/?param=tag&id=1
```

### Criar Nova Tag
```http
POST /api/?param=tags
Content-Type: application/json

{
    "nome": "pesadelo"
}
```

**Validações:**
- `nome`: 2-50 caracteres, deve ser único

### Atualizar Tag
```http
PUT /api/?param=tag
Content-Type: application/json

{
    "id": 1,
    "nome": "novo_nome"
}
```

### Deletar Tag
```http
DELETE /api/?param=tag&id=1
```

### Tags Mais Populares
```http
GET /api/?param=tags/populares
```

**Resposta:**
```json
{
    "erro": null,
    "dados": [
        {
            "id": 1,
            "nome": "voar",
            "total_uso": 15
        }
    ]
}
```

---

## 💭 Endpoints - Interpretações

### Listar Todas as Interpretações
```http
GET /api/?param=interpretacoes
```

### Buscar Interpretação por ID
```http
GET /api/?param=interpretacao&id=1
```

### Buscar Interpretações de um Sonho
```http
GET /api/?param=sonho/interpretacoes&sonho_id=1
```

### Criar Nova Interpretação
```http
POST /api/?param=interpretacoes
Content-Type: application/json

{
    "sonho_id": 1,
    "interpretador": "Dr. João Silva",
    "texto": "Este sonho representa o desejo inconsciente de liberdade..."
}
```

**Validações:**
- `sonho_id`: Deve existir na base de dados
- `interpretador`: 2-100 caracteres (obrigatório)
- `texto`: Mínimo 20 caracteres (obrigatório)

### Atualizar Interpretação
```http
PUT /api/?param=interpretacao
Content-Type: application/json

{
    "id": 1,
    "interpretador": "Dr. João Silva",
    "texto": "Interpretação atualizada e expandida..."
}
```

### Deletar Interpretação
```http
DELETE /api/?param=interpretacao&id=1
```

---

## 🔧 Exemplos Práticos com cURL

### Fluxo Completo de Uso

```bash
# 1. Criar um novo sonho
curl -X POST "http://localhost/api/?param=sonhos" \
  -H "Content-Type: application/json" \
  -d '{
    "conteudo": "Sonhei que estava voando sobre uma cidade iluminada, sentia uma liberdade incrível",
    "tags": "voar,liberdade,cidade,noite"
  }'

# 2. Listar todos os sonhos
curl -X GET "http://localhost/api/?param=sonhos"

# 3. Buscar sonhos por tag específica
curl -X GET "http://localhost/api/?param=sonhos/tag&tag=voar"

# 4. Adicionar interpretação ao sonho
curl -X POST "http://localhost/api/?param=interpretacoes" \
  -H "Content-Type: application/json" \
  -d '{
    "sonho_id": 1,
    "interpretador": "Dr. Maria Santos",
    "texto": "O sonho de voar frequentemente simboliza o desejo de liberdade e superação de limitações. A cidade iluminada pode representar oportunidades e o período noturno sugere exploração do inconsciente."
  }'

# 5. Buscar interpretações do sonho
curl -X GET "http://localhost/api/?param=sonho/interpretacoes&sonho_id=1"

# 6. Ver tags mais populares
curl -X GET "http://localhost/api/?param=tags/populares"
```

### Exemplos de Tratamento de Erros

```bash
# Tentar criar sonho com conteúdo muito curto
curl -X POST "http://localhost/api/?param=sonhos" \
  -H "Content-Type: application/json" \
  -d '{"conteudo": "Curto"}'

# Resposta (400 Bad Request):
{
    "erro": "O conteúdo do sonho deve ter pelo menos 10 caracteres",
    "dados": null
}

# Buscar sonho inexistente
curl -X GET "http://localhost/api/?param=sonho&id=999"

# Resposta (404 Not Found):
{
    "erro": "ID inválido",
    "dados": null
}
```

## 🛡️ Validações e Regras de Negócio

### Sonhos
- **Conteúdo**: Obrigatório, mínimo 10 caracteres
- **Tags**: Opcionais, processadas automaticamente (criação/associação)
- **Relacionamentos**: Cascade delete para tags e interpretações associadas

### Tags
- **Nome**: 2-50 caracteres, deve ser único no sistema
- **Normalização**: Trim automático de espaços
- **Referências**: Verificação de integridade antes de exclusão

### Interpretações
- **Sonho**: Deve existir na base de dados
- **Interpretador**: 2-100 caracteres, obrigatório
- **Texto**: Mínimo 20 caracteres para garantir qualidade
- **Relacionamento**: Vinculada ao sonho via foreign key

## 🚀 Recursos Avançados

### Busca Flexível
- Busca por tags usando `LIKE` para correspondência parcial
- Suporte a múltiplas tags por sonho
- Ordenação cronológica (mais recentes primeiro)

### Otimizações de Performance
- Índices otimizados para consultas frequentes
- Prepared statements para todas as queries
- Connection pooling via Singleton pattern

### Extensibilidade
- Arquitetura baseada em interfaces permite fácil troca de implementações
- Services isolados facilitam adição de novas funcionalidades
- Autoloader personalizado para carregamento eficiente de classes

## 🛠️ Tecnologias e Dependências

- **PHP 7.4+**: Linguagem principal
- **MySQL 5.7+**: Banco de dados relacional
- **PDO**: Camada de abstração para banco de dados
- **Apache mod_rewrite**: Para URLs amigáveis
- **JSON**: Formato de troca de dados
