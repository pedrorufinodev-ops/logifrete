<?php
session_start();

require_once __DIR__ . '/../config/conexao.php';

/** Escapa texto para exibir com segurança no HTML (evita XSS). */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/** Formata número como moeda brasileira. */
function moeda($valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

/** Converte data do banco (AAAA-MM-DD) para o formato brasileiro (DD/MM/AAAA). */
function data_br(?string $data): string
{
    if (!$data) {
        return '';
    }
    $obj = DateTime::createFromFormat('Y-m-d', substr($data, 0, 10));
    return $obj ? $obj->format('d/m/Y') : $data;
}

/** Converte valor digitado ("1.234,56" ou "1234.56") para float. */
function para_decimal($valor): float
{
    $valor = trim((string) $valor);
    if (str_contains($valor, ',')) {
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
    }
    return (float) $valor;
}

/** Endereço em uma linha. */
function endereco_linha(array $end, string $prefixo = ''): string
{
    return $end[$prefixo . 'logradouro'] . ', ' . $end[$prefixo . 'numero'] . ' - '
        . $end[$prefixo . 'bairro'] . ', ' . $end[$prefixo . 'cidade'] . '/' . $end[$prefixo . 'uf'];
}

/** Status possíveis de um frete e a cor do badge do Bootstrap. */
const STATUS_FRETE = [
    'Pendente'      => 'secondary',
    'Em trânsito'   => 'warning',
    'Entregue'      => 'success',
    'Cancelado'     => 'danger',
];

function cor_status(string $status): string
{
    return STATUS_FRETE[$status] ?? 'secondary';
}

/** Guarda uma mensagem na sessão para exibir na próxima página. */
function flash(string $mensagem, string $tipo = 'success'): void
{
    $_SESSION['flash'] = ['mensagem' => $mensagem, 'tipo' => $tipo];
}

/** Redireciona e encerra o script. */
function redirecionar(string $url): void
{
    header('Location: ' . $url);
    exit;
}
