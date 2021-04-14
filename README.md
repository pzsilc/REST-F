<h1>Yoda-Framework - mini framework for pure PHP</h1>

Application is working exactly the way of pure PHP.
Yoda is supporting model-template-view approach, it posses  additional forms, fields, management and basic security systems.



<h1>Config</h1>
Before all you have to set all data in config.php in root folder of your project.
There are 3 vars:
- app_name: app of your name
- app_path: the domain where your app is running (by default this's a localhost)
- database: the creditials for your database (by default host:localhost, user:root, password:, db:)



<h1>Model</h1>
Model represents an essential entity (class) in entire application. 
Models has build-in several functions for more comfortable creating the sql queries.
Models are in folder models.
Model is made of:
    - TABLE constant - name of table in database model is associated with
    - required overrided constructor - inside this function you can declare properties (columns in database / attributes in app). Each field have to be type of any class derived       by Field (full list of that classes in section Fields below)
Properties:
    - attributes: 
        - id (integer, auto managed, you should not edit that), 
        - [your own] (columns in db, each of them has to be declared as type of Field), 
        - TABLE (const string, name of a table in db, require to override in each derived class)
    - methods:
        - constructor:
            - arguments: none
        - __toString: 
            - auto calling
            - overridable, 
            - by default there's id of object
        - qexec:
            - static protected
            - purpose: executin the sql query and if there're any data - convert to the array of objects
            - arguments: $query to executed
            - returns: array of objects or null
        - all:
            - static public
            - purpose: get all objects from table
            - arguments: none
            - returns: array of objects
        - get_object_or_404:
            - static public
            - purpose: find exactly 1 object by id in table
            - arguments: $id
            - returns: single object or throw 404 if object is not exists
        - filter:
            - static public
            - purpose: get only those from all objects which are matching to the requires in arguments
            - arguments: array of exactly-3-elements-length arrays (elements => first: column name; second: math operator; third: value) (e.g. 
              [['id', '>', 100], ['category_id', '=', 7]]
            - returns: array of objects
        - sql: (not recommended)
            - static public
            - purpose: execute raw sql query
            - arguments: $query
            - returns: array of objects
        - max:
            - static public
            - purpose: find object with max value in column
            - arguments: $col_name (column's name)
            - returns: single object with max indicated value
        - min:
            - static public
            - purpose: find object with min value in column
            - arguments: $col_name (column's name)
            - returns: single object with min indicated value
        - save:
            - public
            - purpose: save object into database, function is able to recognize if object is existing in database alright and can adjust sql query for suitable case
            - arguments: $commit (optional, if set true, object will be not saved, and function returns id of next future object)
            - returns: none or if $commit=true id which will be assigned to this object when you will save it
        - delete:
            - public
            - purpose: delete object from database
            - arguments: none
            - returns: null


<h1>Templates</h1>
Templates are ordinary html templates. Yoda supports a BladeOne template engine.
Full documentation here: <a href="https://github.com/EFTEC/BladeOne">https://github.com/EFTEC/BladeOne</a>


<h1>View</h1>
Views are essential engine for logicals operations in your application.
Main purposes for views is handling requests (e.g. rendering templates, redirecting, handleing of models).
Views are in folder views.
Properties:
    - attributes:
        - request (protected, object of Request type)
    - methods:
        - constructor:
            - arguments: none
        - add_message:
            - protected
            - purpose: adding new messages in app. Messages will be printed automaticlly on the top of page (under header by default)
            - arguments: $type (string, type of message, e.g. info, error), $text (string, content of message)
            - returns: null
        - generate_csrf:
            - private
            - purpose: generate new csrf token and store in session to validate forms
            - arguments: none
            - returns: string as html input with token
        - redirect:
            - protected
            - purpose: redirect user
            - arguments: $url (destination url of reidrect)
            - returns: null
        - render:
            - public
            - purpose: rendering templates
            - arguments: $dir (target template according to BladeOne system), $args (array of vars you want to pass to your template)
            - returns: null


<h1>Forms</h1>
Forms is a system supporting forms on both sides - frontend (auto-generate html forms) and backend (quick validating and preparing forms).
You can create a form and associate this with any model you want and have in your application. Then you have very easy way to handling your model in 
client-side and easy systems for e.g. validation.
Properties:
    - attributes:  
        - MODEL (required, reference to target class, form is associated with)
        - instance (instance of object to e.g. edit)
        - fields (auto-assigned, fields of target model as html inputs)
        - data (instance of data to valid)
    - methods:
        - constructor:
            - arguments: none
        - is_valid:
            - public
            - purpose: validate a retrived data
            - arguments: none
            - returns: bool
        - attrs:
            - protected
            - purpose: adding any html attributes to a single input by overriding this function in your own form class
            - arguments: $fieldname (string, name of field (name of attribute in model)), $attrs (array made of 2-elements arrays (key and value in html))
            - returns: null


<h1>Fields</h1>
Fields are required staff for adjust suitable columns in the database base of attributes in model.
Each of them has derived by Field class.
Field class:
Properties:
    - attributes:
        - name: (string, name of field in database/html, has to be exactly same like attr name in model)
        - settings: (array, settings for sql (e.g. ['required'=>true, 'unique'=>true]))
        - attrs: (string, html settings for input)
    - methods:
        - constructor:
            - protected
            - arguments: $name (required, have to be the same like attribute name in model), $settings (optional, settings for sql)
        - init:
            - static public
            - purpose: create instance of field
            - arguments: $name (required, have to be the same like attribute name in model), $settings (optional, settings for sql)
            - returns: object of this field
        - add_attr:
            - public
            - purpose: add new setting for html
            - arguments: $attr (string, setting for html ("key=value")
            - returns: null
        - to_sql:
            - public
            - purpose: precise a suitable part of whole sql query
            - arguments: none
            - returns: string (part of sql query dependent on type of var (e.g. for int will return "INT"))


<h3>List of fields</h3>
<ul>
    <li>CharField - string, (input[type="text"]), required settings: max -> integer</li> 
    <li>TextField - string, (textarea), required settings: max -> integer</li>
    <li>IntegerField - int, (input[type="number"]), required settings: none</li>
    <li>DecimalField - float (input[type="number"](with step)), required settings: number_qty -> integer (total number of digits in value), precision (number of digits after           comma), number_qty < precision
    </li>
    <li>BooleanField - bool, (input type="checkbox"]), required settings: none</li>
    <li>EmailField - string, (input type="email"]), required settings: max -> integer</li>
    <li>PasswordField - string, (input type="password"]), required settings: max -> integer</li>
    <li>DateTimeField - string, (input type="datetime-local"]), required settings: none</li>
    <li>ForeignField - integer, (select over entities of target model), required settings: none <strong>IMPORTANT</strong> this field has own init method and between name and         settings arguments there's a target model arg which is required parameter and has to be a reference to class of model
    </li>
</ul>
</ul>



<h1>Management</h1>
Yoda allows you to quickand easy manage of few things:
You can run "php manage.php [command]" from cmd.
List of commands:
- migrate - (auto-migrate tables into database based of models)
- view [name] - create new view
- model [name] - create new model
- form [name] - create new form



<h1>Urls</h1>
Urls is the section where you can describe your urls for your application.
List of your urls is located in urls.php file.
You should define your urls with url function
<h3>Url function - parameters list</h3>
- $path - path which will trigger on that url
- $view - name of target view
- $action - name of target method in target view
- $http_method - supported http method (by default is 'GET')



<h1>Requests</h1>
Request class is a place where are stored all informations about requests.
This class is usefull for views.
Properties:
    - attributes:
        - method: (http method)
        - post: (data of POST method)
        - get: (data of GET method)
        - session: (data of SESSION storage)
        - server: (data of SERVER storage)
    - methods:
        - get:
            - public
            - purpose: get data from GET storage
            - arguments: key (string, name of var), default_val (any, value returned when key will be not existing)
            - returns: value of var or default_val is var not exists
        - post:
            - public
            - purpose: get data from POST storage
            - arguments: key (string, name of var), default_val (any, value returned when key will be not existing)
            - returns: value of var or default_val is var not exists
        - session:
            - public
            - purpose: get data from SESSION storage
            - arguments: key (string, name of var), default_val (any, value returned when key will be not existing)
            - returns: value of var or default_val is var not exists
         - set_session:
            - public
            - purpose: set new session var
            - arguments: key (string, key for new var), value (any, value for new var)
            - returns: null
         - unset_session:
            - public
            - purpose: unset existing session var
            - arguments: key (string, key of var)
            - returns: null



<h1>Quick tutorial<h1>
<ol>
    <li>Create and set database (config.php)</li>
    <li>Create model (php manage.php model MyModel) and set below props in the constructor:
        <b>
            $this->name = CharField::init('name', ['max' => 128]);
            $this->x = IntegerField::init('x');
            $this->y = DecimalField::init('y', ['precision' => 10, 'numbers_qty' => 7]);
        </b>
    </li>
    <li>Create view (php manage.php view MyModelView)</li>
    <li>Go to urls.php in root folder and let's add this line to your $urls var: <b>url('/', 'MyModelView', 'index', 'GET')</b>
    <li>Go to views/MyModelView.php and declare inside that new function:
        <b>
            public function index(){
                $my_entities = MyModel::all();
                return $this->render('index', ['my_entities' => $my_entities]);
            }
        </b>
    </li>
    <li>Attach your model to view by line <b>require_once __dir__."/../models/MyModel.php";</b> it's necessery</li>
    <li>Go to statics/templates and create new file named index.blade.php</li>
    <li>inside this file just write something like that:
        <b>
            @extends('layout')
            @section('main')
                <h1>My entities:</h1>
                <ul>
                @foreach($my_entities as $entity)
                    <li>
                        Name: {{ $entity->name }}
                        X: {{ $entity->x }}
                        Y: {{ $entity->y }}
                    </li>
                @endforeach
                </ul>
            @endsection
        </b>
    </li>
    <li>Now if you have added few entities into your database, tou should be able to see your data</li>
