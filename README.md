# Directorist - Search by Author

**Directorist - Search by Author** is a custom extension for the [Directorist](https://directorist.com) plugin. It adds a **"Search by Author"** field to the search form layout in the Directory Builder, allowing users to filter listings based on the author.

> 🧩 **Field Name:** `Search by Author`

---

## 🔥 Features

- Adds a custom field in the **Search Form Layout** via **Directory Builder**
- Field label: `Search by Author`
- Fully Select2-enabled dropdown for quick and efficient author search
- Frontend integration with the Directorist listing search system
- Works out of the box once added to your search form layout

---

## 📦 Installation

### Requirements

- WordPress 5.0+
- Directorist plugin installed and activated

### Installation Steps

1. **Download or Clone the Repository**

   ```bash
   git clone https://github.com/MahfuzulAlam/directorist-custom-code.git
   ```

   Or [download the ZIP](https://github.com/MahfuzulAlam/directorist-custom-code/archive/refs/heads/search/author.zip).

2. **Upload the Plugin**

   - Go to your WordPress dashboard
   - Navigate to **Plugins → Add New → Upload Plugin**
   - Upload the ZIP file from the `search/author` branch
   - Click **Install Now**, then **Activate**

---

## 🚀 Walkthrough

### 1️⃣ Add the Field in the Search Form Layout

- Go to: **Directorist → Directory Builder → Search Form Layout**
- Find the field named **"Search by Author"** in the list
- Drag it into your form layout

🖼️ Screenshot:  
![Backend Field Added](https://prnt.sc/Ua_FKWIXp3WH)

---

### 2️⃣ Frontend View

Once saved, the field appears on the frontend search form.

🖼️ Screenshot:  
![Frontend Search Field](https://prnt.sc/xeOUOl-OaAP0)

---

### 3️⃣ View Search Results

After selecting an author and submitting the form, listings will be filtered by the selected author.

🖼️ Screenshot:  
![Search Results](https://prnt.sc/ccQBJHpjNVhZ)

---

## 📁 Repository Structure

```
/directorist-custom-code/
  └── search/
      └── author/
          ├── directorist-author-search.php
          └── (other plugin files)
```

Make sure you're using the `search/author` branch to access this plugin.

---

## 📄 License

This project is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

## 👨‍💻 Author

Developed by [Mahfuzul Alam](https://github.com/MahfuzulAlam)  
Part of the [Directorist](https://directorist.com) plugin ecosystem

---
