# የአይሲቲ ንብረት ጥያቄ ማሳወቂያ ስርዓት
# ICT Property Request Notification System

## አጠቃላይ እይታ (Overview)

የአይሲቲ አስተዳዳሪዎች ከአይሲቲ ጋር የተያያዙ ንብረቶች (ኮምፒውተር፣ ማተሚያ፣ ኔትወርክ መሳሪያዎች፣ ወዘተ) በሚጠየቁበት ጊዜ በሁሉም የስራ ሂደት ደረጃዎች ላይ ማሳወቂያ ይደርሳቸዋል።

IT Admins receive notifications at all workflow stages when ICT-related property (computers, printers, network equipment, etc.) is requested.

## የማሳወቂያ ነጥቦች (Notification Points)

### 1. የጥያቄ ፈጠራ (Request Creation)
**መቼ:** ተጠቃሚ አዲስ የንብረት ጥያቄ ሲፈጥር
**ሁኔታ:** የእቃው መግለጫ የአይሲቲ ቁልፍ ቃላትን ሲይዝ
**ማሳወቂያ:** "አዲስ የአይሲቲ ንብረት ጥያቄ: [የእቃ መግለጫ] - ጥያቄ ቁጥር: [ID]"

**When:** User creates a new property request
**Condition:** Item description contains ICT keywords
**Notification:** "New ICT property request: [item description] - Request #[ID]"

**ፋይል:** `src/controllers/RequestController.php`
**ተግባር:** `create()` method

---

### 2. የእቃ ምዝገባ (Item Registration)
**መቼ:** የንብረት አስተዳደር ክፍል እቃውን ሲመዘግብ
**ሁኔታ:** የተመዘገበው እቃ የአይሲቲ ንብረት ሲሆን
**ማሳወቂያ:** "የአይሲቲ ንብረት ተመዝግቧል: [የእቃ መግለጫ] - ጥያቄ ቁጥር: [ID]"

**When:** Property Management Department registers the item
**Condition:** Registered item is ICT property
**Notification:** "ICT property registered: [item description] - Request #[ID]"

**ፋይል:** `src/controllers/ApprovalController.php`
**ተግባር:** `approve()` method (during registration)

---

### 3. ከስቶር መመደብ (Assignment from Store)
**መቼ:** ግምጃ ቤት እቃውን ከስቶር ሲመድብ
**ሁኔታ:** የተመደበው እቃ የአይሲቲ ንብረት ሲሆን
**ማሳወቂያ:** "የአይሲቲ ንብረት ከስቶር ተመድቧል: [የእቃ ስም] (ኮድ: [ኮድ]) - ብዛት: [ብዛት] - ጥያቄ ቁጥር: [ID]"

**When:** Treasury assigns item from store
**Condition:** Assigned item is ICT property
**Notification:** "ICT property assigned from store: [item name] (Code: [code]) - Quantity: [qty] - Request #[ID]"

**ፋይል:** `public/treasury-assign.php`
**ቦታ:** After item assignment and audit log

---

### 4. ለጠያቂ መላክ (Release to Requester)
**መቼ:** ግምጃ ቤት እቃውን ለጠያቂ ሲያስረክብ
**ሁኔታ:** የተላለፈው እቃ የአይሲቲ ንብረት ሲሆን
**ማሳወቂያ:** "የአይሲቲ ንብረት ለጠያቂ ተላልፏል: [የእቃ ስም] (ኮድ: [ኮድ]) - የመልቀቅ ፍቃድ: [ቁጥር]"

**When:** Treasury releases item to requester
**Condition:** Released item is ICT property
**Notification:** "ICT property released to requester: [item name] (Code: [code]) - Release Permission: [number]"

**ፋይል:** `src/controllers/ApprovalController.php`
**ተግባር:** `releaseItem()` method

---

## የአይሲቲ ቁልፍ ቃላት (ICT Keywords)

የስርዓቱ የሚከተሉትን ቁልፍ ቃላት ይፈልጋል:

### English Keywords:
- **Computers:** computer, laptop, desktop, pc, tablet
- **Peripherals:** printer, scanner, monitor, keyboard, mouse, webcam, headset, speaker
- **Network:** router, switch, network, modem, ethernet, wifi, cable
- **Storage:** hard drive, hdd, ssd
- **Components:** ram, memory, processor, cpu, gpu, motherboard
- **Power:** ups, charger, adapter
- **Connectivity:** usb, hdmi
- **Software:** software, license, antivirus
- **Mobile:** phone, mobile
- **Display:** projector, monitor

### Amharic Keywords (አማርኛ):
- ኮምፒውተር (computer)
- ላፕቶፕ (laptop)
- ዴስክቶፕ (desktop)
- ማተሚያ (printer)
- ስካነር (scanner)
- ማሳያ / ሞኒተር (monitor)
- ኪቦርድ (keyboard)
- ማውስ (mouse)
- ራውተር (router)
- ስዊች (switch)
- ኔትወርክ (network)
- ሰርቨር (server)
- ፕሮጀክተር (projector)
- ኬብል (cable)
- ሞደም (modem)
- ሃርድ (hard drive)
- ሶፍትዌር (software)
- ላይሰንስ (license)
- ታብሌት (tablet)
- ስልክ (phone)
- ቻርጀር (charger)
- አዳፕተር (adapter)

## ቴክኒካል ዝርዝሮች (Technical Details)

### የማወቂያ ተግባር (Detection Function)

```php
private function isIctRelated(string $itemDescription): bool
{
    $ictKeywords = [/* keyword array */];
    $itemLower = mb_strtolower($itemDescription, 'UTF-8');
    
    foreach ($ictKeywords as $keyword) {
        if (mb_stripos($itemLower, mb_strtolower($keyword, 'UTF-8')) !== false) {
            return true;
        }
    }
    
    return false;
}
```

### የማሳወቂያ ኮድ (Notification Code)

```php
if ($this->isIctRelated($itemDescription)) {
    NotificationService::notifyDepartment(
        DEPT_IT_ADMIN,
        $requestId,
        $message,
        $userId  // Exclude current user
    );
}
```

## የስራ ሂደት ምሳሌ (Workflow Example)

### ሁኔታ: ተጠቃሚ ላፕቶፕ ይጠይቃል
**Scenario: User requests a laptop**

1. **ጥያቄ ፈጠራ (Request Creation)**
   - ተጠቃሚ: "ላፕቶፕ ኮምፒውተር" ይጠይቃል
   - ስርዓት: "ላፕቶፕ" የአይሲቲ ቁልፍ ቃል መሆኑን ያውቃል
   - ማሳወቂያ → አይቲ አስተዳዳሪዎች: "አዲስ የአይሲቲ ንብረት ጥያቄ: ላፕቶፕ ኮምፒውተር"

2. **ፀደቃ እና ምዝገባ (Approval & Registration)**
   - የንብረት አስተዳደር: "Dell Latitude 5420" ብሎ ይመዘግባል
   - ስርዓት: "Latitude" የአይሲቲ ቁልፍ ቃል አይደለም ግን "laptop" ከመጀመሪያው ጥያቄ ጋር ተያይዟል
   - ማሳወቂያ → አይቲ አስተዳዳሪዎች: "የአይሲቲ ንብረት ተመዝግቧል: Dell Latitude 5420"

3. **ከስቶር መመደብ (Store Assignment)**
   - ግምጃ ቤት: LAP-001 ኮድ ያለው ላፕቶፕ ይመድባል
   - ስርዓት: "ላፕቶፕ" የአይሲቲ ቁልፍ ቃል መሆኑን ያውቃል
   - ማሳወቂያ → አይቲ አስተዳዳሪዎች: "የአይሲቲ ንብረት ከስቶር ተመድቧል: Dell Latitude 5420 (ኮድ: LAP-001)"

4. **መላክ (Release)**
   - ግምጃ ቤት: የመልቀቅ ፍቃድ RP-20260319-1234 ይሰጣል
   - ስርዓት: የአይሲቲ ንብረት መሆኑን ያውቃል
   - ማሳወቂያ → አይቲ አስተዳዳሪዎች: "የአይሲቲ ንብረት ለጠያቂ ተላልፏል: Dell Latitude 5420 - የመልቀቅ ፍቃድ: RP-20260319-1234"

## ጥቅሞች (Benefits)

### ለአይቲ አስተዳዳሪዎች:
✅ የአይሲቲ ንብረት እንቅስቃሴን በእውነተኛ ጊዜ ክትትል
✅ የንብረት ምዝገባ እና ስርጭት ግንዛቤ
✅ የአይሲቲ ንብረት አስተዳደር ቁጥጥር
✅ ለወደፊት ኦዲት እና ሪፖርት ማመቻቸት

### For IT Admins:
✅ Real-time tracking of ICT property movement
✅ Awareness of property registration and distribution
✅ Control over ICT asset management
✅ Facilitates future audits and reporting

### ለድርጅቱ:
✅ የተሻሻለ የንብረት ተጠያቂነት
✅ የአይሲቲ ንብረት ክትትል
✅ የተሻሻለ የስራ ሂደት ግልጽነት
✅ የተሻሻለ የመረጃ ደህንነት

### For the Organization:
✅ Improved asset accountability
✅ ICT property tracking
✅ Enhanced workflow transparency
✅ Better information security

## መሞከሪያ (Testing)

### እንዴት መሞከር እንደሚቻል:

1. **እንደ ተጠቃሚ ይግቡ** (requester)
2. **አዲስ ጥያቄ ይፍጠሩ** በ Form 20:
   - የእቃ መግለጫ: "ላፕቶፕ ኮምፒውተር Dell"
   - ብዛት: 1
   - ምክንያት: "ለስራ አስፈላጊ"
3. **እንደ አይቲ አስተዳዳሪ ይግቡ** (admin/admin123)
4. **ማሳወቂያዎችን ይመልከቱ** (🔔 ማሳወቂያዎች)
5. **ማሳወቂያ መኖሩን ያረጋግጡ**: "አዲስ የአይሲቲ ንብረት ጥያቄ: ላፕቶፕ..."
6. **የስራ ሂደቱን ይቀጥሉ** እና በእያንዳንዱ ደረጃ ማሳወቂያዎችን ይመልከቱ

### Test Cases:

| የእቃ መግለጫ | የሚታወቅ? | ምክንያት |
|-----------|---------|---------|
| "ላፕቶፕ ኮምፒውተር" | ✅ አዎ | "ላፕቶፕ" ቁልፍ ቃል ነው |
| "Dell Latitude 5420" | ❌ አይደለም | ምንም ቁልፍ ቃል የለም |
| "HP Printer LaserJet" | ✅ አዎ | "printer" ቁልፍ ቃል ነው |
| "ኮምፒውተር ማሳያ" | ✅ አዎ | "ኮምፒውተር" ቁልፍ ቃል ነው |
| "Office Chair" | ❌ አይደለም | የአይሲቲ ንብረት አይደለም |
| "Network Switch Cisco" | ✅ አዎ | "network" እና "switch" ቁልፍ ቃላት ናቸው |

## የተሻሻሉ ፋይሎች (Modified Files)

1. **src/controllers/RequestController.php**
   - Added `isIctRelated()` method
   - Added ICT notification in `create()` method

2. **src/controllers/ApprovalController.php**
   - Added `isIctRelated()` method
   - Added ICT notification in `approve()` method (registration)
   - Added ICT notification in `releaseItem()` method

3. **public/treasury-assign.php**
   - Added `isIctRelated()` function
   - Added ICT notification after item assignment

## ማስታወሻዎች (Notes)

- ማሳወቂያዎች በ `notifications` ሰንጠረዥ ውስጥ ይቀመጣሉ
- የአይሲቲ ማሳወቂያዎች ከመደበኛ የስራ ሂደት ማሳወቂያዎች በተጨማሪ ናቸው
- ቁልፍ ቃላት case-insensitive ናቸው (ትልቅ/ትንሽ ፊደል አይለዩም)
- የአማርኛ እና የእንግሊዝኛ ቁልፍ ቃላት ይደገፋሉ
- የአይሲቲ ማሳወቂያዎች ለአይቲ አስተዳዳሪዎች ብቻ ይላካሉ
- የአሁኑ ተጠቃሚ ከማሳወቂያው ተቀባዮች ይገለላል

## የወደፊት ማሻሻያዎች (Future Enhancements)

- 📊 የአይሲቲ ንብረት ዳሽቦርድ ለአይቲ አስተዳዳሪዎች
- 📈 የአይሲቲ ንብረት ሪፖርቶች እና ትንታኔዎች
- 🔍 የአይሲቲ ንብረት ፍለጋ እና ማጣሪያ
- 📱 የአይሲቲ ንብረት ክትትል በእውነተኛ ጊዜ
- 🏷️ የአይሲቲ ንብረት ምድብ አስተዳደር
- ⚙️ የቁልፍ ቃላት ማበጀት በአስተዳዳሪዎች
