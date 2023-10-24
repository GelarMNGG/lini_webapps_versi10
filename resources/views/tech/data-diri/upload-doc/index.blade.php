@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Upload dokumen';
    $formRouteIndex = 'tech-input-doc.index';
    $formRouteCreate = 'tech-input-doc.create';
    $formRouteEdit = 'tech-input-doc.edit';
    $formRouteDestroy = 'tech-input-doc.destroy';
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

    @if (count($documentDatas) > 0)
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($documentDatas) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="row m-0">
                    <?php $separator=1; ?>
                    @foreach($documentDatas as $data)
                        <div class="col-6 p-2">
                            <div class="bg-card-box br-5 p-2">
                                <span class="text-danger text-uppercase"><strong>{{ isset($data->doc_name) ? strtoupper($data->doc_name) : 'Belum ada data' }}</strong></span>

                                <form action="{{ route($formRouteDestroy, $data->id) }}" class="float-right" style="display:inline" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <a href="{{ route($formRouteEdit, $data->id) }}" class='btn badge badge-info float-right' style="display:inline;"> <i class='fas fa-edit' title='Edit'></i> Ubah</a>

                                    <br><button type="submit" class="btn badge badge-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                </form>

                                @if(isset($data->image))
                                    <br><strong><a type="button" class="text-info" data-toggle="modal" data-target="#docModal{{ $data->id }}">Lihat dokumen </a></strong>
                                @endif

                                <!-- Modal -->
                                <div class="modal fade" id="docModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="projectMinutes" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                        <div class="modal-content-img">
                                            <div class="modal-body text-center">
                                            <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/upload-doc/tech/'.$data->image) }}"  />
                                                <div class="alert alert-warning" id="projectMinutes">
                                                    <h5>
                                                        Tipe dokumen: <span class="text-muted">{{ ucfirst($data->doc_name) }}</span>
                                                    </h5>
                                                </div>
                                            </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                        <?php $separator++; ?>
                    @endforeach
                    <div class="col-12">
                        <?php $paginator = $documentDatas; ?>
                        @include('includes.paginator')
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Upload dokumen</a>
                </div>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($documentDatas) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>

            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Upload dokumen</a>
                </div>
            </div>
        </div> <!-- container-fluid -->
    @endif
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
