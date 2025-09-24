<?php

namespace App\DataTables;

use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Html\Column;
use App\Models\Pengguna\PaniteraModel;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class PaniteraDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<PaniteraModel> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $editUrl = route('panitera.form', ['param' => 'edit', 'id' => Crypt::encrypt($row->id)]);
                $deleteUrl = route('panitera.destroy', ['id' => Crypt::encrypt($row->id)]);
                $actionBtn = '<a href="' . $editUrl . '" class="btn btn-soft-warning btn-sm mb-2"><i class="ti ti-edit"></i></a>';
                $actionBtn .= '<button type="button" onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm ms-1"><i class="ti ti-trash"></i></button>';
                return $actionBtn;
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : '';
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at->format('d-m-Y H:i:s') : '';
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<PaniteraModel>
     */
    public function query(PaniteraModel $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('panitera-table')
            ->columns($this->getColumns())
            ->ajax(route('panitera.index'))
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
                ->addClass('text-center'),
            Column::make('nip'),
            Column::make('nama'),
            Column::make('jabatan'),
            Column::make('status'),
            Column::make('aktif'),
            Column::make('created_at'),
            Column::make('updated_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center')
                ->titleAttr(['class' => 'text-center']),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Panitera_' . date('YmdHis');
    }
}
