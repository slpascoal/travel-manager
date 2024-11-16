<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
API "Travel Manager"</br>
feita por: Silas Lopes Pascoal - slpascoal01@gmail.com
</p>

## Resumo da API:

Esta aplicação é um gerenciador de pedidos de viagens corporativas, desenvolvida em Laravel com uma API documentada usando Swagger. Ela permite que os usuários criem, visualizem, atualizem e excluam pedidos de viagem.

obs.: Optei por desenvolver a API toda em inglês, por conta do inglês ser o idioma padrão para código e documentação.

## Pré-Requisitos

Desenvolvi meu projeto a partir do Windows Subsystem for Linux 2 (WSL2) e Docker Engine on Ubuntu (https://docs.docker.com/engine/install/ubuntu/). 

Por ser uma aplicação Docker, irá rodar em qualquer ambiente configurado, mas a escolha de usar Laravel Sail (consequentemente WSL) foi para facilitar o desenvolvimento, sem precisar fazer nenhuma outra instalação de dependência, aproveitando ao máximo o Docker e otimizando o desenvolvimento.

### WSL2

Para instalar o WSL2, faça: 
- No Windows Terminal, execute: `wsl --install` e reinicie o computador
- Abra novamente o terminal e execute: `wsl –update`
- Com isso, execute `wsl` no terminal, para abrir o terminal em ambiente Ubuntu

### Docker

O Docker é necessário para rodar a aplicação, por conta do Laravel Sail. 

Existem várias formas de instalar o Docker para executar a aplicação, mas deixarei duas para sua escolha:
- Docker Desktop: a mais fácil e rápida, que baixa automaticamente tudo que é necessário.
    - https://www.docker.com/products/docker-desktop/
- Instalação Manual: mesmo sendo a mais trabalhosa, acho a mais estável (se instalada corretamente). Deve ser feita dentro WSL
    - https://docs.docker.com/engine/install/ubuntu/#install-from-a-package

obs.: o Docker Desktop deve estar aberto para os containers subirem.

## Instruções para Uso

Com o Docker Desktop aberto (ou Docker Engine instalado), abra o terminal e faça:
- Abra o WSL executando `wsl`
- Faça o Git Clone
- Após fazer o git clone, abra a pasta do projeto: `cd travel-manager/`
- Dentro da pasta do projeto, execute o seguinte comando para instalar as dependencias do Composer (requisitado na documentação [Laravel Sail](https://laravel.com/docs/11.x/sail#installing-composer-dependencies-for-existing-projects)):
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
``` 
- configure um alias para faclitar a digitação de comandos no terminal: `alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'`
- abra o Visual Studio Code e configure os arquivos ".env" e ".env.testing". No terminal, execute: `code .`
    - No VS Code, apenas tire os comentários dos campos "DB_" e insira a senha `password` em ambos arquivos
- Suba o projeto. Execute no terminal `sail up -d`
    - Caso tenham erro "error respose from daemon", execute no terminal `sudo service apache2 stop` e tente subir o projeto novamente. (pode ter um sistema apache na sua máquina)
- Execute as migrates: `sail art migrate`
    - No Laravel Sail, por padrão o usuário do banco de dados é "sail" e senha "password"

Tudo pronto, a API está pronta para ser usada!

### Documentação da API (Swagger)

Para ver a documentação da API, abra em seu navegador `http://localhost/api/documentation`

## Como usar a API

Para usar a API, faça os seguintes procedimentos:

### Autenticação

Muitas requisições necessitam de autenticação para serem executadas. É necessário ter um usuário cadastrado e logado na API.

Usando uma plataforma de API (Postman ou Insomnia) faça:
- Cadastro: POST `http://localhost/api/register`
    - passe no body JSON: nome, email, senha e confirme a senha (mais informações na documentação)
- Login: POST `http://localhost/api/login`
    - passe no body JSON email e senha (mais informações na documentação)
- Logout: POST `http://localhost/api/logout`
    - necessária autenticação. passe o token do usuário em Auth em modo "Bearer Token"

### Pedidos de viagem

Para consumir a API com suas funcionalidades de pedidos de viagem, usando uma plataforma de API (Postman ou Insomnia), faça:
- Criar um pedido de viagem: POST `http://localhost/api/travels`
    - necessária autenticação. passe o token do usuário em Auth em modo "Bearer Token"
    - passe no body JSON: nome do solicitante, destino, status (opcional), data de ida e data de volta (mais informações na documentação)
    - em "Headers", adicione uma key "Accept" com valor "application/json" (mais informações na documentação)
- Atualizar o status de um pedido de viagem: PUT/PATCH `http://localhost/api/travels/{ID DO PEDIDO DA VIAGEM}`
    - necessária autenticação. passe o token do usuário em Auth em modo "Bearer Token"
    - passe no body JSON: status (mais informações na documentação)
    - em "Headers", adicione uma key "Accept" com valor "application/json" (mais informações na documentação)
- Consultar um pedido de viagem: GET `http://localhost/api/travels/{ID DO PEDIDO DA VIAGEM}`
- Listar todos os pedidos de viagem: GET `http://localhost/api/travels`
- [BÔNUS] Deletar um pedido de viagem: DELETE `http://localhost/api/travels/{ID DO PEDIDO DA VIAGEM}`
    - necessária autenticação. passe o token do usuário em Auth em modo "Bearer Token"
    - em "Headers", adicione uma key "Accept" com valor "application/json" (mais informações na documentação)
  
### Executando Testes

Para executar testes, faça:
- No terminal, execute: `sail art test`

### Encerrando a aplicação

Para fechar a aplicação e seus containers, basta executar no terminal `sail down` e `exit` para sair do WSL

## Funcionalidades da API

A aplicação inclui funcionalidades importantes para gerenciar e proteger as operações, incluindo:

### Autenticação e Autorização
- Utiliza Laravel Sanctum para autenticação via token Bearer, protegendo alguns endpoints e permitindo apenas que usuários autenticados criem, atualizem e excluam viagens.
- Controla permissões com Gates para garantir que apenas usuários autorizados possam modificar ou excluir um pedido de viagem específico.

### Estrutura de Controle de Viagens
A aplicação possui um TravelController, onde são implementados os métodos principais para:
- `index`: Listar todos os pedidos de viagem, com possibilidade de filtrar por status (ex.: "requested", "approved", "canceled").
- `store`: Criar um novo pedido de viagem, validando campos como nome do solicitante, destino, data de partida e retorno, e verificando se já existe uma viagem para a mesma pessoa na mesma data.
- `show`: Exibir os detalhes de um pedido de viagem específico.
- `update`: Atualizar o status de um pedido de viagem.
- `destroy`: Excluir um pedido de viagem específico.

### Documentação da API com Swagger
- Utiliza anotações do Swagger para documentar os endpoints, incluindo informações sobre parâmetros, corpo da requisição e respostas.
- Configuração para autenticação com Bearer Token no Swagger, o que permite testar endpoints protegidos diretamente pela interface do Swagger.

### Validação de Dados e Tratamento de Erros
- Os campos são validados tanto na criação quanto na atualização dos pedidos de viagem para garantir consistência nos dados.
- Respostas adequadas de erro são retornadas, como no caso de um pedido duplicado, para melhorar a experiência do usuário e a integridade da aplicação.

### Tecnologias Usadas

- **[Docker](https://www.docker.com/)**
- **[Laravel Sail](https://laravel.com/docs/11.x/sail#main-content)**
- **[MySQL](https://www.mysql.com/)**
- **[PHPUnit](https://phpunit.de/index.html)**
- **[Laravel Sanctum](https://laravel.com/docs/11.x/sanctum#main-content)**
- **[Swagger](https://swagger.io/)**

## Licença

O framework Laravel é um software open-sourced licenciado por [MIT license](https://opensource.org/licenses/MIT).
