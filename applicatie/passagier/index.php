<?php
    include '../setup.php';
    roleCheck('passagier');
?>
<!DOCTYPE html>
<html lang="en">
    <?= loadHead('Passagier') ?>
    <body>
        <?= loadMenu() ?>
        <main>
            <?= showErrors(); ?>

            <section class="page-header">
                <div class="container">
                    <h1>Welkom, passagier</h1>
                </div>
            </section>

            <section class="search-section">
                <div class="container">
                    <div class="search-form">
                        <h2>Mijn vlucht</h2>
                        <form method="GET" action="inchecken.php">
                            <div class="form-item">
                                <label for="passagiernummer">Passagiernummer:</label>
                                <input type="number" name="passagiernummer" id="passagiernummer" placeholder="..." required>
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