/**
 * Scripts gerais do LogiFrete:
 * - máscaras de CEP, telefone e CPF/CNPJ
 * - busca automática de endereço pelo CEP (API ViaCEP)
 * - confirmação antes de excluir
 */

// Aplica uma máscara simples apenas com números
function somenteNumeros(valor) {
    return valor.replace(/\D/g, '');
}

function mascaraCep(valor) {
    return somenteNumeros(valor).slice(0, 8).replace(/(\d{5})(\d)/, '$1-$2');
}

function mascaraTelefone(valor) {
    const n = somenteNumeros(valor).slice(0, 11);
    if (n.length <= 10) {
        return n.replace(/(\d{2})(\d)/, '($1) $2').replace(/(\d{4})(\d)/, '$1-$2');
    }
    return n.replace(/(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d)/, '$1-$2');
}

function mascaraCpfCnpj(valor) {
    const n = somenteNumeros(valor).slice(0, 14);
    if (n.length <= 11) {
        return n.replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }
    return n.replace(/^(\d{2})(\d)/, '$1.$2')
            .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
            .replace(/\.(\d{3})(\d)/, '.$1/$2')
            .replace(/(\d{4})(\d)/, '$1-$2');
}

const mascaras = {
    cep: mascaraCep,
    telefone: mascaraTelefone,
    cpfcnpj: mascaraCpfCnpj,
};

document.querySelectorAll('[data-mascara]').forEach(function (campo) {
    const funcao = mascaras[campo.dataset.mascara];
    if (funcao) {
        campo.addEventListener('input', function () {
            campo.value = funcao(campo.value);
        });
    }
});

// Busca de endereço pelo CEP usando a API pública ViaCEP
const campoCep = document.getElementById('cep');
if (campoCep) {
    campoCep.addEventListener('blur', async function () {
        const cep = somenteNumeros(campoCep.value);
        const aviso = document.getElementById('aviso-cep');
        if (cep.length !== 8) {
            return;
        }
        aviso.textContent = 'Buscando endereço...';
        try {
            const resposta = await fetch('https://viacep.com.br/ws/' + cep + '/json/');
            const dados = await resposta.json();
            if (dados.erro) {
                aviso.textContent = 'CEP não encontrado. Preencha manualmente.';
                return;
            }
            document.getElementById('logradouro').value = dados.logradouro || '';
            document.getElementById('bairro').value = dados.bairro || '';
            document.getElementById('cidade').value = dados.localidade || '';
            document.getElementById('uf').value = dados.uf || '';
            aviso.textContent = 'Endereço preenchido automaticamente.';
            document.getElementById('numero').focus();
        } catch (erro) {
            aviso.textContent = 'Não foi possível consultar o CEP agora. Preencha manualmente.';
        }
    });
}

// Pede confirmação antes de enviar formulários de exclusão
document.querySelectorAll('form[data-confirmar]').forEach(function (form) {
    form.addEventListener('submit', function (evento) {
        if (!confirm(form.dataset.confirmar)) {
            evento.preventDefault();
        }
    });
});
