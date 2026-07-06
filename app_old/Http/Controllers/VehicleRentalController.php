<?php

namespace App\Http\Controllers;

use App\Queries\VehicleRentalDataTable;
use App\Models\Asset;
use App\Repositories\VehicleRentalRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\VehicleRentalRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateVehicleRentalRequest;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\VehicleRental;
use App\Models\DocumentNextNumber;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class VehicleRentalController extends AppBaseController
{
    /**
     * @var VehicleRentalRepository
     *
     */
    private $vehicleRentalRepository;
    public function __construct(VehicleRentalRepository $vehicleRentalRepo)
    {
        $this->vehicleRentalRepository = $vehicleRentalRepo;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new VehicleRentalDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();

        $accounts = $this->vehicleRentalRepository->getAccounts();

        return view('vehicle-rentals.index', compact('usersBranches', 'accounts'));
    }


    public function create()
    {
        $types = $this->vehicleRentalRepository->getTypes();
        $nextNumber = DocumentNextNumber::getNextNumber('vehicle_rental');

        return view('vehicle-rentals.create', compact('types', 'nextNumber'));
    }

    // public function store(VehicleRentalRequest $request)
    // {

    //     try {

    //         $rental = $this->vehicleRentalRepository->saveRental($request->all());

    //         $data = [];
    //         $input_installment_no = max(1, $rental->installment_no);
    //         for ($i = 1; $i <= $input_installment_no; $i++) {
    //             $data[] = [
    //                 'vehicle_rental_id'  => $rental->id,
    //                 'installment_amount' => $rental->installment_amount,
    //                 'created_at'         => date('Y-m-d H:i:s'),
    //                 'updated_at'         => date('Y-m-d H:i:s'),
    //                 'paid_amount'        => 0,
    //             ];
    //         }

    //         DB::table('vehicle_rental_details')->insert($data);

    //         activity()->causedBy(getLoggedInUser())
    //             ->performedOn($rental)
    //             ->useLog('Vehicle Rental created.')
    //             ->log($rental->name . ' Vehicle Rental.');
    //         DocumentNextNumber::updateNumber('vehicle_rental');
    //         Flash::success(__('messages.vehicle-rentals.saved'));
    //         return $this->sendResponse($rental, __('messages.vehicle-rentals.saved'));
    //     } catch (Throwable $e) {
    //         throw $e;
    //     }
    // }

    public function store(VehicleRentalRequest $request)
    {
        try {
            $rental = $this->vehicleRentalRepository->saveRental($request->all());

            $data = [];
            $input_installment_no = max(1, $rental->installment_no);

            $startDate = Carbon::parse($rental->agreement_date);
            $currentDate = $startDate->copy();
            $plan = $rental->type; // Daily, Monthly, etc.

            for ($i = 1; $i <= $input_installment_no; $i++) {
                $data[] = [
                    'vehicle_rental_id'  => $rental->id,
                    'installment_amount' => $rental->installment_amount,
                    'installment_date'   => $currentDate->format('Y-m-d'),
                    'created_at'         => now(),
                    'updated_at'         => now(),
                    'paid_amount'        => 0,
                ];

                // Move to next installment date based on plan
                switch ($plan) {
                    case 'Daily':
                        $currentDate->addDay();
                        break;
                    case 'Weekly':
                        $currentDate->addWeek();
                        break;
                    case 'Monthly':
                        $currentDate->addMonth();
                        break;
                    case 'Yearly':
                        $currentDate->addYear();
                        break;
                    case 'Twicly': // twice per month
                        $currentDate->addDays(15);
                        break;
                    case 'Quarterly':
                        $currentDate->addMonths(3);
                        break;
                    case 'Half Year':
                        $currentDate->addMonths(6);
                        break;
                    default:
                        $currentDate->addMonth();
                }
            }

            DB::table('vehicle_rental_details')->insert($data);

            activity()->causedBy(getLoggedInUser())
                ->performedOn($rental)
                ->useLog('Vehicle Rental created.')
                ->log($rental->name . ' Vehicle Rental.');

            DocumentNextNumber::updateNumber('vehicle_rental');
            Flash::success(__('messages.vehicle-rentals.saved'));
            return $this->sendResponse($rental, __('messages.vehicle-rentals.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(VehicleRental $rental)
    {

        DB::table('vehicle_rental_details')->where('vehicle_rental_id', $rental->id)->delete();

        $rental->delete();

        activity()->performedOn($rental)->causedBy(getLoggedInUser())
            ->useLog('Vehicle Rental.')->log($rental->name . 'Vehicle Rental');
        return $this->sendSuccess('Asset deleted successfully.');
    }

    public function edit(VehicleRental $rental)
    {
        $types = $this->vehicleRentalRepository->getTypes();

        $paid_amount = DB::table('vehicle_rental_details')->where('vehicle_rental_id', $rental->id)->sum('paid_amount');
        if ($paid_amount > 0) {
            Flash::error('Already Paid so Not Editable.');
            return redirect()->back();
        }

        return view('vehicle-rentals.edit', compact(['rental', 'types']));
    }

    // public function update(VehicleRental $rental, UpdateVehicleRentalRequest $updateVehicleRentalRequest)
    // {
    //     $input = $updateVehicleRentalRequest->all();

    //     $updateStatus = $this->vehicleRentalRepository->updateRental($input, $rental->id);



    //     DB::table('vehicle_rental_details')->where('vehicle_rental_id', $rental->id)->delete();

    //     $data = [];


    //     $input_installment_no = max(1, $updateStatus->installment_no);


    //     for ($i = 1; $i <= $input_installment_no; $i++) {
    //         $data[] = [
    //             'vehicle_rental_id'  => $rental->id,
    //             'installment_amount' => $updateStatus->installment_amount,
    //             'created_at'         => date('Y-m-d H:i:s'),
    //             'updated_at'         => date('Y-m-d H:i:s'),
    //             'branch_id'          => $rental->branch_id,
    //             'paid_amount'        => 0,
    //         ];
    //     }

    //     DB::table('vehicle_rental_details')->insert($data);


    //     activity()->causedBy(getLoggedInUser())
    //         ->performedOn($updateStatus)
    //         ->useLog('Vehicle Rental.')
    //         ->log($updateStatus->name . ' Vehicle Rental.');
    //     Flash::success(__('messages.vehicle-rentals.saved'));
    //     return $this->sendResponse($updateStatus, __('messages.vehicle-rentals.saved'));
    // }

    public function update(VehicleRental $rental, UpdateVehicleRentalRequest $updateVehicleRentalRequest)
    {
        $input = $updateVehicleRentalRequest->all();
        $updateStatus = $this->vehicleRentalRepository->updateRental($input, $rental->id);

        // Delete old details
        DB::table('vehicle_rental_details')->where('vehicle_rental_id', $rental->id)->delete();

        $data = [];
        $input_installment_no = max(1, $updateStatus->installment_no);

        $startDate = Carbon::parse($updateStatus->agreement_date);
        $currentDate = $startDate->copy();
        $plan = $updateStatus->type;

        for ($i = 1; $i <= $input_installment_no; $i++) {
            $data[] = [
                'vehicle_rental_id'  => $rental->id,
                'installment_amount' => $updateStatus->installment_amount,
                'installment_date'   => $currentDate->format('Y-m-d'),
                'created_at'         => now(),
                'updated_at'         => now(),
                'branch_id'          => $rental->branch_id,
                'paid_amount'        => 0,
            ];

            // Move to next installment date based on plan
            switch ($plan) {
                case 'Daily':
                    $currentDate->addDay();
                    break;
                case 'Weekly':
                    $currentDate->addWeek();
                    break;
                case 'Monthly':
                    $currentDate->addMonth();
                    break;
                case 'Yearly':
                    $currentDate->addYear();
                    break;
                case 'Twicly':
                    $currentDate->addDays(15);
                    break;
                case 'Quarterly':
                    $currentDate->addMonths(3);
                    break;
                case 'Half Year':
                    $currentDate->addMonths(6);
                    break;
                default:
                    $currentDate->addMonth();
            }
        }

        DB::table('vehicle_rental_details')->insert($data);

        activity()->causedBy(getLoggedInUser())
            ->performedOn($updateStatus)
            ->useLog('Vehicle Rental.')
            ->log($updateStatus->name . ' Vehicle Rental.');

        Flash::success(__('messages.vehicle-rentals.saved'));
        return $this->sendResponse($updateStatus, __('messages.vehicle-rentals.saved'));
    }
    public function view(VehicleRental $rental)
    {
        $details_rental = DB::table('vehicle_rental_details')->where('vehicle_rental_id', $rental->id)->get();
        $usersBranches = $this->getUsersBranches();
        $accounts = $this->vehicleRentalRepository->getAccounts();

        return view('vehicle-rentals.view', compact(['rental', 'details_rental', 'usersBranches', 'accounts']));
    }
    public function updatePayment($rental_details_id, Request $request)
    {
        $validatedData = $request->validate([
            'paid_amount' => 'required|numeric',
            'branch_id' => 'required|numeric',
            'account_id' => 'required|numeric',
        ]);



        $rental_details_row = DB::table('vehicle_rental_details')->where('id', $request->id)->first();
        $rental_row = DB::table('vehicle_rentals')->where('id', $request->rental_id)->first();

        $paid_date = date("Y-m-d");

        $data_update = [
            'updated_at'         => date('Y-m-d H:i:s'),
            'account_id'          => $request->rental_id,
            'branch_id'          => $request->branch_id,
            'paid_amount'        => $request->paid_amount,
            'paid_date'        => $paid_date,
        ];

        DB::table('vehicle_rental_details')->where('id', $request->id)->update($data_update);

        $all_paid_amount = DB::table('vehicle_rental_details')->where('vehicle_rental_id', $request->rental_id)->sum('paid_amount');

        DB::table('vehicle_rentals')->where('id', $request->rental_id)->update([
            'paid_amount' => $all_paid_amount,
        ]);


        return $this->sendResponse(null, __('messages.vehicle-rentals.saved'));
    }
}
