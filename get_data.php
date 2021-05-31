<?php
    // Подключение к базе
    require("phpsqlinfo_dbinfo.php");
    require("functions.php");

    if (isset($_POST['dateFrom'])) {
        $dateFrom = $_POST['dateFrom'];
        $dateTo = $_POST['dateTo'];
        //print_r($dateFrom. "</br>".$dateTo);

        $dateFrom = new DateTime($dateFrom);
        $dateTo = new DateTime($dateTo);


        // Получение списка сотрудников за выбраный периуд
        //$query = "SELECT DISTINCT responsibleId, responsibleName FROM `task_".PRIFIX_TABLE."` WHERE createdDate > '" . $dateFrom->Format(DATE_ATOM) . "' AND `createdDate` < '" . $dateTo->Format(DATE_ATOM) . "'";
        $query = "SELECT DISTINCT responsibleId, responsibleName FROM `task_".PRIFIX_TABLE."`";
        global $mysqli;
        $tableResult = array();

        if (!$result = $mysqli->query($query)) {
            echo "Извините, возникла проблема в работе сайта.";
            echo "Ошибка: Наш запрос не удался и вот почему: \n";
            echo "Запрос: " . $query . "\n";
            echo "Номер ошибки: " . $mysqli->errno . "\n";
            echo "Ошибка: " . $mysqli->error . "\n";
            exit;
        }
        if ($result->num_rows === 0) {
            echo "За выбронный периуд по данных нет";
            exit();
        } else {
            $NumTable = 1;
            foreach (['Изготовление','Расчет стоимости'] as $filtrTask) {

                $tableResultManager = array();
                array_push($tableResultManager, $NumTable);
                array_push($tableResultManager, $filtrTask);
                // Количество запросов тех помощи запрошеных в текущем периуде
                $query = "SELECT COUNT(*) FROM `task_".PRIFIX_TABLE."` WHERE `createdDate` > '" . $dateFrom->Format(DATE_ATOM) .
                    "' AND `createdDate` < '" . $dateTo->Format(DATE_ATOM) . "' AND `title` LIKE '%[ ".$filtrTask." ]%' ";
                    // удалить менаджера
//                    . " AND `responsibleId` = " . $manager['responsibleId'];
                array_push($tableResultManager, query($query));

                // Количество закрытых сделок за выброный период
                // ******Колличество задач в работе
                $query = "SELECT COUNT(*) FROM `task_".PRIFIX_TABLE."` WHERE `closedDate` > '" . $dateFrom->Format(DATE_ATOM) .
                    "' AND `closedDate` < '" . $dateTo->Format(DATE_ATOM) . "' AND `status` = '5' AND `title` LIKE '%[ ".$filtrTask." ]%'";
                array_push($tableResultManager, query($query));

                // Колличество задач в работе
                $query = "SELECT COUNT(*) FROM `task_".PRIFIX_TABLE."` WHERE (`status` = '2' OR `status` = '3' OR `status` = '4') AND `title` LIKE '%[ ".$filtrTask." ]%'";
                array_push($tableResultManager, query($query));

                // Колличество сделок в работе сейчас
                $query = "SELECT COUNT(DISTINCT dealId) FROM `task_".PRIFIX_TABLE."` INNER JOIN `deals` ON task_".PRIFIX_TABLE." . 
                        dealId = deals.id WHERE deals.closed = 'N' AND task_".PRIFIX_TABLE.".title LIKE '%[ ".$filtrTask." ]%'";
                array_push($tableResultManager, query($query));

                // Колличество проигранных сделок за периуд
                $query = "SELECT COUNT(*) FROM `task_".PRIFIX_TABLE."` INNER JOIN `deals` ON task_".PRIFIX_TABLE.".dealId = deals.id WHERE  deals.closed = 'Y' 
                                                                                                            AND deals.stage_id IN (" . $dealcategory_bad . ")
                                                                                                            AND task_".PRIFIX_TABLE.".title LIKE '%[ ".$filtrTask." ]%'
                                                                                                            AND deals.closedate > '" . $dateFrom->Format(DATE_ATOM) . "'
                                                                                                            AND deals.closedate < '" . $dateTo->Format(DATE_ATOM) . "'";
                array_push($tableResultManager, query($query));

                // Колличество выигранных сделок за периуд
                $query = "SELECT COUNT(*) FROM `task_".PRIFIX_TABLE."` INNER JOIN `deals` ON task_".PRIFIX_TABLE.".dealId = deals.id WHERE  deals.closed = 'Y' 
                                                                                                                    AND deals.stage_id IN (" . $dealcategory_good . ")
                                                                                                                    AND task_".PRIFIX_TABLE.".title LIKE '%[ ".$filtrTask." ]%'
                                                                                                                    AND deals.closedate > '" . $dateFrom->Format(DATE_ATOM) . "'
                                                                                                                    AND deals.closedate < '" . $dateTo->Format(DATE_ATOM) . "'";
                array_push($tableResultManager, query($query));
                array_push($tableResult, $tableResultManager);
                $NumTable += 1;
            }
        }

        $param = array('','', 'Задач создано (за период)', 'Задач выполнены (за период)', 'Задач в работе (сейчас)', 'Сделок в работе (сейчас)', 'Сделок проиграно (за период)', 'Сделок выиграно (за период)');
        $html = '<table><tbody>';

        for ($i = 1; $i < 8; $i++) {
            $html = $html . '<tr><td>' . $param[$i] . '</td>';
            foreach ($tableResult as $pole) {
                if ($pole[2]+$pole[3]+$pole[4]+$pole[5]+$pole[6]){
                    if($i === 1)
                        $html = $html . '<td>' . $pole[$i] . '</td>';
                    else
                        $html = $html . '<td><a target="_top" class="btn btn-block btn-lg btn-warning" onclick="app.buttonRun('.$pole[0].', '.$i.');" id="btn_detailing'.$pole[0].'_'.$i.'">' . $pole[$i] . '</a></td>';
                }
            }
            $html = $html . '</tr>';
        }
        $html = $html . '</tbody></table>';
        echo $html;
    }else {
        print_r("Test");
    }