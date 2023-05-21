<?php

include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT id, name, age, country FROM Sportsmans");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }


    try {
        $stmt = $db->prepare("SELECT id, name, age, country FROM Sportsmans");
        $params = [];

        if (!empty($_COOKIE['ages'])) {
            $filter_age_ids = unserialize($_COOKIE['ages']);
            $in_values1 = implode(',', array_fill(0, count($filter_age_ids), '?'));
            $stmt_sql = isset($stmt_sql) ? $stmt_sql." AND age IN ($in_values1)" : "age IN ($in_values1)";
            $params = array_merge($params, $filter_age_ids);
        }

        if (!empty($_COOKIE['countries'])) {
            $filter_country_ids = unserialize($_COOKIE['countries']);
            $in_values2 = implode(',', array_fill(0, count($filter_country_ids), '?'));
            $stmt_sql = isset($stmt_sql) ? $stmt_sql." AND country IN ($in_values2)" : "country IN ($in_values2)";
            $params = array_merge($params, $filter_country_ids);
        }

        if (isset($stmt_sql)) {
            $stmt_sql = "SELECT id, name, age, country FROM Sportsmans WHERE ".$stmt_sql;
            $stmt = $db->prepare($stmt_sql);
            $stmt->execute($params);
            $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt->execute();
            $values = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("SELECT age FROM Sportsmans");
            $stmt->execute();
            $a_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filter_age_ids = [];
            foreach ($a_ids as $a_id) {
                $filter_age_ids[] = $a_id['age'];
            }

            $stmt = $db->prepare("SELECT country FROM Sportsmans");
            $stmt->execute();
            $c_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filter_country_ids = [];
            foreach ($c_ids as $c_id) {
                $filter_country_ids[] = $c_id['country'];
            }
        }
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }





    $new = array();
    $new['name'] = empty($_COOKIE['name']) ? '' : $_COOKIE['name'];
    $new['age'] = empty($_COOKIE['age']) ? '' : $_COOKIE['age'];
    $new['country'] = empty($_COOKIE['country']) ? '' : $_COOKIE['country'];
    include('assets/Sportsmans.php');
} else {
    $errors = array();
    $messages = array();
    if (!empty($_POST['addnewdate'])) {
        if (empty($_POST['name'])) {
            $errors['name1'] = 'Заполните поле "Имя спортсмена"';
            setcookie('name', '', time() + 24 * 60 * 60);
        } else if (!preg_match('/^[\p{L}\p{M}\s.]+$/u', $_POST['name'])) {
            $errors['name2'] = 'Некорректно заполнено поле "Имя спортсмена"';
            setcookie('name', $_POST['name'], time() + 24 * 60 * 60);
        } else {
            setcookie('name', $_POST['name'], time() + 24 * 60 * 60);
        }

        if (empty($_POST['age'])) {
            $errors['age1'] = 'Заполните поле "Возраст спортсмена"';
            setcookie('age', '', time() + 24 * 60 * 60);
        } else if (!is_numeric($_POST['age'])) {
            $errors['age2'] = 'Некорректно заполнено поле "Возраст спортсмена"';
            setcookie('age', $_POST['age'], time() + 24 * 60 * 60);
        } else {
            setcookie('age', $_POST['age'], time() + 24 * 60 * 60);
        }

        if (empty($_POST['country'])) {
            $errors['country1'] = 'Заполните поле "Страна"';
            setcookie('country', '', time() + 24 * 60 * 60);
        } else if (!preg_match('/^[\p{L}\p{M}\s.]+$/u', $_POST['country'])) {
            $errors['country2'] = 'Некорректно заполнено поле "Страна"';
            setcookie('country', $_POST['country'], time() + 24 * 60 * 60);
        } else {
            setcookie('country', $_POST['country'], time() + 24 * 60 * 60);
        }
        
        if (empty($errors)) {
            $name = $_POST['name'];
            $age = intval($_POST['age']);
            $country = $_POST['country'];
            $stmt = $db->prepare("INSERT INTO Sportsmans (name, age, country) VALUES (?, ?, ?)");
            $stmt->execute([$name, $age, $country]);
            $messages['added'] = 'Спортсмен "'.$name.'" успешно добавлен';
            setcookie('name', '', time() + 24 * 60 * 60);
            setcookie('age', '', time() + 24 * 60 * 60);
            setcookie('country', '', time() + 24 * 60 * 60);
        }
    } 
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)_x$/', $key, $matches)) {
            $id = $matches[1]; 
            $stmt = $db->prepare("SELECT id FROM Performances WHERE SportsmanID = ?");
            $stmt->execute([$id]);
            $empty = $stmt->rowCount() === 0;
            if (!$empty) {
                $errors['delete'] = 'Поле с <b>id = '.$id.'</b> невозможно удалить, т.к. оно связанно с журналом выступлений';
            } else {
                $stmt = $db->prepare("DELETE FROM Sportsmans WHERE id = ?");
                $stmt->execute([$id]);
                $messages['deleted'] = 'Спортсмен с <b>id = '.$id.'</b> успешно удалён';
            }
        }
        if (preg_match('/^edit(\d+)_x$/', $key, $matches)) {
            $id = $matches[1];
            setcookie('edit', $id, time() + 24 * 60 * 60);
        }
        if (preg_match('/^save(\d+)_x$/', $key, $matches)) {
            setcookie('edit', '', time() + 24 * 60 * 60);
            $id = $matches[1];
            $stmt = $db->prepare("SELECT name, age, country FROM Sportsmans WHERE id = ?");
            $stmt->execute([$id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dates['name'] = $_POST['name' . $id];
            $dates['age'] = $_POST['age' . $id];
            $dates['country'] = $_POST['country' . $id];

            if (array_diff_assoc($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE Sportsmans SET name = ?, age = ?, country = ? WHERE id = ?");
                $stmt->execute([$dates['name'], $dates['age'], $dates['country'], $id]);
                $messages['edited'] = 'Спортсмен с <b>id = '.$id.'</b> успешно обновлён';
            }
        }
    }

    if (!empty($_POST['resetall'])) {
        setcookie('ages', '');
        setcookie('countries', '');
    }

    if (!empty($_POST['filter'])) {

        $filter_age_ids = array();
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'filter_age_') !== false) {
                $id = substr($key, 11);
                $filter_age_ids[] = $id;
            }
        }
        setcookie('ages', serialize($filter_age_ids));

        $filter_country_ids = array();
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'filter_country_') !== false) {
                $id = substr($key, 15);
                $filter_country_ids[] = $id;
            }
        }
        setcookie('countries', serialize($filter_country_ids));
        
    }

    if (!empty($messages)) {
        setcookie('messages', serialize($messages), time() + 24 * 60 * 60);
    }
    if (!empty($errors)) {
        setcookie('errors', serialize($errors), time() + 24 * 60 * 60);
    }
    header('Location: Sportsmans.php');
}