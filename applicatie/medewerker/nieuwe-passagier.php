<?php
    include '../setup.php';
    roleCheck('medewerker');

    if(isset($_POST['vluchtnummer'])){
        if(isset($_POST['naam'])){
            $name = $_POST['naam'];
        }else{
            setMessage('warning','Geen geldige naam meegegeven');
        }

        if(isset($_POST['vluchtnummer'])){
            $flightnumber = $_POST['vluchtnummer'];
        }else{
            setMessage('warning','Geen geldige vluchtnummer meegegeven');
        }

        if(isset($_POST['geslacht'])){
            $gender = $_POST['geslacht'];
        }else{
            setMessage('warning','Geen geldige geslacht meegegeven');
        }

        if(isset($_POST['stoel'])){
            $seat = $_POST['stoel'];
        }else{
            setMessage('warning','Geen geldige stoel meegegeven');
        }

        $flight = dbQuery('select * from Vlucht where vluchtnummer = :flightnumber',[
            ':flightnumber' => $flightnumber,
        ]);

        if(empty($flight)){
            setMessage('warning','Geen vlucht gevonden');
        }else{
            //check of max personen al is ingecheckt
            $checkedInPassengers = dbQuery('select count(*) from passagier where vluchtnummer = :flightnumber',[
                ':flightnumber' => $flightnumber,
            ]);
            $checkedInPassengers = $checkedInPassengers[''];

            if($checkedInPassengers >= $flight['max_aantal']){
                setMessage('warning','Maximaal aantal passagiers ingecheckt');
            }
        }

        if(!checkMessages()){
            $passengernumber = dbQuery('select MAX(passagiernummer) as max from passagier',[]);
            $passengernumber = $passengernumber['max'] + 1;

            $counter_number = dbQuery('select balienummer from incheckenVlucht where vluchtnummer = :flightnumber',[
                ':flightnumber' => $flightnumber,
            ]);
            $counter_number = $counter_number['balienummer'];

            $checkin_time = date('Y-m-d H:i:00');

            dbInsert('INSERT INTO Passagier (passagiernummer, naam, vluchtnummer, geslacht, balienummer, stoel, inchecktijdstip)
            VALUES (:passengernumber,:name,:flightnumber,:gender,:counter_number,:seat,:checkin_time)',[
                ':passengernumber' => $passengernumber,
                ':name' => $name,
                ':flightnumber' => $flightnumber,
                ':gender' => $gender,
                ':counter_number' => $counter_number,
                ':seat' => $seat,
                ':checkin_time' => $checkin_time,
            ]);

            setMessage('success','Passagier ingecheckt!');
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <?= loadHead('Nieuwe passagier') ?>
    <body>
        <?= loadMenu() ?>
        <main>
            <?= showMessages() ?>
            <section class="page-header">
                <div class="container">
                    <h1>Nieuwe passagier</h1>
                </div>
            </section>

            <div class="create-form">
                <div class="container">
                    <form method="POST" action="">

                        <div class="form-item">
                            <label for="naam">Naam:</label>
                            <input type="text" name="naam" id="naam">
                        </div>

                        <div class="form-item">
                            <label for="vluchtnummer">Vluchtnummer:</label>
                            <input type="number" name="vluchtnummer" id="vluchtnummer">
                        </div>

                        <div class="form-item">
                            <label for="geslacht">Geslacht:</label>
                            <select type="text" name="geslacht" id="geslacht">
                                <option value="M">Man</option>
                                <option value="V">Vrouw</option>
                                <option value="x">Iets anders</option>
                            </select>
                        </div>

                        <div class="form-item">
                            <label for="stoel">Stoel:</label>
                            <input type="text" name="stoel" id="stoel">
                        </div>

                        <button class="btn btn-green" type="submit">Aanmaken</button>
                    </form>
                </div>
            </div>
        </main>
        <?= loadFooter() ?>
    </body>
</html>