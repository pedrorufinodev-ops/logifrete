/**
 * Tela de frete: adiciona e remove linhas de produtos dinamicamente.
 * Isso permite que UM frete (um transportador) leve VÁRIOS produtos.
 */
const listaItens = document.getElementById('lista-itens');
const modelo = document.getElementById('modelo-item');
const botaoAdicionar = document.getElementById('adicionar-item');
const totalPeso = document.getElementById('total-peso');
const totalValor = document.getElementById('total-valor');

function formatarMoeda(valor) {
    return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

// Recalcula peso e valor total da carga
function atualizarTotais() {
    let peso = 0;
    let valor = 0;
    listaItens.querySelectorAll('.item-produto').forEach(function (item) {
        const opcao = item.querySelector('select').selectedOptions[0];
        const quantidade = parseInt(item.querySelector('input[type=number]').value, 10) || 0;
        if (opcao && opcao.value) {
            peso += parseFloat(opcao.dataset.peso) * quantidade;
            valor += parseFloat(opcao.dataset.valor) * quantidade;
        }
    });
    totalPeso.textContent = peso.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) + ' kg';
    totalValor.textContent = formatarMoeda(valor);
}

// Atualiza a numeração e o botão de remover de cada item
function atualizarItens() {
    const itens = listaItens.querySelectorAll('.item-produto');
    itens.forEach(function (item, indice) {
        item.querySelector('.numero-item').textContent = indice + 1;
        item.querySelector('.remover-item').disabled = itens.length === 1;
    });
    atualizarTotais();
}

// Cria uma nova linha de produto a partir do <template>
function adicionarItem(produtoId, quantidade) {
    const novoItem = modelo.content.firstElementChild.cloneNode(true);
    if (produtoId) {
        novoItem.querySelector('select').value = produtoId;
    }
    if (quantidade) {
        novoItem.querySelector('input[type=number]').value = quantidade;
    }
    novoItem.querySelector('.remover-item').addEventListener('click', function () {
        novoItem.remove();
        atualizarItens();
    });
    novoItem.querySelector('select').addEventListener('change', atualizarTotais);
    novoItem.querySelector('input[type=number]').addEventListener('input', atualizarTotais);
    listaItens.appendChild(novoItem);
    atualizarItens();
}

botaoAdicionar.addEventListener('click', function () {
    adicionarItem();
});

// Ao editar um frete, carrega os itens já salvos; senão começa com uma linha
const itensSalvos = JSON.parse(listaItens.dataset.itens || '[]');
if (itensSalvos.length > 0) {
    itensSalvos.forEach(function (item) {
        adicionarItem(item.produto_id, item.quantidade);
    });
} else {
    adicionarItem();
}

// Impede origem e destino iguais
document.getElementById('form-frete').addEventListener('submit', function (evento) {
    const origem = document.getElementById('origem_id').value;
    const destino = document.getElementById('destino_id').value;
    if (origem && origem === destino) {
        evento.preventDefault();
        alert('A origem e o destino precisam ser endereços diferentes.');
    }
});
