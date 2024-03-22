<?php

    header("Cache-Control: no-cache, must-revalidate");

    require_once "../database/config.php";
    session_start();

    ini_set('display_errors', '0');
    error_reporting(E_ALL);



    $userInfo = $_SESSION['userInfo'];
    includeFileBasedOnRole();

    $bdd = connect();
    $stmt = $bdd->prepare("SELECT * FROM vue_agences ORDER BY nomService ASC");
    $stmt->execute();

    $services = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt2 = $bdd->prepare("SELECT * FROM agences ORDER BY nom_agence ASC");
    $stmt2->execute();

    $agences = $stmt2->fetchAll(PDO::FETCH_OBJ);
    $selectedAgence = isset($_GET['nomAgence']) ? $_GET['nomAgence'] : '';
    $selectedServices = [];

    if (!empty($selectedAgence)) {
        $stmt3 = $bdd->prepare("SELECT * FROM vue_agences WHERE adresse = :adresse ORDER BY service ASC");
        $stmt3->bindParam(':adresse', $selectedAgence);
        $stmt3->execute();
        $selectedServices = $stmt3->fetchAll(PDO::FETCH_OBJ);
    }


?>
<!DOCTYPE html>
<html>
<head>
    <title>Mon Immersion - Gestion Planning</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js@1/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js@1/src/toastify.min.js"></script>
    <link rel="stylesheet" href="../css/modif_calendrier.css">
</head>
<body>
    <div class="grand_container">
        <h1>Gestion du Planning</h1><br><br>
        <form id="calendar-config">
            <p> Veuillez sélectionner les jours de disponibilités de votre agence pour les immersions </p>
            
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="dayOfWeek" value="1" > Lundi
                </label>
                <label>
                    <input type="checkbox" name="dayOfWeek" value="2" > Mardi
                </label>
                <label>
                    <input type="checkbox" name="dayOfWeek" value="3" > Mercredi
                </label>
                <label>
                    <input type="checkbox" name="dayOfWeek" value="4" > Jeudi
                </label>
                <label>
                    <input type="checkbox" name="dayOfWeek" value="5" > Vendredi
                </label>
            </div>

            <div style="margin-bottom: -30px;">

            <label for="jours-maxs">Nombre maximum de participants par jour dans votre service </label>
            <input type="number" id="jours-maxs" name="joursMaxs" value="" min="1" max="4" required>
            <br><br>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <label for="date-debut-immersion">Heure de début d'immersion</label>
                    <input type="time" id="date-debut-immersion" name="dateDebutImmersion" required>
                </div>
                <div>
                    <label for="date-fin-immersion">Heure de fin d'immersion</label>
                    <input type="time" id="date-fin-immersion" name="dateFinImmersion" required>
                </div>
            </div>
            <br><br>


            <label for="nom-agence">Séléctionnez votre site </label>
            <select id="nom-agence" name="nomAgence" onchange="updateServices()">
                <option selected>Choisir un site</option>
                <?php foreach($agences as $agence): ?>
                    <option value="<?= $agence->nom_agence ?>">
                        <?= $agence->nom_agence ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <label for="nom-service">Sélectionnez votre agence</label>
            <select id="nom-service" name="nomService">
                <option selected>Choisir une agence</option>
                <?php foreach($services as $service): ?>
                    <option value="<?= $service->nomService ?>"><?= $service->nomService ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <div id="email-section">
                <label for="email-1">Adresse e-mail</label>
                <div class="email-input-container">
                    <input type="email" id="email-1" name="email[]" class="email-input" required>
                </div>
                <div style="display:flex; align-items: center; justify-content:center;">
                <button type="button" class="add-email">Ajouter une autre adresse</button>
                </div>
            </div>

            <div style="display:flex; align-items: center; justify-content:center; margin-left:5px;">
                <button type="button" id="update-config">Valider les paramètres</button>
            </div>
        </form>
        <br><br><br><br>
    </div>
    <label class="disable_jour"> Vous souhaitez désactiver une date ? Cliquez dessus et confirmer ! </label>
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

        <li<?= (date('w') == 6) ? ' class="weekend"' : '' ?>>Sam</li>
        <li<?= (date('w') == 0) ? ' class="weekend"' : '' ?>>Dim</li>

        </ul>
        <ul class="jours"></ul>
      </div>
    </div>
    <div>
        <button id="generate-file-btn">Confirmer l'ensemble des paramètres</button>
    </div>
    

    <div id="modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Confirmation</h2>
            </div>
            <div class="modal-body">
                <p id="modalDateText"></p>
            </div>

            <div class="modal-footer">
                <button id="cancelButton">Annuler</button>
                <button id="confirmButton">Confirmer</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js@1/src/toastify.min.js"></script>
    <script>

    function updateServices() {
        
    var agenceSelect = document.getElementById("nom-agence");
    var serviceSelect = document.getElementById("nom-service");

    while (serviceSelect.options.length > 1) {
        serviceSelect.remove(1);
    }

    if (agenceSelect.value !== "") {

        var selectedServices = <?= json_encode($services) ?>;
        var selectedAgence = agenceSelect.value;

        var filteredServices = selectedServices.filter(function (service) {
        return service.nomAgence === selectedAgence;
        });

    filteredServices.forEach(function (service) {
        var option = document.createElement("option");
        option.value = service.nomService;
        option.text = service.nomService;
        serviceSelect.add(option);
        });

    }
}



    function openModal(modal) {
        var modal = document.getElementById(modal);
        modal.style.display = "block";
    }

    function closeModal(modal) {
        var modal = document.getElementById(modal);
        modal.style.display = "none";
    }

    var closes = document.getElementsByClassName("close");
    for (var i = 0; i < closes.length; i++) {
    closes[i].onclick = function() {
        var modal = this.parentElement.parentElement.parentElement;
        modal.style.display = "none";
    };
    }


    </script>
    <script>
        var activeDays = [1, 2, 3, 4, 5];
        var inactiveDays = [];
        var disabledDates = [];

        var weekend = [0, 6];

        $(document).ready(function() {
            const daysTag = $("ul.jours");
            const currentDateElement = $(".date_actuelle");
            const prevNextIcon = $(".icones span");
            const generateFileBtn = $("#generate-file-btn");

            let date = new Date();
            let currYear = date.getFullYear();
            let currMonth = date.getMonth();
            let selectedDate;

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
            ];


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

            let emailCount = 1;

            $(document).on("click", ".add-email", function() {
            if (emailCount < 5) {
                emailCount++;
                const newEmailInput = `
                    <div class="email-input-container">
                        <label for="email-${emailCount}">${emailCount === 2 ? "2ème" : (emailCount === 3 ? "3ème" : (emailCount + "ème"))} e-mail :</label>
                        <input type="email" id="email-${emailCount}" name="email[]" class="email-input" required>
                        <button type="button" class="remove-email">Supprimer</button>
                    </div>
                `;
                $("#email-section").append(newEmailInput);
            }
        });

        $(document).on("click", ".remove-email", function() {
            $(this).closest(".email-input-container").remove();
            emailCount--;
        });



            const updateDisabledDates = () => {
            $("ul.jours li").each(function() {
                const day = parseInt($(this).text());
                const dateToCheck = new Date(currYear, currMonth, day);
                const isDisabled = disabledDates.some(date => {
                    const dateObj = new Date(date);
                    return (
                        dateObj.getFullYear() === dateToCheck.getFullYear() &&
                        dateObj.getMonth() === dateToCheck.getMonth() &&
                        dateObj.getDate() === dateToCheck.getDate()
                    );
                });
                if (isDisabled) {
                    $(this).addClass("disabled");
                    $(this)[0].style.display='none';
                    $(this)[0].offsetHeight;
                    $(this)[0].style.display='';
                } else {
                    $(this).removeClass("disabled");
                }
            });
        };


            const renderCalendar = () => {
                let firstDayofMonth = new Date(currYear, currMonth, 1).getDay(),
                    lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate(),
                    lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(),
                    lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate();
                let liTag = "";

                if (firstDayofMonth === 0) {
                    firstDayofMonth = 6;
                } else {
                    firstDayofMonth--;
                }

                for (let i = firstDayofMonth; i > 0; i--) {
                    liTag += `<li class="inactive">${lastDateofLastMonth - i + 1}</li>`;
                }

                for (let i = 1; i <= lastDateofMonth; i++) {
                let isToday =
                    selectedDate &&
                    i === selectedDate.getDate() &&
                    currMonth === selectedDate.getMonth() &&
                    currYear === selectedDate.getFullYear()
                        ? "active"
                        : "";

                let dayOfWeek = new Date(currYear, currMonth, i).getDay();

                let isDisabled = disabledDates.some(date => {
                    const dateObj = new Date(date);
                    return dateObj.getFullYear() === currYear && dateObj.getMonth() === currMonth && dateObj.getDate() === i;
                });

                if (activeDays.includes(dayOfWeek)) {
                    const currentDate = new Date();
                    if (currentDate <= new Date(currYear, currMonth, i)) {
                        liTag += `<li class="${isToday}${isDisabled ? " disabled" : ""}">${i}</li>`;
                    } else {
                        liTag += `<li class="inactive">${i}</li>`;
                    }
                } else {
                    if (!inactiveDays.includes(dayOfWeek)) {
                        inactiveDays.push(dayOfWeek);
                    }
                    liTag += `<li class="inactive">${i}</li>`;
                }
            }
            const selectDate = (day, clickedElement) => {
            $("li.active").removeClass("active");
            $("li.active").css("background-color", "");

            clickedElement.addClass("active");
            selectedDate = new Date(currYear, currMonth, day);
            document.getElementById("modalDateText").textContent = `Êtes-vous sûr de vouloir désactiver le ${day}/${currMonth + 1}/${currYear} ?`;
            openModal("modal"); 
            

            document.getElementById("confirmButton").addEventListener("click", function() {
                var modal = document.getElementById("modal");
                modal.style.display = "none";
                disableDate(selectedDate, clickedElement);
                closeModal();
            });
        };

        var activeToastCount = 0; 

        function showToastWithLimit(message) {
            
            if (activeToastCount >= 2) {
                return;
            }

            activeToastCount++;

            Toastify({
                text: message,
                duration: 8000,
                close: true,
                gravity: "top",
                position: "center",
                backgroundColor: "#1423DC",
                color: "#FFFFFF",
                stopOnFocus: true,
                callback: function() {
                    activeToastCount--;
                }
            }).showToast();
        }

        const disableDate = (dateToDisable, clickedElement) => {
            disabledDates.push(dateToDisable);
            
            showToastWithLimit("Date désactivée avec succès");
    
            clickedElement.addClass("active");
            clickedElement.css("background-color", "");
            sendDisableDateRequest(dateToDisable);
            renderCalendar();
        };
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

                daysTag.find("li").each(function() {
                    const dayOfWeek = new Date(currYear, currMonth, parseInt($(this).text())).getDay();
                    if (weekend.includes(dayOfWeek)) {
                        $(this).addClass("weekend");
                    } else {
                        $(this).removeClass("weekend");
                    }
                });

                desactiverJoursFeries();
            };
            const generateFile = () => {
            const formattedActiveDays = JSON.stringify(activeDays);
            const formattedInactiveDays = JSON.stringify(inactiveDays);
            const formattedDisabledDates = JSON.stringify(disabledDates.map(date => date.toISOString()));
            const nomService = $("#nom-service").val();
            const nomAgence = $("#nom-agence").val();

            const staticEmailInput = $("#email-1");
            const staticEmailValue = staticEmailInput.val().trim();
            const emailInputs = $(".email-input");
            const emailArray = [];
            
            if (staticEmailValue !== '') {
                emailArray.push(staticEmailValue);
            }
            
            emailInputs.each(function() {
                const emailValue = $(this).val().trim();
                if (emailValue !== '') {
                    emailArray.push(emailValue);
                }
            });

            console.log("Email Array:", emailArray);

            const joursMaxs = $("#jours-maxs").val();

            if (nomService && nomAgence && emailArray.length > 0 && joursMaxs) {
                $.ajax({
                    url: "generate_file.php",
                    method: "POST",
                    data: {
                        activeDays: formattedActiveDays,
                        inactiveDays: formattedInactiveDays,
                        disabledDates: formattedDisabledDates,
                        nomService: nomService,
                        nomAgence: nomAgence,
                        email: emailArray,
                        joursMaxs: joursMaxs
                    },
                    success: function(response) {
                        console.log(response);
                        Toastify({
                            text: "L'ensemble de vos paramètres ont été pris en compte",
                            duration: 8000,
                            close: true,
                            gravity: "top",
                            position: 'center',  
                            backgroundColor: "#1423DC",
                            color: "#FFFFFF",
                            stopOnFocus: true 
                        }).showToast();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        console.log(xhr, status, error, nomService, nomAgence, emailArray, formattedActiveDays, formattedDisabledDates, formattedInactiveDays);
                        Toastify({
                            text: "Erreur lors de la génération du fichier, veuillez réessayer ultérieurement",
                            duration: 8000,
                            close: true,
                            gravity: "top",
                            position: 'top-center', 
                            backgroundColor: "#1423DC",
                            color: "#FFFFFF",
                            className: "toastify",
                            stopOnFocus: true 
                        }).showToast();
                    }
                });
            } else {
                console.log(nomService, nomAgence, emailArray.length, joursMaxs);
                Toastify({
                    text: "Veuillez remplir tous les champs",
                    duration: 8000,
                    close: true,
                    gravity: "top",
                    position: 'center',  
                    color: "#FFFFFF",
                    stopOnFocus: true 
                }).showToast();
            }
        };
            const goToPreviousMonth = () => {
                currMonth -= 1;
                if (currMonth < 0) {
                    currMonth = 11;
                    currYear--;
                }
                date = new Date(currYear, currMonth, new Date().getDate());
                renderCalendar();
            };
            const goToNextMonth = () => {
                currMonth += 1;

                if (currMonth > 11) {
                    currMonth = 0;
                    currYear++;
                }
                date = new Date(currYear, currMonth, new Date().getDate());
                renderCalendar();
            };
            const initializeCalendar = () => {
                const currentDate = new Date();
                selectedDate = currentDate;
                currYear = currentDate.getFullYear();
                currMonth = currentDate.getMonth();
                renderCalendar();
            };

            initializeCalendar();

            $("#prev").on("click", function() {
                goToPreviousMonth();
            });

            $("#next").on("click", function() {
                goToNextMonth();
            });

            generateFileBtn.on("click", function() {
                generateFile();
            });

            

            $("#update-config").click(function() {
            activeDays = [];
            inactiveDays = [];
            $("input[name='dayOfWeek']").each(function() {
                const dayOfWeek = parseInt($(this).val());
                if ($(this).is(":checked")) {
                    activeDays.push(dayOfWeek);
                } else {
                    inactiveDays.push(dayOfWeek);
                }
            });

            const emailInputs = $(".email-input");
            const emailArray = [];
            emailInputs.each(function() {
                const emailValue = $(this).val().trim();
                if (emailValue !== '') {
                    emailArray.push(emailValue);
                }
            });

            const nomAgence = $("#nom-agence").val();
            const nomService = $("#nom-service").val();
            const dateDebutImmersion = $("#date-debut-immersion").val();
            const dateFinImmersion = $("#date-fin-immersion").val();

            if (emailArray.length > 0 && nomAgence && nomService && dateDebutImmersion && dateFinImmersion) {
                $.ajax({
                    url: "ajout_emails_service.php",
                    method: "POST",
                    data: {
                        emails: emailArray,
                        nomAgence: nomAgence,
                        nomService: nomService,
                        dateDebutImmersion: dateDebutImmersion,
                        dateFinImmersion: dateFinImmersion
                    },
                    success: function(response) {
                        console.log(response);
                        renderCalendar();
                        Toastify({
                            text: "Configuration mise à jour avec succès",
                            duration: 8000,
                            close: true,
                            gravity: "top",
                            position: 'top-center', 
                            backgroundColor: "#1423DC",
                            color: "#FFFFFF",
                            className: "toastify",
                            stopOnFocus: true 
                        }).showToast();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        console.error(xhr, status, error);
                        Toastify({
                            text: "Erreur lors de la mise à jour de la configuration",
                            duration: 8000,
                            close: true,
                            gravity: "top",
                            position: 'top-center', 
                            backgroundColor: "#FF0000",
                            color: "#FFFFFF",
                            className: "toastify",
                            stopOnFocus: true 
                        }).showToast();
                    }
                });
            } else {
                // Gérer le cas où les données ne sont pas complètes
                console.log("Veuillez remplir tous les champs requis");
            }
        });

        });


        document.getElementById("cancelButton").addEventListener("click", function() {
        var modal = document.getElementById("modal");
        modal.style.display = "none";
    });

    document.getElementsByClassName("close")[0].addEventListener("click", function() {
        var modal = document.getElementById("modal");
        modal.style.display = "none";
    });
    </script>


</body>
</html>