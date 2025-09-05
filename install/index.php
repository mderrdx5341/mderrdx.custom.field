<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;

class mderrdx_custom_field extends CModule
{
    public $MODULE_ID = 'mderrdx.custom.field';
    public $MODULE_NAME = 'Кастомное поле';
    public $MODULE_DESCRIPTION = 'Кастомное поле';

    public function __construct()
    {
        include(__DIR__ . '/version.php');

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }

    private function getModuleDependens(): array 
    {
        return [

        ];
    }

    private function moduleEvents(): array
    {
        //TODO: нужное заменить
        return [
		    [
                "module" =>"iblock",
                "event" => "OnIBlockPropertyBuildList",
                "this_module" => $this->MODULE_ID,
                "class_name" => "\Mderrdx\Custom\Field\CustomFieldJsonSave",
                "method_name" =>  "getTypeDescription"
            ],
            [
                "module" =>"main",
                "event" => "OnUserTypeBuildList",
                "this_module" => $this->MODULE_ID,
                "class_name" => "\Mderrdx\Custom\Field\CustomFieldJsonSave",
                "method_name" =>  "getDescription"
            ],
        ];
    }

    public function DoInstall()
    {
        global $APPLICATION;

        if ($this->checkDependens()) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallEvents();
        
        //$this->InstallDB();
        //$this->InstallEvents();
        //$this->InstallFiles();
        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage("ACADEMY_D7_INSTALL_TITLE"), $this->GetPath() ."/install/step.php");
    }

    public function DoUninstall()
    {
        global $APPLICATION;

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
        $this->UnInstallEvents();
        
        /*
        if ($request['step'] < 2) {
            $APPLICATION->IncludeAdminFile(Loc::getMessage('INSTALL_TITLE'), $this->GetPath() ."/install/unstep1.php");
        } elseif($request['step'] == 2) {
            //$this->UnInstallEvents();
            $this->UnInstallFiles();
            
            if ($request['savedate'] != 'Y') {
                $this->UnInstallDB();
            }

            
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('ACADEMY_D7_INSTALL_TITLE'),
                $this->GetPath() .'/install/unstep2.php'
            );
        }
        */
   
    }

    public function checkDependens(): bool
    {
        global $APPLICATION;
        foreach ($this->getModuleDependens() as $name => $version) {
            if (!ModuleManager::isModuleInstalled($name)) {
                $APPLICATION->throwException("Module: $name  not install");
                return false;
            }
            if (!CheckVersion(ModuleManager::getVersion($name), $version)) {
                $APPLICATION->throwException('Module: ' . $name . ' low version: '. $version);
                return false;
            }
        }

        return true;
    }

    public function InstallDB() : void
    {
        Loader::includeModule($this->MODULE_ID);
        $form = new \Mderrdx\Form\IBlock();
        $form->AddIBlockType();
        $id = $form->AddIblock();
        $form->AddProp($id);
    }

    public function UnInstallDB() : void
    {
        Loader::includeModule($this->MODULE_ID);
        $form = new \Mderrdx\Form\IBlock();
        $form->DelIblock();
    }

    public function InstallFiles()
    {
        CopyDirFiles(
            $this->GetPath() . '/install/components',
            $_SERVER['DOCUMENT_ROOT'] . '/local/components',
            true,
            true
        );
        return true;
    }

    public function UnInstallFiles() 
    {
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/local/components/mderrdx/');
    }

    public function GetPath($notDocumentRoot=false)
    {
        if($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    public function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        foreach($this->moduleEvents() as $event)
        {
            $eventManager->registerEventHandlerCompatible(
                $event["module"],
                $event["event"],
                $event["this_module"],
                $event["class_name"],
                $event["method_name"]
            );
        }
    }

    public function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();
        foreach($this->moduleEvents() as $event)
        {
            $eventManager->unRegisterEventHandler(
                $event["module"],
                $event["event"],
                $event["this_module"],
                $event["class_name"],
                $event["method_name"]
            );
        }
    }
}