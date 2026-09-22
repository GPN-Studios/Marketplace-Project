# Marketplace Project

Marketplace de produtos construído em Laravel: navegação por categoria, carrinho,
checkout com pagamento via Stripe, confirmação de entrega e avaliações — com
autorização consistente em todas as ações sensíveis e cobertura de testes
automatizados.

## Funcionalidades

- **Catálogo e categorias** — produtos organizados por tags (categorias),
  com página de categoria, busca e carrossel na home.
- **Carrinho e checkout** — adicionar/atualizar/remover itens, checkout via
  [Stripe](https://stripe.com), confirmação de entrega e cancelamento, com
  estorno automático de estoque quando o pedido é cancelado ou expira.
- **Máquina de estados de pedido** tipada (`App\Enums\OrderStatus`):
  `cart → pending → paid → completed`, ou `cancelled`.
- **Avaliações** de produtos por item de pedido, com autorização própria
  (`OrderItemPolicy`).
- **Saldo e saque** do vendedor, creditado apenas após confirmação real de
  pagamento.
- **Autenticação** via [Laravel Fortify](https://laravel.com/docs/fortify):
  cadastro, login, verificação de e-mail e redefinição de senha.
- **Rate limiting** em toda a aplicação (carrinho, checkout, saque,
  avaliações, anúncios, perfil, busca, login, cadastro, reset de senha) mais
  um limite global como rede de segurança.
- **Comando agendado** (`orders:expire-stale`) que cancela pedidos pendentes
  expirados e repõe o estoque reservado.
- **Páginas de erro customizadas** (403, 404, 419, 429, 500, 4xx, 5xx).
- **Sem pipeline de build** — front-end em CSS/JS estático, sem Vite/npm.

## Stack

- PHP 8.2+ / [Laravel 12](https://laravel.com/docs/12.x)
- [Laravel Fortify](https://laravel.com/docs/fortify) (autenticação)
- [Stripe](https://stripe.com) (pagamentos)
- [Resend](https://resend.com) (envio de e-mail em produção)
- [spatie/laravel-tags](https://github.com/spatie/laravel-tags) (categorias)
- MySQL
- [Pest](https://pestphp.com) (testes)

## Instalação

Pré-requisitos: PHP 8.2+, Composer e um banco MySQL.

```bash
composer install
composer run setup
```

O comando `setup` copia o `.env.example` para `.env`, gera a `APP_KEY`, cria
o link de storage e roda as migrations. Depois, ajuste as variáveis no `.env`
(ao menos `DB_*` e, para pagamentos, `STRIPE_KEY` / `STRIPE_SECRET` /
`STRIPE_WEBHOOK_SECRET` — veja `.env.example` para a lista completa,
incluindo as opções de `config/shop.php`: moeda, expiração de checkout,
upload de imagens, paginação e limites de rate limiting).

Para popular o catálogo com produtos de exemplo (160 produtos mockados em
8 categorias):

```bash
php artisan db:seed
```

### Rodando localmente

```bash
composer run dev
```

Isso sobe o servidor (`php artisan serve`) e o worker de fila
(`php artisan queue:listen`) juntos. Sem servidor de fila, e-mails e outros
jobs assíncronos não são processados.

Para o webhook do Stripe funcionar localmente, use o
[Stripe CLI](https://stripe.com/docs/stripe-cli) apontando para
`/webhook/stripe`.

## Testes

```bash
composer test
```

A suíte cobre carrinho (incluindo o IDOR corrigido), ciclo de vida de
pedidos, atualização de produtos, avaliações, busca e rate limiting.

## Documentação adicional

- [`AUDITORIA.md`](AUDITORIA.md) — auditoria técnica completa realizada
  antes da v1.0.0.
- [`CHANGELOG.md`](CHANGELOG.md) — histórico de mudanças por versão.

## Licença

Este projeto é distribuído sob a [licença MIT](LICENSE).
