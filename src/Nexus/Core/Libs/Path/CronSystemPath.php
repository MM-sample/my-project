<?php
namespace Nexus\Core\Libs\Path;

use Nexus\Core\Libs\Traits\Property\PropertyTrait;

class CronSystemPath
{
    use PropertyTrait;

    private string $_cron = '';
    private string $_cronControllers = '';
    private string $_cronModels = '';
    private string $_cronServices = '';
    private string $_cronPublic = '';
    private string $_cronPublicXml = '';

    public function setCronPath(string $_systemPath):void
    {
        $this->setCron(sprintf('%s%s/',    $_systemPath, 'auto/Cron'))
             ->setCronControllers(sprintf('%s%s/', $this->getCron(), 'Controllers'))
             ->setCronModels(sprintf('%s%s/',      $this->getCron(), 'Models'))
             ->setCronServices(sprintf('%s%s/',    $this->getCron(), 'Services'))
             ->setCronPublic(sprintf('%s%s/',      $this->getCron(), 'public'))
             ->setCronPublicXml(sprintf('%s%s/',   $this->getCronPublic(), 'Xml'));
    }

}