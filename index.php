<?php
require 'config.php';
$totalClientes = (int)$pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$totalPedidos = (int)$pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$totalProdutos = (int)$pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
$ultimos = $pdo->query("SELECT p.id, c.nome AS cliente, p.data_pedido, p.total
                        FROM pedidos p JOIN clientes c ON c.id = p.cliente_id
                        ORDER BY p.id DESC LIMIT 5")->fetchAll();
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Norte Pizza | Início</title><link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="layout">
<aside class="sidebar">
  <a class="brand" href="index.php"><span class="brand-mark">N</span><span>Norte Pizza<small>Gestão da pizzaria</small></span></a>
  <nav>
    <a class="active" href="index.php">Visão geral</a>
    <a href="clientes.php">Clientes</a>
    <a href="pedidos.php">Pedidos</a>
  </nav>
  <div class="side-note">Sistema de gestão<br>Versão acadêmica</div>
</aside>
<main class="main">
  <header class="topbar"><span>Painel de controle</span><span class="user">Equipe Norte Pizza</span></header>
  <section class="content">
    <div class="page-heading"><div><p class="eyebrow">BEM-VINDO</p><h1>Visão geral</h1><p class="muted">Acompanhe as principais informações da pizzaria.</p></div></div>
    <div class="stats">
      <article class="stat"><span>Clientes cadastrados</span><strong><?= $totalClientes ?></strong><small>Registros no sistema</small></article>
      <article class="stat"><span>Produtos cadastrados</span><strong><?= $totalProdutos ?></strong><small>Itens disponíveis</small></article>
      <article class="stat"><span>Pedidos registrados</span><strong><?= $totalPedidos ?></strong><small>Pedidos realizados</small></article>
    </div>
    <div class="panel">
      <div class="panel-title"><div><h2>Pedidos recentes</h2><p class="muted">Últimos pedidos registrados no sistema.</p></div><a class="button primary" href="pedidos.php">Registrar pedido</a></div>
      <?php if (!$ultimos): ?><div class="empty">Ainda não há pedidos cadastrados.</div><?php else: ?>
      <div class="table-wrap"><table><thead><tr><th>Nº</th><th>Cliente</th><th>Data</th><th>Valor</th></tr></thead><tbody>
      <?php foreach ($ultimos as $p): ?><tr><td>#<?= h($p['id']) ?></td><td><?= h($p['cliente']) ?></td><td><?= h(date('d/m/Y H:i', strtotime($p['data_pedido']))) ?></td><td>R$ <?= number_format($p['total'], 2, ',', '.') ?></td></tr><?php endforeach; ?>
      </tbody></table></div><?php endif; ?>
    </div>
  </section>
</main>
</div>
</body></html>
