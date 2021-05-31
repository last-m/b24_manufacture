<?php
    require("phpsqlinfo_dbinfo.php");

    // отрецательные напровления сделок
    global $dealcategory_bad;
    global $dealcategory_good;
    const PRIFIX_TABLE = 'manufacture';

    $dealcategory_bad = "'C16:LOSE', 'C16:1', 'C16:4','C16:5', 'C16:6', 'C16:7', 'C16:8', 'C16:APOLOGY',
                     'C2:LOSE', 'C2:ON_HOLD', 'C2:2', 'C2:4', 'C2:14', 'C2:15', 'C2:16', 'C2:17', 'C2:18',
                     'C26:LOSE', 'C26:2', 'C26:3', 'C26:4', 'C26:5', 'C26:6', 'C26:APOLOGY',
                     'C8:LOSE', 'C8:APOLOGY', 'C8:11'";
    $dealcategory_good = "'C16:WON', 'C2:WON', 'C26:WON', 'C8:WON'";

    function post_task($id, $title, $status, $groupId, $createrId, $createrName, $createdDate, $closedDate, $dealId, $responsibleId, $responsibleName, $responsibleIcon ){
        global $mysqli;

        $query = "INSERT INTO `task_".PRIFIX_TABLE."` (`id`, `title`, `status`, `groupId`, `createrId`, `createrName`, `createdDate`, `closedDate`, `dealId`, `responsibleId`, `responsibleName`, `responsibleIcon`) VALUES 
                  ('".$id."', '".$title."', '".$status."', '".$groupId."','".$createrId."','".$createrName."', '".$createdDate."', '".$closedDate."', '".$dealId."', '".$responsibleId."', '".$responsibleName."', '".$responsibleIcon."')";
        //$mysqli->query($query);
        if (!$mysqli->query($query)) {
//            printf("Сообщение ошибки: %s\n", $mysqli->error);
//            print_r("<br>".$query."</br>".$dealId."<br>");

            $query = "SELECT id FROM `task_".PRIFIX_TABLE."` WHERE `task_".PRIFIX_TABLE."`.`id` = " . $id;
            if (!$result = $mysqli->query($query)) {
                echo "Извините, возникла проблема в работе сайта.";
                echo "Ошибка: Наш запрос не удался и вот почему: \n";
                echo "Запрос: " . $query . "\n";
                echo "Номер ошибки: " . $mysqli->errno . "\n";
                echo "Ошибка: " . $mysqli->error . "\n";
                exit;
            }
            if ($result->num_rows > 0) {
                print_r("Данные обнавлены</br>");
                $query = "UPDATE `task_".PRIFIX_TABLE."` SET `title` = '" . $title . "', `status` = '" . $status .
                                "', `groupId` = '" . $groupId . "', `createrId` = '" . $createrId . "', `createrName` = '" . $createrName .
                                "', `createdDate` = '" . $createdDate . "', `dealId` = '" . $dealId . "', `responsibleId` = '" . $responsibleId .
                                "', `responsibleName` = '" . $responsibleName . "', `responsibleIcon` = '" . $responsibleIcon .
                            "' WHERE `task_".PRIFIX_TABLE."`.`id` = " . $id;
                if (!$result = $mysqli->query($query)) {
                    echo "Извините, возникла проблема при обновлении сделки.";
                    echo "Ошибка: Наш запрос не удался и вот почему: \n";
                    echo "Запрос: " . $query . "\n";
                    echo "Номер ошибки: " . $mysqli->errno . "\n";
                    echo "Ошибка: " . $mysqli->error . "\n";
                    exit;
                }
            }
        }
    }

    function post_deal($id, $title, $assigned_by_id, $stage_id, $date_create, $closed, $closedate, $opportunity, $company_id){
        global $mysqli;

        if (!strlen($opportunity))
            $opportunity = 0;

        $query = "INSERT INTO `deals` (`id`, `title`, `assigned_by_id`, `stage_id`, `date_create`, `closed`, 
                                       `closedate`, `opportunity`, `company_id`) VALUES 
                 ('".$id."', '".$title."', '".$assigned_by_id."', '".$stage_id."', '".$date_create."', '".$closed."', 
                  '".$closedate."', '".$opportunity."', '".$company_id."')";
        if (!$mysqli->query($query)) {
            printf("Сообщение ошибки: %s\n", $mysqli->error);
            print_r("<br>".$query."</br>".$id."<br>");

            $query = "SELECT id FROM `deals` WHERE `deals`.`id` = " . $id;
            if (!$result = $mysqli->query($query)) {
                echo "Извините, возникла проблема в работе сайта.";
                echo "Ошибка: Наш запрос не удался и вот почему: \n";
                echo "Запрос: " . $query . "\n";
                echo "Номер ошибки: " . $mysqli->errno . "\n";
                echo "Ошибка: " . $mysqli->error . "\n";
                exit;
            }
            if ($result->num_rows > 0) {
                print_r("Данные обнавлены");
                $query = "UPDATE `deals` SET `title` = '" . $title . "', `assigned_by_id` = '" .
                        $assigned_by_id . "', `stage_id` = '" . $stage_id . "', `date_create` = '" .
                        $date_create . "', `closed` = '" . $closed . "', `closedate` = '" . $closedate . "', `opportunity` = '" .
                        $opportunity . "', `company_id` = '" . $company_id . "' WHERE `deals`.`id` = " . $id;
                if (!$result = $mysqli->query($query)) {
                    echo "Извините, возникла проблема при обновлении сделки.";
                    echo "Ошибка: Наш запрос не удался и вот почему: \n";
                    echo "Запрос: " . $query . "\n";
                    echo "Номер ошибки: " . $mysqli->errno . "\n";
                    echo "Ошибка: " . $mysqli->error . "\n";
                    exit;
                }
            }
        }
    }

    // Загрузка задачь из Б24
    function task($dateCreated = '2018-01-01T00:00:00+03:00'){
        $func = 'tasks.task.list';
        $filter = array( "GROUP_ID" => 254, ">CREATED_DATE" => $dateCreated);
        $select = array( "ID", "TITLE", "STATUS", "GROUP_ID", "CREATED_BY", "CREATED_DATE", "RESPONSIBLE_ID", "CLOSED_DATE", "UF_CRM_TASK" );
        $queryUrl = 'https://corp.amper.by/rest/432/lnv4o2lk0lc2yj7g/'.$func;
        $queryData = http_build_query(
            array('order'=> array( "ID"      => "ASC" ),
                'filter' => $filter,
                'select' => $select,
                'start'  => -1
            ));

        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, 1);

        $i = 0;
        print_r($result['total']);
        foreach (current(current(getBatch($func, $filter, $select, $result['total']))) as $tasks){
            foreach (current($tasks) as $task){
               // print_r(substr($task["ufCrmTask"][0],2));
               // print_r("<br>");
                if (strlen($task["ufCrmTask"][0])){
                    post_task($task["id"],$task["title"],$task["status"],$task["groupId"],(int)$task["creator"]["id"],$task["creator"]["name"],$task["createdDate"],$task["closedDate"],substr($task["ufCrmTask"][0],2),$task["responsibleId"],$task["responsible"]["name"],$task["responsible"]["icon"]);
                    $i++;}

            }
        }
        print_r("</br>".$i);
    }

    // Загрузка сделок из Б24
    function deal($dealsId){
        $func = 'crm.deal.list';
        $filter = array( "ID" => $dealsId );
        $select = array( "ID", "TITLE", "ASSIGNED_BY_ID", "STAGE_ID", "DATE_CREATE", "CLOSED", "CLOSEDATE", "OPPORTUNITY", "COMPANY_ID");
        $i = 0;

        foreach (current(getBatch($func, $filter, $select, count($dealsId)))['result'] as $get){
            foreach ($get as $deals) {
                 post_deal($deals["ID"],
                    $deals["TITLE"],
                    $deals["ASSIGNED_BY_ID"],
                    $deals["STAGE_ID"],
                    $deals["DATE_CREATE"],
                    $deals["CLOSED"],
                    $deals["CLOSEDATE"],
                    $deals["OPPORTUNITY"],
                    $deals["COMPANY_ID"]);
                $i++;
            }
        }
        print_r("</br>".$i);
    }

    // Получение ID сделак, для обновления сделок в mysql
    function get_deal_id(){
        global $mysqli;

        // выбираем все ID сделак которые не закрыты или которых нет, а задачи по ним есть
        $query = "SELECT distinct dealId FROM `task_".PRIFIX_TABLE."` LEFT JOIN `deals` ON task_".PRIFIX_TABLE.".
            dealId = deals.id WHERE deals.closed ORDER BY `task_".PRIFIX_TABLE."`.`dealId` DESC";
        if (!$result = $mysqli->query($query)) {
            echo "Извините, возникла проблема в работе сайта.";
            echo "Ошибка: Наш запрос не удался и вот почему: \n";
            echo "Запрос: " . $query . "\n";
            echo "Номер ошибки: " . $mysqli->errno . "\n";
            echo "Ошибка: " . $mysqli->error . "\n";
            exit;
        }
        if ($result->num_rows === 0) {
            $query = "SELECT distinct dealId FROM `task_".PRIFIX_TABLE."`";
            if (!$result = $mysqli->query($query)) {
                echo "Извините, возникла проблема в работе сайта.";
                echo "Ошибка: Наш запрос не удался и вот почему: \n";
                echo "Запрос: " . $query . "\n";
                echo "Номер ошибки: " . $mysqli->errno . "\n";
                echo "Ошибка: " . $mysqli->error . "\n";
                exit;
            }
            if ($result->num_rows === 0) {
                echo "В Базе нет сделок";
                exit;
            }
        }

        print_r($result);
        $dealId = array();
        while ($id = $result->fetch_assoc()){
            array_push($dealId,current($id));
        }
        print_r(count($dealId)."</br>");
        deal($dealId);
    }

    // запрос к базе на калличество
    function query($query){
        global $mysqli;

        if (!$result = $mysqli->query($query)) {
            echo "Извините, возникла проблема в работе сайта.";
            echo "Ошибка: Наш запрос не удался и вот почему: \n";
            echo "Запрос: " . $query . "\n";
            echo "Номер ошибки: " . $mysqli->errno . "\n";
            echo "Ошибка: " . $mysqli->error . "\n";
            exit;
        }
        if ($result->num_rows === 0) {
            echo "За выбронный периуд по данному менаджеру нет данных";
            return 0;
        } else {
            foreach ($result as $co){
                return current($co);
            }
        }
    }

    function getBatch($func, $filter, $select, $record_count) {
        $batch = array();
        for ($i = 1; $i <= ceil($record_count / 50); $i++) {
            $batch['get_'.$i] =
                $func.'?'.http_build_query(
                    array('order'=> array( "ID"      => "ASC" ),
                        'filter' => $filter,
                        'select' => $select,
                        "start" => '$next[get_'.($i-1).']'
                    )
                );
        }
        return executeHook(array('cmd' => $batch));
    }

    function executeHook($params) {
        $queryUrl = 'https://corp.amper.by/rest/432/lnv4o2lk0lc2yj7g/batch.json';
        $queryData = http_build_query($params);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    //**** Переписаные свойства ***//

    // Загружаем все открытые сделки + сделки закрытые в выбраный периуд
    function get_deal($filter){
        $func = 'crm.deal.list';
        $select = array( "ID", "TITLE", "ASSIGNED_BY_ID", "STAGE_ID", "DATE_CREATE", "CLOSED", "CLOSEDATE", "OPPORTUNITY", "COMPANY_ID");
        $queryUrl = 'https://corp.amper.by/rest/432/lnv4o2lk0lc2yj7g/'.$func;
        $queryData = http_build_query(
            array('order'=> array( "ID"      => "ASC" ),
                'filter' => $filter,
                'select' => $select
            ));

        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, 1);

        $i = 0;

        print_r('</br> всего сделок: '.$result['total'].'</br>');

        foreach (getBatch($func, $filter, $select, $result['total']) as $get){
            foreach ($get as $deals) {
                //print_r($i . ' ' . $deals["ID"].' '.$deals["TITLE"] . '</br></br></br></br>');
                post_deal($deals["ID"],
                    $deals["TITLE"],
                    $deals["ASSIGNED_BY_ID"],
                    $deals["STAGE_ID"],
                    $deals["DATE_CREATE"],
                    $deals["CLOSED"],
                    $deals["CLOSEDATE"],
                    $deals["OPPORTUNITY"],
                    $deals["COMPANY_ID"]);
                $i++;
            }
        }

        //exit();
        print_r("</br>Загрузка сделок закончена</br>".$i);
    }

    function cleaning_deals(){
        $owner = get_deal_id_mysql(1);
        $number_deal = count($owner);

        $func = 'crm.deal.list';
        $filter = array("ID" => $owner);
        $select = array("ID");

        foreach (getBatch($func, $filter, $select, count($owner)) as $get) {
            foreach ($get as $deals) {
                for ($j = 0; $j < $number_deal; $j++) {
                    if ($deals["ID"] === $owner[$j])
                        unset($owner[$j]);
                }
            }
        }

        $id_deal = '';
        if (count($owner) > 0) {
            foreach ($owner as $deal) {
                if ($id_deal === '')
                    $id_deal = $id_deal . $deal;
                else
                    $id_deal = $id_deal . ', ' . $deal;
            }

            $query = "DELETE FROM deals WHERE id IN (" . $id_deal . ")";
            global $mysqli;

            if (!$result = $mysqli->query($query)) {
                echo "Извините, возникла проблема в работе сайта.";
                echo "Ошибка: Наш запрос не удался и вот почему: \n";
                echo "Запрос: " . $query . "\n";
                echo "Номер ошибки: " . $mysqli->errno . "\n";
                echo "Ошибка: " . $mysqli->error . "\n";
                exit;
            }
        }
        print_r("</br>Сделки удаленные из Б24 удалены из отчета!!!</br>");
    }
