-- =====================================================
-- LogiFrete - Gerenciador Logístico
-- Script de criação do banco de dados (MySQL / MariaDB - XAMPP)
-- Como usar: abra o phpMyAdmin > aba "Importar" > selecione este arquivo
-- =====================================================

CREATE DATABASE IF NOT EXISTS logistica
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE logistica;

-- Produtos que podem ser transportados
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    descricao VARCHAR(255),
    peso_kg DECIMAL(10,2) NOT NULL DEFAULT 0,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Endereços usados como origem ou destino
CREATE TABLE IF NOT EXISTS enderecos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    apelido VARCHAR(80) NOT NULL,
    cep VARCHAR(9) NOT NULL,
    logradouro VARCHAR(150) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    complemento VARCHAR(80),
    bairro VARCHAR(80) NOT NULL,
    cidade VARCHAR(80) NOT NULL,
    uf CHAR(2) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Transportadores (pessoa física ou jurídica)
CREATE TABLE IF NOT EXISTS transportadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    cpf_cnpj VARCHAR(18) NOT NULL,
    telefone VARCHAR(15),
    email VARCHAR(120),
    veiculo VARCHAR(60),
    placa VARCHAR(8),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Frete: liga UM transportador, UMA origem e UM destino
CREATE TABLE IF NOT EXISTS fretes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transportador_id INT NOT NULL,
    origem_id INT NOT NULL,
    destino_id INT NOT NULL,
    data_frete DATE NOT NULL,
    valor_frete DECIMAL(10,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'Pendente',
    observacao VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transportador_id) REFERENCES transportadores(id),
    FOREIGN KEY (origem_id) REFERENCES enderecos(id),
    FOREIGN KEY (destino_id) REFERENCES enderecos(id)
) ENGINE=InnoDB;

-- Itens do frete: relação 1 para muitos (um frete -> vários produtos)
CREATE TABLE IF NOT EXISTS frete_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    frete_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    FOREIGN KEY (frete_id) REFERENCES fretes(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB;

-- Dados de exemplo (opcional, ajuda na demonstração)
INSERT INTO produtos (nome, descricao, peso_kg, valor) VALUES
('Notebook Dell Inspiron', 'Notebook 15 polegadas', 2.20, 3899.90),
('Monitor LG 24"', 'Monitor Full HD', 3.50, 899.00),
('Cadeira de Escritório', 'Cadeira ergonômica', 12.00, 650.00);

INSERT INTO enderecos (apelido, cep, logradouro, numero, complemento, bairro, cidade, uf) VALUES
('CD Umuarama', '87501-000', 'Avenida Paraná', '1500', 'Galpão 2', 'Centro', 'Umuarama', 'PR'),
('Cliente Campo Grande', '79002-000', 'Rua 14 de Julho', '320', '', 'Centro', 'Campo Grande', 'MS');

INSERT INTO transportadores (nome, cpf_cnpj, telefone, email, veiculo, placa) VALUES
('TransRápido Logística', '12.345.678/0001-90', '(44) 99999-1234', 'contato@transrapido.com', 'Caminhão VW Delivery', 'ABC1D23');
