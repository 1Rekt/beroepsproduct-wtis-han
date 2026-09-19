<?php
    include '../setup.php';
    roleCheck('medewerker');

    $flightnumber = isset($_GET['vluchtnummer']) ? $_GET['vluchtnummer'] : NULL;

    if(!$flightnumber){
        setError('Geen vluchtnummer opgegeven');
    }
    if(!is_numeric($flightnumber) && !empty($flightnumber)){
        setError('Vluchtnummer moet een getal zijn');
    }

    if(isset($_SESSION['errors'])){
        header('Location: /medewerker/index.php');
    }

    $flight = dbQuery('SELECT * 
                        FROM Vlucht v 
                        INNER JOIN Luchthaven l ON l.luchthavencode = v.bestemming 
                        INNER JOIN IncheckenVlucht iv on iv.vluchtnummer = v.vluchtnummer
                        where v.vluchtnummer = :flightnumber',[
        ':flightnumber' => $flightnumber,
    ]);

    
    if(empty($flight)){
        setError('Geen vlucht gevonden');
    }else{
        if(isset($_POST['gewicht'])){
            $weight = $_POST['gewicht'];
            $passengernumber = $_POST['passagiernummer'];

            //check waarde gewicht
            if(empty($weight) || !is_numeric($weight)){
                setMessage('warning','Geen geldig gewicht meegegeven!');
            }else{
                //check of passagiernummer is ingecheckt
                $passenger = dbQuery('SELECT * FROM Passagier
                                    where vluchtnummer = :flightnumber
                                    and passagiernummer = :passengernumber',[
                    ':flightnumber' => $flightnumber,
                    ':passengernumber' => $passengernumber,
                ]);

                if(empty($passenger)){
                    setMessage('warning','Ingevulde passagiernummer is niet ingecheckt bij vlucht');
                }else{
                    //haal objectvolgnummer van passagier op.
                    $objectFollowNumber = dbQuery('select max(objectvolgnummer) from bagageobject where passagiernummer = :passengernumber',[
                        ':passengernumber' => $passengernumber,
                    ]);
                    $objectFollowNumber = $objectFollowNumber[''] + 1;

                    //moet minder dan 10 objectvolgnummer hebben
                    if($objectFollowNumber < 10){
                        $checkedInWeight = dbQuery('SELECT SUM(gewicht)
                                FROM BagageObject b
                                LEFT OUTER JOIN passagier p ON b.passagiernummer = p.passagiernummer
                                WHERE vluchtnummer = :flightnumber',[
                            ':flightnumber' => $flightnumber,
                        ]);
                        $checkedInWeight = $checkedInWeight[''];

                        if($checkedInWeight + $weight <= $flight['max_totaalgewicht']){
                            //check of gewicht pp overschrijft
                            $passengerWeight = dbQuery('SELECT SUM(gewicht)
                                    FROM BagageObject
                                    WHERE passagiernummer = :passengernumber',[
                                ':passengernumber' => $passengernumber,
                            ]);
                            $passengerWeight = $passengerWeight[''];

                            if($passengerWeight + $weight <= $flight['max_gewicht_pp']){
                                dbInsert('INSERT INTO BagageObject (passagiernummer, objectvolgnummer, gewicht)
                                VALUES (:passengernumber,:objectFollowNumber,:weight)',[
                                    ':passengernumber' => $passengernumber,
                                    ':objectFollowNumber' => $objectFollowNumber,
                                    ':weight' => $weight,
                                ]);
                                setMessage('success','Koffer ingecheckt!');
                            }else{
                                setMessage('warning','Je mag nog maar '. $flight['max_gewicht_pp'] - $passengerWeight.'kg meenemen');
                            }
                        }else{
                            setMessage('warning','Gewicht overschrijft het maximale gewicht van de vlucht!');
                        }
                    }else{
                        setMessage('warning','Maximaal aantal van 10 koffers bereikt');
                    }
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <?= loadHead('Inchecken') ?>
    <body>
        <?= loadMenu() ?>
        <main>
        <?php if(checkErrors()){ ?>
                <?= showErrors(); ?>
                <section class="goback-section">
                    <div class="container">
                        <a href="/medewerker/index.php">Ga terug</a>
                    </div>
                </section>
            <?php }else{ ?>
                <?php if(checkMessages()){ ?>
                    <?= showMessages(); ?>
                <?php } ?>

                <?= showFlightData($flight) ?>

                <section class="luggage-checkin">
                    <div class="container">
                        <div class="header">
                            <h2>Bagage inchecken</h2>
                        </div>
                        <div class="body">
                            <form method="POST" action="">
                                <div class="form-item">
                                    <label for="gewicht">Gewicht:</label>
                                    <input type="number" name="gewicht" id="gewicht" step=".01">
                                </div>
                                <div class="form-item">
                                    <label for="passagiernummer">Passagiernummer:</label>
                                    <input type="number" name="passagiernummer" id="passagiernummer">
                                </div>
                                <button class="btn btn-green" type="submit">Inchecken</button>
                            </form>
                        </div>
                    </div>
                </section>
            <?php } ?>
            
        </main>
        <?= loadFooter() ?>
    </body>
</html>