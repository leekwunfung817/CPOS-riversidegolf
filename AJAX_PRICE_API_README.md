# Price Management AJAX System - Documentation

## Overview

This system provides a complete AJAX-based price management solution for the Golf Booking application. It separates the API layer from the presentation layer, allowing for:

- **Dynamic price loading** via AJAX calls
- **User authentication** verification on every API request
- **Inline price editing** with real-time updates
- **Session-based security** - only logged-in users can modify prices
- **RESTful API** design for easy integration

## Files Created

### 1. `price_api.php` - AJAX API Backend
**Location:** `/price_api.php`

This is the backend API that handles all price-related operations.

#### Authentication
- Checks `$_SESSION["management"]` to verify user is logged in
- Returns HTTP 401 if user is not authenticated
- All requests require valid session from `configuration-administraion.php`

#### API Endpoints

##### GET: Fetch a single price
```
GET /price_api.php?action=get&table=golf_price&period=workday&price_category=general_bay&identity=hourly
```

**Parameters:**
- `action` (required): `get`
- `table` (required): `golf_price` or `golf_price_2`
- `period` (required): `workday`, `holiday`, or `holiday_19To22`
- `price_category` (required): `general_bay`, `sand_bay`, `pickle_ball`, or `vip`
- `identity` (optional): `hourly`, `student`, or `disabled` (not used for sand_bay and vip)

**Response:**
```json
{
  "success": true,
  "data": {
    "price": "100",
    "table": "golf_price",
    "period": "workday",
    "identity": "hourly",
    "price_category": "general_bay"
  }
}
```

Or if price not found:
```json
{
  "success": true,
  "data": {
    "price": "-",
    "table": "golf_price",
    ...
  }
}
```

##### POST: Update a price
```
POST /price_api.php
Content-Type: application/x-www-form-urlencoded

action=update&table=golf_price&period=workday&price_category=general_bay&identity=hourly&new_price=150
```

**Parameters:**
- `action` (required): `update`
- `table` (required): `golf_price` or `golf_price_2`
- `period` (required): `workday`, `holiday`, or `holiday_19To22`
- `price_category` (required): `general_bay`, `sand_bay`, `pickle_ball`, or `vip`
- `identity` (optional): `hourly`, `student`, or `disabled`
- `new_price` (required): New price value (must be > 0)

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Price updated successfully",
    "affected_rows": 1
  }
}
```

##### GET: Get all prices for a table
```
GET /price_api.php?action=get_prices_list&table=golf_price&period=workday
```

**Parameters:**
- `action` (required): `get_prices_list`
- `table` (required): `golf_price` or `golf_price_2`
- `period` (optional): Filter by specific period

### 2. `price_manager.js` - Frontend JavaScript Manager
**Location:** `/price_manager.js`

Handles all client-side interactions with the API.

#### Main Class: `PriceManager`

**Methods:**

##### `loadPriceIntoCell(cellElement, table, period, priceCategory, identity = '')`
Loads a price from the API and displays it in a table cell. Makes the cell clickable for editing if user is logged in.

```javascript
const cell = document.getElementById('workday-hourly-golf');
priceManager.loadPriceIntoCell(
    cell, 
    'golf_price', 
    'workday', 
    'general_bay', 
    'hourly'
);
```

##### `fetchPrice(table, period, priceCategory, identity = '')`
Low-level function to fetch a price from the API.

```javascript
priceManager.fetchPrice('golf_price', 'workday', 'general_bay', 'hourly')
    .then(data => {
        console.log('Price:', data.price);
    });
```

##### `enablePriceEdit(cellElement)`
Converts a price cell into an editable input field with save/cancel buttons.

##### `savePrice(cellElement, newPrice)`
Saves the updated price to the database via API.

##### `cancelEdit()`
Cancels the current edit operation and reverts to original value.

#### Features:
- **Automatic highlighting** when prices are updated (green background)
- **Loading indicator** while fetching from API
- **Keyboard support:** Enter to save, Escape to cancel
- **Input validation:** Only values > 0 are accepted
- **Visual feedback:** Editable cells highlight on hover

### 3. `price_display_ajax.php` - Example Implementation
**Location:** `/price_display_ajax.php`

A complete example showing how to use the new AJAX system. This replaces the functionality of the original `price_display.php` but uses AJAX instead of direct queries.

#### Features:
- All prices load via AJAX
- Login status indicator at top of page
- Read-only mode for non-logged-in users
- Full editing capabilities for authenticated users
- Responsive design with clean styling

## Integration Guide

### Option 1: Use the New AJAX Display (Recommended)
Simply access `/price_display_ajax.php` instead of `/price_display.php`.

### Option 2: Integrate into Existing Pages
To add AJAX price loading to any existing page:

#### Step 1: Add Script Reference
```html
<script src="./price_manager.js"></script>
```

#### Step 2: Create Price Display Cells
```html
<td class="price-cell" id="workday-hourly-golf"></td>
```

#### Step 3: Load Prices on Page Load
```javascript
window.addEventListener('DOMContentLoaded', function() {
    priceManager.loadPriceIntoCell(
        document.getElementById('workday-hourly-golf'),
        'golf_price',
        'workday',
        'general_bay',
        'hourly'
    );
});
```

## Security Features

### Authentication Verification
Every API request checks the session:
```php
if (!isset($_SESSION["management"]) || empty($_SESSION["management"])) {
    // Return 401 Unauthorized
}
```

### Input Sanitization
- Database table names are validated against a whitelist
- Period and identity values are escaped
- Price values are validated as floats

### SQL Injection Prevention
All user inputs are escaped using `mysqli::real_escape_string()`

## Error Handling

### API Errors
All API errors return appropriate HTTP status codes:
- `200 OK`: Successful request
- `400 Bad Request`: Invalid parameters
- `401 Unauthorized`: User not logged in
- `500 Internal Server Error`: Database error

### Frontend Error Handling
The JavaScript manager catches errors and displays appropriate messages:
```javascript
.catch(error => {
    console.error('Error:', error);
    alert('Error saving price: ' + error.message);
});
```

## Usage Examples

### Example 1: Fetch and Display a Single Price
```javascript
const priceCell = document.getElementById('price-cell');

priceManager.fetchPrice('golf_price', 'workday', 'general_bay', 'hourly')
    .then(data => {
        if (data && data.price !== '-') {
            priceCell.textContent = '$' + data.price;
        }
    });
```

### Example 2: Load All Prices for a Table
```javascript
priceManager.loadAllPrices('golf_price', 'workday')
    .then(prices => {
        prices.forEach(price => {
            console.log(`${price['price-name']}: $${price.price}`);
        });
    });
```

### Example 3: Custom AJAX Implementation
```javascript
fetch('./price_api.php?action=get&table=golf_price&period=workday&price_category=general_bay&identity=hourly')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Price:', data.data.price);
        }
    });
```

## Session Variables Required

The system uses the following session variables set by `configuration-administraion.php`:

```php
$_SESSION["management"]  // Must exist for authentication
$_SESSION["auth"]        // Authentication token
$_SESSION["datetime"]    // Login timestamp
$_SESSION["email"]       // User's email
$_SESSION["identity"]    // Role (admin, full-time, part-time, manager)
$_SESSION['name']        // Username
$_SESSION['name2']       // Display name
```

## Testing the API

### Using curl:
```bash
# Get a price (requires valid session)
curl "http://localhost/price_api.php?action=get&table=golf_price&period=workday&price_category=general_bay&identity=hourly"

# Update a price (requires valid session)
curl -X POST "http://localhost/price_api.php" \
  -d "action=update&table=golf_price&period=workday&price_category=general_bay&identity=hourly&new_price=150"
```

### Using JavaScript Fetch:
```javascript
// Get a price
fetch('./price_api.php?action=get&table=golf_price&period=workday&price_category=general_bay&identity=hourly')
    .then(r => r.json())
    .then(data => console.log(data));

// Update a price
const formData = new FormData();
formData.append('action', 'update');
formData.append('table', 'golf_price');
formData.append('period', 'workday');
formData.append('price_category', 'general_bay');
formData.append('identity', 'hourly');
formData.append('new_price', '150');

fetch('./price_api.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => console.log(data));
```

## Browser Compatibility

The system requires:
- ES6 JavaScript support (modern browsers)
- Fetch API support
- CSS3 support for styling

Supported browsers:
- Chrome 51+
- Firefox 52+
- Safari 10.1+
- Edge 15+

## Troubleshooting

### Issue: "User not authenticated" error
**Solution:** Make sure user is logged in via `/configuration-administraion.php` before accessing the AJAX API.

### Issue: "Invalid table name" error
**Solution:** Only `golf_price` and `golf_price_2` are valid table names.

### Issue: Prices showing "-"
**Solution:** This indicates no price exists for the specified combination of parameters. Verify the parameters are correct.

### Issue: Prices not loading
**Solution:** 
1. Check browser console for errors (F12 → Console)
2. Verify `price_manager.js` is loaded
3. Check that `price_api.php` is accessible
4. Verify user session is active

## Performance Considerations

- Prices are loaded individually via AJAX (ideal for small sets)
- For loading many prices at once, consider using `get_prices_list` action
- Browser caches can be leveraged by adding cache headers

## Future Enhancements

Possible improvements:
- Batch price loading for multiple cells at once
- Undo/Redo functionality
- Price history tracking
- Bulk update operations
- Export price list functionality
