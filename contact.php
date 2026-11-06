<?php
require __DIR__ . '/header.php';

$connection = dbconnect();
$statement = $connection->query(
    'SELECT email FROM members WHERE contact = 1 AND email IS NOT NULL ORDER BY admin DESC LIMIT 1'
);
$email = $statement->fetchColumn();
$status = '';
$statusType = '';
$name = trim($_POST['name'] ?? '');
$senderEmail = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($_SESSION['contact_token'])) {
    $_SESSION['contact_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['contact_token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['contact_token'], $token)) {
        $status = 'Le formulaire a expiré. Rechargez la page et réessayez.';
        $statusType = 'error';
    } elseif (!is_string($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $status = 'L’adresse de contact du groupe n’est pas configurée dans la base de données.';
        $statusType = 'error';
    } elseif (
        $name === ''
        || strlen($name) > 120
        || filter_var($senderEmail, FILTER_VALIDATE_EMAIL) === false
        || strlen($subject) < 2
        || strlen($subject) > 150
        || preg_match('/[\r\n]/', $subject)
        || $message === ''
        || strlen($message) > 10000
    ) {
        $status = 'Vérifiez votre nom, votre adresse e-mail, le sujet et le message avant de réessayer.';
        $statusType = 'error';
    } else {
        $mailSubject = 'MyBand - ' . $subject;
        $mailBody = "Message envoyé depuis le formulaire MyBand\n\n"
            . "Nom : {$name}\n"
            . "E-mail : {$senderEmail}\n\n"
            . "Message :\n{$message}\n";
        $mailHeaders = [
            'From: MyBand <' . $email . '>',
            'Reply-To: ' . $senderEmail,
            'Content-Type: text/plain; charset=UTF-8',
            'X-Mailer: PHP/' . PHP_VERSION,
        ];

        if (mail($email, $mailSubject, $mailBody, implode("\r\n", $mailHeaders))) {
            $status = 'Merci, votre message a été envoyé.';
            $statusType = 'success';
            $name = '';
            $senderEmail = '';
            $subject = '';
            $message = '';
            $_SESSION['contact_token'] = bin2hex(random_bytes(32));
        } else {
            $status = 'Le serveur n’a pas pu envoyer votre message. Vérifiez la configuration e-mail de PHP/WAMP ou écrivez directement à l’adresse indiquée.';
            $statusType = 'error';
        }
    }
}
?>

<main class="main">
    <header class="intro">
        <p class="eyebrow">Une question ?</p>
        <h1>Contact</h1>
        <p>Pour parler musique, concerts ou collaboration, écrivez-nous.</p>
    </header>

    <section class="formcontact">
        <h2>Envoyer un message</h2>
        <?php if ($status !== ''): ?>
            <p class="form-<?= $statusType ?>" role="<?= $statusType === 'error' ? 'alert' : 'status' ?>">
                <?= htmlspecialchars($status, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
            </p>
        <?php endif; ?>
        <form action="contact.php" method="post">
            <input type="hidden" name="contact_token" value="<?= htmlspecialchars($_SESSION['contact_token'], ENT_QUOTES, 'UTF-8') ?>">

            <label for="name">Votre nom</label>
            <input type="text" id="name" name="name" maxlength="120" autocomplete="name" value="<?= htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>

            <label for="email">Votre adresse e-mail</label>
            <input type="email" id="email" name="email" maxlength="254" autocomplete="email" value="<?= htmlspecialchars($senderEmail, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>

            <label for="subject">Sujet</label>
            <input type="text" id="subject" name="subject" maxlength="150" value="<?= htmlspecialchars($subject, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>

            <label for="message">Votre message</label>
            <textarea id="message" name="message" maxlength="10000" required><?= htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea>

            <button type="submit" class="okbtn">Envoyer le message</button>
        </form>
    </section>

    <section class="contactbar">
        <table>
            <tbody>
                <tr>
                    <td><i class="fas fa-map-marker-alt" aria-hidden="true"></i><p>MyBand</p></td>
                    <td><i class="fas fa-envelope" aria-hidden="true"></i>
                        <p>
                            <?php if (is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)): ?>
                                <a href="mailto:<?= htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><?= htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></a>
                            <?php else: ?>
                                Coordonnée de contact non configurée.
                            <?php endif; ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
</main>

<?php require __DIR__ . '/footer.php'; ?>
