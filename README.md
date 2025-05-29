
# Listing View Restriction Logic

## 🔹 For Guest Users (Non-logged-in)

1. A fixed limit of **5 unique listings** per browser session.
   - This will be tracked using cookies.

2. After viewing 5 unique listings, a popup will be shown with the message:
   > "You've reached your limit. Please purchase your plan to view unlimited listings."

3. The popup will include a **button that links to the Pricing Plans page**, allowing users to purchase a plan.

4. The **Pricing Plans page will have only one available plan**.

---

## 🔹 For Logged-in Users

1. If the user **purchases a plan**, they can **view unlimited listings** for a period defined in the selected plan (as long as the plan remains active).

2. Once the plan **expires**, users can **view 5 unique listings for free**.
   - After that, a popup will prompt them to renew or purchase a new membership plan.

3. **Primary goal**: After 5 free listing views, no user (guest or logged-in) should be able to view more listings without an active plan.

---

## 📝 Additional Note

- **Logged-in users can post unlimited listings for free**, even without a membership plan.