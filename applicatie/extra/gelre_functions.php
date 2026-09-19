<?php

function dbQuery($sql,$params){
    $db = maakVerbinding();

    $query = $db->prepare($sql);
    $query->execute($params);
    $data = $query->fetchAll();

    //remove numeric keys from fetchall
    foreach($data as $key => $values){
        foreach($values as $column => $value){
            if(is_numeric($column)){
                unset($data[$key][$column]);
            }
        }
    }

    if(count($data) == 1){
        $data = $data[0];
    }

    return $data;
}

function dbInsert($sql,$params){
    $db = maakVerbinding();

    $query = $db->prepare($sql);
    $query->execute($params);
    
    return $query;
}

function showFlightData($flight, $passagier = null) {
    $vertrekDatumTime = strtotime($flight['vertrektijd']);

    $html = '<section class="overview">';
    $html .= '<div class="container">';
    $html .= '<div class="header">';
    $html .= '<h1>' . $flight['vluchtnummer'] . ' naar <span>' . $flight['naam'] . '</span></h1>';
    $html .= '<img src="/images/vliegtuig.jpeg" alt="Foto van vliegtuig">';
    $html .= '</div>';
    $html .= '<div class="body">';
    $html .= '<h2>Vertrek</h2>';
    $html .= '<div class="items-grid">';
    $html .= '<div class="item"><div class="title">Datum</div><div class="content">' . date("d M.",$vertrekDatumTime) . '</div></div>';
    $html .= '<div class="item"><div class="title">Tijd</div><div class="content">' . date("H:i",$vertrekDatumTime) . '</div></div>';
    $html .= '<div class="item"><div class="title">Check-in balie</div><div class="content">' . $flight['balienummer'] . '</div></div>';
    $html .= '<div class="item"><div class="title">Gate</div><div class="content">' . $flight['gatecode'] . '</div></div>';
    $html .= '</div>';
    $html .= '<h2>Vlucht informatie</h2>';
    $html .= '<div class="items-grid">';
    $html .= '<div class="item"><div class="title">Land</div><div class="content">' . $flight['land'] . '</div></div>';
    $html .= '<div class="item"><div class="title">Max personen</div><div class="content">' . $flight['max_aantal'] . '</div></div>';
    $html .= '<div class="item"><div class="title">Max bagagegewicht pp</div><div class="content">' . round($flight['max_gewicht_pp']) . 'kg</div></div>';
    $html .= '<div class="item"><div class="title">Max totale bagagegewicht</div><div class="content">' . round($flight['max_totaalgewicht']) . 'kg</div></div>';
    $html .= '</div>';
    if($passagier){
        if($passagier['inchecktijdstip'] !== NULL){
            $incheckDatum = date('Y-m-d H:i', strtotime($passagier['inchecktijdstip']));
        }else{
            $incheckDatum = 'Geen incheckdatum';
        }
        $html .= '<h2>Passagier informatie</h2>';
        $html .= '<div class="items-grid">';
        $html .= '<div class="item"><div class="title">Naam</div><div class="content">' . $passagier['naam'] . '</div></div>';
        $html .= '<div class="item"><div class="title">Geslacht</div><div class="content">' . $passagier['geslacht'] . '</div></div>';
        $html .= '<div class="item"><div class="title">Stoel</div><div class="content">' . $passagier['stoel'] . '</div></div>';
        $html .= '<div class="item"><div class="title">Inchecktijdstip</div><div class="content">' . $incheckDatum . '</div></div>';
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</section>';
    return $html;
}

function showFilterForm($sort_time, $sort_airport) {
    $airports = dbQuery('SELECT * FROM Luchthaven',[]);

    $form = '<form action="" type="get" class="mt-1 filter-form">';
    $form .= '<div class="form-item">';
    $form .= '<label for="sort_time">Tijd:</label>';
    $form .= '<select id="sort_time" name="sort_time">';
    $form .= '<option value=""';
    if($sort_time == NULL){
        $form .= ' selected';
    }
    $form .= '>Niet gesorteerd</option>';
    $form .= '<option value="asc"';
    if($sort_time == 'asc'){
        $form .= ' selected';
    }
    $form .= '>ASC</option>';
    $form .= '<option value="desc"';
    if($sort_time == 'desc'){
        $form .= ' selected';
    }
    $form .= '>DESC</option>';
    $form .= '</select>';
    $form .= '</div>';
    $form .= '<div class="form-item">';
    $form .= '<label for="sort_airport">Luchthaven:</label>';
    $form .= '<select id="sort_airport" name="sort_airport">';
    $form .= '<option value=""';
    if($sort_airport == NULL){
        $form .= ' selected';
    }
    $form .= '>Niet gesorteerd</option>';
    foreach($airports as $airport){
        $form .= '<option value="'.$airport['luchthavencode'].'"';
        if($sort_airport == $airport['luchthavencode']){
            $form .= ' selected';
        }
        $form .= '>'.$airport['naam'].'</option>';
    }
    $form .= '</select>';
    $form .= '</div>';
    $form .= '<input type="submit" class="btn btn-green" value="Zoeken">';
    $form .= '</form>';
    return $form;
}

function getTableRows($flights) {
    $rows = '';
    foreach($flights as $flight){
        $vertrekDatumTime = strtotime($flight['vertrektijd']);
        $rows .= '<tr>';
        $rows .= '<td>'.$flight['vluchtnummer'].'</td>';
        $rows .= '<td>'.date('d-m-Y H:i', $vertrekDatumTime).'</td>';
        $rows .= '<td>'.$flight['naam'].'</td>';
        $rows .= '<td>';
        $rows .= '<a href="inchecken.php?vluchtnummer='.$flight['vluchtnummer'].'" class="btn btn-green">Bekijken</a>';
        $rows .= '</td>';
        $rows .= '</tr>';
    }
    return $rows;
}