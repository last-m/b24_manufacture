// our application constructor
function application () {
}

/* сохраняем текущую ширину фрейма */
application.prototype.saveFrameWidth = function () {
    this.FrameWidth = document.getElementById("app").offsetWidth;
}

/* изменяем размер фрейма */
application.prototype.resizeFrame = function () {

    var currentSize = BX24.getScrollSize();
    minHeight = currentSize.scrollHeight;

    if (minHeight < 400) minHeight = 1800;
    BX24.resizeWindow(this.FrameWidth, minHeight);
}

// /* Выбор пользователя */
// application.prototype.addSelectUser = function() {
//     idUser = 8292;
//     console.log("idUSER = ", idUser);
//     $('#btn_manager').html(idUser);
//     /*BX24.selectUser(function(users) {
//         idUser = users.id;
//         console.log("idUSER = ", idUser);
//         $('#btn_manager').html(users.name);
//     });*/
// }



application.prototype.buttonRun = function(userId,pole){
    console.log(userId,pole);

    document.getElementById( document.getElementById('btn_generation').title).style.background = '#ffc107';
    document.getElementById('btn_detailing'+userId+'_'+pole).style.background = 'linen';

    document.getElementById('btn_generation').title = 'btn_detailing'+userId+'_'+pole;




    var dateFrom = 'dateFrom=' + from.value;
    var dateTo = 'dateTo=' + to.value;
    // 2. Создание переменной request
    var request = new XMLHttpRequest();

    // 3. Настройка запроса
    request.open('POST','detailing.php',true);
    // 4. Подписка на событие onreadystatechange и обработка его с помощью анонимной функции
    request.addEventListener('readystatechange', function() {
        //если запрос пришёл и статус запроса 200 (OK)
        if ((request.readyState==4) && (request.status==200)) {
            // например, выведем объект XHR в консоль браузера
            console.log(request);
            // и ответ (текст), пришедший с сервера в окне alert
            console.log(request.responseText);
            // получить элемент c id = welcome
            var detailing = document.getElementById('detailing');
            // заменить содержимое элемента ответом, пришедшим с сервера
            //$('#sum-tasks').html(htmlSumTasks);
            detailing.innerHTML = request.responseText;
            app.resizeFrame();
        }
    });
    // Устанавливаем заголовок Content-Type(обязательно для метода POST). Он предназначен для указания кодировки, с помощью которой зашифрован запрос. Это необходимо для того, чтобы сервер знал как его раскодировать.
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    // 5. Отправка запроса на сервер. В качестве параметра указываем данные, которые необходимо передать (необходимо для POST)
    request.send('userId=' + userId + '&' + 'pole=' + pole + '&' + dateFrom + '&' + dateTo);
}

/* Нажатие кнопки сформировать */
application.prototype.runGeneration = function(){
    var curapp = this;

    if (from.value.length == 0){
        $('#alert-warning').html('<div class="alert alert-warning" role="alert">Выберите период для отчета  !!!</div>');
        console.log('сотрудник');
    } else {
        $('#alert-warning').html('');
        $('#sum-tasks').html('');
        $('#table-deals').html('');
        arrAllData = [];
        idTasks = [];

        console.log('DATA-   ', from.value);
        console.log('DATA-   ', to.value);


        //1. Сбор данных, необходимых для выполнения запроса на сервере
        // var name = document.getElementById('name').value;
        //Подготовка данных для отправки на сервер
        //т.е. кодирование с помощью метода encodeURIComponent
        //var name = 'nameUser=' + idUser;
        var dateFrom = 'dateFrom=' + from.value;
        var dateTo = 'dateTo=' + to.value;

        //console.log(dateFrom);
       // console.log(dateTo);

        // 2. Создание переменной request
        var request = new XMLHttpRequest();

        // 3. Настройка запроса
        request.open('POST','get_data.php',true);
        // 4. Подписка на событие onreadystatechange и обработка его с помощью анонимной функции
        request.addEventListener('readystatechange', function() {
            //если запрос пришёл и статус запроса 200 (OK)
            if ((request.readyState==4) && (request.status==200)) {
                // например, выведем объект XHR в консоль браузера
                console.log(request);
                // и ответ (текст), пришедший с сервера в окне alert
                console.log(request.responseText);
                // получить элемент c id = welcome
                var welcome = document.getElementById('sum-tasks');
                // заменить содержимое элемента ответом, пришедшим с сервера
                //$('#sum-tasks').html(htmlSumTasks);
                welcome.innerHTML = request.responseText;
                app.resizeFrame();            }
        });
        // Устанавливаем заголовок Content-Type(обязательно для метода POST). Он предназначен для указания кодировки, с помощью которой зашифрован запрос. Это необходимо для того, чтобы сервер знал как его раскодировать.
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
        // 5. Отправка запроса на сервер. В качестве параметра указываем данные, которые необходимо передать (необходимо для POST)
        request.send(dateFrom + '&' + dateTo);

    };




//     curapp.userTaskList(new Date(month_input.value), new Date(month_input.value.substr(0,5) + (parseInt(month_input.value.substr(5,2)) + 1)));
}

// create our application
app = new application();

var arrAllData = [], idTasks = [];
var urlPortal = 'https://corp.amper.by/';
var idUser = 1;

var arDealStage = {}; // Стадии Сделок ВСЕ!



/* *** Отключенное *** */
/* Получение список направлений сделок */
application.prototype.dealСategory = function(){
    var idDealCategory = [], dealCategory = [];

    BX24.callMethod(
        "crm.dealcategory.list",
        {
            order: { "SORT": "ASC" },
            filter: { "IS_LOCKED": "N" },
            select: [ "ID", "NAME"]
        },
        function(result){
            if(result.error())
                console.error(result.error());
            else
            {
                var data = result.data();

                for (indexDealCategory in data) {
                    dealCategory.push({ID: data[indexDealCategory].ID, NAME: data[indexDealCategory].NAME});
                    idDealCategory.push(data[indexDealCategory].ID);
                }

                if(result.more())
                    result.next();
                else
                    app.importDealCategory(idDealCategory, dealCategory);
            }
        }
    );
}


/* Получение Сделок по ИД */
application.prototype.tasksDeals = function (idTasks, dataStart, dataEnd) {
    var curapp = this;
    var sumDealVictory = 0, dealVictory = 0, dealVictoryTask = 0;
    var dealLose = 0, dealLoseTask = 0;
    var dealWork = 0, dealWorkTask = 0;
    var dealNew = 0, dealNewTask = 0;
    var htmlDealWork = '', htmlDealVictory = '', htmlDealLose = '', htmlSumTasks = '';
    var creater = [];

    BX24.callMethod(
        "crm.deal.list",{
            order: { "DATE_CREATE": "ASC" },
            filter: { "ID": idTasks },            // Напровление сделки
            select: [ "ID", "COMPANY_ID", "TITLE", "CLOSEDATE", "CLOSED", "STAGE_ID", "OPPORTUNITY", "DATE_CREATE"]
        },
        function(result)
        {
            if (result.error()) {
                curapp.displayErrorMessage('К сожалению, произошла ошибка получения сделок. Попробуйте повторить отчет позже');
                console.error(result.error());
            }
            else{
                var data = result.data();

                for (indexDeal in data) {
                    for (indexTask in arrAllData) {
                        if (arrAllData[indexTask].UF_CRM_TASK == data[indexDeal].ID)
                            creater = arrAllData[indexTask].CREATOR;
                    }

                    var dateCreateDeal = new Date(data[indexDeal].DATE_CREATE);
                    if (dateCreateDeal.getMonth() >= dataStart.getMonth() && dateCreateDeal.getMonth() <= dataEnd.getMonth())
                        dealNew += 1;

                    if (data[indexDeal].CLOSED == 'N'){
                        dealWork += 1;
                        dealWorkTask += curapp.dealsInTask(data[indexDeal].ID); // Сколько Задачь к данной сделке прикреплено
                        htmlDealWork += curapp.generationTable("table-warning", data[indexDeal].ID, data[indexDeal].TITLE, data[indexDeal].OPPORTUNITY, creater, data[indexDeal].STAGE_ID);

                    }else{
                        if(data[indexDeal].STAGE_ID.substr(4,3)=='WON'){
                            dealVictory += 1;
                            dealVictoryTask += curapp.dealsInTask(data[indexDeal].ID);
                            sumDealVictory += parseFloat(data[indexDeal].OPPORTUNITY);
                            htmlDealVictory += curapp.generationTable("table-success", data[indexDeal].ID, data[indexDeal].TITLE, data[indexDeal].OPPORTUNITY, creater, data[indexDeal].STAGE_ID);

                            console.log(data[indexDeal]);
                        }else{
                            dealLose += 1;
                            dealLoseTask += curapp.dealsInTask(data[indexDeal].ID);
                            htmlDealLose += curapp.generationTable("table-danger", data[indexDeal].ID, data[indexDeal].TITLE, data[indexDeal].OPPORTUNITY, creater, data[indexDeal].STAGE_ID);
                        }
                    }
                }

                if (result.more())
                    result.next();
                else {

                    for(indexTS in arrAllData) {
                        var dateCreateTask = new Date(arrAllData[indexTS].CREATED_DATE);
                        if (dateCreateTask.getMonth() >= dataStart.getMonth() && dateCreateTask.getMonth() <= dataEnd.getMonth())
                            dealNewTask += 1;
                    }


                    $('#sum-tasks').html(htmlSumTasks);
                    $('#table-deals').html('<table class="table table-bordered table-hover">' +
                        htmlDealWork +
                        htmlDealLose +
                        htmlDealVictory +
                        '</table>');

                    curapp.resizeFrame();       // изменение размера окна


                }
            }
        }
    );
}

/* *** Отключенное *** */
