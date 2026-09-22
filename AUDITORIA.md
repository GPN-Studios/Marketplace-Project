# Auditoria Técnica — Marketplace Project (Laravel 12 / Blade / Bootstrap)

**Data:** 2026-09-21
**Escopo:** rotas, controllers, models, migrations, views, policies, integrações (Stripe, Fortify/auth, e-mail), testes, dependências e configuração de ambiente.
**Metodologia:** leitura integral do código-fonte (sem execução da aplicação), rastreamento manual de cada rota até a view final e cruzamento de `$fillable` dos models com as colunas reais das migrations e com os dados enviados pelos controllers.
**Natureza deste documento:** diagnóstico puro. Nenhuma alteração de código foi feita nesta etapa, conforme solicitado.

---

## Sumário executivo

O projeto é um marketplace funcional em sua estrutura (produtos, carrinho, pedidos, pagamento via Stripe, avaliações, saldo de vendedor), mas contém **6 bugs que quebram fluxos centrais de ponta a ponta** (edição de produto, avaliações, páginas de tag/categoria, links de produto) e **1 falha grave de controle de acesso (IDOR)** que permite a qualquer usuário autenticado alterar ou excluir itens do carrinho de outro usuário. Também existe uma **falha na máquina de estados do pagamento** que, dependendo do caminho percorrido, ou impede a confirmação de entrega após pagamento real, ou permite creditar o saldo do vendedor sem que o Stripe tenha processado qualquer pagamento.

Não foram encontradas evidências de SQL injection, XSS refletido via Blade (`{!! !!}`) ou CSRF ausente em formulários — esses pontos foram verificados especificamente e estão corretos. A cobertura de testes automatizados é **zero** (apenas os stubs padrão do Laravel/Pest).

Nenhum comentário `TODO`/`FIXME` foi encontrado no código — os problemas abaixo não estavam sinalizados pelos próprios desenvolvedores, o que sugere que grande parte não foi percebida.

---

## 1. Mapeamento da arquitetura

### 1.1 Rotas → Controllers → Views

| Rota | Controller | View | Status |
|---|---|---|---|
| `GET /` (`home`) | DashboardController::home | dashboard.blade.php | OK |
| `GET /tags/{tag:slug}` (`tags.show`) | closure em web.php | **`tags.show` não existe** | **Quebrado (B7)** |
| `GET /profile/{user}` | UserController::profile | profile.blade.php | OK, mas com dados fake (A11) |
| `PATCH /update/{user}` | UserController::update | — | OK |
| `PATCH /pfpupdate/{user}` | UserController::pfpupdate | — | OK |
| `GET /my-Products` | UserController::userProducts | user_products.blade.php | OK |
| `GET /products/create` | ProductController::create | products/create.blade.php | OK |
| `POST /products/store` | ProductController::store | — | OK |
| `GET /products/show/{product}` | ProductController::show | products/show.blade.php | OK (mas ver B8) |
| `PATCH /products/update/{product}` | ProductController::update | products/edit.blade.php | **Quebrado (B3, B4)** |
| `GET /products/edit/{product}` | ProductController::edit | products/edit.blade.php | OK |
| `DELETE /products/delete/{product}` | ProductController::delete | — | OK |
| `GET /cart` | OrderController::index | cart/cart_index.blade.php | OK |
| `POST /cart/add/{product}` | OrderController::add | — | OK (falta validação de estoque, A14) |
| `PATCH /cart/update/{item}` | OrderController::update | — | **Sem autorização (B1)** |
| `DELETE /cart/delete/{item}` | OrderController::delete | — | **Sem autorização (B1)** |
| `POST /checkout/{order}` (`checkout`) | CheckoutController::checkout | — | Funciona, mas ver A9 |
| `POST /checkout/{order}/complete` | CheckoutController::confirmDelivery | — sem link na UI | **Quebrado (B2), órfão (A12)** |
| `POST /ratings/{orderItem}` | RatingController::store | — sem link na UI | **Quebrado (B5, B6), órfão (A12)** |
| `POST /withdraw` | CheckoutController::withdraw | — sem link na UI | Órfão (A12) |
| `GET /my-Orders` | OrderController::myOrders | user_orders.blade.php | OK |
| `GET /checkout/success/{order}` | CheckoutController::success | checkout_success.blade.php | View placeholder (A11-bis) |
| `GET /checkout/cancel/{order}` | CheckoutController::cancel | checkout_cancel.blade.php | View placeholder, não estorna estoque (A9) |
| `POST /webhook/stripe` | StripeWebhookController::handle | — | OK, assinatura validada corretamente |
| `POST /orders/{order}/checkout` (`orders.checkout`) | StripeController::checkout | — | OK |
| `GET /login`, `GET /signup` | Fortify (view registrada) | auth/login, auth/signup | OK, mas "Esqueceu a senha" quebrado (A17) |

Não existe `routes/api.php` no projeto — a aplicação não expõe nenhuma API além do webhook do Stripe.

### 1.2 Models × Migrations × uso real

| Model | Migration correspondente | `$fillable` bate com as colunas usadas? |
|---|---|---|
| User | `0001_01_01_000000_create_users_table` + `add_balance_to_users_table` | Sim |
| Product | `create_products_table` | Sim |
| Order | `create_orders_table` | Sim |
| OrderItem | `create_order_items_table` | Sim |
| OrderAddress | `create_order_addresses_table` | Model e migration OK entre si, **mas nunca é populado por nenhum controller** (B18) |
| Rating | `create_ratings_table` | **Não** — `$fillable` lista colunas que não existem (`rating`, `comment`) e omite as que existem e são usadas (`is_positive`, `description`) (B6) |

### 1.3 Integrações externas

- **Stripe (pagamento):** `StripeController` (criação de sessão) + `StripeWebhookController` (confirmação via webhook, com verificação de assinatura correta) + `stripe/stripe-php` ^19.3. Implementado, mas com falha de state machine (B2) e sem tratamento de expiração de sessão/pedido abandonado (A9).
- **Autenticação (Laravel Fortify):** login, registro e alteração de senha/perfil implementados e com views customizadas. `emailVerification` e `resetPasswords` estão **habilitados no `config/fortify.php` mas não finalizados** (A16, A17).
- **E-mail:** apenas `MAIL_MAILER=log` configurado; nenhuma Mailable/Notification customizada existe no projeto além das notificações padrão do Fortify (verificação de e-mail, reset de senha) — que, como acima, não estão totalmente conectadas.
- **Tags (spatie/laravel-tags):** implementado e em uso (produtos, dashboard, seeder), porém a `TagSeeder` nunca é chamada pelo `DatabaseSeeder` (D20).

---

## 2. Achados priorizados

Classificação usada: **[Quebrado]** funcionalidade existe mas não opera como deveria · **[Incompleto]** parcialmente implementado · **[Falta implementar]** ausente · **[Débito técnico]** funciona mas é frágil/mal estruturado · **[Segurança]** risco de exploração.
Esforço: **P** (pequeno, horas) · **M** (médio, 1–2 dias) · **G** (grande, >2 dias).

### 🔴 Bloqueadores

---

**B1 — [Segurança] IDOR: qualquer usuário autenticado pode alterar ou excluir itens do carrinho de outro usuário**
📁 [app/Http/Controllers/OrderController.php:66-106](app/Http/Controllers/OrderController.php#L66-L106)
Os métodos `update()` e `delete()` recebem `OrderItem $item` via route model binding, mas nunca verificam se `$item->order->user_id === Auth::id()`. Não existe `OrderItemPolicy`, nem chamada a `$this->authorize()` nesses dois métodos (diferente de praticamente todo o resto do sistema, que usa policies consistentemente).
**Causa raiz:** a policy de `Order` (`OrderPolicy`) cobre `checkout`/`pay`/`confirmDelivery`, mas ninguém estendeu a checagem de posse para o nível de `OrderItem` quando as rotas de carrinho foram criadas.
**Impacto:** um usuário mal-intencionado pode iterar IDs de `OrderItem` (`PATCH /cart/update/{item}` ou `DELETE /cart/delete/{item}`) e manipular/apagar o carrinho de terceiros.
**Esforço:** P — adicionar checagem de posse (policy ou verificação inline) nos dois métodos.

---

**B2 — [Quebrado][Segurança] Máquina de estados do pedido dessincronizada entre webhook de pagamento e confirmação de entrega**
📁 [app/Policies/OrderPolicy.php:18-21](app/Policies/OrderPolicy.php#L18-L21) · [app/Http/Controllers/StripeWebhookController.php:83-86](app/Http/Controllers/StripeWebhookController.php#L83-L86) · [app/Http/Controllers/CheckoutController.php:61-79](app/Http/Controllers/CheckoutController.php#L61-L79)
`confirmDelivery` (policy) só autoriza quando `$order->status === 'pending'`. Porém, o webhook do Stripe, ao confirmar o pagamento, muda o status para `'paid'` (não existe transição para `'pending'→'paid'→confirmDelivery`). Resultado:
1. **Fluxo legítimo quebrado:** depois que o comprador paga de verdade via Stripe, o pedido vira `'paid'`, e `confirmDelivery` (que exigiria `'pending'`) nunca mais poderá ser autorizado — 403 permanente. A avaliação do produto (`RatingPolicy::rate`, que exige `status === 'completed'`) também fica inalcançável por esse caminho.
2. **Bypass de pagamento:** enquanto o pedido ainda está `'pending'` (antes/sem nunca ter sido pago), qualquer requisição autenticada para `POST /checkout/{order}/complete` passa na policy, credita o saldo do vendedor (`$seller->balance += $item->subtotal`) e marca o pedido como `'completed'` — **sem que o Stripe tenha processado nada**.
**Causa raiz:** quando o status `'paid'` foi introduzido (via webhook), a policy de `confirmDelivery` não foi atualizada para refletir a nova transição de estados.
**Esforço:** M — redesenhar a máquina de estados (`pending → paid → completed`) e ajustar a policy.

---

**B3 — [Quebrado] Edição de produto está completamente bloqueada**
📁 [app/Http/Requests/UpdateProductRequest.php:12-15](app/Http/Requests/UpdateProductRequest.php#L12-L15)
```php
public function authorize(): bool
{
    return false;
}
```
Como o `FormRequest::authorize()` retorna `false`, o Laravel lança `AuthorizationException` (403) **antes mesmo de o controller ser executado** — independentemente de o usuário ser o dono do produto ou não. A checagem correta (`$this->authorize('update', $product)`) já existe dentro do controller, tornando este `false` redundante e destrutivo.
**Causa raiz:** valor padrão do `make:request` do Artisan nunca foi trocado para `true` quando a autorização real foi movida para dentro do controller/policy.
**Impacto:** ninguém consegue editar um produto pela UI (o formulário em `products/edit.blade.php` está 100% funcional na aparência, mas todo submit falha).
**Esforço:** P.

---

**B4 — [Quebrado] Upload de nova imagem ao editar produto corrompe a atualização**
📁 [app/Http/Controllers/ProductController.php:50-57](app/Http/Controllers/ProductController.php#L50-L57)
```php
public function update(UpdateProductRequest $request, Product $product): RedirectResponse
{
    $this->authorize('update', $product);
    $product->update($request->validated());
    ...
}
```
Diferente de `store()`, que chama `$request->file('image')->store('products', 'public')`, o `update()` passa `$request->validated()` direto — e quando o campo `image` está presente, `validated()` contém a instância bruta de `UploadedFile`, não um caminho de arquivo. O arquivo nunca é salvo em disco, e a tentativa de persistir um objeto na coluna string `image` provoca erro fatal na camada de banco.
**Causa raiz:** lógica de armazenamento de arquivo (`->store()`) foi implementada apenas em `store()` e não replicada em `update()`.
**Nota:** este bug está "escondido" atrás do B3 — só se manifesta depois que B3 for corrigido.
**Esforço:** P.

---

**B5 — [Quebrado] Avaliações (ratings) nunca podem ser criadas — nome de ability incorreto**
📁 [app/Http/Controllers/RatingController.php:14](app/Http/Controllers/RatingController.php#L14) vs [app/Policies/RatingPolicy.php:10](app/Policies/RatingPolicy.php#L10)
O controller chama `$this->authorize('create', $orderItem)`, mas a única ability definida na `RatingPolicy` se chama `rate`, não `create`. Toda chamada a este endpoint falha na autorização.
**Causa raiz:** dessincronia entre o nome do método da policy e a string passada ao `authorize()` — provavelmente a policy foi renomeada de `create` para `rate` (nome mais semântico) sem atualizar o controller.
**Esforço:** P.

---

**B6 — [Quebrado] `$fillable` do model `Rating` não corresponde às colunas reais nem aos dados enviados pelo controller**
📁 [app/Models/Rating.php:9-15](app/Models/Rating.php#L9-L15) vs [database/migrations/2026_01_13_010152_create_ratings_table.php:17-28](database/migrations/2026_01_13_010152_create_ratings_table.php#L17-L28) vs [app/Http/Controllers/RatingController.php:21-27](app/Http/Controllers/RatingController.php#L21-L27)
- Migration cria as colunas `description` (nullable) e `is_positive` (**NOT NULL**).
- Controller envia `is_positive` e `description` no `Rating::create()`.
- Model declara `$fillable = ['order_item_id','buyer_id','seller_id','rating','comment']` — colunas `rating`/`comment` **não existem** na tabela, e `is_positive`/`description` **não estão no fillable**, sendo descartadas silenciosamente pelo mass assignment.
**Impacto:** mesmo corrigindo B5, o `INSERT` falhará com violação de constraint `NOT NULL` em `is_positive` — erro 500.
**Causa raiz:** os nomes dos campos foram alterados em algum momento (provavelmente de uma nota "rating 1-5 + comment" para "positivo/negativo + descrição") na migration e no controller, mas o model ficou para trás.
**Esforço:** P.
*(B5 + B6 juntos: toda a cadeia de avaliações — migration, model, policy, controller, rota — existe, mas está 100% não-funcional de ponta a ponta.)*

---

**B7 — [Falta implementar] View `tags.show` não existe — todos os links de categoria da home retornam erro**
📁 [routes/web.php:88-96](routes/web.php#L88-L96) referenciada por [resources/views/dashboard.blade.php:10](resources/views/dashboard.blade.php#L10), [:18](resources/views/dashboard.blade.php#L18) e [:77](resources/views/dashboard.blade.php#L77)
A rota `tags.show` faz `return view('tags.show', compact('tag','products'))`, mas não existe `resources/views/tags/show.blade.php` em lugar nenhum do projeto. O banner principal da home, todos os cards de categoria e todos os links "Ver mais" apontam para essa rota — ou seja, a home inteira contém múltiplos links quebrados (`ViewNotFoundException`, HTTP 500).
**Causa raiz:** rota e query foram implementadas, mas a view nunca foi criada.
**Esforço:** M (criar a view seguindo o padrão de listagem já usado em `products/show.blade.php`/dashboard).

---

**B8 — [Quebrado] Links de produto usando `encrypt($product->id)` retornam 404**
📁 [app/Http/Controllers/ProductController.php:56](app/Http/Controllers/ProductController.php#L56) · [resources/views/profile.blade.php:150](resources/views/profile.blade.php#L150)
Ambos os pontos geram a URL com `route('products.show', encrypt($product->id))`. Como a rota `products/show/{product}` usa route model binding padrão por `id` (não há `getRouteKeyName()`/`resolveRouteBinding()` customizado em `Product`), o Laravel tenta buscar um produto cujo `id` seja a string encriptada inteira — nunca encontra, resultando em 404.
**Causa raiz:** parece uma tentativa isolada e abandonada de ofuscar IDs na URL, aplicada de forma inconsistente — a maioria dos links (ex.: `dashboard.blade.php:49`) usa `route('products.show', $product)` corretamente, sem `encrypt()`.
**Impacto:** o redirect pós-edição de produto (uma vez corrigido B3/B4) leva a um 404; a grade "Meus Anúncios" no perfil do usuário está com todos os cards quebrados.
**Esforço:** P — remover o `encrypt()` nos dois locais.

---

### 🟠 Alta prioridade

---

**A9 — [Débito técnico][Quebrado] Estoque é decrementado antes da confirmação de pagamento, sem rollback em cancelamento/abandono**
📁 [app/Http/Controllers/CheckoutController.php:19-58](app/Http/Controllers/CheckoutController.php#L19-L58) · [resources/views/checkout_cancel.blade.php](resources/views/checkout_cancel.blade.php)
`checkout()` decrementa `$product->stock` e marca o pedido como `'pending'` **antes** de qualquer interação com o Stripe. Se o comprador cancelar o checkout do Stripe (`checkout.cancel`) ou simplesmente abandonar a aba, a view de cancelamento é um placeholder vazio (`<!-- ... -->`) que não realiza nenhum estorno de estoque nem expira o pedido. O estoque fica permanentemente "preso" em pedidos nunca pagos.
**Causa raiz:** ausência de um mecanismo de expiração/estorno para pedidos `'pending'` não finalizados (job agendado, ou estorno no próprio `cancel()`).
**Esforço:** M.

---

**A10 — [Segurança][Débito técnico] `.env.example` não documenta as variáveis do Stripe**
📁 [.env.example](.env.example) vs [config/services.php:38-42](config/services.php#L38-L42) vs [.env:64-66](.env#L64-L66)
`STRIPE_KEY`, `STRIPE_SECRET` e `STRIPE_WEBHOOK_SECRET` são lidas em `config/services.php`, mas não aparecem no `.env.example`. Quem clonar o projeto e rodar `composer run setup` (que copia `.env.example` para `.env`) terá a integração de pagamento quebrada silenciosamente, sem nenhuma pista de quais variáveis faltam.
**Nota:** o `.env` atual do repositório contém chaves de teste do Stripe (`pk_test_`/`sk_test_`) — não são segredos de produção e o arquivo está corretamente listado no `.gitignore` (confirmado: nunca foi commitado), mas o hábito de deixar segredos, mesmo de teste, em `.env` sem versionamento do template correto é um risco de higiene.
**Esforço:** P.

---

**A11 — [Falta implementar] Reputação do usuário no perfil é inteiramente mockada com dados fixos**
📁 [resources/views/profile.blade.php:68-79](resources/views/profile.blade.php#L68-L79) e [:129-139](resources/views/profile.blade.php#L129-L139)
Os números "581 Positivas / 20 Negativas" e os dois textos de avaliação ("Atendimento demorado demais...", "Comprei unranked mas veio platina...") são hardcoded no HTML — não vêm de `$user->ratingsReceived()`, relação que já existe no model `User` ([app/Models/User.php:70-73](app/Models/User.php#L70-L73)) mas nunca é chamada por `UserController::profile`. Há inclusive uma inconsistência de rótulo copiada (`class="profile-review positive"` envolvendo um texto com `review-meta negative`).
**Causa raiz:** placeholder visual de layout que nunca foi religado à camada de dados real.
**Esforço:** M.

---

**A12 — [Falta implementar] Endpoints "confirmar entrega", "avaliar" e "sacar saldo" não têm nenhum ponto de entrada na UI**
📁 [routes/web.php:61,63,65](routes/web.php#L61) — nenhuma referência a `checkout.complete`, `ratings.store` ou `withdraw` em `resources/views/**`
O backend dessas três ações existe (rotas, controllers, policies), mas nenhuma view do projeto contém um formulário/botão que os acione. Atualmente só são alcançáveis manipulando requisições manualmente.
**Causa raiz:** backend implementado antes do frontend correspondente; trabalho interrompido no meio.
**Esforço:** M (por ação; envolve também corrigir B2, B5, B6 para que funcionem de fato).

---

**A13 — [Segurança] Validação de MIME de imagem inconsistente entre criar e editar produto**
📁 [app/Http/Requests/StoreProductRequest.php:30](app/Http/Requests/StoreProductRequest.php#L30) (`mimes:jpeg,png,webp`) vs [app/Http/Requests/UpdateProductRequest.php:26](app/Http/Requests/UpdateProductRequest.php#L26) (`sometimes|image|max:2048`, sem `mimes`)
Na edição, a regra `image` sozinha aceita qualquer formato reconhecido como imagem pelo Laravel (inclui SVG, BMP, GIF), diferente da criação, que restringe a JPEG/PNG/WEBP. Baixo risco prático (arquivo é servido via `<img src>`, não incorporado inline), mas é uma inconsistência de validação que deveria ser padronizada — SVG em particular pode carregar script embutido em alguns contextos de exibição.
**Esforço:** P.

---

**A14 — [Débito técnico] Falta checagem de estoque ao adicionar item ao carrinho**
📁 [app/Http/Controllers/OrderController.php:25-64](app/Http/Controllers/OrderController.php#L25-L64)
`add()` valida apenas `quantity >= 1`, sem comparar com `$product->stock` (diferente de `update()`, que faz essa checagem nos botões de +/-). Um usuário pode colocar 999 unidades de um produto com estoque 2 no carrinho sem aviso algum. Isso só é finalmente barrado em `CheckoutController::checkout()` — ou seja, não é falha de segurança (não há overselling), mas é UX/validação inconsistente entre camadas.
**Esforço:** P.

---

**A15 — [Débito técnico] N+1 queries no dashboard**
📁 [app/Http/Controllers/DashboardController.php:13-21](app/Http/Controllers/DashboardController.php#L13-L21)
```php
$tags = Tag::all()->map(function ($tag) {
    $tag->products = Product::withAnyTags([$tag->name])->latest()->take(6)->get();
    return $tag;
});
```
Uma query `Product::withAnyTags` é disparada por tag dentro do `map()`. Com 8 tags (conforme `TagSeeder`), são 9 queries na home a cada carregamento (1 + N), ao invés de 1–2 queries otimizadas.
**Esforço:** P/M.

---

**A16 — [Débito técnico] Verificação de e-mail habilitada no Fortify mas nunca implementada no model `User`**
📁 [config/fortify.php:149](config/fortify.php#L149) (`Features::emailVerification()`) vs [app/Models/User.php:5](app/Models/User.php#L5) (`// use Illuminate\Contracts\Auth\MustVerifyEmail;` comentado — a classe **não implementa** a interface)
Com a feature habilitada na config mas o model não implementando `MustVerifyEmail`, o fluxo de verificação de e-mail do Fortify (incluindo `UpdateUserProfileInformation::updateVerifiedUser`, [app/Actions/Fortify/UpdateUserProfileInformation.php:32-34](app/Actions/Fortify/UpdateUserProfileInformation.php#L32-L34)) nunca é de fato acionado — o `instanceof MustVerifyEmail` é sempre falso.
**Causa raiz:** feature copiada do scaffold padrão do Fortify, nunca finalizada nem conscientemente desativada.
**Esforço:** P (decidir: implementar de verdade ou remover a feature da config).

---

**A17 — [Falta implementar] Reset de senha habilitado no Fortify mas sem views nem link funcional**
📁 [config/fortify.php:148](config/fortify.php#L148) (`Features::resetPasswords()`) · [app/Providers/FortifyServiceProvider.php:53-59](app/Providers/FortifyServiceProvider.php#L53-L59) (só registra `loginView`/`registerView`, faltam `requestPasswordResetLinkView`/`resetPasswordView`) · [resources/views/auth/login.blade.php:47](resources/views/auth/login.blade.php#L47) (`<a href="#">Esqueceu a senha?</a>`)
A feature está ativa e `ResetUserPassword`/action existe e está corretamente implementada, mas nenhuma view foi registrada para as rotas `password.request`/`password.reset` do Fortify, e o link na tela de login é um placeholder morto (`href="#"`).
**Esforço:** M.

---

**A18 — [Débito técnico] Import morto/com erro de digitação aponta para funcionalidade de endereço nunca conectada**
📁 [app/Http/Controllers/OrderController.php:6](app/Http/Controllers/OrderController.php#L6) — `use App\Models\OrderAdress;` (classe real é `OrderAddress`, com dois "d")
O import está incorreto e não é usado em lugar nenhum do arquivo (não quebra nada por não ser referenciado), mas evidencia que a funcionalidade de endereço de entrega — migration `order_addresses`, model `OrderAddress`, relação `Order::adress()` ([app/Models/Order.php:30-32](app/Models/Order.php#L30-L32)) — **nunca foi ligada a nenhum controller**. Não existe nenhum ponto no checkout onde um endereço é criado, editado ou exibido.
**Causa raiz:** feature de endereço de entrega iniciada (schema completo) e abandonada antes de qualquer integração com o fluxo de compra.
**Esforço:** M (implementar o formulário/step de endereço no checkout, ou remover o código morto se a feature foi descontinuada).

---

**A19 — [Cobertura de testes] Nenhum teste automatizado real existe**
📁 [tests/Feature/ExampleTest.php](tests/Feature/ExampleTest.php) · [tests/Unit/ExampleTest.php](tests/Unit/ExampleTest.php)
Ambos os arquivos são exatamente os stubs padrão gerados pelo Laravel/Pest ("a aplicação retorna 200 na home" e "true é true"). Não há nenhum teste cobrindo carrinho, checkout, pagamento, autorização ou avaliações — justamente as áreas onde os bugs mais graves deste relatório (B1–B6) foram encontrados por leitura manual, não por falha de suite.
**Esforço:** G (suite mínima cobrindo os fluxos críticos já seria suficiente para pegar B1, B3, B5 e B6 automaticamente).

---

### 🟡 Débito técnico / itens menores

**D20 — [Débito técnico] `TagSeeder` nunca é chamado pelo `DatabaseSeeder`**
📁 [database/seeders/DatabaseSeeder.php:17-26](database/seeders/DatabaseSeeder.php#L17-L26) não contém `$this->call(TagSeeder::class)`.
Qualquer setup limpo (`php artisan migrate:fresh --seed`) sobe sem nenhuma tag cadastrada, quebrando visualmente a home (que itera sobre `Tag::all()`) mesmo depois de B7 ser corrigido.
**Esforço:** P.

**D21 — [Débito técnico] Dependências desatualizadas**
Levantado via `composer outdated` (comparado ao instalado em `composer.json`):

| Pacote | Instalado | Disponível | Tipo |
|---|---|---|---|
| laravel/framework | 12.37.0 | 13.32.0 | major |
| stripe/stripe-php | 19.3.0 | 21.3.2 | major (integração ativa de pagamento — merece atenção) |
| laravel/fortify | 1.32.1 | 1.39.0 | minor |
| spatie/laravel-tags | 4.10.1 | 4.12.0 | minor |
| pestphp/pest (dev) | 4.1.3 | 5.2.1 | major |
| pestphp/pest-plugin-laravel (dev) | 4.0.0 | 5.0.1 | major |
| barryvdh/laravel-ide-helper (dev) | 3.6.1 | 3.7.0 | minor |
| laravel/pail (dev) | 1.2.3 | 1.2.7 | patch |
| laravel/pint (dev) | 1.25.1 | 1.32.1 | minor |
| laravel/sail (dev) | 1.47.0 | 1.67.0 | minor |
| mockery/mockery (dev) | 1.6.12 | 1.6.15 | patch |
| nunomaduro/collision (dev) | 8.8.2 | 8.9.5 | minor |

Nenhuma dependência não utilizada foi encontrada — todos os pacotes do `require` têm uso confirmado no código (`spatie/laravel-tags`, `stripe/stripe-php`, `laravel/fortify`, `laravel/tinker`).
**Esforço:** M (upgrade do Stripe SDK principalmente merece testes de regressão manuais no fluxo de checkout antes de subir de major version).

**D22 — [Débito técnico] Código morto/inconsistência menor: formulário de cadastro usa URL hardcoded**
📁 [resources/views/auth/signup.blade.php:11](resources/views/auth/signup.blade.php#L11) — `action="{{'/register'}}"` ao invés de `action="{{ route('register') }}"`.
Baixo risco (a rota é registrada pelo próprio Fortify com esse path fixo), mas quebra se o prefixo de rotas do Fortify (`config/fortify.php:89`) for alterado no futuro.
**Esforço:** P.

**D23 — [Débito técnico] `withExceptions` vazio em `bootstrap/app.php`**
📁 [bootstrap/app.php:18-19](bootstrap/app.php#L18-L19)
Nenhum tratamento customizado de exceções (não é obrigatório, mas hoje qualquer erro 500 usa a página padrão de debug do Laravel). Combinado com `APP_DEBUG=true` tanto em `.env` quanto em `.env.example`, isso é aceitável em ambiente local, mas é um lembrete de que **não existe nenhuma salvaguarda no código** contra subir para produção com debug ligado (stack traces expondo variáveis de ambiente, queries, etc. ficariam públicos). Não é um bug hoje, é ausência de rede de segurança.
**Esforço:** P (nota de atenção operacional, não código).

---

## 3. Segurança — checklist específico

| Item verificado | Resultado |
|---|---|
| SQL Injection (raw queries, `whereRaw`, `DB::statement` com input não parametrizado) | ✅ Nenhuma ocorrência — todo acesso a dados passa pelo Eloquent/Query Builder com binding |
| XSS via Blade (`{!! !!}`) | ✅ Nenhuma ocorrência encontrada em `resources/views/**` |
| CSRF em formulários POST/PATCH/DELETE | ✅ `@csrf` presente em todos os formulários revisados; webhook do Stripe corretamente excluído em `bootstrap/app.php:14-16` com verificação de assinatura HMAC como compensação |
| Mass assignment (`$fillable`) | ⚠️ Correto em User, Product, Order, OrderItem, OrderAddress — **incorreto em Rating (B6)** |
| Autorização (policies/gates) | ⚠️ Presente e consistente em Product/User/Order (topo do pedido) — **ausente em OrderItem (B1)**, **quebrada por nome de ability errado em Rating (B5)** |
| Exposição de segredos em views | ✅ Nenhuma chave/segredo renderizado em Blade |
| Exposição de dados sensíveis (senha, tokens) em `$hidden` | ✅ `User::$hidden` cobre `password` e `remember_token` corretamente |
| Rate limiting | ⚠️ Aplicado apenas ao login (via Fortify, `FortifyServiceProvider.php:41-45`) — nenhum limite em checkout, withdraw, criação de produto ou webhook |
| Segredos versionados no git | ✅ `.env` corretamente listado no `.gitignore` e confirmado (via `git log`) nunca commitado |

---

## 4. Índice de esforço (visão consolidada)

| Classificação | Itens | Esforço total estimado |
|---|---|---|
| Bloqueador (🔴) | B1–B8 (8 itens) | ~5 P + 3 M |
| Alta prioridade (🟠) | A9–A19 (11 itens) | ~6 P + 5 M/G |
| Débito técnico (🟡) | D20–D23 (4 itens) | ~3 P + 1 M |

**Recomendação de ordem de ataque** (não é a etapa de correção, apenas para planejamento futuro): B1 (segurança, IDOR) e B2 (máquina de estados financeira) primeiro por risco de integridade de dados/dinheiro; em seguida B3–B8 por serem triviais de corrigir (P) e desbloquearem fluxos inteiros; A9–A19 depois; D20–D23 a qualquer momento em paralelo.
