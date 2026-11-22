# 🚚 LogiFrete: Gerenciador Logístico em PHP

Sistema web para **gerenciar fretes**: cadastro de produtos, endereços (origem e destino) e transportadores, geração de fretes em que **um transportador leva vários produtos** e emissão de **relatório diário** para download.

Projeto da disciplina **Fundamentos de Programação para Internet** do curso de Engenharia de Software da **UNIPAR EAD**.

**Aluno:** Pedro Henrique da Silva Rufino
**Professor:** Huilson José Lorenzi

---

## ✨ Funcionalidades

- **Painel** com o total de cadastros e os últimos fretes
- **Produtos:** cadastrar, editar, excluir e buscar (nome, descrição, peso e valor)
- **Endereços:** cadastrar, editar, excluir e buscar, com **preenchimento automático pelo CEP** (API ViaCEP)
- **Transportadores:** cadastrar, editar, excluir e buscar, com máscaras de CPF/CNPJ e telefone
- **Fretes:** ligam 1 transportador + 1 origem + 1 destino + **N produtos**
  - Os produtos são adicionados e removidos **dinamicamente com JavaScript**
  - Peso total e valor da carga calculados em tempo real
  - Status: Pendente, Em trânsito, Entregue e Cancelado
  - Filtros por data e por status
- **Relatório do dia:** dados agrupados e formatados em PHP, com download em **CSV (Excel)** e opção de **salvar em PDF** pela impressão do navegador

## 🛠️ Tecnologias

| Camada | Tecnologia |
|---|---|
| Backend | PHP 8 com PDO e Prepared Statements |
| Banco de dados | MySQL / MariaDB (XAMPP) |
| Frontend | HTML5, CSS3 e JavaScript puro (ES6+) |
| Visual | Bootstrap 5.3 e Bootstrap Icons |
| API externa | ViaCEP (consulta de CEP) |
| Versionamento | Git e GitHub |

## 📁 Estrutura de pastas

```
logistica/
├── index.php               # Painel inicial
├── config/
│   ├── config.php          # Dados de acesso ao banco
│   └── conexao.php         # Conexão PDO
├── includes/
│   ├── funcoes.php         # Funções auxiliares (formatação, flash, etc.)
│   ├── cabecalho.php       # Topo + menu de navegação
│   └── rodape.php          # Rodapé + scripts
├── produtos/               # CRUD de produtos
├── enderecos/              # CRUD de endereços
├── transportadores/        # CRUD de transportadores
├── fretes/
│   ├── index.php           # Lista com filtros
│   ├── form.php            # Cadastro/edição com itens dinâmicos
│   ├── salvar.php          # Grava frete + itens (transação)
│   ├── ver.php             # Detalhes do frete
│   ├── excluir.php
│   └── relatorio.php       # Relatório diário + download CSV
├── assets/
│   ├── css/estilo.css
│   └── js/
│       ├── app.js          # Máscaras, ViaCEP, confirmação de exclusão
│       └── frete.js        # Campos dinâmicos de produtos no frete
├── database/
│   └── logistica.sql       # Script de criação do banco + dados de exemplo
└── docs/
    └── telas.pdf           # Protótipo das telas (Etapa 1)
```

## 🗄️ Modelo de dados

```
transportadores 1 ──── N fretes N ──── 1 enderecos (origem)
                           │    N ──── 1 enderecos (destino)
                           │
                           1
                           │
                           N
                      frete_itens N ──── 1 produtos
```

Um **frete** pertence a um único transportador e tem vários **itens** (`frete_itens`). Cada item aponta para um produto e guarda a quantidade. É assim que se implementa a relação **1 para muitos**.

## ▶️ Como executar (XAMPP)

1. Instale o [XAMPP](https://www.apachefriends.org/pt_br/index.html) e inicie o **Apache** e o **MySQL** no painel de controle.
2. Copie a pasta `logistica` para `C:\xampp\htdocs\` (no Linux/Mac: `/opt/lampp/htdocs/`).
3. Acesse `http://localhost/phpmyadmin`, abra a aba **Importar**, selecione `database/logistica.sql` e clique em **Executar**.
4. Abra `http://localhost/logistica` no navegador.

> Se o seu MySQL tiver senha, altere `config/config.php`.

## 🔒 Boas práticas aplicadas

- **Prepared Statements (PDO)** em todas as consultas com dados do usuário, contra *SQL Injection*
- **`htmlspecialchars`** em toda saída, contra *XSS*
- **Transação** ao salvar o frete e seus itens, para não deixar dados pela metade
- Validação no **frontend** (HTML5/JS) e no **backend** (PHP)
- Exclusões via **POST**, com confirmação
- Chaves estrangeiras impedem apagar registros que já estão em uso
- Código separado por responsabilidade (config, includes, módulos, assets)

## 📚 Referências

- PHP.NET. *Manual do PHP*. Disponível em: https://www.php.net/manual/pt_BR/. Acesso em: 21 nov. 2026.
- W3SCHOOLS. *HTML, CSS, JavaScript e PHP Reference*. Disponível em: https://www.w3schools.com. Acesso em: 21 nov. 2026.
- BOOTSTRAP. *Bootstrap 5.3 Documentation*. Disponível em: https://getbootstrap.com/docs/5.3/. Acesso em: 21 nov. 2026.
- HAVERBEKE, Marijn. *JavaScript Eloquente*. 2. ed. Disponível em: https://github.com/braziljs/eloquente-javascript. Acesso em: 21 nov. 2026.
- CHACON, Scott; STRAUB, Ben. *Pro Git*. 2. ed. Disponível em: https://git-scm.com/book/pt-br/v2. Acesso em: 21 nov. 2026.
- VIACEP. *Webservice CEP*. Disponível em: https://viacep.com.br. Acesso em: 21 nov. 2026.
