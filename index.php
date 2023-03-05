<?php
include './config.php';
$searching = false;

// Pobranie danych z formularza
$krs_number = $_POST['krs'] ?? '';
$register = $_POST['register'] ?? '';
$result = null;

if (isset($_POST['krs'])) $searching = true;

if (strlen($krs_number) > 9 && $register != '') {
    // Ustawienia API KRS
    $url = 'https://api-krs.ms.gov.pl/api/krs/OdpisAktualny/' . $krs_number . '?rejestr=' . $register . '&format=json';

    // Wywołanie API
    $response = file_get_contents($url);

    // Przetworzenie odpowiedzi API
    $data = json_decode($response, true);
    if (isset($data)) {
        $result = $data;
    }
}
?>
<!DOCTYPE html>
<html lang="pl" class="mx-0 my-0 px-0 py-0">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Krajowy rejestr sądowy</title>
</head>

<body class="container-fluid text-dark">

<div class="main-container shadow w-50 mx-auto">
    <div class="title_1">
        Pobierania danych z Krajowego Rejestru Sądowego
    </div>
    <div class="mx-4 mb-5 py-4 text-center">

        <form method="post" class="py-4" action="">
            <div class="form-group">
                <label for="krs" class="font-weight-bold"><h3>Numer KRS</h3></label>
                <input type="number" class="form-control" name="krs" id="krs" aria-describedby="krs" placeholder="Wprowadź numer KRS">
                <small id="krsError" class="form-text d-none text-danger font-weight-bold">Niepoprawny numer KRS</small>
                <label>Rejestr:</label>
                <select name="register" class="form-control w-50 mx-auto">
                    <option value="p">P – przedsiębiorcy</option>
                    <option value="s">S-stowarzyszenia</option>
                </select>
                <div class="col-auto mt-5">
                    <button type="submit" class="btn btn-dark w-25" id="search">Wyszukaj</button>
                </div>
            </div>
        </form>
        <?php
        if ($result) :
            $data = $result['odpis']['dane'];
            $dzial1 = $data['dzial1'];
            $dzial2 = $data['dzial2'];
            ?>
            <table>
                <tbody>
                <tr>
                    <td width="150">Nazwa</td>
                    <td class="big" colspan="3"><?= $dzial1['danePodmiotu']['nazwa'] ?></td>
                </tr>
                <tr>
                    <td width="150">Województwo</td>
                    <td class="big" width="150"><?= $dzial1['siedzibaIAdres']['siedziba']['wojewodztwo'] ?></td>
                </tr>
                <tr>
                    <td>Numer KRS</td>
                    <td class="big"><?= $result['odpis']['naglowekA']['numerKRS'] ?></td>
                    <td>Powiat</td>
                    <td class="big"><?= $dzial1['siedzibaIAdres']['siedziba']['powiat'] ?></td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td class="big"><?= $dzial1['danePodmiotu']['identyfikatory']['nip'] ?></td>
                    <td>Gmina</td>
                    <td class="big"><?= $dzial1['siedzibaIAdres']['siedziba']['gmina'] ?></td>
                </tr>
                <tr>
                    <td>REGON</td>
                    <td class="big"><?= $dzial1['danePodmiotu']['identyfikatory']['regon'] ?></td>
                    <td>Miejscowość</td>
                    <td class="big"><?= $dzial1['siedzibaIAdres']['siedziba']['miejscowosc'] ?></td>
                </tr>
                <tr>
                    <td>Forma prawna</td>
                    <td><?= $dzial1['danePodmiotu']['formaPrawna'] ?></td>
                    <td>Adres</td>
                    <td><?= $dzial1['siedzibaIAdres']['adres']['ulica'] . ' ' . $dzial1['siedzibaIAdres']['adres']['nrDomu'] ?></td>
                </tr>
                <tr>
                    <td>Kod pocztowy</td>
                    <td class="big"><?= $dzial1['siedzibaIAdres']['adres']['kodPocztowy'] ?></td>
                </tr>
                <tr>
                    <td>Status OPP</td>
                    <td class="big">
                        <input disabled="disabled" checked="<?= $dzial1['danePodmiotu']['czyPosiadaStatusOPP'] ? 'checked' : '' ?>" id="checkbox" name="checkbox" type="checkbox">
                    </td>
                </tr>

                <tr>
                    <td colspan="4"><hr></td>
                </tr>
                <tr>
                    <td>Nazwa organu reprezentacji</td>
                    <td><?= $dzial2['reprezentacja']['nazwaOrganu'] ?></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td>Sposób reprezentacji</td>
                    <td class="big" colspan="3"><?= $dzial2['reprezentacja']['sposobReprezentacji'] ?></td>
                </tr>
                <tr>
                    <td colspan="4"><hr></td>
                </tr>
                <tr>
                    <td>Członkowie reprezentacji</td>
                </tr>
                </tbody>
            </table>
            <table class="mb-4">
                <thead>
                <tr>
                    <th>Nazwisko lub nazwa</th>
                    <th>Nazwisko drugi człon</th>
                    <th>Imię pierwsze</th>
                    <th>Imię drugie</th>
                    <th>Funkcja</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($dzial2['reprezentacja']['sklad'] as $member) :
                    ?>
                    <tr>
                        <td><?= $member['nazwisko']['nazwiskoICzlon'] ?></td>
                        <td><?= $member['nazwisko']['nazwiskoIICzlon'] ?? '' ?></td>
                        <td><?= $member['imiona']['imie'] ?></td>
                        <td>&nbsp;</td>
                        <td><?= $member['funkcjaWOrganie'] ?></td>
                    </tr>
                <?php
                endforeach;
                ?>

                </tbody>
            </table>
        <?php
        elseif ($searching):
            ?>
            <h4>Brak wyników</h4>
        <?php
        endif;
        ?>
    </div>
</div>

</body>
</html>