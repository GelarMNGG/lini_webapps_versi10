@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Dashboard'; 
    $dashboardLink  = 'admin.index';
    $department = Auth::user()->department_id;
    $statusBadge    = array('dark','info','success','danger','purple','pink','warning');
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    <!-- announcement -->
    @if(isset($flashMessageData))
        <p class="alert alert-{{ $flashMessageData->level }}">{{ ucfirst($flashMessageData->message) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
    @endif
    <!-- session -->
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card text-center mt-2">
    <div class="card-header text-uppercase bb-orange"><strong>{{ ucfirst($pageTitle) }}</strong></div>

    <div class="card-body bg-gray-lini-2">
        <div class="row justify-content-center">
            <div class="col-md-2 alert alert-warning">
                <a href="{{ route('admin-minutes-report.index') }}" class="btn icon-box">
                    <i class="fas fa-running rounded-circle icon-big mb-2 badge-danger" style="padding-top:11px !important;"></i>
                </a>
                <br><span class="text-uppercase"><small>Laporan Aktivitas Staff</small></span>
            </div>
        </div>
    </div>
</div> <!-- container-fluid -->

<!-- calendar -->
<style>
    .calendar-toolbar {
    margin-bottom: 10px;
    }
    .calendar-month-row {
    height: 75px;
    }
    .calendar-prior-months-date {
    color: #DDD;
    }
    .calendar-current-date {
    text-align: center;
    display: inline-block;
    width: 115px;
    }
</style>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md">
                <div class="calendar" id="calendar">
                  
                  <!-- Calendar toolbar -->
                  <div class="calendar-toolbar">
                    
                    <!-- Calendar "today" button -->
                    <button data-toggle="calendar" data-action="today" class="btn btn-default">
                      Today
                    </button>
                    
                    <!-- Calendar "prev" button -->
                    <button data-toggle="calendar" data-action="prev" class="btn btn-default">
                      <i class="fa fa-chevron-left"></i>
                    </button>
                    
                    <!-- Calendar "date-indicator" span -->
                    <div class="calendar-current-date"
                          data-day-format="MM/DD/YYYY"
                          data-week-format="MM/DD/YYYY"
                          data-month-format="MMMM, YYYY">
                      (placeholder)
                    </div>
                    
                    <!-- Calendar "next" button -->
                    <button data-toggle="calendar" data-action="next" class="btn btn-default">
                      <i class="fa fa-chevron-right"></i>
                    </button>
                    
                    <div class="btn-group pull-right">
                      
                      <!-- Calendar "day" button -->
                      <button data-toggle="calendar" data-action="day" class="btn btn-default">
                        Day
                      </button>
                      
                      <!-- Calendar "week" button -->
                      <button data-toggle="calendar" data-action="week" class="btn btn-default">
                        Week
                      </button>
                      
                      <!-- Calendar "month" button -->
                      <button data-toggle="calendar" data-action="month" class="btn btn-default">
                        Month
                      </button>
                      
                    </div>
                    
                  </div>
                  
                </div>
            </div>
        </div>
    </div>
</div>
<!-- calendar -->

<div class="card">
    <div class="card-body mt-2">
        <div class="row">
            <div class="col-md">
                <div class="alert alert-warning">
                    This page is intended to show you information and data that visually tracks, analyzes and displays key performance indicators (KPI) to monitor the health of a business, department or specific process. They are customizable to meet the specific needs of a department.
                    <br><strong>Please discuss with us to make this page even more informative and suitable for you.</strong>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- admin procurement -->
<div class="card">
    <div class="card-header"><span class="badge badge-pink float-left mr-1">{{ sizeof($prDatas) <= 5 ? sizeof($prDatas) : '>5' }}</span> PR terbaru</div>
    <div class="card-body">
        @if(sizeof($prDatas) > 0)
            @foreach($prDatas as $dataPr)
                <div class="alert alert-warning">{{ ucfirst($dataPr->name) }} | {{ date('l, d F Y',strtotime($dataPr->date))}} 

                @if($dataPr->status == 2)
                    <span class="badge badge-danger float-right">Minta approval</span>
                @else
                    @foreach($prStatus as $dataStatus)
                        @if($dataStatus->id == $dataPr->status)
                            <span class="badge badge-{{ $statusBadge[$dataStatus->id] }} float-right">{{ ucwords($dataStatus->name) }}</span>
                        @endif
                    @endforeach
                @endif

                </div>
            @endforeach
        @endif
    </div>
</div>
<!-- admin procurement -->
<div class="card">
    <div class="card-header">Pengumuman</div>
    <div class="card-body">
        <div class="alert alert-warning">
            Belum ada pengumuman
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    /*
| ------------------------------------------------------------------------------
| Calendar plugin (rough draft)
| ------------------------------------------------------------------------------
*/

(function($){
  
  var Calendar = function (elem, options) {
    this.elem = elem;
    this.options = $.extend({}, Calendar.DEFAULTS, options);
    this.init();
  };
  
  Calendar.DEFAULTS = {
    datetime: undefined,
    dayFormat: 'DDD',
    weekFormat: 'DDD',
    monthFormat: 'MM/DD/YYYY',
    view: undefined,
  };

  Calendar.prototype.init = function () {
    if (! this.options.datetime || this.options.datetime == 'now') {
      this.options.datetime = moment();
    }
    if (! this.options.view) {
      this.options.view = 'month';
    }
    this.initScaffold()
        .initStyle()
        .render();
  }
  
  Calendar.prototype.initScaffold = function () {
    
    var $elem = $(this.elem),
        $view = $elem.find('.calendar-view'),
        $currentDate = $elem.find('.calendar-current-date');
    
    if (! $view.length) {
      this.view = document.createElement('div');
      this.view.className = 'calendar-view';
      this.elem.appendChild(this.view);
    } else {
      this.view = $view[0];
    }
    console.log($currentDate);
    console.log($currentDate);
    
    if ($currentDate.length > 0) {
      var dayFormat = $currentDate.data('day-format'),
          weekFormat = $currentDate.data('week-format'),
          monthFormat = $currentDate.data('month-format');
      this.currentDate = $currentDate[0];
      if (dayFormat) {
        this.options.dayFormat = dayFormat;
      }
      if (weekFormat) {
        this.options.weekFormat = weekFormat;
      }
      if (monthFormat) {
        this.options.monthFormat = monthFormat;
      }
    }
    return this;
  }
  
  Calendar.prototype.initStyle = function () {
    return this;
  }
  
  Calendar.prototype.render = function () {
    switch (this.options.view) {
      case 'day': this.renderDayView(); break;
      case 'week': this.renderWeekView(); break;
      case 'month': this.renderMonthView(); break;
      befault: this.renderMonth();
    }
  }
  
  Calendar.prototype.renderDayView = function () {
    //$(this.elem).append('Day View');
  }
  
  Calendar.prototype.renderWeekView = function () {
    //$(this.elem).append('Week View');
  }
  
  Calendar.prototype.renderMonthView = function () {
    
    var datetime = this.options.datetime.clone(),
        month = datetime.month();
    datetime.startOf('month').startOf('week');
    
    var $view = $(this.view),
        table = document.createElement('table'),
        tbody = document.createElement('tbody');
    
    $view.html('');
    table.appendChild(tbody);
    table.className = 'table table-bordered';
    
    var week = 0, i;
    while (week < 6) {
      tr = document.createElement('tr');
      tr.className = 'calendar-month-row';
      for (i = 0; i < 7; i++) {
        td = document.createElement('td');
        td.appendChild(document.createTextNode(datetime.format('D')));
        if (month !== datetime.month()) {
          td.className = 'calendar-prior-months-date';
        }
        tr.appendChild(td);
        datetime.add(1, 'day');
      }
      tbody.appendChild(tr);
      week++;
    }
    
    $view[0].appendChild(table);
    
    if (this.currentDate) {
      $(this.currentDate).html(
        this.options.datetime.format(this.options.monthFormat)
      );
    }
    
  }
  
  Calendar.prototype.next = function () {
    switch (this.options.view) {
      case 'day':
        this.options.datetime.add(1, 'day');
        this.render();
        break;
      case 'week':
        this.options.datetime.endOf('week').add(1, 'day');
        this.render();
        break;
      case 'month':
        this.options.datetime.endOf('month').add(1, 'day');
        this.render();
        break;
      default:
        break;
    }
  }
  
  Calendar.prototype.prev = function () {
    switch (this.options.view) {
      case 'day':
        break;
      case 'week':
        break;
      case 'month':
        this.options.datetime.startOf('month').subtract(1, 'day');
        this.render();
        break;
      default:
        break;
    }
  }
  
  Calendar.prototype.today = function () {
    this.options.datetime = moment();
    this.render();
  }

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this),
          data  = $this.data('bs.calendar'),
          options = typeof option == 'object' && option;
      if (! data) {
        data = new Calendar(this, options);
        $this.data('bs.calendar', data);
      }
      
      switch (option) {
        case 'today':
          data.today();
          break;
        case 'prev':
          data.prev();
          break;
        case 'next':
          data.next();
          break;
        default:
          break;
      }
    });
  };

  var noConflict = $.fn.calendar;

  $.fn.calendar             = Plugin;
  $.fn.calendar.Constructor = Calendar;

  $.fn.calendar.noConflict = function () {
    $.fn.calendar = noConflict;
    return this;
  };

  // Public data API.
  $('[data-toggle="calendar"]').click(function(){
    var $this = $(this),
        $elem = $this.parents('.calendar'),
        action = $this.data('action');
    if (action) {
      $elem.calendar(action);
    }
  });
  
})(jQuery);

/*
| ------------------------------------------------------------------------------
| Installation
| ------------------------------------------------------------------------------
*/

$('#calendar').calendar();
</script>
@endsection
