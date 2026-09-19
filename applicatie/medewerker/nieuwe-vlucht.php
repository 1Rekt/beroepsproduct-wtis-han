<?php
    include '../setup.php';
    roleCheck('medewerker');

    $destinations = dbQuery('SELECT * FROM Luchthaven',[]);
    $gates = dbQuery('SELECT * FROM Gate',[]);
    $companies = dbQuery('SELECT * FROM Maatschappij',[]);
    $counters = dbQuery('SELECT * FROM Balie',[]);

    if(isset($_POST['bestemming'])){

        if(isset($_POST['bestemming'])){
            $destination = $_POST['bestemming'];
        }else{
            setMessage('warning','Geen geldige bestemming meegegeven');
        }

        if(isset($_POST['gatecode'])){
            $gate = $_POST['gatecode'];
        }else{
            setMessage('warning','Geen geldige gate meegegeven');
        }

        if(isset($_POST['max_aantal']) && is_numeric($_POST['max_aantal']) && $_POST['max_aantal'] > 0 && $_POST['max_aantal'] < 1000){
            $max_persons = $_POST['max_aantal'];
        }else{
            setMessage('warning','Geen geldige aantal personen meegegeven');
        }

        if(isset($_POST['max_gewicht_pp']) && is_numeric($_POST['max_gewicht_pp']) && $_POST['max_gewicht_pp'] > 0 && $_POST['max_gewicht_pp'] < 10000){
            $max_weight_pp = $_POST['max_gewicht_pp'];
        }else{
            setMessage('warning','Geen geldige max gewicht pp meegegeven');
        }

        if(isset($_POST['max_totaalgewicht']) && is_numeric($_POST['max_totaalgewicht']) && $_POST['max_totaalgewicht'] > 0 && $_POST['max_totaalgewicht'] < 10000){
            $max_totalweight = $_POST['max_totaalgewicht'];
        }else{
            setMessage('warning','Geen geldige max totaal gewicht meegegeven');
        }

        if(isset($_POST['vertrektijd']) && $_POST['vertrektijd']){
            $departure_time = date('Y-m-d H:i:s',strtotime($_POST['vertrektijd']));
        }else{
            setMessage('warning','Geen geldige vertrektijd meegegeven');
        }

        if(isset($_POST['maatschappijcode']) && $_POST['maatschappijcode']){
            $company_code = $_POST['maatschappijcode'];
        }else{
            setMessage('warning','Geen geldige maatschappijcode meegegeven');
        }

        if(isset($_POST['balienummer']) && $_POST['balienummer']){
            $counter_number = $_POST['balienummer'];
        }else{
            setMessage('warning','Geen geldige balienummer meegegeven');
        }

        if(!checkMessages()){
            //get flightnumber
            $flightnumber = dbQuery('SELECT MAX(vluchtnummer) AS max FROM vlucht',[]);
            $flightnumber = $flightnumber['max'] + 1;
            //insert
            dbInsert('INSERT INTO vlucht (vluchtnummer, bestemming, gatecode, max_aantal, max_gewicht_pp, max_totaalgewicht, vertrektijd, maatschappijcode)
            VALUES (:flightnumber,:destination,:gate,:max_persons,:max_weight_pp,:max_totalweight,:departure_time,:company_code)',[
                ':flightnumber' => $flightnumber,
                ':destination' => $destination,
                ':gate' => $gate,
                ':max_persons' => $max_persons,
                ':max_weight_pp' => $max_weight_pp,
                ':max_totalweight' => $max_totalweight,
                ':departure_time' => $departure_time,
                ':company_code' => $company_code,
            ]);

            dbInsert('INSERT INTO incheckenVlucht (vluchtnummer, balienummer)
            VALUES (:flightnumber,:counter_number)',[
                ':flightnumber' => $flightnumber,
                ':counter_number' => $counter_number,
            ]);

            setMessage('success','Vlucht aangemaakt!');
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <?= loadHead('Nieuwe vlucht') ?>
    <body>
        <?= loadMenu() ?>
        <main>
            <?= showMessages(); ?>

            <section class="page-header">
                <div class="container">
                    <h1>Nieuwe vlucht</h1>
                </div>
            </section>
            <div class="create-form">
                <div class="container">
                    <form method="POST" action="">

                        <div class="form-item">
                            <label for="bestemming">Naar:</label>
                            <select type="text" name="bestemming" id="bestemming">
                                <?php foreach($destinations as $destination){ ?>
                                    <option value="<?= $destination['luchthavencode'] ?>"><?= $destination['naam'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-item">
                            <label for="gatecode">Gate:</label>
                            <select type="text" name="gatecode" id="gatecode">
                                <?php foreach($gates as $gate){ ?>
                                    <option value="<?= $gate['gatecode'] ?>"><?= $gate['gatecode'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-item">
                            <label for="max_aantal">Max aantal personen:</label>
                            <input type="number" name="max_aantal" id="max_aantal">
                        </div>

                        <div class="form-item">
                            <label for="max_gewicht_pp">Max gewicht pp:</label>
                            <input type="number" name="max_gewicht_pp" id="max_gewicht_pp">
                        </div>

                        <div class="form-item">
                            <label for="max_totaalgewicht">Max totaalgewicht:</label>
                            <input type="number" name="max_totaalgewicht" id="max_totaalgewicht">
                        </div>

                        <div class="form-item">
                            <label for="vertrektijd">Vertrektijd:</label>
                            <input type="datetime-local" name="vertrektijd" id="vertrektijd">
                        </div>

                        <div class="form-item">
                            <label for="maatschappijcode">Maatschappij:</label>
                            <select type="text" name="maatschappijcode" id="maatschappijcode">
                                <?php foreach($companies as $company){ ?>
                                    <option value="<?= $company['maatschappijcode'] ?>"><?= $company['naam'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-item">
                            <label for="balienummer">Balienummer:</label>
                            <select type="text" name="balienummer" id="balienummer">
                                <?php foreach($counters as $counter){ ?>
                                    <option value="<?= $counter['balienummer'] ?>"><?= $counter['balienummer'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <button class="btn btn-green" type="submit">Aanmaken</button>
                    </form>
                </div>
            </div>
        </main>
        <?= loadFooter() ?>
    </body>
</html>