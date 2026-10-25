# Crud-Blog

Blog com **login** e **CRUD de posts** (criar, ler, editar e apagar), feito com **PHP puro e SQLite**. Cada pessoa cria uma conta, escreve os próprios posts e só pode editar ou apagar o que é seu. O visual segue o tema espacial do [Blog](https://github.com/Nidragnnar/Blog).

## Recursos

- Página inicial com todos os posts, do mais novo para o mais antigo
- Página de cada post, com parágrafos
- Criar conta, entrar e sair
- Painel "Meus posts" para escrever, editar e apagar
- Banco **SQLite**: um único arquivo, criado automaticamente na primeira vez
- Pronto para rodar com **Docker** ou com o servidor embutido do PHP

## Segurança

| Risco | Como o projeto se protege |
| --- | --- |
| SQL injection | Todas as consultas usam PDO com *prepared statements* (`?`) |
| XSS (código injetado na página) | Todo texto do usuário passa por `htmlspecialchars` antes de aparecer |
| CSRF (outro site enviando formulários por você) | Todo formulário leva um token secreto da sessão |
| Senhas | Guardadas com `password_hash` e conferidas com `password_verify` |
| Mexer no post de outra pessoa | Editar e apagar sempre conferem se o post é do usuário logado |
| Roubo de sessão | Cookie `HttpOnly` e `SameSite=Lax`, e novo ID de sessão ao entrar |
| Arquivos internos expostos | Só a pasta `public/` fica acessível pelo navegador |

## Estrutura

```
Crud-Blog/
├── public/              # única pasta acessível pelo navegador
│   ├── index.php        # lista de posts
│   ├── post.php         # um post (post.php?id=1)
│   ├── cadastro.php     # criar conta
│   ├── entrar.php       # login
│   ├── sair.php         # logout
│   ├── painel.php       # meus posts
│   ├── editor.php       # novo post / editar (editor.php?id=1)
│   ├── excluir.php      # apagar post
│   └── assets/          # CSS e JavaScript
├── src/
│   ├── bootstrap.php    # configuração e sessão
│   ├── banco.php        # conexão SQLite e criação das tabelas
│   └── funcoes.php      # segurança, login e utilidades
├── views/               # cabeçalho e rodapé das páginas
├── dados/               # banco blog.sqlite (criado sozinho, fora do Git)
├── Dockerfile
└── docker-compose.yml
```

## Como rodar

### Com Docker

```bash
docker compose up --build
```

Acesse http://localhost:8000. O banco fica guardado no volume `dados` do Docker, então os posts continuam lá mesmo depois de recriar o container.

### Sem Docker

Precisa do PHP 8.1 ou mais novo com a extensão `pdo_sqlite` (já vem na maioria das instalações).

```bash
php -S localhost:8000 -t public
```

Acesse http://localhost:8000. O banco é criado em `dados/blog.sqlite`.

> Para usar outro arquivo de banco, defina a variável de ambiente `BLOG_DB` com o caminho desejado.

## Banco de dados

```sql
usuarios (id, nome, email, senha_hash, criado_em)
posts    (id, autor_id → usuarios.id, titulo, conteudo, criado_em, atualizado_em)
```

As tabelas são criadas automaticamente em `src/banco.php`. Para começar do zero, basta apagar o arquivo `dados/blog.sqlite`.

## Hospedagem

O GitHub Pages não roda PHP. Para colocar o blog online, use uma hospedagem com PHP (o SQLite não precisa de servidor de banco) ou um serviço que rode Docker.
