<header class="navbar">
    <div class="nav-left">
        <a href="index.php"><img src="./Images/logoW.png" alt="Logo PadelConnect" class="logo">
            <h1>PadelConnect</h1>
        </a>
    </div>
    <div class="nav-center">
        <a href="search.php">Chercher un match</a>
        <a href="matchs.php">Mes matchs</a>
        <!-- PHP is admin <a href="manage.php">Gérer</a> -->
    </div>
    <div class="nav-right">
        <a href="profile.php"><img src="./Images/profilL.png" alt="Profil"><!-- Prenom avec le PHP --></a>
    </div>
</header>

<?php /*
<header class="navbar">
    <div class="nav-left">
        <a href="index.php" class="brand">
            <img src="./Images/logoW.png" alt="Logo PadelConnect" class="logo">
            <h1>PadelConnect</h1>
        </a>
    </div>

    <?php if ($isConnected): ?>
        <div class="nav-center">
            <a href="search.php">Chercher un match</a>
            <a href="matchs.php">Mes matchs</a>
            <?php if ($isAdmin): ?>
                <a href="manage.php">Gérer</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="nav-right">
        <?php if ($isConnected): ?>
            <a href="profile.php">
                <img src="./Images/profilL.png" alt="Profil" class="profile-icon">
            </a>
        <?php else: ?>
            <a href="login.php">Se connecter</a>
            <a href="register.php">S'inscrire</a>
        <?php endif; ?>
    </div>
</header>
*/ ?>
