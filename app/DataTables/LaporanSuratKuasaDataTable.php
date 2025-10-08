<?php

namespace App\DataTables;

use App\Enum\StatusSuratKuasaEnum;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class LaporanSuratKuasaDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<PendaftaranSuratKuasaModel> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with(['register'])))
            ->addIndexColumn()
            ->editColumn('tanggal_daftar', function ($row) {
                return \Carbon\Carbon::parse($row->tanggal_daftar)->format('d/m/Y');
            })
            ->addColumn('permohon', function ($row) {
                return $row->pemohon ?? 'N/A';
            })
            ->addColumn('nomor_surat_kuasa', function ($row) {
                // Mengambil nomor registrasi dari relasi registerSuratKuasa
                return $row->register->nomor_surat_kuasa ?? '-';
            })
            ->editColumn('status', function ($row) {
                $statusEnum = StatusSuratKuasaEnum::tryFrom($row->status);
                if ($statusEnum === StatusSuratKuasaEnum::Disetujui) {
                    return '<span class="badge bg-soft-success">' . $statusEnum->value . '</span>';
                } elseif ($statusEnum === StatusSuratKuasaEnum::Ditolak) {
                    return '<span class="badge bg-soft-danger">' . $statusEnum->value . '</span>';
                }
                // Fallback untuk status lain jika ada
                return '<span class="badge bg-soft-warning">' . $row->status . '</span>';
            })
            ->rawColumns(['status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<PendaftaranSuratKuasaModel>
     */
    public function query(PendaftaranSuratKuasaModel $model): QueryBuilder
    {
        $query = $model->newQuery();

        // Menerapkan filter status dari request
        if ($this->request->filled('status') && $this->request->get('status') != '') {
            $query->where('status', $this->request->get('status'));
        }

        // Menerapkan filter tahun dari request
        if ($this->request->filled('tahun') && $this->request->get('tahun') != '') {
            $tahun = $this->request->get('tahun');
            $query->whereYear('tanggal_daftar', $tahun);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('laporansuratkuasa-table')
            ->columns($this->getColumns())
            ->ajax($this->getAjaxOptions())
            ->orderBy(1, 'desc') // Order by tanggal pengajuan (created_at) descending
            ->buttons([
                Button::make('excel'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reload'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('No')->searchable(false)->orderable(false)->width(30),
            Column::make('tanggal_daftar'),
            Column::make('pemohon')->title('Pemohon')->orderable(false),
            Column::make('nomor_surat_kuasa')->title('Nomor Surat')->orderable(false),
            Column::make('status')->title('Status'),
        ];
    }
    protected function getAjaxOptions(): array
    {
        return [
            'url' => route('surat-kuasa.laporan'),
            'type' => 'GET',
            'data' => 'function(d) {
                d.status = $("#statusFilter").val();
                d.tahun = $("#tahunFilter").val();
            }',
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'LaporanSuratKuasa_' . date('YmdHis');
    }
}
