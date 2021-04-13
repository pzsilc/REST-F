# Yoda-Framework
Mini framework do PHPca

Projekt jest podzielony na model-template-view:
- model: 
    - encja aplikacji,
    - znajdują się w folderze models,
    - każdy model musi zawierać stałą TABLE, która jest nazwą tabeli w DB
    - każdy model ma sprecyzowane pola (atrybuty w aplikacji/kokumny w tabeli), które są obiektami typu Field. 
- template:
    - szablony html
    - znajdują się w folderze statics/templates
    - aplikacja wykorzystuje silnik BladeOne do generowania szablonów (dok. <a href="https://github.com/EFTEC/BladeOne">https://github.com/EFTEC/BladeOne</a>)
- view:
    - główna logika aplikacji
    - znajdują się w folderze views
    - główne zadanie - obsługa requestów (zwracanie szablonów, przekierowania, zapisywanie danych itd.)
    
    

Manage.php:
Aplikacja posiada silnik zarządzania kodem w pliku manage.php. Aktywować go można poleceniem php manage.php [polecenie]
Polecenia:
 - migrate - migruje tabele do bazy danych na podstawie modeli i ich pól
 - model [nazwa] - tworzenie modelu
 - view [nazwa] - tworzenie nazwy
 - form [nazwa] - tworzenie formularzu
 
 
 
Forms i Fields (formularze i pola):
Generowanie formularzy to wygodny system do ich tworzenia. Wystarczy stworzyć formularz i powiązać go z modelem za pomocą stałej MODEL.
Formularz zawiera:
- wszystkie potrzebne pola do wyprintowania
- pola można wyprintować za pomocą {!! $form !!}
- is_valid - metodę sprawdzającą poprawność pól
