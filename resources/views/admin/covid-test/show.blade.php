@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Surat pengantar';
    $formRouteIndex = 'admin-covid-test.index';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
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
                    <div class="img-fluid mr-2" style="display:inline-block; line-height:50px; vertical-align:top;">
                        <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}" height="57">
                    </div>
                    <div class="col-md-8" style="display:inline-block;">
                        <p style="line-height:1rem;">
                            <small><strong>PT {{ strtoupper($companyInfo->name) }}</strong></small>
                            <br><small>{{ ucfirst($companyInfo->address) }}</small>
                            <br><small>Telp. {{ ucfirst($companyInfo->phone) }}</small>
                            | <small>Email. {{ ucfirst($companyInfo->email) }}</small>
                        </p>
                    </div>
                    <div class="text-center" style="border-top:1px solid #000"><h5>{{ strtoupper($pageTitle) }}</h5></div>
                    <div class="panel-body">
                        <div class="clearfix">
                            <div class="float-left">
                                <span>Kepada Yth:</span>
                                <br><span class="text-dark"><strong>Klinik Fakhira</strong></span>
                                <br><span>di tempat</span>
                            </div>
                        </div>
                        <div class="row mt-2 mb-2">
                            <div class="col-md">
                                <p>Bersama ini kami dari Tim Satgas Covid-19 PT. Lima Inti Sinergi, mengirim karyawan, berikut:</p>
                                <div class="table-responsive">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td width="121px">Nama</td>
                                                <td>: <strong>{{ ucfirst($covidData->name) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td width="121px">Alamat</td>
                                                <td>: {{ ucfirst($covidData->address) }}</td>
                                            </tr>
                                            <tr>
                                                <td width="121px">NIK</td>
                                                <td>: {{ ucfirst($covidData->nik) }}</td>
                                            </tr>
                                            <tr>
                                                <td width="121px">Departemen</td>
                                                <td>: {{ ucfirst($covidData->department_name) }}</td>
                                            </tr>
                                            <tr>
                                                <td width="121px">Jabatan</td>
                                                <td>: {{ ucfirst($covidData->title) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <p class="mt-2"> Kepada karyawan tersebut mohon dapat dilakukan proses <strong>TES SWAB ANTIGEN/PCR</strong> sebagai persyaratan perjalanan dinas.</p>

                                <p>Demikian surat pengantar ini disampaikan untuk dapat dipergunakan sebagaimana mestinya. Atas perhatian dan kerja samanya atas hal ini, diucapkan terima kasih. </p>
                            </div>
                            <div class="w-100"></div>
                            <div class="row m-0">
                                <div class="col-12">
                                    <div class="clearfix">
                                        <p class="mb-5">Jakarta, {{ date('l, d F Y') }}</p>
                                        <p>
                                            <strong>{{ ucwords($dataOfficer->firstname.' '.$dataOfficer->lastname) }}</strong>
                                            <br>Ketua Satgas Covid-19
                                            <br>PT {{ ucwords($companyInfo->name) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- second section -->
                        <hr class="hr-double-black">
                        <div class="clearfix">
                            <div class="float-left">
                                <span>Kepada Yth:</span>
                                <br>Ketua Satgas Covid-19
                                <br><span class="text-dark"><strong>PT {{ ucwords($companyInfo->name) }}</strong></span>
                                <br><span>di tempat</span>
                            </div>
                        </div>
                        <div class="row mt-2 mb-2">
                            <div class="col-md">
                                <p>Dengan hormat, 
                                    <br>Bersama ini disampaikan bahwa nama berikut ini: </p>
                                <div class="table-responsive">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td width="121px">Nama</td>
                                                <td>: <strong>{{ ucfirst($covidData->name) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td width="121px">Jabatan</td>
                                                <td>: {{ ucfirst($covidData->title) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="mt-2">Untuk keperluan perjalanan dinas dengan informasi sebagai berikut :</p>
                                <div class="table-responsive">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td width="121px">ID Proyek</td>
                                                <td>: {{ ucfirst($covidData->project_number) }}</td>
                                            </tr>
                                            <tr>
                                                <td width="121px">Proyek</td>
                                                <td>: {{ ucfirst($covidData->project_name) }}</td>
                                            </tr>
                                            <tr>
                                                <td width="121px">Tujuan/nama site</td>
                                                <td>: {{ ucfirst($covidData->destination) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <p class="mt-2">Telah dilaksanakan <strong>TES SWAB ANTIGEN/PCR</strong> pada tanggal : ……… - ………………………………… - 2021</p>

                                <p class="mt-2">Demikian disampaikan, terima kasih.</p>
                            </div>
                            <div class="w-100"></div>
                            <div class="row m-0 mb-5">
                                <div class="col-12">
                                    <div class="clearfix">
                                        <p class="mb-5">Jakarta,  ……… - ………………………………… - 2021</p>
                                        <p>
                                            (……………………………………………)
                                            <br>Klinik Fakhira
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-print-none">
                            <hr>
                            <div class="float-right">

                                <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>

                                <a href="{{ route($formRouteIndex) }}" class="btn btn-secondary">Kembali</a>

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
