<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::query();

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        // Dosage form filter
        if ($request->has('dosage_form') && $request->dosage_form != 'all') {
            $query->where('dosage_form', $request->dosage_form);
        }

        // Status filter
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'brand_name');
        $sortOrder = $request->get('sort_order', 'asc');

        $medicines = $query->orderBy($sortBy, $sortOrder)->paginate(8);
        $dosageForms = Medicine::dosageForms();
        $categories = Medicine::medicineCategories();

        // Statistics
        $totalMedicines = Medicine::count();
        $activeMedicines = Medicine::active()->count();
        $injectionsCount = Medicine::where('dosage_form', 'injection')->count();
        $tabletsCount = Medicine::where('dosage_form', 'tablet')->count();

        return view('backend.medicines.index', compact(
            'medicines',
            'dosageForms',
            'categories',
            'totalMedicines',
            'activeMedicines',
            'injectionsCount',
            'tabletsCount',
            'sortBy',
            'sortOrder'
        ));
    }

    public function create()
    {
        $dosageForms = Medicine::dosageForms();
        $units = [
            'strip' => 'Strip',
            'tablet' => 'Tablet',
            'capsule' => 'Capsule',
            'bottle' => 'Bottle',
            'tube' => 'Tube',
            'ampoule' => 'Ampoule',
            'cartridge' => 'Cartridge',
            'syringe' => 'Syringe',
            'vial' => 'Vial',
            'pack' => 'Pack',
            'box' => 'Box',
            'pcs' => 'Pieces'
        ];

        return view('backend.medicines.create', compact('dosageForms', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_code' => 'required|string|max:20|unique:medicines',
            'brand_name' => 'required|string|max:100',
            'generic_name' => 'required|string|max:100',
            'strength' => 'nullable|string|max:50',
            'dosage_form' => 'required|in:' . implode(',', array_keys(Medicine::dosageForms())),
            'unit' => 'required|string|max:20',
            'manufacturer' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,discontinued'
        ]);

        Medicine::create($request->all());

        return redirect()->route('backend.medicines.index')
            ->with('success', 'Medicine added successfully.');
    }

    public function show(Medicine $medicine)
    {
        // Only load prescriptionItems if table/column exist
        if (
            class_exists(\App\Models\PrescriptionItem::class) &&
            \Schema::hasTable('prescription_items') &&
            \Schema::hasColumn('prescription_items', 'medicine_id')
        ) {
            $medicine->load(['prescriptionItems' => function ($q) {
                try {
                    $q->with([
                        'prescription.treatment.patient',
                        'prescription.treatment.doctor.user'
                    ])
                        ->latest()
                        ->limit(10);
                } catch (\Exception $e) {
                    // ignore errors and fallback to empty
                }
            }]);
        }

        return view('backend.medicines.show', compact('medicine'));
    }

    public function edit(Medicine $medicine)
    {
        $dosageForms = Medicine::dosageForms();
        $units = [
            'strip' => 'Strip',
            'tablet' => 'Tablet',
            'capsule' => 'Capsule',
            'bottle' => 'Bottle',
            'tube' => 'Tube',
            'ampoule' => 'Ampoule',
            'cartridge' => 'Cartridge',
            'syringe' => 'Syringe',
            'vial' => 'Vial',
            'pack' => 'Pack',
            'box' => 'Box',
            'pcs' => 'Pieces'
        ];

        return view('backend.medicines.edit', compact('medicine', 'dosageForms', 'units'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $request->validate([
            'medicine_code' => 'required|string|max:20|unique:medicines,medicine_code,' . $medicine->id,
            'brand_name' => 'required|string|max:100',
            'generic_name' => 'required|string|max:100',
            'strength' => 'nullable|string|max:50',
            'dosage_form' => 'required|in:' . implode(',', array_keys(Medicine::dosageForms())),
            'unit' => 'required|string|max:20',
            'manufacturer' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,discontinued'
        ]);

        $medicine->update($request->all());

        return redirect()->route('backend.medicines.index')
            ->with('success', 'Medicine updated successfully.');
    }

    public function destroy(Medicine $medicine)
    {
        // Check if medicine is being used in prescriptions
        if ($medicine->prescriptionItems()->exists()) {
            return redirect()->route('backend.medicines.index')
                ->with('error', 'Cannot delete medicine. It is being used in prescriptions.');
        }

        $medicine->delete();
        return redirect()->route('backend.medicines.index')
            ->with('success', 'Medicine deleted successfully.');
    }

    // API endpoint for autocomplete
    public function autocomplete(Request $request)
    {
        $query = $request->get('query');

        $medicines = Medicine::active()
            ->where(function ($q) use ($query) {
                $q->where('medicine_code', 'like', "%{$query}%")
                    ->orWhere('brand_name', 'like', "%{$query}%")
                    ->orWhere('generic_name', 'like', "%{$query}%");
            })
            ->limit(15)
            ->get()
            ->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'value' => $medicine->brand_name . ' (' . $medicine->generic_name . ') ' . $medicine->strength,
                    'code' => $medicine->medicine_code,
                    'brand_name' => $medicine->brand_name,
                    'generic_name' => $medicine->generic_name,
                    'strength' => $medicine->strength,
                    'dosage_form' => $medicine->dosage_form,
                    'unit' => $medicine->unit
                ];
            });

        return response()->json($medicines);
    }

    // Quick add modal endpoint
    public function quickAdd(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|max:100',
            'generic_name' => 'required|string|max:100',
            'strength' => 'nullable|string|max:50',
            'dosage_form' => 'required|in:' . implode(',', array_keys(Medicine::dosageForms())),
            'unit' => 'required|string|max:20'
        ]);

        // Generate medicine code
        $prefix = strtoupper(substr($request->generic_name, 0, 3));
        $strengthPart = preg_replace('/[^0-9]/', '', $request->strength);
        $code = $prefix . '-' . $strengthPart;

        $counter = 1;
        while (Medicine::where('medicine_code', $code)->exists()) {
            $code = $prefix . '-' . $strengthPart . '-' . $counter;
            $counter++;
        }

        $medicine = Medicine::create([
            'medicine_code' => $code,
            'brand_name' => $request->brand_name,
            'generic_name' => $request->generic_name,
            'strength' => $request->strength,
            'dosage_form' => $request->dosage_form,
            'unit' => $request->unit,
            'manufacturer' => $request->manufacturer,
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'id' => $medicine->id,
            'code' => $medicine->medicine_code,
            'brand_name' => $medicine->brand_name,
            'generic_name' => $medicine->generic_name,
            'full_name' => $medicine->full_name
        ]);
    }

    // Export to CSV
    public function export(Request $request)
    {
        $medicines = Medicine::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="medicines_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($medicines) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'Medicine Code',
                'Brand Name',
                'Generic Name',
                'Strength',
                'Dosage Form',
                'Unit',
                'Manufacturer',
                'Category',
                'Status'
            ]);

            // Data
            foreach ($medicines as $medicine) {
                fputcsv($file, [
                    $medicine->medicine_code,
                    $medicine->brand_name,
                    $medicine->generic_name,
                    $medicine->strength,
                    $medicine->dosage_form_name,
                    $medicine->unit,
                    $medicine->manufacturer,
                    $medicine->category_name,
                    ucfirst($medicine->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Import from CSV
    public function import()
    {
        return view('backend.medicines.import');
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        // Process CSV import
        $path = $request->file('csv_file')->getRealPath();
        $data = array_map('str_getcsv', file($path));

        $header = array_shift($data);
        $imported = 0;
        $errors = [];

        foreach ($data as $row) {
            if (count($row) != count($header)) continue;

            $rowData = array_combine($header, $row);

            try {
                // Generate code if not provided
                if (empty($rowData['medicine_code'])) {
                    $prefix = strtoupper(substr($rowData['generic_name'], 0, 3));
                    $strengthPart = preg_replace('/[^0-9]/', '', $rowData['strength'] ?? '');
                    $code = $prefix . '-' . $strengthPart;

                    $counter = 1;
                    while (Medicine::where('medicine_code', $code)->exists()) {
                        $code = $prefix . '-' . $strengthPart . '-' . $counter;
                        $counter++;
                    }
                    $rowData['medicine_code'] = $code;
                }

                Medicine::updateOrCreate(
                    ['medicine_code' => $rowData['medicine_code']],
                    [
                        'brand_name' => $rowData['brand_name'],
                        'generic_name' => $rowData['generic_name'],
                        'strength' => $rowData['strength'] ?? null,
                        'dosage_form' => $rowData['dosage_form'] ?? 'tablet',
                        'unit' => $rowData['unit'] ?? 'strip',
                        'manufacturer' => $rowData['manufacturer'] ?? null,
                        'status' => $rowData['status'] ?? 'active'
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row error: " . $e->getMessage();
            }
        }

        return redirect()->route('backend.medicines.index')
            ->with('success', "Imported {$imported} medicines successfully.")
            ->with('errors', $errors);
    }

    // Generate medicine code
    public function generateCode(Request $request)
    {
        $genericName = $request->get('generic_name');
        $strength = $request->get('strength');

        if (!$genericName) {
            return response()->json(['code' => '']);
        }

        $prefix = strtoupper(substr($genericName, 0, 3));
        $strengthPart = preg_replace('/[^0-9]/', '', $strength);
        $code = $prefix . '-' . $strengthPart;

        $counter = 1;
        while (Medicine::where('medicine_code', $code)->exists()) {
            $code = $prefix . '-' . $strengthPart . '-' . $counter;
            $counter++;
        }

        return response()->json(['code' => $code]);
    }
}
