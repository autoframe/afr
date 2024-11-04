<?php
declare(strict_types=1);


namespace Autoframe\Core\Captcha;

use Autoframe\Core\String\AfrStr;
use Autoframe\Core\Arr\Merge\AfrArrMergeProfileClass;

abstract class AfrCaptcha
{

    protected array $aParams = [];


    function __construct(array $aParams = [])
    {
        //parent::__construct();
        $this->mergeParams($aParams);
    }

    public function mergeParams(array $aParams): object
    {
        $this->aParams = AfrArrMergeProfileClass::getInstance()->arrayMergeProfile($this->aParams, $aParams);
        return $this;
    }

    public function getParams(): array
    {
        return $this->aParams;
    }

    public function setParams(array $aParams): object
    {
        $this->aParams = $aParams;
        return $this;
    }



    abstract public function getHtmlCaptcha(): string;



    /**
     *
     * TODO JSLIB DE GEN OBIECTE!!!!!!!!!
     *
     */
    protected array $aHeadResources = [
        'js' =>[
            'dependencies' => [
                'jQuery' =>'*',
                //'libs' =>'^2.2',
            ],

            0 =>'/path/to/script.js'

        ],
        'css' =>[

            0 =>'/path/to/style.css'
        ],
    ];
    public function getHeadResources(): array
    {
        return $this->aHeadResources;

    }


}