<?php
// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;
// пространство имен для управления (регистрации/удалении) модуля в системе/базе
use Bitrix\Main\ModuleManager;
// пространство имен для работы с параметрами модулей хранимых в базе данных
use Bitrix\Main\Config\Option;
// пространство имен с абстрактным классом для любых приложений, любой конкретный класс приложения является наследником этого абстрактного класса
use Bitrix\Main\Application;
// пространство имен для работы с директориями
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\File;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

class chelnynet_testdispatcher extends CModule
{
    // переменные модуля
    public  $MODULE_ID;
    public  $MODULE_VERSION;
    public  $MODULE_VERSION_DATE;
    public  $MODULE_NAME;
    public  $MODULE_DESCRIPTION;
    public  $PARTNER_NAME;
    public  $PARTNER_URI;
    public  $errors;

    // конструктор класса, вызывается автоматически при обращение к классу
    function __construct()
    {
        // создаем пустой массив для файла version.php
        $arModuleVersion = array();
        // подключаем файл version.php
        include_once(__DIR__ . '/version.php');

        // версия модуля
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        // дата релиза версии модуля
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        // id модуля
        $this->MODULE_ID = "chelnynet.testdispatcher";
        // название модуля
        $this->MODULE_NAME = "ТестДиспетчер";
        // описание модуля
        $this->MODULE_DESCRIPTION = "Тестовый без локализаций. После установки компонент запускается из файла /testdispatcher.php";
        // имя партнера выпустившего модуль
        $this->PARTNER_NAME = "chelnynet";
        // ссылка на рисурс партнера выпустившего модуль
        $this->PARTNER_URI = "https://chelnynet.info";
    }

    // метод отрабатывает при установке модуля
    function DoInstall()
    {
        // глобальная переменная с обстрактным классом
        global $APPLICATION;
        // создаем таблицы баз данных, необходимые для работы модуля
        $this->InstallDB();
        // регистрируем обработчики событий
        $this->InstallEvents();
        // копируем файлы, необходимые для работы модуля
        $this->InstallFiles();
        // регистрируем модуль в системе
        ModuleManager::RegisterModule($this->MODULE_ID);
        // подключаем скрипт с административным прологом и эпилогом
        $APPLICATION->includeAdminFile(
            Loc::getMessage('INSTALL_TITLE'),
            __DIR__ . '/instalInfo.php'
        );
        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод отрабатывает при удалении модуля
    function DoUninstall()
    {
        // глобальная переменная с обстрактным классом
        global $APPLICATION;
        // удаляем таблицы баз данных, необходимые для работы модуля
        $this->UnInstallDB();
        // удаляем обработчики событий
        $this->UnInstallEvents();
        // удаляем файлы, необходимые для работы модуля
        $this->UnInstallFiles();
        // удаляем регистрацию модуля в системе
        ModuleManager::UnRegisterModule($this->MODULE_ID);
        // подключаем скрипт с административным прологом и эпилогом
        $APPLICATION->includeAdminFile(
            Loc::getMessage('DEINSTALL_TITLE'),
            __DIR__ . '/deInstalInfo.php'
        );
        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для создания таблицы баз данных
    function InstallDB()
    {
        // глобальный объект $DB для работы с базой данных
        global $DB;
        // изначально устанавливаем переменной errors булево значение false
        $this->errors = false;
        // метод выполняет пакет запросов из файла install.sql и возвращает false в случае успеха или массив ошибок
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . "/local/modules/".$this->MODULE_ID."/install/db/install.sql");
        // проверяем ответ, если ответ вернул false, значит таблица успешно создана
        if (!$this->errors) {
            // для успешного завершения, метод должен вернуть true
            return true;
        } else
            return $this->errors;
    }

    // метод для удаления таблицы баз данных
    function UnInstallDB()
    {
        // глобальный объект $DB для работы с базой данных
        global $DB;
        // изначально устанавливаем переменной errors булево значение false
        $this->errors = false;
        // метод выполняет пакет запросов из файла uninstall.sql и возвращает false в случае успеха или массив ошибок
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . "/local/modules/".$this->MODULE_ID."/install/db/uninstall.sql");
        // проверяем ответ, если ответ вернул false, значит таблица успешно удалена
        if (!$this->errors) {
            // для успешного завершения, метод должен вернуть true
            return true;
        } else
            return $this->errors;
    }

    // метод для создания обработчика событий
    function InstallEvents()
    {
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		$eventManager->registerEventHandler(
			'main',
			'OnAfterUserUpdate',
			$this->MODULE_ID,
			'\Chelnynet\TestDispatcher\TestdispatcherTable',
			'OnAfterUserUpdate'
			);
		// для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для удаления обработчика событий
    function UnInstallEvents(){
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		$eventManager->unRegisterEventHandler(
			'main',
			'OnAfterUserUpdate',
			$this->MODULE_ID,
			'\Chelnynet\TestDispatcher\TestdispatcherTable',
			'OnAfterUserUpdate'
			);
        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для копирования файлов модуля при установке
    function InstallFiles()
    {
        // копируем файлы, которые устанавливаем вместе с модулем, копируем в пространство имен для компонентов которое будет иметь имя модуля
        CopyDirFiles(
            __DIR__ . '/root',
            Application::getDocumentRoot() . '/' ,
            true,
            true
        );

		CopyDirFiles(
			__DIR__ . '/components',
			Application::getDocumentRoot() . '/local/components/',
			true,
			true
			);
        // для успешного завершения, метод должен вернуть true
        return true;
    }

    // метод для удаления файлов модуля при удалении
    function UnInstallFiles()
    {
        // удаляем директорию по указанному пути до папки
		File::deleteFile(
			Application::getDocumentRoot() . '/testdispatcher.php'
		);
		Directory::deleteDirectory(
			Application::getDocumentRoot() . '/local/components/' . $this->MODULE_ID . '/',
		);
        // для успешного завершения, метод должен вернуть true
        return true;
    }
}
