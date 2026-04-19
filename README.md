# Greenaxe Offer

Greenaxe Offer to szkielet aplikacji webowej do zarzadzania kosztorysami projektowania ogrodow.

Stack:
- Laravel 13
- Laravel Sail (Docker)
- MySQL 8
- Redis
- Mailpit

## Start projektu (Docker)

1. Zbuduj i uruchom kontenery:

```bash
./vendor/bin/sail up -d
```

2. Uruchom migracje:

```bash
./vendor/bin/sail artisan migrate
```

3. Otworz aplikacje:

- http://localhost

## Struktura domeny

Podstawowe encje:
- Client: klient koncowy
- GardenProject: projekt ogrodu klienta
- Offer: oferta/kosztorys dla projektu
- CostItem: pozycja kosztorysu

Relacje:
- Client 1..N GardenProject
- GardenProject 1..N Offer
- Offer 1..N CostItem

## API (startowe endpointy)

Zdefiniowane trasy REST:
- /api/clients
- /api/garden-projects
- /api/offers
- /api/cost-items

## Logika kosztorysu

Pozycje kosztorysu (`CostItem`) automatycznie wyliczaja `line_total`:

line_total = quantity * unit_price

Oferta (`Offer`) automatycznie przelicza:

- material_cost: suma `line_total` ze wszystkich pozycji
- total_net: (labor_cost + material_cost) * (1 + margin_percent / 100)
- total_gross: total_net * (1 + tax_percent / 100)

## Dalszy rozwoj

Nastepne kroki to:
- autoryzacja (Laravel Breeze / Sanctum)
- frontend panelu ofert (Blade lub Vue/React)
- eksport PDF oferty
- role i uprawnienia
