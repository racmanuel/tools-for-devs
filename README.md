# Tools for Developers

A collection of practical generators for **WordPress developers** to speed up common tasks such as SQL migrations, database operations, REST routes, plugin scaffolding, and custom fields.

## 🚀 Overview

**Tools for Developers** is a productivity-focused WordPress plugin that provides interactive generators to help developers write boilerplate code and SQL faster, safer, and more consistently.

The plugin is designed with:

- ⚡ Productivity & consistency  
- 🧠 Developer‑friendly UI  
- 🧾 Clean, readable generated output  
- 📋 One‑click copy to clipboard  

> ⚠️ This plugin **never executes SQL or code automatically**. It only generates output.

---

## ✨ Generators Included

### 🗄️ WordPress Migration SQL Generator
Generate SQL queries to update site URLs when migrating a WordPress site to a new domain.

**Shortcode**
```text
[tools-for-devs-migration-sql]
```

---

### 🔁 Database Prefix Generator
Generate SQL to rename WordPress database tables and update related keys.

Supports:
- Single site
- Multisite
- Optional WooCommerce tables

**Shortcode**
```text
[tools-for-devs-db-prefix]
```

---

### 🧩 Plugin Header Generator
Generate a complete WordPress plugin header with common metadata and optional requirements.

**Shortcode**
```text
[tools-for-devs-plugin-header-generator]
```

---

### 🛒 WooCommerce Delete Products SQL Generator
Generate SQL queries to delete WooCommerce products.

> ⚠️ Always back up your database before executing generated SQL.

**Shortcode**
```text
[tools-for-devs-delete-products-sql]
```

---

### 🧱 ACF / SCF Field Generator
Generate scaffolding for custom field types compatible with:

- Advanced Custom Fields (ACF)
- Secure Custom Fields (SCF)

Intended for developers building custom field types.

**Shortcode**
```text
[tools-for-devs-acf-field-generator]
```

---

### 🌐 REST Route Generator
Generate `register_rest_route()` boilerplate with support for:

- Permission callbacks
- Dynamic arguments
- Path parameters (`(?P<id>\d+)`)
- Optional controller class mode
- Optional CRUD generation
- `curl` examples

**Shortcode**
```text
[tools-for-devs-rest-route-generator]
```

---

### 🗃️ Database CRUD Generator
Generate `$wpdb` + `dbDelta()` boilerplate for custom database tables, including:

- Table creation
- Version upgrades
- Basic CRUD helpers

**Shortcode**
```text
[tools-for-devs-db-crud-generator]
```

---

## 🧠 Important Notes

- ❌ No SQL or PHP is executed automatically
- 🛑 Always back up your database before running generated SQL
- ⚠️ Incorrect SQL usage can break serialized data

---

## 📦 Installation

### From WordPress Admin
1. Upload the plugin folder to `/wp-content/plugins/` or install it via the Plugins screen.
2. Activate the plugin.
3. Add any generator shortcode to a page or post.

---

## 🌍 Internationalization

All UI strings are prepared for translation using standard WordPress i18n functions.

---

## ❓ FAQ

### Does this plugin modify my database automatically?
No. All generators only output code or SQL.

### Is it safe to use on production?
The plugin itself is safe, but executing SQL on production environments always carries risk. Test on staging first.

### Does it support Secure Custom Fields (SCF)?
Yes. The field generator supports both ACF and SCF workflows.

---

## 🧾 Changelog

### 1.0.0
Initial public release including:

- Migration SQL Generator
- Database Prefix Generator
- Plugin Header Generator
- WooCommerce Delete Products SQL Generator
- ACF / SCF Field Generator
- REST Route Generator
- Database CRUD Generator

---

## 📄 License

Licensed under **GPL v2 or later**  
https://www.gnu.org/licenses/gpl-2.0.html

---

## 👨‍💻 Author

**Manuel Ramírez**  
🌐 https://racmanuel.dev
