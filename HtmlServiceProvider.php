<?php namespace Henrikmartinsson\Html;

use Illuminate\Html\HtmlServiceProvider as BaseHtmlServiceProvider;

class HtmlServiceProvider extends BaseHtmlServiceProvider { 

    /**
     * Add Translations
     * 
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/lang', 'html');
    }

    /**
     * Update alias to use the new form builder.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->alias('form', 'Henrikmartinsson\Html\FormBuilder');

        $this->app->alias('html', 'Henrikmartinsson\Html\HtmlBuilder');
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder()
    {
        $this->app->bindShared('html', function($app)
        {
            return new HtmlBuilder($app['config'], $app['url']);
        });
    }

    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->bindShared('form', function($app)
        {
            $form = new FormBuilder($app['config'], $app['html'], $app['url'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);
        });
    }

}