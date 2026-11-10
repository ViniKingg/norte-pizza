<?php
require 'config.php';
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    try {
        if ($acao === 'salvar' && $nome !== '') {
            $stmt = $pdo->prepare("INSERT INTO clientes (nome, telefone, endereco) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $telefone, $endereco]);
            $mensagem = 'Cliente cadastrado com sucesso.';
        } elseif ($acao === 'editar' && $nome !== '') {
            $stmt = $pdo->prepare("UPDATE clientes SET nome=?, telefone=?, endereco=? WHERE id=?");
            $stmt->execute([$nome, $telefone, $endereco, (int)$_POST['id']]);
            $mensagem = 'Dados do cliente atualizados.';
        } elseif ($acao === 'excluir') {
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE id=?");
            $stmt->execute([(int)$_POST['id']]);
            $mensagem = 'Cliente excluído.';
        }
    } catch (PDOException $e) {
        $erro = 'Não foi possível concluir a operação. Verifique se o cliente possui pedidos vinculados.';
    }
}
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id=?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}
$busca = trim($_GET['busca'] ?? '');
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE nome LIKE ? OR telefone LIKE ? ORDER BY nome");
$stmt->execute(["%$busca%", "%$busca%"]);
$clientes = $stmt->fetchAll();
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Cadastro de Clientes | Norte Pizza</title><link rel="stylesheet" href="assets/style.css"></head>
<body><div class="layout">
<aside class="sidebar"><a class="brand" href="index.php"><span class="brand-mark">N</span><span>Norte Pizza<small>Gestão da pizzaria</small></span></a><nav><a href="index.php">Visão geral</a><a class="active" href="clientes.php">Clientes</a><a href="pedidos.php">Pedidos</a></nav><div class="side-note">Sistema de gestão<br>Versão acadêmica</div></aside>
<main class="main"><header class="topbar"><span>Cadastro e consulta</span><span class="user">Equipe Norte Pizza</span></header><section class="content">
<div class="page-heading"><div><p class="eyebrow">CADASTROS</p><h1>Cadastro de Clientes</h1><p class="muted">Adicione e mantenha os dados dos clientes organizados.</p></div></div>
<?php if ($mensagem): ?><div class="notice success"><?= h($mensagem) ?></div><?php endif; ?><?php if ($erro): ?><div class="notice error"><?= h($erro) ?></div><?php endif; ?>
<div class="panel">
<div class="panel-title"><div><h2><?= $editando ? 'Editar cliente' : 'Novo cliente' ?></h2><p class="muted">Preencha as informações abaixo.</p></div></div>
<form method="post" class="form-grid">
<input type="hidden" name="acao" value="<?= $editando ? 'editar' : 'salvar' ?>"><?php if ($editando): ?><input type="hidden" name="id" value="<?= h($editando['id']) ?>"><?php endif; ?>
<label class="field"><span>Nome completo</span><input name="nome" required maxlength="120" placeholder="Ex.: Maria Silva" value="<?= h($editando['nome'] ?? '') ?>"></label>
<label class="field"><span>Telefone</span><input name="telefone" maxlength="30" placeholder="Ex.: (11) 99999-0000" value="<?= h($editando['telefone'] ?? '') ?>"></label>
<label class="field full"><span>Endereço</span><input name="endereco" maxlength="200" placeholder="Rua, número e bairro" value="<?= h($editando['endereco'] ?? '') ?>"></label>
<div class="form-actions"><button class="button primary" type="submit"><?= $editando ? 'Salvar alterações' : 'Cadastrar cliente' ?></button><?php if ($editando): ?><a class="button light" href="clientes.php">Cancelar</a><?php endif; ?></div>
</form></div>
<div class="panel"><div class="panel-title"><div><h2>Lista de Clientes</h2><p class="muted"><?= count($clientes) ?> registro(s) encontrado(s).</p></div><form method="get" class="search-form"><input name="busca" placeholder="Buscar por nome ou telefone" value="<?= h($busca) ?>"><button class="button light">Buscar</button></form></div>
<?php if (!$clientes): ?><div class="empty">Nenhum cliente encontrado.</div><?php else: ?><div class="table-wrap"><table><thead><tr><th>Nome</th><th>Telefone</th><th>Endereço</th><th>Ações</th></tr></thead><tbody>
<?php foreach ($clientes as $c): ?><tr><td class="strong"><?= h($c['nome']) ?></td><td><?= h($c['telefone']) ?></td><td><?= h($c['endereco']) ?></td><td class="actions"><a class="text-link" href="clientes.php?editar=<?= h($c['id']) ?>">Editar</a><form method="post" onsubmit="return confirm('Excluir este cliente?')"><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= h($c['id']) ?>"><button class="text-link danger" type="submit">Excluir</button></form></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></div>
</section></main></div></body></html>
