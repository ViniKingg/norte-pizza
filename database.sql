CREATE DATABASE IF NOT EXISTS norte_pizza CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE norte_pizza;

CREATE TABLE IF NOT EXISTS clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  telefone VARCHAR(30),
  endereco VARCHAR(200),
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  preco DECIMAL(10,2) NOT NULL DEFAULT 0.00
);

CREATE TABLE IF NOT EXISTS pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  total DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_pedido_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE IF NOT EXISTS pedido_itens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  produto_id INT NOT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
  CONSTRAINT fk_item_produto FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

INSERT INTO produtos (nome, preco)
SELECT 'Pizza Margherita', 35.00 WHERE NOT EXISTS (SELECT 1 FROM produtos WHERE nome='Pizza Margherita');
INSERT INTO produtos (nome, preco)
SELECT 'Pizza Calabresa', 40.00 WHERE NOT EXISTS (SELECT 1 FROM produtos WHERE nome='Pizza Calabresa');
INSERT INTO produtos (nome, preco)
SELECT 'Pizza Portuguesa', 42.00 WHERE NOT EXISTS (SELECT 1 FROM produtos WHERE nome='Pizza Portuguesa');
