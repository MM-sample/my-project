<?php

namespace Nexus\Core\Libs\Path;

use Nexus\Core\Libs\Traits\Property\PropertyTrait;

class SystemPath
{
    use PropertyTrait;

    /** System Path */
    private string $_root = "";

    private string $_env = "";

    private string $_system = "";

    private string $_systemCore = "";

    private string $_systemLibs = "";

    private string $_systemDB = "";

    private string $_config = "";

    private string $_tmp = "";

    private string $_cache = "";

    private string $_logs = "";

    private string $_support = "";

    private string $_validate = "";

    private string $_rules = "";

    private string $_routes = "";

    public function setSystemPath(string $_path):void
    {
        $this->setRoot(sprintf('%s/'          ,$_path))
             ->setSystem(sprintf('%s%s/'      ,$this->getRoot(), 'Nexus'))
             ->setEnv(sprintf('%s%s/'         ,$this->getSystem(), '.env'))
             ->setSystemCore(sprintf('%s%s/'  ,$this->getSystem(), 'Core'))
             ->setConfig(sprintf('%s%s/'      ,$this->getSystem(), 'Config'))
             ->setTmp(sprintf('%s%s/'         ,$this->getSystem(), 'tmp'))
             ->setRoutes(sprintf('%s%s/'      ,$this->getSystem(), 'Routes'))
             ->setCache(sprintf('%s%s/'       ,$this->getTmp(), 'cache'))
             ->setLogs(sprintf('%s%s/'        ,$this->getTmp(), 'logs'))
             ->setSystemLibs(sprintf('%s%s/'  ,$this->getSystemCore(), 'Libs'))
             ->setSystemDB(sprintf('%s%s/'    ,$this->getSystemCore(), 'DB'))
             ->setSupport(sprintf('%s%s/'     ,$this->getSystemCore(), 'Support'))
             ->setValidate(sprintf('%s%s/'    ,$this->getSupport(), 'Validate'))
             ->setRules(sprintf('%s%s/'       ,$this->getValidate(), 'Rules'));
        return;
    }
}