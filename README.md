MOSS Twig bridge

[![Build Status](https://travis-ci.org/mossphp/moss-twig-bridge.png?branch=master)](https://travis-ci.org/mossphp/moss-twig-bridge)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mossphp/moss-twig-bridge/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mossphp/moss-twig-bridge/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mossphp/moss-twig-bridge/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mossphp/moss-twig-bridge/?branch=master)

Adds Twig as template engine with additional functionalities:

__resource__ - simplifies usage of bundle resources, creates symlinks to bundle resources/assets (or if unable, copies them, this can be forced too).

```html
<script src="{% resource 'app:front:js:jquery.min.js' %}"></script>
```

Will create symlink to `./src/app/front/resource/js/jquery.min.js` from `./web/resource/app/front/js/jquery.min.js`.
Same applies to other files, as long as they are placed in bundles resource directory.

__url__ - convenient router wrapper - `{{ url('routeName', { with: 'attributes' }) }}) }}` or `{{ url('controller:identifier:', { with: 'attributes' }) }}`

__localisation__ - localisation/translation module, merging functionality from Twigs i18n extension and more sophisticated Pluralization from `Sf2 Translator`.

```twig
{% trans with {'%name%': 'Michal'} into "pl" "Hello %name%" %}

{% trans with {'%name%': 'Michal'} %}Hello %name%{% endtrans %}

{% trans with {'%name%': 'Michal'} into "pl" %}Hello %name%{% endtrans %}

{% transchoice count with {'%name%': 'Michal'} into "pl" %}
{0} %name%, there are no apples|{1} %name%, there is one apple|]1,Inf] %name%, there are %count% apples
{% endtranschoice %}
```
`with {....}` and `into "pl"` parts are optional and can be ommited

__formatting__ - for formatting values to their country specific formats

```php
{{ $value|number }} - formats $value as number
{{ $value|currency }} - formats $value as currency
{{ $value|time }} - formats $value (which must be a \DateTime object) as time
{{ $value|date }} - formats $value (\DateTime object) as date
{{ $value|dateTime }} - formats $value (\DateTime object) as date time
```

And of course, normal Twig extensions are also included.

To use it, just replace default `view` component in bootstrap with:

```php
	'view' => array(
	        'closure' => function (\Moss\Container\Container $container) {
	                $options = array(
	                    'debug' => true,
	                    'auto_reload' => true,
	                    'strict_variables' => false,
	                    'cache' => '../compile/'
	                );

	                $twig = new Twig_Environment(new Moss\Bridge\Loader\File(), $options);
	                $twig->setExtensions(
	                    array(
	                        new Moss\Bridge\Extension\Resource(),
	                        new Moss\Bridge\Extension\Url($container->get('router')),
	                        new Moss\Bridge\Extension\Trans(),
	                        new Twig_Extensions_Extension_Text(),
	                    )
	                );

	                $view = new \Moss\Bridge\View\View($twig);
	                $view
	                    ->set('request', $container->get('request'))
	                    ->set('config', $container->get('config'));

	                return $view;
	            }
	    )
```

For licence details see LICENCE.md
