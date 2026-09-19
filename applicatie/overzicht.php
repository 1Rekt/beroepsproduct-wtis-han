<?php
    include 'setup.php';

    $flightnumber = isset($_GET['vluchtnummer']) ? $_GET['vluchtnummer'] : NULL;

    if(!$flightnumber){
        setError('Geen vluchtnummer opgegeven');
    }
    if(!is_numeric($flightnumber) && !empty($flightnumber)){
        setError('Vluchtnummer moet een getal zijn');
    }
    if(isset($_SESSION['errors'])){
        header('Location: /index.php');
    }

    $flight = dbQuery('SELECT * 
                        FROM Vlucht v 
                        INNER JOIN Luchthaven l ON l.luchthavencode = v.bestemming 
                        INNER JOIN IncheckenVlucht iv on iv.vluchtnummer = v.vluchtnummer
                        where v.vluchtnummer = :flightnumber and v.vertrektijd > :departure_time',[
        ':flightnumber' => $flightnumber,
        ':departure_time' => date('Y-m-d h:i:s'),
    ]);
    
    if(empty($flight)){
        setError('Geen toekomstige vlucht gevonden op basis van vluchtnumer');
    }

?>
<!DOCTYPE html>
<html lang="en">
    <?= loadHead('Overzicht') ?>
    <body>
        <?= loadMenu() ?>
        <main>

            <?php if(checkErrors()){ ?>
                <?= showErrors(); ?>
                <section class="goback-section">
                    <div class="container">
                        <a href="/">Ga terug</a>
                    </div>
                </section>
            <?php }else{ ?>
                <?= showFlightData($flight) ?>
            <?php } ?>

        </main>
        <?= loadFooter() ?>
    </body>
</html>