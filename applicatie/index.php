<?php
    include 'setup.php';
?>
<!DOCTYPE html>
<html lang="en">
    <?= loadHead('Home') ?>
    <body>
        <?= loadMenu() ?>
        <main>
            <?= showErrors(); ?>

            <section class="page-header">
                <div class="container">
                    <h1>Welkom bij Gelre Airport</h1>
                </div>
            </section>
            <section class="login">
                <div class="container">
                    <div class="card">
                        <div class="options">
                            <h2>Login</h2>
                            <a href="login.php?role=medewerker">Medewerker</a>
                            <a href="login.php?role=passagier">Passagier</a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="search-section">
                <div class="container">
                    <div class="search-form">
                        <h2>Zoeken naar vlucht</h2>

                        <form method="GET" action="overzicht.php">
                            <div class="form-item">
                                <label for="vluchtnummer">Vluchtnummer:</label>
                                <input type="number" name="vluchtnummer" id="vluchtnummer" placeholder="..." required>
                            </div>
                            <button type="submit" class="btn btn-green">Zoeken</button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
        <?= loadFooter() ?>
    </body>
</html>