<?php
    include '../setup.php';
    roleCheck('passagier');

    $passengernumber = isset($_GET['passagiernummer']) ? $_GET['passagiernummer'] : NULL;

    if(!$passengernumber){
        setError('Geen passagiernummer opgegeven');
    }
    if(!is_numeric($passengernumber) && !empty($passengernumber)){
        setError('Passagiernummer moet een getal zijn');
    }
    if(isset($_SESSION['errors'])){
        header('Location: /passagier/index.php');
    }
    
    $passenger = dbQuery('SELECT * FROM Passagier where passagiernummer = :passengernumber',[
        ':passengernumber' => $passengernumber,
    ]);

    if(empty($passenger)){
        setError('Geen passagier gevonden met het passagiersnummer');
    }else{
        $flightnumber = $passenger['vluchtnummer'];
        $flight = dbQuery('SELECT * 
                FROM Vlucht v 
                INNER JOIN Luchthaven l ON l.luchthavencode = v.bestemming 
                INNER JOIN IncheckenVlucht iv on iv.vluchtnummer = v.vluchtnummer
                where v.vluchtnummer = :flightnumber',[
        ':flightnumber' => $flightnumber,
        ]);
    }

    if(isset($_POST['gewicht'])){
        $weight = $_POST['gewicht'];
        //check waarde gewicht
        if(empty($weight) || !is_numeric($weight)){
            setMessage('warning','Geen geldig gewicht meegegeven!');
        }else{
            $objectFollowNumber = dbQuery('select max(objectvolgnummer) from bagageobject where passagiernummer = :passengernumber',[
                ':passengernumber' => $passengernumber,
            ]);
            $objectFollowNumber = $objectFollowNumber[''] + 1;
    
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

    $bagage = dbQuery('SELECT gewicht FROM BagageObject where passagiernummer = :passengernumber',[
        ':passengernumber' => $passengernumber,
    ]);
    if(!isset($bagage[0]) && !empty($bagage)){
        $bagage[0] = $bagage;
        unset($bagage['gewicht']);
    }
    // dd($bagage);

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
                        <a href="/">Ga terug</a>
                    </div>
                </section>
            <?php }else{ ?>

                <?php if(checkMessages()){ ?>
                    <?= showMessages(); ?>
                <?php } ?>

                <?= showFlightData($flight, $passenger) ?>

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
                                <button class="btn btn-green" type="submit">Inchecken</button>
                            </form>
                        </div>
                    </div>
                </section>

                <?php if(count($bagage) > 0){ ?>
                    <section class="passenger-luggage">
                        <div class="container">
                            <div class="header">
                                <h2>Ingecheckte bagage</h2>
                            </div>
                            <div class="body">
                                <?php foreach($bagage as $key => $bagageObject){ ?>
                                    Koffer <?= $key + 1 ?>: <?= $bagageObject['gewicht'] ?>kg<br />
                                <?php } ?>
                            </div>
                        </div>
                    </section>
                <?php } ?>
            <?php } ?>

        </main>
        <?= loadFooter() ?>
    </body>
</html>