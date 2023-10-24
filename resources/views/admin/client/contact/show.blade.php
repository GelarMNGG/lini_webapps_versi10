@extends('layouts.dashboard-form-wizard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Data pelanggan';
    $formRouteIndex = 'client.index';
    
    //contact person form
    $formClientContactUpdate = 'client-contact-person.update';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
    @if ($errors->any())
        <p class="alert alert-danger">
            <small class="form-text">
                <strong>{{ $errors->first() }}</strong>
            </small>
        </p>
    @endif
</div>

<div class="card mt-2">
    <div id="progressbarwizard">
        <div class="card-header text-center text-uppercase bb-orange">
            <strong>{{ ucfirst($pageTitle) }}</strong>
            <br><span class="text-info"><strong>{{ isset($contactData->firstname) ? ucwords($contactData->firstname).' '.ucwords($contactData->lastname) : 'Data tidak tersedia' }}</strong></span>
        </div>
        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <div class="col-md p-2">
                    <div class="alert alert-warning">
                        <div class="float-right text-right">
                            @if($contactData->active == 1)
                                <div class="badge badge-success">Active</div>
                            @else
                                <div class="badge badge-danger">Inactive</div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="mr-1">
                                    <img src="{{ asset('admintheme/images/users/'.$contactData->image) }}" alt="user-image" class="rounded-circle avatar-xl">
                                </div>
                            </div>
                            <div class="col-md">
                                <span class="text-info">{{ ucwords($contactData->firstname).' '.ucwords($contactData->lastname) }}</span>

                                @if(isset($contactData->title))
                                    <br><span class="text-dark">{{ ucwords($contactData->title) }}</span>
                                @endif

                                <br><span class="text-danger text-uppercase">{{ ucwords($clientsData->name) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    @if(isset($contactData))
                        <!-- data tech -->
                            <div class="row alert alert-warning m-0 small">
                                <div class="col-md">
                                    <strong>Nama panggilan</strong>
                                    <br>{{ isset($contactData->name) ? ucfirst($contactData->name) : '-' }}
                                </div>
                                <div class="col-md">
                                    <strong>Nama depan</strong>
                                    <br>{{ isset($contactData->firstname) ? ucwords($contactData->firstname) : '-' }}
                                </div>
                                <div class="col-md">
                                    <strong>Nama belakang</strong>
                                    <br>{{ isset($contactData->lastname) ? ucwords($contactData->lastname) : '-' }}
                                </div>
                                <div class="col-md">
                                    <strong>Nomor HP</strong>
                                    <br>{{ isset($contactData->mobile) ? $contactData->mobile : '-' }}
                                </div>
                            </div>
                            <div class="row alert alert-warning m-0 small">
                                <div class="col-md-4">
                                    <strong>Email</strong>
                                    <br>{{ isset($contactData->email) ? $contactData->email : '-' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Shared by</strong>
                                    <br>{{ ucwords($publisherData->firstname).' '.ucwords($publisherData->lastname) }}
                                </div>
                            </div>
                            <div class="row alert alert-warning m-0 small">
                                <div class="col-md">
                                    <strong>Alamat kantor</strong>
                                    <br>{{ isset($contactData->address) ? $contactData->address : '-' }}
                                </div>
                                <div class="col-md">
                                    <strong>Note</strong>
                                    <br>{{ isset($contactData->note) ? $contactData->note : '-' }}
                                </div>
                            </div>
                        <!-- data tech -->
                    @else
                        <div class="alert alert-warning">Data belum tersedia.</div>
                    @endif
                </div> <!-- end col -->
            </div> <!-- end row -->

        </div>
        <div class="card-body">
            <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Kembali</a>
        </div>
    </div>
</div> <!-- card -->

@endsection

@section ('script')

@endsection
