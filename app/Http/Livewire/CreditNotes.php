<?php

namespace App\Http\Livewire;

use App\Models\CreditNote;
use App\Repositories\CreditNoteRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CreditNotes extends SearchableComponent
{
    public $filterStatus = '';

    public $customer = '';
    public $branchFilterID = '';


    /**
     * @var string[]
     */
    protected $listeners = [
        'refresh' => '$refresh',
        'statusFilter',
        'filterBranch'
    ];

    /**
     * @return string
     */
    public function model()
    {
        return CreditNote::class;
    }

    /**
     * @return string[]
     */
    public function searchableFields()
    {
        return [
            'title',
            'credit_note_number',
            'customer.company_name',
            'user_id',
            'branch_id',
        ];
    }

    /**
     * @return Application|Factory
     */
    public function render()
    {
        $creditNotes = $this->searchCreditNotes();
        $creditNoteRepo = app(CreditNoteRepository::class);
        $statusCount = $creditNoteRepo->getStatusCount();
        $customer = $this->customer;
        $creditNoteStatus = CreditNote::PAYMENT_STATUS;

        return view('livewire.credit-notes', compact('creditNotes', 'statusCount', 'customer', 'creditNoteStatus'));
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchCreditNotes()
    {
        $this->setQuery($this->getQuery()->with('customer'));

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        $this->getQuery()->when($this->filterStatus != '', function (Builder $q) {
            $q->where('payment_status', $this->filterStatus);
        });

        $this->getQuery()->when($this->customer !== '', function (Builder $q) {
            $q->where('customer_id', $this->customer);
        });

        $this->getQuery()->when($this->branchFilterID !== '', function (Builder $q) {
            // If branchFilterID is not empty, filter by branch_id
            $q->where('branch_id', $this->branchFilterID);
        }, function (Builder $q) {
            // If branchFilterID is empty, filter by user's branch_id
            $q->whereIn('branch_id', function ($query) {
                $query->select('branch_id')
                    ->from('users_branches')
                    ->where('user_id', auth()->id()); // Assuming you have the user_id in UsersBranches
            });
        });

        return $this->paginate();
    }

    /**
     * @return Builder
     */
    public function filterResults()
    {
        $searchableFields = $this->searchableFields();
        $search = $this->search;

        $this->getQuery()->when(! empty($search), function (Builder $q) use ($search, $searchableFields) {
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
     * @param $Id
     */
    public function statusFilter($Id)
    {
        $this->filterStatus = $Id;
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
}
