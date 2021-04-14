<h1>Yoda-Framework - mini framework for pure PHP</h1><br/>
<br/>
Application is working exactly the way of pure PHP.<br/>
Yoda is supporting model-template-view approach, it posses  additional forms, fields, management and basic security systems.<br/>



<h1>Config</h1><br/>
Before all you have to set all data in config.php in root folder of your project.<br/>
There are 3 vars:<br/>
- <b>app_name:</b> app of your name<br/>
- <b>app_path:</b> the domain where your app is running (by default this's a localhost)<br/>
- <b>database:</b> the creditials for your database (by default host:localhost, user:root, password:, db:)<br/>



<h1>Model</h1><br/>
Model represents an essential entity (class) in entire application. <br/>
Models has build-in several functions for more comfortable creating the sql queries.<br/>
Models are in folder models.<br/>
Model is made of:<br/>
    - <b>TABLE constant</b> - name of table in database model is associated with<br/>
    - <b>required overrided constructor</b> - inside this function you can declare properties (columns in database / attributes in app). Each field have to be type of any class derived by Field (full list of that classes in section Fields below)<br/>
Properties:<br/>
<ul>
    <li>attributes: <br/>
        <ul>
            <li>id (integer, auto managed, you should not edit that),</li>
            <li>[your own] (columns in db, each of them has to be declared as type of Field),</li>
            <li>TABLE (const string, name of a table in db, require to override in each derived class)</li>
        </ul>
    </li>
    <li>methods:
        <ul>
            <li>constructor:<br/>
                - arguments: none<br/></li>
            <li>__toString: <br/>
                - auto calling<br/>
                - overridable, <br/>
                - by default there's id of object<br/></li>
            <li>qexec:<br/>
                - static protected<br/>
                - purpose: executin the sql query and if there're any data - convert to the array of objects<br/>
                - arguments: $query to executed<br/>
                - returns: array of objects or null<br/></li>
            <li>all:<br/>
                - static public<br/>
                - purpose: get all objects from table<br/>
                - arguments: none<br/>
                - returns: array of objects<br/></li>
            <li>get_object_or_404:<br/>
                - static public<br/>
                - purpose: find exactly 1 object by id in table<br/>
                - arguments: $id<br/>
                - returns: single object or throw 404 if object is not exists<br/></li>
            <li>filter:<br/>
                - static public<br/>
                - purpose: get only those from all objects which are matching to the requires in arguments<br/>
                - arguments: array of exactly-3-elements-length arrays (elements => first: column name; second: math operator; third: value) (e.g. 
                  [['id', '>', 100], ['category_id', '=', 7]])<br/>
                - returns: array of objects<br/></li>
            <li>sql: (not recommended)<br/>
                - static public<br/>
                - purpose: execute raw sql query<br/>
                - arguments: $query<br/>
                - returns: array of objects<br/></li>
            <li>max:<br/>
                - static public<br/>
                - purpose: find object with max value in column<br/>
                - arguments: $col_name (column's name)<br/>
                - returns: single object with max indicated value<br/></li>
            <li>min:<br/>
                - static public<br/>
                - purpose: find object with min value in column<br/>
                - arguments: $col_name (column's name)<br/>
                - returns: single object with min indicated value<br/></li>
            <li>save:<br/>
                - public<br/>
                - purpose: save object into database, function is able to recognize if object is existing in database alright and can adjust sql query for suitable case<br/>
                - arguments: $commit (optional, if set true, object will be not saved, and function returns id of next future object)<br/>
                - returns: none or if $commit=true id which will be assigned to this object when you will save it<br/></li>
            <li>delete:<br/>
                - public<br/>
                - purpose: delete object from database<br/>
                - arguments: none<br/>
                - returns: null<br/></li>
        </ul>
    </li>
</ul>


<h1>Templates</h1><br/>
Templates are ordinary html templates. Yoda supports a BladeOne template engine.<br/>
Full documentation here: <a href="https://github.com/EFTEC/BladeOne">https://github.com/EFTEC/BladeOne</a><br/>



<h1>View</h1><br/>
Views are essential engine for logicals operations in your application.<br/>
Main purposes for views is handling requests (e.g. rendering templates, redirecting, handleing of models).<br/>
Views are in folder views.<br/>
Properties:
<ul>
    <li>attributes:<br/>
        <ul>
            <li>request (protected, object of Request type)</li>
        </ul>
    </li>
    <li>methods:
        <ul>
            <li>constructor:<br/>
            - arguments: none<br/></li>
        <li>add_message:<br/>
            - protected<br/>
            - purpose: adding new messages in app. Messages will be printed automaticlly on the top of page (under header by default)<br/>
            - arguments: $type (string, type of message, e.g. info, error), $text (string, content of message)<br/>
            - returns: null<br/></li>
        <li>generate_csrf:<br/>
            - private<br/>
            - purpose: generate new csrf token and store in session to validate forms<br/>
            - arguments: none<br/>
            - returns: string as html input with token<br/></li>
        <li>redirect:<br/>
            - protected<br/>
            - purpose: redirect user<br/>
            - arguments: $url (destination url of reidrect)<br/>
            - returns: null<br/></li>
        <li>render:<br/>
            - public<br/>
            - purpose: rendering templates<br/>
            - arguments: $dir (target template according to BladeOne system), $args (array of vars you want to pass to your template)<br/>
            - returns: null<br/></li>
        </ul>
    </li>
</ul>



<h1>Forms</h1><br/>
Forms is a system supporting forms on both sides - frontend (auto-generate html forms) and backend (quick validating and preparing forms).<br/>
You can create a form and associate this with any model you want and have in your application. Then you have very easy way to handling your model in <br/>
client-side and easy systems for e.g. validation.<br/>
Properties:
<ul>
    <li>attributes:
        <ul>
            <li>MODEL (required, reference to target class, form is associated with)</li>
            <li>instance (instance of object to e.g. edit)</li>
            <li>fields (auto-assigned, fields of target model as html inputs)</li>
            <li>data (instance of data to valid)</li>
        </ul>
    </li>
    <li>methods:
        <ul>
            <li>constructor:<br/>
                - arguments: none<br/><li>
            <li>is_valid:<br/>
                - public<br/>
                - purpose: validate a retrived data<br/>
                - arguments: none<br/>
                - returns: bool<br/><li>
            <li>attrs:<br/>
                - protected<br/>
                - purpose: adding any html attributes to a single input by overriding this function in your own form class<br/>
                - arguments: $fieldname (string, name of field (name of attribute in model)), $attrs (array made of 2-elements arrays (key and value in html))<br/>
                - returns: null</li>
        </ul>
    </li>
</ul>



<h1>Fields</h1><br/>
Fields are required staff for adjust suitable columns in the database base of attributes in model.<br/>
Each of them has derived by Field class.<br/>
Field class:<br/>
Properties:
<ul>
    <li>attributes:<br/>
        <ul>
            <li>name: (string, name of field in database/html, has to be exactly same like attr name in model)</li>
            <li>settings: (array, settings for sql (e.g. ['required'=>true, 'unique'=>true]))</li>
            <li>attrs: (string, html settings for input)</li>
        </ul>
    </li>
    <li>methods:<br/>
        <ul>
            <li>constructor:<br/>
                - protected<br/>
                - arguments: $name (required, have to be the same like attribute name in model), $settings (optional, settings for sql)</li>
            <li>init:<br/>
                - static public<br/>
                - purpose: create instance of field<br/>
                - arguments: $name (required, have to be the same like attribute name in model), $settings (optional, settings for sql)<br/>
                - returns: object of this field</li>
            <li>add_attr:<br/>
                - public<br/>
                - purpose: add new setting for html<br/>
                - arguments: $attr (string, setting for html ("key=value")<br/>
                - returns: null</li>
            <li>to_sql:<br/>
                - public<br/>
                - purpose: precise a suitable part of whole sql query<br/>
                - arguments: none<br/>
                - returns: string (part of sql query dependent on type of var (e.g. for int will return "INT"))</li>
        </ul>
    </li>
</ul>


<h3>List of fields</h3><br/>
<ul>
    <li><b>CharField</b> - string, (input[type="text"]), required settings: max -> integer</li> 
    <li><b>TextField</b> - string, (textarea), required settings: max -> integer</li>
    <li><b>IntegerField</b> - int, (input[type="number"]), required settings: none</li>
    <li><b>DecimalField</b> - float (input[type="number"](with step)), required settings: number_qty -> integer (total number of digits in value), precision (number of digits after comma), number_qty < precision
    </li>
    <li><b>BooleanField</b> - bool, (input type="checkbox"]), required settings: none</li>
    <li><b>EmailField</b> - string, (input type="email"]), required settings: max -> integer</li>
    <li><b>PasswordField</b> - string, (input type="password"]), required settings: max -> integer</li>
    <li><b>DateTimeField</b> - string, (input type="datetime-local"]), required settings: none</li>
    <li><b>ForeignField</b> - integer, (select over entities of target model), required settings: none <strong>IMPORTANT</strong> this field has own init method and between name and settings arguments there's a target model arg which is required parameter and has to be a reference to class of model
    </li>
</ul>



<h1>Management</h1><br/>
Yoda allows you to quickand easy manage of few things:<br/>
You can run "php manage.php [command]" from cmd.<br/>
List of commands:<br/>
- <b>migrate<b> - (auto-migrate tables into database based of models)<br/>
- <b>view [name]</b> - create new view<br/>
- <b>model [name]</b> - create new model<br/>
- <b>form [name]</b> - create new form<br/>



<h1>Urls</h1><br/>
Urls is the section where you can describe your urls for your application.<br/>
List of your urls is located in urls.php file.<br/>
You should define your urls with url function<br/>
<h3>Url function - parameters list</h3><br/>
- <b>path</b> - path which will trigger on that url<br/>
- <b>view</b> - name of target view<br/>
- <b>action</b> - name of target method in target view<br/>
- <b>http_method</b> - supported http method (by default is 'GET')<br/>



<h1>Requests</h1><br/>
Request class is a place where are stored all informations about requests.<br/>
This class is usefull for views.<br/>
Properties:<br/>
<ul>
    <li>attributes:
        <ul>
            <li>method: (http method)</li>
            <li>post: (data of POST method)</li>
            <li>get: (data of GET method)</li>
            <li>session: (data of SESSION storage)</li>
            <li>server: (data of SERVER storage)</li>
        </ul>
    </li>
    <li>methods:
        <ul>
            <li>get:<br/>
                - public<br/>
                - purpose: get data from GET storage<br/>
                - arguments: key (string, name of var), default_val (any, value returned when key will be not existing)<br/>
                - returns: value of var or default_val is var not exists<br/>
            </li>
            <li>post:<br/>
                - public<br/>
                - purpose: get data from POST storage<br/>
                - arguments: key (string, name of var), default_val (any, value returned when key will be not existing)<br/>
                - returns: value of var or default_val is var not exists<br/>
            </li>
            <li>session:<br/>
                - public<br/>
                - purpose: get data from SESSION storage<br/>
                - arguments: key (string, name of var), default_val (any, value returned when key will be not existing)<br/>
                - returns: value of var or default_val is var not exists<br/>
            </li>
            <li>set_session:<br/>
                - public<br/>
                - purpose: set new session var<br/>
                - arguments: key (string, key for new var), value (any, value for new var)<br/>
                - returns: null<br/>
            </li>
            <li>unset_session:<br/>
                - public<br/>
                - purpose: unset existing session var<br/>
                - arguments: key (string, key of var)<br/>
                - returns: null<br/>
            </li>
        </ul>
    </li>
</ul>



<h1>Quick tutorial</h1><br/>
<ol style='font-size: 13px;'>
    <li>Create and set database (config.php)</li>
    <li>Create model (php manage.php model MyModel) and set below props in the constructor:<br/>
        <b>
            $this->name = CharField::init('name', ['max' => 128]);<br/>
            $this->x = IntegerField::init('x');<br/>
            $this->y = DecimalField::init('y', ['precision' => 10, 'numbers_qty' => 7]);<br/>
        </b>
    </li>
    <li>Create view (php manage.php view MyModelView)</li>
    <li>Go to urls.php in root folder and let's add this line to your $urls var: <b>url('/', 'MyModelView', 'index', 'GET')</b>
    <li>Go to views/MyModelView.php and declare inside that new function:<br/>
        <b>
            public function index(){<br/>
                $my_entities = MyModel::all();<br/>
                return $this->render('index', ['my_entities' => $my_entities]);<br/>
            }<br/>
        </b>
    </li>
    <li>Attach your model to view by line <b>require_once __dir__."/../models/MyModel.php";</b> it's necessery</li>
    <li>Go to statics/templates and create new file named index.blade.php</li>
    <li>inside this file just write something like that:<br/>
        <b>
            @extends('layout')<br/>
            @section('main')<br/>
                My entities:<br/>
                @foreach($my_entities as $entity)<br/>
                    <div><br/>
                        Name: {{ $entity->name }}<br/>
                        X: {{ $entity->x }}<br/>
                        Y: {{ $entity->y }}<br/>
                    </div><br/>
                @endforeach<br/>
            @endsection<br/>
        </b>
    </li>
    <li>Now if you have added few entities into your database, tou should be able to see your data</li>
