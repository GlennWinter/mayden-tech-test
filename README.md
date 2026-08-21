# Shopping List App

### \### Scope

**Timeframe:** 6 hours

### \### Stories aiming to complete

These directly fulfil the objectives stated in the brief's overview:

1. View a list of items on a shopping list
2. Add items to the shopping list
3. Remove stuff from the shopping list
4. When I’ve bought something from my list I want to be able to cross it off the list
5. Persist the data so I can view the list if I move away from the page
6. Total up the prices
7. Put a spending limit in place, alert me if I go over the limit



#### \### Stories not aiming to complete

Deprioritised them due to time constraint and not mentioned in core objective:

6. I want to be able to reorder items on my list - Nice UX to have but not vital to the core product.
7. I want to share my shopping list via email - Nice to have but doesn't affect the functionality of the requirements.
8. User and password protect - Will take up a lot of the given timescale and is something that should be done properly — prioritising it risks the core functionality being incomplete.



#### \### Time estimates

|Stories|Estimate|
|-|-|
|1|\~1h|
|2|\~45min|
|3|\~30min|
|4|\~45min|
|5|\~10min using Eloquent|
|7 \& 8|\~30min (treated as one deliverable — same user journey text in the brief)|
|6 (not building)|\~45min est|
|9 (not building)|\~1h est.|
|10 (not building)|\~2h+ est.|

Remainder of the 6 hours will be spent on tests, improving the frontend and adding accessibility options.



#### \### Requirements

\- PHP 8.3+

\- Composer 2.x

\- Node 20+ and npm

### 

#### \### How to Install

1\. Clone the project: `git clone https://github.com/GlennWinter/mayden-tech-test`

2\. Open a terminal in the project directory

3\. Run `composer install`

4\. Copy the environment file: `cp .env.example .env` (Windows PowerShell: `copy .env.example .env`)

5\. Run `php artisan key:generate`

6\. Run `php artisan migrate` — say yes if prompted to create the SQLite database file

7\. Run `npm install`

8\. Run `composer dev` — this starts the PHP server, queue listener, and Vite dev server together

9\. Visit http://localhost:8000



###### From here you'll be able to create, view and delete a shopping list and change accessibility options. If you click into a shopping list, you'll be able to add items, see your budget, cross off items and also delete them.



#### \### Running tests

php artisan test



#### \### Running all CI checks (lint, format, types, static analysis, tests)

composer ci:check

