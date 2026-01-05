# Laravel MVC Flow: How Controller Passes Data to Views

## Overview of the Flow

```
User Request → Route → Controller → View (Blade Template) → Response
```

## Step-by-Step Explanation

### 1. **User Makes a Request**
When a user visits: `http://yourapp.com/coordinator/monitor-interns`

---

### 2. **Route Matches the URL** (`routes/web.php`)

```php
Route::get('/coordinator/monitor-interns', [CoordinatorController::class, 'monitorInterns'])
    ->name('coordinator.monitor-interns');
```

**What happens:**
- Laravel matches `/coordinator/monitor-interns` URL
- Calls the `monitorInterns()` method in `CoordinatorController`

---

### 3. **Controller Processes Data** (`app/Http/Controllers/CoordinatorController.php`)

```php
public function monitorInterns()
{
    // Step 1: Get data from database
    $interns = User::where('user_role', 'Intern')->get();
    $totalInterns = 10;
    $activeInterns = 8;
    $needsSupport = 2;
    $avgProgress = 75;
    
    // Step 2: Pass data to view using compact()
    return view('coordinator.monitor-interns', compact('interns', 'totalInterns', 'activeInterns', 'needsSupport', 'avgProgress'));
}
```

**What `compact()` does:**
- Creates an associative array from variable names
- Converts: `compact('interns', 'totalInterns')` 
- Into: `['interns' => $interns, 'totalInterns' => $totalInterns]`

**Alternative ways to pass data:**

```php
// Method 1: Using compact() - MOST COMMON
return view('coordinator.monitor-interns', compact('interns', 'totalInterns'));

// Method 2: Using array notation
return view('coordinator.monitor-interns', [
    'interns' => $interns,
    'totalInterns' => $totalInterns
]);

// Method 3: Using with() method
return view('coordinator.monitor-interns')
    ->with('interns', $interns)
    ->with('totalInterns', $totalInterns);
```

---

### 4. **View Receives Variables** (`resources/views/coordinator/monitor-interns.blade.php`)

The variables from the controller are now **automatically available** in the Blade template.

```blade
{{-- These variables come from the controller --}}
<h3>{{ $totalInterns }}</h3>
<h3>{{ $activeInterns }}</h3>
<h3>{{ $needsSupport }}</h3>
<h3>{{ $avgProgress }}%</h3>

@foreach($interns as $intern)
    <p>{{ $intern['full_name'] }}</p>
@endforeach
```

---

## Real Example from Your Code

### Controller (`CoordinatorController.php`)
```php
public function monitorInterns()
{
    $user = Auth::user();
    
    // Fetch interns from database
    $interns = User::where('user_role', 'Intern')
        ->where('coordinator_id', $user->coordinator_id)
        ->get()
        ->map(function ($intern) {
            return [
                'id' => $intern->user_id,
                'full_name' => $intern->first_name . ' ' . $intern->last_name,
                'email' => $intern->email,
                // ... more data
            ];
        });
    
    // Calculate statistics
    $totalInterns = $interns->count();
    $activeInterns = $interns->where('status_text', 'Active')->count();
    $needsSupport = $interns->where('status_text', 'Needs Support')->count();
    $avgProgress = $totalInterns > 0 ? round($interns->avg('progress_percentage')) : 0;
    
    // Pass 5 variables to the view
    return view('coordinator.monitor-interns', compact(
        'interns',       // Collection of intern data
        'totalInterns',  // Integer
        'activeInterns', // Integer
        'needsSupport',  // Integer
        'avgProgress'    // Integer
    ));
}
```

### View (`monitor-interns.blade.php`)
```blade
{{-- Dashboard Statistics Cards --}}
<div class="col-md-3">
    <p>Total Interns</p>
    <h3>{{ $totalInterns }}</h3>  {{-- Uses variable from controller --}}
</div>

<div class="col-md-3">
    <p>Active Interns</p>
    <h3>{{ $activeInterns }}</h3>  {{-- Uses variable from controller --}}
</div>

<div class="col-md-3">
    <p>Needs Support</p>
    <h3>{{ $needsSupport }}</h3>  {{-- Uses variable from controller --}}
</div>

<div class="col-md-3">
    <p>Avg Progress</p>
    <h3>{{ $avgProgress }}%</h3>  {{-- Uses variable from controller --}}
</div>

{{-- Loop through interns collection --}}
@if($interns->isEmpty())
    <p>No Interns Yet</p>
@else
    @foreach($interns as $intern)
        <div class="card">
            <h6>{{ $intern['full_name'] }}</h6>
            <p>{{ $intern['email'] }}</p>
            <span>{{ $intern['status_text'] }}</span>
        </div>
    @endforeach
@endif
```

---

## Key Points to Remember

1. **Controller prepares data** - Fetches from database, processes, calculates
2. **Controller returns view with data** - Uses `compact()` or array notation
3. **View receives variables automatically** - No need to import or declare them
4. **Blade displays data** - Use `{{ $variableName }}` syntax

---

## Visual Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER REQUEST                             │
│              GET /coordinator/monitor-interns                    │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                      ROUTE (web.php)                             │
│  Route::get('/coordinator/monitor-interns',                     │
│             [CoordinatorController::class, 'monitorInterns'])   │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│              CONTROLLER (CoordinatorController.php)              │
│                                                                  │
│  public function monitorInterns() {                             │
│      // 1. Fetch data from database                             │
│      $interns = User::where(...)->get();                        │
│                                                                  │
│      // 2. Process and calculate                                │
│      $totalInterns = $interns->count();                         │
│      $activeInterns = $interns->where(...)->count();            │
│                                                                  │
│      // 3. Return view with variables                           │
│      return view('coordinator.monitor-interns',                 │
│          compact('interns', 'totalInterns', 'activeInterns'));  │
│  }                                                               │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           │ Variables passed:
                           │ - $interns
                           │ - $totalInterns
                           │ - $activeInterns
                           │ - $needsSupport
                           │ - $avgProgress
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│         VIEW (resources/views/coordinator/monitor-interns       │
│                      .blade.php)                                 │
│                                                                  │
│  <h3>{{ $totalInterns }}</h3>                                   │
│  <h3>{{ $activeInterns }}</h3>                                  │
│                                                                  │
│  @foreach($interns as $intern)                                  │
│      <p>{{ $intern['full_name'] }}</p>                          │
│  @endforeach                                                    │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                     HTML RESPONSE TO USER                        │
│              (Rendered HTML page with data)                      │
└─────────────────────────────────────────────────────────────────┘
```

---

## Common Blade Syntax

```blade
{{-- Display variable --}}
{{ $variableName }}

{{-- Conditional --}}
@if($totalInterns > 0)
    <p>You have interns!</p>
@else
    <p>No interns yet</p>
@endif

{{-- Loop through array/collection --}}
@foreach($interns as $intern)
    <p>{{ $intern['name'] }}</p>
@endforeach

{{-- Check if collection is empty --}}
@if($interns->isEmpty())
    <p>No data</p>
@endif
```

---

## Why This Works?

Laravel's **View** system automatically extracts the array keys as variable names:

```php
// In Controller
compact('interns', 'totalInterns')

// Becomes this array:
[
    'interns' => $interns,
    'totalInterns' => $totalInterns
]

// Laravel extracts keys and makes them available as:
// $interns and $totalInterns in the Blade template
```

This is Laravel's **"magic"** - it automatically makes controller variables available in views!
