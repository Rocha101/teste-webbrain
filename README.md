# Sistema de Chamados de TI - Prefeitura

Sistema de gerenciamento de chamados de TI desenvolvido para a Prefeitura, permitindo o registro e acompanhamento de problemas técnicos, sugestões e incidentes.

## Tecnologias Utilizadas

- Frontend: Bootstrap 5, jQuery
- Backend: PHP 8
- Database: MySQL
- Server: XAMPP

## Requisitos do Sistema

- XAMPP com PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Navegador web moderno (Chrome, Firefox, Edge)

## Instalação

1. Clone este repositório para a pasta htdocs do XAMPP:
```bash
git clone [url-do-repositorio] teste-webbrain
```

2. Importe o arquivo database.sql no phpMyAdmin para criar o banco de dados

3. Configure o arquivo config/database.php com suas credenciais do banco de dados

4. Acesse o sistema através do navegador:
```
http://localhost/teste-webbrain/index.php
```

## Usuario Admin

Email: admin@prefeitura.com
Senha: senhaprefeitura

## Funcionalidades

- Sistema de autenticação completo
- Registro de chamados com anexos
- Timeline de atualizações
- Gestão de contatos por chamado
- Interface responsiva e amigável

## Estrutura do Projeto

```
/
├── assets/           # Arquivos estáticos (CSS, JS, imagens)
├── config/           # Arquivos de configuração
├── includes/         # Classes e funções PHP
├── uploads/          # Diretório para upload de arquivos
└── views/            # Templates das páginas
```

## Segurança

- Proteção contra SQL Injection
- Sanitização de dados
- Senhas criptografadas
- Proteção contra XSS
- Validação de sessão

## Autor

Desenvolvido como parte do desafio técnico para avaliação de habilidades em desenvolvimento web.
