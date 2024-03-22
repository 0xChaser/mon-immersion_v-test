<?php
require "../database/config.php";

$conn = connect();

session_start();

$email_agent = getEmailFromSession();
$email_BAL = "par-mon-immersion@enedis.fr";

includeFileBasedOnRole();



if (isset($_POST['formsend'])) {
    $nniAgent = getNNIFromSession($userInfo);
    $agent = getFormattedNameFromSession($userInfo);
    $emailAgent = getEmailFromSession($userInfo);
    $categorie = $_POST['categorie_info'] ?? '';
    $description_remarques = $_POST['description_remarques'] ?? '';
    $etat  = "En attente de traitement" ;

    if (!empty($description_remarques) && $categorie !== '' && $categorie !== 'Choisir une catégorie') {
        $ajoutRemarques = $conn->prepare("INSERT INTO Remarques (nni_Agent, agent, email_agent, categories, remarques, date_ajout, etat) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
        if ($ajoutRemarques->execute([$nniAgent, $agent, $emailAgent, $categorie, $description_remarques, $etat])) {
            $_SESSION['toast_message'] = "Votre information a bien été envoyée.";
}


    }
}






?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/remarques_bugs_agent.css">
    <link rel="shortcut icon" type="image/png" href="../img/logo_enedis.png">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <title>Mon Immersion - Remarques / Bugs</title>
</head>
<body>

    <h1> Remarques / Bugs </h1>

    <?php if (isset($error_message)): ?>
        <p class="error"><?= $error_message; ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="categorie_info">Catégorie de votre Information :</label>
        <select id="categorie_info" name="categorie_info" required>
            <option value="">Choisir une catégorie</option>
            <option value="Bug">Bug</option>
            <option value="Remarque">Remarque</option>
        </select>

        <br> 
        <div class="info-container">
        <svg class="info-icon" width="40" height="40" viewBox="0 0 39 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M19.5002 2.9375C24.0258 2.9375 28.366 4.73528 31.5661 7.93535C34.7662 11.1354 36.564 15.4756 36.564 20.0012C36.564 24.5268 34.7662 28.867 31.5661 32.0671C28.366 35.2672 24.0258 37.0649 19.5002 37.0649C14.9747 37.0649 10.6344 35.2672 7.43437 32.0671C4.2343 28.867 2.43652 24.5268 2.43652 20.0012C2.43652 15.4756 4.2343 11.1354 7.43437 7.93535C10.6344 4.73528 14.9747 2.9375 19.5002 2.9375ZM22.0596 13.4139C23.3271 13.4139 24.3557 12.5339 24.3557 11.2299C24.3557 9.92581 23.3247 9.04588 22.0596 9.04588C20.7921 9.04588 19.7684 9.92581 19.7684 11.2299C19.7684 12.5339 20.7921 13.4139 22.0596 13.4139ZM22.5057 27.1297C22.5057 26.8689 22.5959 26.1912 22.5447 25.8061L20.5411 28.112C20.1267 28.5483 19.6075 28.8506 19.3637 28.7701C19.2532 28.7294 19.1607 28.6506 19.1031 28.5478C19.0455 28.445 19.0264 28.3251 19.0493 28.2095L22.3887 17.66C22.6617 16.3218 21.9109 15.1006 20.3192 14.9446C18.6398 14.9446 16.1682 16.6484 14.6642 18.8105C14.6642 19.0689 14.6155 19.7124 14.6667 20.0975L16.6679 17.7892C17.0822 17.3577 17.5649 17.0531 17.8086 17.1359C17.9287 17.179 18.0271 17.2675 18.0827 17.3823C18.1383 17.4972 18.1467 17.6293 18.106 17.7502L14.7959 28.2485C14.4132 29.477 15.1371 30.6811 16.8921 30.9541C19.4759 30.9541 21.0017 29.2917 22.5081 27.1297H22.5057Z" fill="#1423DC"/>
        </svg>
        <div class="hover-text">
          En soumettant un retour, vous acceptez de vous conformer à ne pas écrire de texte offensants, abusifs ou contenant des informations personnelles ou sensibles.
          Si votre retour ne respecte pas ces règles, il sera supprimé.
        </div>
      </div>

        <label class="add_label" for="description_remarques">Description de la remarque :</label>
        <textarea id="description_remarques" name="description_remarques" required cols="85" rows="30" placeholder="Communiquez-nous votre avis, remarque, bug ou suggestion à propos de Mon Immersion"></textarea>

        <button class="btn_ajout" type="submit" name="formsend">Ajouter la remarque</button>

    </form>

    <?php if (isset($_SESSION['toast_message'])): ?>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        window.onload = function() {
            Toastify({
                text: "<?= $_SESSION['toast_message']; ?>",
                duration: 6000,
                close: true,
                gravity: "top",
                position: "center",
                backgroundColor: "#1423DC", 
                stopOnFocus: true
            }).showToast();
        };
    </script>
    <?php
        unset($_SESSION['toast_message']);
    ?>
    <?php endif; ?>

</body>
</html>
