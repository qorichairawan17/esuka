<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Support\Facades\Crypt;
use App\Models\Sync\StagingSyncSuratKuasaModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class StagingSuratKuasaDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<StagingSyncSuratKuasaModel> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actionBtn = '<button type="button" onclick="showDetail(\'' . Crypt::encrypt($row->id) . '\')" class="btn btn-soft-primary btn-sm"><i class="ti ti-eye"></i></button>';
                return $actionBtn;
            })
            ->editColumn('status', function ($row) {
                if ($row->status == \App\Enum\StatusSuratKuasaEnum::Ditolak->value) {
                    $badgeClass = 'bg-danger';
                } elseif ($row->status == \App\Enum\StatusSuratKuasaEnum::Disetujui->value) {
                    $badgeClass = 'bg-success';
                } else {
                    $badgeClass = 'bg-warning';
                }
                return '<span class="badge ' . $badgeClass . '">' . $row->status . '</span>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d-m-Y H:i:s');
            })->editColumn('updated_at', function ($row) {
                return $row->updated_at->format('d-m-Y H:i:s');
            })
            ->rawColumns(['action', 'status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<StagingSyncSuratKuasaModel>
     */
    public function query(StagingSyncSuratKuasaModel $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('stagingsuratkuasa-table')
            ->columns($this->getColumns())
            ->ajax(route('sync.index'))
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
            Column::make('user_id')->title('User ID')->addClass('align-top'),
            Column::make('email')->title('Email')->addClass('align-top'),
            Column::make('nama_lengkap')->title('Nama Lengkap')->addClass('align-top'),
            Column::make('perihal')->title('Perihal')->addClass('align-top'),
            Column::make('jenis_surat')->title('Jenis Surat')->addClass('align-top'),
            Column::make('nomor_surat_kuasa')->title('No. Surat Kuasa')->addClass('align-top'),
            Column::make('klasifikasi')->title('Klasifikasi')->addClass('align-top'),
            Column::make('status')->title('Status')->addClass('align-top'),
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
        return 'StagingSuratKuasa_' . date('YmdHis');
    }
}
