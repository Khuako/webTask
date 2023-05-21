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
</head>
<body>
    <header>
        <div class="header-items">
            <a href="index.php" class="logo">
                <img src="images/logo.png" alt="logo" width="37" height="37">
                <h1>Спорт</h1>
            </a>
            <nav>
                <ul>
                    <li><a href="Sportsmans.php">Список спортсменов</a></li>
                    <li><a href="Sports.php">Список видов спорта</a></li>
                    <li><a href="Stadiums.php">Список стадионов</a></li>
                    <li><a class="active" href="#">Журнал выступлений</a></li>
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
                <h2>Журнал выступлений</h2>
            </div>
            <div class="main-content">
                <div class="top-table">
                    <div class="newdates">
                        <div class="newdates-item">
                            <label for="SportsmanID">Имя спортсмена</label>
                        </div>
                        <div class="newdates-item">
                            <select name="SportsmanID">
                                <?php
                                $stmt = $db->prepare("SELECT id, name FROM Sportsmans");
                                $stmt->execute();
                                $Sportsmans = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                print("<option selected disabled>выберите спортсмена</option>");
                                foreach ($Sportsmans as $sportsman) {
                                    if (!empty($new['SportsmanID']) && ($new['SportsmanID'] ==  $sportsman['id'])) {
                                        printf('<option selected value="%d">%d. %s</option>', $sportsman['id'], $sportsman['id'], $sportsman['name']);
                                    } else {
                                        printf('<option value="%d">%d. %s</option>', $sportsman['id'], $sportsman['id'], $sportsman['name']);
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="newdates-item">
                            <label for="SportId">Вид спорта</label>
                        </div>
                        <div class="newdates-item">
                            <select name="SportId">
                                <?php
                                $stmt = $db->prepare("SELECT id, name FROM Sports");
                                $stmt->execute();
                                $Sports = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                print("<option selected disabled>выберите вид спорт</option>");
                                foreach ($Sports as $sport) {
                                    if (!empty($new['SportId']) && ($new['SportId'] ==  $sport['id'])) {
                                        printf('<option selected value="%d">%d. %s</option>', $sport['id'], $sport['id'], $sport['name']);
                                    } else {
                                        printf('<option value="%d">%d. %s</option>', $sport['id'], $sport['id'], $sport['name']);
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="newdates-item">
                            <label for="StadiumID">Название стадиона</label>
                        </div>
                        <div class="newdates-item">
                            <select name="StadiumID">
                                <?php
                                $stmt = $db->prepare("SELECT id, name FROM Stadiums");
                                $stmt->execute();
                                $Stadiums = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                print("<option selected disabled>выберите стадион</option>");
                                foreach ($Stadiums as $stadium) {
                                    if (!empty($new['StadiumID']) && ($new['StadiumID'] ==  $stadium['id'])) {
                                        printf('<option selected value="%d">%d. %s</option>', $stadium['id'], $stadium['id'], $stadium['name']);
                                    } else {
                                        printf('<option value="%d">%d. %s</option>', $stadium['id'], $stadium['id'], $stadium['name']);
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="newdates-item">
                            <label for="date">Дата проведения</label>
                        </div>
                        <div class="newdates-item">
                            <input type="date" name="date" value=<?php print($new['date']); ?>>
                        </div>
                        <div class="newdates-item">
                            <input type="submit" name="addnewdate" value="Добавить">
                        </div>
                    </div>
                    <div id="filter-block" style="display:none;">
                        <h3>Фильтр</h3>
                        <input type="date" name="date" value="<?php echo isset($_COOKIE["datex"]) ? $_COOKIE["datex"] : ""?>">
                        </br></br>
                        <div class="row">
                            <div class="multiselect">
                                <div class="selectBox" onclick="showCheckboxes('checkboxes1')">
                                    <select>
                                        <option>Вид спорта</option>
                                    </select>
                                    <div class="overSelect"></div>
                                </div>
                                <div id="checkboxes1">
                                    <?php
                                    $stmt = $db->prepare("SELECT id, name FROM Sports");
                                    $stmt->execute();
                                    $Sports = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($Sports as $sport) {
                                        echo '<label for="sport'.$sport['id'].'"><input type="checkbox" ';
                                        echo empty($filter_sport_ids) ? "" : (in_array($sport['id'], $filter_sport_ids) ? "checked " : "");
                                        echo 'name="filter_sport_'.$sport['id'].'" id="sport'.$sport['id'].'">'.$sport['name'].'</label>';
                                    }
                                    ?>
                                    <button type="button" id="checkAll1">Отменить всё</button>
                                </div>
                            </div>
                            <div class="multiselect">
                                <div class="selectBox" onclick="showCheckboxes('checkboxes2')">
                                    <select>
                                        <option>Стадион</option>
                                    </select>
                                    <div class="overSelect"></div>
                                </div>
                                <div id="checkboxes2">
                                    <?php
                                    $stmt = $db->prepare("SELECT id, name FROM Stadiums");
                                    $stmt->execute();
                                    $Stadiums = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($Stadiums as $stadium) {
                                        echo '<label for="stadium'.$stadium['id'].'"><input type="checkbox" ';
                                        echo empty($filter_stadium_ids) ? "" : (in_array($stadium['id'], $filter_stadium_ids) ? "checked " : "");
                                        echo 'name="filter_stadium_'.$stadium['id'].'" id="stadium'.$stadium['id'].'">'.$stadium['name'].'</label>';
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
                echo    '<table>
                            <tr>
                                <th>Имя спортсмена</th>
                                <th>Вид спорта</th>
                                <th>Название стадиона</th>
                                <th>Дата проведения</th>
                                <th colspan=2>
                                    <button type="button" onclick="toggleFilter()">
                                        <img src="https://cdn-icons-png.flaticon.com/512/107/107799.png" alt="filters" width="20" height="20">
                                    </button>
                                </th>
                            <tr>';
                foreach ($values as $value) {
                    echo    '<tr>';
                    echo        '<td>';
                                    $stmt = $db->prepare("SELECT id, name FROM Sportsmans");
                                    $stmt->execute();
                                    $Sportsmans = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo            '<select'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                    else print(" "); echo 'name="SportsmanID'.$value['id'].'">';
                                        foreach ($Sportsmans as $sportsman) {
                                            if ($sportsman['id'] == $value['SportsmanID']) {
                                                printf('<option selected value="%d">%d. %s</option>', $sportsman['id'], $sportsman['id'], $sportsman['name']);
                                            } else {
                                                printf('<option value="%d">%d. %s</option>', $sportsman['id'], $sportsman['id'], $sportsman['name']);
                                            }
                                        }
                    echo            '</select>';
                    echo        '</td>';

                    echo        '<td>';
                                    $stmt = $db->prepare("SELECT id, name FROM Sports");
                                    $stmt->execute();
                                    $Sports = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo            '<select'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                    else print(" "); echo 'name="SportId'.$value['id'].'">';
                                        foreach ($Sports as $sport) {
                                            if ($sport['id'] == $value['SportId']) {
                                                printf('<option selected value="%d">%d. %s</option>', $sport['id'], $sport['id'], $sport['name']);
                                            } else {
                                                printf('<option value="%d">%d. %s</option>', $sport['id'], $sport['id'], $sport['name']);
                                            }
                                        }
                    echo            '</select>';
                    echo        '</td>';

                    echo        '<td>';
                                    $stmt = $db->prepare("SELECT id, name FROM Stadiums");
                                    $stmt->execute();
                                    $Stadiums = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo            '<select'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                    else print(" "); echo 'name="StadiumID'.$value['id'].'">';
                                        foreach ($Stadiums as $stadium) {
                                            if ($stadium['id'] == $value['StadiumID']) {
                                                printf('<option selected value="%d">%d. %s</option>', $stadium['id'], $stadium['id'], $stadium['name']);
                                            } else {
                                                printf('<option value="%d">%d. %s</option>', $stadium['id'], $stadium['id'], $stadium['name']);
                                            }
                                        }
                    echo            '</select>';
                    echo        '</td>';

                    echo        '<td> <input'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                                                else print(" "); echo 'type="date" name="date'.$value['id'].'" value="'.$value['date'].'"> 
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