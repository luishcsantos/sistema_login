<?php
// ======================================================
// painel.php - Mural de avisos
// Todos os usuários logados podem ler os avisos
// Só o administrador pode publicar e excluir
// ======================================================
require 'conexao.php';

// Redireciona para o login quem não está logado
exigirLogin();

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Redireciona para o painel quem não é administrador
    exigirAdmin();

    // ----- PUBLICAR AVISO -----
    if ($_POST['acao'] === 'publicar') {
        $titulo   = trim($_POST['titulo']);
        $mensagem = trim($_POST['mensagem']);

        // Só grava se o título e a mensagem foram preenchidos
        if ($titulo != '' && $mensagem != '') {
            $consulta = $pdo->prepare("INSERT INTO avisos (titulo, mensagem, autor) VALUES (?, ?, ?)");
            $consulta->execute([$titulo, $mensagem, $_SESSION['usuario']['nome']]);
            $_SESSION['msg'] = 'Aviso publicado!';
        }
    }

    // ----- EXCLUIR AVISO -----
    if ($_POST['acao'] === 'excluir') {
        $consulta = $pdo->prepare("DELETE FROM avisos WHERE id = ?");
        $consulta->execute([(int)$_POST['id']]);
        $_SESSION['msg'] = 'Aviso excluído.';
    }

    // Recarrega a página (evita reenviar o formulário ao apertar F5)
    header('Location: painel.php');
    exit;
}

// Busca os avisos, do mais novo para o mais antigo
$consulta = $pdo->query("SELECT * FROM avisos ORDER BY criado_em DESC, id DESC");
$avisos = $consulta->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avisos | Mural de Avisos</title>

    <!-- Liga o arquivo de estilos -->
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <!-- Barra de navegação -->
    <?php include 'menu.php'; ?>

    <main class="conteudo">
        <h1>Mural de Avisos</h1>

        <!-- Mostra a mensagem de sucesso, se houver -->
        <?php mostrarMensagem(); ?>

        <?php if (eAdmin()): ?>
            <!-- Formulário de novo aviso (só o administrador vê) -->
            <div class="cartao">
                <h2>Novo aviso</h2>
                <form method="post">
                    <input type="hidden" name="acao" value="publicar">

                    <label>Título</label>
                    <input type="text" name="titulo" maxlength="150" required>

                    <label>Mensagem</label>
                    <textarea name="mensagem" rows="3" required></textarea>

                    <button type="submit">Publicar</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Aviso de modo leitura (só o usuário comum vê) -->
            <p class="sub">Você está no modo leitura. Só administradores publicam avisos.</p>
        <?php endif; ?>

        <!-- Mensagem para quando não há avisos -->
        <?php if (count($avisos) == 0): ?>
            <p class="vazio">Nenhum aviso publicado ainda.</p>
        <?php endif; ?>

        <!-- Lista de avisos (um cartão para cada aviso) -->
        <?php foreach ($avisos as $aviso): ?>
            <?php
                // Converte a data do banco em número e depois formata como dia/mês/ano hora:minuto
                $dataConvertida = strtotime($aviso['criado_em']);
                $dataFormatada  = date('d/m/Y H:i', $dataConvertida);
            ?>
            <div class="cartao">
                <h3><?= $aviso['titulo'] ?></h3>
                <p><?= nl2br($aviso['mensagem']) ?></p>
                <small class="data">
                    Por <?= $aviso['autor'] ?> em <?= $dataFormatada ?>
                </small>

                <!-- Botão de excluir (só o administrador vê) -->
                <?php if (eAdmin()): ?>
                    <form method="post" onsubmit="return confirm('Excluir este aviso?');">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="id" value="<?= (int)$aviso['id'] ?>">
                        <button type="submit" class="perigo">Excluir</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html>
