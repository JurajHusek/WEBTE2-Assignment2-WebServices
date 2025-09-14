1. Vytvorte API pre poskytovanie webovej služby týkajúcej sa držiteľov Nobelových cien.
Jednotlivé metódy API musia umožňovať:
1.1 pridať nového nositeľa Nobelovej ceny spolu so všetkými dostupnými informáciami (ošetrite, aby nebolo možné pridať osobu, ktorá už v databáze existuje; jedna osoba môže získať viacero ocenení)
1.2 pridať naraz viacerých nositeľov na základe JSON súboru
1.3 modifikovať ľubovoľné údaje o osobe v databáze
1.4 vymazať osoby z databázy (spolu s osobou sa musia vymazať aj všetky informácie, ktoré sa k nej viažu)
1.5 získať zoznam držiteľov Nobelových cien na základe zadaného roku, kategórie a krajiny (ak niektorý parameter nie je zadaný, filter sa nepoužije)

Webovú službu vytvorte pomocou jednej z nasledujúcich alternatív: XML-RPC, JSON-RPC, SOAP alebo REST.
Pri riešení sa bude kontrolovať, či je funkcionalita implementovaná naozaj pomocou zvolenej technológie.
Ak zvolíte REST, dbajte na dodržanie princípov REST architektúry.

2. Skontrolujte, či sa aj pri zobrazovaní údajov v tabuľke používajú výhradne metódy webovej služby (vrátane aplikácie príslušných filtrov).

3. Správne odchytávajte chyby a vracajte príslušné stavové kódy (200, 400, 500 a podobne).

4. Sprístupnite podstránku s dokumentáciou API.
4.1 Ak vytvoríte SOAP službu s WSDL dokumentom, postačí vizualizovať metódy pomocou dostupného WSDL viewera.
4.2 V prípade REST služby odporúčame vytvoriť popis pomocou knižnice, ktorá umožní jednotlivé endpointy aj testovať (napríklad SwaggerUI).

5. Otestujte metódy API rozšírením funkcionality z prvého zadania.
5.1 V privátnej zóne doplňte možnosť pridávať, upravovať a mazať údaje.
5.2 Pri pridávaní nositeľov si zvoľte, či budete zadávať údaje jednotlivo alebo hromadne.
5.3 Pri úprave údajov predvyplňte formulár hodnotami z databázy, aby bol systém intuitívny.

6. Nezabudnite na validáciu vstupov pri registrácii, prihlasovaní a úpravách záznamov – musí prebiehať na front-ende aj na back-ende.
Nepoužívajte funkcie JavaScriptu ako alert alebo confirm ani žiadne iné vyskakovacie okná – bude to penalizované.
