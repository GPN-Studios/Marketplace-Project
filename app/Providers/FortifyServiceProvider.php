<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute((int) config('shop.rate_limits.login'))->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute((int) config('shop.rate_limits.two_factor'))->by($request->session()->get('login.id'));
        });

        // O Fortify só throttla automaticamente as rotas para as quais existe um
        // slot de config (login, two-factor, verification). As rotas de cadastro
        // e de recuperação de senha não têm slot equivalente, então limitamos
        // manualmente aqui (mesmo padrão de chave usado no limiter de login).
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute((int) config('shop.rate_limits.register'))->by($request->ip());
        });

        RateLimiter::for('password-email', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip());

            return Limit::perMinute((int) config('shop.rate_limits.password_email'))->by($throttleKey);
        });

        RateLimiter::for('password-update', function (Request $request) {
            return Limit::perMinute((int) config('shop.rate_limits.password_update'))->by($request->ip());
        });

        // As rotas do Fortify (register.store, password.email, password.update)
        // já estão registradas neste ponto do boot, mas só ficam visíveis para
        // Route::getRoutes() depois que o roteamento é carregado — o que
        // acontece depois do boot() dos providers. Por isso o throttle é
        // injetado no momento em que a rota é de fato casada com a requisição
        // (ainda antes da pipeline de middleware ser montada), e não aqui.
        $throttledFortifyRoutes = [
            'register.store' => 'register',
            'password.email' => 'password-email',
            'password.update' => 'password-update',
        ];

        Event::listen(RouteMatched::class, function (RouteMatched $event) use ($throttledFortifyRoutes) {
            if ($limiter = $throttledFortifyRoutes[$event->route->getName()] ?? null) {
                $event->route->middleware('throttle:'.$limiter);
            }
        });

        // -- form views //

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.signup');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        Fortify::resetPasswordView(function ($request) {
            return view('auth.reset-password', ['request' => $request]);
        });

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });
    }
}
