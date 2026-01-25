<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    // =========================
    // INDEX - LIST MEDICINES
    // =========================
    public function index(Request $request)
    {
        $query = Medicine::query();

        // Search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Dosage form filter
        if ($request->filled('dosage_form') && $request->dosage_form !== 'all') {
            $query->where('dosage_form', $request->dosage_form);
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'brand_name');
        $sortOrder = $request->get('sort_order', 'asc');

        $medicines = $query->orderBy($sortBy, $sortOrder)->paginate(8);

        return view('backend.medicines.index', [
            'medicines' => $medicines,
            'dosageForms' => Medicine::dosageForms(),
            'categories' => Medicine::medicineCategories(),
            'totalMedicines' => Medicine::count(),
            'activeMedicines' => Medicine::active()->count(),
            'injectionsCount' => Medicine::where('dosage_form', 'injection')->count(),
            'tabletsCount' => Medicine::where('dosage_form', 'tablet')->count(),
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    // =========================
    // CREATE / STORE
    // =========================
    public function create()
    {
        return view('backend.medicines.create', [
            'dosageForms' => Medicine::dosageForms(),
            'units' => $this->units(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->validationRules());

        Medicine::create($request->all());

        return redirect()->route('backend.medicines.index')
            ->with('success', 'Medicine added successfully.');
    }

    // =========================
    // SHOW
    // =========================
    public function show(Medicine $medicine)
    {
        // Load related prescription items with treatment, patient & doctor
        $medicine->load('prescriptionItems.prescription.treatment.patient');

        return view('backend.medicines.show', compact('medicine'));
    }

    // =========================
    // EDIT / UPDATE
    // =========================
    public function edit(Medicine $medicine)
    {
        return view('backend.medicines.edit', [
            'medicine' => $medicine,
            'dosageForms' => Medicine::dosageForms(),
            'units' => $this->units(),
        ]);
    }

    public function update(Request $request, Medicine $medicine)
    {
        $request->validate($this->validationRules($medicine->id));

        $medicine->update($request->all());

        return redirect()->route('backend.medicines.index')
            ->with('success', 'Medicine updated successfully.');
    }

    // =========================
    // DESTROY
    // =========================
    public function destroy(Medicine $medicine)
    {
        if ($medicine->prescriptionItems()->exists()) {
            return redirect()->route('backend.medicines.index')
                ->with('error', 'Cannot delete medicine. It is being used in prescriptions.');
        }

        $medicine->delete();

        return redirect()->route('backend.medicines.index')
            ->with('success', 'Medicine deleted successfully.');
    }

    // =========================
    // AUTOCOMPLETE API
    // =========================
    public function autocomplete(Request $request)
    {
        $query = $request->get('query', '');

        $results = Medicine::active()
            ->where(function ($q) use ($query) {
                $q->where('medicine_code', 'like', "%{$query}%")
                    ->orWhere('brand_name', 'like', "%{$query}%")
                    ->orWhere('generic_name', 'like', "%{$query}%");
            })
            ->limit(15)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'value' => "{$m->brand_name} ({$m->generic_name}) {$m->strength}",
                'code' => $m->medicine_code,
                'brand_name' => $m->brand_name,
                'generic_name' => $m->generic_name,
                'strength' => $m->strength,
                'dosage_form' => $m->dosage_form,
                'unit' => $m->unit,
            ]);

        return response()->json($results);
    }

    // =========================
    // QUICK ADD API
    // =========================
    public function quickAdd(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|max:100',
            'generic_name' => 'required|string|max:100',
            'strength' => 'nullable|string|max:50',
            'dosage_form' => 'required|in:' . implode(',', array_keys(Medicine::dosageForms())),
            'unit' => 'required|string|max:20',
        ]);

        $medicine = Medicine::create([
            'medicine_code' => $this->generateCode($request->generic_name, $request->strength),
            'brand_name' => $request->brand_name,
            'generic_name' => $request->generic_name,
            'strength' => $request->strength,
            'dosage_form' => $request->dosage_form,
            'unit' => $request->unit,
            'manufacturer' => $request->manufacturer,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'id' => $medicine->id,
            'code' => $medicine->medicine_code,
            'brand_name' => $medicine->brand_name,
            'generic_name' => $medicine->generic_name,
            'full_name' => $medicine->full_name,
        ]);
    }

    // =========================
    // EXPORT CSV
    // =========================
    public function export()
    {
        $medicines = Medicine::all();

        return response()->streamDownload(function () use ($medicines) {
            $file = fopen('php://output', 'w');

            // Header row
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

            // Data rows
            foreach ($medicines as $m) {
                fputcsv($file, [
                    $m->medicine_code,
                    $m->brand_name,
                    $m->generic_name,
                    $m->strength,
                    $m->dosage_form_name,
                    $m->unit,
                    $m->manufacturer,
                    $m->category_name,
                    ucfirst($m->status),
                ]);
            }

            fclose($file);
        }, 'medicines_' . date('Y-m-d') . '.csv');
    }

    // =========================
    // IMPORT CSV
    // =========================
    public function import()
    {
        return view('backend.medicines.import');
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $rows = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));
        $header = array_shift($rows);
        $imported = 0;

        foreach ($rows as $row) {
            if (count($row) !== count($header)) continue;
            $data = array_combine($header, $row);

            if (empty($data['medicine_code'])) {
                $data['medicine_code'] = $this->generateCode($data['generic_name'], $data['strength'] ?? '');
            }

            Medicine::updateOrCreate(
                ['medicine_code' => $data['medicine_code']],
                [
                    'brand_name' => $data['brand_name'],
                    'generic_name' => $data['generic_name'],
                    'strength' => $data['strength'] ?? null,
                    'dosage_form' => $data['dosage_form'] ?? 'tablet',
                    'unit' => $data['unit'] ?? 'strip',
                    'manufacturer' => $data['manufacturer'] ?? null,
                    'status' => $data['status'] ?? 'active',
                ]
            );

            $imported++;
        }

        return redirect()->route('backend.medicines.index')
            ->with('success', "Imported {$imported} medicines successfully.");
    }

    // =========================
    // HELPER METHODS
    // =========================

    // Validation rules, reusable for store/update
    private function validationRules($ignoreId = null): array
    {
        $uniqueCodeRule = 'unique:medicines,medicine_code' . ($ignoreId ? ',' . $ignoreId : '');

        return [
            'medicine_code' => "required|string|max:20|{$uniqueCodeRule}",
            'brand_name' => 'required|string|max:100',
            'generic_name' => 'required|string|max:100',
            'strength' => 'nullable|string|max:50',
            'dosage_form' => 'required|in:' . implode(',', array_keys(Medicine::dosageForms())),
            'unit' => 'required|string|max:20',
            'manufacturer' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,discontinued',
        ];
    }

    // Medicine units list
    private function units(): array
    {
        return [
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
            'pcs' => 'Pieces',
        ];
    }

    // Generate unique medicine code
    private function generateCode(string $genericName, string $strength = ''): string
    {
        $prefix = strtoupper(substr($genericName, 0, 3));
        $strengthPart = preg_replace('/[^0-9]/', '', $strength);
        $code = $prefix . '-' . $strengthPart;

        $counter = 1;
        while (Medicine::where('medicine_code', $code)->exists()) {
            $code = $prefix . '-' . $strengthPart . '-' . $counter;
            $counter++;
        }

        return $code;
    }
}
