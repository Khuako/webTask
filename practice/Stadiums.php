<?php

include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT id, name, city FROM Stadiums");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $new = array();
    $new['name'] = empty($_COOKIE['name']) ? '' : $_COOKIE['name'];
    $new['city'] = empty($_COOKIE['city']) ? '' : $_COOKIE['city'];
    include('assets/Stadiums.php');
} else {
    $errors = array();
    $messages = array();
    if (!empty($_POST['addnewdate'])) {
        if (empty($_POST['name'])) {
            $errors['name1'] = 'Заполните поле "Название стадиона"';
            setcookie('name', '', time() + 24 * 60 * 60);
        } else if (!preg_match('/^[\p{L}\p{M}\s.]+$/u', $_POST['name'])) {
            $errors['name2'] = 'Некорректно заполнено поле "Название стадиона"';
            setcookie('name', $_POST['name'], time() + 24 * 60 * 60);
        } 

        if (empty($_POST['city'])) {
            $errors['city1'] = 'Заполните поле "Город"';
            setcookie('city', '', time() + 24 * 60 * 60);
        } else if (!preg_match('/^[\p{L}\p{M}\s.]+$/u', $_POST['city'])) {
            $errors['city2'] = 'Некорректно заполнено поле "Город"';
            setcookie('city', $_POST['city'], time() + 24 * 60 * 60);
        } 
        
        if (empty($errors)) {
            $name = $_POST['name'];
            $city = $_POST['city'];
            $stmt = $db->prepare("INSERT INTO Stadiums (name, city) VALUES (?, ?)");
            $stmt->execute([$name, $city]);
            $messages['added'] = 'Стадион "'.$name.'" успешно добавлен';
            setcookie('name', '', time() + 24 * 60 * 60);
            setcookie('city', '', time() + 24 * 60 * 60);
        }
    } 
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)_x$/', $key, $matches)) {
            $id = $matches[1]; 
            $stmt = $db->prepare("SELECT id FROM Performances WHERE StadiumID = ?");
            $stmt->execute([$id]);
            $empty = $stmt->rowCount() === 0;
            if (!$empty) {
                $errors['delete'] = 'Поле с <b>id = '.$id.'</b> невозможно удалить, т.к. оно связанно с журналом выступлений';
            } else {
                $stmt = $db->prepare("DELETE FROM Stadiums WHERE id = ?");
                $stmt->execute([$id]);
                $messages['deleted'] = 'Стадион с <b>id = '.$id.'</b> успешно удалён';
            }
        }
        if (preg_match('/^edit(\d+)_x$/', $key, $matches)) {
            $id = $matches[1];
            setcookie('edit', $id, time() + 24 * 60 * 60);
        }
        if (preg_match('/^save(\d+)_x$/', $key, $matches)) {
            setcookie('edit', '', time() + 24 * 60 * 60);
            $id = $matches[1];
            $stmt = $db->prepare("SELECT name, city FROM Stadiums WHERE id = ?");
            $stmt->execute([$id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dates['name'] = $_POST['name' . $id];
            $dates['city'] = $_POST['city' . $id];

            if (array_diff_assoc($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE Stadiums SET name = ?, city = ? WHERE id = ?");
                $stmt->execute([$dates['name'], $dates['city'],  $id]);
                $messages['edited'] = 'Стадион с <b>id = '.$id.'</b> успешно обновлён';
            }
        }
    }
    if (!empty($messages)) {
        setcookie('messages', serialize($messages), time() + 24 * 60 * 60);
    }
    if (!empty($errors)) {
        setcookie('errors', serialize($errors), time() + 24 * 60 * 60);
    }
    header('Location: Stadiums.php');
}