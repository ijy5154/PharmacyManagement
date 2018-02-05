<?php
session_start();
require 'db.connect.php';

function stringToColorCode($str) {
    $code = dechex(crc32($str));
    $code = substr($code, 0, 6);
    return '#'.$code;
}

$_SESSION['Company'] = '1';

$db = new DB();

$sql = 'SELECT `Info`, `User`, `UserName`, `TB-MedicineInfo`.`Company`, `Medicine_Name`, `Description`, DATE_FORMAT(`Start_Date`, "%Y-%m-%d") AS `Start_Date`,                        DATE_FORMAT(`End_Date`, "%Y-%m-%d") AS `End_Date`, NOW() BETWEEN DATE_SUB(`End_Date`, INTERVAL `Alarm` MONTH) AND DATE_ADD(DATE_SUB(`End_Date`, INTERVAL `Alarm` MONTH), INTERVAL 7 DAY) AS `Expire`, `Alarm`
    FROM `TB-MedicineInfo`
    LEFT JOIN `TB-UserInfo` USING(`User`)
    WHERE
        `TB-MedicineInfo`.`Company`=:Company
    ORDER BY `End_Date` ASC';
$stmt = $db->prepare($sql);
$stmt->bindParam(':Company', $_SESSION['Company']);
$stmt->execute();

$status = Array();
$calendar = Array();
$expire_popup = false;

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $job['id'] = $row['Info'];
    $job['title'] = $row['Medicine_Name'];
    $job['start'] = $row['Start_Date'];
    $job['end'] = $row['End_Date'];

    $job['color'] = stringToColorCode($row['Medicine_Name']);
    $job['allDay'] = false;
    $calendar[] = $job;

    $status[$i]['id'] = $row['Info'];
    $status[$i]['color'] = $job['color'];
    $status[$i]['username'] = $row['UserName'];
    $status[$i]['medicine_name'] = $row['Medicine_Name'];
    $status[$i]['description'] = nl2br($row['Description']);
    $status[$i]['start_date'] = $row['Start_Date'];
    $status[$i]['end_date'] = $row['End_Date'];
    $status[$i]['expire'] = $row['Expire'];
    $status[$i]['alarm'] = $row['Alarm'];
    
    if($row['Expire'] && $row['Alarm'] != 0){
        $expire_popup = true;
    }
    $i++;
}

$calendar = json_encode($calendar);

$css = array(
            '//cdn.datatables.net/1.10.11/css/dataTables.bootstrap.min.css',
			'/css/bootstrap-datepicker.min.css',
            '/css/typeaheadjs.css',
            '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.css'
        );

$js = array(
            '//cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js',
            '//cdn.datatables.net/1.10.11/js/dataTables.bootstrap.min.js',
			'/js/bootstrap-datepicker.min.js',
        	'/js/bootstrap-datepicker.kr.min.js',
            '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js',
            '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.6.1/fullcalendar.min.js',
            '/js/typeahead.bundle.min.js',
            '/js/management.js'
        );
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta charset="utf-8">
        <link rel="shortcut icon" href="/images/favicon.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <title>약품 관리 시스템</title>

        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="/css/common.css">
        <?php if (!empty($css) && is_array($css)){ ?>
            <?php foreach ($css as $style){ ?>
            <link rel="stylesheet" type="text/css" href="<?php echo $style ?>">
            <?php } 
            } 
        ?>
    </head>
    <body>
        <div id="header" class="navbar navbar-default navbar-statice-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><img src="/images/main-logo.jpg" width="35px" alt="" /></a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                	<?php if (empty($_SESSION['User'])) { ?>
			        <li class="dropdown">
			          <a href="#"class="dropdown-toggle" data-toggle="dropdown"><b>로그인</b> <span class="caret"></span></a>
						<ul id="login-dp" class="dropdown-menu">
							<li>
								 <div class="row">
										<div class="col-md-12">
											 <form id="login_form" class="form" role="form" method="post" accept-charset="UTF-8" id="login-nav">
													<div class="form-group">
														 <label class="sr-only" for="ID">ID</label>
														 <input type="text" class="form-control" id="ID" name="ID" placeholder="ID" required>
													</div>
													<div class="form-group">
														 <label class="sr-only" for="Password">Password</label>
														 <input type="password" class="form-control" id="Password" name="Password" placeholder="Password" required>
													</div>
													<div class="form-group">
														 <button type="submit" class="btn btn-primary btn-block">로그인</button>
													</div>
											 </form>
										</div>
								 </div>
							</li>
						</ul>
					</li>
					<?php }else{ ?>
					<li>
						<a id="logout" href="/logout.php" ><b>로그아웃</b></a>
					</li>
					<?php } ?>
				</ul>
            </div>
        </div>

        <div id="main" class="container">
            <ol class="breadcrumb">
                <li>약품 등록현황</li>
            </ol>

            <input type="hidden" id="calendar_data" value='<?php echo $calendar ?>' />

            <table id="Status" class="table table-striped table-hover" width="100%">
                <thead>
                    <tr>
                        <th>약품명</th>
                        <th>담당자</th>
                        <th>입고일</th>
                        <th>만료일</th>
                        <th>등록일</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($status as $row){ ?>
                    <tr <?php if($row['expire'] && $row['alarm'] != 0) echo 'style="background:#FFDDDD;"' ?> >
                        <td><?php echo $row['medicine_name'] ?></td>
                        <td><?php echo $row['username'] ?></td>
                        <td><?php echo $row['start_date'] ?></td>
                        <td><?php echo $row['end_date'] ?></td>
                        <td><?php echo $row['id'] ?></td>
                        <td><?php echo $row['color'] ?></td>
                        <td><?php echo $row['alarm'] ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Modal -->
            <div id="InfoModal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <form id="reserve_form" class="form-horizontal" role="form" data-toggle="validator" method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">상세정보</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label  class="col-sm-2 control-label" for="medicine_name">약품명</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control typeahead" id="medicine_name" name="medicine_name" placeholder="MedicineName" required/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="User" >담당자</label>
                                    <div class="col-sm-10">
                                        <input type="hidden" class="form-control" id="user" name="user" required/>
                                        <input type="text" class="form-control" id="user_name" name="user_name" placeholder="UserName" required/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="description" >약품정보</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="description" name="description" placeholder="MedicineInfomation" style="height:150px;"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">약품기간</label>
                                    <div class="col-sm-10">
                                         <div class="input-daterange input-group" id="datepicker">
                                            <input type="text" class="form-control" id="start_date" name="start_date" name="start_date" placeholder="약품 입고" required>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-resize-horizontal"></span>
                                            </span>
                                            <input type="text" class="form-control" id="end_date" name="end_date" name="end_date" placeholder="약품 만료" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="alarm" >만료알림</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="alarm" name="alarm" placeholder="만료알림" placeholder="Month" min="0" value="0" required/>
                                        <span class="help-block">* 0으로 설정시 알림제외 (한달단위)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-success" id="Add" name="Add">등록</button>
                                <button class="btn btn-primary" id="Modify" name="Modify">수정</button>
                                <button class="btn btn-danger" id="Del" name="Del">삭제</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                        <input type="hidden" id="method" name="method" value=""/>
                        <input type="hidden" id="info" name="info" value=""/>
                        <input type="hidden" id="login_name" name="login_name" value="<?php echo $_SESSION['User_Name'] ?>"/>
                        <input type="hidden" id="expire_popup" value="<?php echo $expire_popup ?>"/>
                    </form>
                </div>
            </div>

            <!-- Modal -->
            <div id="ExpireModal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <form id="reserve_form" class="form-horizontal" role="form" data-toggle="validator" method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">알림</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="description" >만료정보</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="expire_description" name="expire_description" placeholder="MedicineInfomation" style="height:150px;">
<?php foreach ($status as $row){ if($row['expire'] && $row['alarm'] != 0) { echo $row['medicine_name'].' : '.$row['start_date'].' ~ '.$row['end_date'].'('.$row['expire'].' 달후 만료)'.PHP_EOL;}}?>
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="//code.jquery.com/jquery-1.12.1.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
        <script src="/js/header.js"></script>
        <?php  if (!empty($js) && is_array($js)){ 
            foreach ($js as $src){ ?>
                <script type="text/javascript" src="<?php echo $src ?>"></script>
            <?php }
        } ?>
        

        <style>
            #calendar { margin-bottom: 10pt; }
            .fc-view thead th.fc-sun { color:#d42403; }
            .fc-view thead th.fc-sat { color:#1974db; }
            .fc-view tbody td.fc-sun { color:#d42403; }
            .fc-view tbody td.fc-sat { color:#1974db; }
            .job_hidden { display:none; }
        </style>   
    </body>
</html>

