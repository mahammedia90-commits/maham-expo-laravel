#!/bin/bash

# Comprehensive API Test — Maham Services
# Uses correct route paths from `php artisan route:list`

pass=0
fail=0

check() {
    local label="$1"
    local result="$2"
    if [ "$result" = "PASS" ]; then
        echo "  ✅ $label"
        pass=$((pass+1))
    else
        echo "  ❌ $label — $result"
        fail=$((fail+1))
    fi
}

get_token() {
    curl -s -X POST http://localhost:8001/api/v1/auth/login \
      -H "Content-Type: application/json" \
      -d "{\"identifier\":\"$1\",\"password\":\"password\"}" \
      | python3 -c "import sys,json; print(json.load(sys.stdin)['data']['token'])" 2>/dev/null
}

test_endpoint() {
    local label="$1"
    local url="$2"
    local token="$3"
    local result
    if [ -n "$token" ]; then
        result=$(curl -s "$url" -H "Authorization: Bearer $token" -H "Accept: application/json" | python3 -c "import sys,json; d=json.load(sys.stdin); print('PASS' if d.get('success',False) else d.get('error_code','FAIL'))" 2>&1)
    else
        result=$(curl -s "$url" -H "Accept: application/json" | python3 -c "import sys,json; d=json.load(sys.stdin); print('PASS' if d.get('success',False) or d.get('status','')=='ok' else 'FAIL')" 2>&1)
    fi
    check "$label" "$result"
}

# Get tokens
ADMIN_TOKEN=$(get_token "admin@maham-expo.sa")
SA_TOKEN=$(get_token "admin@example.com")
INV_TOKEN=$(get_token "ahmed@techventures.sa")
MER_TOKEN=$(get_token "mohammed@alsalamtrading.sa")
SPO_TOKEN=$(get_token "sponsor1@techgroup.sa")

ADMIN_ID=$(curl -s http://localhost:8001/api/v1/auth/me -H "Authorization: Bearer $ADMIN_TOKEN" | python3 -c "import sys,json; print(json.load(sys.stdin)['data']['id'])" 2>/dev/null)

echo "Tokens obtained for: admin, super-admin, investor, merchant, sponsor"
echo "Admin ID: $ADMIN_ID"
echo ""

# ==========================================
echo "=========================================="
echo "  1. AUTH SERVICE"
echo "=========================================="

test_endpoint "Health" "http://localhost:8001/api/health" ""

R=$(curl -s -X POST http://localhost:8001/api/v1/auth/login -H "Content-Type: application/json" -d '{"identifier":"admin@maham-expo.sa","password":"password"}' | python3 -c "import sys,json; d=json.load(sys.stdin); print('PASS' if d['success'] else 'FAIL')" 2>&1)
check "Login" "$R"

test_endpoint "Auth/Me" "http://localhost:8001/api/v1/auth/me" "$ADMIN_TOKEN"

R=$(curl -s -X POST http://localhost:8001/api/v1/auth/refresh -H "Authorization: Bearer $ADMIN_TOKEN" | python3 -c "import sys,json; d=json.load(sys.stdin); print('PASS' if d['success'] else 'FAIL')" 2>&1)
check "Token Refresh" "$R"

# Re-login after refresh
ADMIN_TOKEN=$(get_token "admin@maham-expo.sa")

R=$(curl -s -X POST http://localhost:8001/api/v1/service/verify-token -H "Content-Type: application/json" -d "{\"token\":\"$ADMIN_TOKEN\"}" | python3 -c "import sys,json; d=json.load(sys.stdin); print('PASS' if d['success'] else 'FAIL')" 2>&1)
check "S2S verify-token" "$R"

R=$(curl -s -X POST http://localhost:8001/api/v1/service/check-permission -H "Content-Type: application/json" -d "{\"user_id\":\"$ADMIN_ID\",\"permission\":\"events.view\"}" | python3 -c "import sys,json; d=json.load(sys.stdin); print('PASS' if d['success'] else 'FAIL')" 2>&1)
check "S2S check-permission" "$R"

R=$(curl -s -X POST http://localhost:8001/api/v1/service/user-info -H "Content-Type: application/json" -d "{\"user_id\":\"$ADMIN_ID\"}" | python3 -c "import sys,json; d=json.load(sys.stdin); print('PASS' if d['success'] else 'FAIL')" 2>&1)
check "S2S user-info" "$R"

test_endpoint "Users List" "http://localhost:8001/api/v1/users" "$ADMIN_TOKEN"
test_endpoint "Roles List" "http://localhost:8001/api/v1/roles" "$ADMIN_TOKEN"
test_endpoint "Permissions List" "http://localhost:8001/api/v1/permissions" "$ADMIN_TOKEN"
test_endpoint "Services List" "http://localhost:8001/api/v1/services" "$ADMIN_TOKEN"

echo ""
echo "=========================================="
echo "  2. EXPO API — PUBLIC"
echo "=========================================="

test_endpoint "Health" "http://localhost:8002/api/health" ""
test_endpoint "Events" "http://localhost:8002/api/v1/events" ""
test_endpoint "Categories" "http://localhost:8002/api/v1/categories" ""
test_endpoint "Cities" "http://localhost:8002/api/v1/cities" ""
test_endpoint "FAQs" "http://localhost:8002/api/v1/faqs" ""
test_endpoint "Banners" "http://localhost:8002/api/v1/banners" ""
test_endpoint "Pages" "http://localhost:8002/api/v1/pages" ""

# Event detail (get first event)
EVENT_ID=$(curl -s http://localhost:8002/api/v1/events -H "Accept: application/json" | python3 -c "import sys,json; d=json.load(sys.stdin).get('data',{}).get('data',[]); print(d[0]['id'] if d else '')" 2>/dev/null)

if [ -n "$EVENT_ID" ]; then
    test_endpoint "Event Detail" "http://localhost:8002/api/v1/events/$EVENT_ID" ""
    test_endpoint "Event Sections" "http://localhost:8002/api/v1/events/$EVENT_ID/sections" ""
    test_endpoint "Event Spaces" "http://localhost:8002/api/v1/events/$EVENT_ID/spaces" ""
    test_endpoint "Event Services" "http://localhost:8002/api/v1/events/$EVENT_ID/services" ""
    test_endpoint "Event Sponsors" "http://localhost:8002/api/v1/events/$EVENT_ID/sponsor-packages" ""
fi

echo ""
echo "=========================================="
echo "  3. EXPO API — ADMIN MANAGE"
echo "=========================================="

test_endpoint "Events" "http://localhost:8002/api/v1/manage/events" "$ADMIN_TOKEN"
test_endpoint "Services" "http://localhost:8002/api/v1/manage/services" "$ADMIN_TOKEN"
test_endpoint "Rental Requests" "http://localhost:8002/api/v1/manage/rental-requests" "$ADMIN_TOKEN"
test_endpoint "Visit Requests" "http://localhost:8002/api/v1/manage/visit-requests" "$ADMIN_TOKEN"
test_endpoint "Profiles" "http://localhost:8002/api/v1/manage/profiles" "$ADMIN_TOKEN"
test_endpoint "Dashboard" "http://localhost:8002/api/v1/manage/dashboard" "$ADMIN_TOKEN"
test_endpoint "Statistics" "http://localhost:8002/api/v1/manage/statistics" "$ADMIN_TOKEN"
test_endpoint "Ratings" "http://localhost:8002/api/v1/manage/ratings" "$ADMIN_TOKEN"
test_endpoint "Support Tickets" "http://localhost:8002/api/v1/manage/support-tickets" "$ADMIN_TOKEN"
test_endpoint "Rental Contracts" "http://localhost:8002/api/v1/manage/rental-contracts" "$ADMIN_TOKEN"
test_endpoint "Invoices" "http://localhost:8002/api/v1/manage/invoices" "$ADMIN_TOKEN"
test_endpoint "Pages" "http://localhost:8002/api/v1/manage/pages" "$ADMIN_TOKEN"
test_endpoint "FAQs" "http://localhost:8002/api/v1/manage/faqs" "$ADMIN_TOKEN"
test_endpoint "Banners" "http://localhost:8002/api/v1/manage/banners" "$ADMIN_TOKEN"
test_endpoint "Categories" "http://localhost:8002/api/v1/manage/categories" "$ADMIN_TOKEN"
test_endpoint "Cities" "http://localhost:8002/api/v1/manage/cities" "$ADMIN_TOKEN"
test_endpoint "Users" "http://localhost:8002/api/v1/manage/users" "$ADMIN_TOKEN"
test_endpoint "Settings" "http://localhost:8002/api/v1/manage/settings" "$ADMIN_TOKEN"
test_endpoint "Sponsors" "http://localhost:8002/api/v1/manage/sponsors" "$ADMIN_TOKEN"
test_endpoint "Analytics" "http://localhost:8002/api/v1/manage/analytics" "$ADMIN_TOKEN"

# Nested manage routes (require event ID)
if [ -n "$EVENT_ID" ]; then
    test_endpoint "Event Sections (manage)" "http://localhost:8002/api/v1/manage/events/$EVENT_ID/sections" "$ADMIN_TOKEN"
    test_endpoint "Event Spaces (manage)" "http://localhost:8002/api/v1/manage/events/$EVENT_ID/spaces" "$ADMIN_TOKEN"
    test_endpoint "Event Sponsor Packages" "http://localhost:8002/api/v1/manage/events/$EVENT_ID/sponsor-packages" "$ADMIN_TOKEN"
fi

echo ""
echo "=========================================="
echo "  4. INVESTOR (ahmed@techventures.sa)"
echo "=========================================="

if [ "${INV_TOKEN:0:3}" = "eyJ" ]; then
    check "Login" "PASS"
    test_endpoint "My Spaces" "http://localhost:8002/api/v1/my/spaces" "$INV_TOKEN"
    test_endpoint "Received Rental Requests" "http://localhost:8002/api/v1/my/received-rental-requests" "$INV_TOKEN"
    test_endpoint "Received Visit Requests" "http://localhost:8002/api/v1/my/received-visit-requests" "$INV_TOKEN"
    test_endpoint "My Payments" "http://localhost:8002/api/v1/my/payments" "$INV_TOKEN"
    test_endpoint "My Dashboard" "http://localhost:8002/api/v1/my/dashboard" "$INV_TOKEN"
    test_endpoint "My Activity" "http://localhost:8002/api/v1/my/activity" "$INV_TOKEN"
    test_endpoint "My Rental Contracts" "http://localhost:8002/api/v1/my/rental-contracts" "$INV_TOKEN"
else
    check "Login" "FAIL"
fi

echo ""
echo "=========================================="
echo "  5. MERCHANT (mohammed@alsalamtrading.sa)"
echo "=========================================="

if [ "${MER_TOKEN:0:3}" = "eyJ" ]; then
    check "Login" "PASS"
    test_endpoint "My Dashboard" "http://localhost:8002/api/v1/my/dashboard" "$MER_TOKEN"
    test_endpoint "My Activity" "http://localhost:8002/api/v1/my/activity" "$MER_TOKEN"
else
    check "Login" "FAIL"
fi

echo ""
echo "=========================================="
echo "  6. SPONSOR (sponsor1@techgroup.sa)"
echo "=========================================="

if [ "${SPO_TOKEN:0:3}" = "eyJ" ]; then
    check "Login" "PASS"
    test_endpoint "Sponsor Contracts" "http://localhost:8002/api/v1/my/sponsor-contracts" "$SPO_TOKEN"
    test_endpoint "Sponsor Payments" "http://localhost:8002/api/v1/my/sponsor-payments" "$SPO_TOKEN"
    test_endpoint "Sponsor Assets" "http://localhost:8002/api/v1/my/sponsor-assets" "$SPO_TOKEN"
    test_endpoint "Sponsor Exposure" "http://localhost:8002/api/v1/my/sponsor-exposure" "$SPO_TOKEN"
    test_endpoint "My Dashboard" "http://localhost:8002/api/v1/my/dashboard" "$SPO_TOKEN"
else
    check "Login" "FAIL"
fi

echo ""
echo "=========================================="
echo "  7. SUPER ADMIN (admin@example.com)"
echo "=========================================="

if [ "${SA_TOKEN:0:3}" = "eyJ" ]; then
    check "Login" "PASS"
    test_endpoint "Users (auth)" "http://localhost:8001/api/v1/users" "$SA_TOKEN"
    test_endpoint "Roles (auth)" "http://localhost:8001/api/v1/roles" "$SA_TOKEN"
    test_endpoint "Settings (expo)" "http://localhost:8002/api/v1/manage/settings" "$SA_TOKEN"
    test_endpoint "Users (expo)" "http://localhost:8002/api/v1/manage/users" "$SA_TOKEN"
else
    check "Login" "FAIL"
fi

echo ""
echo "=========================================="
echo "  8. COMMON AUTHENTICATED"
echo "=========================================="

test_endpoint "Favorites" "http://localhost:8002/api/v1/favorites" "$ADMIN_TOKEN"
test_endpoint "Profile" "http://localhost:8002/api/v1/profile" "$ADMIN_TOKEN"

echo ""
echo "=========================================="
echo "  RESULTS"
echo "=========================================="
echo "  ✅ Passed: $pass"
echo "  ❌ Failed: $fail"
echo "  📊 Total:  $((pass+fail))"
echo "=========================================="
