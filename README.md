# NOLOH: The Future of Web Development is Now
<p align="center"><a href="https://noloh.com" target="_blank"><img src="https://user-images.githubusercontent.com/199062/262089354-16877707-0495-4bac-b794-807587eb90d0.png" width="400" alt="NOLOH PHP Framework Logo"></a></p>

Open-sourced in 2023, born in 2005 and continuously updated and maintained, NOLOH is a unified, object-oriented PHP Framework that eliminates the need for HTML, JavaScript, and AJAX. Unlike other frameworks, NOLOH is event-driven, lightweight, and on-demand, enabling you to create feature-rich web applications that are both client and server optimized. Experience the ease and power of a single development language and fully integrated framework that's still easier and more efficient than anything else out there.

## Table of Contents
- [Getting Started with NOLOH](#getting-started-with-noloh)
- [Features](#features)
- [Code Examples](#code-examples)
- [Documentation](#documentation)
- [API Reference](#api-reference)
- [Deployment](#deployment)
- [Versioning](#versioning)
- [Authors](#authors)
- [License](#license)
- [Acknowledgments](#acknowledgments)
- [Support and Community](#support-and-community)

## Getting Started with NOLOH
### Prerequisites
NOLOH runs on any version of PHP after PHP 5.1. NOLOH is fully backwards compatible with prior versions of PHP and is tested to work with::
- PHP 5.1+
- PHP 7.0+
- PHP 8.0+

### Installation
```bash
# Download the latest NOLOH release
wget https://github.com/noloh/noloh/releases/latest/download/noloh.zip

# Unzip the latest NOLOH release
unzip noloh.zip
```
### Quick Start / Hello World
Simply create a file, ex.`hello.php`, include NOLOH, and write a basic hello world application:
```php
    <?php
    require_once("noloh/NOLOH.php"); 
     
    class HelloWorld extends WebPage
    {
        function __construct()
        {
            parent::WebPage('My First NOLOH Application');
            System::Alert('Hello World');
        }
    }
    ?>
```
Simply navigate in your browser to `hello.php` URL (assuming you have a functioning web-server with PHP) and enjoy!

## Features
- **Single Unified Language** - NOLOH offers a single, unified language, freeing you from the complexities of HTML, JavaScript, AJAX, and Comet.

- **Fully Object-Oriented** -  Developers can enjoy the advantages of object-oriented programming, including inheritance, abstraction, and modularity, making code reusable and extendable.

- **Lightweight & On-Demand** - NOLOH's lightweight architecture means it only delivers the necessary code for each specific user, resulting in faster websites and WebApps for both the client and server. On-demand resource loading ensures optimal efficiency.

- **View State Management** - NOLOH handles all aspects of managing application and user view state as well as client-server communication for the developer transparently and automatically.

- **Security & Confidentiality** - Your valuable business logic remains secure on the server side, protected from exposure. NOLOH also comes with built-in security measures to safeguard your application against potential threats.
- **SEO & URL Friendly** - NOLOH offers automatic SEO, bookmark-friendly navigation making your application search engine friendly while delivering a seamless user experience.

- **Full CSS Support** - Unlike other frameworks and platforms, NOLOH allows the best of all worlds, allowing for both direct CSS class and style assignment as well as full support for full CSS, LESS, and SASS style sheet support, allowing you to leverage and use CSS where it makes sense.

- **Cross-Browser Compatibility** -  Say goodbye to browser sniffing. NOLOH handles user agent detection and ensures your application runs smoothly across all popular browsers and operating systems.
- **Extensive List of Controls** -  NOLOH comes with a complete library fully customizable controls ranging from the most basic (Button, Label, etc.) to the most complex (Accordian, ListView, Menu, TreeList, etc.) Though you can always use your own custom or 3rd party controls too.

- **Developer-Friendly** - With its extensive list of syntactic sugars, rich library of customizable controls and extensible objects, NOLOH speeds up your development cycle. It also offers simplified lifecycle management and automatic error handling for a hassle-free development experience.

- **Flexible and Extensible** - NOLOH is incredibly adaptable, allowing you to use your own PHP libraries or third-party libraries such as PEAR, or Composer, and does not dictate the tools you use for development.

- **Broad Database Support** - Easily connect to any database, including PostgreSQL, MySQL, and ODBC, using an intuitive and object-oriented approach.

- **Backwards Compatible** - Already have content in HTML or scripts in Javascript? No problem. NOLOH includes `MarkupRegion`, `RIchMarkupRegion`, and `ClientScript` concepts to allow you to fully leverage your existing content, scripts, and libraries to display and use allowing you to transition smoothly.
-  **Mature & Maintained**: Established in 2005, NOLOH is a reliable choice with a proven track record.
- **Why PHP?** - PHP is ubiquitous  and generally runs everywhere. It's one of the most popular and accessible languages for web application development and boasts a large, active community, NOLOH runs atop it to allow anyone to leverage its extensive capabilities without having too many dependencies or setup complexities.

## Code Examples
### Basic Usage
#### Events
```php
require_once("noloh/NOLOH.php"); 
 
class SomeApp extends WebPage
{
    function __construct()
    {
        parent::WebPage('Testing an Event');
        //Instantiate a button
        $button = new Button();
        /*Add the button to the Controls of the Panel and
        sets the Click event of the button to a ServerEvent
        that will call the function SomeFunc, with $button as an argument,
        when the button is clicked*/
        $this->Controls->Add($button)
	        ->Click = new ServerEvent($this, "SomeFunc", $button);
    }
    function SomeFunc($button)
    {
        $button->Text = "Triggered an Event";
    }
}
```
#### Simple Application
Most times you'll actually have an application spread across multiple objects. Generally you'll instantiate and launch your other components from your main WebPage object which you can think of almost as a main() in other languages. Normally you'll have one file per class, which makes things easier  as NOLOH will automatically try to include the file when used.

In this following example we add a SomePanel object that has a Button and TextArea complete with events to your application. Note that you could add as many SomePanel objects you want, in a loop for example, they would all be added and have their own events and properties seperate from each other.

```php
require_once('../noloh/NOLOH.php');

class BasicApp extends WebPage
{
    function __construct()
    {
        parent::WebPage('Testing App w/ Objects');
        //Instantiate and adds a new SomePanel object, complete with its events
        $this->Controls->Add(new SomePanel());
    }
}
class SomePanel extends Panel
{
    function __construct()
    {
        parent::Panel();
        //Instantiate a button and a textarea
        $button = new Button();
        $textArea = new TextArea();
        //Add the button and the textarea to the Controls of the Panel
        $this->Controls->AddRange(
	        $button,
	        $textArea
	    );
        /*Sets the Click event of the button to a ServerEvent
        that will call the function SomeFunc when the button is clicked*/
        $button->Click = new ServerEvent($this, "SomeFunc", $textArea);
    }
    function SomeFunc($textArea)
    {
        $currentText = $textArea->Text;
        System::Alert("The current text of the TextArea is: $currentText");
    }
}
```
#### Data::$Links
Data::$Links is NOLOH's syntactic sugar for accessing your databases that removes much of the challenges when interacting with your databases. It allows for central DB access across your application, and takes care of all formatting and escaping issues, and even allows you to pass parameters natively to your SQL, views, and stored procedures, as if they were local functions. This makes working with your database feel as though it's a natural extension of your application. 
```php
// Sets the connection of your DB1 Data:Link. DB1 is the name you which to reference your database with. It can be any valid Property name.
Data::$Links->DB1 = new DataConnection(Data::Postgres, 'DB1', 'user');
// Executes a regular SQL Query
$results = Data::$Links->DB1->ExecSQL('SELECT * FROM sometable');
// Execute a regular SQL Query with arguments
$results = Data::$Links->DB1->ExecSQL('SELECT * FROM people WHERE state = $1 AND zip = $2', 'New York', '10065');
// or via tokens ordered or associative
$results = Data::$Links->DB1->ExecSQL('SELECT * FROM people WHERE state = :state', array('state', 'New York'));
// Associative
$results = Data::$Links->DB1->ExecSQL('SELECT * FROM people WHERE state = :state', array('state' => 'New York'));
// Get data directly from a view
$results = Data::$Links->DB1->ExecView('public.v_get_all_users');
// or used with stored procedures directly
$results = Data::$Links->DB1->ExecFunction('public.sp_get_user', $id);
// or to execute processes
Data::$Links->DB1->ExecFunction('public.sp_add_user', 'Asher', 'Snyder', 10021);
```
### More Examples
- [Basic Site Example](https://github.com/noloh/StackOverflow-Answer--Basic-Site-Example)
- [Hangman](https://github.com/noloh/HangMan)
- [Image Viewer](https://github.com/noloh/ImageViewer/blob/master/index.php)

### Nodules
Leverage existing NOLOH Modules we call [Nodules](https://github.com/noloh/Nodules), to quickly expand your application's capabilities without re-creating the wheel. Ex. Leverage NavHandler to help you with Navigation, or Google Analytics to quickly include Google Analytics in your app.


## Documentation
- http://www.noloh.com/Docs
- YouTube: http://youtube.com/phpframework

### php|architect
- [NOLOH: The Comprehensive PHP Framework](https://www.phparch.com/magazine/2010-2/may/)
- [NOLOH's Notables](https://www.phparch.com/magazine/2010-2/december/)

## API Reference
- [NOLOH API Docs](http://noloh.com/Docs/#!/api)

## Deployment
### Deploying to Production
Generally in production environments you'll want to move NOLOH out of public accessibly paths and into a private directory. If you previously had NOLOH in publicly accessibly paths such as:
- `/var/www/html/`
- `/usr/share/html`
- `/var/www/appname/public` 

you'll want to move NOLOH one directory up. This shouldn't change much for your use as you'd simply update your `require` line to the updated path, which can be absolute, or relative:
```php
    require_once('../noloh/NOLOH.php');
    // or
    require_once('/var/www/appname/NOLOH.php');
    // or you may have a centrally located NOLOH at
    require_once('/var/www/noloh/NOLOH.php');
```
However, since NOLOH is no longer publicly accessible you'll have to ensure its assets such as images, CSS, etc. are still accessible to the browser. This is an easy thing to do, simply add location block to your `nginx` configuration file (or similar) that routes all NOLOH asset traffic to the non-public path.

```nginx
	location ~* /noloh/.*\.(css|js|gif|png|jpg)$
	{
	    root /var/www/;
	}
```
That it! Now all NOLOH related requests for assets will be routed to the right path and return the appropriate file without issue.

## Versioning
- Fully updated CHANGELOG coming soon!

## Authors
- Asher Snyder 
- Philip Ross
- Flowtrac
- NOLOH LLC.

## License
The NOLOH PHP framework is open-sourced software licensed since August 2023 under the [LGPL v2.1 license](https://www.gnu.org/licenses/old-licenses/lgpl-2.1.en.html#SEC1).

## Acknowledgments
NOLOH has been in development since 2005, and used across numerous industries and applications for many years. It's been a secret sauce to many, and an indispensable partner. We've been wanting to open source it for many years after our commercial attempt concluded. Fortunately the time has come where we can finally share NOLOH with the world without any encumbrances or costs. 

Lots of effort went into making this possible and would like to thank all our past users that have contributed advice, bugs, and support. We hope you enjoy it, and get to leverage its features and experience the joy of NOLOH for yourself.

## Support
- E-mail: support@noloh.com

