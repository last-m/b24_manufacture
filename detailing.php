<?php
    require("phpsqlinfo_dbinfo.php");
    require("functions.php");

    function task_list($fTask, $dateFrom = '', $dateTo = '', $pole = ''){
        global $mysqli;

        if ($fTask === 1){
            $filtrTask = 'Изготовление';}
        else {$filtrTask = 'Расчет стоимости';}

        $query = "SELECT id, title, responsibleName, createrId, createrName FROM `task_".PRIFIX_TABLE."` 
                  WHERE (`status` = '2' OR `status` = '3' OR `status` = '4') AND `title` LIKE '%[ ".$filtrTask." ]%'";

        if($dateFrom != '')
            $query = "SELECT id, title, responsibleName, createrId, createrName FROM `task_".PRIFIX_TABLE."` 
                WHERE `createdDate` > '" . $dateFrom->Format(DATE_ATOM) . "' AND `createdDate` < '" . $dateTo->Format(DATE_ATOM) . "' AND `title` LIKE '%[ ".$filtrTask." ]%'";

        if($pole != '')
            $query = "SELECT id, title, responsibleName, createrId, createrName FROM `task_".PRIFIX_TABLE."` 
                WHERE `closedDate` > '" . $dateFrom->Format(DATE_ATOM) . "' AND `closedDate` < '" . $dateTo->Format(DATE_ATOM) . "' AND `status` = '5' AND `title` LIKE '%[ ".$filtrTask." ]%'";

        if (!$result = $mysqli->query($query)) {
            echo "Извините, возникла проблема в работе сайта.";
            echo "Ошибка: Наш запрос не удался и вот почему: \n";
            echo "Запрос: " . $query . "\n";
            echo "Номер ошибки: " . $mysqli->errno . "\n";
            echo "Ошибка: " . $mysqli->error . "\n";
            exit;
        }

        if ($result->num_rows === 0) {
            echo "За выбронный периуд данных нет";
            exit();
        }

        $html = '<table><tbody>' .
            '<tr><td>ID</td><td>Название</td><td>Постановщик</td></tr>';
        foreach ($result as $res) {
            $html = $html . '<tr><td>[' . $res['id'] . ']</td><td><a href="https://corp.amper.by/company/personal/user/432/tasks/task/view/' .
                $res['id'] . '/" target="_blank">' . $res['title'] . '</a></td><td><a href="https://corp.amper.by/company/personal/user/' .
                $res['createrId'] . '/" target="_blank">' . $res['createrName'] .'</a></td>';
            if ($res['closed'] === "Y") {
                $html = $html . '<td>' . $res['closedate'] . '</td>';
            } else
                $html = $html . '<td></td>';
            $html = $html . '</tr>';
        }
        $html = $html . '</tbody></table>';
        return $html;
    }

    function deal_list($dealCategory, $fTask, $dateFrom, $dateTo){
        global $mysqli;

        if ($fTask === 1){
            $filtrTask = 'Изготовление';}
        else {$filtrTask = 'Расчет стоимости';}

        $query = "SELECT `deals`.`id`, `deals`.`title`, `deals`.`stage_id`, `deals`.`opportunity`, 
                         `deals`.`date_create`, `deals`.`closed`, `deals`.`closedate` 
                    FROM `task_".PRIFIX_TABLE."` INNER JOIN `deals` ON task_".PRIFIX_TABLE.".dealId = deals.id 
                    WHERE  deals.closed = 'Y' 
                        AND deals.stage_id IN ("    . $dealCategory . ")
                        AND task_".PRIFIX_TABLE.".title  LIKE '%[ ".$filtrTask." ]%'
                        AND deals.closedate > '"    . $dateFrom->Format(DATE_ATOM) . "'
                        AND deals.closedate < '"    . $dateTo->Format(DATE_ATOM) . "'";

        if($dealCategory === '')
            $query = "SELECT DISTINCT `deals`.`id`, `deals`.`title`, `deals`.`stage_id`, `deals`.`opportunity`, 
                         `deals`.`date_create`, `deals`.`closed`, `deals`.`closedate` 
                         FROM `task_".PRIFIX_TABLE."` INNER JOIN `deals` ON task_".PRIFIX_TABLE.".dealId = deals.id 
                         WHERE deals.closed = 'N' AND task_".PRIFIX_TABLE.".title  LIKE '%[ ".$filtrTask." ]%'";

        if (!$result = $mysqli->query($query)) {
            echo "Извините, возникла проблема в работе сайта.";
            echo "Ошибка: Наш запрос не удался и вот почему: \n";
            echo "Запрос: " . $query . "\n";
            echo "Номер ошибки: " . $mysqli->errno . "\n";
            echo "Ошибка: " . $mysqli->error . "\n";
            exit;
        }

        if ($result->num_rows === 0) {
            echo "За выбронный периуд данных нет";
            exit();
        }
        $html = '<table><tbody>' .
            '<tr><td>ID</td><td>Название</td><td>Сумма</td><td>Создана</td><td>Закрыта</td></tr>';
        foreach ($result as $res) {
            $html = $html . '<tr><td>[' . $res['id'] . ']</td><td><a href="https://corp.amper.by/crm/deal/details/' .
                    $res['id'] . '/?IFRAME=Y" target="_blank">' . $res['title'] . '</a></td><td>' . $res['opportunity'] .
                    '</td><td>' . $res['date_create'] . '</td>';
            if ($res['closed'] === "Y") {
                $html = $html . '<td>' . $res['closedate'] . '</td>';
            } else
                $html = $html . '<td></td>';
            $html = $html . '</tr>';
        }
        $html = $html . '</tbody></table>';
        return $html;
    }

    // если в массиве пост есть ключ userId, то
    if (isset($_POST['userId'])) {
        $userId = (int)$_POST['userId'];
        $pole = (int)$_POST['pole'];
        $dateFrom = $_POST['dateFrom'];
        $dateTo = $_POST['dateTo'];

        $dateFrom = new DateTime($dateFrom);
        $dateTo = new DateTime($dateTo);

        if ($pole === 7) {
            // Выигранные сделоки за периуд
            echo(deal_list($dealcategory_good, $userId, $dateFrom, $dateTo));
        } elseif ($pole === 6) {
            echo(deal_list($dealcategory_bad, $userId, $dateFrom, $dateTo));
        } elseif ($pole === 5) {
            echo(deal_list('', $userId, $dateFrom, $dateTo));
        } elseif ($pole === 4){
            echo (task_list($userId));
        } elseif ($pole === 3){
            echo (task_list($userId, $dateFrom, $dateTo, $pole));
        } elseif ($pole === 2){
            echo (task_list($userId, $dateFrom, $dateTo));
        }

        exit();

    }