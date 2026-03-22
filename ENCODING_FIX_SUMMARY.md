# የአማርኛ ጽሁፍ ችግር መፍትሄ / Amharic Encoding Fix

## ችግሩ / Problem
የአማርኛ ጽሁፍ በትክክል አይታይም (ßè«ßêØßìÆßïìßë░ßê¡ ይመስላል)

## መፍትሄ / Solution

### 1. የመረጃ ቋት ማስተካከያ / Database Fix

በ phpMyAdmin ወይም MySQL command line ውስጥ ይህንን ያስኬዱ:

```bash
mysql -u root -p property_request_system < database/fix_utf8_encoding.sql
```

### 2. ምርመራ / Testing

ይህንን ገጽ ይጎብኙ:
```
http://localhost:8000/database/test_encoding.php
```

ይህ ገጽ:
- የመረጃ ቋቱን encoding ያረጋግጣል
- የአማርኛ ጽሁፍ በትክክል እንደሚሰራ ይፈትሻል
- ችግሮችን ያሳያል

### 3. የተፈጠሩ ፋይሎች / Created Files

1. **database/fix_utf8_encoding.sql** - የመረጃ ቋት encoding ለማስተካከል
2. **database/test_encoding.php** - encoding ለመፈተሽ
3. **FIX_AMHARIC_ENCODING.md** - ዝርዝር መመሪያ

### 4. ፈጣን መፍትሄ / Quick Fix

በ phpMyAdmin:
1. `property_request_system` database ይምረጡ
2. "SQL" tab ይጫኑ
3. `database/fix_utf8_encoding.sql` ይክፈቱ እና ይቅዱ
4. "Go" ይጫኑ

### 5. ማረጋገጫ / Verification

ከማስተካከያ በኋላ:
1. http://localhost:8000 ይጎብኙ
2. በ treasury1 ይግቡ (password: password)
3. "ከሰው እጅ ላይ ያለ ንብረት" ክፍል ይመልከቱ
4. የአማርኛ ጽሁፍ በትክክል መታየት አለበት

## ለወደፊቱ / Prevention

- ሁሉም ፋይሎች UTF-8 encoding መጠቀም አለባቸው
- MySQL configuration (my.ini) utf8mb4 መጠቀም አለበት
- አዲስ መረጃ ሲያስገቡ በአማርኛ ይፈትሹ

## እገዛ / Help

ችግር ካለ:
1. `database/test_encoding.php` ያስኬዱ
2. ቀይ ጽሁፍ (errors) ካለ፣ `fix_utf8_encoding.sql` ያስኬዱ
3. MySQL service ዳግም ያስጀምሩ
4. እንደገና ይፈትሹ
