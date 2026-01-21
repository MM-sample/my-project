<?php
namespace Nexus\Core\Libs\Path;

use Nexus\Core\Libs\Traits\Property\PropertyTrait;

class ApiSystemPath
{
    use PropertyTrait;

    private string $_api = '';
    private string $_apiHttp = '';
    private string $_apiControllers = '';
    private string $_apiModels = '';
    private string $_apiRequests = '';
    private string $_apiServices = '';
    private string $_apiRules = '';

    public function setApiPath(string $_systemPath):void
    {
        $this->setApi(sprintf('%s%s/',            $_systemPath, 'api'))
             ->setApiHttp(sprintf('%s%s/',        $this->getApi(), 'Http'))
             ->setApiControllers(sprintf('%s%s/', $this->getApiHttp(), 'Controllers'))
             ->setApiModels(sprintf('%s%s/',      $this->getApiHttp(), 'Models'))
             ->setApiRequests(sprintf('%s%s/',    $this->getApiHttp(), 'Requests'))
             ->setApiServices(sprintf('%s%s/',    $this->getApiHttp(), 'Services'))
             ->setApiRules(sprintf('%s%s/',       $this->getApiRequests(), 'Rules'));
    }

}