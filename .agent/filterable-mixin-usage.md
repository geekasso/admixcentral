# Filterable List Mixin - Usage Guide

## Overview
The `filterableMixin` is a reusable Alpine.js component that provides:
- **URL-persisted filtering** (search, status, customer filters)
- **Automatic filter state management**
- **Filtered item counting**
- **Real-time status updates** via event listening

Located in: `resources/views/layouts/app.blade.php`

## Basic Usage

### 1. In your Alpine component:

```javascript
Alpine.data('myComponent', (items) => ({
    // Spread in the filterable mixin
    ...window.filterableMixin(items, 'my-status-event'),
    
    // Add your custom properties
    myCustomProp: 'value',
    
    init() {
        // Initialize the filterable functionality
        this.initFilterable();
        
        // Add your custom initialization
        // ...
    }
}))
```

### 2. In your Blade view:

```blade
<div x-data="myComponent({{ $items->map(fn($i) => [
    'id' => $i->id,
    'searchData' => strtolower($i->name . ' ' . $i->description),
    'companyName' => $i->company->name,
    'online' => $i->status === 'online'
])->values()->toJson() }})">
    
    <!-- Your filters -->
    <input x-model="search" placeholder="Search...">
    <select x-model="statusFilter">...</select>
    <select x-model="customerFilter">...</select>
    
    <!-- Counter display -->
    <span x-text="'Showing ' + filteredCount + ' of ' + items.length + ' items'"></span>
</div>
```

## Item Structure

Each item in the array should have:

```javascript
{
    id: 123,                    // Required: unique identifier
    searchData: "...",          // Required: lowercase searchable text
    companyName: "Acme Inc",    // Required: for customer filter
    online: true                // Optional: initial online status
}
```

## Provided Properties

- `search` - Search query (synced with URL `?search=...`)
- `statusFilter` - Status filter: 'all', 'online', 'offline' (synced with URL `?status=...`)
- `customerFilter` - Customer filter (synced with URL `?customer=...`)
- `items` - Array of items being filtered
- `itemStatuses` - Object tracking real-time status updates

## Provided Methods

### `initFilterable()`
Initializes the mixin functionality. **Must be called** from your component's `init()` method.

### `updateUrl(key, value)`
Updates URL query parameters. Called automatically when filters change.

## Provided Getters

### `filteredCount`
Returns the number of items matching current filters.

```html
<span x-text="filteredCount"></span>
```

## Event Listening

The mixin listens for status update events (default: `'device-updated'`):

```javascript
// Dispatch this event to update item status
window.dispatchEvent(new CustomEvent('device-updated', {
    detail: { id: 123, online: true }
}));
```

## Examples

### Dashboard (Firewalls)
```javascript
Alpine.data('dashboard', (initialFirewalls) => ({
    ...window.filterableMixin(initialFirewalls, 'device-updated'),
    
    offlineCount: 0,
    showOnlineBadge: false,

    init() {
        this.initFilterable();
        setTimeout(() => this.showOnlineBadge = true, 2500);
        // ...
    }
}))
```

### Firewalls Index
```javascript
x-data="{
    ...window.filterableMixin([...items...], 'firewall-updated'),
    
    // Page-specific properties
    deleteModalOpen: false,
    
    init() {
        this.initFilterable();
    }
}"
```

## Benefits

✅ **DRY Principle** - Write filtering logic once, use everywhere
✅ **URL Persistence** - Filters survive page refreshes
✅ **Shareable Links** - URLs include filter state
✅ **Consistent UX** - Same behavior across all pages
✅ **Easy to Extend** - Add page-specific features alongside mixin
