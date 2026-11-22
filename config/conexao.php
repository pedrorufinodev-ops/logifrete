<?php
require_once __DIR__ . '/config.php';

/**
 * Retorna uma conexão PDO única (padrão Singleton simples).
 * Usamos PDO com Prepared Statements para evitar SQL Injection.
 */
function conexao(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NOME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USUARIO, DB_SENHA, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Erro ao conectar no banco de dados. Verifique se o MySQL do XAMPP está ligado '
                . 'e se o script database/logistica.sql foi importado. Detalhe: ' . $e->getMessage());
        }
    }

    return $pdo;
}
