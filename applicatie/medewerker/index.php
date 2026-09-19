<?php
    include '../setup.php';
    roleCheck('medewerker');

    $sort_time = (isset($_GET['sort_time']) && !empty($_GET['sort_time'])) ? $_GET['sort_time'] : null;
    $sort_airport = (isset($_GET['sort_airport']) && !empty($_GET['sort_airport'])) ? $_GET['sort_airport'] : null;

    $query = 'SELECT v.vluchtnummer, vertrektijd, naam 
    FROM Vlucht v 
    INNER JOIN Luchthaven l ON l.luchthavencode = v.bestemming 
    INNER JOIN IncheckenVlucht iv on iv.vluchtnummer = v.vluchtnummer';

    $params = [];

    if($sort_airport){
        $query .= ' WHERE v.bestemming = :bestemming';
        $params[':bestemming'] = $sort_airport;
    }
    if($sort_time){
        $query .= ' ORDER BY vertrektijd '. $sort_time;
    }

    $flights = dbQuery($query,$params);

?>
<!DOCTYPE html>
<html lang="en">
    <?= loadHead('Medewerker') ?>
    <body>
        <?= loadMenu() ?>
        <main>
            <?= showErrors(); ?>

            <section class="page-header">
                <div class="container">
                    <h1>Welkom, Medewerker</h1>
                </div>
            </section>

            <section class="search-section">
                <div class="container">
                    <div class="search-form">
                        <h2>Zoeken naar vlucht</h2>
                        <form method="GET" action="inchecken.php">
                            <div class="form-item">
                                <label for="vluchtnummer">Vluchtnummer:</label>
                                <input type="text" name="vluchtnummer" id="vluchtnummer" placeholder="..." required>
                            </div>
                            <button type="submit" class="btn btn-green">Zoeken</button>
                        </form>
                    </div>
                </div>
            </section>

            <section class="flights-table">
                <div class="container">
                    <h2>Vluchten overzicht</h2>
                    <?= showFilterForm($sort_time,$sort_airport) ?>
                    <div class="overflow-x-auto">
                        <table>
                            <thead>
                                <tr>
                                    <td>Vluchtnummer</td>
                                    <td>Datum</td>
                                    <td>Naar</td>
                                    <td>Acties</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?= getTableRows($flights) ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </main>
        <?= loadFooter() ?>
    </body>
</html>