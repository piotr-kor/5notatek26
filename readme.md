# Kilka uwag na temat dodawania bibliotek PHP.
1. W repozytorium nie ma katalogu bibliotek (bo nie powinno go być). Potrzebne biblioteki trzeba sobie pobrać do katalogu projektu. 
    - Lista potrzebnych bibliotek jest w pliku composer.json 
    - Wersje tych bibliotek (i innych wymaganych) są w pliku composer.lock
2. Biblioteki można pobrać ręcznie, ale łatwiej zrobić to narzędziem o nazwie Composer https://getcomposer.org/
3. Żeby dodać do projektu bibliotekę php-jwt należy wywołać w terminalu polecenie 
    ```
    composer require firebase/php-jwt
    ``` 
    To polecenie utworzy też (lub zaktualizuje pliki composer.json i composer.lock)
4. Jeśli composer.json i composer.lock zawierają wszystkie potrzebne informacje o bibliotekach wystrczy polecenie 
    ```
    composer install
    ```
5. Jeśli Composer nie jest poprawnie zainstalowany, a jedynie pobrany do katalogu projektu, wtedy przydatne mogą być polecenia:
    ```
    php composer.phar require firebase/php-jwt
    php composer.phar install
    ```