# Fix Amharic Text Encoding Issues

## Problem
Amharic text is displaying as garbled characters (ßè«ßêØßìÆßïìßë░ßê¡) instead of proper Amharic script.

## Solution

### Step 1: Run the UTF-8 Encoding Fix Script

Open phpMyAdmin or MySQL command line and run:

```bash
mysql -u root -p property_request_system < database/fix_utf8_encoding.sql
```

Or in phpMyAdmin:
1. Select the `property_request_system` database
2. Click on "SQL" tab
3. Copy and paste the contents of `database/fix_utf8_encoding.sql`
4. Click "Go"

### Step 2: Verify Database Encoding

Check that your database and tables are using utf8mb4:

```sql
-- Check database encoding
SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME 
FROM information_schema.SCHEMATA 
WHERE SCHEMA_NAME = 'property_request_system';

-- Check table encodings
SELECT TABLE_NAME, TABLE_COLLATION 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'property_request_system';
```

### Step 3: Check my.ini/my.cnf Configuration

Add these lines to your MySQL configuration file (usually in C:\xampp\mysql\bin\my.ini):

```ini
[client]
default-character-set = utf8mb4

[mysql]
default-character-set = utf8mb4

[mysqld]
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
init-connect='SET NAMES utf8mb4'
```

Then restart MySQL service.

### Step 4: Re-insert Corrupted Data

If the data is still corrupted after running the fix script, you'll need to manually update the records:

```sql
-- Example: Fix inventory item names
UPDATE inventory_items 
SET item_name = 'ኮምፒውተር ዴስክቶፕ'
WHERE item_code = 'COMP-001';

-- Check the result
SELECT item_code, item_name FROM inventory_items WHERE item_code = 'COMP-001';
```

### Step 5: Verify PHP Configuration

Ensure your PHP files are saved with UTF-8 encoding (without BOM):
- In VS Code: Check bottom right corner, should say "UTF-8"
- If not, click on it and select "Save with Encoding" → "UTF-8"

### Step 6: Test

1. Go to http://localhost:8000/dashboard.php
2. Login with treasury user (treasury1 / password)
3. Check if "ከሰው እጅ ላይ ያለ ንብረት" section shows proper Amharic text

## Prevention

To prevent this issue in the future:

1. Always use UTF-8 encoding when creating/editing files
2. Ensure database connection uses utf8mb4 charset
3. Use prepared statements with proper encoding
4. Test with Amharic text immediately after creating new features

## Quick Test Query

Run this to see if encoding is working:

```sql
SELECT 'ኮምፒውተር' as test_amharic;
```

If you see proper Amharic text, encoding is working correctly.
