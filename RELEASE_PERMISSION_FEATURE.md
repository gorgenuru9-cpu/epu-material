# የመልቀቅ ፍቃድ ባህሪ / Release Permission Feature

## አጠቃላይ እይታ / Overview

የመልቀቅ ፍቃድ ስርዓት ግምጃ ቤት እቃውን ከተመደበ በኋላ ለጠያቂው ይፋዊ የመልቀቅ ፍቃድ ሰነድ እንዲሰጥ ያስችላል። ጠያቂው ይህንን ሰነድ አትሞ ወደ ግምጃ ቤት ይዞ ሄዶ እቃውን ይወስዳል።

The Release Permission system allows Treasury to issue an official release permission document to the requester after assigning an item. The requester can print this document and use it to collect the item from Treasury.

## ባህሪዎች / Features

### 1. የመልቀቅ ፍቃድ መፍጠር / Release Permission Creation

✅ **ራስ-ሰር የፍቃድ ቁጥር መፍጠር**
- ቅርጸት: `RP-YYYYMMDD-XXXX`
- ምሳሌ: `RP-20240318-0001`
- ልዩ እና የማይደገም

✅ **ሙሉ መረጃ ማስቀመጥ**
- የጠያቂ መረጃ (ስም፣ መለያ ቁጥር)
- የእቃ መረጃ (ስም፣ ኮድ፣ ብዛት)
- የመልቀቅ መረጃ (ቀን፣ ያስረከበው ሰው)

### 2. የመልቀቅ ፍቃድ ሰነድ / Release Permission Document

✅ **ሙያዊ ቅርጸት**
- የተደራጀ እና ግልጽ አቀራረብ
- በአማርኛ እና በእንግሊዝኛ
- ለማተም ዝግጁ (Print-ready)

✅ **የሚካተቱ መረጃዎች**
- የፍቃድ ቁጥር (በትልቅ ፊደል)
- የጠያቂ ሙሉ መረጃ
- የእቃ ዝርዝር መግለጫ
- የመልቀቅ ቀን እና ሰዓት
- የግምጃ ቤት እና የጠያቂ የፊርማ ቦታ

### 3. ውህደት / Integration

✅ **ከዳሽቦርድ ጋር**
- ለተጠናቀቁ ጥያቄዎች "📄 የመልቀቅ ፍቃድ" አገናኝ
- ቀጥተኛ መዳረሻ ለጠያቂዎች

✅ **ከጥያቄ ዝርዝሮች ጋር**
- የፍቃድ ቁጥር ማሳያ
- "ይመልከቱ እና ያትሙ" ቁልፍ
- ማስታወሻ መልእክት

✅ **ከማስታወቂያ ስርዓት ጋር**
- ጠያቂው የፍቃድ ቁጥር ያለው ማስታወቂያ ይቀበላል
- "ጥያቄዎ ተጠናቋል። የመልቀቅ ፍቃድ ቁጥር: RP-20240318-0001"

## የስራ ሂደት / Workflow

### ደረጃ 1: እቃ መመደብ / Item Assignment
1. ግምጃ ቤት እቃውን ከስቶር ይመርጣል
2. ለጠያቂው መለያ ቁጥር ይመድባል
3. ስቶክ በራስ-ሰር ይቀንሳል
4. ሁኔታ ወደ "item_retrieved" ይቀየራል

### ደረጃ 2: የመልቀቅ ፍቃድ መስጠት / Issue Release Permission
1. ግምጃ ቤት "እቃ አስረክብ" ይጫናል
2. ስርዓቱ የመልቀቅ ፍቃድ ይፈጥራል:
   - ልዩ የፍቃድ ቁጥር ይፈጥራል
   - ሙሉ መረጃ በዳታቤዝ ውስጥ ያስቀምጣል
   - የማስታወቂያ መልእክት ይልካል
3. ሁኔታ ወደ "completed" ይቀየራል

### ደረጃ 3: ፍቃድ ማተም / Print Permission
1. ጠያቂው ወደ ዳሽቦርድ ይገባል
2. "📄 የመልቀቅ ፍቃድ" ይጫናል
3. የፍቃድ ሰነድ በአዲስ መስኮት ይከፈታል
4. "🖨️ አትም" ይጫናል

### ደረጃ 4: እቃ መውሰድ / Collect Item
1. ጠያቂው የፍቃድ ሰነድ ያትማል
2. ወደ ግምጃ ቤት ይዞ ይሄዳል
3. ግምጃ ቤት የፍቃድ ቁጥር ያረጋግጣል
4. ሁለቱም ወገኖች ይፈርማሉ
5. ጠያቂው እቃውን ይወስዳል

## የዳታቤዝ መዋቅር / Database Structure

### release_permissions ሰንጠረዥ

```sql
CREATE TABLE release_permissions (
    permission_id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL UNIQUE,
    requester_id INT NOT NULL,
    requester_identification VARCHAR(50) NOT NULL,
    item_id INT NOT NULL,
    item_code VARCHAR(50) NOT NULL,
    item_name VARCHAR(200) NOT NULL,
    quantity_released INT NOT NULL,
    permission_number VARCHAR(100) NOT NULL UNIQUE,
    issued_by INT NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT
);
```

## የፋይል መዋቅር / File Structure

```
database/
├── add_release_permission.sql      # የሰንጠረዥ መዋቅር
└── add_release_permission.php      # የመጀመሪያ ስክሪፕት

src/models/
└── ReleasePermission.php           # የመልቀቅ ፍቃድ ሞዴል

src/controllers/
└── ApprovalController.php          # የተዘመነ releaseItem() ዘዴ

public/
├── release-permission.php          # የፍቃድ ሰነድ ገጽ
├── dashboard.php                   # የተዘመነ ከአገናኝ ጋር
└── request-details.php             # የተዘመነ ከፍቃድ ክፍል ጋር

lang/
└── am.php                          # አዲስ ትርጉሞች
```

## API / ዘዴዎች

### ReleasePermission::issue()
```php
ReleasePermission::issue(
    int $requestId,
    int $requesterId,
    string $requesterIdentification,
    int $itemId,
    string $itemCode,
    string $itemName,
    int $quantity,
    int $issuedBy,
    ?string $notes = null
): array|false
```

### ReleasePermission::getByRequest()
```php
ReleasePermission::getByRequest(int $requestId): ?array
```

### ReleasePermission::getByPermissionNumber()
```php
ReleasePermission::getByPermissionNumber(string $permissionNumber): ?array
```

### ReleasePermission::getByRequester()
```php
ReleasePermission::getByRequester(int $requesterId): array
```

## የደህንነት ባህሪዎች / Security Features

✅ **የመዳረሻ ቁጥጥር**
- ጠያቂው የራሱን ፍቃድ ብቻ ማየት ይችላል
- ግምጃ ቤት ሁሉንም ፍቃዶች ማየት ይችላል
- ሌሎች ክፍሎች መዳረሻ የላቸውም

✅ **ልዩ የፍቃድ ቁጥሮች**
- የማይደገሙ ቁጥሮች
- ቀላል ለማረጋገጥ
- የማይበላሹ

✅ **የማስታወሻ ዱካ**
- ሁሉም ድርጊቶች በ audit log ውስጥ ይመዘገባሉ
- የፍቃድ መፍጠር
- የፍቃድ መመልከት

## የአጠቃቀም ምሳሌዎች / Usage Examples

### ለጠያቂዎች / For Requesters

1. **የፍቃድ ማየት**
   ```
   ዳሽቦርድ → የተጠናቀቁ ጥያቄዎች → "📄 የመልቀቅ ፍቃድ"
   ```

2. **ፍቃድ ማተም**
   ```
   የፍቃድ ገጽ → "🖨️ አትም" ቁልፍ
   ```

3. **እቃ መውሰድ**
   ```
   የታተመ ፍቃድ → ወደ ግምጃ ቤት → ፊርማ → እቃ መውሰድ
   ```

### ለግምጃ ቤት / For Treasury

1. **ፍቃድ መስጠት**
   ```
   ዳሽቦርድ → "እቃ አስረክብ" → ራስ-ሰር ፍቃድ ይፈጠራል
   ```

2. **ፍቃድ ማረጋገጥ**
   ```
   ጠያቂው ፍቃድ ይዞ ይመጣል → የፍቃድ ቁጥር ማረጋገጥ → ፊርማ → እቃ መስጠት
   ```

## ጥቅሞች / Benefits

✅ **ይፋዊ ሰነድ**
- ህጋዊ ማስረጃ
- ግልጽ የመልቀቅ ሂደት
- ተጠያቂነት

✅ **ቀላል ማረጋገጥ**
- ልዩ የፍቃድ ቁጥር
- ሙሉ የእቃ መረጃ
- የመልቀቅ ቀን እና ሰዓት

✅ **የተሻሻለ ክትትል**
- ሁሉም መልቀቅ ተመዝግቧል
- የማስታወሻ ዱካ
- ሪፖርት ማድረግ ቀላል

✅ **ሙያዊ አቀራረብ**
- ለማተም ዝግጁ
- ባለሁለት ቋንቋ (አማርኛ/እንግሊዝኛ)
- ግልጽ እና የተደራጀ

## የወደፊት ማሻሻያዎች / Future Enhancements

⚠️ **አማራጭ ማሻሻያዎች:**

1. **QR Code**
   - በፍቃድ ሰነድ ላይ QR code
   - ለፈጣን ማረጋገጥ

2. **ኢሜይል ማሳወቂያ**
   - ፍቃድ በኢሜይል መላክ
   - PDF attachment

3. **የፍቃድ ታሪክ**
   - ለጠያቂው ሁሉንም ፍቃዶች ማሳያ
   - የፍለጋ እና የማጣሪያ ችሎታ

4. **የፊርማ ማረጋገጥ**
   - ዲጂታል ፊርማ
   - የፊርማ ማረጋገጥ

## ማጠቃለያ / Summary

የመልቀቅ ፍቃድ ስርዓት የንብረት ጥያቄ አስተዳደር ስርዓትን ያጠናቅቃል። ጠያቂዎች አሁን:

1. ✅ ጥያቄ ያስገባሉ (Form 20)
2. ✅ በ5 ክፍሎች ይፀድቃል
3. ✅ እቃ በንብረት አስተዳደር ይመዘገባል
4. ✅ መጠባበቂያ በመዝገብ ቤት ይፈጠራል
5. ✅ እቃ ከስቶር ይመደባል
6. ✅ **የመልቀቅ ፍቃድ ይቀበላሉ** ← አዲስ!
7. ✅ **ፍቃድ አትመው እቃ ይወስዳሉ** ← አዲስ!

ስርዓቱ አሁን ሙሉ እና ለምርት ዝግጁ ነው!

---

**የተፈጠረበት ቀን:** 2024
**ስሪት:** 1.1.0
**ሁኔታ:** ✅ ተግባራዊ
