<?php

namespace App\Http\Livewire;

use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Invoices extends SearchableComponent
{
    public $statusFilter = '';
    public $customer = '';
    public $branchFilterID = '';
    public $monthFilter = '';
    public $startDateFilter = '';
    public $endDateFilter = '';

    /**
     * @var string[]
     */
    protected $listeners = [
        'refresh' => '$refresh',
        'filterStatus',
        'filterBranch',
        'filterMonth',
        'filterCustomer',
        'filterDateRange'
    ];

    /**
     * @return string
     */
    public function model()
    {
        return Invoice::class;
    }

    /**
     * @return string[]
     */
    public function searchableFields()
    {
        return [
            'title',
            'invoice_number',
            'customer.company_name',
            'user_id',
            'branch_id',
        ];
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View
     */
    public function render()
    {
        $invoices = $this->searchInvoices();
        $invoiceRepo = app(InvoiceRepository::class);
        $statusCount = $invoiceRepo->getInvoicesStatusCount();
        $customer = $this->customer;
        $invoiceStatus = Invoice::PAYMENT_STATUS;

        return view('livewire.invoices', compact('invoices', 'statusCount', 'customer', 'invoiceStatus'));
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchInvoices()
    {
        $this->setQuery($this->getQuery()->with('customer'));

        $this->filterResults();

        // Apply month filter
        if (!empty($this->monthFilter)) {
            $date = Carbon::createFromFormat('Y-m', $this->monthFilter);

            $this->getQuery()->where(function (Builder $q) use ($date) {
                $q->whereYear('invoice_date', $date->year)
                    ->whereMonth('invoice_date', $date->month);
            });
        }

        // Apply other filters
        $this->getQuery()->when($this->statusFilter !== '', function (Builder $q) {
            $q->where('payment_status', $this->statusFilter);
        });

        $this->getQuery()->when($this->customer !== '', function (Builder $q) {
            $q->where('customer_id', $this->customer);
        });

        $this->getQuery()->when($this->branchFilterID !== '', function (Builder $q) {
            $q->where('branch_id', $this->branchFilterID);
        }, function (Builder $q) {
            $q->whereIn('branch_id', function ($query) {
                $query->select('branch_id')
                    ->from('users_branches')
                    ->where('user_id', auth()->id());
            });
        });

        // Apply date range filter on invoice_date
        if (!empty($this->startDateFilter)) {
            $this->getQuery()->whereDate('invoice_date', '>=', $this->startDateFilter);
        }
        if (!empty($this->endDateFilter)) {
            $this->getQuery()->whereDate('invoice_date', '<=', $this->endDateFilter);
        }

        // Draft invoices on top (sorted by DI number desc), then regular invoices (by invoice number desc)
        $this->getQuery()->reorder()
            ->orderByRaw("CASE WHEN payment_status = 0 THEN 0 ELSE 1 END ASC")
            ->orderByRaw("LENGTH(invoice_number) DESC")
            ->orderByRaw("invoice_number DESC");

        return $this->paginate(false);
    }

    /**
     * @return Builder
     */
    public function filterResults()
    {
        $searchableFields = $this->searchableFields();
        $search = $this->search;

        $this->getQuery()->when(!empty($search), function (Builder $q) use ($search, $searchableFields) {
            $this->getQuery()->where(function (Builder $q) use ($search, $searchableFields) {
                $searchString = '%' . $search . '%';
                foreach ($searchableFields as $field) {
                    if (Str::contains($field, '.')) {
                        $field = explode('.', $field);
                        $q->orWhereHas($field[0], function (Builder $query) use ($field, $searchString) {
                            $query->whereRaw("lower($field[1]) like ?", $searchString);
                        });
                    } else {
                        $q->orWhereRaw("lower($field) like ?", $searchString);
                    }
                }
            });
        });

        return $this->getQuery();
    }

    /**
     * @param  int  $id
     */
    public function filterStatus($id)
    {
        $this->statusFilter = $id;
        $this->resetPage();
    }

    /**
     * @param  string  $month
     */
    public function filterMonth($month)
    {
        $this->monthFilter = $month;
        $this->resetPage();
    }

    /**
     * @param  int  $customerId
     */
    public function filterCustomer($customerId)
    {
        $this->customer = $customerId;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function filterBranch($id)
    {
        $this->branchFilterID = $id;
        $this->resetPage();
    }

    /**
     * @param  string  $startDate
     * @param  string  $endDate
     */
    public function filterDateRange($startDate, $endDate)
    {
        $this->startDateFilter = $startDate;
        $this->endDateFilter = $endDate;
        $this->resetPage();
    }
}
