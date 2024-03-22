<?php

session_start();
require_once "../database/config.php";


includeFileBasedOnRole();


$nomAgence = $_GET['nomAgence']; 
$nomService = $_GET['nomService']; 
$file_to_include = $nomService . '_calendrier_' . $nomAgence . '.php'; 

if (file_exists($file_to_include)) {
    include $file_to_include; // Inclure le fichier s'il existe
} else {
    $service_not_set_up = true; // Définir la variable $service_not_set_up à true si le fichier n'existe pas
}

$immersionsCounts = array(); // Créer un tableau pour stocker le nombre d'immersions par jour

$pdo = connect(); // Établir une connexion à la base de données

$sql = "SELECT date_immersion, COUNT(*) as count FROM immersions WHERE nomService = :nomService AND nomAgence = :nomAgence GROUP BY date_immersion"; // Requête SQL pour récupérer le nombre d'immersions par jour
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":nomService", $nomService); // Lier le paramètre :nomService
$stmt->bindValue(":nomAgence", $nomAgence); // Lier le paramètre :nomAgence
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dateImmersion = $row['date_immersion'];
    $dateImmersion = date("Y-m-d", strtotime($dateImmersion));  // Formater la date au format "yyyy-mm-dd"
    $count = $row['count'];
    if (!isset($immersionsCounts[$dateImmersion])) {
        $immersionsCounts[$dateImmersion] = $count; // Stocker le nombre d'immersions dans le tableau
    } else {
        $immersionsCounts[$dateImmersion] += $count; // Ajouter le nombre d'immersions existant à celui du jour actuel
}
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Mon Immersion - Calendrier</title>
  <link rel="stylesheet" href="../css/calendrier.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>

<button id="backButton">Retour</button>

  <div class="grand_container">

    <div>
      <h1 class="title-blue"><?= $nomService. " de " . $nomAgence ?></h1><br>
    </div>
    <div>
      <h2 class="title-blue-text">Prise de rendez-vous</h2><br>
    </div>

  <?php if(isset($service_not_set_up) && $service_not_set_up === true): ?>
  <div>
    <p class="aucun-planning">Ce service n'est pas disponible pour le moment, veuillez réessayer ultérieurement.</p>
  </div>
  <?php endif; ?>

  <?php if (!isset($service_not_set_up) || $service_not_set_up === false): ?>

    <div class="groupe">
      <header>
        <p class="date_actuelle"></p>
        <div class="icones">
          <span id="prev" class="material-symbols-rounded">&#10094;</span>
          <span id="next" class="material-symbols-rounded">&#10095;</span>
        </div>
      </header>
      <div class="calendrier">
        <ul class="semaines">
          <li>Lun</li>
          <li>Mar</li>
          <li>Mer</li>
          <li>Jeu</li>
          <li>Ven</li>
          <li class="weekend">Sam</li>
          <li class="weekend">Dim</li>
        </ul>
        <ul class="jours"></ul>
      </div>
    </div>
    <div>
      <button id="redirection-btn" style="display: none;">Confirmer cette date</button>
    </div>
  </div>

  <?php endif; ?>
  <script>
  document.getElementById("backButton").addEventListener("click", function () {
      window.history.back();
    });

  </script>
  <script>
const activeDays = <?php echo json_encode($activeDays); ?>; // Tableau des jours actifs (récupéré depuis PHP)
const inactiveDays = <?php echo json_encode($inactiveDays); ?>; // Tableau des jours inactifs (récupéré depuis PHP)
const disabledDates = <?php echo json_encode($disabledDates); ?>; // Tableau des dates désactivées (récupéré depuis PHP)
const nomService = <?php echo json_encode($nomService); ?>; // Nom du service (récupéré depuis PHP)
const nomAgence = <?php echo json_encode($nomAgence); ?>; // Nom de l'agence (récupéré depuis PHP)
const immersionsCounts = <?php echo json_encode($immersionsCounts); ?>; // Nombre d'immersions (récupéré depuis PHP)
const joursMaxs = <?php echo ($joursMaxs); ?>; // Nombre maximum de jours (récupéré depuis PHP)


function activeDaysCount(dayOfWeek) {
  let count = 0;
  for (let i = 0; i < activeDays.length; i++) {
      if (activeDays[i] === dayOfWeek) {
          count++;
      }
  }
  return count;
}

function isDateInactive(day, month, year) {
  const formattedDate = `${year}-${(month).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
  console.log('formattedDate:', formattedDate);

  const immersionCount = parseInt(immersionsCounts[formattedDate] || '0');
  console.log('immersionCount:', immersionCount);

  const joursMaxsNum = parseInt(joursMaxs);
  console.log('joursMaxs:', joursMaxsNum);

  const result = immersionCount >= joursMaxsNum;
  console.log('result:', result);

  return result;
}

// Le code ci-dessus définit deux fonctions JavaScript :

// - activeDaysCount : compte le nombre de jours actifs dans la semaine
// - isDateInactive : vérifie si une date est inactive en fonction du nombre d'immersions par rapport à la valeur joursMaxs

$(document).ready(function() {
  const daysTag = $("ul.jours"); // Sélectionne les balises <ul> ayant la classe "jours"
  const currentDateElement = $(".date_actuelle"); // Sélectionne l'élément ayant la classe "date_actuelle"
  const prevNextIcon = $(".icones span"); // Sélectionne les balises <span> ayant la classe "icones"
  const redirectionBtn = $("#redirection-btn"); // Sélectionne le bouton ayant l'id "redirection-btn"

  let date = new Date(); // Crée un objet Date pour la date actuelle
  let currYear = date.getFullYear(); // Récupère l'année de la date actuelle
  let currMonth = date.getMonth(); // Récupère le mois de la date actuelle
  let selectedDate; // Variable pour stocker la date sélectionnée

  const months = [
    "Janvier",
    "Février",
    "Mars",
    "Avril",
    "Mai",
    "Juin",
    "Juillet",
    "Août",
    "Septembre",
    "Octobre",
    "Novembre",
    "Décembre",
  ]; // Tableau des mois en français


  var joursFeries = [
            "2024-01-01", // Nouvel An 2024
            "2024-04-01", // Lundi de Pâques 2024
            "2024-05-01", // Fête du Travail 2024
            "2024-05-08", // Victoire 1945 2024
            "2024-05-09", // Ascension 2024
            "2024-05-20", // Lundi de Pentecôte 2024
            "2024-07-14", // Fête Nationale 2024
            "2024-08-15", // Assomption 2024
            "2024-11-01", // Toussaint 2024
            "2024-11-11", // Armistice 1918 2024
            "2024-12-25", // Noël 2024
            "2025-01-01", // Nouvel An 2025
            "2025-04-21", // Lundi de Pâques 2025
            "2025-05-01", // Fête du Travail 2025
            "2025-05-08", // Victoire 1945 2025
            "2025-05-29", // Ascension 2025
            "2025-06-09", // Lundi de Pentecôte 2025
            "2025-07-14", // Fête Nationale 2025
            "2025-08-15", // Assomption 2025
            "2025-11-01", // Toussaint 2025
            "2025-11-11", // Armistice 1918 2025
            "2025-12-25"  // Noël 2025
        ];


            function desactiverJoursFeries() {
            joursFeries.forEach(function(jourFerie) {
                var dateFerie = new Date(jourFerie);
                $("ul.jours li").each(function() {
                    var day = parseInt($(this).text());
                    var dateToCheck = new Date(currYear, currMonth, day);

                    if(dateFerie.getFullYear() === dateToCheck.getFullYear() && dateFerie.getMonth() === dateToCheck.getMonth() && dateFerie.getDate() === dateToCheck.getDate()) {
                        $(this).addClass("inactive");
                        $(this)[0].style.pointerEvents = 'none'; 
                    }
                });
            });
        }

  const renderCalendar = () => {
  let firstDayofMonth = new Date(currYear, currMonth, 1).getDay(); // Jour de la semaine du premier jour du mois
  let lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate(); // Dernier jour du mois
  let lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(); // Jour de la semaine du dernier jour du mois
  let lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate(); // Dernier jour du mois précédent
  let liTag = ""; // Pour stocker les balises <li> du calendrier

  if (firstDayofMonth === 0) {
    firstDayofMonth = 6;
  } else {
    firstDayofMonth--;
  }

  for (let i = firstDayofMonth; i > 0; i--) {
    liTag += `<li class="inactive">${lastDateofLastMonth - i + 1}</li>`;
  }

  // Calcul de la date de coupure (7 jours plus tard)
  const cutoffDate = new Date();
  cutoffDate.setDate(cutoffDate.getDate() + 7);

  for (let i = 1; i <= lastDateofMonth; i++) {
    let isToday = selectedDate && i === selectedDate.getDate() && currMonth === selectedDate.getMonth() && currYear === selectedDate.getFullYear() ? "active" : "";
    let dayOfWeek = new Date(currYear, currMonth, i).getDay();
    const dateToCheck = new Date(currYear, currMonth, i);
    let isWithin7Days = dateToCheck < cutoffDate;

    if (!isWithin7Days && activeDays.includes(dayOfWeek)) {
      const formattedDate = new Date(currYear, currMonth, i).toISOString().split('T')[0];
      const isDisabled = disabledDates.includes(formattedDate);
      const isInactive = isDateInactive(i, currMonth + 1, currYear);
      liTag += `<li class="${isToday} ${isDisabled ? 'inactive' : ''} ${isInactive ? 'inactive' : ''}">${i}</li>`;
    } else {
      liTag += `<li class="inactive">${i}</li>`; 
    }
  }

  for (let i = lastDayofMonth; i < 6; i++) {
    liTag += `<li class="inactive">${i - lastDayofMonth + 1}</li>`;
  }

  currentDateElement.text(`${months[currMonth]} ${currYear}`);
  daysTag.html(liTag);

  daysTag.find("li").not(".inactive").on("click", function() {
    const day = $(this).text();
    const clickedElement = $(this);
    selectDate(parseInt(day), clickedElement);
  });

  desactiverJoursFeries();
};

  const selectDate = (day, element) => {
    const currentDate = new Date();
    if (currentDate <= new Date(currYear, currMonth, day)) {
      daysTag.find("li").removeClass("active");
      element.addClass("active");
      selectedDate = new Date(currYear, currMonth, day);
      enableRedirectionButton();
    } else {
      disableRedirectionButton();
    }
    // La fonction ci-dessus est appelée lorsque l'utilisateur sélectionne une date
    // Elle met à jour la classe des balises <li> pour indiquer la date sélectionnée
  };
// Fonction pour activer le bouton
const enableRedirectionButton = () => {
  redirectionBtn.css("display", "block").addClass("bouton"); // Affiche le bouton et ajoute la classe 'bouton'
};

  const disableRedirectionButton = () => {
  redirectionBtn.css("display", "none").removeClass("bouton"); // Cache le bouton et supprime la classe 'bouton'
};
  // Les fonctions ci-dessus activent ou désactivent le bouton de redirection en fonction de la sélection de date

  const goToPreviousMonth = () => {
    currMonth -= 1;
    if (currMonth < 0) {
      currMonth = 11;
      currYear--;
    }
    date = new Date(currYear, currMonth, new Date().getDate());
    renderCalendar();
    disableRedirectionButton();
  };

  const goToNextMonth = () => {
    currMonth += 1;

    if (currMonth > 11) {
      currMonth = 0;
      currYear++;
    }
    date = new Date(currYear, currMonth, new Date().getDate());
    renderCalendar();
    disableRedirectionButton();
  };
  // Les fonctions ci-dessus permettent de passer au mois précédent ou suivant dans le calendrier

  const initializeCalendar = () => {
    const currentDate = new Date();
    selectedDate = currentDate;
    currYear = currentDate.getFullYear();
    currMonth = currentDate.getMonth();
    renderCalendar();
  };
  // La fonction ci-dessus initialise le calendrier en utilisant la date actuelle

  initializeCalendar();

  $("#prev").on("click", function() {
    goToPreviousMonth();
  });

  $("#next").on("click", function() {
    goToNextMonth();
  });

  redirectionBtn.on("click", function() {
    const formattedDate = selectedDate.toLocaleDateString();
    const url = `../users/page_redirection.php?date=${formattedDate}&nomService=${encodeURIComponent(nomService)}&nomAgence=${encodeURIComponent(nomAgence)}`;
    window.location.href = url;
  });
  // Les gestionnaires d'événements ci-dessus sont attachés aux clics sur les boutons précédent et suivant,
  // ainsi qu'au clic sur le bouton de redirection

});
  console.log(activeDays, inactiveDays, disabledDates, nomAgence, nomService, immersionsCounts, joursMaxs, isDateInactive);


</script>

  </script>
</body>
</html>