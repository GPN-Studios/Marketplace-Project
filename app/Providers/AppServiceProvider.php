<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\Tags\Tag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // O site inteiro usa Bootstrap 5 (via CDN); sem isso, ->links()
        // renderiza o paginador padrão em Tailwind, quebrando a identidade visual.
        Paginator::useBootstrapFive();

        // O binding implícito padrão do Laravel ({tag:slug}) faz um WHERE
        // simples contra a coluna 'slug', mas o spatie/laravel-tags guarda
        // slug como JSON traduzível (ex: {"pt_BR":"eletronicos"}) — um WHERE
        // direto nunca casa. Resolvemos manualmente usando o accessor
        // traduzido do model (que já retorna a string no locale atual).
        Route::bind('tag', function (string $value) {
            return Tag::all()->first(fn (Tag $tag) => $tag->slug === $value)
                ?? abort(404);
        });

        $this->registerRateLimiters();
    }

    /**
     * Limitadores de requisição para as ações da aplicação (fora dos fluxos
     * de autenticação, que já são limitados em FortifyServiceProvider).
     * Cada limite é generoso o bastante para uso normal, mas barra scripts
     * automatizados e cliques repetidos acidentais.
     */
    private function registerRateLimiters(): void
    {
        // rede de segurança geral (aplicada a todo o grupo "web" em
        // bootstrap/app.php) — bem generosa, só existe para barrar flood
        // básico. O webhook do Stripe fica de fora: ele é autenticado por
        // assinatura, não por IP, e retentativas legítimas não podem ser
        // descartadas por um limite genérico.
        RateLimiter::for('global', function (Request $request) {
            if ($request->is('webhook/stripe')) {
                return Limit::none();
            }

            return Limit::perMinute((int) config('shop.rate_limits.global'))->by($request->ip());
        });

        // busca é pública (sem login) — limita por IP para evitar scraping
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute((int) config('shop.rate_limits.search'))->by($request->ip());
        });

        // adicionar/alterar/remover itens do carrinho
        RateLimiter::for('cart', function (Request $request) {
            return Limit::perMinute((int) config('shop.rate_limits.cart'))->by($request->user()?->id ?? $request->ip());
        });

        // checkout, pagamento, confirmar recebimento, cancelar
        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute((int) config('shop.rate_limits.checkout'))->by($request->user()?->id ?? $request->ip());
        });

        // saque de saldo — ação financeira, deve ser pouco frequente
        RateLimiter::for('withdraw', function (Request $request) {
            return Limit::perMinute((int) config('shop.rate_limits.withdraw'))->by($request->user()?->id ?? $request->ip());
        });

        // avaliações
        RateLimiter::for('ratings', function (Request $request) {
            return Limit::perMinute((int) config('shop.rate_limits.ratings'))->by($request->user()?->id ?? $request->ip());
        });

        // criar/editar/excluir anúncios
        RateLimiter::for('listings', function (Request $request) {
            return Limit::perMinute((int) config('shop.rate_limits.listings'))->by($request->user()?->id ?? $request->ip());
        });

        // editar dados de perfil / foto
        RateLimiter::for('profile', function (Request $request) {
            return Limit::perMinute((int) config('shop.rate_limits.profile'))->by($request->user()?->id ?? $request->ip());
        });
    }
}
