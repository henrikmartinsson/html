<?php namespace Henrikmartinsson\Html;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Html\HtmlBuilder as BaseHtmlBuilder;
use Illuminate\Routing\UrlGenerator;

class HtmlBuilder extends BaseHtmlBuilder {

    /**
     * Config instance.
     * 
     * @var ConfigRepository
     */
    protected $config;

    /**
     * Create a new HTML builder instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository $config
     * @param  \Illuminate\Routing\UrlGenerator  $url
     * @return void
     */
    public function __construct(ConfigRepository $config, UrlGenerator $url = null)
    {
        parent::__construct($url);

        $this->config = $config;
    }
}