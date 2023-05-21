<?php

include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT id, SportsmanID, SportId, StadiumID, date FROM Performances");
        $params = [];

        if (!empty($_COOKIE['datex'])) {
            $stmt_sql = isset($stmt_sql) ? $stmt_sql." AND date = ?" : "date = ?";
            $params[] = $_COOKIE['datex'];
        }

        if (!empty($_COOKIE['sports'])) {
            $filter_sport_ids = unserialize($_COOKIE['sports']);
            $in_values1 = implode(',', array_fill(0, count($filter_sport_ids), '?'));
            $stmt_sql = isset($stmt_sql) ? $stmt_sql." AND SportId IN ($in_values1)" : "SportId IN ($in_values1)";
            $params = array_merge($params, $filter_sport_ids);
        }

        if (!empty($_COOKIE['stadiums'])) {
            $filter_stadium_ids = unserialize($_COOKIE['stadiums']);
            $in_values2 = implode(',', array_fill(0, count($filter_stadium_ids), '?'));
            $stmt_sql = isset($stmt_sql) ? $stmt_sql." AND StadiumID IN ($in_values2)" : "StadiumID IN ($in_values2)";
            $params = array_merge($params, $filter_stadium_ids);
        }

        if (isset($stmt_sql)) {
            $stmt_sql = "SELECT id, SportsmanID, SportId, StadiumID, date FROM Performances WHERE ".$stmt_sql;
            $stmt = $db->prepare($stmt_sql);
            $stmt->execute($params);
            $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt->execute();
            $values = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("SELECT id FROM Sports");
            $stmt->execute();
            $s_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filter_sport_ids = [];
            foreach ($s_ids as $s_id) {
                $filter_sport_ids[] = $s_id['id'];
            }

            $stmt = $db->prepare("SELECT id FROM Stadiums");
            $stmt->execute();
            $s_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filter_stadium_ids = [];
            foreach ($s_ids as $s_id) {
                $filter_stadium_ids[] = $s_id['id'];
            }
        }
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $new = array();
    $new['SportsmanID'] = empty($_COOKIE['SportsmanID']) ? '' : $_COOKIE['SportsmanID'];
    $new['SportId'] = empty($_COOKIE['SportId']) ? '' : $_COOKIE['SportId'];
    $new['StadiumID'] = empty($_COOKIE['StadiumID']) ? '' : $_COOKIE['StadiumID'];
    $new['date'] = empty($_COOKIE['date']) ? '' : $_COOKIE['date'];
    include('assets/Performances.php');
} else {
    $errors = array();
    $messages = array();
    if (!empty($_POST['addnewdate'])) {

        if (empty($_POST['SportsmanID'])) {
            $errors['SportsmanID'] = 'Заполните поле "SportsmanID"';
        } else {
            setcookie('SportsmanID', $_POST['SportsmanID'], time() + 24 * 60 * 60);
        }

        if (empty($_POST['SportId'])) {
            $errors['SportId'] = 'Заполните поле "SportId"';
        } else {
            setcookie('SportId', $_POST['SportId'], time() + 24 * 60 * 60);
        }

        if (empty($_POST['StadiumID'])) {
            $errors['StadiumID'] = 'Заполните поле "StadiumID"';
        } else {
            setcookie('StadiumID', $_POST['StadiumID'], time() + 24 * 60 * 60);
        }

        if (empty($_POST['date'])) {
            $errors['date'] = 'Заполните поле "date"';
        } else {
            setcookie('date', $_POST['date'], time() + 24 * 60 * 60);
        }

        if (empty($errors)) {
            $SportsmanID = $_POST['SportsmanID'];
            $SportId = $_POST['SportId'];
            $StadiumID = $_POST['StadiumID'];
            $date = $_POST['date'];

            $stmt = $db->prepare("INSERT INTO Performances (SportsmanID, SportId, StadiumID, date) 
                VALUES (?, ?, ?, ?)");
            $stmt->execute([$SportsmanID, $SportId, $StadiumID, $date]);
            $messages['added'] = 'Данные успешно добавлены';
            setcookie('SportsmanID', '', time() + 24 * 60 * 60);
            setcookie('SportId', '', time() + 24 * 60 * 60);
            setcookie('StadiumID', '', time() + 24 * 60 * 60);
            setcookie('date', '', time() + 24 * 60 * 60);
        }
    } 
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)_x$/', $key, $matches)) {
            $id = $matches[1]; 
            $stmt = $db->prepare("DELETE FROM Performances WHERE id = ?");
            $stmt->execute([$id]);
            $messages['deleted'] = 'Запись с <b>id = '.$id.'</b> успешно удалена';
        }
        if (preg_match('/^edit(\d+)_x$/', $key, $matches)) {
            $id = $matches[1];
            setcookie('edit', $id, time() + 24 * 60 * 60);
        }
        if (preg_match('/^save(\d+)_x$/', $key, $matches)) {
            setcookie('edit', '', time() + 24 * 60 * 60);
            $id = $matches[1];
            $stmt = $db->prepare("SELECT SportsmanID, SportId, StadiumID, date FROM Performances WHERE id = ?");
            $stmt->execute([$id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dates['SportsmanID'] = $_POST['SportsmanID' . $id];
            $dates['SportId'] = $_POST['SportId' . $id];
            $dates['StadiumID'] = $_POST['StadiumID' . $id];
            $dates['date'] = $_POST['date' . $id];

            if (array_diff_assoc($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE Performances SET SportsmanID = ?, SportId = ?, StadiumID = ?, date = ? WHERE id = ?");
                $stmt->execute([$dates['SportsmanID'], $dates['SportId'], $dates['StadiumID'], $dates['date'], $id]);
                $messages['edited'] = 'Запись с <b>id = '.$id.'</b> успешно обновлена';
            }
        }
    }
    
    if (!empty($_POST['resetall'])) {
        setcookie('datex', '');
        setcookie('sports', '');
        setcookie('stadiums', '');
    }

    if (!empty($_POST['filter'])) {

        if (!empty($_POST['date']))
            setcookie('datex', $_POST['date']);

        $filter_sport_ids = array();
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'filter_sport_') !== false) {
                $id = substr($key, 13);
                $filter_sport_ids[] = $id;
            }
        }
        setcookie('sports', serialize($filter_sport_ids));

        $filter_stadium_ids = array();
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'filter_stadium_') !== false) {
                $id = substr($key, 15);
                $filter_stadium_ids[] = $id;
            }
        }
        setcookie('stadiums', serialize($filter_stadium_ids));
        
    }

    if (!empty($messages)) {
        setcookie('messages', serialize($messages), time() + 24 * 60 * 60);
    }
    if (!empty($errors)) {
        setcookie('errors', serialize($errors), time() + 24 * 60 * 60);
    }
    header('Location: Performances.php');
}