<?php
namespace Nexus\Core\Libs\Path;

use Nexus\Core\Libs\Traits\Property\PropertyTrait;

class AppSystemPath
{
    use PropertyTrait;

    private string $_app = '';
    private string $_appHttp = '';
    private string $_appControllers = '';
    private string $_appPublic = '';
    private string $_appPublicXml = '';
    private string $_appPublicImages = '';
    private string $_appPublicImagesProducts = '';

    public function setAppPath(string $_systemPath):void
    {
        $this->setApp(sprintf('%s%s/',                     $_systemPath, 'App'))
             ->setAppHttp(sprintf('%s%s/',                 $this->getApp(), 'Http'))
             ->setAppPublic(sprintf('%s%s/',               $this->getApp(), 'public'))
             ->setAppPublicXml(sprintf('%s%s/',            $this->getAppPublic(), 'xml'))
             ->setAppPublicImages(sprintf('%s%s/',         $this->getAppPublic(), 'images'))
             ->setAppPublicImagesProducts(sprintf('%s%s/', $this->getAppPublicImages(), 'products'))
             ->setAppControllers(sprintf('%s%s/',          $this->getAppHttp(), 'Controllers'));
    }

}