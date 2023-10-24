@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'purchase requisition';
    $formRouteIndex = 'user-pr.index';
    $formRouteUpdate= 'user-pr.update';
?>
@endsection

@section('content')
<div class="flash-message">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="content">
    <!-- Start Content-->
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <span class="logo float-left">
                        <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}" height="57">
                    </span>
                    <div class="panel-heading text-center text-uppercase">
                        <h3>{{ $pageTitle }}</h3>
                        Proyek: <strong><span class="text-info">{{ strtoupper($infoTaskProject->project_name) }}</span></strong>
                        <br>Task: <strong><span class="text-danger">{{ isset($infoTaskProject->name) ? strtoupper($infoTaskProject->name) : 'Belum ada task' }}</span></strong>
                    </div>
                    <hr>
                    <div class="panel-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <span>Nama pemohon: {{ ucwords($dataPR->firstname).' '.ucwords($dataPR->lastname) }}</span>
                                <br><span>Departemen: {{ ucwords($dataPR->department_name) }}</span>
                            </div>
                            <div class="float-right">
                                <h4>No PR #<strong>{{ $dataPR->number }}</strong> </h4>
                                <span>Tanggal: {{ date('d F Y', strtotime($dataPR->date)) }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-md">
                                Jenis kategori barang/jasa: <strong>{{ strtoupper($dataPR->category_name) }}</strong>
                            </div>
                        </div>
                        <!-- end row -->
                        <div class="row mt-4">
                            <div class="col-md">DETAIL RINCIAN PERMINTAAN BARANG</div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama barang/jasa</th>
                                                <th>Tanggal kebutuhan</th>
                                                <th>Satuan</th>
                                                <th>Jumlah</th>
                                                <th>Budget</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>{{ ucfirst($dataPR->name) }}</td>
                                                <td>{{ date('d F Y', strtotime($dataPR->date)) }}</td>
                                                <td>{{ ucfirst($dataPR->unit) }}</td>
                                                <td>{{ $dataPR->amount }}</td>
                                                <td width="121px">Rp. {{ number_format($dataPR->budget ?? 0) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row m-0">
                            <div class="col-xl-6 col-6">
                                <div class="clearfix">
                                    <h5 class="small text-dark text-uppercase">Lokasi penerima & PIC</h5>
                                    <small>
                                        {{ ucfirst($dataProject->pc_firstname.' '.$dataProject->pc_lastname ?? '-') }}
                                    </small>
                                </div>
                                <div class="clearfix mt-3">
                                    <h5 class="small text-dark text-uppercase">Keterangan</h5>
                                    <small>
                                        {{ ucfirst($dataPR->note ?? '-') }}
                                    </small>
                                </div>
                            </div>
                            <div class="col-xl-6 col-6">
                                <h5 class="small text-dark text-uppercase">Penjelasan/alasan dari kebutuhan permintaan pembelian</h5>
                                <small>
                                    {{ ucfirst($dataPR->alasan ?? '-') }}
                                </small>
                            </div>
                        </div>
                        <hr>
                        <div class="row m-0 mb-5">
                            <div class="col-md">
                                <div class="clearfix">
                                    <h5 class="small text-dark text-uppercase mb-5">dibuat, <br>project manager</h5>
                                    <small>
                                        {{ ucwords($dataPR->firstname).' '.ucwords($dataPR->lastname) }}
                                        <br>{{ date('l, d F Y', strtotime($dataPR->date_submitted)) }}
                                    </small>
                                </div>
                            </div>
                            <div class="col-md">
                                <h5 class="small text-dark text-uppercase mb-5">Disetujui, <br>Kepala departemen Project</h5>
                                <small>
                                    Adi Nareswara
                                    <br>{{ date('l, d F Y', strtotime($dataPR->date_submitted)) }}
                                </small>
                            </div>

                            @if(isset($dataPR->date_approved))
                            <div class="col-md">
                                <h5 class="small text-dark text-uppercase mb-5">Disetujui, <br>Kepala departemen procurement</h5>
                                <small>
                                    Ali Drifitra
                                    <br>{{ date('l, d F Y', strtotime($dataPR->date_approved)) }}
                                </small>
                            </div>
                            @endif
                        </div>
                        <hr>
                        <div class="d-print-none">
                            <div class="float-right">
                                <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>
                                <a href="{{ route($formRouteIndex,'project_id='.$infoTaskProject->project_id.'&task_id='.$infoTaskProject->id) }}" class="btn btn-secondary">Kembali</a>

                                @if($dataPR->status < 2)
                                    <form action="{{ route($formRouteUpdate, $dataPR->id) }}" style="display:inline-block" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                        @csrf
                                        @method('PUT')
                                        <!-- data -->
                                        <input type="text" name="project_id" value="{{ $infoTaskProject->project_id }}" hidden>
                                        <input type="text" name="task_id" value="{{ $infoTaskProject->id }}" hidden>
                                        <!-- data -->
                                        <input class="form-control" type="number" name="status" value="2" hidden>
                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Kirim ke Procurement</button>
                                    </form>
                                @endif

                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <!-- end row -->
        
    </div> <!-- container-fluid -->

</div> <!-- content -->

@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>
@endsection
