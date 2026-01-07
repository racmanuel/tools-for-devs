# Tools for Developers

A collection of practical generators for **WordPress developers** to speed up common tasks such as SQL migrations, database operations, REST routes, plugin scaffolding, and custom fields.

**Tools for Developers** focuses on:

- ⚡ Productivity & consistency  
- 🧠 Developer-friendly UI  
- 🧾 Clean, readable generated output  
- 📋 One-click copy to clipboard  

---

## ✨ Features

This plugin provides a set of **interactive generators** that output ready-to-use **SQL queries** and **PHP boilerplate**, without executing anything automatically.

### Included Generators

#### 🗄️ WordPress Migration SQL Generator
Generate SQL queries to safely update site URLs when migrating a WordPress installation to a new domain.

**Shortcode**
```
[tools-for-devs-migration-sql]
```

---

#### 🔁 Database Prefix Generator
Generate SQL to rename WordPress tables and update related references.

Supports:
- Single site & multisite
- Optional WooCommerce tables

**Shortcode**
```
[tools-for-devs-db-prefix]
```

---

#### 🧩 Plugin Header Generator
Generate a complete WordPress plugin header block with common metadata and optional requirements.

**Shortcode**
```
[tools-for-devs-plugin-header-generator]
```

---

#### 🛒 WooCommerce Delete Products SQL Generator
Generate SQL queries to delete WooCommerce products.

> ⚠️ Use with caution. Always back up your database.

**Shortcode**
```
[tools-for-devs-delete-products-sql]
```

---

#### 🧱 ACF / SCF Field Generator
Generate scaffolding for **custom field types** compatible with:

- Advanced Custom Fields (ACF)
- Secure Custom Fields (SCF)

Designed for developers building their own field types.

**Shortcode**
```
[tools-for-devs-acf-field-generator]
```

---

#### 🌐 REST Route Generator
Generate `register_rest_route()` boilerplate with support for:

- Permission callbacks
- Dynamic arguments
- Path parameters (`(?P<id>\d+)`)
- Controller class mode
- Optional CRUD generation
- `curl` examples

**Shortcode**
```
[tools-for-devs-rest-route-generator]
```

---

#### 🗃️ Database CRUD Generator
Generate `$wpdb` + `dbDelta()` boilerplate for custom database tables, including:

- Table creation
- Version upgrades
- Basic CRUD helpers

**Shortcode**
```
[tools-for-devs-db-crud-generator]
```

---

## 🧠 Important Notes

- ❌ **No SQL is executed automatically**
- ✅ This plugin only **generates code**
- 🛑 Always back up your database before running generated SQL
- ⚠️ Incorrect SQL usage may break serialized data

---

## 📦 Installation

1. Upload the plugin folder to `/wp-content/plugins/`, or install it via the WordPress admin.
2. Activate the plugin from **Plugins → Installed Plugins**.
3. Add any generator shortcode to a page or post.

---

## 🌍 Internationalization

All UI strings are prepared for translation using standard WordPress i18n functions.

---

## ❓ FAQ

### Does this plugin modify my database automatically?
No. All generators output code or SQL only. You decide if and when to run it.

### Is it safe to use on production?
The plugin itself is safe. However, executing SQL on production environments always carries risk. Test on staging first.

### Does it support Secure Custom Fields (SCF)?
Yes. The field generator is compatible with both ACF and SCF workflows.

---

## 🖼️ Screenshots

1. Migration SQL Generator with CodeMirror and copy button  
2. Database Prefix Generator (multisite + WooCommerce support)  
3. REST Route Generator (Controller + CRUD mode)  
4. Database CRUD Generator (`dbDelta` + upgrades)  
5. Plugin Header Generator  
6. ACF / SCF Field Generator  

---

## 🧾 Changelog

### 1.0.0
Initial release including 7 generators:

- Migration SQL Generator  
- Database Prefix Generator  
- Plugin Header Generator  
- WooCommerce Delete Products SQL Generator  
- ACF / SCF Field Generator  
- REST Route Generator  
- Database CRUD Generator  

---

## 📄 License

Licensed under the **GPL v2 or later**  
See https://www.gnu.org/licenses/gpl-2.0.html

---

## 👨‍💻 Author

Developed by **Manuel Ramírez**  
🌐 https://racmanuel.dev
