<?php

session_start();

require_once "../database/config.php";



$parametresExistants = $_GET;

$nomDomaine = isset($_GET['nomDomaine']) ? $_GET['nomDomaine'] : '';

$chaineRequete = http_build_query($parametresExistants);

$url = '../agences_dr_paris/page_domaines.php?' . $chaineRequete;

$bdd = connect();

$requete = $bdd->prepare("SELECT * FROM vue_agences WHERE domaines = :nomDomaine ORDER BY  nomService ASC");
$requete->execute(array('nomDomaine' => $nomDomaine));
$infos = $requete->fetchAll(PDO::FETCH_OBJ);

$requete2 = $bdd->prepare("SELECT descriDomaines, imageDomaines FROM domaines WHERE nomDomaines = :nomDomaine");
$requete2->execute(array('nomDomaine' => $nomDomaine));
$infos_domaines = $requete2->fetch(PDO::FETCH_ASSOC);


includeFileBasedOnRole();
?>

<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>Mon Immersion - <?= $nomDomaine ?></title>
    <link rel="stylesheet" href="../css/page_domaines.css">
    <link rel="shortcut icon" type="image/png" href="../img/logo_enedis.png">
  </head>

  <body>

  <button id="backButton">Retour</button>

    <div class="div_titre">



    <h1 class="grand_titre"><?= $nomDomaine ?></h1>
    </div>

    <div class="groupe">

    <div class="conteneur_principal">

    <div class="image_domaine">
      <?php if (!empty($infos_domaines['imageDomaines'])): ?>
        <img src="data:image/png;base64,<?= base64_encode($infos_domaines['imageDomaines']) ?>" alt="<?= htmlspecialchars($nomDomaine) ?>">
      <?php else: ?>
      <p>Pas d'image disponible pour ce domaine</p>
      <?php endif; ?>
    </div>


    <div class="description_domaine">
    <p><?= nl2br($infos_domaines['descriDomaines']) ?></p>
    </div>

    </div>


      <?php if (empty($infos)) : ?>
        <h2 class="aucun-service">Il n'y a pas de possibilité d'immersion à ce jour au sein de ce domaine via Mon Immersion</h2>
      <?php endif; ?>

        <?php if (!empty($infos)) : ?>
          
          <h1 class="autre_titre">Voici les différents services concernant ce domaine :</h1>

          <div class="liste_domaines">
            <?php foreach ($infos as $info) : ?>
              <div class="enfants">

                <div class="rectangle">

                <div class="bulle_info"> 
                
                  <svg width="33" height="33" viewBox="0 0 39 40" fill="none" xmlns="http://www.w3.org/2000/svg" onclick="openModal(event, 'modal<?= $info->id?>')">
                    <path d="M19.5002 2.9375C24.0258 2.9375 28.366 4.73528 31.5661 7.93535C34.7662 11.1354 36.564 15.4756 36.564 20.0012C36.564 24.5268 34.7662 28.867 31.5661 32.0671C28.366 35.2672 24.0258 37.0649 19.5002 37.0649C14.9747 37.0649 10.6344 35.2672 7.43437 32.0671C4.2343 28.867 2.43652 24.5268 2.43652 20.0012C2.43652 15.4756 4.2343 11.1354 7.43437 7.93535C10.6344 4.73528 14.9747 2.9375 19.5002 2.9375ZM22.0596 13.4139C23.3271 13.4139 24.3557 12.5339 24.3557 11.2299C24.3557 9.92581 23.3247 9.04588 22.0596 9.04588C20.7921 9.04588 19.7684 9.92581 19.7684 11.2299C19.7684 12.5339 20.7921 13.4139 22.0596 13.4139ZM22.5057 27.1297C22.5057 26.8689 22.5959 26.1912 22.5447 25.8061L20.5411 28.112C20.1267 28.5483 19.6075 28.8506 19.3637 28.7701C19.2532 28.7294 19.1607 28.6506 19.1031 28.5478C19.0455 28.445 19.0264 28.3251 19.0493 28.2095L22.3887 17.66C22.6617 16.3218 21.9109 15.1006 20.3192 14.9446C18.6398 14.9446 16.1682 16.6484 14.6642 18.8105C14.6642 19.0689 14.6155 19.7124 14.6667 20.0975L16.6679 17.7892C17.0822 17.3577 17.5649 17.0531 17.8086 17.1359C17.9287 17.179 18.0271 17.2675 18.0827 17.3823C18.1383 17.4972 18.1467 17.6293 18.106 17.7502L14.7959 28.2485C14.4132 29.477 15.1371 30.6811 16.8921 30.9541C19.4759 30.9541 21.0017 29.2917 22.5081 27.1297H22.5057Z" fill="#1423DC"/>
                    </svg>
                    <p class="description" value="<?= $info->descriService ?>"></p>
    
                  </div> 

                    <h1 class="titre_domaines">
                        <a class="lien_service" href="page_services.php?nomAgence=<?= $info->nomAgence ?>&nomService=<?= $info->nomService ?>"><?= $info->nomService ?> <?= $info->nomAgence ?> </a>
                    </h1>
                  

                  </div>
              </div>
                <div id="modal<?= $info->id ?>" class="modal">
                  <div class="modal-content">
                    <div class="modal-header">
                      <span class="close" onclick="closeModal('<?= $info->id ?>')">&times;</span>
                      <h2><?= $info->nomService ?> de <?= $info->nomAgence ?></h2>
                      <p class="adresse_service"><?= $info->adresse ?></p>
                    </div>
                    <div class="modal-body">
                      <p><?= $info->descriService ?></p>
                    </div>
                  </div>
                </div>

            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>


            
        </div>
    </div>

    <script>
      function openModal(event, modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = "block";
      }

      function closeModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = "none";
      }

      var modals = document.getElementsByClassName("modal");
      var spans = document.getElementsByClassName("close");

      for (var i = 0; i < spans.length; i++) {
        spans[i].onclick = function() {
          var modalId = this.parentNode.parentNode.parentNode.id;
          closeModal(modalId);
        };
      }

      window.onclick = function(event) {
        for (var i = 0; i < modals.length; i++) {
          if (event.target == modals[i]) {
            closeModal(modals[i].id);
          }
        }
      };

      document.getElementById("backButton").addEventListener("click", function() {
  window.history.back();
});
    </script>
  </body>
</html>
