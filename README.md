API Sistema de Sonhos
Uma API RESTful para gerenciamento de sonhos, tags e interpretações, desenvolvida em PHP com arquitetura MVC.

**Arquitetura**

Controller: Recebe requisições HTTP e delega para services
Service: Regras de negócio e validações
DAO: Acesso ao banco de dados MySQL
Interfaces: Contratos para DAOs

**Banco de Dados**

sql
CREATE DATABASE banco_sonhos;

CREATE TABLE sonhos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conteudo TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE sonho_tag (
    sonho_id INT,
    tag_id INT,
    PRIMARY KEY (sonho_id, tag_id),
    FOREIGN KEY (sonho_id) REFERENCES sonhos(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE interpretacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sonho_id INT NOT NULL,
    interpretador VARCHAR(100) NOT NULL,
    texto TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sonho_id) REFERENCES sonhos(id) ON DELETE CASCADE
);

**Instalação**

Clone o projeto
Configure o banco MySQL
Ajuste as credenciais em generic/MysqlSingleton.php
Configure seu servidor web para apontar para index.php

**Endpoints**

Sonhos

Listar todos os sonhos
httpGET /api-sonho/?param=sonhos
Resposta:
json{
    "erro": null,
    "dados": [
        {
            "id": 1,
            "conteudo": "Sonhei que estava voando...",
            "criado_em": "2024-01-15 10:30:00",
            "tags": "voar, liberdade"
        }
    ]
}

Buscar sonho por ID
httpGET /api-sonho/?param=sonho&id=1

Criar novo sonho
httpPOST /api-sonho/?param=sonhos
Content-Type: application/json
{
    "conteudo": "Descrição do sonho aqui...",
    "tags": "tag1,tag2,tag3"
}

Atualizar sonho
httpPUT /api-sonho/?param=sonho
Content-Type: application/json
{
    "id": 1,
    "conteudo": "Novo conteúdo do sonho...",
    "tags": "nova_tag1,nova_tag2"
}

Deletar sonho
httpDELETE /api-sonho/?param=sonho&id=1

Buscar sonhos por tag
httpGET /api-sonho/?param=sonhos/tag&tag=voar

**Tags**

Listar todas as tags
httpGET /api/?param=tags

Buscar tag por ID
httpGET /api/?param=tag&id=1

Criar nova tag
httpPOST /api/?param=tags
Content-Type: application/json
{
    "nome": "pesadelo"
}

Atualizar tag
httpPUT /api/?param=tag
Content-Type: application/json
{
    "id": 1,
    "nome": "novo_nome"
}

Deletar tag
httpDELETE /api/?param=tag&id=1

Tags mais populares
httpGET /api/?param=tags/populares

**Interpretações**

Listar todas as interpretações
httpGET /api/?param=interpretacoes

Buscar interpretação por ID
httpGET /api/?param=interpretacao&id=1

Buscar interpretações de um sonho
httpGET /api/?param=sonho/interpretacoes&sonho_id=1

Criar nova interpretação
httpPOST /api/?param=interpretacoes
Content-Type: application/json
{
    "sonho_id": 1,
    "interpretador": "João Silva",
    "texto": "Este sonho representa o desejo de liberdade..."
}

Atualizar interpretação
httpPUT /api/?param=interpretacao
Content-Type: application/json
{
    "id": 1,
    "interpretador": "João Silva",
    "texto": "Interpretação atualizada..."
}

Deletar interpretação
httpDELETE /api/?param=interpretacao&id=1

**Validações**

Sonhos
Conteúdo: mínimo 10 caracteres
Tags: opcionais, separadas por vírgula

Tags
Nome: 2-50 caracteres
Deve ser único

Interpretações
Sonho deve existir
Interpretador: 2-100 caracteres
Texto: mínimo 20 caracteres

**Exemplo de Uso (cURL)**

bash# Criar um sonho
curl -X POST "http://localhost/api/?param=sonhos" \
  -H "Content-Type: application/json" \
  -d '{"conteudo":"Sonhei que estava voando sobre a cidade","tags":"voar,cidade"}'

# Listar sonhos
curl "http://localhost/api/?param=sonhos"

# Buscar por tag
curl "http://localhost/api/?param=sonhos/tag&tag=voar"

🛠️ Tecnologias

PHP 7.4+
MySQL 5.7+
PDO para conexão com banco
Arquitetura MVC personalizada