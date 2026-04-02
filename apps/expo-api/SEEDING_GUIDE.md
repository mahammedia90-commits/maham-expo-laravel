# 📚 DATABASE SEEDING GUIDE

**Date**: April 2, 2026
**Purpose**: Populate test data for Three Portals (Investor, Merchant, Sponsor)

---

## 🎯 Overview

Three new seeders have been created to populate the database with realistic test data for the three portal systems:

1. **InvestorProfileSeeder** - 6 investor records
2. **MerchantBusinessProfileSeeder** - 6 merchant business profiles
3. **SpaceBoothSeeder** - 10 booth/space records for merchants
4. **RentalContractSeeder** - 5 merchant rental contracts
5. **SponsorSeeder** - 6 sponsor records (updated with portal data)
6. **SponsorContractSeeder** - 6 sponsor contracts
7. **SponsorPaymentSeeder** - 9 sponsor payment records

---

## 📊 DATA OVERVIEW

### Investor Portal Data (6 investors)
```
✓ Ahmed Al-Rashid (أحمد الراشد) - Technology - 5M SAR Investment - 25.5% ROI
✓ Sarah Mohammed (سارة محمد) - Real Estate - 3.5M SAR Investment - 18.2% ROI
✓ Mohammed Hassan (محمد حسن) - Retail - 2.8M SAR Investment - 22.1% ROI
✓ Fatima Al-Otaibi (فاطمة العتيبي) - Technology - 4.2M SAR Investment - 28.3% ROI
✓ Ali Al-Nouri (علي النوري) - Hospitality - 2.1M SAR Investment - 15.7% ROI
✓ Laila Al-Sharif (ليلى الشريف) - Real Estate - 6.5M SAR Investment - 31.2% ROI
```

### Merchant Portal Data (6 merchants + 10 booths + 5 contracts)
```
Merchants:
✓ Modern Tech Store (متجر التقنية الحديثة) - Technology
✓ Luxury Fashion World (عالم الملابس الفاخرة) - Fashion
✓ Arab Flavors Restaurant (مطعم النكهات العربية) - Food & Beverage
✓ Jewelry & Gold Gems (جواهر الزينة والذهب) - Jewelry
✓ Furniture & Decor Home (محل الأثاث والديكور) - Home & Living
✓ Fitness Center Pro (مركز اللياقة البدنية) - Health & Fitness

Booths:
✓ 10 booths ranging from 8-30 capacity
✓ Prices: 40,000 - 150,000 SAR
✓ Mix of occupied and available booths
✓ Various hall locations (A, B, C, Entrance, VIP)

Rental Contracts:
✓ 5 active contracts
✓ Payment status: paid, partial, unpaid
✓ Contract amounts: 50,000 - 120,000 SAR
```

### Sponsor Portal Data (6 sponsors + 6 contracts + 9 payments)
```
Sponsors by Tier:
✓ Platinum (1): Advanced Tech Company - 2.5M SAR
✓ Gold (2): Gulf Investment Bank - 1.5M SAR, Global Logistics - 1.2M SAR
✓ Silver (2): Media & Publishing - 800K SAR, Training Center - 750K SAR
✓ Bronze (1): Security & Protection - 400K SAR

Contracts:
✓ 6 sponsorship contracts with deliverables
✓ Payment terms: Monthly, Quarterly, Upfront
✓ All linked to MAHAM Expo 2024 event

Payments:
✓ 9 payment records
✓ Status: completed (6), pending (3)
✓ Methods: bank_transfer (8), credit_card (1)
✓ Various installment schedules
```

---

## 🚀 How to Run Seeders

### Option 1: Run All Seeders (Full Database)

Run the complete database seeding including all existing data plus the new portal data:

```bash
cd C:\Users\Mustafa\Desktop\mobile-app\maham-expo-laravel\apps\expo-api

# Run all seeders
php artisan db:seed

# Or with fresh database (DELETE ALL DATA)
php artisan migrate:fresh --seed
```

**Note**: This will run ALL seeders in the correct order as defined in `DatabaseSeeder.php`

### Option 2: Run Only Portal Seeders

If you want to add just the portal data to an existing database:

```bash
cd C:\Users\Mustafa\Desktop\mobile-app\maham-expo-laravel\apps\expo-api

# Run specific portal seeders
php artisan db:seed --class=InvestorProfileSeeder
php artisan db:seed --class=MerchantBusinessProfileSeeder
php artisan db:seed --class=SpaceBoothSeeder
php artisan db:seed --class=RentalContractSeeder
php artisan db:seed --class=SponsorSeeder
php artisan db:seed --class=SponsorContractSeeder
php artisan db:seed --class=SponsorPaymentSeeder
```

### Option 3: Run Portal Seeder Bundle

If you want to run all portal seeders at once:

```bash
cd C:\Users\Mustafa\Desktop\mobile-app\maham-expo-laravel\apps\expo-api

# This will run all three portal seeders in order
php artisan db:seed --class=PortalsSeeder
```

**Note**: PortalsSeeder includes InvestorProfileSeeder, MerchantBusinessProfileSeeder, SpaceBoothSeeder, RentalContractSeeder, SponsorSeeder, SponsorContractSeeder, and SponsorPaymentSeeder

---

## 📋 Seeding Order & Dependencies

```
Database Structure:
├── Events (already seeded)
├── Spaces/Booths (NEW: SpaceBoothSeeder)
│   └── Depends on: Events
├── Investors (NEW: InvestorProfileSeeder)
├── Merchants (NEW: MerchantBusinessProfileSeeder)
│   ├── Rental Contracts (NEW: RentalContractSeeder)
│   │   └── Depends on: Spaces, Merchants
├── Sponsors (NEW/Updated: SponsorSeeder)
│   ├── Sponsor Contracts (NEW: SponsorContractSeeder)
│   │   └── Depends on: Sponsors, Events
│   └── Sponsor Payments (NEW: SponsorPaymentSeeder)
│       └── Depends on: Sponsors
```

**Recommended Order**:
1. SpaceBoothSeeder (depends on Events)
2. InvestorProfileSeeder (no dependencies)
3. MerchantBusinessProfileSeeder (no dependencies)
4. RentalContractSeeder (depends on Spaces, Merchants)
5. SponsorSeeder (or will be overwritten - handle carefully)
6. SponsorContractSeeder (depends on Sponsors)
7. SponsorPaymentSeeder (depends on Sponsors)

---

## ⚠️ IMPORTANT NOTES

### Duplicate Seeder Classes

**Warning**: Both the original DatabaseSeeder and new seeders have:
- `RentalContractSeeder.php` - May create duplicate contracts
- `SponsorSeeder.php` - May create duplicate sponsors

**Solution**:
- Option A: Delete old versions and keep new ones with portal data
- Option B: Run only specific portal seeders instead of the full db:seed
- Option C: Modify existing seeders to use new data

### Merchant Data Handling

- `MerchantBusinessProfileSeeder` creates NEW merchant records
- The original `BusinessProfileSeeder` may also create merchant records
- Check if both should run or if you need to replace one

### Event Dependency

SpaceBoothSeeder creates booths that depend on existing Event records. If no events exist:
```bash
# First seed events if needed
php artisan db:seed --class=EventSeeder

# Then seed booths
php artisan db:seed --class=SpaceBoothSeeder
```

---

## 🔍 Verification After Seeding

After running seeders, verify the data:

```bash
# Check investor count
php artisan tinker
>>> App\Models\InvestorProfile::count()
6

# Check merchant count
>>> App\Models\BusinessProfile::where('type', 'merchant')->count()
6

# Check booths count
>>> App\Models\Space::count()
10

# Check sponsors count
>>> App\Models\Sponsor::count()
6

# Check rental contracts
>>> App\Models\RentalContract::count()
5

# Check sponsor contracts
>>> App\Models\SponsorContract::count()
6

# Check sponsor payments
>>> App\Models\SponsorPayment::count()
9
```

---

## 🧪 Testing Portal APIs After Seeding

Once data is seeded, test the portal endpoints:

```bash
# Investor Portal
curl http://localhost:8002/api/admin/investor-portal/dashboard
curl http://localhost:8002/api/admin/investor-portal/investors

# Merchant Portal
curl http://localhost:8002/api/admin/merchant-portal/dashboard
curl http://localhost:8002/api/admin/merchant-portal/merchants
curl http://localhost:8002/api/admin/merchant-portal/booths

# Sponsor Portal
curl http://localhost:8002/api/admin/sponsor-portal/dashboard
curl http://localhost:8002/api/admin/sponsor-portal/sponsors
curl http://localhost:8002/api/admin/sponsor-portal/packages
```

---

## 📝 Seeder File Locations

All seeders are located in:
```
C:\Users\Mustafa\Desktop\mobile-app\maham-expo-laravel\apps\expo-api\database\seeders\
```

Files:
- ✅ `InvestorProfileSeeder.php` (New)
- ✅ `MerchantBusinessProfileSeeder.php` (New)
- ✅ `SpaceBoothSeeder.php` (New)
- ✅ `RentalContractSeeder.php` (New)
- ✅ `SponsorSeeder.php` (Updated)
- ✅ `SponsorContractSeeder.php` (New)
- ✅ `SponsorPaymentSeeder.php` (New)
- ✅ `PortalsSeeder.php` (Master seeder)

---

## 🎯 Quick Start

```bash
# Navigate to API directory
cd C:\Users\Mustafa\Desktop\mobile-app\maham-expo-laravel\apps\expo-api

# Clear database and seed everything
php artisan migrate:fresh --seed

# Or just add portal data to existing database
php artisan db:seed --class=PortalsSeeder
```

---

## 📞 Troubleshooting

### Error: "Table 'xxx' doesn't exist"
**Solution**: Run migrations first
```bash
php artisan migrate
```

### Error: "SQLSTATE[23000]: Integrity constraint violation"
**Solution**: Foreign key constraint failed - ensure events/spaces exist
```bash
php artisan db:seed --class=EventSeeder
php artisan db:seed --class=SpaceBoothSeeder
```

### Duplicate data after seeding
**Solution**: Drop and reseed, or check which seeders to skip
```bash
php artisan migrate:refresh --seed
```

### Memory limit exceeded
**Solution**: Increase PHP memory
```bash
php -d memory_limit=512M artisan db:seed
```

---

**Last Updated**: April 2, 2026
**Status**: ✅ Ready for seeding
