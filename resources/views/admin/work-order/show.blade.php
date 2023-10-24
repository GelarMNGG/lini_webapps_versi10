@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'work order';
    $formRouteIndex = 'admin.index';
?>
@endsection

@section('content')

<!-- test -->
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
                        </div>
                        <hr>
                        <div class="panel-body">
                            <div class="clearfix">
                                <div class="row">
                                    <div class="col-md-8 float-left">
                                        <h3><strong>PT {{ $companyInfo->name }}</strong></h3>
                                        <br><span>{{ $companyInfo->address }}</span>
                                        <br>Telp: {{ $companyInfo->phone }}, E-mail: {{ $companyInfo->email }}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md">
                                            W.O. No.
                                            <br>W.O. Date
                                        </div>
                                        <div class="col-md">
                                            <span class="text-uppercase">WO-2019-009</span>
                                            <br><span>{{ date('d F Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            Project ID No.
                                            <br>Project Name
                                            <br>Customer Name
                                        </div>
                                        <div class="col-md">
                                            <span class="text-uppercase">027/LNI/CSI/0121</span>
                                            <br><span>2021 CSI ITC Project All Nations</span>
                                            <br><span>PT CSI</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th colspan="3" style="text-align:center">Worker's personal identiry</th>
                                                    <th colspan="3" style="text-align:center">Payment & Renumeration</th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td> </td>
                                                    <td> </td>
                                                    <td> </td>
                                                    <td> </td>
                                                    <td> </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th colspan="6" style="text-align:center">Scope of Work</th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td> </td>
                                                    <td> </td>
                                                    <td> </td>
                                                    <td> </td>
                                                    <td> </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th colspan="6" style="text-align:center">Terms & Condition</th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td> </td>
                                                    <td> </td>
                                                    <td> </td>
                                                    <td> </td>
                                                    <td> </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <p>Signing & Approval</p>
                            </div>
                            <div class="row m-0 mb-5">
                                <div class="col-xl-4 col-4">
                                    <div class="clearfix">
                                        <h5 class="small text-dark text-uppercase mb-5">
                                            Made by
                                            <br>BC
                                        </h5>
                                        <small>
                                            Name: John due
                                            <br>Date: {{ date('d F Y') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-4">
                                    <h5 class="small text-dark text-uppercase mb-5">
                                        Acknowledge by
                                        <br>PM
                                    </h5>
                                    
                                    <small>
                                        Name: David Bach
                                        <br>Date: {{ date('d F Y') }}
                                    </small>
                                </div>
                                <div class="col-xl-4 col-4">
                                    <h5 class="small text-dark text-uppercase mb-5">
                                        Approved by
                                        <br>Director
                                    </h5>
                                    <small>
                                        Name: Darmo Sentono
                                        <br>Date: {{ date('d F Y') }}
                                    </small>
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-5">
                                <style>
                                    .note-text, .note-text p{font-size:.7rem;}
                                </style>
                                <div class="col-md-6 note-text">
                                    <p><strong>Remarks</strong></p>
                                    <p>(1) Fill in based on Total Price in "Scope of Work"</p>
                                </div>
                                <div class="col-md-6 note-text">
                                    <p><strong>Additional work Y/N</strong>
                                    <br>If yes, please explain below:
                                    <br>What is the impact:
                                    <br>Describe the additional work(s):</br>
                                </div>
                            </div>
                            <hr>
                            <div class="d-print-none">
                                <div class="float-right">
                                    <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>
                                    <form action="#" style="display:inline-block" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                        @csrf
                                        @method('PUT')
                                        <input class="form-control" type="number" name="status" value="3" hidden>
                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-edit' title='done'> </i> Ubah</button>
                                    </form>
                                    <form action="#" style="display:inline-block" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                        @csrf
                                        @method('PUT')
                                        <input class="form-control" type="number" name="status" value="1" hidden>
                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-info" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Kirim</button>
                                    </form>
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

    

<!-- test -->

@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>
@endsection
