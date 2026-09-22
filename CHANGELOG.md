# Changelog

Todas as mudanças notáveis do projeto são documentadas neste arquivo.
O formato segue [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e o projeto adota [Versionamento Semântico](https://semver.org/lang/pt-BR/).

## [1.0.0] - 2026-09-22

Primeira versão com todos os fluxos centrais funcionando de ponta a ponta
(navegação por categoria, carrinho, checkout, pagamento via Stripe,
confirmação de entrega e avaliações), autorização consistente em todas
as ações sensíveis, e cobertura de testes automatizados. Antes desta
versão, uma auditoria técnica completa (`AUDITORIA.md`) identificou 8
bugs bloqueadores — incluindo uma falha de controle de acesso (IDOR) no
carrinho e uma máquina de estados de pagamento quebrada — além de 11
itens de alta prioridade e 4 de débito técnico, todos corrigidos nesta
versão.

### Adicionado
- Auditoria técnica completa do estado do projeto (`AUDITORIA.md`).
- `config/shop.php` e `app/helpers.php`: configuração e formatação
  (moeda, upload de imagem, paginação, rate limits) centralizadas.
- `App\Enums\OrderStatus`: máquina de estados de pedido tipada
  (`cart → pending → paid → completed`, ou `cancelled`).
- Autorização por item de pedido (`OrderItemPolicy`) para ações de
  carrinho e avaliação.
- Comando agendado `orders:expire-stale` que cancela pedidos pendentes
  expirados e repõe o estoque reservado.
- Página de categoria (`tags.show`), busca de produtos e páginas
  institucionais ("sobre" / "projeto").
- Ações de confirmar entrega, avaliar produto e sacar saldo na UI
  (antes só existiam no backend, sem ponto de entrada).
- Rate limiting em toda a aplicação (carrinho, checkout, saque,
  avaliações, anúncios, perfil, busca, login, cadastro, reset de senha)
  e um limite global como rede de segurança.
- Fluxos de verificação de e-mail e redefinição de senha do Fortify,
  antes habilitados na config mas não funcionais.
- Páginas de erro customizadas (403, 404, 419, 429, 500, 4xx, 5xx).
- Suíte de testes automatizados cobrindo carrinho, pedidos, produtos,
  avaliações, busca e rate limiting (antes, cobertura zero).
- Catálogo de produtos mockados: 20 produtos por categoria de tag
  (8 categorias, 160 produtos), semeado no banco via `ProductSeeder`.

### Corrigido
- **[Segurança/IDOR]** Qualquer usuário autenticado podia alterar ou
  excluir itens do carrinho de outro usuário.
- Máquina de estados dessincronizada entre webhook de pagamento e
  confirmação de entrega, que tanto bloqueava entregas legítimas
  quanto permitia creditar saldo ao vendedor sem pagamento real.
- Edição de produto completamente bloqueada por um `authorize()`
  hardcoded como `false`.
- Upload de nova imagem ao editar produto corrompia a atualização.
- Avaliações nunca podiam ser criadas (nome de ability incorreto e
  `$fillable` do model `Rating` não batia com as colunas reais).
- Links de produto quebrados por uso indevido de `encrypt()` no id.
- View `tags.show` ausente, quebrando todos os links de categoria da
  home.
- Estoque decrementado no checkout sem estorno em cancelamento/abandono.
- `TagSeeder` nunca era chamado pelo `DatabaseSeeder`.
- N+1 queries no dashboard (uma consulta por categoria).
- Reputação do usuário no perfil totalmente mockada com dados fixos.

### Alterado
- Pipeline de build removido: o front-end usa CSS/JS estático direto,
  sem Vite/npm — um clone novo não precisa mais de Node.
