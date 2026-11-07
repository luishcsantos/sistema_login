<!-- ====================================================
     menu.php - Barra de navegação (incluída nas páginas)
     ==================================================== -->
<nav class="menu">
    <!-- Nome do sistema -->
    <span class="logo">Mural de Avisos</span>

    <div class="links">
        <!-- Link para o mural (todos veem) -->
        <a href="painel.php">Avisos</a>

        <!-- Link para os usuários (só o administrador vê) -->
        <?php if (eAdmin()): ?>
            <a href="usuarios.php">Usuários</a>
        <?php endif; ?>

        <!-- Nome e perfil de quem está logado -->
        <span class="quem">
            <?= $_SESSION['usuario']['nome'] ?>
            <span class="selo <?= $_SESSION['usuario']['perfil'] ?>"><?= $_SESSION['usuario']['perfil'] ?></span>
        </span>

        <!-- Link para sair -->
        <a href="sair.php" class="sair">Sair</a>
    </div>
</nav>
