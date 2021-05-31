<?php
    // Подключение к базе
    require("phpsqlinfo_dbinfo.php");
    require("functions.php");

    $date = new DateTime('-120 days');
    task($date->Format(DATE_ATOM));
    sleep(2);
    get_deal_id();

    //Очитска удаленных сделок
   // cleaning_deals();

//    get_deal(array( "CLOSED" => 'Y', ">CLOSEDATE" => $date->Format(DATE_ATOM)));
//    print_r("Импорт сделок 1 завершон <br>");
//    sleep(2);
//
//    //*Импорт всех открытых сделок
//    get_deal(array( "CLOSED" => 'N'));
//    print_r("Импорт сделок 2 завершон <br>");
//    sleep(2);

