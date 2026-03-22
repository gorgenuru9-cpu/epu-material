# የአይሲቲ ሙያ ድጋፍ መመሪያ / ICT Support Guide

## አጠቃቀም / Overview

የአይሲቲ ሙያ ድጋፍ ስርዓት ተጠቃሚዎች የአይቲ ድጋፍ እንዲጠይቁ የሚያስችል ነው።

## ባህሪያት / Features

### ለተጠቃሚዎች / For Users:

1. **አዲስ ድጋፍ ጥያቄ መፍጠር** (Create Support Request)
   - ርዕስ (Subject)
   - ዝርዝር መግለጫ (Description)
   - ምድብ (Category): Hardware, Software, Network, Account, Other
   - ቅድሚያ (Priority): Low, Medium, High, Urgent

2. **ጥያቄዎችን መከታተል** (Track Requests)
   - የጥያቄ ሁኔታ (Status): Pending, In Progress, Resolved, Closed
   - የመልስ ጊዜ (Response time)
   - መፍትሄ (Resolution)

3. **ማሳወቂያዎች** (Notifications)
   - የሁኔታ ለውጦች (Status updates)
   - የመልስ ማሳወቂያዎች (Response notifications)

### ለአይቲ አስተዳዳሪዎች / For IT Admins:

1. **ሁሉም ጥያቄዎችን ማየት** (View All Requests)
   - በቅድሚያ የተደረደ (Sorted by priority)
   - የተጠቃሚ መረጃ (User information)
   - የክፍል መረጃ (Department information)

2. **ጥያቄዎችን ማስተዳደር** (Manage Requests)
   - ሁኔታ መቀየር (Change status)
   - መልስ መስጠት (Respond)
   - መፍትሄ መስጠት (Provide resolution)

3. **ስታቲስቲክስ** (Statistics)
   - ጠቅላላ ጥያቄዎች (Total requests)
   - በመጠባበቅ ላይ (Pending)
   - የተፈቱ (Resolved)

## የመጠቀሚያ መመሪያ / Usage Instructions

### አዲስ ድጋፍ ጥያቄ መፍጠር / Creating a Support Request

1. **ወደ አይሲቲ ድጋፍ ገጽ ይሂዱ**
   - Sidebar → 💻 አይሲቲ ድጋፍ

2. **"አዲስ ድጋፍ ጥያቄ" ይጫኑ**
   - ቁልፉ በላይኛው ቀኝ ጥግ ላይ ነው

3. **ፎርሙን ይሙሉ**
   - **ርዕስ**: የችግሩን አጭር መግለጫ
   - **ዝርዝር መግለጫ**: ችግሩን በዝርዝር ይግለጹ
   - **ምድብ**: የችግሩን ዓይነት ይምረጡ
   - **ቅድሚያ**: የችግሩን አስቸኳይነት ይምረጡ

4. **"ጥያቄ ላክ" ይጫኑ**

### ጥያቄዎችን መከታተል / Tracking Requests

1. **የእኔ የድጋፍ ጥያቄዎች ክፍል**
   - ሁሉም ጥያቄዎችዎን ማየት ይችላሉ
   - የሁኔታ ለውጦችን ማየት ይችላሉ

2. **የሁኔታ ምልክቶች**
   - 🟡 PENDING - በመጠባበቅ ላይ
   - 🔵 IN PROGRESS - በሂደት ላይ
   - 🟢 RESOLVED - ተፈትቷል
   - ⚫ CLOSED - ተዘግቷል

3. **የቅድሚያ ምልክቶች**
   - 🔴 URGENT - አስቸኳይ
   - 🟠 HIGH - ከፍተኛ
   - 🟡 MEDIUM - መካከለኛ
   - 🟢 LOW - ዝቅተኛ

## የዳታቤዝ ማዋቀሪያ / Database Setup

### ሰንጠረዥ መፍጠር / Create Table

```bash
mysql -u root -p property_request_system < database/create_ict_support_table.sql
```

ወይም በ phpMyAdmin:
1. `property_request_system` database ይምረጡ
2. "SQL" tab ይጫኑ
3. `database/create_ict_support_table.sql` ይክፈቱ እና ይቅዱ
4. "Go" ይጫኑ

## ምሳሌዎች / Examples

### ምሳሌ 1: የሃርድዌር ችግር

**ርዕስ**: ኮምፒውተሩ አይነሳም
**መግለጫ**: የቢሮዬ ኮምፒውተር ዛሬ ጠዋት ከተነሳ በኋላ እንደገና አይነሳም። የኤሌክትሪክ ችግር አይመስልም።
**ምድብ**: Hardware
**ቅድሚያ**: High

### ምሳሌ 2: የሶፍትዌር ችግር

**ርዕስ**: ስርዓቱ በጣም ዘግይቷል
**መግለጫ**: የንብረት ጥያቄ ስርዓቱ ዛሬ በጣም ዘግይቷል። ገጾች ለመጫን ረጅም ጊዜ ይወስዳል።
**ምድብ**: Software
**ቅድሚያ**: Medium

### ምሳሌ 3: የኔትወርክ ችግር

**ርዕስ**: ኢንተርኔት አይገናኝም
**መግለጫ**: ከትናንት ጀምሮ ኢንተርኔት ግንኙነት የለም። WiFi ተገናኝቷል ግን ኢንተርኔት የለም።
**ምድብ**: Network
**ቅድሚያ**: Urgent

## ለአይቲ አስተዳዳሪዎች / For IT Administrators

### ጥያቄዎችን ማስተዳደር

1. **ሁሉም ጥያቄዎችን ማየት**
   - በቅድሚያ የተደረደ ዝርዝር
   - አስቸኳይ ጥያቄዎች በላይ

2. **መልስ መስጠት**
   - "መልስ ስጥ" ቁልፍ ይጫኑ
   - መፍትሄ ያስገቡ
   - ሁኔታ ይቀይሩ

3. **ስታቲስቲክስ መከታተል**
   - ጠቅላላ ጥያቄዎች
   - አማካይ የመልስ ጊዜ
   - የተፈቱ ጥያቄዎች መቶኛ

## ምክሮች / Tips

### ለተጠቃሚዎች:

1. **ግልጽ ርዕስ ይጻፉ** - ችግሩን በአጭሩ ይግለጹ
2. **ዝርዝር መግለጫ ይስጡ** - ችግሩን በዝርዝር ይግለጹ
3. **ቅድሚያ በትክክል ይምረጡ** - አስቸኳይ ጥያቄዎችን ብቻ "Urgent" ያድርጉ
4. **ምስሎችን ያያይዙ** (በወደፊት) - የስክሪን ሾት ካለ ያያይዙ

### ለአይቲ አስተዳዳሪዎች:

1. **በፍጥነት ይመልሱ** - አስቸኳይ ጥያቄዎችን በቅድሚያ
2. **ግልጽ መፍትሄ ይስጡ** - ተጠቃሚው ሊረዳው የሚችል መፍትሄ
3. **ሁኔታ ያዘምኑ** - ተጠቃሚው እንዲከታተል
4. **ስታቲስቲክስ ይከታተሉ** - የአገልግሎት ጥራትን ለማሻሻል

## ችግር መፍታት / Troubleshooting

### ጥያቄ መላክ አልተቻለም

1. **ሁሉም መስኮች ተሞልተዋል?**
2. **ኢንተርኔት ግንኙነት አለ?**
3. **ብራውዘር ይሞክሩ** - Chrome, Firefox, Edge

### ጥያቄዎች አይታዩም

1. **ገጹን ያድሱ** (F5)
2. **ብራውዘር cache ያጽዱ**
3. **እንደገና ይግቡ**

## የወደፊት ማሻሻያዎች / Future Enhancements

- [ ] የፋይል ማያያዣ (File attachments)
- [ ] የቻት ድጋፍ (Chat support)
- [ ] የኢሜል ማሳወቂያዎች (Email notifications)
- [ ] የሞባይል መተግበሪያ (Mobile app)
- [ ] የእውቀት ቤዝ (Knowledge base)
- [ ] FAQ ክፍል (FAQ section)

## ድጋፍ / Support

ለተጨማሪ እገዛ:
- የአይቲ ድጋፍ ቡድን: support@example.com
- ስልክ: +251-11-xxx-xxxx
- ቢሮ: የአይቲ ክፍል, 2ኛ ፎቅ
