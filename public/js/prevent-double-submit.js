/**
 * Desabilita o botão de envio assim que um formulário é submetido, para
 * evitar requisições duplicadas por clique duplo/repetido (ex.: criar o
 * mesmo anúncio duas vezes, enviar duas avaliações). Puramente defensivo:
 * não decide nada, só evita disparar a mesma requisição outra vez antes da
 * página trocar. A regra de negócio (impedir duplicidade de verdade)
 * continua sempre no backend.
 *
 * Para uma ação que precisa poder ser reenviada sem reload (raro), marque
 * o formulário com data-allow-resubmit.
 */
document.addEventListener('submit', function (event) {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || form.dataset.allowResubmit !== undefined) {
        return;
    }

    const submitter = event.submitter
        || form.querySelector('button[type="submit"], input[type="submit"]');

    if (!submitter || submitter.disabled) {
        return;
    }

    submitter.disabled = true;
    submitter.classList.add('is-submitting');
});
