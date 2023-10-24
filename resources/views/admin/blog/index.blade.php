@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar artikel';
    $formRouteCreate = 'admin-blog.create';
    $formRouteEdit = 'admin-blog.edit';
    $formRouteShow = 'admin-blog.show';
    $formRouteDestroy = 'admin-blog.destroy';

    $statusBadge    = array('','danger','info','success','purple','pink','dark');
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

<div class="card mt-2">
    <div class="card-header text-center text-uppercase bb-orange">
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>

    @if (isset($blogs))
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <?php $separator=1; ?>
                @foreach($blogs as $data)
                    <div class="col-sm p-2">
                        <div class="bg-card-box br-5 p-2">
                            <div class="img-ca-box">
                                @if(isset($data->image))
                                    <button type="button" class="btn badge-pill text-dark" data-toggle="modal" style="position:absolute;" data-target="#blogModal{{ $data->id }}"><i class="fas fa-eye"></i> </button>

                                    <img class="img-ca" src="{{ asset('img/blogs/'.$data->image) }}">
                                @else
                                    <div class="p-5 text-center"><img class="img-ca-2" src="{{ asset('img/blogs/default.png') }}"></div>
                                @endif
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="blogModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="blogData" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                    <div class="modal-content-img">
                                        <div class="modal-body text-center">
                                            <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/blogs/'.$data->image) }}"  />
                                                <div class="alert alert-warning text-left" id="blogData">
                                                    <h5>
                                                        <span class="text-muted">{{ ucfirst($data->title) }}</span>
                                                    </h5>
                                                    <p> {!! $data->summary !!} </p>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br><strong>{{ isset($data->title) ? ucwords($data->title) : 'Belum ada data' }}</strong>

                            <div class="badge badge-{{ $statusBadge[$data->type] }} float-right">{{ $data->type_name }}</div>

                            <br><small>Oleh: </small>
                            @if($data->author_type == 'admin')
                                @foreach($admins as $admin)
                                    @if($admin->id == $data->author_id)
                                        <span>{{ ucwords($admin->firstname).' '.ucwords($admin->lastname) }}</span>
                                    @endif
                                @endforeach
                            @else
                                @foreach($users as $user)
                                    @if($user->id == $data->author_id)
                                        <span>{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}</span>
                                    @endif
                                @endforeach
                            @endif

                            <br>
                            @if($data->status == 1)
                                <span class="text-info">Published</span>
                            @else
                                <span class="text-danger">draft</span>
                            @endif

                            | <span class="text-secondary">{{ $data->views ? $data->views : '0' }} <i class="fas fa-eye"></i></span>

                            | <span class="text-info">{{ $data->created_at ? date('l, d F Y',strtotime($data->created_at)) : '-' }}</span>

                            <br>{{ ucfirst($data->summary) }}

                            <div class="mt-1">
                                <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                    @method('DELETE')
                                    @csrf

                                    <a href="{{ route($formRouteShow, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-warning t-white'> <i class='fas fa-eye' title='Lihat'></i> Lihat</a>

                                    <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>

                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                    <?php $separator++; ?>
                @endforeach
                <div class="w-100"></div>
                <div class="col-md">
                    <?php 
                        #$blogs->setPath('expenses-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                    ?>
                    <?php $paginator = $blogs; ?>
                    @include('includes.paginator')
                </div>
            </div>
        </div>
    @else
        <div class="card-body bg-gray-lini-2">
            <div class="alert alert-warning">Belum ada data.</div>
        </div>
    @endif
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah blog</a>
            <a class="btn btn-blue-lini mt-1" href="#">Kembali</a>
        </div>
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable(
            "order":[]
        );
    } );
</script>
@endsection
