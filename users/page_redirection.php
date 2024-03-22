<?php
ob_start(); 
header("Cache-Control: no-cache, must-revalidate");

session_start();

require_once "../database/config.php";


$userInfo = $_SESSION['userInfo'];
includeFileBasedOnRole();
redirectToHomeIfNoRole();

$FSDUM_agent = GetCodeFsdumOfAgent($userInfo);
$nni = getNNIFromSession($userInfo);
$formattedName = getFormattedNameFromSession($userInfo);
$email =  getEmailFromSession($userInfo) ;


$connexion = connect();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation d'immersion</title>
    <link rel="shortcut icon" type="image/png" href="../img/logo_enedis.png">
    <link rel="stylesheet" href="../css/page_redirection.css">
</head>
<body>
    <?php

    $dateParam = $_GET['date'] ?? '';
    $nomService = $_GET['nomService'] ?? '';
    $nomAgence = $_GET['nomAgence'] ?? '';


    $stmt = $connexion->prepare("SELECT heure_debut, heure_fin FROM vue_agences WHERE nomAgence = :nomAgence AND nomService = :nomService LIMIT 1");
    $stmt->execute([':nomAgence' => $nomAgence, ':nomService' => $nomService]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $heure_debut_immersion = $result['heure_debut'] ?? 'Non spécifiée';
    $heure_fin_immersion = $result['heure_fin'] ?? 'Non spécifiée';

    $debut = date_create_from_format('H:i:s', $heure_debut_immersion);
    $fin = date_create_from_format('H:i:s', $heure_fin_immersion);

    $formatted_debut = $debut->format('H\hi');
    $formatted_fin = $fin->format('H\hi');

    function getFSDUMService($connexion, $nomAgence, $nomService) {
        $query = "SELECT FSDUM_service FROM vue_agences WHERE nomAgence = :nomAgence AND nomService = :nomService";
        $stmt = $connexion->prepare($query);
        $stmt->bindParam(':nomAgence', $nomAgence, PDO::PARAM_STR);
        $stmt->bindParam(':nomService', $nomService, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['FSDUM_service'] ?? null;
    }

    function ajouterImmersionBDD($connexion, $data) {

        $requete = $connexion->prepare("INSERT INTO immersions (date_immersion, pseudo, nni, email_demandeur, email_manager, nomAgence, nomService, commentaire, statut, feedback, FSDUM_lieu, FSDUM_agent, heure_debut, heure_fin) VALUES (:date_immersion, :pseudo, :nni, :email, :email_manager, :nomAgence, :nomService, :commentaire, :statut, :feedback, :FSDUM_lieu, :FSDUM_agent, :heure_debut, :heure_fin)");

        $feedback ="En attente du Feedback" ;

        $requete->bindParam(':date_immersion', $data['date_immersion']);
        $requete->bindParam(':pseudo', $data['pseudo']);
        $requete->bindParam(':nni', $data['nni']);
        $requete->bindParam(':email', $data['email']);
        $requete->bindParam(':email_manager', $data['email_manager']);
        $requete->bindParam(':nomAgence', $data['nomAgence']);
        $requete->bindParam(':nomService', $data['nomService']);
        $requete->bindParam(':commentaire', $data['commentaire']);
        $requete->bindParam(':statut', $data['statut']);
        $requete->bindParam(':feedback', $feedback);
        $requete->bindParam(':FSDUM_lieu', $data['FSDUM_lieu']);
        $requete->bindParam(':FSDUM_agent', $data['FSDUM_agent']);
        $requete->bindParam(':heure_debut', $data['heure_debut']);
        $requete->bindParam(':heure_fin', $data['heure_fin']);

        $requete->execute();
    }

    if (isset($_POST['envoyer'])) {
        $pseudo = $_POST['pseudo'];
        $nni = $_POST['nni'];
        $email = $_POST['email'];
        $email_manager = $_POST['email_manager'];
        $nomAgence = $_POST['nomAgence'];
        $nomService = $_POST['nomService'];
        $commentaire = $_POST['commentaire'];
        $date_immersion_input = $_POST['date_selectionnee'];
        list($day, $month, $year) = explode('/', $date_immersion_input);
        $date_immersion_email = (new DateTime("$year-$month-$day"))->format('d-m-Y');
        $date_immersion_db = (new DateTime("$year-$month-$day"))->format('Y-m-d');
        $statut = "En Attente d'Approbation Managériale";

        if ($email_manager === $email) {
            $erreur = "L'adresse email du manager ne peut pas être identique à votre adresse email.";
        } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@enedis\.fr$/', $email_manager)) {
            $erreur = "L'adresse email du manager doit être du domaine @enedis.fr.";
        } else {

        $FSDUM_service = getFSDUMService($connexion, $nomAgence, $nomService);

        $dataBDD = [
            'date_immersion' => $date_immersion_db,
            'date_immersion_email' => $date_immersion_email,
            'pseudo' => $pseudo,
            'nni' => $nni,
            'email' => $email,
            'email_manager' => $email_manager,
            'nomAgence' => $nomAgence,
            'nomService' => $nomService,
            'commentaire' => $commentaire,
            'statut' => $statut,
            'FSDUM_lieu' => $FSDUM_service, 
            'FSDUM_agent' => $FSDUM_agent,
            'heure_debut' => $heure_debut_immersion,
            'heure_fin' => $heure_fin_immersion
        ];
    
        ajouterImmersionBDD($connexion, $dataBDD);
    
        $lastInsertedId = $connexion->lastInsertId();
    
        header('Location: ./confirmation.php');
        exit();
    }}
    ?>

    <form method="POST">

        <h1>Confirmation d'immersion</h1>

        <label for="date_selectionnee">Date sélectionnée</label>
        <input type="text" name="date_selectionnee" id="date_selectionnee"
            value="<?= date('d/m/Y H:i', strtotime($dateParam)); ?>" readonly>

        <label for="nomService">Service</label>
        <input type="text" name="nomService" id="nomService" value="<?= $nomService; ?>"readonly>

        <label for="nomAgence">Agence</label>
        <input type="text" name="nomAgence" id="nomAgence" value="<?= $nomAgence; ?>" readonly>

        <label for="pseudo">NOM et PRÉNOM </label>
        <input type="text" name="pseudo" id="pseudo" value="<?= $formattedName; ?>" readonly>

        <label for="nni">NNI :</label>
        <input type="text" name="nni" id="nni" value="<?= $nni ?>" readonly>

        <label for="email">Mon e-mail :</label>
        <input type="email" name="email" id="email" value="<?= $email ?>" readonly>

        <label for="email_manager">L'email de mon manager :</label> 
        <input class= "a_remplir2" type="email" name="email_manager" id="email_manager" required>

        <?php if (isset($erreur)): ?>   
            <div class="error-message">
                <?php echo $erreur; ?>
            </div>
        <?php endif; ?>

        <label for="commentaire">Message (obligatoire) :</label>
        <textarea class="a_remplir" name="commentaire" id="commentaire" placeholder="je souhaite réaliser cette immersion car..." required></textarea>

        <button type="submit" name="envoyer" id="envoi_immersion" value="Envoyer">Confirmer ma demande d'Immersion</button>



    </form>

    <script>

    var envoiImmersionButton = document.getElementById('envoi_immersion');

    envoiImmersionButton.addEventListener('click', function(event) {
        var btn = this;

        var form = btn.closest('form');
        if (form.checkValidity()) {
            btn.innerText = 'Envoi en cours...';

            setTimeout(function() {
                btn.disabled = true;
                btn.style.cursor = "default";
            }, 100); 
        } else {
            event.preventDefault(); 
        }
    });

    envoiImmersionButton.addEventListener('mouseover', function() {
        if (!this.disabled) {
            this.style.backgroundColor = '#1423DC';
        }
    });

    envoiImmersionButton.addEventListener('mouseout', function() {
        this.style.backgroundColor = ''; 
    });

    envoiImmersionButton.addEventListener('mouseenter', function() {
        if (!this.disabled) {
            this.style.backgroundColor = '#1423DC';
        }
    });

    envoiImmersionButton.addEventListener('mouseleave', function() {
        this.style.backgroundColor = ''; 
    });



        document.getElementById("date_selectionnee").value = "<?= $dateParam; ?>";
        document.getElementById("nomService").value = "<?= $nomService; ?>";
        document.getElementById("nomAgence").value = "<?= $nomAgence; ?>";
    </script>
</body>

</html>