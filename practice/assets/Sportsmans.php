<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel="stylesheet" href="styles/style.css">
    <link type="image/x-icon" href="images/logo.png" rel="shortcut icon">
    <link type="Image/x-icon" href="images/logo.png" rel="icon">
    <title>Sport</title>
</head>
<script>
    function toggleFilter() {
        var filterBlock = document.getElementById("filter-block");
        if (filterBlock.style.display === "none") {
            filterBlock.style.display = "block";
        } else {
            filterBlock.style.display = "none";
        }
    }

    var expanded = false;
    function showCheckboxes(checkboxesId) {
        var checkboxes = document.getElementById(checkboxesId);
        if (!expanded) {
            checkboxes.style.display = "block";
            expanded = true;
        } else {
            checkboxes.style.display = "none";
            expanded = false;
        }
    }
</script>
<body>
    <header>
        <div class="header-items">
            <a href="index.php" class="logo">
                <img src="images/logo.png" alt="logo" width="37" height="37">
                <h1>Спорт</h1>
            </a>
            <nav>
                <ul>
                    <li><a class="active" href="#">Список спортсменов</a></li>
                    <li><a href="Sports.php">Список видов спорта</a></li>
                    <li><a href="Stadiums.php">Список стадионов</a></li>
                    <li><a href="Performances.php">Журнал выступлений</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <?php
            if (!empty($_COOKIE['messages'])) {
                echo '<div class="messages">';
                $messages = unserialize($_COOKIE['messages']);
                foreach ($messages as $message) {
                    echo $message . '</br>';
                }
                echo '</div>';
                setcookie('messages', '', time() + 24 * 60 * 60);
            }
            if (!empty($_COOKIE['errors'])) {
                echo '<div class="errors">';
                $errors = unserialize($_COOKIE['errors']);
                foreach ($errors as $error) {
                    echo $error . '</br>';
                }
                echo '</div>';
                setcookie('errors', '', time() + 24 * 60 * 60);
            }
        ?>
        <form action="" method="POST">
            <div class="main-content">
                <h2>Список спортсменов</h2>
            </div>
            <div class="main-content">
                <div class="top-table">
                    <div class="newdates">
                        <div class="newdates-item">
                            <label for="name">Имя спортсмена:</label>
                        </div>
                        <div class="newdates-item">
                            <input name="name" value="<?php print($new['name']); ?>" placeholder="имя">
                        </div>
                        <div class="newdates-item">
                            <label for="age">Возраст спортсмена:</label>
                        </div>
                        <div class="newdates-item">
                            <input name="age" value="<?php print($new['age']); ?>" placeholder="возраст">
                        </div>
                        <div class="newdates-item">
                            <label for="country">Страна:</label>
                        </div>
                        <div class="newdates-item">
                            <input name="country" value="<?php print($new['country']); ?>" placeholder="cтрана">
                        </div>
                        <div class="newdates-item">
                            <input type="submit" name="addnewdate" value="Добавить">
                        </div>
                    </div>


                    <div id="filter-block" style="display:none;">
                        <h3>Фильтр</h3>
                        <div class="row">
                            <div class="multiselect">
                                <div class="selectBox" onclick="showCheckboxes('checkboxes1')">
                                    <select>
                                        <option>Возраст</option>
                                    </select>
                                    <div class="overSelect"></div>
                                </div>
                                <div id="checkboxes1">
                                    <?php
                                    $stmt = $db->prepare("SELECT age FROM Sportsmans ORDER BY age");
                                    $stmt->execute();
                                    $Ages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($Ages as $age) {
                                        echo '<label for="age'.$age['age'].'"><input type="checkbox" ';
                                        echo empty($filter_age_ids) ? "" : (in_array($age['age'], $filter_age_ids) ? "checked " : "");
                                        echo 'name="filter_age_'.$age['age'].'" id="age'.$age['age'].'">'.$age['age'].'</label>';
                                    }
                                    ?>
                                    <button type="button" id="checkAll1">Отменить всё</button>
                                </div>
                            </div>

                            <div class="multiselect">
                                <div class="selectBox" onclick="showCheckboxes('checkboxes2')">
                                    <select>
                                        <option>Страна</option>
                                    </select>
                                    <div class="overSelect"></div>
                                </div>
                                <div id="checkboxes2">
                                    <?php
                                    $stmt = $db->prepare("SELECT id, country FROM Sportsmans ORDER BY country");
                                    $stmt->execute();
                                    $Countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($Countries as $country) {
                                        echo '<label for="country'.$country['country'].'"><input type="checkbox" ';
                                        echo empty($filter_country_ids) ? "" : (in_array($country['country'], $filter_country_ids) ? "checked " : "");
                                        echo 'name="filter_country_'.$country['country'].'" id="country'.$country['country'].'">'.$country['country'].'</label>';
                                    }
                                    ?>
                                    <button type="button" id="checkAll2">Отменить всё</button>
                                </div>
                            </div>
                        </div>
                        </br></br>
                        <input type="submit" name="filter" value="Применить">
                        <input type="submit" name="resetall" value="Сбросить всё">
                    </div>


                </div>
            </div>
            <div class="main-content">
            <?php
                echo    '<table class="table-mobile">
                            <tr>
                                <th>id</th>
                                <th>Имя спортсмена</th>
                                <th>Возраст спортсмена</th>
                                <th>Страна</th>
                                <th colspan=2>
                                    <button type="button" onclick="toggleFilter()">
                                        <img src="https://cdn-icons-png.flaticon.com/512/107/107799.png" alt="filters" width="20" height="20">
                                    </button>
                                </th>
                            <tr>';
                foreach ($values as $value) {
                    echo    '<tr>';
                    echo        '<td>'; print($value['id']); echo '</td>';
                    echo        '<td>
                                    <input'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                                    else print(" "); echo 'name="name'.$value['id'].'" value="'.$value['name'].'">
                                </td>';
                    echo        '<td>
                                    <input'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                                    else print(" "); echo 'name="age'.$value['id'].'" value="'.$value['age'].'">
                                </td>';
                    echo        '<td>
                                    <input'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                                    else print(" "); echo 'name="country'.$value['id'].'" value="'.$value['country'].'">
                                </td>';
                if (empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) {
                    echo        '<td> <input name="edit'.$value['id'].'" type="image" src="https://static.thenounproject.com/png/2185844-200.png" width="20" height="20" alt="submit"/> </td>';
                    echo        '<td> <input name="clear'.$value['id'].'" type="image" src="https://cdn-icons-png.flaticon.com/512/860/860829.png" width="20" height="20" alt="submit"/> </td>';
                } else {
                    echo        '<td colspan=2> <input name="save'.$value['id'].'" type="image" src="https://cdn-icons-png.flaticon.com/512/84/84138.png" width="20" height="20" alt="submit"/> </td>';
                }
                    echo    '</tr>';
                }
                echo '</table>';
            ?>
            </div>
        </form>
    </main>
<script>
    document.getElementById('checkAll1').addEventListener('click', 
        function() {
            var checkboxes = document.querySelectorAll('#checkboxes1 input[type=checkbox]');
            if (this.innerHTML === 'Выбрать все') {
                checkboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });
                this.innerHTML = 'Отменить все';
            } else {
                checkboxes.forEach(function(checkbox) {
                checkbox.checked = false;
            });
                this.innerHTML = 'Выбрать все';
            }
        });

    document.getElementById('checkAll2').addEventListener('click',
        function() {
            var checkboxes = document.querySelectorAll('#checkboxes2 input[type=checkbox]');
            if (this.innerHTML === 'Выбрать все') {
                checkboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });
                this.innerHTML = 'Отменить все';
            } else {
                checkboxes.forEach(function(checkbox) {
                checkbox.checked = false;
            });
                this.innerHTML = 'Выбрать все';
            }
        });
</script>
</body>
</html>