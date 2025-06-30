# Directorist - Custom QR Code Extension

**Directorist - Custom QR Code** is a lightweight and useful extension that enables you to show a QR code on the single listing page. Scanning the QR code will take users directly to that specific listing's page—ideal for quick access, sharing offline, or embedding on printed materials.

---

## 🔧 How It Works

This extension provides a shortcode that you can use within the single listing page template using the **Directory Builder**. The QR code dynamically reflects the current listing’s URL.

When someone scans the QR code with their device, they will be redirected to the exact single listing page, making it easier to access or share the listing on-the-go.

---

## 🚀 Setup Instructions

### 1. Add QR Code to Single Listing Template

1. Navigate to **Directorist Listings > Directory Builder > Single Page Layout**.
2. Drag and drop the **Custom Content** field into the layout.
3. Inside the custom content editor, insert the shortcode:

   ```
   [directorist_listing_qrcode]
   ```

4. Save the layout.

📸 Example: [Directory Builder Screenshot](https://prnt.sc/vDMkhoiERiQr)

### 2. View on Single Listing Page

Once configured, visit any single listing page to see the QR code appear beautifully and sharply rendered.

📸 Example: [Single Listing Screenshot](https://prnt.sc/_JAtWDcF4zNu)

---

## 🧩 Shortcode

```php
[directorist_listing_qrcode]
```

Use this shortcode anywhere on the single listing template to generate the QR code for the current listing.

---

## 🔮 Upcoming Features

We’re planning to introduce a dynamic QR code field so users can insert their own custom links or texts during listing submission. These values will then be rendered as QR codes on the single listing page—opening up a range of use cases like social profiles, contact info, or special offers.

---

## 📥 Requirements

- WordPress 5.0 or higher
- Directorist plugin must be installed and active

---

## 💬 Support

Need help? Contact us via:

- 📧 Email: [asayeedalam@gmail.com](mailto:asayeedalam@gmail.com)

---

## 📝 Changelog

**1.0.0** – Initial release with static QR code shortcode based on current listing URL.

---

Thank you for using **Directorist - Custom QR Code**! 🚀
