<?php


require "../database/config.php";

session_start();

includeFileBasedOnRole();

ini_set('display_errors', '0');
error_reporting(E_ALL);
    

$bdd = connect();

$stmt = $bdd->prepare("SELECT * from immersions");
$stmt->execute();
$immersions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$userRole = "XIB_ADMINISTRATEUR"

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mon Immersion - Liste des Immersions</title>
    <link rel="stylesheet" href="../css/own_immersions.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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


<h1> Liste des Immersions </h1>

<div class="bouton_export">
    <svg id="exportButton"width="340" height="61" viewBox="0 0 340 61" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="340" height="61" rx="10" fill="#41A57D"/>
    <path d="M26.1 39.5V22.148H37.572L37.56 24.656H29.04V29.516H36.564V31.988H29.016V36.956L37.74 36.968V39.5H26.1ZM52.2017 39.5H49.1057L45.8417 35.084L42.5897 39.5H39.4937L44.2577 33.044L39.8897 27.092H42.9857L45.8537 30.968L48.7217 27.092H51.8297L47.4257 33.044L52.2017 39.5ZM54.4138 43.364V27.092H57.3298L57.3538 29.048C57.5138 28.776 57.7178 28.508 57.9658 28.244C58.2218 27.98 58.5138 27.744 58.8418 27.536C59.1778 27.328 59.5498 27.164 59.9578 27.044C60.3658 26.916 60.8138 26.852 61.3018 26.852C62.2538 26.852 63.0978 27.08 63.8338 27.536C64.5698 27.984 65.1458 28.676 65.5618 29.612C65.9858 30.548 66.1978 31.732 66.1978 33.164C66.1978 34.628 65.9818 35.848 65.5498 36.824C65.1178 37.8 64.5138 38.532 63.7378 39.02C62.9618 39.5 62.0618 39.74 61.0378 39.74C60.5658 39.74 60.1338 39.676 59.7418 39.548C59.3578 39.42 59.0138 39.244 58.7098 39.02C58.4058 38.796 58.1378 38.552 57.9058 38.288C57.6818 38.024 57.4938 37.748 57.3418 37.46V43.364H54.4138ZM60.3898 37.556C61.2698 37.556 61.9978 37.208 62.5738 36.512C63.1498 35.808 63.4378 34.708 63.4378 33.212C63.4378 31.916 63.1658 30.896 62.6218 30.152C62.0858 29.4 61.3418 29.024 60.3898 29.024C59.3658 29.024 58.5978 29.42 58.0858 30.212C57.5738 30.996 57.3178 31.996 57.3178 33.212C57.3178 33.996 57.4298 34.72 57.6538 35.384C57.8858 36.04 58.2258 36.568 58.6738 36.968C59.1298 37.36 59.7018 37.556 60.3898 37.556ZM73.929 39.74C72.745 39.74 71.713 39.488 70.833 38.984C69.953 38.472 69.269 37.74 68.781 36.788C68.301 35.836 68.061 34.692 68.061 33.356C68.061 32.06 68.293 30.924 68.757 29.948C69.229 28.972 69.905 28.212 70.785 27.668C71.665 27.124 72.717 26.852 73.941 26.852C75.125 26.852 76.153 27.112 77.025 27.632C77.897 28.152 78.569 28.896 79.041 29.864C79.521 30.832 79.761 31.996 79.761 33.356C79.761 34.612 79.533 35.72 79.077 36.68C78.629 37.632 77.969 38.38 77.097 38.924C76.233 39.468 75.177 39.74 73.929 39.74ZM73.941 37.52C74.613 37.52 75.165 37.336 75.597 36.968C76.037 36.6 76.361 36.096 76.569 35.456C76.785 34.816 76.893 34.088 76.893 33.272C76.893 32.512 76.797 31.812 76.605 31.172C76.421 30.532 76.109 30.02 75.669 29.636C75.237 29.244 74.661 29.048 73.941 29.048C73.261 29.048 72.697 29.228 72.249 29.588C71.809 29.94 71.477 30.436 71.253 31.076C71.037 31.708 70.929 32.44 70.929 33.272C70.929 34.024 71.025 34.724 71.217 35.372C71.417 36.012 71.737 36.532 72.177 36.932C72.617 37.324 73.205 37.52 73.941 37.52ZM82.3513 39.5V27.092H85.1473V29.78C85.3473 29.164 85.6313 28.64 85.9993 28.208C86.3753 27.768 86.8153 27.432 87.3193 27.2C87.8233 26.968 88.3713 26.852 88.9633 26.852C89.1073 26.852 89.2473 26.86 89.3833 26.876C89.5193 26.892 89.6193 26.92 89.6833 26.96V29.756C89.5953 29.716 89.4793 29.688 89.3353 29.672C89.1993 29.656 89.0793 29.648 88.9753 29.648C88.3753 29.6 87.8433 29.624 87.3793 29.72C86.9233 29.816 86.5353 29.98 86.2153 30.212C85.9033 30.436 85.6673 30.724 85.5073 31.076C85.3473 31.42 85.2673 31.824 85.2673 32.288V39.5H82.3513ZM99.1573 29.156H96.3853L96.3973 36.272C96.3973 36.64 96.4373 36.912 96.5173 37.088C96.6053 37.256 96.7413 37.368 96.9253 37.424C97.1173 37.472 97.3733 37.496 97.6933 37.496H99.2173V39.344C99.0573 39.408 98.8133 39.464 98.4853 39.512C98.1653 39.568 97.7333 39.596 97.1893 39.596C96.2053 39.596 95.4413 39.468 94.8973 39.212C94.3613 38.948 93.9893 38.576 93.7813 38.096C93.5733 37.616 93.4693 37.044 93.4693 36.38V29.156H91.4533V27.092H93.5533L94.2853 23.408H96.3853V27.08H99.1573V29.156ZM103.956 33.968C103.948 34.672 104.06 35.296 104.292 35.84C104.524 36.384 104.868 36.812 105.324 37.124C105.78 37.428 106.344 37.58 107.016 37.58C107.704 37.58 108.3 37.428 108.804 37.124C109.316 36.812 109.656 36.34 109.824 35.708H112.5C112.356 36.572 112.012 37.304 111.468 37.904C110.924 38.504 110.264 38.96 109.488 39.272C108.72 39.584 107.92 39.74 107.088 39.74C105.888 39.74 104.832 39.488 103.92 38.984C103.016 38.472 102.308 37.744 101.796 36.8C101.292 35.856 101.04 34.732 101.04 33.428C101.04 32.156 101.272 31.028 101.736 30.044C102.2 29.052 102.868 28.272 103.74 27.704C104.62 27.136 105.676 26.852 106.908 26.852C108.132 26.852 109.16 27.112 109.992 27.632C110.832 28.152 111.464 28.88 111.888 29.816C112.32 30.744 112.536 31.832 112.536 33.08V33.968H103.956ZM103.956 32.156H109.776C109.776 31.556 109.672 31.016 109.464 30.536C109.256 30.048 108.936 29.664 108.504 29.384C108.08 29.096 107.544 28.952 106.896 28.952C106.256 28.952 105.716 29.108 105.276 29.42C104.836 29.732 104.504 30.136 104.28 30.632C104.056 31.12 103.948 31.628 103.956 32.156ZM115.258 39.5V27.092H118.054V29.78C118.254 29.164 118.538 28.64 118.906 28.208C119.282 27.768 119.722 27.432 120.226 27.2C120.73 26.968 121.278 26.852 121.87 26.852C122.014 26.852 122.154 26.86 122.29 26.876C122.426 26.892 122.526 26.92 122.59 26.96V29.756C122.502 29.716 122.386 29.688 122.242 29.672C122.106 29.656 121.986 29.648 121.882 29.648C121.282 29.6 120.75 29.624 120.286 29.72C119.83 29.816 119.442 29.98 119.122 30.212C118.81 30.436 118.574 30.724 118.414 31.076C118.254 31.42 118.174 31.824 118.174 32.288V39.5H115.258ZM140.224 27.092L135.652 39.5H133.348L128.764 27.092H131.44L134.416 35.78H134.572L137.536 27.092H140.224ZM144.128 33.968C144.12 34.672 144.232 35.296 144.464 35.84C144.696 36.384 145.04 36.812 145.496 37.124C145.952 37.428 146.516 37.58 147.188 37.58C147.876 37.58 148.472 37.428 148.976 37.124C149.488 36.812 149.828 36.34 149.996 35.708H152.672C152.528 36.572 152.184 37.304 151.64 37.904C151.096 38.504 150.436 38.96 149.66 39.272C148.892 39.584 148.092 39.74 147.26 39.74C146.06 39.74 145.004 39.488 144.092 38.984C143.188 38.472 142.48 37.744 141.968 36.8C141.464 35.856 141.212 34.732 141.212 33.428C141.212 32.156 141.444 31.028 141.908 30.044C142.372 29.052 143.04 28.272 143.912 27.704C144.792 27.136 145.848 26.852 147.08 26.852C148.304 26.852 149.332 27.112 150.164 27.632C151.004 28.152 151.636 28.88 152.06 29.816C152.492 30.744 152.708 31.832 152.708 33.08V33.968H144.128ZM144.128 32.156H149.948C149.948 31.556 149.844 31.016 149.636 30.536C149.428 30.048 149.108 29.664 148.676 29.384C148.252 29.096 147.716 28.952 147.068 28.952C146.428 28.952 145.888 29.108 145.448 29.42C145.008 29.732 144.676 30.136 144.452 30.632C144.228 31.12 144.12 31.628 144.128 32.156ZM155.429 39.5V27.092H158.225V29.78C158.425 29.164 158.709 28.64 159.077 28.208C159.453 27.768 159.893 27.432 160.397 27.2C160.901 26.968 161.449 26.852 162.041 26.852C162.185 26.852 162.325 26.86 162.461 26.876C162.597 26.892 162.697 26.92 162.761 26.96V29.756C162.673 29.716 162.557 29.688 162.413 29.672C162.277 29.656 162.157 29.648 162.053 29.648C161.453 29.6 160.921 29.624 160.457 29.72C160.001 29.816 159.613 29.98 159.293 30.212C158.981 30.436 158.745 30.724 158.585 31.076C158.425 31.42 158.345 31.824 158.345 32.288V39.5H155.429ZM169.613 39.74C168.749 39.74 167.929 39.604 167.153 39.332C166.385 39.052 165.737 38.616 165.209 38.024C164.689 37.432 164.361 36.668 164.225 35.732H166.793C166.905 36.188 167.101 36.56 167.381 36.848C167.669 37.136 168.009 37.348 168.401 37.484C168.793 37.612 169.193 37.676 169.601 37.676C170.345 37.676 170.945 37.548 171.401 37.292C171.865 37.036 172.097 36.64 172.097 36.104C172.097 35.712 171.969 35.4 171.713 35.168C171.457 34.936 171.049 34.76 170.489 34.64L168.125 34.1C167.077 33.868 166.233 33.484 165.593 32.948C164.961 32.412 164.641 31.656 164.633 30.68C164.625 29.944 164.809 29.288 165.185 28.712C165.561 28.136 166.121 27.684 166.865 27.356C167.609 27.02 168.529 26.852 169.625 26.852C171.073 26.852 172.233 27.176 173.105 27.824C173.977 28.464 174.425 29.38 174.449 30.572H171.965C171.877 30.036 171.625 29.62 171.209 29.324C170.793 29.02 170.253 28.868 169.589 28.868C168.901 28.868 168.337 29 167.897 29.264C167.457 29.528 167.237 29.932 167.237 30.476C167.237 30.852 167.405 31.148 167.741 31.364C168.077 31.58 168.581 31.764 169.253 31.916L171.485 32.456C172.125 32.616 172.649 32.832 173.057 33.104C173.465 33.376 173.785 33.676 174.017 34.004C174.249 34.324 174.409 34.656 174.497 35C174.593 35.336 174.641 35.648 174.641 35.936C174.641 36.736 174.433 37.42 174.017 37.988C173.609 38.548 173.029 38.98 172.277 39.284C171.525 39.588 170.637 39.74 169.613 39.74ZM183.014 39.5V22.148H194.486L194.474 24.656H185.954V29.516H193.478V31.988H185.93V36.956L194.654 36.968V39.5H183.014ZM209.116 39.5H206.02L202.756 35.084L199.504 39.5H196.408L201.172 33.044L196.804 27.092H199.9L202.768 30.968L205.636 27.092H208.744L204.34 33.044L209.116 39.5ZM215.913 26.852C216.881 26.852 217.733 27.044 218.469 27.428C219.205 27.812 219.793 28.344 220.233 29.024C220.681 29.704 220.949 30.476 221.037 31.34H218.541C218.485 30.964 218.349 30.608 218.133 30.272C217.917 29.928 217.625 29.652 217.257 29.444C216.889 29.228 216.445 29.12 215.925 29.12C214.997 29.12 214.241 29.464 213.657 30.152C213.081 30.84 212.793 31.896 212.793 33.32C212.793 34.632 213.069 35.676 213.621 36.452C214.173 37.228 214.961 37.616 215.985 37.616C216.497 37.616 216.933 37.504 217.293 37.28C217.661 37.048 217.949 36.764 218.157 36.428C218.373 36.084 218.513 35.74 218.577 35.396H221.001C220.929 36.236 220.665 36.984 220.209 37.64C219.761 38.296 219.169 38.812 218.433 39.188C217.705 39.556 216.865 39.74 215.913 39.74C214.777 39.74 213.765 39.492 212.877 38.996C211.997 38.492 211.305 37.764 210.801 36.812C210.297 35.852 210.045 34.696 210.045 33.344C210.045 32.056 210.281 30.928 210.753 29.96C211.225 28.984 211.901 28.224 212.781 27.68C213.661 27.128 214.705 26.852 215.913 26.852ZM226.018 33.968C226.01 34.672 226.122 35.296 226.354 35.84C226.586 36.384 226.93 36.812 227.386 37.124C227.842 37.428 228.406 37.58 229.078 37.58C229.766 37.58 230.362 37.428 230.866 37.124C231.378 36.812 231.718 36.34 231.886 35.708H234.562C234.418 36.572 234.074 37.304 233.53 37.904C232.986 38.504 232.326 38.96 231.55 39.272C230.782 39.584 229.982 39.74 229.15 39.74C227.95 39.74 226.894 39.488 225.982 38.984C225.078 38.472 224.37 37.744 223.858 36.8C223.354 35.856 223.102 34.732 223.102 33.428C223.102 32.156 223.334 31.028 223.798 30.044C224.262 29.052 224.93 28.272 225.802 27.704C226.682 27.136 227.738 26.852 228.97 26.852C230.194 26.852 231.222 27.112 232.054 27.632C232.894 28.152 233.526 28.88 233.95 29.816C234.382 30.744 234.598 31.832 234.598 33.08V33.968H226.018ZM226.018 32.156H231.838C231.838 31.556 231.734 31.016 231.526 30.536C231.318 30.048 230.998 29.664 230.566 29.384C230.142 29.096 229.606 28.952 228.958 28.952C228.318 28.952 227.778 29.108 227.338 29.42C226.898 29.732 226.566 30.136 226.342 30.632C226.118 31.12 226.01 31.628 226.018 32.156ZM240.896 39.68C240.096 39.68 239.456 39.58 238.976 39.38C238.496 39.18 238.136 38.912 237.896 38.576C237.656 38.24 237.5 37.864 237.428 37.448C237.356 37.024 237.32 36.592 237.32 36.152V21.668H240.176V35.816C240.176 36.352 240.284 36.768 240.5 37.064C240.724 37.36 241.084 37.532 241.58 37.58L242.204 37.604V39.44C241.988 39.504 241.768 39.56 241.544 39.608C241.32 39.656 241.104 39.68 240.896 39.68Z" fill="white"/>
    <path fill-rule="evenodd" clip-rule="evenodd" d="M303 12.5V20.5H316V22.5H303V29.5H316V14.5C316 13.395 315.164 12.5 314.133 12.5H303ZM303 31.5H316V38.5H303V31.5ZM303 40.5H316V46.5C316 47.605 315.164 48.5 314.133 48.5H303V40.5ZM301 40.5V48.5H289.867C288.836 48.5 288 47.605 288 46.5V40.5H301ZM301 20.5H288V14.5C288 13.395 288.836 12.5 289.867 12.5H301V20.5ZM297.052 22.5V29.5H301V22.5H297.052ZM297.052 31.5V38.5H301V31.5H297.052ZM280 23.5C280 23.2348 280.105 22.9804 280.293 22.7929C280.48 22.6054 280.735 22.5 281 22.5H294.158C294.423 22.5 294.678 22.6054 294.865 22.7929C295.053 22.9804 295.158 23.2348 295.158 23.5V37.5C295.158 37.7652 295.053 38.0196 294.865 38.2071C294.678 38.3946 294.423 38.5 294.158 38.5H281C280.735 38.5 280.48 38.3946 280.293 38.2071C280.105 38.0196 280 37.7652 280 37.5V23.5ZM283.607 25.5H285.867L287.701 29.254L289.64 25.5H291.752L288.842 30.5L291.818 35.5H289.59L287.591 31.57L285.601 35.5H283.34L286.364 30.482L283.607 25.5Z" fill="white"/>
    </svg>
</div>

    <table>
        <tr>
            <th>NNI</th>
            <th>NOM Prénom</th>
            <th>Agence</th>
            <th>Service</th>
            <th>Date</th>
            <th>Heure de début</th>
            <th>Heure de fin</th>
            <th>Feedback</th>
            <th>Statut</th>
            <?php if ($userRole === "XIB_MANAGER" || $userRole === "XIB_ADMINISTRARTEUR") :  ?>
            <th>Etat</th>
            <?php endif; ?>
        </tr>

        <?php foreach ($immersions as $immersion) : ?>
        <tr>
            <td><?= $immersion['nni']; ?></td>
            <td><?= $immersion['pseudo'];?></td>
            <td><?= $immersion['nomAgence']; ?></td>
            <td><?= $immersion['nomService']; ?></td>
            <td>
            <?php 
                $dateParts = explode("-", $immersion['date_immersion']);
                $formattedDate = $dateParts[2] . "-" . $dateParts[1] . "-" . $dateParts[0];
                echo $formattedDate;
            ?>
            </td>
            <td>
              <?php
              $debut = date_create_from_format('H:i:s', $immersion['heure_debut']);
              echo $formatted_debut = $debut ? $debut->format('H\hi') : 'Non spécifiée';
              ?>
          </td>
          <td>
              <?php
              $fin = date_create_from_format('H:i:s', $immersion['heure_fin']);
              echo $formatted_fin = $fin ? $fin->format('H\hi') : 'Non spécifiée';
              ?>
          </td>

            <td>
                <?php if (empty($immersion['feedback'])) : ?>
                    <span class="aucune"> L'immersions n'a pas encore été réalisée </span>
                <?php else : ?>
                    <span class="feedback"><?= $immersion['feedback']; ?></span>
                <?php endif; ?>
            </td>


            <td><?= $immersion['statut']; ?></td>

            <?php if ($userRole === "XIB_MANAGER" || $userRole === "XIB_ADMINISTRATEUR") : ?>
                <td>
                    <?php
                    $immersionId = $immersion['id_immersion'];
                    $approveUrl = "approve.php?id_immersion=$immersionId";
                    $rejectUrl = "reject.php?id_immersion=$immersionId";
                    $cancelUrl = "cancel.php?id_immersion=$immersionId";
                    ?>
                    
                    <?php if ($immersion['statut'] === "En Attente d'Approbation Managériale") : ?>
                        <a href="<?= $approveUrl; ?>">
                            <button class="yes">Approuver</button>
                        </a>
                        <a href="<?= $rejectUrl; ?>">
                            <button class="no">Rejeter</button>
                        </a>
                    <?php elseif ($immersion['statut'] === "Approuvée par le manager") : ?>
                        <a href="<?= $cancelUrl; ?>">
                            <button class="no">Annulation</button>
                        </a>
                        <?php elseif ($immersion['statut'] === "Refusée par le manager" || $immersion['statut'] === "Annulée par le manager" ) : ?>
                        <a href="<?= $approveUrl; ?>">
                            <button class="yes">Approuver</button>
                        </a>
                    <?php endif; ?>
                </td>
            <?php endif; ?>



        </tr>
        <?php endforeach; ?>
    </table>

    <script>





    document.getElementById("exportButton").addEventListener("click", function() {
        var exportData = [];
        exportData.push(["NNI", "NOM Prénom", "Site", "Service", "Date", "Heure Début", "Heure de fin","Feedback", "Statut"]);
        
        var tableRows = document.querySelectorAll("table tr");
        for (var i = 1; i < tableRows.length; i++) {
            var rowData = [];
            var cells = tableRows[i].querySelectorAll("td");
            
            rowData.push(cells[0].textContent);
            rowData.push(cells[1].textContent);
            rowData.push(cells[2].textContent);
            rowData.push(cells[3].textContent);
            rowData.push(cells[4].textContent);
            rowData.push(cells[5].textContent);
            rowData.push(cells[6].textContent);

            var feedbackCell = cells[7].querySelector(".feedback");
            var feedbackText = feedbackCell ? feedbackCell.textContent : "L'immersion n'a pas encore été réalisée";
            rowData.push(feedbackText);
            
            rowData.push(cells[8].textContent);
            
            exportData.push(rowData);
        }
        
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.aoa_to_sheet(exportData);
        XLSX.utils.book_append_sheet(wb, ws, "Données");

        XLSX.writeFile(wb, "Export_Immersions.xlsx");
    });
    
    
    document.querySelector('.bouton_export').addEventListener('mouseenter', function() {
    this.querySelector('rect').style.fill = "#96CD32";
    this.style.cursor = "pointer";
    });

    document.querySelector('.bouton_export').addEventListener('mouseleave', function() {
    this.querySelector('rect').style.fill = "#41A57D";
    this.style.cursor = "";
    });
</script>
  
</body>
</html>
