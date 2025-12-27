<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class AdministratorDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<User> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $detailUrl = route('administrator.detail', ['id' => Crypt::encrypt($row->id)]);
                $editUrl = route('administrator.form', ['param' => 'edit', 'id' => Crypt::encrypt($row->id)]);
                $deleteUrl = route('administrator.destroy', ['id' => Crypt::encrypt($row->id)]);
                $actionBtn = '';
                $actionBtn = '<a href="' . $detailUrl . '" class="btn btn-soft-primary btn-sm"><i class="ti ti-eye"></i></a>';
                $actionBtn .= '<a href="' . $editUrl . '" class="btn btn-soft-warning btn-sm"><i class="ti ti-edit"></i></a>';
                Auth::id() === $row->id ? $actionBtn .= '<button type="button" disabled class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>' : $actionBtn .= '<button type="button" onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm ms-1"><i class="ti ti-trash"></i></button>';
                return '<div class="d-flex flex-row gap-1">' . $actionBtn . '</div>';
            })
            ->editColumn('block', function ($row) {
                return $row->block == 1 ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-danger">Tidak</span>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : '';
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at->format('d-m-Y H:i:s') : '';
            })
            ->rawColumns(['block', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<User>
     */
    public function query(User $model): QueryBuilder
    {
        return $model->where('role', '!=', 'User')->orderBy('updated_at', 'desc')->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('administrator-table')
            ->columns($this->getColumns())
            ->ajax(route('administrator.index'))
            ->orderBy(1)
            ->selectStyleSingle()
            ->processing(true)
            ->serverSide();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')
                ->title('#')
                ->orderable(false)
                ->searchable(false)
                ->width(30)
                ->addClass('text-center align-top'),
            Column::make('name')->addClass('align-top'),
            Column::make('email')->addClass('align-top'),
            Column::make('role')->addClass('align-top'),
            Column::make('block')->addClass('align-top'),
            Column::make('created_at')->addClass('align-top'),
            Column::make('updated_at')->addClass('align-top'),
            Column::computed('action')
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Administrator_' . date('YmdHis');
    }
}
