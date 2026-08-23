# Shopping List App

### Scope

**Timeframe:** 6 hours

### Stories aiming to complete

These directly fulfil the objectives stated in the brief's overview:

1. View a list of items on a shopping list
2. Add items to the shopping list
3. Remove stuff from the shopping list
4. When I’ve bought something from my list I want to be able to cross it off the list
5. Persist the data so I can view the list if I move away from the page
6. Total up the prices
7. Put a spending limit in place, alert me if I go over the limit



### Stories not aiming to complete

Deprioritised them due to time constraint and not mentioned in core objective:

6. I want to be able to reorder items on my list - Nice UX to have but not vital to the core product.
7. I want to share my shopping list via email - Nice to have but doesn't affect the functionality of the requirements.
8. User and password protect - Will take up a lot of the given timescale and is something that should be done properly — prioritising it risks the core functionality being incomplete.



### Time estimates

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



### Requirements

- PHP 8.3+ with the required PHP extensions enabled including SQLite

- Composer 2.x

- Node.js 20+ (includes npm)

### How to Install

1\. Clone the project: `git clone https://github.com/GlennWinter/mayden-tech-test`

2\. Open a terminal in the project directory

3\. Run `composer install`

4\. Copy the environment file: `cp .env.example .env` (Windows PowerShell: `copy .env.example .env`)

5\. Run `php artisan key:generate`

6\. Run `php artisan migrate` — say yes if prompted to create the SQLite database file

7\. Run `npm install`

8\. Run `composer dev` — this starts the PHP server, queue listener, and Vite dev server together

9\. Visit http://localhost:8000



From here you'll be able to create, view and delete a shopping list and change accessibility options. If you click into a shopping list, you'll be able to add items, see your budget, cross off items and also delete them.



### Running tests

php artisan test


### Running all CI checks (lint, format, types, static analysis, tests)

composer ci:check

### Retrospective
If I had more time, I would complete the remaining stories from the brief:

-Reordering items: I would add a sort_order column to the shopping list items table and provide an endpoint for updating item positions. The interface could then support drag-and-drop reordering.

-Sharing a shopping list by email: I would use Laravel’s mail functionality to send the recipient a secure link to the shopping list. Depending on the access requirements, this could be implemented using a temporary signed URL.

-User authentication and password protection: I would add user accounts and associate each shopping list with its owner. I would use Laravel Sanctum for authentication and authorisation policies to ensure users could only manage shopping lists they were permitted to access.

### Future improvements:

-Improve accessibility by adding further user preferences and testing the application using keyboard-only navigation, screen readers and automated WCAG auditing tools.

-Improve frontend error handling by displaying clear validation and server-error messages, as well as adding loading and disabled states to prevent duplicate submissions.

-Use Laravel’s centralised exception handling and logging, with targeted try/catch blocks for recoverable failures, such as an email failing to send.

-Add application monitoring so unexpected errors can be identified and investigated more easily.

-Add database indexes where appropriate and review queries for potential performance issues as the amount of data increases.

-Add end-to-end tests for important user journeys, such as creating a list, adding items, updating the budget and deleting a list.

-Refine the interface with clearer feedback, smoother interactions and more consistent styling.

-As the application grows, I would introduce a shared styling structure for common design elements, such as colours, spacing, buttons and form controls. The current component-scoped styles are appropriate for the application’s present size, but centralising shared styles would help maintain consistency and make the interface easier to scale.

### What I'd do better next time:

-I would remove the unused files and dependencies included with the initial Laravel Herd installation earlier in the development process.

-The task coincided with a pre-booked holiday, so I prioritised completing it during the shorter periods I had available. If circumstances allowed, I would complete a similar task in fewer, longer sessions to maintain continuity and reduce context switching.

-I would document technical decisions, assumptions and API behaviour throughout development rather than leaving most of the documentation until the end.

-I would run the complete CI checks throughout development rather than primarily towards the end, allowing formatting, type and linting issues to be identified earlier.


### Final 

I've really enjoyed working through the task and I'm looking forward to discussing my approach and the decisions I made.

Thank you for your time, Glenn Winter
