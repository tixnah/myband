<?php
require_once __DIR__ . '/band_generators.php';
require_once __DIR__ . '/dbconnect.php';

session_start();

if (isset($_GET['disconnect'])) {
    unset($_SESSION['login'], $_SESSION['admin']);
    session_regenerate_id(true);
}

$loginError = false;
if (isset($_POST['login'], $_POST['password'])) {
    $connection = dbconnect();
    $statement = $connection->prepare(
        'SELECT * FROM members WHERE login = :login'
    );
    $statement->execute(['login' => trim($_POST['login'])]);
    $row = $statement->fetch();

    if ($row) {
        if (isset($row['salt']) && is_string($row['salt']) && $row['salt'] !== '') {
            $passwordHash = hash('sha256', $row['salt'] . $_POST['password']);
            $passwordIsValid = hash_equals($row['password'], $passwordHash);
        } elseif (preg_match('/\A[a-f0-9]{64}\z/i', $row['password'])) {
            $passwordIsValid = hash_equals(
                $row['password'],
                hash('sha256', $_POST['password'])
            );
        } else {
            $passwordIsValid = hash_equals($row['password'], $_POST['password']);
        }

        if ($passwordIsValid) {
            session_regenerate_id(true);
            $_SESSION['login'] = $row['login'];
            $_SESSION['admin'] = (int) $row['admin'];
        } else {
            $loginError = true;
        }
    } else {
        $loginError = true;
    }
}

if (!isset($_SESSION['bandname'])) {
    $_SESSION['bandname'] = generate_bandname();
}
if (!isset($_SESSION['bandlogo'])) {
    $_SESSION['bandlogo'] = generate_bandlogo();
}

$member = isset($_SESSION['login']);
$admin = $member && (int) ($_SESSION['admin'] ?? 0) === 1;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>My Band</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <link rel="stylesheet" href="myband.css?v=<?= filemtime(__DIR__ . '/myband.css') ?>">
    <script>
        function authenticate() {
            document.getElementById('loginModal').style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="navbar">
        <ul>
            <li class="brandlogo"><img src="logo.php?logo=<?= rawurlencode($_SESSION['bandlogo']) ?>" alt="Logo du groupe"></li>
            <li class="brandtext"><?= htmlspecialchars($_SESSION['bandname'], ENT_QUOTES, 'UTF-8') ?></li>
            <li class="nav-link"><a href="contact.php">CONTACT</a></li>
            <li class="nav-link">
                <?php if ($member): ?>
                    <a href="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>?disconnect=1">DISCONNECT</a>
                <?php else: ?>
                    <a href="#" onclick="authenticate(); return false;">CONNECT</a>
                <?php endif; ?>
            </li>
            <li class="nav-link"><a href="setlist.php">SETLIST</a></li>
            <li class="nav-link"><a href="index.php">HOME</a></li>
        </ul>
    </div>

    <?php if (!$member): ?>
        <div id="loginModal" class="modal"<?= $loginError ? ' style="display:block"' : '' ?>>
            <form id="loginForm" class="modal-content animate" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" method="post">
                <div class="dlgheadcontainer">
                    <span onclick="document.getElementById('loginModal').style.display='none'" class="close" title="Fermer">&times;</span>
                    <h1>Connexion</h1>
                </div>
                <div class="dlgcontainer">
                    <?php if ($loginError): ?>
                        <p class="form-error" role="alert">Identifiant ou mot de passe incorrect.</p>
                    <?php endif; ?>
                    <label for="login">Identifiant</label>
                    <input type="text" name="login" id="login" autocomplete="username" required>
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" autocomplete="current-password" required>
                    <button type="submit" class="okbtn">Se connecter</button>
                    <button type="button" onclick="document.getElementById('loginModal').style.display='none'" class="cancelbtn">Annuler</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
