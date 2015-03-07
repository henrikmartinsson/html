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

    /**
     * Create translation navigation
     *
     * @param  string       $name
     * @param  array        $attributes
     * @return string
     */
    public function navTranslations($name, $attributes = [], $errors = null)
    {
        $locales = $this->config->get('translatable.locales');

        $html = '';

        foreach ($locales as $locale)
        {
            $liAttributes['class'] = '';
            $liAttributes['role'] = 'presentation';

            // Activate the first tab
            if ($locale === reset($locales))
            {
                $liAttributes['class'] = 'active';
            }

            $li = '<li'.$this->attributes($liAttributes).'>';

            $link = $this->link('#'.$name.$locale, trans('locales.language.' . $locale), ['data-toggle' => 'tab', 'role' => 'tab']);

            $html .= $li.$link.'</li>';
        }

        return '<ul'.$this->attributes($attributes).'>'.$html.'</ul>';
    }
}