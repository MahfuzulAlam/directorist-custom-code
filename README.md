# **Directorist Business Hours CSV Import**  
*A custom extension to bulk import business hours for Directorist listings via CSV.*  

---

## **📌 Overview**  
This extension enables **CSV-based import of business hours** for [Directorist](https://directorist.com/) listings. It maps custom CSV columns to Directorist's business hour meta fields (`bdbh_monday`, `bdbh_tuesday`, etc.), supporting:  
✅ Multiple time slots per day  
✅ "Closed" day markers  
✅ 24-hour time formatting  

**Use Cases**:  
- Migrate business data from other platforms  
- Bulk update hours for hundreds of listings  
- Standardize opening hours across directories  

---

## **⚙️ Installation**  

### **Option 1: Install as Plugin (Recommended)**  
1. Download the ZIP from [GitHub](https://github.com/MahfuzulAlam/directorist-custom-code/tree/import/business-hour).  
2. Upload to **WordPress → Plugins → Add New → Upload Plugin**.  
3. Activate the plugin.  

### **Option 2: Manual Integration (Developers Only)**  
Add this code to your theme’s `functions.php` or a custom plugin:  
```php
add_action('directorist_listing_imported', function($post_id, $post) {
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    foreach ($days as $day) {
        $meta_key = "bdbh_{$day}";
        if (!empty($post[$meta_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($post[$meta_key]));
        }
    }
}, 10, 2);
```

---

## **📂 CSV Preparation**  

### **Required Columns**  
| Column Name       | Example Value                 | Notes                          |
|-------------------|-------------------------------|--------------------------------|
| `bdbh_monday`     | `08:00-17:00`                 | Single slot                    |
| `bdbh_tuesday`    | `09:00-12:00, 13:00-18:00`    | Multiple slots (comma-separated) |
| `bdbh_sunday`     | `Closed`                      | Marks as closed                |

### **Rules**  
- **Time Format**: `HH:MM-HH:MM` (24-hour clock).  
- **Multiple Slots**: Separate with commas (e.g., `08:00-12:00, 13:00-17:00`).  
- **Closed Days**: Use `Closed` or leave the cell empty.  

### **Example CSV**  
```csv
title,bdbh_monday,bdbh_tuesday,bdbh_wednesday
"Café A","08:00-12:00, 13:00-17:00","09:00-18:00","Closed"
"Store B","10:00-20:00","10:00-20:00","10:00-16:00"
```

---

## **🛠️ Usage**  
1. **Prepare your CSV** with columns for each day (e.g., `bdbh_monday`).  
2. Go to **Directorist → Tools → Import Listings**.  
3. Upload your CSV.  
4. **Map Fields**:  
   - For business hour columns (`bdbh_monday`, etc.), select **"Do not import"**.  
5. Run the importer.  

---

## **❓ FAQ**  

### **Q1: How do I format time slots?**  
**A**: Use `HH:MM-HH:MM` (e.g., `13:00-14:30`). For multiple slots: `08:00-12:00, 13:00-17:00`.  

### **Q2: Can I skip days without hours?**  
**A**: Yes! Omit the column or mark as `Closed`.  

### **Q3: Why aren’t my hours saving?**  
**Fix**:  
- Verify column names (e.g., `bdbh_monday`).  
- Check for typos in time formats.  
- Ensure you selected **"Do not import"** for hour columns.  

### **Q4: Does this work with Directorist Pro?**  
**A**: Yes! Compatible with both free and Pro versions.  

---

## **🐛 Troubleshooting**  
- **"Invalid time format"**: Ensure 24-hour format (e.g., `14:00` instead of `2:00 PM`).  
- **"Hours not updating"**: Clear Directorist cache after import.  
- **"Column not found"**: Double-check CSV headers match exactly (case-sensitive).  

---

## **📜 License**  
MIT License. Use freely in commercial/non-commercial projects.  

---

## **💡 Support**  
- **Report Issues**: [GitHub Issues](https://github.com/MahfuzulAlam/directorist-custom-code/issues)  
- **Need Customization?** Contact the developer.  

**Happy Importing!** 🚀
