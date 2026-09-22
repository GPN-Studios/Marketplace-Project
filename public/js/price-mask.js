const input = document.querySelector('#priceInput');
const hidden = document.querySelector('#priceHidden');

input.addEventListener('input', (e) => {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 12) value = value.slice(0, 12);

    hidden.value = value;

    let number = (value ? Number(value) : 0) / 100;
    e.target.value = number.toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
});