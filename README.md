# BREWSIP — WD1 PHP / MySQL Assignment

## Setup on Mac with XAMPP
1. Copy the entire `starcoffee-complete` folder into `/Applications/XAMPP/htdocs/`.
2. Open XAMPP Manager and start Apache and MySQL.
3. Open `http://localhost/phpmyadmin`, select **New**, create database **coffee_shop** (utf8mb4).
4. Select the database and import `database/coffee_shop.sql` (the original supplied SQL). This dump contains the seven original table definitions but **no rows**.
5. Import `database/seed.sql` to add example categories, coffee products, prices, and images. **Only import seed.sql into an empty category/menu_item dataset**.
6. Check `config/db.php` for your local database credentials (XAMPP default root / empty password).
7. Open `http://localhost/starcoffee-complete/setup_admin.php` to create the first administrator. **Delete setup_admin.php immediately afterward.**
8. Visit `http://localhost/starcoffee-complete/` for the customer website and `/admin/login.php` for administration.

## Features
- Home, Menu, About, Cart, Checkout, My Orders, Reviews, customer login/registration.
- Admin login/logout, dashboard, category and menu CRUD, order status management, SQL sales reporting.
- Responsive layout, active navigation, server-side validation, prepared statements, password hashing, session authentication, CSRF tokens, transactions, and stock checks.
- Images are included from the supplied Archive.zip in `assets/img/`.

## Notes
- Checkout stores orders and deducts stock; **there is no real payment gateway**.
- Custom drink configuration from the HTML prototype is **not persisted**, because the provided database has no customization columns. Menu ordering uses the existing schema.
- The original SQL has no data. The sample seed is optional and uses the original table structure.
- Menu deletion can be prevented by foreign-key constraints if an item has orders or reviews; edit availability to hide it instead.
- A cancelled order does not automatically restock items. Status changes do not process refunds.
- Do not deploy the first-admin setup page publicly. Configure HTTPS and non-default DB credentials for production.
- `setup_admin.php` is only for local first-run setup and refuses to run once an administrator exists.

## Assignment documentation outline
1. Website purpose and main functions.
2. Database and required component setup instructions.
3. Key challenges and solutions (migration from static HTML to PHP/MySQL, relational data, secure checkout, responsive design).
4. Individual contributions (complete with your actual group members' work).

## Demo test cases
1. Register customer; login/logout; incorrect password.
2. Filter menu; add to cart; update/remove items.
3. Checkout; verify order and order_items in phpMyAdmin and reduced stock.
4. Review a drink; verify review is stored.
5. Admin create/edit/hide/delete unused menu items and categories.
6. Admin change order status; verify sales report.
7. Check phone-sized screen and browser form validation.

> For submission, acknowledge any AI assistance as required by your lecturer. Review and understand all code before submitting.

## Updated customer experience
- `menu.php`: category sidebar, featured and favourites tabs, circular product images.
- `drink.php`: drink detail page and customization controls.
- `cart.php`: asynchronous add-to-cart confirmation, quantities and pickup-style summary.
- `index.php`: animated sections and horizontally scrollable customer favourites.

**Database limitation:** The supplied schema does not include columns for drink size, add-ins, outlet, pickup time or customization. Preferences are shown in the session cart only; the checkout stores the base menu price and order items in the existing tables. Pickup selection/time on the cart are illustrative and are not persisted. To support priced customizations and actual pickup scheduling, add appropriate database fields/tables and update checkout totals.


## Updated customer experience
- Full-screen homepage hero, scroll reveal effects, image-only favourites carousel with arrow controls.
- Centered Menu / About Us / Quiz navigation and icon-only cart/account links.
- Four-question drink recommendation quiz (`quiz.php`) based on available database items.
- First-visit-per-browser-tab-session promotional popup. STAR20 applies a 10% order discount at checkout.
- Guest checkout creates a guest customer database record with an unguessable password; guests cannot log into it. Existing registered users should sign in.
- Order totals reflect discounts while order_items retains undiscounted line subtotals. Account for this difference in custom reports.
- Pickup outlet and time are still demonstration-only because the original database lacks corresponding fields.
- No online payments are processed. Test end-to-end in XAMPP before submitting.
"# ZatiPart" 
