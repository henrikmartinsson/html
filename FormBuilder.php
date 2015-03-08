<?php namespace Henrikmartinsson\Html;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Html\FormBuilder as BaseFormBuilder;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Html\HtmlBuilder;

class FormBuilder extends BaseFormBuilder {

    /**
     * Config instance.
     * 
     * @var ConfigRepository
     */
    protected $config;

    /**
     * Is the model for this form translatable.
     * 
     * @var boolean
     */
    protected $translatable = false;

    /**
     * Create a new form builder instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository $config
     * @param  UrlGenerator  $url
     * @param  HtmlBuilder  $html
     * @param  string  $csrfToken
     * @return void
     */
    public function __construct(ConfigRepository $config, HtmlBuilder $html, UrlGenerator $url, $csrfToken)
    {
        parent::__construct($html, $url, $csrfToken);

        $this->config = $config;
    }

    /**
     * Create a form label element.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    public function label($name, $value = null, $options = array())
    {
        if (isset($options['language']) and strlen($options['language']) > 0)
        {
            $name = $name . '_' . $options['language'];

            unset($options['language']);
        }

        if($value)
        {
            $value = trans($value);
        }

        return parent::label($name, $value, $options);
    }

    /**
     * Create a form input help element
     *
     * @param  string       $name
     * @param  MessageBag   $errors
     * @param  boolean      $block
     * @return string
     */
    public function helpInline($name, $errors, $block = false)
    {
        if ($errors->has($name))
        {
            $class = ($block) ? 'help-block' : 'help-inline';

            $attributes = array('class' => $class);
            
            return '<span'.$this->html->attributes($attributes).'>'.$errors->first($name).'</span>';
        }
        
        return '';
    }

    /**
     * Handle translatable models in form builder.
     *
     * @param  string  $name
     * @return string
     */
    protected function getModelValueAttribute($name)
    {
        if ($this->translatable)
        {
            $translations = $this->config->get('translatable.locales');

            foreach($translations as $translation)
            {
                if(ends_with($name, '_'.$translation))
                {
                    $name = substr($name, 0, -strlen('_'.$translation));

                    if (is_object($this->model))
                    {
                        return object_get($this->model->translate($translation), $this->transformKey($name));
                    }
                    elseif (is_array($this->model))
                    {
                        return array_get($this->model->translate($translation), $this->transformKey($name));
                    }
                }
            }
        }

        return parent::getModelValueAttribute($name);
    }

    /**
     * Set wether model is translatable or not and then create a new model based form builder.
     *
     * @param  mixed  $model
     * @param  array  $options
     * @return string
     */
    public function model($model, array $options = array())
    {
        $this->setTranslatable($model);

        return parent::model($model, $options);
    }

    /**
     * Set the model instance on the form builder. Also set wether model is translatable or not
     *
     * @param  mixed  $model
     * @return void
     */
    public function setModel($model)
    {
        $this->setTranslatable($model);

        parent::setModel($model);
    }

    /**
     * Check if the model is translatable.
     *
     * @param  mixed  $model
     * @return void
     */
    protected function setTranslatable($model)
    {
        $key = 'translatedAttributes';

        if (is_object($model))
        {
            $this->translatable = property_exists(get_class($model), $key);
        }
        elseif (is_array($this->model))
        {
            $this->translatable = array_key_exists($key, $model);
        }
    }

    /**
     * Open form group and set error class.
     *
     * @param  boolean $hasError 
     * @return string
     */
    public function openGroup($hasError = false)
    {
        $class = 'form-group';
        
        if ($hasError)
        {
            $class .= ' has-error';
        }

        $attributes = ['class' => $class];

        return '<div'.$this->html->attributes($attributes).'>';
    }

    /**
     * Close form group
     * 
     * @return string
     */
    public function closeGroup()
    {
        return '</div>';
    }

    public function textGroup($name, $label, $errors, $language = null)
    {
        if($language)
        {
            $name = $name . '_' . $language;
        }

        $html = $this->openGroup($errors->has($name));

        $html .= $this->label($name, $label);

        $html .= $this->text($name, null, ['class' => 'form-control']);

        $html .= $this->helpInline($name, $errors, true);

        $html .= $this->closeGroup();

        return $html;
    }
}