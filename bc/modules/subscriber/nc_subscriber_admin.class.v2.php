<?php

/* $Id: nc_subscriber_admin.class.v2.php 8355 2012-11-07 11:31:34Z lemonade $ */

/**
 * Класс для работы с модулем в "админке"
 */
class nc_subscriber_admin {

    protected $db;
    protected $UI_CONFIG, $ADMIN_TEMPLATE, $MODULE_FOLDER, $ADMIN_PATH;
    protected $_settings;
    protected $core, $nc_subscriber;
    protected $_file1_id, $_file_id2, $_file_id3;
    protected $_files_id;

    public function __construct() {
        // global variables
        global $db, $UI_CONFIG, $MODULE_VARS, $ADMIN_PATH, $ADMIN_TEMPLATE, $MODULE_FOLDER;
        $this->core = nc_Core::get_object();
        // global variables to internal
        $this->db = & $db;
        $this->UI_CONFIG = & $UI_CONFIG;
        $this->ADMIN_PATH = $ADMIN_PATH;
        $this->ADMIN_TEMPLATE = $ADMIN_TEMPLATE;
        $this->MODULE_FOLDER = $MODULE_FOLDER;
        // superglobal variable
        $this->POST = & $_POST;

        $this->nc_subscriber = nc_subscriber::get_object();
        $this->_settings = $this->nc_subscriber->tools->get_settings();

        $this->_files_id[1] = intval($_POST['file1_id']);
        $this->_files_id[2] = intval($_POST['file2_id']);
        $this->_files_id[3] = intval($_POST['file3_id']);

        return;
    }

    /**
     * Функция выводит форму с настройками модуля
     */
    public function settings() {

        echo "
    <form action='admin.php' method='post' enctype='multipart/form-data'>
      <fieldset>
        <legend>" . NETCAT_MODULE_SUBSCRIBER_MAILER_SETTINGS . ":</legend>";
        $this->_checkbox(NETCAT_MODULE_SUBSCRIBER_MERGE_MAIL, 'MergeMail', $this->_settings['MergeMail']);
        $this->_input_text(NETCAT_MODULE_SUBSCRIBER_MAX_MAIL, 'MaxMailCount', $this->_settings['MaxMailCount']);
        $this->_input_text(NETCAT_MODULE_SUBSCRIBER_FROM_NAME, 'FromName', $this->_settings['FromName']);
        $this->_input_text(NETCAT_MODULE_SUBSCRIBER_FROM_EMAIL, 'FromEmail', $this->_settings['FromEmail']);
        $this->_input_text(NETCAT_MODULE_SUBSCRIBER_REPLY_TO, 'ReplyEmail', $this->_settings['ReplyEmail']);
        $this->_input_text(NETCAT_MODULE_SUBSCRIBER_TEST_EMAIL, 'TestEmail', $this->_settings['TestEmail']);
        $this->_input_text(NETCAT_MODULE_SUBSCRIBER_CHARSET, 'Charset', $this->_settings['Charset']);

        // вывод парметра "поле с адресом"
        echo " <div style='margin:10px 0; _padding:0;'>\n
      " . NETCAT_MODULE_SUBSCRIBER_EMAIL_FIELD . ": <br>";
        $fields = $this->db->get_results("SELECT  `Field_Name` AS `name`
                                        FROM `Field`
                                        WHERE `System_Table_ID` = '3'
                                        AND `TypeOfData_ID` = '1'
                                        AND `Format` LIKE 'email%'
                                        ORDER BY `Priority` ", ARRAY_A);
        if (empty($fields)) {
            echo NETCAT_MODULE_SUBSCRIBER_NONE_EMAIL_FIELD;
        } else {
            echo "<select name='EmailField'  style='width:50%'>\n";
            foreach ($fields as $field) {
                echo "<option value='" . $field['name'] . "' " . ($field['name'] == $this->_settings['EmailField'] ? "selected" : "") . ">" . $field['name'] . "</option>\n";
            }
            echo "</select>";
        }

        echo "</div>
      </fieldset>

      <fieldset>
        <legend>" . NETCAT_MODULE_SUBSCRIBER_SUBSCRIPTION_CONFIRM . ":</legend>
        <div style='margin:10px 0; _padding:0;'>\n
          " . NETCAT_MODULE_SUBSCRIBER_SUBSCRIPTION_CONFIRM . ": <br>
          <select name='ConfirmType'  style='width:50%'>\n
            <option value='0' " . ($this->_settings['ConfirmType'] == 0 ? "selected" : "") . ">" . NETCAT_MODULE_SUBSCRIBER_ONLY_UNREGISTERED . "</option>
            <option value='1' " . ($this->_settings['ConfirmType'] == 1 ? "selected" : "") . " >" . NETCAT_MODULE_SUBSCRIBER_FOR_ONE_SUBSCRIPTION . " </option>
            <option value='2' " . ($this->_settings['ConfirmType'] == 2 ? "selected" : "") . ">" . NETCAT_MODULE_SUBSCRIBER_ALWAYS . "</option>
          </select>\n
        </div>\n";

        $this->_input_text(NETCAT_MODULE_SUBSCRIBER_UNCONFIRMED_MAX_TIME, 'MaxTime', $this->_settings['MaxTime']);
        $this->_input_text(NETCAT_MODULE_SUBJECT, 'ConfirmSubject', $this->_settings['ConfirmSubject']);
        $this->_checkbox(NETCAT_MODULE_HTML_MAIL, 'ConfirmHTML', $this->_settings['ConfirmHTML']);
        $this->_textarea(NETCAT_MODULE_MAIL_BODY, 'ConfirmBody', $this->_settings['ConfirmBody'], 1);
        echo nc_mail_attachment_form('subscriber_confirm');

        echo "
      </fieldset>

      <fieldset>
        <legend>" . NETCAT_MODULE_SUBSCRIBER_OTHER . "</legend>";
        $this->_textarea(NETCAT_MODULE_SUBSCRIBER_SUBSCRIBE_FORM, 'FormSubscribe', $this->_settings['FormSubscribe'], 1);
        $this->_textarea(NETCAT_MODULE_SUBSCRIBER_TEXT_CONFIRM, 'TextConfirm', $this->_settings['TextConfirm'], 1);
        $this->_textarea(NETCAT_MODULE_SUBSCRIBER_TEXT_UNSCRIBE, 'TextUnscribe', $this->_settings['TextUnscribe'], 1);
        $this->_textarea(NETCAT_MODULE_SUBSCRIBER_TEXT_ERROR, 'TextError', $this->_settings['TextError'], 1);

        echo "
      </fieldset>
      <input type='submit' class='hidden'>
      <input type='hidden' name='phase' value='51' />
      </form>\n";

        $this->_js_resize();

        // admin buttons
        $this->UI_CONFIG->actionButtons[] = array(
            "id" => "submit",
            "caption" => NETCAT_MODULE_SUBSCRIBER_SAVE_BUTTON,
            "action" => "mainView.submitIframeForm()"
        );

        return 0;
    }

    /**
     * Функция для сохранения настройек модуля
     * @return <type>
     */
    public function settings_save() {
        // валидация данных
        $settings = array();
        $settings['MergeMail'] = intval($_POST['MergeMail']);
        $settings['FromName'] = $this->db->escape($_POST['FromName']);
        $settings['FromEmail'] = $this->db->escape($_POST['FromEmail']);
        $settings['ReplyEmail'] = $this->db->escape($_POST['ReplyEmail']);
        $settings['TestEmail'] = $this->db->escape($_POST['TestEmail']);
        $settings['Charset'] = $this->db->escape($_POST['Charset']);
        $settings['EmailField'] = $this->db->escape($_POST['EmailField']);
        $settings['ConfirmType'] = intval($_POST['ConfirmType']);
        $settings['ConfirmSubject'] = $this->db->escape($_POST['ConfirmSubject']);
        $settings['ConfirmBody'] = $this->db->escape($_POST['ConfirmBody']);
        $settings['ConfirmHTML'] = intval($_POST['ConfirmHTML']);
        $settings['TextConfirm'] = $this->db->escape($_POST['TextConfirm']);
        $settings['TextUnscribe'] = $this->db->escape($_POST['TextUnscribe']);
        $settings['TextError'] = $this->db->escape($_POST['TextError']);
        $settings['MaxMailCount'] = intval($_POST['MaxMailCount']);
        $settings['MaxTime'] = intval($_POST['MaxTime']);
        $settings['FormSubscribe'] = $this->db->escape($_POST['FormSubscribe']);

        // обновление (или вставка) настроек
        foreach ($settings as $key => $value) {
            if (isset($this->_settings[$key])) {
                $query = "UPDATE `Subscriber_Settings` SET `Value` = '" . $value . "' WHERE `Key` = '" . $key . "'";
            } else {
                $query = "INSERT INTO `Subscriber_Settings` (`Value`, `Key`) VALUES( '" . $value . "', '" . $key . "')";
            }
            $this->db->query($query);
        }
        // настройки нужно получить заново
        $nc_subscriber = nc_subscriber::get_object();
        $this->_settings = $nc_subscriber->tools->get_settings('', 1);

        nc_mail_attachment_form_save('subscriber_confirm');

        return 0;
    }

    /**
     * Функция выводит статистику всех рассылок
     */
    public function stats() {
        // выполнение основого запроса на статистику всех рассылок
        $this->db->last_error = '';
        $res = $this->db->get_results("SELECT sm.`Mailer_ID`, sm.`MailCount`, sm.`LastSend`, sm.`Name`,
                                          COUNT(su.`User_ID`) AS `UserCount`
                                   FROM ( `Subscriber_Mailer` AS `sm`)
                                   LEFT JOIN `Subscriber_Subscription` AS `su` ON su.`Mailer_ID` = sm.`Mailer_ID`
                                   GROUP BY sm.`Mailer_ID` ", ARRAY_A);
        // если в ходе запроса произошла ошибка - то выбросить исключение
        if ($this->db->last_error) {
            throw new ExceptionDB(__CLASS__ . '::' . __FUNCTION__);
        }
        // нет статистики
        if (empty($res)) {
            nc_print_status(NETCAT_MODULE_SUBSCRIBER_STATS_IS_EMPTY, 'info');
            return false;
        }

        echo "<table border='0' cellpadding='0' cellspacing='0' width='100%'>
            <tr><td >
               <table border='0' cellpadding='4' cellspacing='1' width='100%'>
                <tr>
                 <td >ID</td>
                 <td >" . NETCAT_MODULE_SUBSCRIBER_MAILER_NAME . "</td>
                 <td  align='center'>" . NETCAT_MODULE_SUBSCRIBER_SUBSCRIBERS_COUNT . "</td>
                 <td  align='center'>" . NETCAT_MODULE_SUBSCRIBER_MAIL_SEND_COUNT . "</td>
                 <td  align='center'>" . NETCAT_MODULE_SUBSCRIBER_LAST_SEND . "</td>
                </tr>";


        foreach ($res as $row) {
            // существует ли полная статистика для рассылки?
            $row['exist_full'] = $this->db->get_var("SELECT `Mailer_ID` FROM `Subscriber_Log` WHERE `Mailer_ID` = '" . $row['Mailer_ID'] . "'");
            echo "<tr>\n" .
                "<td font size=-2>" . $row['Mailer_ID'] . "</font></td>\n" .
                ($row['exist_full'] ?
                    "<td ><a href='admin.php?phase=4&amp;mailer_id=" . $row['Mailer_ID'] . "'>" . $row['Name'] . "</a></td>\n" :
                    "<td >" . $row['Name'] . "</td>\n"
                ) .
                "<td  align='center'>" . $row['UserCount'] . "</td>\n" .
                "<td  align='center'>" . $row['MailCount'] . "</td>\n" .
                "<td  align='center'>" . $row['LastSend'] . "</td>\n" .
                "</tr>";
        }

        echo "</table> </td> </tr></table>";

        return 0;
    }

    /**
     * Фуннкция выводит полную статистику одной рассылки
     * @param int $mailer_id
     */
    public function stats_mailer($mailer_id) {
        $mailer = intval($mailer);

        // выполнение основого запроса на статистику всех рассылок
        $this->db->last_error = '';
        $res = $this->db->get_results("SELECT sl.`Mailer_ID`, sl.`User_ID`, sl.`ActionTime`, sl.`ActionType`,
                                   u.`Login`, u.`UserType`, u.`" . $this->_settings['EmailField'] . "`
                                   FROM `Subscriber_Log` AS `sl` , `User` AS `u`
                                   WHERE sl.`Mailer_ID` = '" . intval($mailer_id) . "'
                                   AND u.`User_ID` = sl.`User_ID`
                                   ORDER BY sl.`ID` DESC ", ARRAY_A);

        // если в ходе запроса произошла ошибка - то выбросить исключение
        if ($this->db->last_error) {
            throw new ExceptionDB(__CLASS__ . '::' . __FUNCTION__);
        }
        // нет статистики
        if (empty($res)) {
            nc_print_status(NETCAT_MODULE_SUBSCRIBER_STATS_IS_EMPTY, 'info');
            return false;
        }

        $name = $this->nc_subscriber->get($mailer_id, 'Name');

        echo NETCAT_MODULE_SUBSCRIBER_FULL_STATS_MAILER . " <a href='admin.php?phase=2&mailer_id=" . $mailer_id . "'>" . $name . "</a>. <br/><br>";

        echo "<table border='0' cellpadding='0' cellspacing='0' width='100%'>
            <tr><td >
               <table border='0' cellpadding='4' cellspacing='1' width='100%'>
                <tr>
                 <td >" . NETCAT_MODULE_SUBSCRIBER_USER . "</td>
                 <td >" . NETCAT_MODULE_SUBSCRIBER_ACTION . "</td>
                 <td >" . NETCAT_MODULE_SUBSCRIBER_DATE_TIME . "</td>
                </tr>";


        foreach ($res as $row) {
            $user = (($row['UserType'] == 'normal') ? ("<a href='" . $this->ADMIN_PATH . "user/index.php?phase=4&UserID=" . $row['User_ID'] . "'>" . $row['Login'] . "</a>") :
                "" . NETCAT_MODULE_SUBSCRIBER_UNRESISTRED_USER . " (" . $row[$this->_settings['EmailField']] . ") ");

            $action = ($row['ActionType'] == 'mail' ? NETCAT_MODULE_SUBSCRIBER_MAIL_SEND :
                ($row['ActionType'] == 'subscribe' ? NETCAT_MODULE_SUBSCRIBER_SUBSCRIBE :
                    ($row['ActionType'] == 'confirm' ? NETCAT_MODULE_SUBSCRIBER_CONFIRM : NETCAT_MODULE_SUBSCRIBER_UNSUBSCRIBE)));
            echo "<tr>\n" .
                "<td >" . $user . "</td>\n" .
                "<td >" . $action . "</td>\n" .
                "<td >" . $row['ActionTime'] . "</td>\n" .
                "</tr>";
        }

        echo " </table>
          </td></tr> </table>";

        echo "<form action='admin.php' method='post'>
          <input type='hidden' name='mailer_id' value='" . $mailer_id . "' />
          <input type='hidden' name='phase' value='41' />
           </form>";

        // admin buttons
        $this->UI_CONFIG->actionButtons[] = array(
            "id" => "submit",
            "caption" => NETCAT_MODULE_SUBSCRIBER_CLEAR_BUTTON,
            "action" => "mainView.submitIframeForm('')",
            "red_border" => true,
        );

        return 0;
    }

    /**
     * Функция выводит список рассылок
     */
    public function mailer_list() {
        $nc_core = nc_Core::get_object();

        // валидация фильтров
        $filter_status = intval($_POST['filter_status']);
        $filter_type = intval($_POST['filter_type']);

        $this->db->last_error = '';
        $mailers = $this->db->get_results("SELECT sm.`Mailer_ID`, sm.`Name`, sm.`Type`, sm.`Active`,
                                              sc.`Sub_Class_Name`, sc.`Sub_Class_ID`, sc.`Subdivision_ID`, COUNT(us.`User_ID`) AS `Count`
                                        FROM (`Subscriber_Mailer` as sm)
                                        LEFT JOIN `Sub_Class` AS sc ON sc.`Sub_Class_ID` = sm.`Sub_Class_ID`
                                        LEFT JOIN `Subscriber_Subscription` as us  ON us.`Mailer_ID` = sm.`Mailer_ID`
                                        WHERE 1
                                        " . ($filter_status ? " AND sm.`Active` = '" . ($filter_status == 1 ? "1" : "0") . "'" : "") . "
                                        " . ($filter_type ? " AND sm.`Type` = '" . $filter_type . "'" : "") . "
                                        GROUP BY sm.`Mailer_ID` ", ARRAY_A);

        if ($this->db->last_error) {
            throw new ExceptionDB(__CLASS__ . '::' . __FUNCTION__);
        }

        // add button
        $this->UI_CONFIG->actionButtons[] = array(
            "id" => "add",
            "caption" => NETCAT_MODULE_SUBSCRIBER_MAILER_ADD,
            "location" => 'module.subscriber.mailer.add'
        );

        // если рассылок нет - то вывести плашку с сообщением, но не выходить из функции
        if (empty($mailers)) {
            nc_print_status(NETCAT_MODULE_MAILER_NO_ONE_MAILER, 'info');
        }

        // в любом случае нужно вывести "Выборка рассылок"
        echo "
        <fieldset>\n" .
            "<legend>" . NETCAT_MODULE_SUBSCRIBER_MAILER_FILTER . "</legend>\n" .
            "<form action='admin.php' method='post'>
            <table border='0'>
            <tr>
              <td>" . NETCAT_MODULE_SUBSCRIBER_MAILER_FILTER_TYPE . "</td>
          <td style='width:70%'>
            <select name='filter_type' style='width: 100%'>
              <option value='0' " . ($filter_type == 0 ? 'selected' : '') . ">" . NETCAT_MODULE_SUBSCRIBER_ALL . "</option>
              <option value='1' " . ($filter_type == 1 ? 'selected' : '') . ">" . NETCAT_MODULE_SUBSCRIBER_MAILER_CC . "</option>
              <option value='2' " . ($filter_type == 2 ? 'selected' : '') . ">" . NETCAT_MODULE_SUBSCRIBER_MAILER_PERIODICAL . "</option>
              <option value='3' " . ($filter_type == 3 ? 'selected' : '') . ">" . NETCAT_MODULE_SUBSCRIBER_MAILER_SERVICE . "</option>
            </select></td>
        </tr>
        <tr>
          <td>" . NETCAT_MODULE_SUBSCRIBER_MAILER_FILTER_STATUS . "</td>
          <td>
            <select name='filter_status' style='width: 100%'>
              <option value='0' " . ($filter_status == 0 ? 'selected' : '') . ">" . NETCAT_MODULE_SUBSCRIBER_ALL . "</option>
              <option value='1' " . ($filter_status == 1 ? 'selected' : '') . ">" . NETCAT_MODULE_SUBSCRIBER_ONLY_ACTIVE . "</option>
              <option value='2' " . ($filter_status == 2 ? 'selected' : '') . ">" . NETCAT_MODULE_SUBSCRIBER_ONLY_UNACTIVE . "</option>
            </select></td>
        </tr>
        <tr>
          <td colspan='2' align='center'>
            <input type='submit' title='" . NETCAT_MODULE_SUBSCRIBER_FILTER . "' value='" . NETCAT_MODULE_SUBSCRIBER_FILTER . "' />
          </td>
        </table>
        <input type='hidden' name='phase' value='1' />
        </form>
        
        </fieldset></div>";

        // если нет рассылок - то не имеет смысла выводить таблицу
        if (empty($mailers)) {
            return 0;
        }

        echo "
      <form action='admin.php' method='post' name='mainForm' id='mainForm'>
      <input type='hidden' name='phase' value='11' />
      <input type='hidden' name='action_type' value='' />
      <table border='0' cellpadding='0' cellspacing='0' width='100%'>
        <tr><td >
            <table class='admin_table' width='100%'>
              <tr>
                <td >ID</td>
                <td >" . NETCAT_MODULE_SUBSCRIBER_MAILER_NAME . "</td>
                <td >" . CONTROL_CONTENT_SUBDIVISION_FUNCS_SECTION . "</td>
                <td >" . NETCAT_MODULE_SUBSCRIBER_TYPE . "</td>
                <td  align='center'>" . NETCAT_MODULE_SUBSCRIBER_SUBSCRIBERS_COUNT . "</td>
                <td  align='center'>" . NETCAT_MODULE_SUBSCRIBER_SETTINGS . "</td>
                <td  align='center'>" . ($nc_core->get_settings('PacketOperations') ? "<div class='icons icon_type_bool'></div>" : "") . "</td>
                </tr>";


        foreach ($mailers as $mailer) {
            $mailer['TypeName'] = (($mailer['Type'] == 1) ? NETCAT_MODULE_SUBSCRIBER_MAILER_CC : (($mailer['Type'] == 2) ? NETCAT_MODULE_SUBSCRIBER_MAILER_PERIODICAL : NETCAT_MODULE_SUBSCRIBER_MAILER_SERVICE));
            if ($mailer['Type'] == 3) $mailer['Count'] = '';
            if (!$mailer['Type'] != 3 && $mailer['Count']) {
                $mailer['Count'] = "<a href='admin.php?phase=6&amp;mailer_id=" . $mailer['Mailer_ID'] . "'>" . $mailer['Count'] . "</a>";
            }
            echo "<tr>\n" .
                "<td >" . $mailer['Mailer_ID'] . "</td>\n" .
                "<td ><font " . (!$mailer['Active'] ? "style='color:#cccccc;'" : "") . ">" . $mailer['Name'] . "</font></td>\n" .
                "<td >" . ($mailer['Type'] == 1 ? "<a href='" . $this->ADMIN_PATH . "subdivision/SubClass.php?phase=3&SubClassID=" . $mailer['Sub_Class_ID'] . "&amp;SubdivisionID=" . $mailer['Subdivision_ID'] . "'>" . $mailer['Sub_Class_Name'] . "</a>" : "") . "</font></td>\n" .
                "<td >" . $mailer['TypeName'] . "</td>\n" .
                "<td  align='center' >" . $mailer['Count'] . "</td>\n" .
                "<td  align='center'><a href='admin.php?phase=2&amp;mailer_id=" . $mailer['Mailer_ID'] . "'><div class='icons icon_settings" . (!$mailer['Active'] ? "_disabled" : "") . "' title='" . NETCAT_MODULE_SUBSCRIBER_SETTINGS . "'></div></a></td>\n" .
                "<td  align='center'>" . ($nc_core->get_settings('PacketOperations') ? "<input type='checkbox' name='mailer_" . $mailer['Mailer_ID'] . "' value='" . $mailer['Mailer_ID'] . "' />" : "") . "</td>\n" .
                "</tr>";
        }
        echo "


          </table>
          </td>
        </tr>
      </table>
      </form>";
        echo "
    <script type='text/javascript'>\n
      function sumbit_form ( action_type ) {\n
        document.getElementById('mainForm').action_type.value =  action_type;\n
        parent.mainView.submitIframeForm('mainForm');\n
        return 0;\n
      }\n
    </script>\n";


        if ($nc_core->get_settings('PacketOperations')) {
            $this->UI_CONFIG->actionButtons[] = array(
                "id" => "submit",
                "caption" => NETCAT_MODULE_SUBSCRIBER_DELETESELECTED,
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form('delete')",
                "align" => "left",
                "red_border" => true,
            );
            $this->UI_CONFIG->actionButtons[] = array(
                "id" => "submit1",
                "caption" => NETCAT_MODULE_SUBSCRIBER_SELECTEDOFF,
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form('unactive')",
                "align" => "left"
            );
            $this->UI_CONFIG->actionButtons[] = array(
                "id" => "submit2",
                "caption" => NETCAT_MODULE_SUBSCRIBER_SELECTEDON,
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form('active')",
                "align" => "left"
            );
        }
    }

    /**
     * Функция выводит форму добавления/редактрования рассылки
     * @param int $mailer_id номер рассылки ( 0 при добавлении )
     */
    public function mailer_form($mailer_id = 0) {
        // нужны функции, типа  nc_list_select
        require_once($this->core->INCLUDE_FOLDER . 's_common.inc.php');
        // все парметры рассылки
        $keys = array('Name', 'Type', 'Sub_Class_ID', 'Active', 'Access',
            'InStat', 'Period', 'SubscribeCond', 'SendCond', 'ActionType', 'SubscribeAction',
            'Header', 'Footer', 'Record', 'Subject', 'HTML');
        $mailer_id = intval($mailer_id);

        if ($mailer_id) { // редактирование существующей рассылки
            // формирование запроса
            foreach ($keys as $key) {
                $query_select[] = "`" . $key . "`";
            }
            $this->db->last_error = '';
            $mailer = $this->db->get_row("SELECT " . join(',', $query_select) . "
                                    FROM `Subscriber_Mailer`
                                    WHERE `Mailer_ID` = '" . $mailer_id . "'", ARRAY_A);
            // обработка ошибок
            if ($this->db->last_error) {
                throw new ExceptionDB(__CLASS__ . '::' . __FUNCTION__);
            }
            if (empty($mailer)) {
                throw new Exception(NETCAT_MODULE_SUBSCRIBER_MAILER_DOES_NOT_EXIST);
            }
            // для подписки на раздел нужно узнать раздел и сайт
            if ($mailer['Type'] == 1 && $mailer['Sub_Class_ID']) {
                $res = $this->db->get_row("SELECT s.`Subdivision_ID`, s.`Catalogue_ID`
                                   FROM `Subdivision` AS `s`, `Sub_Class` AS `sc`
                                   WHERE sc.`Subdivision_ID` = s.`Subdivision_ID`
                                   AND sc.`Sub_Class_ID` = '" . intval($mailer['Sub_Class_ID']) . "'", ARRAY_A);
                if (empty($res)) {
                    throw new Exception(NETCAT_MODULE_SUBSCRIBER_NOT_SUB_FOR_CC . "<br/> Sub class id: " . $mailer['Sub_Class_ID']);
                }

                $mailer = array_merge($mailer, $res);
                unset($res);
            }
        } else { // добавление новой рассылки - данные могут прийти из post'a
            foreach ($keys as $key) {
                if (isset($_POST[$key])) $mailer[$key] = $_POST[$key];
            }
            if (!$mailer['Type']) $mailer['Type'] = 1;
            if (!$mailer['Access']) $mailer['Access'] = 1;
        }

        echo "<form name='main' id='main' method='post' enctype='multipart/form-data'  action='admin.php' style='padding:0; margin:0;' >\n" .
            "<!-- Основные настройки -->" .
            "<div id='maininfoOff' style='cursor: pointer; display: none' onclick='this.style.display=\"none\";
                                                                                document.getElementById(\"maininfoOn\").style.display=\"\";'>
           &#x25BA; " . NETCAT_MODULE_SUBSCRIBER_MAIN_SETTINGS . "
      </div>
      <div id='maininfoOn'>
      <fieldset>
       <legend>" . NETCAT_MODULE_SUBSCRIBER_MAIN_SETTINGS . "</legend>\n";
        $this->_input_text(NETCAT_MODULE_SUBSCRIBER_MAILER_NAME, 'Name', $mailer['Name']);

        echo "<div style='margin:10px 0; _padding:0;'>\n" .
            "" . NETCAT_MODULE_SUBSCRIBER_MAILER_TYPE . " <br>" .
            "<select name='Type' id='Type' style='width:50%' onchange='nc_subs.change_type(); return false;' >\n" .
            "<option value='1' " . ($mailer['Type'] == 1 ? "selected" : "") . ">" . NETCAT_MODULE_SUBSCRIBER_MAILER_CC . "</option>\n" .
            "<option value='2' " . ($mailer['Type'] == 2 ? "selected" : "") . ">" . NETCAT_MODULE_SUBSCRIBER_MAILER_PERIODICAL . "</option>\n" .
            "<option value='3' " . ($mailer['Type'] == 3 ? "selected" : "") . ">" . NETCAT_MODULE_SUBSCRIBER_MAILER_SERVICE . "</option>\n" .
            "</select>\n" .
            "</div>\n" .
            "<div style='margin:10px 0; _padding:0;'>\n" .
            "" . NETCAT_MODULE_SUBSCRIBER_ACCESS_TO . ": <br>" .
            "<select name='Access'  style='width:50%'>\n" .
            "<option value='1' " . ($mailer['Access'] == 1 ? "selected" : "") . ">" . NETCAT_MODULE_SUBSCRIBER_ACCESS_ALL . "</option>\n" .
            "<option value='2' " . ($mailer['Access'] == 2 ? "selected" : "") . ">" . NETCAT_MODULE_SUBSCRIBER_ACCESS_REGISTERED . "</option>\n" .
            "<option value='3' " . ($mailer['Access'] == 3 ? "selected" : "") . ">" . NETCAT_MODULE_SUBSCRIBER_ACCESS_AUTHORIZED . "</option>\n" .
            "</select>\n" .
            "</div>\n";

        $this->_checkbox(NETCAT_MODULE_SUBSCRIBER_ACTIVE, 'Active', $mailer['Active']);
        $this->_checkbox(NETCAT_MODULE_SUBSCRIBER_IN_STAT, 'InStat', $mailer['InStat']);

        echo "</fieldset></div>\n";

        echo "<div id='specOff' style='cursor: pointer;' onclick='this.style.display=\"none\";document.getElementById(\"specOn\").style.display=\"\"; updateCM();'>
       &#x25BA; " . NETCAT_MODULE_SUBSCRIBER_SPECIFIC_SETTINGS . "
      </div>
      <div id='specOn' style='display: none'>";

        echo "<fieldset>\n" .
            "<legend>" . NETCAT_MODULE_SUBSCRIBER_SPECIFIC_SETTINGS . "</legend>\n" .
            "<div id='div_type_1' name='div_type_1' style='display: none;'>" .
            "<div style='margin:10px 0; _padding:0;'>\n" .
            "<table width='50%'>\n" .
            "<tr><td width='30%'>" . NETCAT_MODULE_SUBSCRIBER_SITE . "</td>" .
            "<td width='70%'>\n<select name='site_list' id='site_list' style='width: 100%;' onchange='nc_subs.change_site(); return false;'>\n";

        $sites = $this->db->get_results("SELECT `Catalogue_ID` as `id`, `Catalogue_Name` as `name` FROM `Catalogue` ORDER BY `Priority`", ARRAY_A);
        if (!empty($sites)) {
            foreach ($sites as $site) {
                if (!$first_site) $first_site = $site['id'];
                echo "<option value='" . $site['id'] . "' " . ($mailer['Catalogue_ID'] == $site['id'] ? "selected" : "") . ">" . $site['id'] . ". " . $site['name'] . "</option>\n";
            }
        }

        echo "</select></td></tr>\n" .
            "<tr><td>" . NETCAT_MODULE_SUBSCRIBER_SUBDIVISION . "</td>" .
            "<td><div id='div_sub_list'><select name='sub_list' id='sub_list' style='width: 100%;'>" .
            "</select></div></td></tr>\n" .
            "<tr><td>" . NETCAT_MODULE_SUBSCRIBER_CC . "</td>" .
            "<td><div id='div_subclass_list'><select name='subclass_list' style='width: 100%' id='subclass_list'>" .
            "</select></div></td></tr>\n" .
            "</table>" .
            "</div>\n" .
            "<div style='margin:10px 0; _padding:0;'>\n" .
            "" . NETCAT_MODULE_SUBSCRIBER_ADD_OBJECT_TO_MAILLIST . ":  <br/>" .
            "<input type='checkbox' name='rule_add_on'   value='1'" . (($mailer['ActionType'] & 1) ? " checked" : "") . "/> " . NETCAT_MODULE_SUBSCRIBER_ACTION_ADD_ON . " <br/>" .
            "<input type='checkbox' name='rule_add_off'  value='2'" . (($mailer['ActionType'] & 2) ? " checked" : "") . "/> " . NETCAT_MODULE_SUBSCRIBER_ACTION_ADD_OFF . " <br/>" .
            "<input type='checkbox' name='rule_edit_on'  value='4'" . (($mailer['ActionType'] & 4) ? " checked" : "") . "/> " . NETCAT_MODULE_SUBSCRIBER_ACTION_EDIT_ON . " <br/>" .
            "<input type='checkbox' name='rule_edit_off' value='8'" . (($mailer['ActionType'] & 8) ? " checked" : "") . "/> " . NETCAT_MODULE_SUBSCRIBER_ACTION_EDIT_OFF . " <br/>" .
            "<input type='checkbox' name='rule_on'       value='16'" . (($mailer['ActionType'] & 16) ? " checked" : "") . "/> " . NETCAT_MODULE_SUBSCRIBER_ACTION_ON . " <br/>" .
            "<input type='checkbox' name='rule_off'      value='32'" . (($mailer['ActionType'] & 32) ? " checked" : "") . "/> " . NETCAT_MODULE_SUBSCRIBER_ACTION_OFF . " <br/>" .
            "</div>\n" .
            "</div>" .
            "<div id='div_type_23' name='div_type_23' style='display: none;'>" .
            "<div style='margin:10px 0; _padding:0;'>\n
           " . NETCAT_MODULE_SUBSCRIBER_MAILER_PERIOD . ": <br/>" .
            nc_list_select("SubscriberPeriod", "Period", $mailer['Period'], null, null,
                "<select name='Period' style='width:30%'>", null, null, true) .
            "</div>\n" .
            "</div>\n" .
            "</fieldset></div>\n";


        echo "<div id='condOff' style='cursor: pointer;' onclick='this.style.display=\"none\";document.getElementById(\"condOn\").style.display=\"\"; \$nc(window).trigger(\"resize\"); updateCM();'>
      &#x25BA; " . NETCAT_MODULE_SUBSCRIBER_COND_AND_ACTION . "
     </div>
     <div id='condOn' style='display: none'>

     <fieldset>\n" .
            "<legend>" . NETCAT_MODULE_SUBSCRIBER_COND_AND_ACTION . "</legend>\n";
        // условия и дейсвтия
        $this->_textarea(NETCAT_MODULE_SUBSCRIBER_SUBSCRIBE_COND, 'SubscribeCond', $mailer['SubscribeCond'], 1);
        $this->_textarea(NETCAT_MODULE_SUBSCRIBER_SEND_COND, 'SendCond', $mailer['SendCond'], 1);
        $this->_textarea(NETCAT_MODULE_SUBSCRIBER_SUBSCRIBE_ACTION, 'SubscribeAction', $mailer['SubscribeAction'], 1);


        echo "</fieldset></div>\n
     <div id='tempOff' style='cursor: pointer;' onclick='this.style.display=\"none\";document.getElementById(\"tempOn\").style.display=\"\"; \$nc(window).trigger(\"resize\"); updateCM();'>
       &#x25BA; " . NETCAT_MODULE_SUBSCRIBER_MAIL_TEMPLATE . "
      </div>
      <div id='tempOn' style='display: none'>

      <fieldset>\n" .
            "<legend>" . NETCAT_MODULE_SUBSCRIBER_MAIL_TEMPLATE . "</legend>\n";

        $this->_input_text(NETCAT_MODULE_SUBJECT, 'Subject', $mailer['Subject']);
        $this->_checkbox(NETCAT_MODULE_HTML_MAIL, 'HTML', $mailer['HTML']);
        $this->_textarea(NETCAT_MODULE_SUBSCRIBER_MAIL_TEMPLATE_HEADER, 'Header', $mailer['Header'], 1, 0);
        $this->_textarea(NETCAT_MODULE_SUBSCRIBER_MAIL_TEMPLATE_CONTENT, 'Record', $mailer['Record'], 1, 0);
        $this->_textarea(NETCAT_MODULE_SUBSCRIBER_MAIL_TEMPLATE_FOOTER, 'Footer', $mailer['Footer'], 1, 0);
        echo nc_mail_attachment_form('subscriber_template_' . ($mailer_id ? $mailer_id : 'new'));
        echo "</fieldset></div>\n";
        // js for resize
        $this->_js_resize();
        // admin buttons
        $this->UI_CONFIG->actionButtons[] = array(
            "id" => "submit",
            "caption" => NETCAT_MODULE_SUBSCRIBER_SAVE_BUTTON,
            "action" => "mainView.submitIframeForm('main')"
        );

        if ($mailer_id) {
            $this->UI_CONFIG->actionButtons[] = array(
                "id" => "delete",
                "caption" => NETCAT_MODULE_SUBSCRIBE_ADM_DELETE,
                "align" => "left",
                "action" => "mainView.submitIframeForm('deleteForm')",
                "red_border" => true,
            );
        }

        echo "<input type='hidden' name='phase' value='21' />\n" .
            "<input type='hidden' name='mailer_id' value='" . $mailer_id . "' />" .
            "<input type='submit' class='hidden' />" .
            "</form><br/>\n";

        if ($mailer_id) {
            echo "<form name='deleteForm' id='deleteForm' method='post' action='admin.php'>\n" .
                "<input type='hidden' name='phase' value='11'>\n" .
                "<input type='hidden' name='action_type' value='delete'>\n" .
                "<input type='hidden' name='mailer_{$mailer_id}' value='{$mailer_id}'>\n" .
                "</form>";
        }

        echo "
    <script type='text/javascript' src='admin.js'></script>
    <script type='text/javascript'>
      var none_cc_text = '" . NETCAT_MODULE_SUBSCRIBER_NONE_CC . "';
      nc_subs = new nc_subscriber();
      nc_subs.set_site(" . ($mailer['Catalogue_ID'] ? $mailer['Catalogue_ID'] : $first_site) . ");
      nc_subs.set_sub(" . ($mailer['Subdivision_ID'] ? $mailer['Subdivision_ID'] : 0) . ");
      nc_subs.set_cc(" . ($mailer['Sub_Class_ID'] ? $mailer['Sub_Class_ID'] : 0) . ");
      nc_subs.init();
      nc_subs.change_type();
      function updateCM() {
        \$nc('TEXTAREA.has_codemirror').each(function(){
            \$nc(this).data('codemirror').refresh();
        });
      }
    </script>";

        return 0;
    }

    /**
     * Сохранение данных от формы добавления/изменения рассылки
     * @param int $mailer_id номер рассылки ( 0 - добавление )
     */
    public function mailer_save($mailer_id) {
        $result = $_POST;
        if (!get_magic_quotes_gpc()) {
            $result = nc_Core::get_object()->input->recursive_add_slashes($_POST);
        }
        $result['ActionType'] = $result['rule_add_on'] + $result['rule_add_off'] +
            $result['rule_edit_on'] + $result['rule_edit_off'] +
            $result['rule_on'] + $result['rule_off'];

        $result['Active'] = $result['Active'] + 0;
        $result['InStat'] = $result['InStat'] + 0;
        $result['HTML'] = $result['HTML'] + 0;
        if ($mailer_id) {
            $this->nc_subscriber->update($mailer_id, $result);
            nc_mail_attachment_form_save('subscriber_template_' . $mailer_id);
        } else {
            $mailer_id = $this->nc_subscriber->add($result);
            nc_mail_attachment_form_save('subscriber_template_' . $mailer_id, 'subscriber_template_new');
        }

        return 0;
    }

    /**
     * Обработка массовых дейсвий (вкл, выкл, удаление) с рассылками
     */
    public function mailer_list_update() {
        $nc_s = nc_subscriber::get_object();
        $mailer_ids = array();
        foreach ($_POST as $k => $v) {
            if (substr($k, 0, 6) == 'mailer') $mailer_ids[] = intval($v);
        }
        if (empty($mailer_ids)) return false;

        switch ($_POST['action_type']) {
            case 'active': // включение
                $nc_s->activate($mailer_ids);
                break;
            case 'unactive': // выключение
                $nc_s->unactivate($mailer_ids);
                break;
            case 'drop': // удаление
                $nc_s->delete($mailer_ids);
                break;
        }
    }

    /**
     * Форма для подтверждения удаления рассылок
     */
    public function mailer_confirm_deletion() {
        $mailer_ids = array();
        foreach ($_POST as $k => $v) {
            if (substr($k, 0, 6) == 'mailer') $mailer_ids[] = intval($v);
        }
        if (empty($mailer_ids)) return false;

        nc_print_status(NETCAT_MODULE_SUBSCRIBER_CONFIRM_REMOVE_MAILERS, 'info');
        echo "<form action='admin.php' method='post'><ul>";
        $res = $this->db->get_results("SELECT `Mailer_ID`, `Name` FROM `Subscriber_Mailer`
                             WHERE `Mailer_ID` IN (" . join(',', $mailer_ids) . ") ", ARRAY_A);
        foreach ($res as $row) {
            echo "<li>" . $row['Name'] . " <input type='hidden' name='mailer_" . $row['Mailer_ID'] . "' value='" . $row['Mailer_ID'] . "' /></li>\n";
        }
        echo "</ul><input type='hidden' name='phase' value='11' />\n
          <input type='hidden' name='action_type' value='drop' />
          </form>";

        $this->UI_CONFIG->actionButtons[] = array(
            "id" => "submit",
            "caption" => CONTROL_USER_FUNC_CONFIRM_DEL_OK,
            "action" => "mainView.submitIframeForm()",
            "red_border" => true,
        );
    }

    /**
     * Функция показывает подписчиков на данныую рассылку
     * @param int $mailer_id номер рассылки
     */
    public function sbs_show($mailer_id) {
        $nc_core = nc_Core::get_object();

        $mailer_id = intval($mailer_id);
        $mailer = $this->nc_subscriber->get($mailer_id);

        // выполнение запроса
        $this->db->last_error = '';
        $subscriptions = $this->db->get_results("SELECT ss.`ID`, ss.`Status`,
                                                   u.`User_ID`, u.`Login`, u.`UserType`, u.`" . $this->_settings['EmailField'] . "`
                                             FROM `Subscriber_Subscription`  AS `ss`,
                                                  `User` AS `u`
                                             WHERE u.`User_ID` = ss.`User_ID`
                                             AND `Mailer_ID` = '" . $mailer_id . "'
                                             ORDER BY `UserType`, `User_ID`", ARRAY_A);
        if ($this->db->last_error) {
            throw new ExceptionDB(__CLASS__ . '::' . __FUNCTION__);
        }
        // если нет подписчиков
        if (empty($subscriptions)) {
            nc_print_status(NETCAT_MODULE_SUBSCRIBER_NO_SUBSCRIBERS, 'info');
            return 0;
        }
        echo NETCAT_MODULE_SUBSCRIBER_MAILER_SUBSCRIBER_LIST . " <a href='admin.php?phase=2&mailer_id=" . $mailer_id . "'>" . $mailer['Name'] . "</a>. <br/><br>";

        // вывод формы
        echo "
      <form action='admin.php' method='post' name='mainForm' id='mainForm'>
      <input type='hidden' name='phase' value='61' />
      <input type='hidden' name='action_type' value='' />
      <input type='hidden' name='mailer_id' value='" . $mailer_id . "' />
      <table border='0' cellpadding='0' cellspacing='0' width='100%'>
        <tr><td >
            <table border='0' cellpadding='4' cellspacing='1' width='100%'>
              <tr>
                <td >" . NETCAT_MODULE_SUBSCRIBER_USER . "</td>
                <td >" . NETCAT_MODULE_SUBSCRIBE_STATUS . "</td>
                <td  align='center'>" . ($nc_core->get_settings('PacketOperations') ? "<div class='icons icon_type_bool'></div>" : "") . "</td>
              </tr>";


        foreach ($subscriptions as $sbs) {
            switch ($sbs['Status']) {
                case 'on':
                    $sbs['Status'] = "" . NETCAT_MODULE_SUBSCRIBE_TURNEDON . "";
                    break;
                case 'off':
                    $sbs['Status'] = "" . NETCAT_MODULE_SUBSCRIBE_TURNEDOFF . "";
                    break;
                case 'wait':
                    $sbs['Status'] = "" . NETCAT_MODULE_SUBSCRIBE_WAIT_CONFIRM . "";
                    break;
                default:
                    $sbs['Status'] = '';
                    break;
            }
            $user = ($sbs['UserType'] == 'normal' ? $sbs['User_ID'] . ". <a  href='" . $this->ADMIN_PATH . "user/index.php?phase=4&UserID=" . $sbs['User_ID'] . "'>" . $sbs['Login'] . "</a>" :
                NETCAT_MODULE_SUBSCRIBE_UNREGISTERED_USER . " (" . $sbs['Email'] . ")");

            echo "<tr>\n" .
                "<td >" . $user . "</td>\n" .
                "<td >" . $sbs['Status'] . "</td>\n" .
                "<td  align='center'>" . ($nc_core->get_settings('PacketOperations') ? "<input type='checkbox' name='sbs_" . $sbs['ID'] . "' value='" . $sbs['ID'] . "' />" : "") . "</td>\n" .
                "</tr>";
        }

        echo "</table></td></tr></table></form>";
        // скрипт для смены действия
        echo "
    <script type='text/javascript'>\n
      function sumbit_form ( action_type ) {\n
        document.getElementById('mainForm').action_type.value =  action_type;\n
        parent.mainView.submitIframeForm('mainForm');\n
        return 0;\n
      }\n
    </script>\n";

        if ($nc_core->get_settings('PacketOperations')) {
            $this->UI_CONFIG->actionButtons[] = array(
                "id" => "submit",
                "caption" => NETCAT_MODULE_SUBSCRIBER_DELETESELECTED,
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form('drop')",
                "align" => "left",
                "red_border" => true,
            );
            $this->UI_CONFIG->actionButtons[] = array(
                "id" => "submit1",
                "caption" => NETCAT_MODULE_SUBSCRIBER_SELECTEDOFF,
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form('off')",
                "align" => "left"
            );
            $this->UI_CONFIG->actionButtons[] = array(
                "id" => "submit2",
                "caption" => NETCAT_MODULE_SUBSCRIBER_SELECTEDON,
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form('on')",
                "align" => "left"
            );
        }

        return 0;
    }

    /**
     * Массовая обработка подписок на определенную рассылку
     * @return <type>
     */
    public function sbs_update() {

        $nc_s = nc_subscriber::get_object();

        if ($_POST['action_type'] == 'add') {
            $nc_s->subscription_add($_POST['mailer_id'], $_POST['UserID'], 0, 'on');
            return 0;
        }

        $sbs_ids = array();
        foreach ($_POST as $k => $v) {
            if (substr($k, 0, 3) == 'sbs') $sbs_ids[] = intval($v);
        }
        if (empty($sbs_ids)) return false;

        switch ($_POST['action_type']) {
            case 'on':
                $nc_s->subscription_change_status($sbs_ids, 'on');
                break;
            case 'off':
                $nc_s->subscription_change_status($sbs_ids, 'off');
                break;
            case 'drop':
                $nc_s->subscription_delete($sbs_ids);
                break;
        }
    }

    /**
     * Функция показывает все подписки пользователя
     * @param int $user_id номер пользователя
     */
    public function user_show($user_id) {
        $nc_core = nc_Core::get_object();

        $nc_subscriber = nc_subscriber::get_object();
        $user_id = intval($user_id);

        if (!$user_id) {
            throw new ExceptionParam(__CLASS__ . '::' . __FUNCTION__, 'user_id');
        }

        $this->db->last_error = '';
        //  выполнение запроса
        $subscriptions = $this->db->get_results("SELECT ss.`ID`, ss.`Status`, sm.`Name`, sm.`Mailer_ID`
                                            FROM `Subscriber_Subscription`  AS `ss`,
                                                 `Subscriber_Mailer`  AS `sm`
                                            WHERE ss.`User_ID` = '" . $user_id . "'
                                            AND ss.`Mailer_ID` = sm.`Mailer_ID`
                                            ORDER BY sm.`Mailer_ID`", ARRAY_A);

        if ($this->db->last_error) {
            throw new ExceptionDB(__CLASS__ . '::' . __FUNCTION__);
        }

        echo "
      <form action='" . $this->ADMIN_PATH . "user/index.php' method='post' name='mainForm' id='mainForm'>
      <input type='hidden' name='phase' value='16' />
      <input type='hidden' name='action_type' value='' />
      <input type='hidden' name='UserID' value='" . $user_id . "' />";

        if (empty($subscriptions)) {
            nc_print_status(NETCAT_MODULE_SUBSCRIBE_USER_NOT_SUBSCRIBE, 'info');
        } else {
            echo "<table border='0' cellpadding='0' cellspacing='0' width='100%'>
        <tr><td >
            <table border='0' cellpadding='4' cellspacing='1' width='100%'>
              <tr>
                <td  width='50%'>" . NETCAT_MODULE_SUBSCRIBE_MAILER . "</td>
                <td  width='40%'>" . NETCAT_MODULE_SUBSCRIBE_STATUS . "</td>
                <td  width='10%' align='center'>" . ($nc_core->get_settings('PacketOperations') ? "<div class='icons icon_type_bool'></div>" : "") . "</td>
              </tr>";


            foreach ($subscriptions as $sbs) {
                // статус
                switch ($sbs['Status']) {
                    case 'on':
                        $sbs['Status'] = "" . NETCAT_MODULE_SUBSCRIBE_TURNEDON . "";
                        break;
                    case 'off':
                        $sbs['Status'] = "" . NETCAT_MODULE_SUBSCRIBE_TURNEDOFF . "";
                        break;
                    case 'wait':
                        $sbs['Status'] = "" . NETCAT_MODULE_SUBSCRIBE_WAIT_CONFIRM . "";
                        break;
                    default:
                        $sbs['Status'] = '';
                        break;
                }
                echo "<tr>\n" .
                    "<td >" . $sbs['Mailer_ID'] . ". <a href='" . $this->ADMIN_PATH . "../modules/subscriber/admin.php?phase=2&amp;mailer_id=" . $sbs['Mailer_ID'] . "'>" . $sbs['Name'] . "</a></td>\n" .
                    "<td >" . $sbs['Status'] . "</td>\n" .
                    "<td  align='center'>" . ($nc_core->get_settings('PacketOperations') ? "<input type='checkbox' name='sbs_" . $sbs['ID'] . "' value='" . $sbs['ID'] . "' />" : "") . "</td>\n" .
                    "</tr>";
            }

            echo "</table></td></tr></table>";
        }

        echo $this->core->token->get_input();
        echo "</form>";
        echo "
    <script type='text/javascript'>\n
      function sumbit_form ( action_type ) {\n
        document.getElementById('mainForm').action_type.value =  action_type;\n
        parent.mainView.submitIframeForm('mainForm');\n
        return 0;\n
      }\n
    </script>\n";


        $this->UI_CONFIG->actionButtons[] = array(
            "id" => "submit3",
            "caption" => NETCAT_MODULE_SUBSCRIBE_ADD_SUBSCRIBE,
            "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form('add_ask')",
            "align" => "right"
        );

        if (!empty($subscriptions) && $nc_core->get_settings('PacketOperations')) {
            $this->UI_CONFIG->actionButtons[] = array(
                "id" => "submit",
                "caption" => NETCAT_MODULE_SUBSCRIBER_DELETESELECTED,
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form('drop')",
                "align" => "left",
                "red_border" => true,
            );
            $this->UI_CONFIG->actionButtons[] = array(
                "id" => "submit1",
                "caption" => NETCAT_MODULE_SUBSCRIBER_SELECTEDOFF,
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form('off')",
                "align" => "left"
            );
            $this->UI_CONFIG->actionButtons[] = array(
                "id" => "submit2",
                "caption" => NETCAT_MODULE_SUBSCRIBER_SELECTEDON,
                "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form('on')",
                "align" => "left"
            );
        }

        return 0;
    }

    /**
     * Работа с подписками пользователя
     * @param int $user_id номер пользователя
     */
    public function user_add_form($user_id) {
        $user_id = intval($user_id);
        $mailers = $this->db->get_results("SELECT sm.`Mailer_ID`, sm.`Name`
                            FROM (`Subscriber_Mailer` AS `sm`)
                            LEFT JOIN `Subscriber_Subscription` AS `ss` ON (sm.`Mailer_ID` = ss.`Mailer_ID`
                            AND  ss.`User_ID` = '" . $user_id . "')
                            WHERE ss.`ID` IS NULL
                            AND sm.`Type` <> 3
                            ORDER BY sm.`Mailer_ID`", ARRAY_A);
        if (empty($mailers)) {
            nc_print_status(NETCAT_MODULE_SUBSCRIBE_NONE_MAILERS_FOR_USER, 'info');
            return 0;
        }

        echo "
      <form action='" . $this->ADMIN_PATH . "user/index.php' method='post' name='mainForm' id='mainForm'>
      <input type='hidden' name='phase' value='16' />
      <input type='hidden' name='action_type' value='add' />
      <input type='hidden' name='UserID' value='" . $user_id . "' />";

        echo "<div style='margin:10px 0; _padding:0;'>\n
        " . NETCAT_MODULE_SUBSCRIBE_SELECT_MAILER . ": <br>
        <select name='mailer_id'  style='width:50%'>\n";

        foreach ($mailers as $mailer) {
            echo "<option value='" . $mailer['Mailer_ID'] . "'>" . $mailer['Name'] . "</option>";
        }

        echo "</select>";
        echo $this->core->token->get_input();
        echo "</form>";

        // admin buttons
        $this->UI_CONFIG->actionButtons[] = array(
            "id" => "submit",
            "caption" => NETCAT_MODULE_SUBSCRIBE_ACTION_SUBSCRIBE,
            "action" => "mainView.submitIframeForm()"
        );

        return 0;
    }

    /**
     * Вывод формы для единоразовой рассылки
     */
    public function once_show() {
        // подписанные на рассылку
        $mailer_id = $_POST['mailer_id'];
        // группы пользователя
        $grpID = $_POST['grpID'];
        if (!$grpID) $grpID = array();
        // тип пользователя
        $user_type = intval($_POST['user_type']);
        // включен/выключен
        $checked = intval($_POST['checked']);

        echo "<form name='main' id='main' action='admin.php' method='post' enctype='multipart/form-data' >";

        // выборка пользвотелей
        echo
            "<div id='userOff' style='cursor: pointer; display: none' onclick='this.style.display=\"none\";
               document.getElementById(\"userOn\").style.display=\"\";'>
           &#x25BA; " . CONTROL_USER_FUNCS_USERSGET . "
      </div>
      <div id='userOn'>
      <fieldset>
       <legend>" . CONTROL_USER_FUNCS_USERSGET . "</legend>";

        echo "<table border='0' cellpadding='6' cellspacing='0' width='100%'><tr><td colspan=2'> ";

        // подписанные на рассылку
        $mailers = $this->db->get_results("SELECT DISTINCT sm.`Mailer_ID` as `id`, sm.`Name` AS `name`
                                      FROM `Subscriber_Mailer` AS `sm`, `Subscriber_Subscription` as s
                                      WHERE sm.Mailer_ID = s.Mailer_ID
                                      ORDER BY sm.`Mailer_ID`", ARRAY_A);
        if (!empty($mailers)) {
            echo "<div style='margin:10px 0; _padding:0;'>\n
       " . NETCAT_MODULE_SUBSCRIBER_SUBSCRIBE_USER . ":<br/>";
            echo "<select name='mailer_id'>";
            echo "<option value='0'>" . NETCAT_MODERATION_MOD_NOANSWER . "</option>";
            foreach ($mailers as $mailer) {
                echo "<option value='" . $mailer['id'] . "' " . ($mailer_id == $mailer['id'] ? "selected='selected'" : "") . ">" . $mailer['name'] . "</option>";
            }
            echo "</select></div>";
        }


        // группа пользователя
        $groups = $this->db->get_results("SELECT `PermissionGroup_ID` AS `id`, `PermissionGroup_Name` AS `name`
                                      FROM `PermissionGroup`", ARRAY_A);
        echo "" . CONTROL_USER_GROUP . ":<br/>";
        echo "<select name='grpID[]' multiple size='3'>";
        if (!empty($groups)) {
            foreach ($groups AS $group)
                echo "<option value='" . $group['id'] . "' " . (in_array($group['id'], $grpID) ? 'selected' : '') . ">" . $group['id'] . ": " . $group['name'];
        }
        echo "</select>";

        echo "</td></tr><tr><td width='20%'>";
        echo "" . NETCAT_MODULE_SUBSCRIBER_ONCE_TYPE_USER . " : </td><td><input type='radio' name='user_type' value='0' id='user_type_0' " . ($user_type == 0 ? "checked" : "") . "/>
                            <label for='user_type_0'>" . NETCAT_MODULE_SUBSCRIBER_ONCE_REGISTRED . "</label>
                            &nbsp;&nbsp;
                            <input type='radio' name='user_type' value='1' id='user_type_1' " . ($user_type == 1 ? "checked" : "") . "/>
                            <label for='user_type_1'>" . NETCAT_MODULE_SUBSCRIBER_ONCE_ALL . "</label>";

        echo "</td></tr><tr><td>";
        echo "" . NETCAT_MODULE_SUBSCRIBER_USER_CHECK . " : </td><td> <input type='radio' name='checked' value='0' id='checked_0' " . ($checked == 0 ? "checked" : "") . "/>
                            <label for='checked_0'>" . NETCAT_MODULE_SUBSCRIBER_USER_CHECK_ALL . "</label>
                            &nbsp;&nbsp;
                            <input type='radio' name='checked' value='1' id='checked_1' " . ($checked == 1 ? "checked" : "") . "/>
                            <label for='checked_1'>" . NETCAT_MODULE_SUBSCRIBER_USER_CHECKED . "</label>
                            &nbsp;&nbsp;
                            <input type='radio' name='checked' value='2' id='checked_2' " . ($checked == 2 ? "checked" : "") . "/>
                            <label for='checked_2'>" . NETCAT_MODULE_SUBSCRIBER_USER_NONECHECK . "</label>";
        echo "</td></tr><tr><td colspan='2'><br/>";

        $systemTableID = 3;
        require_once $this->core->INCLUDE_FOLDER . "s_list.inc.php";
        $is_there_any_files = 0;
        global $db;
        require $this->core->ROOT_FOLDER . "message_fields.php";

        echo showSearchForm($fldName, $fldType, $fldDoSearch, $fldFmt);


        echo "</td></tr></table>";
        echo "</fieldset></div>\n";


        // Формирование письма
        echo
            "<div id='mailOff' style='cursor: pointer; display: none' onclick='this.style.display=\"none\";
               document.getElementById(\"mailOn\").style.display=\"\";'>
           &#x25BA; " . NETCAT_MODULE_SUBSCRIBER_MAIL_TEMPLATE . "
      </div>
      <div id='mailOn'>
      <fieldset>
       <legend>" . NETCAT_MODULE_SUBSCRIBER_MAIL_TEMPLATE . "</legend>";

        echo $this->_input_text(NETCAT_MODULE_SUBJECT, 'Subject', stripslashes($_POST['Subject']));
        echo $this->_checkbox(NETCAT_MODULE_HTML_MAIL, 'HTML', $_POST['HTML']);
        echo $this->_textarea(NETCAT_MODULE_MAIL_BODY, 'MailBody', stripslashes($_POST['MailBody']), 1, 1);


        echo "</fieldset></div>\n";

        // Файлы
        echo
            "<div id='fileOff' style='cursor: pointer; display: none' onclick='this.style.display=\"none\";
               document.getElementById(\"fileOn\").style.display=\"\";'>
           &#x25BA; " . NETCAT_MODULE_SUBSCRIBER_USER_ATTACH . "
      </div>
      <div id='fileOn'>
      <fieldset>
       <legend>" . NETCAT_MODULE_SUBSCRIBER_USER_ATTACH . "</legend>";

        echo $this->_input_file(NETCAT_MODULE_SUBSCRIBER_FILE_1, 'file1', $this->_files_id[1]);
        echo $this->_input_file(NETCAT_MODULE_SUBSCRIBER_FILE_2, 'file2', $this->_files_id[2]);
        echo $this->_input_file(NETCAT_MODULE_SUBSCRIBER_FILE_3, 'file3', $this->_files_id[3]);


        echo "</fieldset></div>\n";


        $this->_js_resize();


        echo "
    <input type='hidden' name='phase' value='72' />
    <script type='text/javascript'>\n
      function sumbit_form ( phase ) {\n
        document.getElementById('main').phase.value =  phase;\n
        parent.mainView.submitIframeForm('main');\n
        return 0;\n
      }\n
    </script>\n";


        $this->UI_CONFIG->actionButtons[] = array(
            "id" => "submit",
            "caption" => NETCAT_MODULE_SUBSCRIBE_TESTSEND,
            "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form(71)",
            "align" => "left"
        );
        $this->UI_CONFIG->actionButtons[] = array(
            "id" => "submit1",
            "caption" => NETCAT_MODULE_SUBSCRIBE_SEND,
            "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form(72)",
        );
    }

    public function once_test_send() {
        // проверка адреса для тестовой рассылки
        $nc_email = $this->_settings['TestEmail'];
        if (!$nc_email || !preg_match("/^[0-9a-z-._]+@[a-z]+\.[a-z]{1,6}$/i", $nc_email)) {
            nc_print_status(NETCAT_MODULE_SUBSCRIBER_ONCE_INVALID_TEST_EMAIL, 'error');
            return;
        }

        $this->once_file_save();

        // посылаем письмо
        $s = nc_subscriber_send::get_object();
        $s->send_mail($nc_email, stripslashes($_POST['MailBody']), stripslashes($_POST['Subject']), $_POST['HTML'], $this->_files_id);

        // завершаем работу функции
        nc_print_status(sprintf(NETCAT_MODULE_SUBSCRIBER_ONCE_MAIL_SEND, $nc_email), 'ok');
        return 0;
    }

    public function once_send() {
        // обработка входных файлов
        $this->once_file_save();
        // выборка пользователя
        $systemTableID = 3;
        $db = $this->db;
        require($this->core->ROOT_FOLDER . "message_fields.php");
        require_once($this->core->INCLUDE_FOLDER . "s_list.inc.php");
        $search_params = getSearchParams($fld, $fldType, $fldDoSearch, $_POST['srchPat']);

        // рассылка, на которую должен быть подписан пользователь
        $mailer_id = intval($_POST['mailer_id']);
        // условия выборки пользователя
        if ($_POST['user_type'] == 0) $where .= " AND `UserType` = 'normal' ";
        if ($_POST['checked'] == 1) $where .= " AND `Checked` = '1' ";
        if ($_POST['checked'] == 2) $where .= " AND `Checked` = '0' ";
        if ($mailer_id)
            $where .= " AND a.User_ID = s.User_ID AND s.Mailer_ID = '" . $mailer_id . "' ";
        if (!empty($_POST['grpID']))
            $where .= "AND `ug`.PermissionGroup_ID IN ( " . join(', ', array_map('intval', $_POST['grpID'])) . ") ";
        $where .= $search_params['query'];


        $query = "SELECT DISTINCT a.`" . $this->_settings['EmailField'] . "`
              FROM `User` AS `a`, `User_Group` AS `ug` " . ($mailer_id ? ", `Subscriber_Subscription` as s " : "") . "
              WHERE `ug`.`User_ID` = `a`.`User_ID`
              " . $where . "
              GROUP BY a.`User_ID` ";
        $emails = $db->get_col($query);

        if (empty($emails)) return 0;


        $subject = $db->escape(stripslashes($_POST['Subject']));
        $body = $db->escape(stripslashes($_POST['MailBody']));
        $html = intval($_POST['HTML']);
        $files = "";
        for ($i = 1; $i < 4; $i++)
            if ($this->_files_id[$i] == 0) unset($this->_files_id[$i]);

        if (!empty($this->_files_id)) $files = join(',', $this->_files_id);

        foreach ($emails as $email) {
            $email = $db->escape($email);
            $db->query("INSERT INTO `Subscriber_Prepared` (`Email`, `Subject`, `Body`, `HTML`, `Files`)
        VALUES ('" . $email . "','" . $subject . "','" . $body . "','" . $html . "', '" . $files . "')");
        }

        return count($emails);
    }

    protected function once_file_save() {
        // файлы
        for ($i = 1; $i < 4; $i++) {
            if ($_FILES['file' . $i]['size'] && !$_FILES['file' . $i]['error']) {
                // возможно, нужно удалить старый файл
                $id = $this->_files_id[$i];
                if ($id) {
                    $filename = $this->db->get_var("SELECT `Virt_Name` FROM `Filetable` WHERE `ID`='" . $id . "' ");
                    if ($filename) unlink($this->core->FILES_FOLDER . $filename);
                    $this->db->query("DELETE FROM `Filetable` WHERE `ID`='" . $id . "' LIMIT 1");
                    $this->_files_id[$i] = 0;
                }
                $name = md5(rand(0, 100) . $_FILES['file' . $i]['name']);
                if (move_uploaded_file($_FILES['file' . $i]['tmp_name'], $this->core->FILES_FOLDER . $name)) {
                    $filename = $this->db->escape($_FILES['file' . $i]['name']);
                    $filetype = $this->db->escape($_FILES['file' . $i]['type']);
                    $filesize = intval($_FILES['file' . $i]['size']);
                    $this->db->query("INSERT INTO `Filetable` (`Real_Name`, `Virt_Name`, `File_Path`, `File_Type`, `File_Size`)
            VALUES ('" . $filename . "','" . $name . "','/','" . $filetype . "','" . $filesize . "') ");
                    $this->_files_id[$i] = $this->db->insert_id;
                }
            }
        }

        return 0;
    }

    /**
     * Функция рисует элемент для ввода текста
     * @param string $disc описание
     * @param string $name имя
     * @param string $value  значение
     */
    protected function _input_text($disc, $name, $value) {
        echo "<div style='margin:10px 0; _padding:0;'>\n
       " . $disc . ":<br/>
       <input type='text' name='" . $name . "' style='width:50%' value='" . htmlentities($value, ENT_QUOTES, MAIN_ENCODING) . "'/>
       </div>\n";
    }

    /**
     * Функция рисует элемент для ввода текстового блока
     * @param string $disc описание
     * @param string $name имя
     * @param string $value  значение
     * @param bool $is_resizeble разрешить изменение высоты
     * @param bool $editor показывать кнопку для редактирования в визуальном редакторе
     */
    protected function _textarea($disc, $name, $value, $is_resizeble = 0, $editor = 0) {
        global $system_env;
        echo "<div style='margin:10px 0; _padding:0;'>\n" . $disc . ": <br/>";
        if ($editor) {
            $windowWidth = 750;
            $windowHeight = 605;
            switch (nc_Core::get_object()->get_settings('EditorType')) {
                default:
                case 2:
                    $editor_name = 'FCKeditor';
                    break;
                case 3:
                    $editor_name = 'ckeditor4';
                    $windowWidth = 1100;
                    $windowHeight = 420;
                    break;
                case 4:
                    $editor_name = 'tinymce';
                    break;
            }
            $editor_path = "editors/{$editor_name}/neditor.php";
            echo "<button type='button' onclick=\"window.open('" . $this->core->SUB_FOLDER . $this->core->HTTP_ROOT_PATH . $editor_path . "?form=main&control=" . $name . "', 'Editor', 'width={$windowWidth},height={$windowHeight},resizable=yes,scrollbars=no,toolbar=no,location=no,status=no,menubar=no');\">" . TOOLS_HTML_INFO . "</button><br>";
        }
        if ($is_resizeble) {
            echo "<div class='resize_block'><a class='textarea_shrink' href='#" . $name . "' > &#x25BC; </a>
                                     <a class='textarea_grow' href='#" . $name . "'> &#x25B2; </a></div>";
        }

        echo
            "<textarea name='" . $name . "' id='" . $name . "' style='width:100%; height:20em; line-height:1em'>" . htmlentities($value, ENT_COMPAT, MAIN_ENCODING) . "</textarea>" .
            "</div>\n";
    }

    /**
     * Функция рисует элемент-checkbox
     * @param string $disc описание
     * @param string $name имя
     * @param bool $value  включен/выключен
     */
    protected function _checkbox($disc, $name, $value) {
        echo " <div style='margin:10px 0; _padding:0;'>\n
        <input type='checkbox' name='" . $name . "' id='" . $name . "' value='1'" . ($value ? " checked" : "") . "/>
        <label for='" . $name . "'>" . $disc . "</label>
        </div>\n";
    }

    protected function _input_file($disc, $name, $id = 0) {
        $id = intval($id);
        if ($id) {
            $filename = $this->db->get_var("SELECT `Real_Name` FROM `Filetable` WHERE `ID`='" . $id . "' ");
        }
        echo "<div style='margin:10px 0; _padding:0;'>\n
       " . $disc . ":<br/>
       <input type='file' name='" . $name . "' id='" . $name . "' style='width: 50%'/>
       " . ($filename ? "<br/> Закачан: " . $filename : "") . "
       " . ($id ? "<input type='hidden' name='" . $name . "_id' value='" . $id . "' /> " : "") . "
</div>\n";
    }

    /**
     * Вывод js для ресайза текстовых блоков
     */
    protected function _js_resize() {
        echo " <script type='text/javascript'>
    (function (){
      var links = document.getElementsByTagName(\"A\");
      var onclicker_grow   = function (event) { ShrinkArea(event, 50);  }
      var onclicker_shrink = function (event) { ShrinkArea(event,-50);  }
      for (var i = 0 ; i < links.length ; i++){
          if (links[i].className.search('textarea_shrink') != -1 ) {
            bindEvent(links[i], 'click', onclicker_grow );
          }
          if (links[i].className.search('textarea_grow') != -1 ) {
            bindEvent(links[i], 'click', onclicker_shrink);
          }
      }
    }) ();
  </script>";
    }

}

?>