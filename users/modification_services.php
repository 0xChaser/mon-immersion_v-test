<?php
require "../database/config.php";

$conn = connect();

session_start();

includeFileBasedOnRole();
ini_set('display_errors', '0');
error_reporting(E_ALL);


if(isset($_POST['formsend_service'])){

    if(isset($_POST['add_nomAgence'])) { $add_nomAgence = $_POST['add_nomAgence']; }
    if(isset($_POST['add_domaines'])) { $add_domaines = $_POST['add_domaines']; }
    if(isset($_POST['add_nomService'])) { $add_nomService = $_POST['add_nomService']; }
    if(isset($_POST['add_descriService'])) { $add_descriService = $_POST['add_descriService']; }
    if(isset($_POST['add_fsdumService'])) { $add_fsdumService = $_POST['add_fsdumService']; }
    $lien_adresse ="A modifier" ;

    if(!empty($add_nomAgence) && !empty($add_descriService) && !empty($add_domaines)&& !empty($add_fsdumService) && !empty($add_nomService)){

        $ajoutService = $conn->prepare("INSERT INTO vue_agences(nomService, nomAgence, descriService, domaines, lien_adresse, FSDUM_service) VALUES (:nomService, :nomAgence, :descriService, :domaine, :lien_adresse, :fsdumService)");

        $ajoutService->bindParam(':nomService', $add_nomService, PDO::PARAM_STR);
        $ajoutService->bindParam(':nomAgence', $add_nomAgence, PDO::PARAM_STR);
        $ajoutService->bindParam(':descriService', $add_descriService, PDO::PARAM_STR);
        $ajoutService->bindParam(':fsdumService', $add_fsdumService, PDO::PARAM_STR);
        $ajoutService->bindParam(':domaine', $add_domaines, PDO::PARAM_STR);
        $ajoutService->bindParam(':lien_adresse', $lien_adresse, PDO::PARAM_STR);

        $ajoutService->execute();


    } else {
        $error_message = "Remplir tous les champs";
    }
}

if(isset($_POST['formsend_domaines'])){

    if(isset($_POST['nomDomaines_add'])) { $nomDomaines_add = $_POST['nomDomaines_add']; }
    if(isset($_POST['descriDomaines_add'])) { $descriDomaines_add = $_POST['descriDomaines_add']; }

    $imageContent = null;
    if(isset($_FILES['imageDomaines_add']) && $_FILES['imageDomaines_add']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imageDomaines_add']['tmp_name'];
        $fileType = mime_content_type($fileTmpPath);

        if(in_array($fileType, ['image/jpeg', 'image/png', 'image/gif'])) {
            $imageContent = file_get_contents($fileTmpPath);
        }
    }

    if(!empty($nomDomaines_add) && !empty($descriDomaines_add)){

        $ajoutDomaine = $conn->prepare("INSERT INTO domaines(nomDomaines, descriDomaines, imageDomaines) VALUES (:nomDomaines, :descriDomaines, :imageDomaines)");

        $ajoutDomaine->bindParam(':nomDomaines', $nomDomaines_add, PDO::PARAM_STR);
        $ajoutDomaine->bindParam(':descriDomaines', $descriDomaines_add, PDO::PARAM_STR);
        $ajoutDomaine->bindParam(':imageDomaines', $imageContent, PDO::PARAM_LOB);

        $ajoutDomaine->execute();

    } else {
        $error_message = "Remplir tous les champs";
    }
}


function getAgencesData($conn) {
    $query = "SELECT * FROM vue_agences";
    $stmt = $conn->query($query);
    $agencesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $agencesData;
}

function getDomainData($conn){
    $sql = "SELECT * from domaines ORDER BY nomDomaines ASC";
    $requete = $conn->query($sql);
    $domaines = $requete->fetchAll(PDO::FETCH_ASSOC);
    return $domaines;
}

$stmt = $conn->prepare("SELECT * FROM agences ORDER BY nom_agence ASC");
$stmt->execute();
$sites = $stmt->fetchAll(PDO::FETCH_OBJ);

$stmt2 = $conn->prepare("SELECT nomDomaines FROM domaines ORDER BY nomDomaines ASC");
$stmt2->execute();
$select_noms = $stmt2->fetchAll(PDO::FETCH_OBJ);

function updateDataService($conn, $id_service_edit, $nomAgence_edit, $nomService_edit, $descriService_edit, $domaines_edit, $fsdumService_edit) {
    $query = "UPDATE vue_agences SET nomAgence = :nomAgence_edit, nomService = :nomService_edit, descriService = :descriService_edit, domaines = :domaines_edit, FSDUM_service = :fsdumService_edit WHERE id = :id_service_edit";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_service_edit', $id_service_edit, PDO::PARAM_INT);
    $stmt->bindParam(':nomAgence_edit', $nomAgence_edit, PDO::PARAM_STR);
    $stmt->bindParam(':nomService_edit', $nomService_edit, PDO::PARAM_STR);
    $stmt->bindParam(':descriService_edit', $descriService_edit, PDO::PARAM_STR);
    $stmt->bindParam(':domaines_edit', $domaines_edit, PDO::PARAM_STR);
    $stmt->bindParam(':fsdumService_edit', $fsdumService_edit, PDO::PARAM_STR);
    $stmt->execute();
}

function updateDataDomaine($conn, $id_domaines_edit, $nomDomaines_edit, $descriDomaines_edit, $imageContent = null) {

    $queryAncienNom = "SELECT nomDomaines FROM domaines WHERE id_domaines = :id_domaines_edit";
    $stmtAncienNom = $conn->prepare($queryAncienNom);
    $stmtAncienNom->bindParam(':id_domaines_edit', $id_domaines_edit, PDO::PARAM_INT);
    $stmtAncienNom->execute();
    $result = $stmtAncienNom->fetch(PDO::FETCH_ASSOC);
    $ancien_nom_domaine = $result['nomDomaines'];

    if ($imageContent !== null) {

        $query = "UPDATE domaines SET nomDomaines = :nomDomaines_edit, descriDomaines = :descriDomaines_edit, imageDomaines = :imageContent WHERE id_domaines = :id_domaines_edit";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':imageContent', $imageContent, PDO::PARAM_LOB);
    } else {

        $query = "UPDATE domaines SET nomDomaines = :nomDomaines_edit, descriDomaines = :descriDomaines_edit WHERE id_domaines = :id_domaines_edit";
        $stmt = $conn->prepare($query);
    }
    
    $stmt->bindParam(':id_domaines_edit', $id_domaines_edit, PDO::PARAM_INT);
    $stmt->bindParam(':nomDomaines_edit', $nomDomaines_edit, PDO::PARAM_STR);
    $stmt->bindParam(':descriDomaines_edit', $descriDomaines_edit, PDO::PARAM_STR);
    $stmt->execute();

    if ($ancien_nom_domaine !== $nomDomaines_edit) {
        $query_modif_nom = "UPDATE vue_agences SET domaines = :nomDomaines_edit WHERE domaines = :ancien_nom_domaine";
        $stmt2 = $conn->prepare($query_modif_nom);
        $stmt2->bindParam(':nomDomaines_edit', $nomDomaines_edit, PDO::PARAM_STR);
        $stmt2->bindParam(':ancien_nom_domaine', $ancien_nom_domaine, PDO::PARAM_STR);
        $stmt2->execute();
    }
}

function deleteService($conn, $id_service_delete) {
    $query = "DELETE FROM vue_agences WHERE id = :id_service_delete";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_service_delete', $id_service_delete, PDO::PARAM_INT);
    $stmt->execute();
}


function deleteDomain($conn, $id_domaines_delete) {
    $query = "DELETE FROM domaines WHERE id_domaines = :id_domaines_delete";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_domaines_delete', $id_domaines_delete, PDO::PARAM_INT);
    $stmt->execute();
}


    
if (isset($_POST['action_service']) && $_POST['action_service'] == 'deleteService') {
    $id_service_delete = $_POST['id_service_delete'];
    deleteService($conn, $id_service_delete);
}else{

    if (isset($_POST['id_service_edit'])) {
        $id_service_edit = $_POST['id_service_edit'];
    }
    if (isset($_POST['nomAgence_edit'])) {
        $nomAgence_edit = $_POST['nomAgence_edit'];
    }
    if (isset($_POST['nomService_edit'])) {
        $nomService_edit = $_POST['nomService_edit'];
    }
    if (isset($_POST['descriService_edit'])) {
        $descriService_edit = $_POST['descriService_edit'];
    }
    if (isset($_POST['domaines_edit'])) {
        $domaines_edit = $_POST['domaines_edit'];
    }
    if (isset($_POST['fsdumService_edit'])) {
        $fsdumService_edit = $_POST['fsdumService_edit'];
    }

    updateDataService($conn, $id_service_edit, $nomAgence_edit, $nomService_edit, $descriService_edit, $domaines_edit, $fsdumService_edit);
}


if (isset($_POST['action_domaine']) && $_POST['action_domaine'] == 'deleteDomain') {
    $id_domaines_delete = $_POST['id_domaines_delete'];
    deleteDomain($conn, $id_domaines_delete);
} else {
    $imageContent = null; 
    
    if (isset($_FILES['imageDomaines_edit']) && $_FILES['imageDomaines_edit']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imageDomaines_edit']['tmp_name'];
        $fileType = mime_content_type($fileTmpPath);

        if (in_array($fileType, ['image/jpeg', 'image/png', 'image/gif'])) {
            $imageContent = file_get_contents($fileTmpPath);
        } else {

        }
    } elseif ($_FILES['imageDomaines_edit']['error'] !== UPLOAD_ERR_NO_FILE) {

    }

    $id_domaines_edit = $_POST['id_domaines_edit'];
    $nomDomaines_edit = $_POST['nomDomaines_edit'];
    $descriDomaines_edit = $_POST['descriDomaines_edit'];

    updateDataDomaine($conn, $id_domaines_edit, $nomDomaines_edit, $descriDomaines_edit, $imageContent);
}



$agencesData = getAgencesData($conn);
$domaines = getDomainData($conn);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/modification_services.css">
    <title>Mon Immersion - Ajouter un Service</title>
    <script>
    
    window.addEventListener('DOMContentLoaded', (event) => {
    let rows = document.querySelectorAll('tr');

    rows.forEach((row, index) => {
        if(index % 2 === 0) {
            row.style.backgroundColor = '#F2F2F2';
        } else {
            row.style.backgroundColor = 'white';
        }
    });
    });

</script>
</head>
<body>

        <h1 id="headerTitle">Ajouter un service</h1>

        <div class="navbar_modif">
            <button id="edit_services_btn" onclick="showSection('edit_services')">Modifier un service</button>
            <button id="edit_domaines_btn" onclick="showSection('edit_domaines')">Modifier un domaine</button>
        </div>

        <div class="navbar_ajout">
            <button id="add_services_btn" onclick="showSection('add_services')">Ajouter un service</button>
            <button id="add_domaines_btn" onclick="showSection('add_domaines')">Ajouter un domaine</button>
        </div>





    <div id="edit_servicesSection" style="display: flex;">

        <div class="filtres_div">


            <div>

                <label for="filterAgency">Filtrer par sites :</label>

                <select id="filterAgency" onchange="filterTable()">
                    <option value="all">Tous les sites</option>
                    <option value="Grenelle">Grenelle</option>
                    <option value="Batignolles">Batignolles</option>
                    <!-- <option value="Equation">Equation</option>
                    <option value="Saint-Maur">Saint-Maur</option> -->
                    <option value="Italie">Italie</option>
                </select>

            </div>

            <div>    

                <label for="filterDomain">Filtrer par domaine:</label>

                <select id="filterDomain" onchange="filterTable()">
                    <option value="all">Tous les domaines</option>
                    <?php foreach ($domaines as $domaine): ?>
                        <option value="<?= $domaine['nomDomaines'] ?>"><?= $domaine['nomDomaines'] ?></option>
                    <?php endforeach; ?>
                </select>


            </div>

        </div>


        <table border="1">
            <tr class="table-header">
                <th style="display: none;">ID</th>
                <th>Nom du Site</th>
                <th>Nom du Service</th>
                <th>Description</th>
                <th>Domaine</th>
                <th>Code FSDUM</th>
                <th>Action</th>
            </tr>
            <?php foreach ($agencesData as $agence) { ?>
                <tr>
                    <td style="display:none"><?= $agence['id'] ?></td>
                    <td><?= $agence['nomAgence'] ?></td>
                    <td><?= $agence['nomService'] ?></td>
                    <td><?= $agence['descriService'] ?></td>
                    <td><?= $agence['domaines'] ?></td>
                    <td><?= $agence['FSDUM_service'] ?></td>
                    <td>
                    <button class="bouton_edit" data-id="<?= $agence['id'] ?>"
                        data-nomAgence="<?= htmlspecialchars($agence['nomAgence']) ?>"
                        data-nomService="<?= htmlspecialchars($agence['nomService']) ?>"
                        data-descriService="<?= htmlspecialchars($agence['descriService']) ?>"
                        data-domaines="<?= htmlspecialchars($agence['domaines']) ?>"
                        data-fsdumService="<?= htmlspecialchars($agence['FSDUM_service']) ?>"
                        onclick="openEditServiceModal(this)">Modifier
                    </button>
                    <button class="bouton_delete" onclick="openDeleteServiceModal(<?= $agence['id'] ?>)">Supprimer</button>
                    </td>
                </tr>

                    <div id="editServiceModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('editServiceModal')">&times;</span>
                        <h2>Modifier un Service</h2>
                        <form method="post">

                            <input type="hidden" name="id_service_edit" id="editServiceId">
                            
                            <label for="editNomAgence">Nom du Site :</label>
                            <input type="text" name="nomAgence_edit" id="editNomAgence" required><br>
                            
                            <label for="editNomService">Nom du Service :</label>
                            <input type="text" name="nomService_edit" id="editNomService" required ><br>

                            <label for="editDescriService">Description du Service :</label>
                            <textarea name="descriService_edit" id="editDescriService" required style="width: 500px; height: 107px;"></textarea><br>

                            <label for="editDomaines">Domaines :</label>
                            <select name="domaines_edit" id="editDomaines" required>
                                <?php foreach ($domaines as $domaine): ?>
                                    <option value="<?= htmlspecialchars($domaine['nomDomaines'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($domaine['nomDomaines'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select><br>

                            <label for="editfsdumService">Code FSDUM :</label>
                            <input type="text" name="fsdumService_edit" id="editfsdumService" required ><br>


                            <input type="hidden" class="bouton_edit" name="action_service" value="editDomain">
                            <button class="bouton_edit" type="submit">Mettre à jour</button>

                        </form>
                    </div>
                </div>


                <div id="deleteServiceModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('deleteServiceModal')">&times;</span>
                        <h2>Supprimer le Service</h2>
                        <form method="post">
                            <input type="hidden" name="action_service" value="deleteService">
                            <input type="hidden" name="id_service_delete" id="deleteServiceId">
                            <p>Êtes vous sûr de vouloir supprimer ce service ?</p>
                            <button class="bouton_delete" type="submit">Supprimer</button>
                        </form>
                    </div>
                </div>



            <?php } ?>
        </table>
    </div>

    <div id="edit_domainesSection" style="display: none;">
        <table border="1">
            <tr class="table-header">
                <th style="display: none;">ID</th>
                <th>Nom du Domaine</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php foreach ($domaines as $domaine) { ?>
                <tr>
                    <td style="display:none"><?= $domaine['id_domaines'] ?></td>
                    <td><?= $domaine['nomDomaines'] ?></td>
                    <td><?= $domaine['descriDomaines'] ?></td>
                    <td>
                    <button class="bouton_edit" data-id_domaines="<?= $domaine['id_domaines'] ?>"
                        data-nomDomaines="<?= htmlspecialchars($domaine['nomDomaines']) ?>"
                        data-descriDomaines="<?= htmlspecialchars($domaine['descriDomaines']) ?>"
                        onclick="openEditDomainModal(this)">Modifier
                    </button>
                    <button class="bouton_delete" onclick="openDeleteDomainModal(<?= $domaine['id_domaines'] ?>)">Supprimer</button>
                    </td>
                </tr>



                <div id="editDomainModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('editDomainModal')">&times;</span>
                        <h2>Modifier un Domaine</h2>
                        <form method="post" enctype="multipart/form-data">

                            <label for="editNomDomaines">Nom du Domaine :</label>
                            <input type="text" name="nomDomaines_edit" id="editNomDomaines" required>

                            <label for="editDescriDomaine">Description du Domaine :</label>
                            <textarea name="descriDomaines_edit" id="editDescriDomaine" required style="width: 426px; height: 133px;"></textarea>

                            <label for="editImageDomaines">Image du Domaine :</label>
                            <input type="file" name="imageDomaines_edit" id="editImageDomaine" accept="image/*">


                            <input type="hidden" name="id_domaines_edit" id="editDomainId">
                            <button class="bouton_edit" type="submit">Mettre à jour</button>
                        </form>
                    </div>
                </div>

                <div id="deleteDomainModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('deleteDomainModal')">&times;</span>
                        <h2>Supprimer le Domaine</h2>
                        <form method="post">
                            <input type="hidden" name="action_domaine" value="deleteDomain">
                            <input type="hidden" name="id_domaines_delete" id="deleteDomainId">
                            <p>Êtes vous sûr de vouloir supprimer ce service ?</p>
                            <button class="bouton_delete" type="submit">Supprimer</button>
                        </form>
                    </div>
                </div>


                <?php } ?>
            </table>

        <div id="noResults" class="aucun-service" >Aucun service pour ce domaine</div>    
        
    </div>
    

    <div id="add_servicesSection" style="display: none;">

        <form class="add_form" method="post">

            <h2> Ajouter un Service </h2>

            <div class="add_service_container">
                <div style="display:flex; align-items:center; flex-direction:column">
                    <label class="add_label" for="add_nomAgence">Nom du site</label>
                    <select class="add_select" id="add_nomAgence" name="add_nomAgence" onchange="updateServices()">
                        <option selected>Choisir un site</option>
                        <?php foreach($sites as $site): ?>
                            <option value="<?= $site->nom_agence ?>"><?= $site->nom_agence ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display:flex; align-items:center; flex-direction:column">
                    <label class="add_label" for="add_domaines">Nom du domaine</label>
                    <select class="add_select" id="add_domaines" name="add_domaines" onchange="updateServices()">
                        <option selected>Choisir le domaine</option>
                        <?php foreach($select_noms as $select_nom): ?>
                            <option value="<?= $select_nom->nomDomaines ?>"> <?= $select_nom->nomDomaines ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <label class="add_label" for="add_nomService">Nom du service</label>
            <input class="add_input" type="text" name="add_nomService" required>

            <label class="add_label" for="add_descriService">Description du service</label>
            <textarea placeholder="" cols="65" rows="10" type="text" name="add_descriService" required></textarea>

            <label class="add_label" for="add_fsdumService">Code FSDUM du Service</label>
            <input class="add_input" type="text" name="add_fsdumService" required>

            <button class="btn_ajout" type="submit" name="formsend_service">Ajouter le Service</button>

            <?php if (isset($error_message)) : ?>
                        <p class="msg"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if (isset($message)) : ?>
                        <p class="msg"><?php echo $message; ?></p>
            <?php endif; ?>
        </form>



    </div>

    <div id="add_domainesSection" style="display: none;">

        <form method="post" class="add_form" enctype="multipart/form-data">

            <h2> Ajouter un Domaine </h2>

            <label class="add_label" for="addNomDomaines">Nom du Domaine :</label>
            <input class="add_input" type="text" id="addNomDomaines" name="nomDomaines_add" required>

            <label class="add_label" for="addDescriDomaines">Description du Domaine :</label>
            <textarea id="addDescriDomaines" name="descriDomaines_add" required cols="65" rows="10" placeholder=""></textarea>

            <label class="add_label" for="addImageDomaines">Image du Domaine :</label>
            <input class="add_input_image" type="file" id="addImageDomaines" name="imageDomaines_add" accept="image/*">

            <button class="btn_ajout" type="submit" name="formsend_domaines">Ajouter le Domaine</button>

            <?php if (isset($error_message)) : ?>
                <p class="msg"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if (isset($message)) : ?>
                <p class="msg"><?php echo $message; ?></p>
            <?php endif; ?>

        </form>

    </div>





<script>


function showSection(section) {
    const sections = ['edit_services', 'edit_domaines', 'add_services', 'add_domaines'];
    const buttons = {
        'edit_services': 'Modifier un service',
        'edit_domaines': 'Modifier un domaine',
        'add_services': 'Ajouter un service',
        'add_domaines': 'Ajouter un domaine'
    };

    sections.forEach(sec => {
        const sectionElement = document.getElementById(`${sec}Section`);
        const buttonElement = document.getElementById(`${sec}_btn`);
        if (sec === section) {
            sectionElement.style.display = 'flex';
            buttonElement.style.backgroundColor = '#96CD32';
            document.getElementById('headerTitle').innerText = buttons[sec];
        } else {
            sectionElement.style.display = 'none';
            buttonElement.style.backgroundColor = '';
        }
    });
}

window.onload = function() {
    showSection('add_services');
};

            
 function filterTable() {
    const filterAgencyValue = document.getElementById("filterAgency").value;
    const filterDomainValue = document.getElementById("filterDomain").value;
    const rows = document.querySelectorAll("table tr");
    const tableHeader = document.querySelector(".table-header");
    let rowCount = 0;

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const agencyCell = row.cells[1].textContent;
        const domainCell = row.cells[4].textContent;

        const agencyPassesFilter = filterAgencyValue === "all" || agencyCell === filterAgencyValue;
        const domainPassesFilter = filterDomainValue === "all" || domainCell === filterDomainValue;

        if (agencyPassesFilter && domainPassesFilter) {
            row.style.display = "table-row";
            rowCount++;
        } else {
            row.style.display = "none";
        }
    }

    const noResultsElement = document.getElementById("noResults");

    if (rowCount > 0) {
        tableHeader.style.display = "table-row";
        noResultsElement.style.display = "none";
    } else {
        tableHeader.style.display = "none";
        noResultsElement.style.display = "block";
    }
}


function openEditServiceModal(buttonElement) {
    const id_service = buttonElement.getAttribute("data-id");
    const nomAgence = buttonElement.getAttribute("data-nomAgence");
    const nomService = buttonElement.getAttribute("data-nomService");
    const descriService = buttonElement.getAttribute("data-descriService");
    const fsdumService = buttonElement.getAttribute("data-fsdumService");
    const domaines = buttonElement.getAttribute("data-domaines");

    document.getElementById("editServiceId").value = id_service;
    document.getElementById("editNomAgence").value = nomAgence;
    document.getElementById("editNomService").value = nomService;
    document.getElementById("editDescriService").value = descriService;
    document.getElementById("editDomaines").value = domaines;
    document.getElementById("editfsdumService").value = fsdumService;

    openModal('editServiceModal');
}

function openDeleteServiceModal(id_service) {
    document.getElementById("deleteServiceId").value = id_service;
    openModal('deleteServiceModal');
}


function openEditDomainModal(buttonElement) {
    const id_domaines = buttonElement.getAttribute("data-id_domaines");
    const nomDomaines = buttonElement.getAttribute("data-nomDomaines");
    const descriDomaines = buttonElement.getAttribute("data-descriDomaines");

    document.getElementById("editDomainId").value = id_domaines;
    document.getElementById("editNomDomaines").value = nomDomaines;
    document.getElementById("editDescriDomaine").value = descriDomaines;

    openModal('editDomainModal');
}

function openDeleteDomainModal(id_domaines) {
    document.getElementById("deleteDomainId").value = id_domaines;
    openModal('deleteDomainModal');
}

function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}


    </script>
</body>
</html>