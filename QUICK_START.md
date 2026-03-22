# 🚀 Quick Start Guide - የንብረት ጥያቄ አስተዳደር ስርዓት

## ✅ System is LIVE!

**URL**: http://localhost:8000  
**Status**: 🟢 Running

---

## 🔐 Login Credentials

| Username | Password | Role |
|----------|----------|------|
| requester1 | password123 | ጠያቂ (Requester) |
| requester_main1 | password123 | ጠያቂው ዋና ክፍል |
| property_main1 | password123 | የንብረት አስተዳደር ዋና ክፍል |
| property_dept1 | password123 | የንብረት አስተዳደር ክፍል |
| registry1 | password123 | መዝገብ ቤት |
| treasury1 | password123 | ግምጃ ቤት |

---

## 🎯 Test the Complete Workflow (5 Minutes)

### 1️⃣ Create Request
- Login: `requester1` / `password123`
- Click: **"አዲስ ጥያቄ ፍጠር"**
- Fill Form 20 and submit

### 2️⃣ First Approval
- Login: `requester_main1` / `password123`
- Click: **"ፍቀድ"** on the request

### 3️⃣ Second Approval
- Login: `property_main1` / `password123`
- Click: **"ፍቀድ"** on the request

### 4️⃣ Third Approval + Registration
- Login: `property_dept1` / `password123`
- Click: **"ፍቀድ"**
- Fill item registration form
- Submit

### 5️⃣ Fourth Approval + Backup
- Login: `registry1` / `password123`
- Click: **"ፍቀድ"**
- (Backup created automatically)

### 6️⃣ Final Release
- Login: `treasury1` / `password123`
- Click: **"እቃ ውሰድ"** (Retrieve)
- Click: **"እቃ አስረክብ"** (Release)

### 7️⃣ Verify Completion
- Login: `requester1` / `password123`
- See notification: **"ጥያቄዎ ተጠናቋል"**
- Status: **"ተጠናቋል"** ✅

---

## 📊 What You Can Do

✅ Create property withdrawal requests  
✅ Approve/reject at each stage  
✅ Register items  
✅ View complete audit trail  
✅ Track request status  
✅ Receive notifications  
✅ View statistics  

---

## 🛠️ Server Commands

### Start Server
```bash
cd public
C:\xampp\php\php.exe -S localhost:8000
```

### Stop Server
Press `Ctrl+C` in the terminal

### Reinitialize Database
```bash
C:\xampp\php\php.exe database/init.php
```

---

## 📁 Important Files

- **Login**: http://localhost:8000/login.php
- **Dashboard**: http://localhost:8000/dashboard.php
- **Database Config**: `config/database.php`
- **Translations**: `lang/am.php`

---

## 🆘 Troubleshooting

### Can't Access System
- Check server is running: http://localhost:8000
- Restart server if needed

### Login Not Working
- Verify database is initialized
- Check credentials are correct
- Clear browser cookies

### Database Error
- Run: `C:\xampp\php\php.exe database/init.php`
- Check MySQL is running in XAMPP

---

## 📚 Full Documentation

- **FINAL_BUILD_SUMMARY.md** - Complete feature list
- **INSTALLATION.md** - Detailed setup guide
- **SYSTEM_SUMMARY.md** - Technical overview
- **README.md** - System description

---

**System Status**: ✅ READY TO USE  
**Enjoy your new property request management system!** 🎉
