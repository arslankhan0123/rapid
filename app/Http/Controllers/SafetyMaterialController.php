<?php

namespace App\Http\Controllers;

use App\Queries\SafetyMaterialDataTable;
use Illuminate\Http\Request;
use App\Repositories\SafetyMaterialRepository;
use Yajra\DataTables\DataTables;
use App\Http\Requests\SafetyMaterialRequest;
use App\Models\SafetyMaterial;
use App\Http\Requests\UpdateSafetyMaterialRequest;

class SafetyMaterialController extends AppBaseController
{
    private $safetyMaterialRepository;

    public function __construct(SafetyMaterialRepository $safetyMaterialRepo)
    {
        $this->safetyMaterialRepository = $safetyMaterialRepo;
    }

    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         return DataTables::of((new SafetyMaterialDataTable())->get($request->all()))->make(true);
    //     }

    //     return view('safety_materials.index');
    // }
    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         try {
    //             // Get raw data first
    //             $query = SafetyMaterial::with(['employee'])->select('safety_materials.*');

    //             if (!empty($request->get('search')['value'])) {
    //                 $search = $request->get('search')['value'];
    //                 $query->where(function ($q) use ($search) {
    //                     $q->where('category', 'LIKE', "%$search%")
    //                         ->orWhere('amount', 'LIKE', "%$search%")
    //                         ->orWhere('duration', 'LIKE', "%$search%")
    //                         ->orWhereHas('employee', function ($q) use ($search) {
    //                             $q->where('name', 'LIKE', "%$search%");
    //                         });
    //                 });
    //             }

    //             $data = $query->orderBy('date', 'desc')->get();

    //             return DataTables::of($data)
    //                 ->addColumn('employee_name', function ($row) {
    //                     return $row->employee ? $row->employee->name : '';
    //                 })
    //                 ->editColumn('date', function ($row) {
    //                     return $row->date ? $row->date->format('Y-m-d') : '';
    //                 })
    //                 ->editColumn('next_date', function ($row) {
    //                     return $row->next_date ? $row->next_date->format('Y-m-d') : '';
    //                 })
    //                 ->editColumn('amount', function ($row) {
    //                     return (string) $row->amount;
    //                 })
    //                 ->editColumn('duration', function ($row) {
    //                     return (string) $row->duration;
    //                 })
    //                 ->editColumn('category', function ($row) {
    //                     return (string) $row->category;
    //                 })
    //                 ->make(true);
    //         } catch (\Exception $e) {
    //             \Log::error('DataTable Error: ' . $e->getMessage() . ' Line: ' . $e->getLine());
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => $e->getMessage(),
    //                 'line' => $e->getLine(),
    //                 'trace' => $e->getTraceAsString()
    //             ], 500);
    //         }
    //     }
    //     $employees = $this->safetyMaterialRepository->getEmployees();

    //     return view('safety_materials.index', compact('employees'));
    // }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = SafetyMaterial::with(['employee'])->select('safety_materials.*');

                // Search
                $searchValue = $request->get('search')['value'] ?? null;
                if (!empty($searchValue)) {
                    $query->where(function ($q) use ($searchValue) {
                        $q->where('category', 'LIKE', "%$searchValue%")
                            ->orWhere('amount', 'LIKE', "%$searchValue%")
                            ->orWhere('duration', 'LIKE', "%$searchValue%")
                            ->orWhereHas('employee', function ($q) use ($searchValue) {
                                $q->where('name', 'LIKE', "%$searchValue%");
                            });
                    });
                }

                // Filter by employee
                $employeeId = $request->get('employee_id');
                if (!empty($employeeId)) {
                    $query->where('employee_id', $employeeId);
                }

                $data = $query->orderBy('date', 'desc')->get();

                return DataTables::of($data)
                    ->addColumn('employee_name', fn($row) => $row->employee?->name ?? '')
                    ->editColumn('date', fn($row) => $row->date?->format('Y-m-d') ?? '')
                    ->editColumn('next_date', fn($row) => $row->next_date?->format('Y-m-d') ?? '')
                    ->editColumn('amount', fn($row) => (string) $row->amount)
                    ->editColumn('duration', fn($row) => (string) $row->duration)
                    ->editColumn('category', fn($row) => (string) $row->category)
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('DataTable Error: ' . $e->getMessage() . ' Line: ' . $e->getLine());
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        }

        $employees = $this->safetyMaterialRepository->getEmployees();
        return view('safety_materials.index', compact('employees'));
    }


    public function create()
    {
        $employees = $this->safetyMaterialRepository->getEmployees();
        $durationOptions = $this->safetyMaterialRepository->getDurationOptions();
        $safetyMaterial = null; // Add this

        return view('safety_materials.create', compact('employees', 'durationOptions', 'safetyMaterial'));
    }


    public function store(SafetyMaterialRequest $request)
    {
        $input = $request->all();

        try {
            $safetyMaterial = $this->safetyMaterialRepository->create($input);

            return $this->sendResponse($safetyMaterial, 'Safety Material saved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error saving Safety Material: ' . $e->getMessage());
        }
    }

    public function show(SafetyMaterial $safetyMaterial)
    {
        $safetyMaterial->load('employee');
        return view('safety_materials.show', compact('safetyMaterial'));
    }

    public function edit(SafetyMaterial $safetyMaterial)
    {
        $employees = $this->safetyMaterialRepository->getEmployees();
        $durationOptions = $this->safetyMaterialRepository->getDurationOptions();

        return view('safety_materials.edit', compact('safetyMaterial', 'employees', 'durationOptions'));
    }

    public function update(SafetyMaterial $safetyMaterial, UpdateSafetyMaterialRequest $request)
    {
        $input = $request->all();

        try {
            $safetyMaterial = $this->safetyMaterialRepository->update($input, $safetyMaterial->id);

            return $this->sendResponse($safetyMaterial, 'Safety Material updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error updating Safety Material: ' . $e->getMessage());
        }
    }

    public function destroy(SafetyMaterial $safetyMaterial)
    {
        try {
            $safetyMaterial->delete();
            return $this->sendSuccess('Safety Material deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error deleting Safety Material');
        }
    }

    public function calculateNextDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'duration' => 'required|in:1month,2m,3m,6m,9m,1year'
        ]);

        $safetyMaterial = new SafetyMaterial();
        $safetyMaterial->date = $request->date;
        $safetyMaterial->duration = $request->duration;

        return response()->json([
            'next_date' => $safetyMaterial->calculateNextDate()->format('Y-m-d')
        ]);
    }
}
