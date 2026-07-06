<?php

namespace App\Http\Controllers;

use App\Queries\ProjectCalculationDataTable;
use Illuminate\Http\Request;
use App\Repositories\ProjectCalculationRepository;
use Yajra\DataTables\DataTables;
use App\Http\Requests\ProjectCalculationRequest;
use App\Models\ProjectsCalculation;
use App\Http\Requests\UpdateProjectCalculationRequest;

class ProjectCalculationController extends AppBaseController
{
    private $projectCalculationRepository;

    public function __construct(ProjectCalculationRepository $projectCalculationRepo)
    {
        $this->projectCalculationRepository = $projectCalculationRepo;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new ProjectCalculationDataTable())->get($request->all()))->make(true);
        }

        return view('project_calculations.index');
    }

    public function create()
    {
        return view('project_calculations.create');
    }

    // public function store(ProjectCalculationRequest $request)
    // {
    //     $input = $request->all();

    //     try {
    //         $project = $this->projectCalculationRepository->create($input);

    //         return $this->sendResponse($project, 'Project Calculation saved successfully');
    //     } catch (\Exception $e) {
    //         return $this->sendError('Error saving Project Calculation: ' . $e->getMessage());
    //     }
    // }
    public function store(ProjectCalculationRequest $request)
    {
        $input = $request->all();

        try {
            $project = $this->projectCalculationRepository->create($input);

            return redirect()
                ->route('project-calculations.index')
                ->with('success', 'Project Calculation saved successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error saving Project Calculation: ' . $e->getMessage()]);
        }
    }


    public function show(ProjectsCalculation $projectCalculation)
    {
        $projectCalculation->load(['values', 'expenses']);
        return view('project_calculations.show', compact('projectCalculation'));
    }

    public function edit(ProjectsCalculation $projectCalculation)
    {
        $projectCalculation->load(['values', 'expenses', 'partners']);
        return view('project_calculations.edit', compact('projectCalculation'));
    }

    // public function update(ProjectsCalculation $projectCalculation, UpdateProjectCalculationRequest $request)
    // {
    //     $input = $request->all();

    //     try {
    //         $project = $this->projectCalculationRepository->update($input, $projectCalculation->id);

    //         return $this->sendResponse($project, 'Project Calculation updated successfully');
    //     } catch (\Exception $e) {
    //         return $this->sendError('Error updating Project Calculation: ' . $e->getMessage());
    //     }
    // }

    public function update(ProjectsCalculation $projectCalculation, UpdateProjectCalculationRequest $request)
    {
        $input = $request->all();

        try {
            $this->projectCalculationRepository->update($input, $projectCalculation->id);

            return redirect()
                ->route('project-calculations.index')
                ->with('success', 'Project Calculation updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error updating Project Calculation: ' . $e->getMessage()]);
        }
    }


    public function destroy(ProjectsCalculation $projectCalculation)
    {
        try {
            $projectCalculation->delete();
            return $this->sendSuccess('Project Calculation deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error deleting Project Calculation');
        }
    }
}
