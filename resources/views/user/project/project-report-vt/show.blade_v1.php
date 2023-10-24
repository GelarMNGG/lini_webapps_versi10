@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
        $pageTitle      = 'Laporan '.strtoupper($projectTask->project_name).'-Task '.ucwords(strtolower($projectTask->name));
        $statusBadge    = array('','info','danger','purple','pink','warning','dark');
    //template
        $formRouteTemplateShow = 'user-projects-template.show';
    //image
        $formRouteShow = 'user-projects.show';
        $formRouteUpdate= 'user-projects.update';
    //report format
        $formReportFormatStore = 'user-projects-report-qc.store';
        $formReportFormatUpdate = 'user-projects-report-qc.update';
        $formReportFormatDestroy = 'user-projects-report-qc.destroy';
    //report format title
        $formReportFormatTitleStore = 'user-projects-report-format-t.store';
        $formReportFormatTitleUpdate = 'user-projects-report-format-t.update';
        $formReportFormatTitleDestroy = 'user-projects-report-format-t.destroy';
    //report sformat subtitle
        $formReportFormatSubTitleStore = 'user-projects-report-format-st.store';
        $formReportFormatSubTitleUpdate = 'user-projects-report-format-st.update';
        $formReportFormatSubTitleDestroy = 'user-projects-report-format-st.destroy';
    //image comments
        $formImageCommentsStore = 'user-projects-report-qc-comments.store';

?>
@endsection

@section('content')
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid mt-2">
            <div class="flash-message">
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
            <div class="row">
                <div class="col-md-12">
                    <div class="card-box">
                        @if($dataProject->customer_logo != null)
                            <span class="logo report-logo float-left">
                                <img src="{{ asset('img/clients/'.$dataProject->customer_logo) }}" alt="logo {{ $dataProject->customer_name }}">
                            </span>
                        @endif
                        @if($dataProject->partner_logo != null)
                            <span class="logo report-logo float-right">
                                <img src="{{ asset('img/clients/'.$dataProject->partner_logo) }}" alt="logo {{ $dataProject->partner_name }}">
                            </span>
                        @else
                            <span class="logo report-logo float-right">
                                <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}">
                            </span>
                        @endif
                        <div class="panel-heading text-center">
                            <h1 class="text-uppercase">Laporan <strong>{{ isset($projectTask->project_name) ? strtoupper($projectTask->project_name) : '' }}</strong></h1>
                            <h2 class="text-uppercase"> <strong><span class="text-danger">{{ isset($projectTask->name) ? strtoupper($projectTask->name) : 'Belum ada task' }}</span></strong></h2>
                            <!-- report date -->
                            {{ date('l, d F Y',strtotime($projectReportAllData->created_at)) }}
                        </div>
                        <hr>

                        <!-- table of content -->
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h2>Daftar isi</h2>
                                        <ol>
                                            @if(isset($projectReportFormatTitles))
                                                @foreach($projectReportFormatTitles as $tocTitle)
                                                    <h4> <li> {{ ucfirst($tocTitle->title) }}
                                                        <form action="{{ route($formReportFormatTitleDestroy, $tocTitle->id) }}" method="POST" class="d-print-none" style="display:inline;">
                                                            @method('DELETE')
                                                            @csrf
                                                            <!-- hidden data -->
                                                                <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                                                <input type="hidden" name="task_id" value="{{ $projectTask->id }}">
                                                                <input type="hidden" name="pra_id" value="{{ $projectReportAllData->id }}">
                                                            <!-- sort info -->
                                                                <button type="button" class="border-dashed text-dark small" title="Sort order"><i class="fas fa-sort-amount-up"></i> {{ $tocTitle->sort_order ?? '0' }}</button>
                                                            <!-- data sources -->
                                                                <button type="button" class="border-dashed text-dark small" title="Database"><i class="fas fa-database"></i> </button>
                                                            <!-- add subtitle -->
                                                                <button type="button" class="border-dashed text-dark small" data-toggle="modal" data-target="#liniSubTitleModal{{$tocTitle->id}}" value="Add sub title" style="padding:2px;"><i class="fas fa-plus"></i> Tambah sub judul </button>
                                                            <!-- edit -->
                                                                <button type="button" class="border-dashed text-dark small" data-toggle="modal" data-target="#editTitleModal{{$tocTitle->id}}" value="Edit sub title"> <i class="fas fa-edit"></i> </button>
                                                            <!-- delele button -->
                                                                <button type="submit" class="border-dashed text-danger small" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')" title="Delete"><i class="fas fa-trash"></i></button>
                                                        </form>
                                                    </li> </h4>
                                                    <!-- edit title modal -->
                                                        <div class="modal fade" id="editTitleModal{{$tocTitle->id}}" tabindex="-1" role="dialog" aria-labelledby="editTitleModal{{$tocTitle->id}}Label" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content" style="max-height:unset;">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="editTitleModal{{$tocTitle->id}}Label">Edit judul di daftar isi</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <form action="{{ route($formReportFormatTitleUpdate,$tocTitle->id) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <!-- hidden data -->
                                                                        <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                                                        <input type="hidden" name="task_id" value="{{ $projectTask->id }}">
                                                                        <input type="hidden" name="pra_id" value="{{ $projectReportAllData->id }}">
                                                                        <!-- hidden data end -->
                                                                        <div class="modal-body justify-content-center">
                                                                            <div class="row">
                                                                                <div class="col-md text-left">
                                                                                    <div class="form-group">
                                                                                        <label>Judul</label>
                                                                                        <input type="text" class="form-control" name="title" value="{{ ucfirst($tocTitle->title) }}" required>
                                                                                    </div>
                                                                                </div> <!-- end col -->
                                                                                <div class="col-md-2 text-left">
                                                                                    <div class="form-group">
                                                                                        <label>Urutan </label>
                                                                                        <input type="text" class="form-control" name="sort_order" value="{{ ucfirst($tocTitle->sort_order) }}">
                                                                                    </div>
                                                                                </div> <!-- end col -->
                                                                            </div> <!-- end row -->            
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            <button type="submit" class="btn btn-orange">Save changes</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <!-- edit title modal end -->
                                                    <!-- subtitle -->
                                                        @if($tocTitle->subtitleCount > 0)
                                                            <ul class="">
                                                                @foreach($projectReportFormatSubTitles as $tocSubTitle)
                                                                    @if($tocSubTitle->title_id == $tocTitle->id)
                                                                        <li>
                                                                            {{ ucfirst($tocSubTitle->subtitle) }}
                                                                            <form action="{{ route($formReportFormatSubTitleDestroy, $tocSubTitle->id) }}" method="POST" class="d-print-none" style="display:inline;">
                                                                                @method('DELETE')
                                                                                @csrf
                                                                                <!-- hidden data -->
                                                                                    <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                                                                    <input type="hidden" name="task_id" value="{{ $projectTask->id }}">
                                                                                    <input type="hidden" name="pra_id" value="{{ $projectReportAllData->id }}">
                                                                                <!-- sort info -->
                                                                                    <button type="button" class="border-dashed text-success small" title="Sort order"><i class="fas fa-sort-amount-up"></i> {{ $tocSubTitle->sort_order ?? '0' }}</button>
                                                                                <!-- data sources -->
                                                                                    <button type="button" class="border-dashed text-success small" title="Database"><i class="fas fa-database"></i> </button>
                                                                                <!-- edit -->
                                                                                    <button type="button" class="border-dashed text-success small" data-toggle="modal" data-target="#editSubTitleModal{{$tocSubTitle->id}}" value="Edit sub title"> <i class="fas fa-edit"></i> </button>
                                                                                <!-- delele button -->
                                                                                    <button type="submit" class="border-dashed text-danger small" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')" title="Delete"><i class="fas fa-trash"></i></button>
                                                                            </form>
                                                                        </li>
                                                                        <!-- edit subtitle modal -->
                                                                            <div class="modal fade" id="editSubTitleModal{{$tocSubTitle->id}}" tabindex="-1" role="dialog" aria-labelledby="editSubTitleModal{{$tocSubTitle->id}}Label" aria-hidden="true">
                                                                                <div class="modal-dialog" role="document">
                                                                                    <div class="modal-content" style="max-height:unset;">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title" id="editSubTitleModal{{$tocSubTitle->id}}Label">Edit sub judul di daftar isi</h5>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <form action="{{ route($formReportFormatSubTitleUpdate,$tocSubTitle->id) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                                                            @csrf
                                                                                            @method('PUT')
                                                                                            <!-- hidden data -->
                                                                                            <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                                                                            <input type="hidden" name="task_id" value="{{ $projectTask->id }}">
                                                                                            <input type="hidden" name="pra_id" value="{{ $projectReportAllData->id }}">
                                                                                            <input type="hidden" name="title_id" value="{{ $tocTitle->id }}">
                                                                                            <!-- hidden data end -->
                                                                                            <div class="modal-body justify-content-center">
                                                                                                <div class="row">
                                                                                                    <div class="col-md text-left">
                                                                                                        <div class="form-group">
                                                                                                            <label>Sub judul <span class='text-info'>{{ ucfirst($tocTitle->title) }}</span></label>
                                                                                                            <input type="text" class="form-control" name="subtitle" value="{{ ucfirst($tocSubTitle->subtitle) }}" required>
                                                                                                        </div>
                                                                                                    </div> <!-- end col -->
                                                                                                    <div class="col-md-2 text-left">
                                                                                                        <div class="form-group">
                                                                                                            <label>Urutan </label>
                                                                                                            <input type="text" class="form-control" name="sort_order" value="{{ ucfirst($tocSubTitle->sort_order) }}">
                                                                                                        </div>
                                                                                                    </div> <!-- end col -->
                                                                                                </div> <!-- end row -->            
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                <button type="submit" class="btn btn-orange">Save changes</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        <!-- edit subtitle modal end -->
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    <!-- subtitle end -->
                                                    <!-- add subtitle modal -->
                                                            <div class="modal fade" id="liniSubTitleModal{{$tocTitle->id}}" tabindex="-1" role="dialog" aria-labelledby="liniSubTitleModal{{$tocTitle->id}}Label" aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content" style="max-height:unset;">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="liniSubTitleModal{{$tocTitle->id}}Label">Tambah sub judul ke daftar isi</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <form action="{{ route($formReportFormatSubTitleStore) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                                            @csrf
                                                                            <!-- hidden -->
                                                                                <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                                                                <input type="hidden" name="task_id" value="{{ $projectTask->id }}">
                                                                                <input type="hidden" name="pra_id" value="{{ $projectReportAllData->id }}">
                                                                                <input type="hidden" name="title_id" value="{{ $tocTitle->id }}">
                                                                            <!-- hidden end -->
                                                                            <div class="modal-body justify-content-center">
                                                                                <div class="row">
                                                                                    <div class="col-md-6 text-left">
                                                                                        <div class="form-group">
                                                                                            <label>Sub judul <span class='text-info'>{{ ucfirst($tocTitle->title) }}</span></label>
                                                                                            <input type="text" class="form-control" name="subtitle" required>
                                                                                        </div>
                                                                                    </div> <!-- end col -->
                                                                                </div> <!-- end row -->            
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                <button type="submit" class="btn btn-orange">Save changes</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    <!-- add subtitle modal end -->
                                                @endforeach
                                                <hr>
                                            @endif
                                            <h4 class="small text-danger d-print-none">
                                                <button type="button" class="border-dashed text-danger" data-toggle="modal" data-target="#liniTitleModal"> <i class="fa fa-plus"></i> Tambah judul</button>
                                            </h4>
                                        </ol>
                                        <!-- add title modal -->
                                            <div class="modal fade" id="liniTitleModal" tabindex="-1" role="dialog" aria-labelledby="liniTitleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content" style="max-height:unset;">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="liniTitleModalLabel">Tambah judul ke daftar isi</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route($formReportFormatTitleStore) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                            @csrf
                                                            <!-- hidden -->
                                                            <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                                            <input type="hidden" name="task_id" value="{{ $projectTask->id }}">
                                                            <input type="hidden" name="pra_id" value="{{ $projectReportAllData->id }}">
                                                            <!-- hidden end -->
                                                            <div class="modal-body justify-content-center">
                                                                <div class="row">
                                                                    <div class="col-md-6 text-left">
                                                                        <div class="form-group">
                                                                            <label>Judul</label>
                                                                            <input type="text" class="form-control" name="title" required>
                                                                        </div>
                                                                    </div> <!-- end col -->
                                                                </div> <!-- end row -->           
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-orange">Save changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <!-- add title modal end -->
                                    </div>
                                </div>
                            </div>
                        <!-- table of content end -->
                        <p style="page-break-after: always;">&nbsp;</p>
                        <p style="page-break-before: always;">&nbsp;</p>













                        <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- content -->
                                        @if(isset($projectReportFormatDatas))
                                            @foreach($projectReportFormatDatas as $projectReportFormatData)
                                                <div class="">
                                                    <form action="{{ route($formReportFormatDestroy, $projectReportFormatData->id) }}" method="POST" class="form-center d-print-none" style="position:absolute;z-index:77;">
                                                        @method('DELETE')
                                                        @csrf
                                                        <!-- hidden data -->
                                                        <?php
                                                            //template count
                                                            if (count($projectReportTemplateSelectedDatas) > 0) {
                                                                $templateButton = '';
                                                            } else{
                                                                $templateButton = ' disabled';
                                                            }
                                                        ?>
                                                        <!-- sort info -->
                                                            <button type="button" class="btn badge-success button-img-report" title="Sort order"><i class="fas fa-sort-amount-up"></i> {{ $projectReportFormatData->sort_order }}</button>
                                                        <!-- select template -->
                                                            <button type="button" class="btn badge-warning button-img-report" data-toggle="modal" data-target="#template_modal{{ $projectReportFormatData->id }}" title="Template data" {{ $templateButton }}><i class="fas fa-cogs"></i></button>
                                                        <!-- edit button -->
                                                            <button type="button" class="btn badge-warning button-img-report" data-toggle="modal" data-target="#cat_id_modal{{ $projectReportFormatData->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                                                        <!-- delele button -->
                                                            <button type="submit" class="btn badge-danger button-img-report" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')" title="Delete"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                    <div class="box">
                                                        <h4> {{ ucfirst($projectReportFormatData->name) }}</h4>
                                                        <div class="row">
                                                            @if(isset($projectReportFormatData->template_id))
                                                                @if(isset($projectReportTemplateContentDatas))
                                                                    <!-- tipe -->
                                                                    <?php $image = 1; ?>
                                                                    @if($projectReportFormatData->type == $image)
                                                                        <?php $iContent = 1; ?>
                                                                        @foreach($projectReportTemplateContentDatas as $templateContent)
                                                                            @if($templateContent->template_id == $projectReportFormatData->template_id)
                                                                                <?php 
                                                                                    //column count
                                                                                    $columnData = $projectReportFormatData->image_count;
                                                                                    if ($columnData == 3) {
                                                                                        $column = 4;
                                                                                        $columnDiv = 3;
                                                                                    }elseif($columnData == 2){
                                                                                        $column = 6;
                                                                                        $columnDiv = 2;
                                                                                    }else{
                                                                                        $column = 12;
                                                                                        $columnDiv = 1;
                                                                                    }
                                                                                ?>
                                                                                <div class="col-md-{{$column}}">
                                                                                    <figure class="figure">
                                                                                        <div class="img-report-all">
                                                                                            <img src="{{ asset('img/projects/'.$dataProject->folder.'/'.$templateContent->image) }}">
                                                                                        </div>
                                                                                        <figcaption class="figure-caption text-center">{{ isset($templateContent->subcat_name) ? ucfirst($templateContent->subcat_name) : ucfirst($templateContent->subcatcust_name) }}</figcaption>
                                                                                    </figure>
                                                                                </div>
                                                                                <?php 
                                                                                    if ($iContent % $columnDiv == 0) {
                                                                                        echo "<div class='w-100'></div>";
                                                                                    }
                                                                                ?>
                                                                            <?php $iContent++; ?>
                                                                            @endif
                                                                        @endforeach
                                                                    @else
                                                                        <?php $ict = 1; ?>
                                                                        @foreach($projectReportTemplateContentDatasText as $templateContent)
                                                                            @if($templateContent->template_id == $projectReportFormatData->template_id)
                                                                                <?php 
                                                                                    //column count
                                                                                    $columnData = $projectReportFormatData->image_count;
                                                                                    if ($columnData == 3) {
                                                                                        $column = 4;
                                                                                        $columnDiv = 3;
                                                                                    }elseif($columnData == 2){
                                                                                        $column = 6;
                                                                                        $columnDiv = 2;
                                                                                    }else{
                                                                                        $column = 12;
                                                                                        $columnDiv = 1;
                                                                                    }
                                                                                ?>
                                                                                <div class="col-md-{{$column}} text-justify">
                                                                                    {!! $templateContent->text !!}
                                                                                </div>
                                                                                <?php 
                                                                                    if ($ict % $columnDiv == 0) {
                                                                                        echo "<div class='w-100'></div>";
                                                                                    }
                                                                                ?>
                                                                            @endif
                                                                            <?php $ict++; ?>
                                                                        @endforeach
                                                                    @endif
                                                                @else
                                                                    <div class="alert alert-warning">Belum ada data.</div>
                                                                @endif
                                                            @else
                                                                <img src="{{ asset('img/projects/report/report_format/'.$projectReportFormatData->image) }}" class="img-fluid">
                                                            @endif
                                                
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- edit modal -->
                                                    <form action="{{ route($formReportFormatUpdate,$projectReportFormatData->id) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                        @csrf
                                                        @method('PUT')
                                                        <!-- hidden data -->
                                                        <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                                        <input type="hidden" name="task_id" value="{{ $projectTask->id }}">
                                                        <!-- modal content -->
                                                        <div class="modal fade" id="cat_id_modal{{ $projectReportFormatData->id }}" tabindex="-1" role="dialog" aria-labelledby="projectImageModal" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content" style="max-height:unset;">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="liniModalLabel">Tambah Format laporan</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    
                                                                    <div class="modal-body justify-content-center">
                                                                        <div class="row">
                                                                            <div class="col-md-6 text-left">
                                                                                <?php $icode = 1; ?>
                                                                                @if(isset($projectReportFormatCodeDatas))
                                                                                    @foreach($projectReportFormatCodeDatas as $projectReportFormatCodeData)
                                                                                    <?php
                                                                                        if ($projectReportFormatCodeData->id == $projectReportFormatData->f_code) {
                                                                                            $check = ' checked';
                                                                                        }else{$check = '';}
                                                                                        //disable mix format
                                                                                        if ($projectReportFormatCodeData->text_count > 0 && $projectReportFormatCodeData->image_count > 0) {
                                                                                            $radioDisabled = ' disabled';
                                                                                        } else{
                                                                                            $radioDisabled = '';
                                                                                        }
                                                                                        $fcodeCombinedValue = $projectReportFormatCodeData->id.'.'.$projectReportFormatCodeData->type;
                                                                                    ?>
                                                                                        <div class="form-check col-md">
                                                                                            <input class="form-check-input" type="radio" name="f_code" id="f_code{{ $icode }}"  value="{{ $fcodeCombinedValue }}" {{ $check }} {{ $radioDisabled }}>
                                                                                            <label class="form-check-label" for="f_code{{ $icode }}">
                                                                                                @if($projectReportFormatCodeData->title != null)
                                                                                                    Judul H<span class="text-bold">{{ $projectReportFormatCodeData->title }}</span>
                                                                                                @else
                                                                                                    <!-- paragraf -->
                                                                                                    {{ $projectReportFormatCodeData->text_count != 0 ? $projectReportFormatCodeData->text_count.' paragraf' : '' }}
                                                                                                    <!-- image -->
                                                                                                    {{ $projectReportFormatCodeData->image_count != 0 ? ' '.$projectReportFormatCodeData->image_count.' gambar' : '' }}
                                                                                                @endif
                                                                                            </label>
                                                                                            <br>
                                                                                            <img src="{{ asset('img/projects/report/report_format/'.$projectReportFormatCodeData->image) }}" class="img-responsive">
                                                                                        </div>
                                                                                        <?php $icode++; ?>
                                                                                    @endforeach
                                                                                    <hr>
                                                                                    <div class="col-md form-group">
                                                                                        <label for="sort_order"><strong>Urutan</strong></label>
                                                                                        <input name="sort_order" data-parsley-type="digits" type="text" class="form-control parsley-success" required="" placeholder="Masukkan nomor urut format" value="{{ $projectReportFormatData->sort_order }}">
                                                                                    </div>
                                                                                @endif
                                                                            </div> <!-- end col -->
                                                                        </div> <!-- end row -->   
                                                                    </div>
                                                                    
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-orange">Save changes</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                <!-- edit modal end -->
                                                <!-- template modal -->
                                                    <form action="{{ route($formReportFormatUpdate,$projectReportFormatData->id) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                        @csrf
                                                        @method('PUT')
                                                        <!-- hidden data -->
                                                        <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                                        <input type="hidden" name="task_id" value="{{ $projectTask->id }}">
                                                        <!-- modal content -->
                                                        <div class="modal fade" id="template_modal{{ $projectReportFormatData->id }}" tabindex="-1" role="dialog" aria-labelledby="projectImageModal" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content" style="max-height:unset;">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="liniModalLabel">Pilih template data</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    
                                                                    <div class="modal-body justify-content-center">
                                                                        <div class="row">
                                                                            <div class="col-md-6 text-left">
                                                                                <?php $icode = 1; ?>
                                                                                @if(isset($projectReportTemplateSelectedDatas))
                                                                                    <div class="col-md form-group">
                                                                                        <label for="template_id"><strong>Data Template</strong></label>
                                                                                        <select id="template_id" name="template_id" class="form-control select2{{ $errors->has('template_id') ? ' has-error' : '' }}" required>
                                                                                            <?php
                                                                                                if(old('template_id') != null) {
                                                                                                    $template_id = old('template_id');
                                                                                                }elseif(isset($projectReportFormatData->template_id)){
                                                                                                    $template_id = $projectReportFormatData->template_id;
                                                                                                }else{
                                                                                                    $template_id = null;
                                                                                                }
                                                                                            ?>
                                                                                            @if ($template_id != null)
                                                                                                @foreach ($projectReportTemplateSelectedDatas as $dataTemplate)
                                                                                                    @if ($dataTemplate->id == $template_id)
                                                                                                        <option value='{{ $dataTemplate->template_id }}'>{{ strtoupper(strtolower($dataTemplate->name)) }}</option>
                                                                                                    @endif
                                                                                                @endforeach
                                                                                            @else
                                                                                                <option value="0">Pilih data template</option>
                                                                                            @endif
                                                                                            @foreach($projectReportTemplateSelectedDatas as $dataTemplate)
                                                                                                @if ($dataTemplate->type == $projectReportFormatData->type)
                                                                                                        <option value='{{ $dataTemplate->template_id }}'>{{ strtoupper(strtolower($dataTemplate->name)) }}</option>
                                                                                                    @endif
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>
                                                                                @else
                                                                                    <div class="col-md alert alert-warning">Data template tidak tersedia.</div>
                                                                                @endif
                                                                            </div> <!-- end col -->
                                                                        </div> <!-- end row -->   
                                                                    </div>
                                                                    
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-orange">Save changes</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                <!-- template modal end -->
                                            @endforeach
                                        @endif
                                        <div class="d-print-none card-body border-dashed rounded d-flex justify-content-center mb-2">
                                            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#liniModal">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <!-- add modal -->
                                            <form action="{{ route($formReportFormatStore) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                @csrf
                                                <!-- hidden -->
                                                <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                                <input type="hidden" name="task_id" value="{{ $projectTask->id }}">
                                                <input type="hidden" name="pra_id" value="{{ $projectReportAllData->id }}">
                                                <input type="hidden" name="sort_order" value="1">

                                                <div class="modal fade" id="liniModal" tabindex="-1" role="dialog" aria-labelledby="liniModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content" style="max-height:unset;">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="liniModalLabel">Tambah Format laporan</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body justify-content-center">
                                                                <div class="row">
                                                                    <div class="col-md-6 text-left">
                                                                        <?php $icode = 1; ?>
                                                                        @if(isset($projectReportFormatCodeDatas))
                                                                            @foreach($projectReportFormatCodeDatas as $projectReportFormatCodeData)
                                                                            <?php
                                                                                //disable mix format
                                                                                if ($projectReportFormatCodeData->text_count > 0 && $projectReportFormatCodeData->image_count > 0) {
                                                                                    $radioDisabled = ' disabled';
                                                                                } else{
                                                                                    $radioDisabled = '';
                                                                                }
                                                                                $fcodeCombinedValue1 = $projectReportFormatCodeData->id.'.'.$projectReportFormatCodeData->type;
                                                                            ?>
                                                                                <div class="form-check col-md">
                                                                                    <input class="form-check-input" type="radio" name="f_code" id="f_code{{ $icode }}"  value="{{ $fcodeCombinedValue1 }}" {{ $radioDisabled }}>
                                                                                    <label class="form-check-label" for="f_code{{ $icode }}">
                                                                                        @if($projectReportFormatCodeData->title != null)
                                                                                            Judul H<span class="text-bold">{{ $projectReportFormatCodeData->title }}</span>
                                                                                        @else
                                                                                            <!-- paragraf -->
                                                                                            {{ $projectReportFormatCodeData->text_count != 0 ? $projectReportFormatCodeData->text_count.' paragraf' : '' }}
                                                                                            <!-- image -->
                                                                                            {{ $projectReportFormatCodeData->image_count != 0 ? ' '.$projectReportFormatCodeData->image_count.' gambar' : '' }}
                                                                                        @endif
                                                                                    </label>
                                                                                    <br>
                                                                                    <img src="{{ asset('img/projects/report/report_format/'.$projectReportFormatCodeData->image) }}" class="img-responsive">
                                                                                </div>
                                                                                <?php $icode++; ?>
                                                                            @endforeach
                                                                        @endif
                                                                    </div> <!-- end col -->
                                                                </div> <!-- end row -->
                                                                            
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-orange">Save changes</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        <!-- add modal end -->
                                    </div>
                                </div>
                                <hr>
                                <div class="row m-0 mb-5">
                                    <div class="col-md">
                                        <span class="text-uppercase"><strong>Signature</strong></span>
                                    </div>
                                    <div class="w-100"></div>
                                    <div class="col-md">
                                        <div class="clearfix">
                                            <h5 class="small text-dark text-uppercase mb-5">{{ isset($dataProject->customer_name) ? ucwords($dataProject->customer_name) : 'Belum ada' }}
                                            </h5>
                                            <hr class="mb-0">
                                            <small>
                                                Name: {{ isset($dataProject->customer_pic_firstname) ? ucfirst($dataProject->customer_pic_firstname).' '.ucfirst($dataProject->customer_pic_lastname) : '' }} 
                                                <br>Date: <span class="text-muted">{{ isset($projectReportAllData->approved_by_cust_at) ? date('l, d F Y',strtotime($projectReportAllData->approved_by_cust_at)) : '-' }}</span>
                                            </small>
                                        </div>
                                    </div>
                                    @if(isset($dataProject->partner_name))
                                        <div class="col-md">
                                            <h5 class="small text-dark text-uppercase mb-5">{{ isset($dataProject->partner_name) ? ucwords($dataProject->partner_name) : '' }}</h5>
                                            <hr class="mb-0">
                                            <small>
                                                Nama: {{ isset($dataProject->partner_pic_firstname) ? ucfirst($dataProject->partner_pic_firstname).' '.ucfirst($dataProject->partner_pic_lastname) : '' }} 
                                                <br>Date: <span class="text-muted">{{ isset($projectReportAllData->approved_by_partner_at) ? date('l, d F Y',strtotime($projectReportAllData->approved_by_partner_at)) : '-' }}</span>
                                            </small>
                                        </div>
                                    @endif
                                    <div class="col-md">
                                        <h5 class="small text-dark text-uppercase mb-5">{{ isset($companyInfo->name) ? ucwords($companyInfo->name) : 'PT Lima Inti Sinergi' }}</h5>
                                        <hr class="mb-0">
                                        <small>
                                            Nama: {{ isset($dataProject->pm_firstname) ? ucfirst($dataProject->pm_firstname).' '.ucfirst($dataProject->pm_lastname) : '' }} 
                                            <br>Date: <span class="text-muted"> {{ isset($projectReportAllData->approved_by_pm_at) ? date('l, d F Y',strtotime($projectReportAllData->approved_by_pm_at)) : '-' }}</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="d-print-none">
                                    <div class="float-right">
                                        <form action="{{ route($formReportFormatUpdate,$projectReportAllData->id) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                        <?php /*
                                        <form action="{{ route($formReportFormatUpdate,$projectReportFormatData->id) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate> */ ?>
                                            @csrf
                                            @method('PUT')
                                            <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>

                                            <!-- hidden data -->
                                            <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                            <input type="hidden" name="task_id" value="{{ $projectTask->id }}">

                                            @if(Auth::user()->user_level == 3)
                                                @if(!isset($projectReportAllData->approved_by_pm_at) && $projectReportAllData->status == 1)
                                                    <!-- the data status -->
                                                    <input class="form-control" type="text" name="status" value="2" hidden>
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Approve</button>
                                                @elseif(isset($projectReportAllData->approved_by_pm_at) && !isset($projectReportAllData->approved_by_cust_at))
                                                    <input class="form-control" type="text" name="status" value="0" hidden>
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Reset</button>
                                                @endif
                                            @endif

                                            @if(Auth::user()->user_level == 4)
                                                @if(!isset($projectReportAllData->submitted_at))
                                                    <input class="form-control" type="text" name="status" value="1" hidden>
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Submit</button>
                                                @endif
                                                @if(isset($projectReportAllData->approved_by_pm_at) && $projectReportAllData->status == 2)
                                                    <input class="form-control" type="text" name="status" value="3" hidden>
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" onclick="return confirm('Apakah Anda yakin share laporan {{ strtoupper($projectTask->project_name) }} - Task {{ ucwords(strtolower($projectTask->name)) }} ke pelanggan?')" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Share to customer</button>
                                                @endif
                                            @endif

                                            <a href="{{ route($formRouteShow,$projectTask->id) }}" type="button" class="btn btn-secondary">Kembali</a>
                                        </form>

                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            @if(Auth::user()->user_level == 3)
                                @if($projectReportAllData->countSubmitted > 0 && $projectReportAllData->countPMApproved < 1)
                                    <form action="route($formRouteUpdate,$projectTemplate->id)" class="text-right mt-1" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                        @csrf
                                        @method('PUT')

                                        <!-- hidden data -->
                                        <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                        <input type="hidden" name="task_id" value="{{ $projectTask->id }}">
                                        <input type="hidden" name="status" value="0">

                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-times' title='done'> </i> Reject</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    <?php /* css setting */ ?>
                    <?php $css_1 = 'secondary'; ?>
                    <?php $css_2 = 'secondary'; ?>
                    <?php $css_3 = 'secondary'; ?>
                    <?php $css_4 = 'secondary'; ?>
                    <?php $css_text_1 = 'dark'; ?>
                    <?php $css_text_2 = 'dark'; ?>
                    <?php $css_text_3 = 'dark'; ?>
                    <?php $css_text_4 = 'dark'; ?>
                    <?php $fa_code_1 = 'times text-danger'; ?>
                    <?php $fa_code_2 = 'times text-danger'; ?>
                    <?php $fa_code_3 = 'times text-danger'; ?>
                    <?php $fa_code_4 = 'times text-danger'; ?>
                    
                    @if(isset($projectReportAllData->approved_by_cust_at))
                        <?php $css_1 = 'warning'; $css_ = 'warning'; $css_3 = 'warning'; $css_4 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; $css_text_3 = 'text-info'; $css_text_4 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; $fa_code_3 = 'check text-success'; $fa_code_4 = 'check text-success'; ?>
                    @elseif(isset($projectReportAllData->approved_by_pm_at))
                        <?php $css_1 = 'warning'; $css_2 = 'warning'; $css_3 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; $css_text_3 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; $fa_code_3 = 'check text-success'; ?>
                    @elseif(isset($projectReportAllData->submitted_at))
                        <?php $css_1 = 'warning'; $css_2 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; ?>
                    @elseif(isset($projectReportAllData->technician))
                        <?php $css_1 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; ?>
                    @endif
                    <!-- Approval notifications -->
                    <div class="alert alert-{{ $css_4 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_4 }}"></i> ]</strong> Laporan disetujui oleh <span class="{{ $css_text_4 }}">Pelanggan</span>. 
                        <div class="float-right">{{ isset($projectReportAllData->approved_by_cust_at) ? date('l, d F Y', strtotime($projectReportAllData->approved_by_cust_at)) : 'Belum disetujui' }}</div>
                    </div>
                    <div class="alert alert-{{ $css_3 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_3 }}"></i> ]</strong> Laporan disetujui oleh <span class="{{ $css_text_3 }}">Project Manager</span>. 
                        <div class="float-right">{{ isset($projectReportAllData->approved_by_pm_at) ? date('l, d F Y', strtotime($projectReportAllData->approved_by_pm_at)) : 'Belum disetujui' }}</div>
                    </div>
                    <div class="alert alert-{{ $css_2 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_2 }}"></i> ]</strong> Laporan diajukan oleh <span class="{{ $css_text_2 }}">Admin Document</span>.
                        <div class="float-right">{{ isset($projectReportAllData->submitted_at) ? date('l, d F Y', strtotime($projectReportAllData->submitted_at)) : 'Belum disubmit' }}</div>
                    </div>
                    <?php /*
                    <div class="alert alert-{{ $css_1 }}">
                        <strong>[ <i class="fas fa-{{ $fa_code_1 }}"></i> ]</strong> Format laporan diajukan oleh <span class="{{ $css_text_1 }}">Teknisi</span>.
                        <div class="float-right">{{ isset($projectReportAllData->submitted_at) ? date('l, d F Y', strtotime($projectReportAllData->submitted_at)) : 'Belum disubmit' }}</div>
                    </div>
                    */ ?>
                    <div class="d-print-none">
                        <form method="post" action="{{ route($formImageCommentsStore) }}" enctype="multipart/form-data" class="card-box">
                            @csrf

                            <span class="input-icon icon-right">
                                <textarea rows="3" name="comment" class="form-control" placeholder="Kirim komentar" value="{{ old('comment') ?? '' }}" required></textarea>
                            </span>
                            <!-- comment data -->
                            <input value="{{ $projectTask->project_id }}" name="project_id" hidden>
                            <input value="{{ $projectTask->id }}" name="task_id" hidden>
                            <input value="{{ $projectTask->name }}" name="task_title" hidden>
                            <input value="user" name="receiver_type" hidden>
                            <input value="{{ isset($projectReportAllData->id) ? $projectReportAllData->id : '' }}" name="pra_id" hidden>
                            <!-- comment data -->
                            <div class="pt-1 float-right">
                                <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light">Kirim komentar</button>
                            </div>
                            <ul class="nav nav-pills profile-pills mt-1">
                                <li>
                                    <a href="#"><i class="far fa-smile"></i></a>
                                </li>
                            </ul>
                        </form>

                        @if(isset($projectReportAllData->commentsCount) && $projectReportAllData->commentsCount > 0)
                            @foreach ($dataComments as $data1)
                            <div class="card-box">
                                <div class='media mb-3'>
                                    @if($data1->publisher_type == 'user')
                                        @foreach($users as $dataUser)
                                            @if($data1->publisher_id == $dataUser->id)
                                                <img src="{{ asset('admintheme/images/users/'.$dataUser->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                                <div class='media-body'>
                                                    <h5 class='mt-0'>
                                                        <a href='#' class='text-dark'>
                                                            {{ ucfirst($dataUser->firstname).' '.ucfirst($dataUser->lastname) }} 
                                                            <span class="text-info">{{ ucwords($dataUser->title) }}</span>
                                                        </a>
                                                        <small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->date)) }}</small>
                                                    </h5>
                                                    <p>{{ ucfirst($data1->comment) }}</p>
                                                    <div class='comment-footer'>
                                                        <span>{{ date('l, d M Y', strtotime($data1->date)) }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div> <!-- container-fluid -->
    </div> <!-- content -->
@endsection

@section ('script')
<script>
    
</script>

@endsection
