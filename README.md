QUIQQER Controls
========

Repository for all standard controls supplied with QUIQQER.
Controls are PHP classes which output HTML and thereby create GUI elements.

Package name:

    quiqqer/controls



Features
--------

- Main control class (QUI\Control)


Installation
------------

The package name is: quiqqer/controls


Contribute
----------

- Issue Tracker: https://dev.quiqqer.com/quiqqer/controls/issues
- Source Code: https://dev.quiqqer.com/quiqqer/controls/tree/master


Support
-------

If you found any flaws, have any wishes or suggestions you can send an email
to support@pcsg.de to inform us about your concerns.  

We will try to respond to your request and forward it to the responsible developer.


License
-------

MIT


Usage
-----

### Make your class a QUI-Control:
Make your class extend the main control-class (`QUI\Control`):
```php
class MyClass extends QUI\Control {}
```

### Generate your control's HTML
Overwrite the `getBody()`-method to return the control's HTML:
```php
public function getBody()
{
    return "<h1>Hello world!</h1";
}
```

### Style your control
Use `addCSSFile()` to add an CSS-file:
```php
public function __construct($attributes = array())
{
    // Important when overwriting the constructor!
    parent::__construct($attributes);
    
    $this->addCSSFile("path/to/css/file.css");
}        
```

Or use inline styles for the whole control:
```php
public function __construct($attributes = array())
{
    // Important when overwriting the constructor!
    parent::__construct($attributes);
    
    // Set only one style
    $this->setStyle("property", "value");
    
    // Set multiple styles add once
    $this->setStyles([
        "property1" => "value1",
        "property2" => "value2",
        "property3" => "value3",
    ]);
}        
```


### Use your JavaScript-controls
```php
public function getBody()
{    
    // The value set for "define" in the control
    $this->setJavaScriptControl('the/controls/defined/name');
    
    // Set some options for the JavaScript-control
    $this->setJavaScriptControlOption('option', 'value');
}
```


### More
For further functionalities see the functions (and their documentation) in the main control-class (`QUI\Control`)