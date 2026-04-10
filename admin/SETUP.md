# Setup & Quick Start Guide

## 1️⃣ Database Setup (REQUIRED FIRST)

Visit this URL in your browser to create all database tables:
```
http://localhost/company_profile/admin/setup-db.php
```

This will create:
- ✓ `admin_users` table (for admin login)
- ✓ `contact_messages` table (for inbox)
- ✓ `articles` table (for blog articles)
- ✓ `services` table (for services)
- ✓ `settings` table (for site configuration)

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

⚠️ **IMPORTANT:** Change your password immediately after first login!

---

## 2️⃣ Admin Pages Available

### Dashboard
`http://localhost/company_profile/admin/`
- Overview of articles, services, and messages
- Quick stats and recent activity

### Articles Management
`http://localhost/company_profile/admin/articles/`
- Create, edit, delete articles
- Built-in rich text editor (TinyMCE)
- Automatic image upload support

### Services Management
`http://localhost/company_profile/admin/services/`
- Manage services/products
- Organize with sort order

### Inbox / Messages
`http://localhost/company_profile/admin/inbox/`
- View all contact form messages
- Mark as read/unread
- Delete messages
- Click "Buka" to view full message and reply

### Settings
`http://localhost/company_profile/admin/settings/`
- Site configuration
- Contact information
- Social media links

---

## 3️⃣ Inbox Features

The inbox is now fully functional:

### List View (`/admin/inbox/`)
- ✓ See all messages from contact form
- ✓ Filter by read/unread status
- ✓ Unread count badge
- ✓ Quick preview of message text
- ✓ Mark all as read
- ✓ Delete individual messages

### Detail View (`/admin/inbox/view.php?id={id}`)
- ✓ Full message content
- ✓ Sender details (name, email, phone)
- ✓ Message timestamp
- ✓ Delete button
- ✓ "Reply via Email" button (opens your email client)
- ✓ Auto-marks message as read when opened

---

## 4️⃣ Contact Form Integration

The contact form on `/kontak` page:
- ✓ Saves all submissions to `contact_messages` table
- ✓ Stores: name, email, phone, message, timestamp
- ✓ Notifies you via inbox system
- ✓ Validates email and phone
- ✓ Requires either email or phone

---

## 5️⃣ File Structure

```
admin/
├── bootstrap.php              ← Load this in every admin file
├── login.php                  ← Admin login page
├── logout.php                 ← Logout endpoint
├── index.php                  ← Dashboard
├── setup-db.php              ← Database setup (DELETE after use)
├── articles/
│   ├── index.php             ← List articles
│   ├── create.php            ← New article form
│   ├── edit.php              ← Edit article form
│   ├── upload-image.php      ← Image upload handler
│   └── jodit-config.php      ← Editor config
├── services/
│   └── index.php             ← Manage services
├── inbox/
│   ├── index.php             ← Message list
│   └── view.php              ← View single message (FIXED)
├── settings/
│   └── index.php             ← Site settings
└── includes/
    ├── admin-header.php      ← Page header (includes auth check)
    └── admin-footer.php      ← Page footer
```

---

## 6️⃣ Security Notes

1. **Delete `setup-db.php` after setup** (already created, remove after first run)
2. **Change default password** immediately
3. **Use strong passwords** for admin accounts
4. **Keep PHP updated** 
5. **Keep database backups** regular

---

## 7️⃣ Troubleshooting

### "Table not found" error
→ Run the database setup: `http://localhost/company_profile/admin/setup-db.php`

### Can't login
→ Check username/password (default: admin/admin123)
→ Verify database connection in `/config/db.php`

### Messages not appearing in inbox
→ Check that contact form submission worked
→ Look in browser console for form errors
→ Verify `contact_messages` table exists

### Image upload not working
→ Check that `/uploads/articles` directory exists and is writable
→ Verify `admin/articles/upload-image.php` is accessible

---

## 8️⃣ Next Steps

1. ✅ Run database setup
2. ✅ Login with default credentials
3. ✅ Change your password
4. ✅ Add some articles/services
5. ✅ Test contact form from `/kontak` page
6. ✅ Check inbox for test message
7. ✅ Delete `setup-db.php` file

---

**Happy managing! 🚀**