<h1>Restowy "framework"  do phpca.</h1>
<br/>
<h3>Struktura projektu</h3>
<ul>
<li><b>app</b> -> Kod źródłowy aplikacji</li>
<li><b>server</b> -> ustawienia aplikacji</li>
<li><b>core (w starszych wersjach 'engine')</b> -> kod frameworku</li>
</ul>
<br/>
<h3>app</h3>
<ul>
  <li>
    <b>models.php - Modele aplikacji</b>
    <p>
      Klasy pochodne od klasy Model połączone z bazą danych przez migracje.<br/>
      W konstruktorze modelu należy sprecyzować pola, które muszą być instancją klasy Fields\Field.<br/>
      Pole definiuje się przez przypisanie $this->pole = new Fields\PoleField::init(ustawienia);<br/>
      Ustawienia definiuje tablica asocjacyjna, np. ['max_length' => 128].
      <h4>Dostępne pola:</h4>
      <ul>
        <li>CharField: ustawienia(max_length)</li>
        <li>TextField: ustawienia()</li>
        <li>IntegerField: ustawienia()</li>
        <li>DecimalField: ustawienia(precision, decimal_point)</li>
        <li>BooleanField: ustawienia()</li>
        <li>EmailField: ustawienia(max_length)</li>
        <li>PasswordField: ustawienia(max_length)</li>
        <li>DateField: ustawienia()</li>
        <li>DateTimeField: ustawienia()</li>
        <li>CharField: ustawienia(max_length)</li>
        <li>FileField: ustawienia(accepts)</li>
        <li>JSONField: ustawienia()</li>
        <li>ForeignField: (pierwszy argument 'model' -> referencja do klasy) ustawienia()</li>
      </ul>
      Wszystkie pola przyjmują również ustawienia: required, default, unique<br/>
      Po zdefiniowaniu modeli można je migrować poleceniem "php manage migrate"
    </p>
  </li>
  <li>
    <b>permissions.php - Metody dostępu</b>
    <p>
      Klasy pochodne od klasy BasePermission.<br/>
      Precyzują czy użytkownik ma dostęp do zasobu.<br/>
      Każda klasa musi zawierać metodę "check($request, $view, $method)", która zwraca Boolean (czy jest dozwolony dostęp).
    </p>
  </li>
  <li>
    <p>
      <b>serializers.php - Klasy serializujące</b>
      <p>
        Klasy pochodne od klasy Serializer.<br/>
        Argumenty:<br/>
        - instance (Model instance): obiekt klasy modelu<br/>
        - data (array): dane do zapisania<br/>
        - many (Boolean): true jeśli serializowana ma być tablica obiektów<br/>
        Pola wymagane:<br/>
        - const MODEL -> referencja do modelu<br/>
        - const FIELDS -> pola serializowane<br/>
        Pola niewymagane:<br/>
        - const READ_FIELDS -> pola tylko do odczytu<br/>
        - const WRITE_ONLY -> pola tylko do wpisania<br/>
        Pole dodatkowe:<br/>
        - errors (array): tablica błędów jeśli is_valid zwróci false<br/>
        - request (object): obiekt klasy Request<br/>
        Metody:<br/>
        - is_valid() -> walidacja danych (pole data) (wymagane do zapisania danych)<br/>
        - create($validated_data) -> nadpisanie zapisywania instancji<br/>
        - destroy() -> nadpisanie usuwania<br/>
      </p>
    </p>
  </li>
  <li>
    <b>traits.php - Traity phpcowe</b>
  </li>
  <li>
    <b>views.php - Widoki, główna logika aplikacji</b>
    <p>
      Klasy pochodne od Views\View.<br/>
      Klasy te dostarczają metody do obsługi CRUDa modelu (_list, retrieve, create, update, destroy).<br/>
      Pola wymagane:<br/>
      - const MODEL - referencja do modelu<br/>
      - const SERIALIZER - referencja do serializera<br/>
      - const PERMISSIONS - tablica klas permisji<br/>
      Metody:<br/>
      - get_queryset($request) - zwraca tablice obiektów dla _list<br/>
      - get_object($request, $id) - zwraca obiekt<br/>
      - get_model() - zwraca klasę modelu<br/>
      - get_serializer() - zwraca instancję serializera<br/>
      - as_view() - mapuje metody klasy na pojedyncze routy (zastosowanie w urls.php)
    </p>
  </li>
</ul>
<hr/>
<h4>settings.json</h4>
<p>
  Ustawienia aplikacji<br/>
  Pola "databases" i "defaultDatabase":<br/>
  <ul>
    <li>
      "databases" - tablica konfiguracji baz danych. Można podłączyć więcej<br/>
      niż 1 bazę danych. Pole alias jest nazwą bazy (w aplikacji) żeby łatwiej można było się zorientować.
    </li>
    <li>
      "defaultDatabase" - alias domyślnej bazy danych
    </li>
  </ul>
</p>
<hr/>
<h4>urls.php</h4>
<p>
  Ustawienia urlów<br/>
  <b>Metody klasy URL</b>
  <ul>
    <li>
      add($url(string), $route(array), $http_method(string, niewymagany, domyślnie 'GET'))<br/>
      <b>Argument $route</b>
      Jeśli chcemy zmapować klasę i jej metody do routerów wystawczy użyć składni KlasaWidoku::as_view()<br/>
      Jeśli chcemy dodać własną metodę należy użyć składni KlasaWidok::_customMethod(), ważny jest '_' przed nazwą metody.<br/>
      Taka metoda domyślnie przyjmie argument $request. Jeśli chcemy nadać jej więcej argumentów, trzeba ustawić dodatkowe zmienne <br/>
      w urlu za pomocą składni '<:pole>', np. 'posts/<:id>/comments/'. Taka metoda dostanie dodatkowo parametr $id jako argument.
    </li>
    <li>_include($target(string)) - dołącza routy innej aplikacji</li>
  </ul>
</p>
