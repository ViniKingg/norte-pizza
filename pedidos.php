<?php
require 'config.php';
$mensagem = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteId = (int)($_POST['cliente_id'] ?? 0);
    $produtoIds = $_POST['produto_id'] ?? [];
    $quantidades = $_POST['quantidade'] ?? [];
    $itens = [];
    try {
        foreach ($produtoIds as $i => $produtoId) {
            $produtoId = (int)$produtoId;
            $qtd = (int)($quantidades[$i] ?? 0);
            if ($produtoId > 0 && $qtd > 0) {
                $stmt = $pdo->prepare("SELECT id, nome, preco FROM produtos WHERE id=?");
                $stmt->execute([$produtoId]);
                $produto = $stmt->fetch();
                if ($produto) {
                    $produto['quantidade'] = $qtd;
                    $produto['subtotal'] = (float)$produto['preco'] * $qtd;
                    $itens[] = $produto;
                }
            }
        }
        if ($clienteId <= 0 || !$itens) {
            $erro = 'Selecione um cliente e pelo menos um produto com quantidade válida.';
        } else {
            $total = array_sum(array_column($itens, 'subtotal'));
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO pedidos (cliente_id, total) VALUES (?, ?)");
            $stmt->execute([$clienteId, $total]);
            $pedidoId = (int)$pdo->lastInsertId();
            $stmtItem = $pdo->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            foreach ($itens as $item) {
                $stmtItem->execute([$pedidoId, $item['id'], $item['quantidade'], $item['preco'], $item['subtotal']]);
            }
            $pdo->commit();
            $mensagem = "Pedido #$pedidoId registrado com sucesso. Total: R$ " . number_format($total, 2, ',', '.') . ".";
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $erro = 'Não foi possível registrar o pedido. Confira os dados e tente novamente.';
    }
}
$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome")->fetchAll();
$produtos = $pdo->query("SELECT id, nome, preco FROM produtos ORDER BY nome")->fetchAll();
$pedidos = $pdo->query("SELECT p.id, p.data_pedido, p.total, c.nome AS cliente FROM pedidos p JOIN clientes c ON c.id=p.cliente_id ORDER BY p.id DESC LIMIT 8")->fetchAll();
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Registrar Pedido | Norte Pizza</title><link rel="stylesheet" href="assets/style.css"></head>
<body><div class="layout">
<aside class="sidebar"><a class="brand" href="index.php"><span class="brand-mark">N</span><span>Norte Pizza<small>Gestão da pizzaria</small></span></a><nav><a href="index.php">Visão geral</a><a href="clientes.php">Clientes</a><a class="active" href="pedidos.php">Pedidos</a></nav><div class="side-note">Sistema de gestão<br>Versão acadêmica</div></aside>
<main class="main"><header class="topbar"><span>Pedidos e vendas</span><span class="user">Equipe Norte Pizza</span></header><section class="content">
<div class="page-heading"><div><p class="eyebrow">VENDAS</p><h1>Registrar Pedido</h1><p class="muted">Selecione o cliente e os produtos do pedido.</p></div></div>
<?php if ($mensagem): ?><div class="notice success"><?= h($mensagem) ?></div><?php endif; ?><?php if ($erro): ?><div class="notice error"><?= h($erro) ?></div><?php endif; ?>
<?php if (!$clientes || !$produtos): ?><div class="notice info">Antes de registrar pedidos, cadastre pelo menos um cliente e um produto no banco de dados.</div><?php endif; ?>
<div class="panel"><div class="panel-title"><div><h2>Dados do pedido</h2><p class="muted">Você pode selecionar até três produtos por pedido.</p></div></div>
<form method="post">
<label class="field customer-select"><span>Cliente</span><select name="cliente_id" required><option value="">Selecione um cliente</option><?php foreach ($clientes as $c): ?><option value="<?= h($c['id']) ?>"><?= h($c['nome']) ?></option><?php endforeach; ?></select></label>
<div class="order-labels"><span>Produto</span><span>Quantidade</span></div>
<?php for ($i=0; $i<3; $i++): ?><div class="product-line"><select name="produto_id[]" aria-label="Produto <?= $i+1 ?>"><option value="">Selecione um produto</option><?php foreach ($produtos as $p): ?><option value="<?= h($p['id']) ?>"><?= h($p['nome']) ?> — R$ <?= number_format($p['preco'],2,',','.') ?></option><?php endforeach; ?></select><input type="number" name="quantidade[]" min="1" max="99" value="" placeholder="Qtd." aria-label="Quantidade <?= $i+1 ?>"></div><?php endfor; ?>
<div class="form-actions"><button class="button primary" type="submit" <?= (!$clientes || !$produtos) ? 'disabled' : '' ?>>Registrar pedido</button></div>
<p class="hint">O valor total é calculado automaticamente pelo sistema com base nos produtos e quantidades informados.</p>
</form></div>
<div class="panel"><div class="panel-title"><div><h2>Pedidos recentes</h2><p class="muted">Últimos registros realizados.</p></div></div>
<?php if (!$pedidos): ?><div class="empty">Ainda não há pedidos registrados.</div><?php else: ?><div class="table-wrap"><table><thead><tr><th>Nº do pedido</th><th>Cliente</th><th>Data</th><th>Valor total</th></tr></thead><tbody><?php foreach ($pedidos as $p): ?><tr><td>#<?= h($p['id']) ?></td><td class="strong"><?= h($p['cliente']) ?></td><td><?= h(date('d/m/Y H:i', strtotime($p['data_pedido']))) ?></td><td>R$ <?= number_format($p['total'],2,',','.') ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
</div></section></main></div></body></html>
