# Google reCAPTCHA
-----------

Version: 1.0
Author: Илья Журавлёв
Build Date: 2017-04-11  
Requirements: Symphony 2.3 or higher

## About

Google reCAPTCHA integration for Symphony CMS


## Installation

1. Upload the `google_recaptcha` folder in this archive to your Symphony `extensions` folder.

2. Enable it the usual way.


## Usage

1. Get Google reCAPTCHA API keys

2. Go to System > Preferences and enter your reCAPTCHA site/secret API key pair

3. Add the "Google reCAPTCHA" filter rule to your Event via Blueprints > Events

4. Save the Event

5. Paste this snippet before the closing </head> tag on your HTML template:

```HTML    
<script src="https://www.google.com/recaptcha/api.js"></script>
```

6. Paste this snippet at the end of the <form> where you want the reCAPTCHA widget to appear:

```HTML    
<div class="g-recaptcha" data-sitekey="{$google_recaptcha}"></div>
```

### Explicitly render the reCAPTCHA widget

Deferring the render can be achieved by specifying your onload callback function and adding parameters to the JavaScript resource.

1. Specify your onload callback function.  This function will get called when all the dependencies have loaded.

```HTML
<script type="text/javascript">
var recaptchaCallback = function(){
	$('.g-recaptcha').each(function(index, element){
		$(element).html('');
		grecaptcha.render(element, { sitekey: $(element).data('sitekey') });
	});
};
</script>
```

2. Insert the Javascript resource, setting the `onload` parameter to the name of your onload callback function and the `render` parameter to `explicit`.

```HTML
<script src="https://www.google.com/recaptcha/api.js?onload=recaptchaCallback&amp;render=explicit" async="async" defer="defer"></script>
```

When your callback is executed, you can call the `grecaptcha.render` method from the [Javascript API](https://developers.google.com/recaptcha/docs/display#js_api).


## Changelog

### 1.0
 - Initial release
