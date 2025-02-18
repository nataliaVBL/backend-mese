# 🚀 Projeto Mese

Este projeto se trata do backend do sistema da Mese desenvolvido com o framework Laravel. O sistema, até o momento, permite operações de CRUD e pesquisas dinâmicas. 

## ⚙️ Tecnologias

- PHP 8.2
- Laravel
- PostgresSQL
- Composer
- Docker

## 💻 Instalação

Após clonar o projeto, instale as dependências do projeto. 

```bash
composer install
```
Em seguida, é necessário configurar o banco de dados. Primeiro, copie o seguinte arquivo de ambiente: 

```bash
cp .env.example .env
```
Configure o .env com os detalhes do banco: 

```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nome_do_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```
O banco de dados está rodando em um contêiner Docker. Para criá-lo use: 

```bash
docker-compose up -d --build
```
Caso as tabelas do banco de dados não estejam criadas, use o comando: 

```bash
docker-compose exec app php artisan migrate
```
Para visualizar as tabelas você pode obtar por uma extensão ou um software de sua preferência. No meu caso, eu utilizei o Beekeeper Studio.

Depois de subir o banco de dados basta rodar o comando: 

```bash
php artisan serve --port=8001
```

## 🛠️ Endpoints da API

Tendo o servidor do próprio Laravel rodando, é possível utilizar as rotas declaradas no arquivo `routes/api.php`.

Até então, temos disponível os CRUDs de Painel, Módulo, Sensor e Usuário. 







