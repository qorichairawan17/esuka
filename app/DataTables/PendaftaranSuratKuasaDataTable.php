<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class PendaftaranSuratKuasaDataTable extends DataTable
{
    /**
     * Censors a string if the user role is not 'User'.
     *
     * @param string|null $data The data to be censored.
     * @param string $role The user's role.
     * @param int $visibleChars The number of characters to keep visible.
     * @return string|null The censored or original data.
     */
    private function sensorData(?string $data, string $role, int $visibleChars = 4): ?string
    {
        if (is_null($data)) {
            return null;
        }

        return ($role !== \App\Enum\RoleEnum::User->value) ? substr($data, 0, $visibleChars) . str_repeat('*', max(0, strlen($data) - $visibleChars)) : $data;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<PendaftaranSuratKuasaModel> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $detailUrl = route('surat-kuasa.detail', ['id' => Crypt::encrypt($row->id)]);
                $actionBtn = '<a href="' . $detailUrl . '" class="btn btn-soft-primary btn-sm mb-2"><i class="ti ti-eye"></i></a>';

                $roleBased = Auth::user()->role;
                if ($roleBased == \App\Enum\RoleEnum::User->value && $row->status == \App\Enum\StatusSuratKuasaEnum::Ditolak->value) {
                    $editUrl = route('surat-kuasa.form', ['param' => 'edit', 'klasifikasi' => $row->klasifikasi, 'id' => Crypt::encrypt($row->id)]);
                    $actionBtn .= '<a href="' . $editUrl . '" class="btn btn-soft-warning btn-sm mb-2"><i class="ti ti-edit"></i></a>';
                } elseif ($roleBased != \App\Enum\RoleEnum::User->value) {
                    $editUrl = route('surat-kuasa.form', ['param' => 'edit', 'klasifikasi' => $row->klasifikasi, 'id' => Crypt::encrypt($row->id)]);
                    $deleteUrl = route('surat-kuasa.destroy', ['id' => Crypt::encrypt($row->id)]);
                    $actionBtn .= '<a href="' . $editUrl . '" class="btn btn-soft-warning btn-sm mb-2"><i class="ti ti-edit"></i></a>';
                    $actionBtn .= '<button type="button" onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm ms-1"><i class="ti ti-trash"></i></button>';
                }
                return $actionBtn;
            })
            ->editColumn('id_daftar', function ($row) {
                $nomorSurat = ($row->register && $row->register->nomor_surat_kuasa) ? '<br>Nomor Surat : ' . $row->register->nomor_surat_kuasa : '';
                return '<a href="' . route('surat-kuasa.detail', ['id' => Crypt::encrypt($row->id)]) . '" title="Detail Pendaftaran">' . $row->id_daftar . '</a>' . $nomorSurat;
            })
            ->editColumn('updated_at', function ($row) {
                return $row->updated_at ? $row->updated_at->format('d-m-Y H:i:s') : '';
            })
            ->editColumn('jenis_surat', function ($row) {
                return $row->klasifikasi . ' - Perkara (' . $row->jenis_surat . ')';
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
            ->rawColumns(['action', 'id_daftar', 'status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<PendaftaranSuratKuasaModel>
     */
    public function query(PendaftaranSuratKuasaModel $model): QueryBuilder
    {
        $roleBased = Auth::user()->role;
        if ($roleBased == \App\Enum\RoleEnum::User->value) {
            return $model::with(['register', 'pihak'])->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->newQuery();
        }
        return $model::with(['register', 'pihak'])->orderBy('created_at', 'desc')->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('pendaftaransuratkuasa-table')
            ->columns($this->getColumns())
            ->ajax(route('surat-kuasa.index'))
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
            Column::make('id_daftar'),
            Column::make('tanggal_daftar'),
            Column::make('pemohon'),
            Column::make('perihal'),
            Column::make('jenis_surat'),
            Column::make('tahapan'),
            Column::make('status'),
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
        return 'PendaftaranSuratKuasa_' . date('YmdHis');
    }
}
