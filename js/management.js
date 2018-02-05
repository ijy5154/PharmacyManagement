$(document).ready(function(){
    var status_table = $('#Status').DataTable({
        initComplete: function () {
            this.api().columns(0).every( function () {
                var column = this;
                var select = $('<select style="width:100%; height:30px;" class="form-control"><option value="">전체</option></select>')
                    .appendTo( $('#medicine_option') )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        },
        'order': [[ 3, 'asc' ]],
        'columnDefs': [
            {
                'targets': [ 4, 5, 6 ],
                'visible': false
            }
        ],
        'dom': '<"row"<"col-sm-12"f<"#medicine_option">>><"#calendar">lt<"row"<"col-sm-6"i><"col-sm-6"p>>',
        'language': {
            'lengthMenu': "_MENU_ 개씩 보기",
            'info': "_PAGE_ / _PAGES_",
            'infoEmpty': "",
            'emptyTable': "등록된 일정이 없습니다.",
            'searchPlaceholder': "검색",
            'zeroRecords': "검색결과가 없습니다",
            'search': "",
            'infoFiltered': "(총 _MAX_ 개중)",
            'paginate': {
                'previous': "&laquo;",
                'next': "&raquo;"
            }
        }
    });

    $('#Status').on( 'click', 'tbody tr', function () {
        var rows = status_table.row( this ).data();

        if( jQuery.type(rows) !== 'undefined'){
            var id = rows[4];

            $.ajax({type: "POST",
                url: "schedule_detail.php",
                data:{ID: id},
                success: function(data){
                    var result = $.parseJSON(data);

                    if(result.success){
                    	modal_view(result);
                    } else {
                        alert(result.reason);
                    }
                }
            });
        }
    });

    var calendar_data = jQuery.parseJSON($('#calendar_data').val());

    status_table.on('search.dt', function () {
        var rows = new Array();

        status_table.rows({filter: 'applied'}).data().each(function(value, index) {
            row = new Object();

            row.id = value[4];
            row.title = value[0]; 
            row.start = value[2]; 

            if (value[3]) {
                row.end = value[3];
            }

            row.color = value[5];
			row.alarm = value[6];
            row.allDay = false;
            rows.push(row);
        });

        $('#calendar').fullCalendar('removeEvents');
        $('#calendar').fullCalendar('addEventSource', rows);
    });

    $('#calendar').fullCalendar({
        defaultDate: Date.now(),
        defaultView: 'month',
        allDaySlot: false,
        fixedWeekCount: false,
        allDayDefault: true,
        selectable: false,
        selectHelper: true,
        editable: false,
        eventLimit: true, // allow "more" link when too many events
        events: calendar_data,
        monthNames: ["1월","2월","3월","4월","5월","6월","7월","8월","9월","10월","11월","12월"],
        monthNamesShort: ["1월","2월","3월","4월","5월","6월","7월","8월","9월","10월","11월","12월"],
        dayNames: ["일요일","월요일","화요일","수요일","목요일","금요일","토요일"],
        dayNamesShort: ["일","월","화","수","목","금","토"],
        titleFormat: {
        	week: "YYYY년 MMMM DD일",
        },
        customButtons: {
        	addButton: {
                text: '약품 등록',
                click: function(e) {
                	if($('#logout').is(":visible")){
                    	modal_reset();
                    	$('#Add').show();
                    	$('#Modify').hide();
                    	$('#Del').hide();
                    	
                        $('#medicine_name').prop("readonly", false);
                        $('#user_name').prop("readonly", false);
                        $('#description').prop("readonly", false);
                        $('#start_date').prop("readonly", false);
                        $('#end_date').prop("readonly", false);
                        
                        $('#user_name').val($('#login_name').val()); //담당자 이름
                        
                    	$('#InfoModal').modal('show');
                    	
                    	$('#start_date').val(moment().format('YYYY-MM-DD'));
                    	$('#end_date').val(moment().add(1, 'month').format('YYYY-MM-DD'));                
						$('#alarm').val(0);
                	}else{
                		alert('로그인 후 이용해주세요.');
                		e.stopPropagation();
                	    $(".dropdown-toggle").dropdown('toggle');
						$("#ID").focus();
                	}
                }
            }
        },
        header: {
        	left: "title addButton",
			right:  'month,agendaWeek today prev,next'
        },
        buttonText: {
			today : '오늘',
			month : '월별',
		    week : '주별'
        },
        eventClick: function(calEvent, jsEvent, view) {
            var id = calEvent.id;

            if (jQuery.type(id) !== 'undefined') {
                $.post('schedule_detail.php', {ID: id}, function(result) {
                    if(result.success){
                    	modal_view(result);
                    } else {
                        alert(result.reason);
                    }
                }, 'json');
            }
        }
    });
    
    $('#start_date').datepicker({
    	'autoclose': true,
    	'format': 'yyyy-mm-dd',
    	'language' : 'kr'
    }).on('changeDate', function(ev){
        var dateData = $('#start_date').val();
		$('#end_date').val(dateData);
	});
    
    $('#end_date').datepicker({
    	'autoclose': true,
    	'format': 'yyyy-mm-dd',
    	'language' : 'kr'
    });
    
    function modal_reset(){
    	$('#info').val('');
        $('#medicine_name').val('');
        $('#user_name').val('');
        $('#description').val('');
        $('#start_date').val('');
        $('#end_date').val('');
		$('#alarm').val('');
    }
    
    function modal_view(result){
    	$('#Add').hide();
    	
    	// 승인자일 경우 수정/삭제버튼 표시
    	if(result.author){
    		// 필드 Edit 허용
            $('#info').prop("readonly", false);
            $('#medicine_name').prop("readonly", false);
            $('#user_name').prop("disabled", false);
            $('#description').prop("readonly", false);
            $('#start_date').prop("readonly", false);
            $('#end_date').prop("readonly", false);
			$('#alarm').prop("readonly", false);
            
	    	$('#Modify').show();
	    	$('#Del').show();
    	}else{
    		// 필드 Edit 제한
            $('#info').prop("readonly", true);
            $('#medicine_name').prop("readonly", true);
            $('#user_name').prop("disabled", true);
            $('#description').prop("readonly", true);
            $('#start_date').prop("readonly", true);
            $('#end_date').prop("readonly", true);
			$('#alarm').prop("readonly", true);
            
    		$('#Modify').hide();
	    	$('#Del').hide();
    	}
    	
    	$('#info').val(result.id);
        $('#medicine_name').val(result.medicine_name);
        $('#user_name').val(result.user_name);
        $('#description').val(result.description);
        $('#start_date').val(result.start_date);
        $('#end_date').val(result.end_date);
		$('#alarm').val(result.alarm);
        
        $('#InfoModal').modal('show');    	
    }
    
    $('#reserve_form').submit(function( event ) {
    	event.preventDefault();
    	var submit = $(this.id).context.activeElement;

    	$('#method').val(submit.name);
    	
    	$.ajax({
			type: "POST",
			url: "schedule_post.php",
			data: $('#reserve_form').serialize(),
			success: function(data){
				var result = $.parseJSON(data);
				
				 if(result.success){
					 location.reload(true);
                 } else {
                     alert(result.reason);
                 }
			},
			error: function(){
				alert("failure");
			}
		});
	});

	//만료 약품 팝업노출
	if($('#expire_popup').val()){
		var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', 'alarm.mp3');
        audioElement.setAttribute('autoplay', 'autoplay');

        $.get();
        audioElement.addEventListener("load", function() {
	        audioElement.play();
        }, true);

		$('#ExpireModal').modal('show');
	}

	var bestPictures = new Bloodhound({
	  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
	  queryTokenizer: Bloodhound.tokenizers.whitespace,
	  prefetch: '/livesearch.php?q=',
	  remote: {
		url: '/livesearch.php?q=%QUERY',
		wildcard: '%QUERY'
	  }
	});

	$('#medicine_name').typeahead(null, {
		name: 'data',
		displayKey: 'text',
		source: bestPictures
	});
});