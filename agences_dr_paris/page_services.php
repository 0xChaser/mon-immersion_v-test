<?php

session_start();

require_once "../database/config.php";
includeFileBasedOnRole();


$nomAgence = $_GET['nomAgence'];
$nomService = $_GET['nomService'];

$parametresExistants = $_GET;
$parametresExistants['nomAgence'] = $nomAgence;
$parametresExistants['nomService'] = $nomService;

$chaineRequete = http_build_query($parametresExistants);
$url = '../calendrier_agences/calendrier.php?' . $chaineRequete;

$bdd = connect();

$requete = $bdd->prepare("SELECT * FROM vue_agences WHERE nomAgence = :nomAgence AND nomService = :nomService");
$requete->execute(array('nomAgence' => $nomAgence, 'nomService' => $nomService));
$info = $requete->fetch(PDO::FETCH_OBJ);

$requete2 = $bdd->prepare("SELECT lien_adresse, adresse FROM vue_agences WHERE nomAgence = :nomAgence ");
$requete2->execute(array('nomAgence' => $nomAgence));
$infos = $requete2->fetch(PDO::FETCH_OBJ);
?>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Mon Immersion - <?= $info->nomService ?></title>
    <link rel="shortcut icon" type="image/png" href="../img/logo_enedis.png">
    <link rel="stylesheet" href="../css/page_services.css">
</head>
<body>

<button id="backButton">Retour</button>

<div class="div_titre">


     <div class="contenu_central">

        <div class="partie_svg">
          <h1 class="grand_titre"><?= $nomAgence ?></h1>
          <img src="../img/agences/carte_<?= $nomAgence ?>.svg" alt="<?= $nomAgence ?>">
        </div>

      <div class="conteneur_image">
        <a href="<?= $infos->lien_adresse ?>" target="_blank">        
        <svg  xmlns="http://www.w3.org/2000/svg" height="35" width="35" viewBox="0 0 384 512"><path class="icon"d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" fill="#41A57D"/></svg>
            </a>
        <h1 class="autre_titre"><?= $infos->adresse ?></h1>
      </div>
  </div>

</div>

<div class="groupe">
    <div class="section_gauche">
        <div class="site-image">

        <img src="../img/agences/<?= $nomAgence ?>.png" alt="Image">

        </div>

    </div>

    <div class="section_droite">

        <h1 class="titre_domaines"><?= $nomService ?></h1>

        <div class="texte">

          <p class="description" style="height: <?= $nomService == 'Acheminement' ? '500px' : 'auto';  ?>">
              <?= nl2br(str_replace('. ', ".\n", $info->descriService)) ?>
          </p>

        </div>
    </div>
</div>

<a href="<?= $url ?>" >
    <div class="bouton">Prendre Rendez-vous <br></div>
</a>


<script>

document.getElementById("backButton").addEventListener("click", function() {
  window.history.back();
});

</script>
</body>
</html>
